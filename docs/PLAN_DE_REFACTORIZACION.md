# Plan de Refactorización de Arquitectura: Livewire y Controladores

Este documento describe el plan para refactorizar la arquitectura actual de la aplicación, migrando de un uso intensivo de componentes de página completa de Livewire a un enfoque híbrido más sostenible y performante, utilizando controladores de Laravel y vistas de Blade como base.

## 1. Problema Detectado

La arquitectura actual se basa casi exclusivamente en componentes de página completa de Livewire para renderizar las vistas y manejar la lógica de la aplicación.

Tras un análisis del fichero `routes/web.php`, se confirma que la mayoría de las rutas, especialmente en las secciones de `superadmin` y `productor`, apuntan directamente a una clase de componente Livewire.

**Consecuencias Negativas:**

*   **Rendimiento:** Cada interacción del usuario genera una petición AJAX al servidor que debe manejar el estado del componente completo, aumentando la latencia y la carga del servidor.
*   **Mantenibilidad:** Los componentes se vuelven masivos y complejos, mezclando responsabilidades de control de ruta, obtención de datos y lógica de la vista. Esto viola el Principio de Responsabilidad Única y dificulta la depuración y la evolución del código.
*   **Mala Práctica:** Se desvía de la recomendación de la comunidad y del propio Livewire, que aboga por usar controladores para las páginas y "espolvorear" componentes de Livewire para añadir reactividad en zonas concretas (el principio 80/20).

## 2. Solución Propuesta

Adoptar una arquitectura híbrida, siguiendo las mejores prácticas de Laravel y Livewire.

**Roles y Responsabilidades:**

1.  **Controladores de Laravel:** Se encargarán de manejar la petición HTTP inicial, obtener los datos necesarios para la carga de la página y renderizar la vista principal de Blade.
2.  **Vistas Blade:** Definirán la estructura HTML principal de la página (layouts, títulos, contenido estático).
3.  **Componentes de Livewire:** Se insertarán dentro de las vistas Blade para encapsular únicamente las partes de la UI que requieren reactividad (tablas con filtros, formularios dinámicos, etc.).

## 3. Plan de Acción General

Para cada ruta a refactorizar, se seguirán los siguientes pasos:

1.  **Crear/Utilizar un Controlador** de Laravel apropiado.
2.  **Crear un método** en el controlador para manejar la lógica de la ruta.
3.  **Mover la lógica de obtención de datos iniciales** del método `mount()` o `render()` del componente de Livewire al nuevo método del controlador.
4.  **Crear una vista Blade** que reciba los datos del controlador.
5.  **Actualizar `routes/web.php`** para que la ruta apunte al método del controlador.
6.  **Incrustar el componente de Livewire** dentro de la nueva vista Blade (`<livewire:componente-nombre />`).
7.  **Simplificar el componente de Livewire**, eliminando toda la lógica y el marcado que ahora es manejado por el controlador y la vista Blade, dejándolo enfocado en su funcionalidad reactiva.

## 4. Registro de Avances

---
### **14 de Septiembre de 2025 - Refactorización del Panel de Administración y Componentización de Widgets**

*   **Contexto:** Se abordó la refactorización del panel de administración para mejorar su estructura, funcionalidad y mantenibilidad, migrando a un diseño basado en componentes reutilizables. Se corrigieron errores de rutas y se estableció una base sólida para el desarrollo futuro.

*   **Corrección de Errores de Acceso y Rutas:**
    *   **Diagnóstico y Solución:** Se identificaron y corrigieron múltiples errores `Route [...] not defined` e `Invalid route action` causados por la refactorización previa de Livewire a controladores y por nombres de rutas inconsistentes.
    *   **Acciones:**
        *   Se definieron correctamente las rutas para `admin.panel`, `admin.productores.panel`, `admin.instituciones.panel`, `admin.productores.listado`, `admin.productores.importar`, `admin.solicitudes.gestionar` en `routes/web.php`, apuntando a sus respectivos controladores (`AdminController`, `ProductorController`, `InstitucionController`, `SolicitudController`) o componentes Livewire según el plan de refactorización.
        *   Se corrigieron las llamadas a `route()` en las vistas (`admin/panel.blade.php`, `admin/instituciones/panel.blade.php`, `admin/productores/panel.blade.php`, `layouts/partials/navigation/admin-nav.blade.php`, `layouts/partials/sidebar/admin.blade.php`) para usar los nombres de ruta correctos (ej. `admin.solicitudes.gestionar` en lugar de `solicitudes.gestionar`).
        *   Se limpió la caché de Laravel (`php artisan optimize:clear`) para aplicar los cambios.

*   **Diseño y Componentización del Nuevo Dashboard de Administración (Maqueta):**
    *   **Propuesta de Diseño:** Se propuso un nuevo diseño de dashboard basado en widgets para el `superadmin`, enfocado en supervisión, auditoría y gestión global.
    *   **Ruta y Controlador de Maqueta:** Se creó una ruta (`admin.panel.maqueta`) y un método (`AdminController@panelMaqueta`) para la maqueta, permitiendo trabajar en el diseño sin afectar el panel actual.
    *   **Vista de Maqueta:** Se creó `resources/views/admin/panel-maqueta.blade.php` para el nuevo diseño.
    *   **Componentización de Widgets:** Se crearon componentes Blade reutilizables en `resources/views/components/widgets/` para cada sección del dashboard, promoviendo la modularidad y reusabilidad:
        *   `dashboard-kpi.blade.php`: Tarjetas de indicadores clave. Se restauró su estilo original más simple.
        *   `map-widget.blade.php`: Mapa interactivo de productores.
        *   `activity-feed.blade.php`: Feed de actividad reciente del sistema.
        *   `quick-actions.blade.php`: Botones de acciones rápidas.
        *   `pending-requests.blade.php`: Listado de solicitudes pendientes (sin botones de acción directa, con enlace a la gestión).
        *   `news-widget.blade.php`: Noticias del sector.
        *   `activity-item.blade.php`, `quick-action-button.blade.php`, `request-item.blade.php`: Componentes auxiliares para listas.
    *   **Actualización de Controladores:** `AdminController@panelMaqueta` se actualizó para pasar datos de ejemplo a todos los nuevos widgets.
    *   **Refactorización de Vistas:** `panel-maqueta.blade.php` se reescribió para utilizar estos nuevos componentes.
    *   **Corrección de Gráficos:** Se solucionó el problema de los gráficos "infinitos" en la maqueta envolviendo los elementos `<canvas>` en `divs` con altura fija.

