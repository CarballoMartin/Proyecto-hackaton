# Plan de Refactorización: Módulo de Mapas Modulares

## 1. Objetivo

El objetivo es refactorizar la funcionalidad de mapas existente en un sistema modular, escalable y reutilizable. Esto nos permitirá servir diferentes capas de información geoespacial a diferentes roles de usuario (Productor, Superadmin, Institución) utilizando un único mapa base y una arquitectura de componentes desacoplados.

## 2. Arquitectura Propuesta

Se implementará una arquitectura de 3 capas, similar a la del módulo de estadísticas, para asegurar la separación de responsabilidades y la máxima flexibilidad.

### a. Capa de Servicio de Datos (`MapDataService`)

*   **Responsabilidad**: Realizar consultas a la base de datos para obtener datos geoespaciales **crudos** (coordenadas, GeoJSON, etc.). Es agnóstico a la presentación.
*   **Ubicación**: `app/Services/MapDataService.php`
*   **Métodos Clave**: `getProductorLocations(User $user = null)`, `getTransportRoutes()`, `getUnidadProductivaPerimeter(UnidadProductiva $up)`.
*   **Modularidad**: Los métodos aceptarán parámetros opcionales para filtrar datos según el solicitante. Si no se especifica un usuario o el usuario es un administrador, se devolverán los datos globales.

### b. Constructores de Capas (`Layer Builders`)

Esta es la capa de "módulos". Cada funcionalidad del mapa (ej. marcadores de productores, editor de parcelas) será una clase autocontenida.

*   **Interfaz (`MapLayerInterface`)**: Un contrato que toda capa debe cumplir.
    *   **Ubicación**: `app/Interfaces/MapLayerInterface.php`
    *   **Método Requerido**: `build(User $user): ?array` - Devuelve la configuración de la capa en formato de array (que se convertirá a JSON) o `null` si el usuario no tiene permiso para verla.

*   **Implementaciones Concretas**: Clases que implementan la interfaz.
    *   `ProductorLayerBuilder.php`: Construye la capa de marcadores de productores.
    *   `LogisticaLayerBuilder.php`: Construye la capa de seguimiento de transportes.
    *   `EditorPerimetrosBuilder.php`: Construye la capa que activa las herramientas de dibujo para un productor en su propia chacra.
    *   **Ubicación**: `app/Map/LayerBuilders/`

### c. Orquestador de Mapa (`MapBuilderService`)

*   **Responsabilidad**: Ensamblar la configuración final del mapa para un usuario específico.
*   **Ubicación**: `app/Services/MapBuilderService.php`
*   **Flujo de Trabajo**:
    1.  Se inyecta con un array de todas las implementaciones de `MapLayerInterface` disponibles (vía Service Provider).
    2.  Recibe un usuario en su método `buildFor(User $user)`.
    3.  Itera sobre todos los `LayerBuilders` registrados.
    4.  Para cada uno, invoca el método `build($user)`. Este método internamente verificará los permisos del usuario (`$user->can(...)`) para decidir si devuelve los datos de la capa o `null`.
    5.  Recolecta todas las configuraciones de capa no nulas y las ensambla en un único array listo para ser enviado a la vista como JSON.

## 3. Plan de Implementación Paso a Paso

### Fase 1: Crear la Arquitectura Base

1.  [ ] Crear la interfaz `app/Interfaces/MapLayerInterface.php`.
2.  [ ] Crear el servicio `app/Services/MapDataService.php` (con métodos vacíos).
3.  [ ] Crear el servicio `app/Services/MapBuilderService.php`.
4.  [ ] Crear el directorio `app/Map/LayerBuilders/`.
5.  [ ] Registrar los servicios en el Service Container (`AppServiceProvider.php`). Se registrará `MapBuilderService` y se le inyectarán todas las clases que implementen `MapLayerInterface`.

### Fase 2: Implementar la Primera Capa (Ubicación de Productores)

1.  [ ] **Backend**: Implementar la lógica en `MapDataService` para obtener las coordenadas de los productores.
2.  [ ] **Backend**: Crear la clase `app/Map/LayerBuilders/ProductorLayerBuilder.php`. En su método `build()`, verificará el permiso (`$user->can('view-any-productor-location')`), llamará al `MapDataService` y formateará los datos como una capa GeoJSON.
3.  [ ] **Permisos**: Crear el Gate o Policy para `view-any-productor-location`.

### Fase 3: Implementar el Orquestador y la Vista

1.  [ ] **Backend**: Implementar la lógica en `MapBuilderService` para que itere sobre los constructores de capas y ensamble la configuración.
2.  [ ] **Controlador**: Crear un `MapController` que inyecte el `MapBuilderService`, llame a `buildFor(auth()->user())` y pase la configuración a una vista.
3.  [ ] **Frontend**: Crear la vista `map.blade.php` con un contenedor `<div>` para el mapa. Añadir un script que inicialice una librería (ej. Leaflet.js) y use la variable JSON para renderizar las capas.

### Fase 4: Implementar Capas Adicionales

1.  [ ] Implementar la capa para que un productor marque su ubicación (ej. `EditorUbicacionBuilder`). Esta capa solo se activará si el usuario tiene el permiso `edit-own-location` y está viendo su propio perfil.
2.  [ ] Implementar la capa de logística, y así sucesivamente, creando nuevos `LayerBuilders` y permisos según sea necesario.

## 4. Consideraciones Clave

*   **Permisos Primero**: El sistema depende de que la lógica de autorización se base en permisos abstractos (`Gates` o `Policies` de Laravel), no en roles. Esto es crucial para la modularidad.
*   **Librería de Frontend**: La arquitectura es agnóstica a la librería de mapas (Leaflet, OpenLayers, Mapbox). La única parte que necesita conocer la librería es el script en la vista final.
*   **Rendimiento**: Para capas con muchos datos, se debe considerar la carga asíncrona de datos o el uso de caché en el `MapDataService`.
