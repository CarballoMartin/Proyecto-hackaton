# Módulo Ambiental - Documentación Detallada

Este documento describe en detalle el módulo ambiental para productores: objetivos, componentes, vistas, flujos de datos, modelos involucrados y consideraciones de uso.

## Objetivo
- Monitorear el estado ambiental de las unidades productivas combinando clima, vegetación (NDVI) y suelo.
- Detectar y gestionar alertas ambientales con acciones recomendadas.
- Proveer un dashboard integrado y vistas específicas para NDVI y Suelo.

## Arquitectura general
- Framework: Laravel + Livewire + Blade + TailwindCSS.
- Componentes Livewire principales:
  - `App\Livewire\Productor\Ambiental\DashboardIntegrado`
  - `App\Livewire\Productor\Ambiental\General`
  - `App\Livewire\Productor\Ambiental\Ndvi`
  - `App\Livewire\Productor\Ambiental\Suelo`
  - `App\Livewire\Productor\Ambiental\AlertasDetalle`
- Servicios externos/propios usados:
  - `App\Services\DashboardAmbientalService`
  - `App\Services\AlertaAmbientalService`
  - `App\Services\SatelitalApi\CopernicusApiService` (NDVI)
  - `App\Services\SueloApi\SoilGridsApiService` (suelo)
- Modelos clave: `Productor`, `UnidadProductiva`, `AlertaAmbiental`, `IndiceVegetacion`, `DatoClimaticoCache`, `CaracteristicaSuelo`.

## Componentes y flujo de datos

### DashboardIntegrado
Archivo: `app/Livewire/Productor/Ambiental/DashboardIntegrado.php`
Vista: `resources/views/livewire/productor/ambiental/dashboard-integrado.blade.php`

- Props/estado:
  - `productor`: productor autenticado.
  - `dashboard`: arreglo con métricas agregadas (clima, NDVI, suelo, certificación, recomendaciones).
  - `alertasActivas`: colección de `AlertaAmbiental` filtradas por unidad y período.
  - Flags: `cargando`, `actualizando`.
  - Filtros: `filtroUnidad` ('todas' o id), `filtroPeriodo` (7/30/90 días).
- Ciclo de vida:
  - `mount()`: resuelve `productor` y llama `cargarDashboard()` si existe.
  - `boot()`: inyecta `DashboardAmbientalService` y `AlertaAmbientalService`.
- Acciones:
  - `updatedFiltroUnidad()` / `updatedFiltroPeriodo()`: recarga dashboard.
  - `actualizarDatos()`: para cada `UnidadProductiva` con coordenadas, genera y guarda alertas vía `AlertaAmbientalService`, luego recarga dashboard y muestra éxito.
  - `marcarAlertaComoLeida($id)`: marca notificada y refiltra.
  - `desactivarAlerta($id)`: desactiva y refiltra.
- Lógica de filtrado:
  - `filtrarAlertas()`: filtra por unidades del productor, período (`Carbon::now()->subDays(filtroPeriodo)`), ordena por severidad y fecha.
- Renderiza dashboard con:
  - Resumen general (estado, cobertura de datos, alertas críticas, calidad promedio).
  - Métricas por categoría: clima, NDVI, suelo.
  - Certificación ambiental (porcentaje, desglose, nivel).
  - Listado de alertas activas y recomendaciones.

### General
Archivo: `app/Livewire/Productor/Ambiental/General.php`
Vista: `resources/views/livewire/productor/ambiental/general.blade.php` (contenida en layout-tab)

- Recolecta para el tablero general:
  - `alertasActivas`: `AlertaAmbiental::activas()` para unidades del productor (orden por nivel y fecha).
  - `alertasCriticas`: subconjunto crítico/alto.
  - `datosClima`: último `DatoClimaticoCache`.
  - `unidadesConAlertas`: agrupadas por unidad, con conteo y nivel máximo.
  - `estadisticas`: totales y agregados de alertas (críticas, altas, unidades afectadas, tipo más común).
- Renderiza tarjetas resumen, widget de clima `@livewire('productor.clima-widget')`, listado de unidades con alertas y navegación por tabs.

### Ndvi
Archivo: `app/Livewire/Productor/Ambiental/Ndvi.php`
Vista: `resources/views/livewire/productor/ambiental/ndvi.blade.php`

- Props/estado: `productor`, `unidadesProductivas`, `unidadSeleccionada`, `periodo` (30/90/180/365), `datosNdvi`, `estadisticas`, flags `cargando`/`actualizando`.
- Ciclo de vida: en `mount()`, carga unidades con coordenadas y selecciona primera para iniciar datos.
- Datos históricos: consulta `IndiceVegetacion` por `unidad_productiva_id` y `fecha_imagen >= now()-periodo`.
- Acciones:
  - `updatedUnidadSeleccionada()` / `updatedPeriodo()`: recarga datos.
  - `actualizarDatos()`: usa `CopernicusApiService->obtenerNDVI($unidad)` y `guardarDatosNDVI(...)` para refrescar, recarga y muestra flash de éxito.
  - `actualizarTodasLasUnidades()`: dispara actualización masiva `CopernicusApiService->actualizarDatosNDVI()` y reporta resultados.