*   **Ajustes de Layout y Reusabilidad:**
    *   **Layout de Ancho Completo:** Se modificó `layouts/admin.blade.php` para eliminar el contenedor de ancho fijo (`max-w-7xl`) y fondo blanco, haciéndolo estructuralmente idéntico a `layouts/productor.blade.php`. Esto permite que las vistas controlen su propio ancho y fondo, facilitando diseños de ancho completo.
    *   **Widget de Clima Reutilizable:** Se creó `weather-widget.blade.php` y se integró tanto en la maqueta del panel de administrador como en el panel del productor, demostrando la reusabilidad de los componentes.
    *   **Modal de Actividad Reciente:** Se creó `components/modals/activity-feed.blade.php` para mostrar la actividad reciente en un modal global. Se añadió un botón de activación en el encabezado (`components/panel-layout.blade.php`) y se incluyó el modal en el layout del administrador (`layouts/admin.blade.php`).

*   **Próximos Pasos:** Se planificó la reorganización de los widgets en el dashboard y la conexión de los datos de los KPIs a la base de datos. Se decidió mover el widget de "Actividad Reciente" a un modal global.

---
### **13 de Septiembre de 2025 - Refactorización de Componentes Públicos y Estado Global**

*   **Contexto:** Se abordó la tarea de eliminar las dependencias de Livewire restantes en las páginas públicas (landing page), que incluían el formulario de contacto y el flujo de solicitud de registro para instituciones.

*   **Refactorización de Componentes:**
    *   **Formulario de Contacto:** Se reemplazó el componente Livewire `ContactForm` por un componente de Blade/Alpine.js (`landing-contact-form.blade.php`). La lógica de envío ahora es manejada por `LandingPageContactController` y utiliza `fetch` para una experiencia AJAX sin recarga de página.
    *   **Flujo de Solicitud Institucional:** Este flujo se dividió en dos partes:
        1.  La página de información estática (`InformacionRegistroInstitucional`) fue convertida de un componente Livewire a una vista de Blade simple, servida por `PaginasEstaticasController`.
        2.  El modal de formulario (`SolicitudModal`) fue reemplazado por un componente Blade/Alpine (`solicitud-institucion-modal.blade.php`) que realiza un envío de formulario estándar a un nuevo `SolicitudController`.

*   **Centralización del Estado de Modales:**
    *   **Problema:** Surgieron múltiples errores y regresiones al intentar abrir los modales, incluyendo un conflicto con la variable global `window.open` y fallos en la comunicación entre componentes.
    *   **Solución:** Se implementó una arquitectura de **estado unificado**. Se añadió un único `x-data` a la etiqueta `<body>` del layout `guest.blade.php` para gestionar el estado de visibilidad de todos los modales (`contactModalOpen`, `solicitudModalOpen`). Esto resolvió todos los conflictos de raíz y simplificó la lógica.

*   **Mejoras de UX y Corrección de Errores:**
    *   **Error Crítico de Alpine:** Se solucionó un error `Illegal invocation` al renombrar una variable de estado conflictiva (`open` -> `mobileMenuOpen`) en el menú de navegación móvil.
    *   **Error de Blade:** Se corrigió un error de parseo `Undefined constant "isSubmitting"` utilizando la sintaxis explícita `x-bind:disabled` en lugar del atajo `:disabled`.
    *   **Glitch Visual:** Se arregló un parpadeo transparente en el modal de contacto al mover las clases de estilo al elemento correcto de la transición.
    *   **Mejoras de Modal:** Se añadieron transiciones suaves de apertura/cierre al modal de solicitud y un estado de "Enviando..." al botón de envío para mejorar el feedback al usuario.
    *   **Rediseño Visual:** Se rediseñó la página de información de registro institucional para alinearla con la estética de la página "La Cuenca", utilizando una cabecera de color bordó y un layout de tarjetas interactivas.

*   **Estado Actual:** Las páginas públicas del sitio ya no tienen dependencias de Livewire. La interactividad es manejada por Alpine.js con un estado centralizado y robusto.

---
### **12 de Septiembre de 2025 - Implementación del Backend de Clima y Widget Dinámico**

*   **Contexto:** Se implementó desde cero el sistema para obtener y mostrar datos climáticos en los paneles de la aplicación, siguiendo el plan de `planApiclima.txt`.
*   **Backend y Automatización:**
    *   **Migraciones:** Se creó la tabla `clima` para almacenar los datos de la API de OpenWeather y se verificó que la tabla `municipios` ya contaba con campos para coordenadas.
    *   **Seeder de Coordenadas:** Se mejoró el seeder `MunicipioCoordinatesSeeder` para que pueble las coordenadas de los municipios leyendo dinámicamente el archivo `municipios.geojson`, asegurando datos precisos.
    *   **Comando Artisan:** Se creó el comando `clima:actualizar` que itera sobre todos los municipios, llama a la API de OpenWeather y guarda los resultados en la tabla `clima`.
    *   **Programación de Tareas:** Se configuró el Scheduler de Laravel para ejecutar el comando `clima:actualizar` cada tres horas, automatizando la obtención de datos. Se investigó y solucionó la forma de configurar el scheduler en Laravel 12 (usando `routes/console.php`).
