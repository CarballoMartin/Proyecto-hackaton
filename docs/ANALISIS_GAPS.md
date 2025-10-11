# 🚨 GAPS E INCONSISTENCIAS DETALLADAS

**Documento anexo al Análisis Completo del Proyecto**  
**Fecha:** 11 de Octubre de 2025

---

## ÍNDICE

1. [Gaps de Arquitectura](#1-gaps-de-arquitectura)
2. [API para Móvil Incompleta](#2-api-para-móvil-incompleta)
3. [Panel Institucional Incompleto](#3-panel-institucional-incompleto)
4. [Testing Insuficiente](#4-testing-insuficiente)
5. [Base de Datos](#5-base-de-datos)
6. [Seguridad](#6-seguridad)
7. [Performance](#7-performance)
8. [Frontend](#8-frontend)
9. [Deployment](#9-deployment)
10. [Tareas Programadas](#10-tareas-programadas)
11. [Archivos Obsoletos](#11-archivos-obsoletos)
12. [Git y Control de Versiones](#12-git-y-control-de-versiones)

---

## 1. GAPS DE ARQUITECTURA

### 1.1 Migración Livewire Incompleta

**Problema:**
El proyecto está en proceso de refactorización desde componentes de página completa de Livewire hacia controladores tradicionales de Laravel. Esta migración está incompleta.

**Estado Actual:**
- ✅ 5-6 componentes ya refactorizados (archivos .bak)
- ⚠️ 17 componentes Livewire activos
- ⚠️ Algunos componentes son de página completa (anti-patrón)

**Componentes Problemáticos:**
1. `GestionarUnidadProductiva.php` - Componente complejo de página completa
2. `Dashboard.php` (Productor) - Debería ser controlador
3. `Dashboard.php` (Institucional) - Debería ser controlador

**Impacto:**
- ❌ **Rendimiento:** Overhead de Livewire innecesario
- ❌ **Mantenibilidad:** Código mixto (Livewire + Controllers)
- ❌ **Complejidad:** Dificulta onboarding de nuevos desarrolladores
- ❌ **Deuda Técnica:** Aumenta con cada día sin refactorizar

**Solución Propuesta:**

**Fase 1 (Alta Prioridad - 1 semana):**
```php
// Componentes a refactorizar:
- Productor\Dashboard.php → ProductorController@dashboard
- Institucional\Dashboard.php → InstitucionController@dashboard
```

**Fase 2 (Media Prioridad - 2 semanas):**
```php
// Evaluar si GestionarUnidadProductiva puede ser refactorizado
// o si su complejidad justifica mantenerlo en Livewire
```

**Fase 3 (Baja Prioridad - 1 semana):**
```php
// Limpiar archivos .bak y actualizar documentación
```

**Criterios para Mantener en Livewire:**
- Interactividad compleja en tiempo real
- Actualizaciones frecuentes sin reload
- Formularios multi-paso con validación instantánea
- Componentes reutilizables pequeños

**Criterios para Migrar a Controllers:**
- Páginas estáticas o semi-estáticas
- Listados simples con paginación
- Formularios simples
- Dashboards con datos que no cambian frecuentemente

---

### 1.2 Archivos .bak

**Problema:**
Existen archivos .bak en el proyecto que indican componentes refactorizados pero no eliminados.

**Archivos Encontrados:**
```
app/Livewire/Productor/Parajes/CrearParajeModal.php.bak
app/Livewire/Productor/UnidadesProductivas/CrearUnidadProductiva.php.bak
routes/web.php.bak
```

**Vistas .bak:**
```
resources/views/livewire/productor/unidades-productivas/crear-unidad-productiva.blade.php (posible .bak)
```

**Impacto:**
- ⚠️ **Confusión:** Desarrolladores no saben si son necesarios
- ⚠️ **Espacio:** Ocupan espacio innecesario
- ⚠️ **Versionamiento:** Ruido en el repositorio

**Solución:**

**Opción A - Eliminar (Recomendada):**
```bash
# Si el código nuevo funciona correctamente
find app/ -name "*.bak" -delete
find resources/ -name "*.bak" -delete
find routes/ -name "*.bak" -delete
```

**Opción B - Archivar:**
```bash
# Si hay incertidumbre
mkdir archive/
mv app/Livewire/Productor/Parajes/CrearParajeModal.php.bak archive/
# ... etc
# Luego commitear con mensaje claro
```

**Opción C - Documentar:**
```markdown
# En un archivo ARCHIVED_COMPONENTS.md
- CrearParajeModal.php.bak: Reemplazado por ParajeController + modal Blade
- CrearUnidadProductiva.php.bak: Reemplazado por UnidadProductivaController
- Fecha de reemplazo: 12 de septiembre de 2025
- Razón: Migración de arquitectura Livewire → Controllers
```

---

### 1.3 Inconsistencia de Nomenclatura

**Problema:**
Uso mixto de "Chacra" vs "Unidad Productiva" en el código.

**Ejemplos:**
```php
// En rutas
Route::get('/productor/chacras/{id}/mapa', ...);      // Usa "chacras"
Route::get('/productor/unidades-productivas', ...);   // Usa "unidades-productivas"

// En vistas
"Mis Chacras"              // UI visible
"Gestionar Unidad Productiva"  // UI visible

// En código
$unidadProductiva->...     // Variable
UnidadProductiva::class    // Modelo
```

**Impacto:**
- ⚠️ **Confusión:** Desarrolladores no saben qué término usar
- ⚠️ **Búsqueda:** Dificulta encontrar código relacionado
- ⚠️ **Comunicación:** Ambigüedad en conversaciones técnicas

**Solución:**

**Estándar Propuesto:**
```php
// CÓDIGO (Backend, URLs): "unidad-productiva" / "unidadProductiva"
Route::get('/productor/unidades-productivas/{id}', ...);
$unidadProductiva = UnidadProductiva::find($id);

// UI (Frontend visible): "Chacra" (término coloquial preferido por usuarios)
<h1>Mis Chacras</h1>
<button>Agregar Chacra</button>

// DOCUMENTACIÓN: Ambos, pero especificar
"Unidad Productiva (Chacra)" en la primera mención
```

**Script de Migración (opcional):**
```bash
# Renombrar rutas inconsistentes
# Esto es opcional y puede causar breaking changes en frontend
sed -i 's/\/chacras\//\/unidades-productivas\//g' routes/web.php
```

**Recomendación:**
Mantener el estándar actual pero documentarlo claramente en un `GLOSSARY.md`:
- **Código:** unidad-productiva
- **UI:** Chacra (más amigable para usuarios finales)

---

## 2. API PARA MÓVIL INCOMPLETA

### Estado Actual

**Implementado (30%):**
- ✅ Autenticación con OTP
- ✅ Login sin contraseña
- ✅ Tokens Sanctum
- ✅ Endpoint `/api/user`
- ✅ Endpoint `/api/locations`
- ✅ Endpoint `/api/clima`
- ✅ Endpoint `/api/productor/clima`
- ✅ Endpoint `/api/municipios/{id}/parajes`
- ✅ Endpoint `/api/cuaderno/movimientos-guardados`

**Faltante (70%):**
- ❌ CRUD de unidades productivas
- ❌ Gestión de stock
- ❌ Sincronización offline
- ❌ Notificaciones push
- ❌ Estadísticas
- ❌ Declaraciones
- ❌ Perfil de usuario

### Endpoints Faltantes Detallados

#### 2.1 Unidades Productivas

```php
// Falta implementar:
GET    /api/unidades-productivas              // Listar UPs del productor
GET    /api/unidades-productivas/{id}         // Ver detalle de UP
POST   /api/unidades-productivas              // Crear nueva UP
PUT    /api/unidades-productivas/{id}         // Actualizar UP
DELETE /api/unidades-productivas/{id}         // Eliminar UP

// Respuesta esperada para GET /api/unidades-productivas:
{
  "data": [
    {
      "id": 1,
      "nombre": "Campo San José",
      "identificador_local": "12.345.6.78901/23",
      "superficie": 100.5,
      "municipio": {
        "id": 1,
        "nombre": "Apóstoles"
      },
      "latitud": -27.9075,
      "longitud": -55.7605,
      "activo": true,
      "completo": true
    }
  ],
  "meta": {
    "total": 10,
    "current_page": 1
  }
}
```

**Controlador sugerido:**
```php
// app/Http/Controllers/Api/UnidadProductivaController.php
class UnidadProductivaController extends Controller
{
    public function index(Request $request)
    {
        $productor = Productor::where('usuario_id', $request->user()->id)->firstOrFail();
        
        return UnidadProductivaResource::collection(
            $productor->unidadesProductivas()
                ->with(['municipio', 'paraje'])
                ->paginate(20)
        );
    }
    
    // ... resto de métodos CRUD
}
```

#### 2.2 Stock Animal

```php
// Falta implementar:
GET    /api/unidades-productivas/{id}/stock    // Stock actual de una UP
POST   /api/unidades-productivas/{id}/stock    // Registrar movimiento
GET    /api/stock/{id}                         // Ver movimiento específico

// Respuesta esperada:
{
  "data": {
    "unidad_productiva_id": 1,
    "stock_actual": [
      {
        "especie": "Ovino",
        "categoria": "Cordero",
        "raza": "Merino",
        "cantidad": 50
      }
    ],
    "ultimo_movimiento": "2025-10-10T15:30:00Z"
  }
}
```

#### 2.3 Declaraciones

```php
// Falta implementar:
GET    /api/declaraciones                      // Listar declaraciones del productor
GET    /api/declaraciones/{id}                 // Ver detalle de declaración
GET    /api/declaraciones/{id}/movimientos     // Movimientos de una declaración

// Para el cuaderno de campo móvil
GET    /api/declaraciones/activa               // Declaración activa actual
POST   /api/declaraciones/activa/movimientos   // Añadir movimientos a la activa
```

#### 2.4 Estadísticas

```php
// Falta implementar:
GET    /api/estadisticas/dashboard             // Estadísticas para dashboard móvil
GET    /api/estadisticas/evolucion             // Evolución de stock

// Respuesta esperada:
{
  "total_animales": 150,
  "total_unidades_productivas": 3,
  "especies": {
    "Ovino": 100,
    "Caprino": 50
  },
  "evolucion_ultimos_6_meses": [
    {"mes": "May 2025", "total": 145},
    {"mes": "Jun 2025", "total": 148},
    // ...
  ]
}
```

### Documentación de API Faltante

**Problema:**
- ❌ No existe documentación OpenAPI/Swagger
- ❌ No hay ejemplos de requests/responses
- ❌ No hay guía de rate limiting
- ❌ No hay changelog de versiones de API

**Solución:**

**Opción A - Laravel Scramble (Recomendada):**
```bash
composer require dedoc/scramble
```

**Opción B - L5-Swagger:**
```bash
composer require darkaonline/l5-swagger
```

**Opción C - Manual:**
Crear `docs/API_DOCUMENTATION.md` con:
- Listado de endpoints
- Ejemplos de request/response
- Códigos de error
- Rate limiting
- Versionamiento

### Sincronización Offline

**Problema:**
La app móvil necesitará funcionar offline para productores en áreas rurales sin conectividad constante.

**Requerimientos:**
1. **Cola de Sincronización:**
   - Almacenar movimientos localmente
   - Sincronizar cuando hay conexión
   - Resolver conflictos

2. **Estrategia de Resolución de Conflictos:**
   - Last-write-wins
   - Timestamps de modificación
   - Versioning optimista

3. **Endpoints de Sync:**
```php
POST /api/sync/movimientos    // Sincronizar movimientos pendientes
POST /api/sync/check          // Verificar qué necesita sincronizar
GET  /api/sync/status         // Estado de sincronización
```

### Estimación de Tiempo

| Tarea | Tiempo Estimado | Prioridad |
|-------|-----------------|-----------|
| CRUD Unidades Productivas | 3-4 días | CRÍTICA |
| Stock Animal API | 4-5 días | CRÍTICA |
| Declaraciones API | 3-4 días | CRÍTICA |
| Estadísticas API | 2-3 días | ALTA |
| Sincronización Offline | 5-7 días | ALTA |
| Documentación OpenAPI | 2-3 días | MEDIA |
| Notificaciones Push | 3-4 días | MEDIA |
| Tests de API | 4-5 días | ALTA |

**Total: 26-39 días (5-8 semanas)**

---

## 3. PANEL INSTITUCIONAL INCOMPLETO

### Estado Actual (40% completo)

**Implementado:**
- ✅ Dashboard básico con estadísticas
- ✅ Autenticación y middleware de rol
- ✅ Gestión de participantes (CRUD completo)
- ✅ Cache de estadísticas

**Pendiente según `PLAN_IMPLEMENTACION_PANEL_INSTITUCIONAL.md`:**

#### Fase 0: Mejoras del Dashboard (Pendiente)
- ❌ Mejorar tarjeta de estado
- ❌ Agregar métricas de actividad reciente
- ❌ Indicador de salud institucional
- ❌ Últimos participantes agregados

**Estimación:** 2-3 días

#### Fase 2: Sistema de Solicitudes (Pendiente)
- ❌ Listar solicitudes pendientes
- ❌ Aprobar/rechazar solicitudes
- ❌ Historial de decisiones
- ❌ Notificaciones a solicitantes
- ❌ Exportar reportes de solicitudes

**Archivos a crear:**
```php
app/Livewire/Institucional/Solicitudes/
├── GestionarSolicitudes.php
├── RevisarSolicitud.php
└── HistorialSolicitudes.php
```

**Estimación:** 1 semana

#### Fase 3: Reportes y Estadísticas (Pendiente)
- ❌ Dashboard con gráficos interactivos (Chart.js)
- ❌ Reportes por período
- ❌ Estadísticas de crecimiento
- ❌ Exportación a PDF/Excel
- ❌ Métricas de actividad

**Gráficos a implementar:**
- 📊 Participantes por mes (barras)
- 📈 Crecimiento temporal (líneas)
- 🍩 Distribución por tipo (dona)
- 📊 Actividad por período (áreas)

**Estimación:** 2 semanas

#### Fase 4: Perfil Institucional (Pendiente)
- ❌ Editar información básica
- ❌ Subir/actualizar logo
- ❌ Configurar permisos por rol
- ❌ Gestionar contactos
- ❌ Configurar notificaciones

**Estimación:** 1 semana

#### Fase 5: Notificaciones y Comunicación (Pendiente)
- ❌ Centro de notificaciones
- ❌ Mensajes internos
- ❌ Anuncios institucionales
- ❌ Recordatorios automáticos
- ❌ Integración con email/SMS

**Estimación:** 2 semanas

### Total Estimado para Panel Institucional: 6-8 semanas

**Priorización Sugerida:**
1. **Semana 1-2:** Fase 0 (Dashboard) + Fase 2 (Solicitudes)
2. **Semana 3-4:** Fase 3 (Reportes básicos)
3. **Semana 5:** Fase 4 (Perfil)
4. **Semana 6-8:** Fase 3 (Reportes avanzados) + Fase 5 (Notificaciones)

---

## 4. TESTING INSUFICIENTE

### Cobertura Actual: ~35%

**Tests Existentes:**
```
tests/Feature/              14 archivos (Jetstream auth tests)
tests/Unit/                  4 archivos
  - HistorialMovimientosAccionTest.php   (6 tests)
  - StockHistoryServiceTest.php          (3 tests)
  - UserRoleTest.php                     (1 test)
  - ExampleTest.php                      (1 test)
```

**Total: ~23 tests**

### Áreas Sin Cobertura

#### 4.1 Controladores (90% sin tests)

**Sin Tests:**
- ❌ `AdminController`
- ❌ `ProductorController`
- ❌ `InstitucionController`
- ❌ `CuadernoDeCampoController`
- ❌ `UnidadProductivaController`
- ❌ Todos los controladores API (100%)

**Ejemplo de test faltante:**
```php
// tests/Feature/Productor/CuadernoDeCampoTest.php
public function test_productor_puede_guardar_movimientos()
{
    $productor = Productor::factory()->create();
    $up = UnidadProductiva::factory()->create();
    $productor->unidadesProductivas()->attach($up);
    
    $this->actingAs($productor->usuario);
    
    $response = $this->post(route('cuaderno.store'), [
        'upId' => $up->id,
        'movimientos_json' => json_encode([
            [
                'especie_id' => 1,
                'categoria_id' => 1,
                'raza_id' => 1,
                'tipo_registro_id' => 1,
                'cantidad' => 10,
                // ...
            ]
        ])
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('stock_animals', [
        'unidad_productiva_id' => $up->id,
        'cantidad' => 10
    ]);
}
```

#### 4.2 Servicios (100% sin tests)

**Servicios Críticos Sin Tests:**
- ❌ `EstadisticasService`
- ❌ `ChartJsBuilder`
- ❌ `ProductorImporter`
- ❌ `UnidadProductivaImporter`
- ❌ `PdfExportService`
- ❌ `CsvExcelProcessor`

**Ejemplo de test faltante:**
```php
// tests/Unit/Services/EstadisticasServiceTest.php
public function test_calcula_composicion_por_especie_correctamente()
{
    $productor = Productor::factory()->create();
    $up = UnidadProductiva::factory()->create();
    $productor->unidadesProductivas()->attach($up);
    
    // Crear movimientos de prueba
    StockAnimal::factory()->create([
        'unidad_productiva_id' => $up->id,
        'especie_id' => 1, // Ovino
        'cantidad' => 100
    ]);
    
    StockAnimal::factory()->create([
        'unidad_productiva_id' => $up->id,
        'especie_id' => 2, // Caprino
        'cantidad' => 50
    ]);
    
    $service = app(EstadisticasService::class);
    $composicion = $service->getComposicionPorEspecie($productor);
    
    $this->assertEquals(100, $composicion['Ovino']);
    $this->assertEquals(50, $composicion['Caprino']);
}
```

#### 4.3 Actions (100% sin tests)

**Actions Sin Tests:**
- ❌ `CreateProductor`
- ❌ `UpdateProductor`
- ❌ `CreateUnidadProductiva`
- ❌ `GuardarMovimientosAction`
- ❌ `FiltrarMovimientosAction`

#### 4.4 API (100% sin tests)

**Endpoints Sin Tests:**
- ❌ `/api/solicitar-codigo`
- ❌ `/api/iniciar-sesion`
- ❌ `/api/locations`
- ❌ `/api/clima`
- ❌ Todos los endpoints futuros

**Ejemplo de test de API:**
```php
// tests/Feature/Api/AuthTest.php
public function test_puede_solicitar_codigo_otp()
{
    $productor = Productor::factory()->create();
    
    $response = $this->postJson('/api/solicitar-codigo', [
        'identificador' => $productor->usuario->email
    ]);
    
    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Si su cuenta de productor existe, se ha enviado un código de acceso.'
             ]);
    
    // Verificar que se creó un dispositivo con código
    $this->assertDatabaseHas('dispositivos', [
        'usuario_id' => $productor->usuario->id
    ]);
}
```

### Plan de Incremento de Cobertura

**Objetivo: 60% de cobertura en 4-6 semanas**

| Semana | Área | Tests a Crear | Cobertura Esperada |
|--------|------|---------------|-------------------|
| 1 | API Auth + Locations | 10 tests | 40% |
| 2 | Servicios Críticos | 15 tests | 45% |
| 3 | Actions Principales | 10 tests | 50% |
| 4 | Controladores Core | 15 tests | 55% |
| 5-6 | Feature Tests Completos | 20 tests | 60% |

---

## 5. BASE DE DATOS

### 5.1 Inconsistencia SQLite/MySQL

**Problema:**
- Desarrollo usa SQLite (`database.sqlite`)
- Producción usará MySQL
- Posibles diferencias de comportamiento

**Riesgos:**
- ❌ Diferencias en tipos de datos
- ❌ Funciones SQL diferentes
- ❌ Comportamiento de transactions
- ❌ Collations y encoding

**Solución:**

**Opción A - Unificar en MySQL (Recomendada):**
```env
# Usar MySQL también en desarrollo con Docker
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ovino_dev
```

**Opción B - Tests en Ambos:**
```php
// phpunit.xml - Mantener SQLite para tests
<env name="DB_CONNECTION" value="sqlite"/>

// .env - Usar MySQL para desarrollo
DB_CONNECTION=mysql
```

**Opción C - Docker Compose actualizado:**
```yaml
# Asegurar que desarrollo use MySQL de Docker
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: ovino_dev
```

### 5.2 Falta de Índices Documentados

**Problema:**
No hay documentación explícita de índices necesarios para performance.

**Índices Sugeridos:**
```php
// En migraciones futuras o nueva migración de índices
Schema::table('stock_animals', function (Blueprint $table) {
    $table->index('unidad_productiva_id');
    $table->index('especie_id');
    $table->index('fecha_registro');
    $table->index(['unidad_productiva_id', 'fecha_registro']);
});

Schema::table('declaraciones_stock', function (Blueprint $table) {
    $table->index('productor_id');
    $table->index('periodo_id');
    $table->index(['productor_id', 'estado']);
});

Schema::table('unidades_productivas', function (Blueprint $table) {
    $table->index('identificador_local'); // Para búsquedas
    $table->index('municipio_id');
});
```

**Acción:**
Crear migración `2025_10_12_000000_add_performance_indexes.php`

### 5.3 Soft Deletes No Implementado

**Problema:**
Ningún modelo usa `SoftDeletes`, riesgo de pérdida de datos históricos.

**Modelos que Deberían Tener Soft Deletes:**
- `Productor` (mantener historial)
- `UnidadProductiva` (mantener historial)
- `Institucion` (mantener historial)
- `InstitucionalParticipante` (mantener historial)

**Implementación:**
```php
// En modelos:
use Illuminate\Database\Eloquent\SoftDeletes;

class Productor extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}

// Migración:
Schema::table('productors', function (Blueprint $table) {
    $table->softDeletes();
});
```

**Estimación:** 1 día para todos los modelos críticos

---

## 6. SEGURIDAD

### 6.1 SMS en Producción

**Problema CRÍTICO:**
Sistema usa `FakeSmsService` que solo escribe en logs.

**Archivos Involucrados:**
```php
app/Services/FakeSmsService.php     // Actual (fake)
app/Services/TwilioSmsService.php   // Existe pero no configurado
app/Interfaces/SmsServiceInterface.php
```

**Solución:**

**Paso 1 - Configurar Twilio:**
```env
# .env
TWILIO_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890
```

**Paso 2 - Actualizar Service Provider:**
```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->bind(SmsServiceInterface::class, function ($app) {
        if (config('app.env') === 'production') {
            return new TwilioSmsService();
        }
        return new FakeSmsService();
    });
}
```

**Paso 3 - Verificar TwilioSmsService:**
```php
// Asegurar que TwilioSmsService esté implementado correctamente
// Revisar manejo de errores
// Agregar logs de envío
```

**Estimación:** 2-3 horas

### 6.2 Rate Limiting

**Problema:**
No hay documentación de rate limiting para API.

**Solución:**
```php
// routes/api.php
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/solicitar-codigo', [AuthController::class, 'solicitarCodigo']);
});

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/clima', [ClimaController::class, 'index']);
});
```

### 6.3 Validación y Sanitización

**Estado:**
- ✅ CSRF habilitado
- ✅ Validación de formularios implementada
- ✅ Uso de Eloquent (previene SQL injection)
- ⚠️ Falta validación consistente en API
- ⚠️ No hay sanitización de HTML en campos de texto

**Mejoras Sugeridas:**
```php
// Sanitización global de inputs
// En Request classes:
protected function prepareForValidation()
{
    $this->merge([
        'observaciones' => strip_tags($this->observaciones)
    ]);
}
```

---

## 7. PERFORMANCE

### 7.1 Caché

**Problema:**
Caché limitado solo a estadísticas institucionales.

**Oportunidades de Caché:**

**1. Catálogos:**
```php
// Especies, Razas, Categorías, etc.
Cache::remember('especies', 3600, function () {
    return Especie::all();
});
```

**2. Configuración:**
```php
Cache::remember('periodo_activo', 1800, function () {
    return ConfiguracionActualizacion::where('activo', true)->first();
});
```

**3. Estadísticas de Dashboard:**
```php
Cache::remember("productor_{$id}_stats", 600, function () use ($id) {
    return EstadisticasService::getKpisProductor($id);
});
```

**Implementación:**
```php
// Crear trait CachesData
trait CachesData
{
    protected function getCached($key, $ttl, $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }
    
    protected function invalidateCache($key)
    {
        Cache::forget($key);
    }
}
```

### 7.2 N+1 Queries

**Problema Potencial:**
Uso inconsistente de `with()` para eager loading.

**Revisar:**
```php
// MALO (N+1):
$unidadesProductivas = $productor->unidadesProductivas;
foreach ($unidadesProductivas as $up) {
    echo $up->municipio->nombre; // Query por cada UP
}

// BUENO:
$unidadesProductivas = $productor->unidadesProductivas()->with('municipio')->get();
```

**Herramienta:**
```bash
composer require barryvdh/laravel-debugbar --dev
```

### 7.3 Consultas Históricas Pesadas

**Problema:**
`StockHistoryService::getStockAt()` puede ser costoso para rangos grandes.

**Solución - Tabla de Snapshots:**
```php
// Crear tabla stock_snapshots
Schema::create('stock_snapshots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('productor_id');
    $table->foreignId('unidad_productiva_id');
    $table->date('fecha');
    $table->json('stock_data'); // {especie_id: cantidad}
    $table->timestamps();
    
    $table->unique(['unidad_productiva_id', 'fecha']);
});

// Crear snapshot diario con comando
php artisan make:command CreateDailyStockSnapshots
```

**Estimación:** 2-3 días

---

## 8. FRONTEND

### 8.1 Sistema de Diseño No Documentado

**Problema:**
No hay guía de componentes ni paleta de colores documentada.

**Crear:**
```markdown
# docs/DESIGN_SYSTEM.md

## Colores
- Primary: #2563eb (blue-600)
- Secondary: #7c3aed (purple-600)
- Success: #16a34a (green-600)
- Danger: #dc2626 (red-600)
- Warning: #f59e0b (amber-500)

## Componentes
- Buttons
- Cards
- Modals
- Forms
- Tables
```

### 8.2 Accesibilidad

**Problemas Encontrados:**
- ⚠️ Algunos elementos sin labels
- ⚠️ Contraste de colores no verificado
- ⚠️ No hay tests de accesibilidad

**Herramientas:**
```bash
npm install --save-dev axe-core
npm install --save-dev @axe-core/cli
```

### 8.3 Optimización de Assets

**Mejoras Sugeridas:**
- Lazy loading de imágenes
- WebP para imágenes
- Code splitting
- Tree shaking

---

## 9. DEPLOYMENT

### 9.1 Variables de Entorno

**Problema CRÍTICO:**
No existe `.env.example`.

**Solución Inmediata:**
```bash
cp .env .env.example
# Limpiar valores sensibles
sed -i 's/APP_KEY=.*/APP_KEY=/' .env.example
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=/' .env.example
# etc.
```

**Contenido Mínimo:**
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database/database.sqlite
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
QUEUE_CONNECTION=database

OPENWEATHER_API_KEY=

TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_PHONE_NUMBER=
```

### 9.2 CI/CD

**Problema:**
No hay automatización de deployment.

**GitHub Actions Sugerido:**
```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

---

## 10. TAREAS PROGRAMADAS

### 10.1 Cron Jobs No Documentados

**Problema:**
`clima:actualizar` no está en DESPLIEGUE.md.

**Solución:**
Actualizar DESPLIEGUE.md con:
```markdown
### Configurar Cron Jobs:
```bash
# Añadir al crontab del servidor
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

### 10.2 Monitoring de Colas

**Problema:**
No hay monitoring de queue workers.

**Solución - Laravel Horizon:**
```bash
composer require laravel/horizon
php artisan horizon:install
```

---

## 11. ARCHIVOS OBSOLETOS

### Listado Completo

**En raíz:**
- `dashboardejemplo.txt`
- `desarrollo_territorial.txt` (duplicado)
- `diseñoPanel.blade.php`
- `promp de siempre.txt`
- `tests.txt`

**Archivos .bak:**
- `routes/web.php.bak`
- `app/Livewire/Productor/Parajes/CrearParajeModal.php.bak`
- `app/Livewire/Productor/UnidadesProductivas/CrearUnidadProductiva.php.bak`

**Acción:**
```bash
# Crear carpeta archive
mkdir archive
mv dashboardejemplo.txt archive/
mv desarrollo_territorial.txt archive/
# etc.

# O eliminar directamente si no son necesarios
rm dashboardejemplo.txt
```

---

## 12. GIT Y CONTROL DE VERSIONES

### 12.1 Cambios Sin Commit

**Archivos Modificados (9):**
```
database/migrations/2025_09_30_000002_add_descripcion_to_institucions_table.php
database/seeders/ProductorSeederMejorado.php
database/seeders/StockAnimalSeederMejorado.php
database/seeders/UnidadProductivaSeederMejorado.php
database/seeders/UsuarioInstitucionalSeeder.php
docs/COMANDOS_INSTITUCIONES.txt
docs/PROPUESTA_TARJETA_ESTADO_INSTITUCIONAL.md
resources/views/institucional/ayuda.blade.php
resources/views/livewire/productor/dashboard.blade.php
```

**Acción Inmediata:**
```bash
git add -A
git commit -m "feat(instituciones): agregar descripción y mejorar seeders"
git push origin feat/instituciones-logo
```

### 12.2 Estrategia de Branching

**Problema:**
No está documentada.

**Sugerencia - GitFlow Simplificado:**
```
main          → Producción
develop       → Desarrollo
feat/*        → Features
fix/*         → Bugfixes
hotfix/*      → Hotfixes urgentes
```

**Documentar en:**
```markdown
# CONTRIBUTING.md

## Branching Strategy
- `main`: Código en producción
- `develop`: Rama de desarrollo principal
- `feat/nombre`: Nueva funcionalidad
- `fix/nombre`: Corrección de bug
```

---

## RESUMEN DE PRIORIDADES

### CRÍTICO (Esta Semana)
1. ✅ Crear .env.example
2. ✅ Commit de cambios pendientes
3. ✅ Configurar SMS real (Twilio)
4. ✅ Documentar cron jobs

### ALTA (2 Semanas)
1. ⏳ Completar API móvil básica
2. ⏳ Tests a 50% cobertura
3. ⏳ Limpiar archivos obsoletos
4. ⏳ Implementar soft deletes

### MEDIA (1 Mes)
1. ⏳ Panel institucional completo
2. ⏳ Optimización de performance
3. ⏳ Sistema de permisos granular
4. ⏳ Documentación de diseño

### BAJA (Backlog)
1. ⏳ CI/CD
2. ⏳ Monitoring (Horizon)
3. ⏳ Tests de accesibilidad
4. ⏳ Internacionalización

---

**Fin del Documento de Gaps**

Este documento debe ser revisado y actualizado cada sprint o iteración de desarrollo.

