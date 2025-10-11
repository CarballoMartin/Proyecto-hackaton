# Plan de Implementación: Módulo de Estadísticas

Este documento detalla la arquitectura y los pasos para implementar el nuevo módulo de estadísticas y reportes del sistema, asegurando que sea una solución robusta, modular, escalable y fácil de mantener.

## 1. Objetivo

El objetivo es centralizar la lógica de negocio para la generación de datos estadísticos y desacoplar la aplicación de tecnologías específicas de frontend (como librerías de gráficos). Esto permitirá reutilizar la lógica para diferentes roles (Productor, Administrador) y facilitará futuras actualizaciones o cambios tecnológicos.

## 2. Arquitectura Propuesta

Se implementará una arquitectura de tres capas desacopladas para manejar la generación de estadísticas:

### a. Capa de Servicio de Datos (`EstadisticasService`)

*   **Responsabilidad**: Es el cerebro de la operación. Su única función es realizar consultas complejas a la base de datos para obtener **datos crudos** y agnósticos a la presentación.
*   **Ubicación**: `app/Services/EstadisticasService.php`
*   **Características Clave**:
    *   Contendrá métodos como `getComposicionPorEspecie()`, `getEvolucionStockMensual()`, etc.
    *   Cada método aceptará parámetros opcionales como `$productor` y `$filtros` (un array con fechas, IDs de chacras, etc.).
    *   Si no recibe un productor, calculará estadísticas globales (para el rol de Admin). Si lo recibe, filtrará los datos para ese productor.

### b. Adaptador de Gráficos (`ChartBuilder`)

*   **Responsabilidad**: Actuar como un "traductor". Toma los datos crudos del `EstadisticasService` y los formatea en la estructura JSON específica que requiere la librería de frontend (en nuestro caso, Chart.js). Esto nos independiza de la librería.
*   **Componentes**:
    1.  **Interfaz (`ChartBuilderInterface`)**: Un contrato que define qué métodos debe tener cualquier constructor de gráficos. Ej: `buildPieChart(array $data)`, `buildLineChart(array $data)`.
        *   **Ubicación**: `app/Interfaces/ChartBuilderInterface.php`
    2.  **Implementación (`ChartJsBuilder`)**: La clase concreta que implementa la interfaz y genera el JSON para Chart.js.
        *   **Ubicación**: `app/Services/ChartJsBuilder.php`

### c. Controladores (Ej: `ProductorController`)

*   **Responsabilidad**: Ser el "director de orquesta". Su código será muy simple y legible.
*   **Flujo de trabajo**:
    1.  Recibe la petición HTTP y extrae los filtros.
    2.  Pide los datos crudos al `EstadisticasService`.
    3.  Pasa esos datos crudos al `ChartJsBuilder` para obtener el JSON formateado.
    4.  Pasa el JSON final a la vista Blade.

## 3. Plan de Implementación Paso a Paso

Seguiremos este plan en orden para asegurar una construcción metódica.

### Fase 1: Creación de la Arquitectura Base

1.  [x] Crear el archivo de la interfaz `app/Interfaces/ChartBuilderInterface.php`.
2.  [x] Crear la clase `app/Services/ChartJsBuilder.php` que implemente la interfaz (con métodos vacíos).
3.  [x] Crear la clase `app/Services/EstadisticasService.php` (con métodos vacíos).
4.  [x] Registrar `ChartBuilderInterface` y `EstadisticasService` en el Service Container de Laravel (`AppServiceProvider.php`) para que se puedan inyectar automáticamente en los controladores.

### Fase 2: Implementación del Primer Gráfico (Composición por Especie)

1.  [x] **Backend**: Implementar la lógica en `EstadisticasService` (`getComposicionPorEspecie`) y en `ChartJsBuilder` (`buildPieChart`).
    *   **Progreso**: Se implementó el método `getComposicionPorEspecie` en `EstadisticasService` para obtener los totales de stock por especie.
2.  [ ] **Controlador**: Modificar `ProductorController@estadisticas` para inyectar y usar los servicios, y pasar el JSON del gráfico a la vista.
3.  [ ] **Frontend**: Modificar la vista `productor.estadisticas.index` para que el script de Chart.js use la variable JSON que viene del controlador, reemplazando los datos de ejemplo.

### Fase 3: Implementación de Gráficos Adicionales

1.  [ ] Implementar el gráfico de "Composición por Categoría" (Gráfico de Barras), repitiendo el flujo de la Fase 2.
2.  [ ] Implementar el gráfico de "Evolución del Stock" (Gráfico de Líneas), repitiendo el flujo de la Fase 2.

### Fase 4: Implementación de Filtros

1.  [ ] **Backend**: Modificar los métodos en `EstadisticasService` para que acepten el array `$filtros` y lo apliquen a las consultas de base de datos (usando `when()` de Eloquent).
2.  [ ] **Controlador**: Actualizar el método `estadisticas` para que recoja los parámetros de la `Request` HTTP, los valide y los pase al `EstadisticasService`.
3.  [ ] **Frontend**: Hacer que el formulario de filtros en la vista envíe los datos para recargar la página con los parámetros en la URL.

### Fase 5: Implementación para el Panel de Administrador

1.  [ ] Crear un `Admin\EstadisticasController`.
2.  [ ] Reutilizar `EstadisticasService` y `ChartJsBuilder` sin pasarle un productor para obtener datos globales.
3.  [ ] Crear las vistas Blade para el panel de administrador.