*   **Widget de Clima Dinámico:**
    *   **Lógica Centralizada:** Se creó un Trait `ManagesWeatherData` para encapsular toda la lógica de obtención y formateo de los datos del clima, evitando código duplicado.
    *   **Refactorización de Controladores:** Se refactorizaron `AdminController` y `ProductorController` para usar el nuevo Trait.
        *   El `ProductorController` ahora obtiene el clima correspondiente al municipio del productor autenticado.
        *   El `AdminController` se preparó para un widget más avanzado.
    *   **Componente Inteligente:** Se refactorizó el componente Blade `weather-widget.blade.php` para que sea "inteligente". Se le añadió una propiedad `filterable` que, si es `true`, muestra un formulario con un desplegable para filtrar el clima por cualquier municipio.
    *   **Implementación de Filtro:** En el panel de administrador, el widget ahora muestra el filtro, permitiendo al Super Admin ver el clima de cualquier municipio. En el panel del productor, el widget muestra el clima local sin el filtro.
*   **Depuración:**
    *   Se solucionó un error `401 Invalid API Key` corrigiendo el guardado de la clave en el archivo `.env`.
    *   Se solucionó un error de `MassAssignmentException` en el modelo `Clima`.
    *   Se solucionó un error de `Table not found` especificando el nombre de la tabla en el modelo `Clima`.
    *   Se diagnosticó y solucionó un error `Undefined variable $municipios` que ocurría por un error de lógica en el `AdminController`.
*   **Estado Actual:** La funcionalidad de backend para el clima está completa y automatizada. El widget del clima es dinámico y se adapta a su contexto (admin vs. productor). La página del panel de administrador carga sin errores. Sin embargo, el usuario reporta que la visualización del widget en sí está "rota".
*   **Próximo Paso:** Depurar y corregir el estado visual del `weather-widget` para asegurar que muestra los datos correctamente después de la carga.

---
### **12 de Septiembre de 2025 - Refactorización del Formulario de Creación de Unidad Productiva (Continuación)**

*   **Contexto:** Se retomó la refactorización del formulario de creación de Unidades Productivas, que había sido pausada, para solucionar bugs críticos y mejorar la experiencia de usuario.
*   **Causa Raíz:** El componente Livewire `CrearUnidadProductiva` era un "dios" que manejaba la lógica de 3 pasos, el estado, la validación y la comunicación con el mapa, generando complejidad y errores de flujo.
*   **Solución - Arquitectura de Controlador + Vistas:**
    1.  **Controlador:** Se creó `UnidadProductivaController` para orquestar el flujo. Cada paso es ahora un método (`createStep1`, `createStep2`, `createStep3`, `store`).
    2.  **Vistas Blade:** Se dividió el formulario en vistas separadas para cada paso (`step-1`, `step-2`, `step-3`), simplificando el renderizado y la lógica de la interfaz.
    3.  **Gestión de Estado:** Se reemplazó el manejo de estado de Livewire por el uso de la **sesión de Laravel** para persistir los datos del formulario entre los pasos de forma fiable.
*   **Corrección de Bugs y Flujo:**
    *   Se solucionó un **bucle infinito** en el Paso 3, causado por una lógica de renderizado condicional defectuosa en la vista.
    *   Se corrigió el flujo de navegación, que saltaba del Paso 1 directamente al mapa. Ahora sigue la secuencia lógica `Paso 1 -> Paso 2 (Instrucciones) -> Mapa -> Paso 2 (Confirmación) -> Paso 3`.
    *   Se ajustó la redirección final para que, tras crear la chacra, dirija a la lista de "Mis Chacras" en lugar de al panel principal.
*   **Mejoras de Validación y UX:**
    *   **RNSPA Único:** Se implementó una regla de validación `unique` en el backend (`CreateUnidadProductiva` Action) para impedir el registro de un RNSPA duplicado, previniendo la corrupción de datos que ocurría anteriormente.
    *   **Formato RNSPA:** Se añadió validación con `regex` en el backend y un `pattern` en el frontend para asegurar el formato `XX.XXX.X.XXXXX/XX`.
    *   **Máscara de Input:** Se implementó un script de JavaScript para **auto-formatear** el campo RNSPA, mejorando drásticamente la experiencia de usuario.
    *   **Tooltips Restaurados:** Se reintrodujeron todos los tooltips de ayuda en los campos de los Pasos 1 y 3, que se habían perdido durante la migración inicial.
    *   **Feedback al Usuario:** Se añadió el renderizado de mensajes de éxito/error en la página de listado "Mis Chacras" y se clarificaron los campos opcionales en la interfaz.
*   **API y Frontend:**
    *   Se solucionó un error crítico **`401 Unauthorized`** que impedía la carga de "Parajes". La causa era una configuración incorrecta de Sanctum. Se solucionó añadiendo el middleware `EnsureFrontendRequestsAreStateful` a la configuración de la API en `bootstrap/app.php`.

*   **Estado Actual:** El componente `CrearUnidadProductiva` ha sido **completamente eliminado** y reemplazado por un flujo MVC robusto, mantenible y con una experiencia de usuario superior. El sistema ahora previene errores de duplicación de datos y guía al usuario de forma más efectiva.

---
### **12 de Septiembre de 2025 - Diagnóstico de Bug de Navegación y Limpieza de Componentes**

*   **Contexto:** Se investigó un bug crítico que causaba que la navegación fallara en el primer intento después de ejecutar `php artisan optimize:clear`. El usuario sospechaba que el problema estaba relacionado con la eliminación de componentes de Livewire.

*   **Diagnóstico y Causa Raíz:**
    1.  **Análisis Inicial:** Se exploraron varias teorías, incluyendo el script de la pantalla de carga y middlewares de la aplicación, pero ninguna fue la causa.
    2.  **Descubrimiento Clave:** El usuario descubrió que el bug solo ocurría al usar el servidor de desarrollo de Vite (`npm run dev`) y no con los assets compilados para producción (`npm run build`).
    3.  **Causa Confirmada:** Se concluyó que el problema era una **condición de carrera (race condition)**. El servidor de Vite, al procesar los assets "just-in-time", entraba en conflicto con los scripts globales de Livewire inyectados en todas las páginas. En la primera carga (lenta por la limpieza de caché), este conflicto rompía el renderizado.