- Estadísticas calculadas:
  - promedio, máximo, mínimo, tendencia (`IndiceVegetacion::tendenciaNdviUnidad`), clasificación/estado recientes, conteo y porcentaje de datos confiables (nubosidad <= 20%).
- UI: filtros de unidad y período, tarjetas de resumen, gráfico de tendencia con Chart.js, tabla histórica con confiabilidad y clasificación.

### Suelo
Archivo: `app/Livewire/Productor/Ambiental/Suelo.php`
Vista: `resources/views/livewire/productor/ambiental/suelo.blade.php`

- Props/estado: `productor`, `unidadesProductivas`, `unidadProductivaId`, `caracteristicasSuelo`, `recomendaciones`, `recomendacionesPasturas`, flags `cargando`/`actualizando`.
- Ciclo de vida: `mount()` setea timeout, carga unidades (id, nombre, lat, lon) sin auto-consulta para evitar timeouts.
- Acciones:
  - `updatedUnidadProductivaId()`: carga datos de la unidad.
  - `cargarDatos()`: obtiene último `CaracteristicaSuelo` de la unidad, normaliza recomendaciones y recomendaciones de pasturas a arrays.
  - `actualizarDatos()`: genera datos realistas (temporal) con `generarDatosSueloRealistas($unidad)`, los guarda con `guardarDatosSueloLocal(...)`, recarga y muestra éxito. Loggea pasos para trazabilidad.
  - `actualizarTodasLasUnidades()`: usa `SoilGridsApiService->actualizarDatosSuelo()` y notifica resultados.
- Cálculos auxiliares: capacidad de retención de agua; clasificaciones de textura, pH y calidad.
- UI: filtros, mensajes de éxito/error, tarjetas de resumen (estado general, índice de calidad, textura), bloques de pH, materia orgánica, composición (arcilla/limo/arena), nutrientes (N, P, K, CEC), y secciones de recomendaciones y pasturas.

### AlertasDetalle
Archivo: `app/Livewire/Productor/Ambiental/AlertasDetalle.php`
Vista: `resources/views/livewire/productor/ambiental/alertas-detalle.blade.php`

- Filtros: `filtroTipo` (sequia/tormenta/estres_termico/helada), `filtroNivel` (critico/alto/medio/bajo), `filtroEstado` (activas/todas/inactivas), `busqueda`.
- Paginación con `WithPagination` (10 por página) y querystring sincronizado.
- Acciones: marcar como leída (`marcarLeida`), desactivar (`desactivarAlerta`), reactivar (`reactivarAlerta`), limpiar filtros.
- Seguridad de acceso: verifica que la alerta pertenezca al productor autenticado (`perteneceAlProductor`).
- Render: lista con badges de nivel/estado, contexto por tipo, recomendaciones expandibles y acciones.

## Vistas y navegación
- `general.blade.php` actúa como contenedor con tabs (General, Alertas, Histórico, NDVI, Suelo, Configuración) y embebe componentes Livewire.
- `dashboard-integrado.blade.php` muestra el tablero unificado con KPIs, métricas por categoría, certificación y alertas.
- `ndvi.blade.php` y `suelo.blade.php` presentan vistas específicas con filtros y resultados.

## Dependencias y datos
- Autenticación: todas las consultas derivan del `Productor` asociado a `Auth::id()`.
- Coordenadas: varias funciones requieren `latitud` y `longitud` en `UnidadesProductivas`.
- Servicios externos: Copernicus (Sentinel-2) para NDVI; SoilGrids (FAO) para suelo. En `Suelo`, existe generación local temporal de datos simulados si la API no responde.
- Modelos involucrados: ver sección Arquitectura general.

## Estados, errores y UX
- Flags `cargando`/`actualizando` bloquean acciones y muestran loaders.
- Manejo de errores: `addError(...)` y `session()->flash('success', ...)` en operaciones clave.
- En vistas: mensajes vacíos (“Sin datos”) con CTA para actualizar.

## Buenas prácticas y rendimiento
- Evitar cargas iniciales pesadas: en `Suelo`, no se consulta automáticamente hasta seleccionar unidad.
- Uso de `set_time_limit(30)` para evitar timeouts en inicialización de `Suelo`.
- Consultas acotadas por período (`Ndvi`) y `select()` de campos necesarios (unidades en `Suelo`).

## Cómo operar el módulo
- Dashboard Integrado: ajustar filtros de unidad y período, clic en “Actualizar Dashboard”.
- NDVI: seleccionar unidad y período; si no hay datos, usar “Obtener Datos NDVI”.
- Suelo: seleccionar unidad; usar “Actualizar Datos de Suelo” para consultar/generar y guardar.
- Alertas: gestionar desde `AlertasDetalle` (leer, desactivar, reactivar) y desde tarjetas del dashboard.

## Futuras extensiones
- Completar integración real con `SoilGridsApiService` y robustecer manejo de errores.
- Incorporar mapas y tiles satelitales en NDVI.
- Exportaciones (CSV/PDF) de series y reportes.