*   **Decisión Estratégica - Acelerar la Migración de Livewire:**
    *   Debido a los continuos conflictos y la complejidad que Livewire estaba introduciendo en el entorno de desarrollo, se tomó la decisión de priorizar y acelerar la migración de los componentes restantes a un stack de Blade/Alpine.js con `fetch` para las llamadas al backend.

*   **Progreso en Limpieza de "Código Muerto":**
    *   **Investigación de Dependencias:** Se investigó por qué al eliminar los componentes de Livewire para la gestión de Stock (`CrearStock`, `EditarStock`) se rompía la aplicación.
    *   **Dependencia Oculta Encontrada:** Se descubrió que el componente `VerStock` (que se pensaba obsoleto) contenía un enlace (`route('productor.stock.editar', ...)`) , creando una dependencia oculta que rompía el sistema de rutas de Laravel al eliminar el componente `EditarStock`.
    *   **Eliminación de Componentes de Stock:** Tras confirmar que la nueva sección "Mi Stock" es de solo lectura y la gestión se realiza en el "Cuaderno de Campo", se procedió a una limpieza completa:
        *   Se eliminaron **5 componentes de Livewire** y sus vistas: `ListarStock`, `CrearStock`, `EditarStock`, `VerStock`, y `EliminarStock`.
        *   Se eliminaron las rutas asociadas a estos componentes del archivo `routes/web.php`.

*   **Estado Actual:**
    *   El bug de navegación ha sido resuelto de forma indirecta al eliminar los componentes conflictivos.
    *   La base de código ha sido significativamente limpiada, reduciendo el número de componentes de Livewire de 36 (estimación inicial) a **17 restantes**.
    *   El sistema está estable y listo para continuar con la migración planificada.

*   **Próximo Paso:** Iniciar la migración sistemática de los 17 componentes restantes, comenzando con `CrearParajeModal` como prueba de concepto para el nuevo patrón "Alpine + Fetch".

---
### **11 de Septiembre de 2025 (Continuación) - Avances en Panel de Productor**

*   **Contexto:** Se continuó con la refactorización del panel de productor, enfocándose en migrar funcionalidades clave de Livewire a un stack de Controlador + Blade/AlpineJS y en crear nuevas secciones siguiendo la arquitectura moderna.

*   **Progreso:**
    *   **Refactorización del Perfil de Productor**: 
        *   Se reemplazó el componente de página completa de Livewire `Productor/Perfil.php` por un modal dinámico.
        *   Se creó `ProductorProfileController` para manejar la carga de datos y las actualizaciones vía AJAX (`fetch`).
        *   La lógica de negocio se consolidó en la acción `UpdateProductorProfile`.
        *   Se construyó el modal con Blade y Alpine.js (`productor-perfil.blade.php` y `app.js`), replicando el patrón del modal de configuración del admin.
        *   Se corrigieron varios bugs durante el proceso, incluyendo errores de sintaxis en JavaScript y Blade, demostrando la estabilidad de la nueva arquitectura.
    *   **Creación de la Página "Mis Chacras"**: 
        *   Se creó la página para listar las Unidades Productivas del productor desde cero, siguiendo la arquitectura de Controlador + Vista.
        *   Se añadió el método `unidadesProductivasIndex` a `ProductorController`.
        *   Se creó la vista `productor.unidades-productivas.index`, manteniendo la convención de usar "Chacra" solo en etiquetas visibles.
        *   Se actualizaron las rutas y el menú lateral para reflejar la nueva página.
    *   **Creación de la Página "Mi Stock"**: 
        *   Se implementó una nueva página de inventario para reemplazar la vista previa del dashboard.
        *   Se añadió el método `stockIndex` a `ProductorController`, incluyendo lógica para filtrar por unidad productiva.
        *   Se creó la vista `productor.stock.index` con un formulario de filtro.
        *   Se integró la página en las rutas y el menú lateral.
    *   **Ajustes de Componentes Existentes**: 
        *   Se ajustó el componente Livewire `GestionarChacra` para que use el layout `productor-wizard`, sacándolo del panel principal para una mejor experiencia de usuario.
        *   Se renombró la clase y la vista de `GestionarChacra` a `GestionarUnidadProductiva` para mantener la consistencia interna del código, aunque su lógica de Livewire se mantuvo por su complejidad.
    *   **Diseño de Nuevas Secciones (Maquetación)**:
        *   Se diseñó y maquetó una nueva página de **Estadísticas**, añadiendo `Chart.js` como dependencia para crear gráficos interactivos (torta, barras, líneas) y una barra de filtros avanzada (fechas, chacra, especie). Se incluyó un botón para exportar a PDF.
        *   Se diseñó y maquetó una nueva página de **Reportes** como un centro de descargas para documentación variada (análisis de suelo, guías, plantillas).
        *   Se reorganizó el menú lateral del productor para crear una nueva sección "Análisis y Datos", agrupando las nuevas páginas de Estadísticas y Reportes.

*   **Estado Actual:** El panel del productor ha sido significativamente ampliado y estandarizado. Las funcionalidades más importantes ahora siguen la arquitectura de Controlador + Vista, mejorando la mantenibilidad. Se han sentado las bases visuales y estructurales para las futuras secciones de análisis de datos.
---
### **11 de Septiembre de 2025 (Continuación) - Actualizaciones y Correcciones Post-Refactorización de Layouts**

*   **Contexto:** Tras la refactorización inicial de los layouts base, se identificaron y corrigieron varios problemas relacionados con la integración y el flujo de usuario.

*   **Progreso:**
    *   **Corrección de `panel-layout.blade.php`:** Se ajustó el componente `panel-layout.blade.php` para que dejara de ser un documento HTML completo y se convirtiera en un componente Blade puro, envolviéndose correctamente en `app.blade.php`.
    *   **Ajuste de Layouts de Panel:** Se modificaron `layouts/admin.blade.php` y `layouts/productor.blade.php` para que usaran correctamente `<x-app-layout>` como su capa base, envolviendo a `<x-panel-layout>`.
    *   **Registro de Componente `productor-layout`:** Se añadió `Blade::component('layouts.productor', 'productor-layout');` en `AppServiceProvider.php` para que la etiqueta `<x-productor-layout>` fuera reconocida.
    *   **Refactorización de `CrearUnidadProductiva` (Intento y Reversión):**
        *   Se inició la refactorización del componente `CrearUnidadProductiva` a un controlador y vista Blade/Alpine.
        *   Se implementó la lógica de pasos y redirección al mapa usando la sesión.
        *   **Decisión:** Debido a la complejidad de replicar el flujo exacto y el esfuerzo percibido, se decidió **revertir** esta refactorización. Se eliminaron el controlador, la acción y la vista Blade creados, y se restauraron las rutas y el componente Livewire original.
    *   **Corrección de Flujo de Onboarding:**
        *   **Error `Route [productor.dashboard] not defined`:** Se identificó que el enlace "Inicio" en `layouts/partials/sidebar/productor.blade.php` apuntaba a una ruta inexistente (`productor.dashboard`). Se corrigió para que apuntara a `productor.panel`.
        *   **Formulario de Creación de UP dentro del Panel:** Se corrigió el componente `CrearUnidadProductiva` para que usara `#[Layout('layouts.productor-wizard')]` en lugar de `layouts.productor`, asegurando que el formulario se muestre en un layout limpio.
        *   **Dashboard sin Envoltura (`Undefined variable $slot`):** Se reescribió `resources/views/productor/dashboard.blade.php` para que utilizara la sintaxis de componentes (`<x-productor-layout>`) en lugar de `@extends`, solucionando el error de la variable `$slot` y asegurando que el dashboard se renderice con su layout completo.
    *   **Activación de Carrusel:** Se corrigió la clase CSS en `productor.dashboard.blade.php` (`animate-marquee` a `animate-scroll-and-repeat`) para activar la animación del carrusel de estadísticas.

*   **Estado Actual:** El flujo de onboarding y el panel del productor funcionan correctamente y se visualizan según el diseño. La arquitectura de layouts está consolidada.

*   **Próximo Paso:** Refactorizar el componente `app/Livewire/Productor/Perfil.php` a un controlador y una acción, extrayendo su lógica de negocio compleja. (Actualmente en progreso: se ha creado `app/Actions/Productor/UpdateProductorProfile.php`).
---
### **11 de Septiembre de 2025 (Continuación) - Refactorización de Layouts Base y Navegación**

*   **Contexto:** Se inició la refactorización de la estructura de layouts para unificar el diseño de los paneles de administración y productor, adoptando el estilo del layout `productor.blade.php` como base. Se busca desacoplar la navegación de Livewire y hacerla explícita en cada layout de panel.

*   **Progreso:**
    *   **Definición de Layout Base:** Se creó `resources/views/components/panel-layout.blade.php` a partir del diseño de `resources/views/layouts/productor.blade.php`. Este nuevo componente es un layout genérico que incluye la barra superior, el menú lateral (con un slot para los enlaces) y el área de contenido principal. Se integró el menú de usuario dinámico de Jetstream y se reemplazaron los `@yield` por `slots` (`$sidebar`, `$header_title`, y el `slot` por defecto para el contenido principal).
    *   **Limpieza de `app.blade.php`:** Se simplificó `resources/views/layouts/app.blade.php` para que sea un layout base mínimo, conteniendo solo los elementos HTML esenciales (`head`, `body`, `@vite`, `@livewireStyles`, `@livewireScripts`, `@stack('modals')`, `@stack('scripts')`) y la pantalla de carga global. Se movieron los estilos CSS del loader a `resources/css/app.css`.
    *   **Refactorización de `admin.blade.php`:** Se actualizó `resources/views/layouts/admin.blade.php` para que utilice el nuevo `<x-panel-layout>` como su layout principal. Se eliminó la dependencia de `@livewire('navigation-menu')` y se preparó para inyectar el menú lateral específico del administrador.
    *   **Registro de Componente:** Se añadió el registro de `admin-layout` como un componente de Blade en `AppServiceProvider.php` (`Blade::component('layouts.admin', 'admin-layout');`).

*   **Estado Actual:** Se ha establecido una base sólida para los layouts de los paneles, permitiendo una estructura unificada y desacoplada de Livewire para la navegación. El diseño del panel de productor ha sido adoptado como estándar.

*   **Próximos Pasos:**
    1.  **Registrar `panel-layout`:** Añadir `Blade::component('components.panel-layout', 'panel-layout');` en `AppServiceProvider.php`.
    2.  **Crear el menú lateral del administrador:** Crear `resources/views/layouts/partials/sidebar/admin.blade.php` y copiar los enlaces de `layouts/partials/navigation/superadmin.blade.php`, adaptándolos para un menú lateral.
    3.  **Actualizar `admin.blade.php`:** Modificar `resources/views/layouts/admin.blade.php` para que use `<x-panel-layout>` y le pase el título del encabezado y el nuevo menú lateral del administrador.
    4.  **Eliminar archivos de navegación antiguos:** Eliminar `resources/views/navigation-menu.blade.php` y `resources/views/layouts/partials/navigation/superadmin.blade.php` (una vez que su contenido haya sido migrado).
    5.  **Refactorizar `productor.blade.php`:** Actualizar `resources/views/layouts/productor.blade.php` para que también use `<x-panel-layout>` y le pase su propio título y menú lateral (que también habrá que crear).
---
### **11 de Septiembre de 2025 - Refactorización del Panel de Productor**

*   **Contexto:** Se inició la refactorización de la sección de Productores, que presentaba el mismo anti-patrón de componentes de página completa de Livewire que el panel de administración.

*   **Progreso:**
    *   **Análisis Inicial:** Se analizó `routes/web.php` para identificar los componentes de Livewire que gestionaban las páginas del productor (ej. `ProductorPanel`, `Productor\Dashboard`, etc.).
    *   **Creación de Controlador:** Se creó un nuevo `ProductorController` en `app/Http/Controllers/Productor/` para centralizar la lógica de las rutas de esta sección.
    *   **Refactorización de Layouts:**
        *   Se detectó que el layout `layouts.productor.blade.php` estaba siendo usado por el asistente de bienvenida (`welcome-onboarding`).
        *   Para desacoplar, se renombró el layout del asistente a `layouts.productor-wizard.blade.php` y se actualizó su componente (`App\View\Components\ProductorLayout`) para que apuntara al nuevo nombre, conservando la funcionalidad del onboarding.
        *   Se creó un **nuevo layout maestro** para el panel de productor en `layouts/productor.blade.php`.
    *   **Diseño de Maqueta (Iterativo):**
        *   Se refactorizó la ruta principal (`/productor/panel`) para que fuera gestionada por `ProductorController@dashboard`.
        *   Se creó una vista `productor/dashboard.blade.php`.
        *   Tras varias iteraciones y feedback del usuario, se diseñó una **maqueta estática completa** para el nuevo panel, abandonando el diseño simple de tarjetas.
        *   La maqueta final se inspira en la estructura del "Cuaderno de Campo", con un layout de aplicación más moderno y funcional.

*   **Estado Actual:**
    *   Se ha establecido un **layout de panel robusto y moderno** para toda la sección del productor. Es un archivo autocontenido que no depende de `layouts.app`, con un menú lateral responsive, una barra de encabezado limpia y un panel de notificaciones deslizable.
    *   La ruta principal del productor (`/productor/panel`) ahora muestra una **maqueta visualmente rica y aprobada** del nuevo dashboard, que incluye widgets para un carrusel de datos, gráficos, vista previa de stock, clima y noticias.
    *   El sistema está en un punto ideal para comenzar a conectar la lógica de negocio y los datos reales a esta nueva interfaz.

*   **Guía para Continuar:**
    
    **Fase 1: Desacoplamiento de la Lógica de Negocio (Próximo Gran Paso)**

    *   **Objetivo Principal:** Antes de conectar datos a la nueva interfaz, se refactorizará y desacoplará la lógica de negocio de los antiguos componentes de Livewire del productor.
    *   **Estrategia:**
        1.  **Análisis de Componentes:** Se analizará cada componente de Livewire de la sección del productor (ej. `CrearUnidadProductiva`, `GestionarChacra`, `CrearStock`, etc.).
        2.  **Identificación de Lógica:** Se identificará la lógica de negocio clave dentro de cada componente (validaciones, interacciones con la base de datos, cálculos, etc.).
        3.  **Extracción a Clases de Servicio/Acción:** Dicha lógica se extraerá a clases PHP dedicadas y reutilizables, siguiendo los patrones ya establecidos en el panel de administración (ej. `App\Services\*`, `App\Actions\*`). Por ejemplo, la lógica para crear una unidad productiva se moverá a una `CreateUnidadProductivaAction` o un `UnidadProductivaService`.
    *   **Resultado Esperado:** Al final de esta fase, tendremos un conjunto de clases de servicio/acción que encapsulan toda la lógica de negocio del panel del productor, completamente independientes de Livewire o de cualquier controlador.

    **Fase 2: Conexión de la Interfaz**

    *   **Objetivo:** Una vez que la base de lógica de negocio sea sólida, el trabajo de crear los métodos de los controladores y conectar las vistas será mucho más rápido y limpio.
    *   **Estrategia:**
        1.  **Conectar Datos al Dashboard:** Modificar el método `ProductorController@dashboard` para que llame a los nuevos servicios/acciones y pase los datos a la vista `productor.dashboard.blade.php`.
        2.  **Refactorizar Secciones (Una por una):** Para cada enlace del menú lateral (Perfil, Mis Chacras, etc.), seguir el ciclo de crear la ruta, el método del controlador (que llamará a la lógica de negocio ya desacoplada), la vista Blade, y finalmente activar el enlace en el menú.
        3.  **Centralizar Menú Lateral:** Mover el código del menú lateral a un parcial de Blade para no repetirlo en cada vista.
        4.  **Implementar Widgets Dinámicos:** Abordar la lógica de los widgets más complejos del dashboard (gráficos, clima).

---
### **10 de Septiembre de 2025 (Continuación) - Corrección de Bugs**

*   **Contexto:** Se abordaron los bugs listados en la sección "Próximo Bug a Resolver" (ahora eliminada) y un bug de navegación adicional descubierto durante las pruebas.

*   **Progreso en Corrección de Bugs:**
    *   **Solucionado - Conflicto de Navegación en Perfil**: Se eliminó el atributo `wire:navigate` de los enlaces que conectan con la página de perfil en `navigation-menu.blade.php`. Esto fuerza una recarga de página completa, evitando la redeclaración de scripts y solucionando el error.
    *   **Solucionado - Duplicidad de ID de Email en Perfil**: Se corrigió el ID duplicado del campo de email en `resources/views/profile/update-profile-information-form.blade.php`, renombrándolo a `profile_email` para cumplir con el estándar HTML.
    *   **Solucionado - Bug de Navegación General ("Primer clic falla")**: Se detectó una condición de carrera en el manejador de la pantalla de carga. Se refactorizó la lógica a un componente global de Alpine (`loaderManager`) que previene la navegación por defecto, muestra el loader y redirige manualmente con un `setTimeout`, garantizando una experiencia de usuario fluida y sin errores.

*   **Estado Actual:** La página de perfil y la navegación global son estables. La base de JavaScript para la interactividad ha sido fortalecida y centralizada en Alpine.js, siguiendo la arquitectura deseada.

---
### **09 de Septiembre de 2025**

*   **Progreso:**
    *   **Refactorización del Modal "Registrar Productor"**: 
        *   Migrado de componente Livewire a modal de Blade/Alpine.js con envío de datos vía `fetch` a un controlador estándar de Laravel (`ProductorController@store`).
        *   Se creó el método `store` en `app/Http/Controllers/Admin/ProductorController.php` con la lógica de validación y guardado.
        *   Se añadió la ruta `POST /superadmin/productores` (`admin.productores.store`) en `routes/web.php`.
        *   Se creó el nuevo componente Blade `resources/views/components/modals/productor-form.blade.php`.
        *   Se integró el nuevo modal en `resources/views/layouts/app.blade.php` y se eliminaron los archivos Livewire (`app/Livewire/Admin/ProductorFormModal.php` y `resources/views/livewire/admin/productor-form-modal.blade.php`).
    *   **Refactorización del Modal "Registrar Institución"**: 
        *   Migrado de componente Livewire a modal de Blade/Alpine.js con envío de datos vía `fetch` a un controlador estándar de Laravel (`InstitucionController@store`).
        *   Se creó el método `store` en `app/Http/Controllers/Admin/InstitucionController.php` con la lógica de validación y guardado (incluyendo manejo de `solicitud_id`).
        *   Se añadió la ruta `POST /superadmin/instituciones` (`admin.instituciones.store`) en `routes/web.php`.
        *   Se creó el nuevo componente Blade `resources/views/components/modals/institucion-form.blade.php`.
        *   Se integró el nuevo modal en `resources/views/layouts/app.blade.php` y se eliminaron los archivos Livewire (`app/Livewire/Admin/InstitucionFormModal.php` y `resources/views/livewire/admin/institucion-form-modal.blade.php`).
    *   **Corrección de Apertura de Modales**: Se ajustaron los eventos `dispatch` en `resources/views/layouts/partials/navigation/superadmin.blade.php` a `kebab-case` (`open-productor-form-modal`, `open-institucion-modal`) para coincidir con los listeners de Alpine.js.
    *   **Transiciones Suaves en Modales**: Se añadieron las directivas `x-transition` a los componentes `productor-form.blade.php` e `institucion-form.blade.php`.

---
### **10 de Septiembre de 2025**

*   **Progreso:**
    *   **Se completó la refactorización del `ConfiguracionModal`**. Se migraron todos los componentes de Livewire (`ConfiguracionIcono`, `ConfiguracionModal`, `UpdateStockSettings`) a un único componente de Blade/Alpine. La lógica de backend ahora reside en `SettingsController`. El modal ahora es más grande, tiene una interfaz de pestañas para futura expansión y guarda los cambios de forma asíncrona (AJAX) sin recargar la página.
    *   **Se completó la refactorización del Panel de Notificaciones**. Se reemplazó el componente `Notifications` de Livewire por un componente de Blade/Alpine. La nueva versión es más simple, abre un panel lateral directamente al hacer clic y carga las notificaciones de forma asíncrona desde un nuevo `NotificationsController`. Se eliminaron los archivos de Livewire correspondientes.
    *   **Mejora de UX: Pantalla de Carga Global**. Se implementó una pantalla de carga (`loader`) en toda la aplicación para enmascarar el parpadeo de renderizado de componentes. El loader aparece instantáneamente en la navegación (incluyendo la navegación SPA de Livewire) y se desvanece suavemente cuando la página de destino está completamente cargada.
    *   **Centralización de Lógica Alpine.js**. Se movió el código JavaScript de los componentes refactorizados a `resources/js/app.js` para asegurar una inicialización robusta y evitar conflictos con el ciclo de vida de Livewire.
    *   **Verificación de Refactorización**: Se ha confirmado que los componentes `GestionarSolicitudes` e `ImportarProductores` ya han sido refactorizados para seguir la arquitectura de Controlador + Vista, eliminándolos de las tareas pendientes.

*   **Estado Actual:** La base de componentes de la interfaz ha sido significativamente modernizada, mejorando el rendimiento y la mantenibilidad. El sistema está estable.

*   **Próximo paso:** Continuar aplicando el patrón de refactorización a los componentes restantes.

---
### **09 de Septiembre de 2025**

*   **Progreso:**
    *   Análisis inicial de la arquitectura del proyecto y diagnóstico del uso de componentes de página completa de Livewire como anti-patrón.
    *   Creación de este documento de planificación.
    *   **Se completó la refactorización de la ruta `admin.productores.listado`** como prueba de concepto, separando el componente en un controlador y una vista Blade que contiene al componente de Livewire (ahora más pequeño).
    *   **Se completó la refactorización de la ruta `admin.productores.panel`**, reemplazando el componente de Livewire por un controlador y una vista Blade estática.
    *   **Se completó la refactorización de la ruta `admin.instituciones.panel`** siguiendo el mismo patrón.
    *   **Se diagnosticó y solucionó un error `Failed to open stream`**, identificando la causa raíz en el sistema de pestañas del panel de administración.
    *   **Se refactorizó el núcleo del panel de administración:**
        *   Se reescribió el menú de navegación (`layouts/partials/navigation/superadmin.blade.php`) para usar enlaces `<a>` estándar en lugar de eventos de Alpine/Livewire.
        *   Se desmanteló el sistema de pestañas que orquestaba los componentes desde `livewire/admin/admin-panel.blade.php`.
        *   Se reemplazó el componente principal `AdminPanel` por un nuevo `AdminController` y una vista estática (`admin/panel.blade.php`) que ahora funciona como el dashboard principal.
        *   Se eliminaron todos los componentes de Livewire refactorizados (`AdminPanel`, `ProductorPanel`, `InstitucionPanel`) y sus vistas.

*   **Estado Actual:** La arquitectura del panel de administración ha sido migrada con éxito a un enfoque de Controladores y Vistas Blade, solucionando los errores de raíz y la deuda técnica. La base para continuar con el resto de la aplicación es ahora sólida y limpia.

*   **Próximo paso:** Aplicar este mismo patrón a los componentes restantes y a otras secciones de la aplicación (Panel de Productor).

---
### **Refactorización del Modal de Configuración (`ConfiguracionModal`)**

*   **Objetivo:** Convertir el modal de Livewire a un modal de Blade/Alpine.js para mejorar el rendimiento y la ligereza, y centralizar su lógica en un controlador estándar de Laravel.
*   **Progreso Actual:**
    *   Se analizó el componente `ConfiguracionModal.php` (shell del modal) y `UpdateStockSettings.php` (formulario real).
    *   Se creó el controlador `App\Http\Controllers\Admin\SettingsController.php`.
*   **Proceso a realizarse:**
    1.  **Implementar el método `store`** en `SettingsController` (copiando la lógica de validación y guardado del antiguo componente `UpdateStockSettings`).
    2.  **Crear la Ruta `POST`** en `routes/web.php` que apunte a `SettingsController@store`.
    3.  **Crear el Nuevo Modal (Componente de Blade + Alpine):**
        *   Crear `resources/views/components/modals/configuracion.blade.php`.
        *   Contendrá el HTML del modal y el formulario, con Alpine.js para la visibilidad y el formulario estándar.
        *   Aplicar estilos para el tamaño (3/5 de pantalla, centrado).
    4.  **Refactorizar el Icono-Disparador:**
        *   Eliminar `app/Livewire/ConfiguracionIcono.php` y su vista.
        *   En `resources/views/navigation-menu.blade.php`, reemplazar `@livewire('configuracion-icono')` por un botón con `@click="$dispatch('open-configuracion-modal')"`.
    5.  **Integrar y Cargar Datos (View Composer):**
        *   Crear un `ViewServiceProvider` (o usar `AppServiceProvider`) para compartir la instancia de `ConfiguracionActualizacion` con el layout principal (`layouts.app`).
        *   Modificar `layouts/app.blade.php` para incluir el nuevo modal (`<x-modals.configuracion :configuracion="$configuracion" />`) y eliminar las inclusiones de los antiguos modales de Livewire.
    6.  **Limpieza Final:**
        *   Eliminar `app/Livewire/Admin/ConfiguracionModal.php` y su vista.
        *   Eliminar `app/Livewire/Admin/Settings/UpdateStockSettings.php` y su vista.

---
### **12 de Septiembre de 2025 - Refactorización del Formulario de Creación de Unidad Productiva**

**Resumen del Progreso:**

Hemos iniciado la refactorización del componente Livewire `CrearUnidadProductiva` a un flujo de Controlador y Vistas de Blade, manteniendo la funcionalidad y apariencia originales.

1.  **Componente Livewire Antiguo:** Renombramos `CrearUnidadProductiva.php` y su vista a archivos `.bak` para desactivarlos y mantenerlos como referencia.
2.  **Controlador Nuevo:** Creamos `app/Http/Controllers/Productor/UnidadProductivaController.php` para manejar la lógica del formulario.
3.  **Vistas Blade Nuevas:** Creamos `resources/views/productor/unidades-productivas/create/step-1.blade.php` y `step-3.blade.php`. Adaptamos su contenido de la vista Livewire original, reemplazando directivas de Livewire por HTML/Blade estándar y corrigiendo el uso de componentes de Blade (`<x-label>`, `<x-input>`, `<x-input-error>`).
4.  **Rutas Explícitas:** Actualizamos `routes/web.php` para definir rutas claras para cada paso del formulario (`/crear`, `/crear/paso-1`, `/crear/paso-3`, `/crear/finalizar`), apuntando a los métodos del nuevo controlador.
5.  **Gestión de Estado:** Implementamos el uso de la sesión de Laravel en el controlador para guardar los datos del formulario entre los pasos.
6.  **Adaptación del Controlador del Mapa:** Modificamos `MapController@store` para que, después de guardar la ubicación, redirija al `Paso 3` del nuevo formulario.
7.  **Lógica de Guardado Final:** Implementamos los métodos `createStep3` y `store` en `UnidadProductivaController` para el manejo del último paso y el guardado final en la base de datos.
8.  **Desplegable de Parajes Dinámico:**
    *   Creamos un controlador de API (`app/Http/Controllers/Api/ParajeController.php`) y una ruta de API (`GET /api/municipios/{municipio}/parajes`) para obtener los parajes de un municipio.
    *   Añadimos JavaScript a `step-1.blade.php` para cargar dinámicamente los parajes cuando se selecciona un municipio.

**Error Actual:**

El problema actual es un error `401 Unauthorized` cuando el JavaScript en `step-1.blade.php` intenta hacer la llamada a la API para obtener los parajes.

*   Aunque el JavaScript fue actualizado para enviar el token CSRF (`X-CSRF-TOKEN`), la petición sigue siendo rechazada por el servidor.
*   El último mensaje de error específico en la consola fue `at updateParajes (crear:298:49)`, lo que indica un problema dentro de la función JavaScript `updateParajes`.

**Próximos Pasos (para cuando retomemos):**

El siguiente paso es depurar este error `401 Unauthorized`. Esto probablemente implica revisar la configuración de Laravel Sanctum para asegurar que las peticiones de la API desde el frontend sean reconocidas como autenticadas por sesión.

*   **Verificar `bootstrap/app.php`:** Necesitamos inspeccionar este archivo para confirmar que el middleware de Sanctum (`EnsureFrontendRequestsAreStateful` o su equivalente en Laravel 12) está configurado correctamente para las rutas de la API.
*   **Revisar `SANCTUM_STATEFUL_DOMAINS`:** Asegurarnos de que el dominio de la aplicación esté correctamente listado en la variable de entorno `SANCTUM_STATEFUL_DOMAINS` en el archivo `.env`.
