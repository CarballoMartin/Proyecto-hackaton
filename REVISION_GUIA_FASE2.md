# 🔍 REVISIÓN CRÍTICA: Guía Fase 2 Alertas

**Fecha:** 17 de Octubre de 2025  
**Revisor:** Claude (Anthropic)  
**Guía Revisada:** `docs/GUIA_FASE2_ALERTAS_AMBIENTALES.md`

---

## ✅ LO QUE ESTÁ BIEN

### 1. Estructura General
- ✅ Índice claro y navegable
- ✅ Pasos numerados y secuenciales
- ✅ Código completo incluido
- ✅ Ejemplos visuales del resultado
- ✅ Checklist final

### 2. Arquitectura
- ✅ Bien pensada y desacoplada
- ✅ Usa el patrón Service Layer correctamente
- ✅ Modelo con scopes útiles
- ✅ Relaciones bidireccionales

### 3. Código
- ✅ PHP 8.2+ compatible (match expressions)
- ✅ Type hints correctos
- ✅ Nombres descriptivos
- ✅ Métodos bien documentados

---

## ⚠️ PROBLEMAS IDENTIFICADOS

### 1. 🚨 CRÍTICO: Falta Factory para Testing

**Problema:**
La guía menciona `DatoClimaticoCache::factory()->create()` pero NO incluye la creación del factory.

**Impacto:**
Los tests no van a funcionar sin el factory.

**Solución:**
```php
// database/factories/DatoClimaticoCache Factory.php
<?php

namespace Database\Factories;

use App\Models\DatoClimaticoCache;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatoClimaticoCache Factory extends Factory
{
    protected $model = DatoClimaticoCache::class;

    public function definition(): array
    {
        return [
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'fuente' => 'open_meteo',
            'temperatura_actual' => $this->faker->randomFloat(1, 5, 40),
            'velocidad_viento' => $this->faker->randomFloat(1, 0, 80),
            'codigo_clima' => $this->faker->numberBetween(0, 99),
            'temperaturas_max' => [
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
                $this->faker->randomFloat(1, 20, 42),
            ],
            'temperaturas_min' => [
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
                $this->faker->randomFloat(1, 0, 20),
            ],
            'precipitacion' => [
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
                $this->faker->randomFloat(1, 0, 100),
            ],
            'probabilidad_lluvia' => [100, 80, 60, 40, 20, 10, 5],
            'viento_max' => [50, 40, 30, 20, 15, 10, 10],
            'fechas' => [
                now()->format('Y-m-d'),
                now()->addDay()->format('Y-m-d'),
                now()->addDays(2)->format('Y-m-d'),
                now()->addDays(3)->format('Y-m-d'),
                now()->addDays(4)->format('Y-m-d'),
                now()->addDays(5)->format('Y-m-d'),
                now()->addDays(6)->format('Y-m-d'),
            ],
            'datos_completos' => [],
            'fecha_consulta' => now()->subHours($this->faker->numberBetween(0, 23)),
        ];
    }

    /**
     * Factory para condición de sequía
     */
    public function sequia(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 36,
            'temperaturas_max' => [36, 37, 35, 36, 37, 36, 35],
            'precipitacion' => [0, 0, 0, 0, 0, 0, 0],
        ]);
    }

    /**
     * Factory para tormenta
     */
    public function tormenta(): static
    {
        return $this->state(fn (array $attributes) => [
            'precipitacion' => [60, 20, 10, 5, 0, 0, 0],
            'viento_max' => [70, 50, 30, 20, 15, 10, 10],
        ]);
    }

    /**
     * Factory para estrés térmico
     */
    public function estreTermico(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 38,
            'temperaturas_max' => [38, 37, 36, 35, 34, 33, 32],
        ]);
    }

    /**
     * Factory para helada
     */
    public function helada(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 8,
            'temperaturas_min' => [3, 4, 5, 6, 8, 10, 12],
        ]);
    }
}
```

**Agregar al Step 8 de la guía:**
- Paso previo: Crear factory antes de los tests

---

### 2. ⚠️ ALTO: Falta Factory de AlertaAmbiental

**Problema:**
Si queremos tests más avanzados, necesitamos factory de AlertaAmbiental también.

**Solución:**
```php
// database/factories/AlertaAmbientalFactory.php
<?php

namespace Database\Factories;

use App\Models\AlertaAmbiental;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertaAmbientalFactory extends Factory
{
    protected $model = AlertaAmbiental::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['sequia', 'tormenta', 'estres_termico', 'helada']);
        
        return [
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'tipo' => $tipo,
            'nivel' => $this->obtenerNivel($tipo),
            'titulo' => $this->obtenerTitulo($tipo),
            'mensaje' => $this->obtenerMensaje($tipo),
            'datos_contexto' => [],
            'activa' => true,
            'leida' => false,
            'fecha_inicio' => now(),
            'fecha_fin' => null,
            'notificado_email' => false,
            'notificado_sms' => false,
            'fecha_notificacion' => null,
        ];
    }

    private function obtenerNivel($tipo): string
    {
        return match($tipo) {
            'sequia' => 'critico',
            'tormenta' => 'alto',
            'estres_termico' => 'medio',
            'helada' => 'bajo',
        };
    }

    private function obtenerTitulo($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Sequía Prolongada',
            'tormenta' => 'Tormenta Intensa',
            'estres_termico' => 'Estrés Térmico',
            'helada' => 'Riesgo de Helada',
        };
    }

    private function obtenerMensaje($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Tu campo está en riesgo de sequía.',
            'tormenta' => 'Se espera tormenta intensa.',
            'estres_termico' => 'Temperaturas extremas detectadas.',
            'helada' => 'Se esperan temperaturas bajas.',
        };
    }

    public function activa(): static
    {
        return $this->state(['activa' => true]);
    }

    public function inactiva(): static
    {
        return $this->state(['activa' => false, 'fecha_fin' => now()]);
    }

    public function leida(): static
    {
        return $this->state(['leida' => true]);
    }

    public function noLeida(): static
    {
        return $this->state(['leida' => false]);
    }
}
```

---

### 3. ⚠️ MEDIO: Validación de Datos en el Servicio

**Problema:**
El servicio no valida que los datos climáticos sean recientes antes de crear alertas.

**Riesgo:**
Podríamos crear alertas basadas en datos obsoletos.

**Solución:**
Agregar validación en `detectarAlertasParaUnidad`:

```php
// Al inicio del método
$datosClimaticos = $unidad->datosClimaticos()
    ->where('fecha_consulta', '>=', now()->subDays(30))
    ->where('fecha_consulta', '>=', now()->subHours(25)) // ← AGREGAR ESTO
    ->orderBy('fecha_consulta', 'desc')
    ->get();

if ($datosClimaticos->isEmpty()) {
    Log::warning('No hay datos climáticos recientes para la unidad', [
        'unidad_productiva_id' => $unidad->id,
    ]);
    return $estadisticas;
}

// Validar que el dato más reciente no sea muy viejo
$datoMasReciente = $datosClimaticos->first();
if ($datoMasReciente->fecha_consulta < now()->subHours(25)) {
    Log::warning('Datos climáticos obsoletos, no se crean alertas', [
        'unidad_productiva_id' => $unidad->id,
        'ultima_actualizacion' => $datoMasReciente->fecha_consulta,
    ]);
    return $estadisticas;
}
```

---

### 4. ⚠️ MEDIO: Falta Manejo de Productores Múltiples

**Problema:**
Una UnidadProductiva puede tener múltiples productores (relación many-to-many), pero el servicio `obtenerAlertasActivasParaProductor` podría traer duplicados.

**Solución:**
Ya está bien implementado con `distinct()`, pero falta documentarlo:

```php
// En AlertasAmbientalesService
public function obtenerAlertasActivasParaProductor($productorId): \Illuminate\Database\Eloquent\Collection
{
    return AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productorId) {
        $query->where('productors.id', $productorId);
    })
    ->activas()
    ->with('unidadProductiva')
    ->distinct() // ← Asegurar que no haya duplicados
    ->orderBy('nivel', 'asc')
    ->orderBy('created_at', 'desc')
    ->get();
}
```

---

### 5. ⚠️ MEDIO: Falta Seeder para Demo

**Problema:**
Para mostrar el sistema funcionando, sería útil tener un seeder que cree alertas de ejemplo.

**Solución:**
```php
// database/seeders/AlertasAmbientalesDemoSeeder.php
<?php

namespace Database\Seeders;

use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use Illuminate\Database\Seeder;

class AlertasAmbientalesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->limit(5)
            ->get();

        if ($unidades->isEmpty()) {
            $this->command->warn('No hay unidades productivas con coordenadas.');
            return;
        }

        // Crear alerta de sequía
        AlertaAmbiental::create([
            'unidad_productiva_id' => $unidades->first()->id,
            'tipo' => 'sequia',
            'nivel' => 'critico',
            'titulo' => 'Sequía Prolongada',
            'mensaje' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales.',
            'datos_contexto' => [
                'temperatura_promedio' => 35,
                'dias_sin_lluvia' => 18,
            ],
            'fecha_inicio' => now(),
            'activa' => true,
            'leida' => false,
        ]);

        // Crear alerta de tormenta
        if ($unidades->count() > 1) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[1]->id,
                'tipo' => 'tormenta',
                'nivel' => 'alto',
                'titulo' => 'Tormenta Intensa',
                'mensaje' => 'Se espera tormenta intensa. Asegura instalaciones y protege animales vulnerables.',
                'datos_contexto' => [
                    'lluvia_esperada' => 65,
                    'viento_esperado' => 70,
                    'fecha_esperada' => now()->addDay()->format('Y-m-d'),
                ],
                'fecha_inicio' => now(),
                'activa' => true,
                'leida' => false,
            ]);
        }

        // Crear alerta de estrés térmico
        if ($unidades->count() > 2) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[2]->id,
                'tipo' => 'estres_termico',
                'nivel' => 'medio',
                'titulo' => 'Estrés Térmico',
                'mensaje' => 'Temperaturas extremas pueden afectar el bienestar animal.',
                'datos_contexto' => [
                    'temperatura_maxima' => 38,
                    'dias_consecutivos' => 4,
                ],
                'fecha_inicio' => now()->subDays(2),
                'activa' => true,
                'leida' => true, // Esta ya fue leída
            ]);
        }

        $this->command->info('✅ Alertas de demostración creadas');
    }
}
```

**Agregar a `database/seeders/DatabaseSeeder.php`:**
```php
// Descomentar cuando quieras datos de demo
// $this->call(AlertasAmbientalesDemoSeeder::class);
```

---

### 6. ⚠️ BAJO: Falta Middleware de Autorización

**Problema:**
No hay verificación explícita de que un productor solo pueda ver/modificar sus propias alertas.

**Solución:**
Agregar en el componente Livewire:

```php
// En AlertasWidget.php
public function marcarComoLeida($alertaId)
{
    $productor = Productor::where('usuario_id', Auth::id())->first();
    
    if (!$productor) {
        return;
    }

    $alerta = \App\Models\AlertaAmbiental::find($alertaId);

    // ← AGREGAR ESTA VALIDACIÓN
    if (!$alerta || !$this->perteneceAlProductor($alerta, $productor)) {
        $this->dispatch('error', [
            'mensaje' => 'No tienes permiso para modificar esta alerta'
        ]);
        return;
    }

    $alerta->marcarComoLeida();
    $this->cargarAlertas();
    
    $this->dispatch('alerta-leida', [
        'mensaje' => 'Alerta marcada como leída'
    ]);
}

private function perteneceAlProductor($alerta, $productor): bool
{
    return $alerta->unidadProductiva
        ->productores()
        ->where('productors.id', $productor->id)
        ->exists();
}
```

---

### 7. ⚠️ BAJO: Falta Logging de Operaciones

**Problema:**
No hay logs de cuándo se crean/desactivan alertas para auditoría.

**Solución:**
Agregar logs en el servicio:

```php
// En crearOActualizarAlerta
AlertaAmbiental::create([...]);

// ← AGREGAR
Log::info('Alerta ambiental creada', [
    'tipo' => $tipo,
    'unidad_productiva_id' => $unidad->id,
    'unidad_nombre' => $unidad->nombre,
    'nivel' => $datosAlerta['nivel'],
]);

return true;
```

```php
// En desactivarAlerta
foreach ($alertas as $alerta) {
    $alerta->desactivar();
    
    // ← AGREGAR
    Log::info('Alerta ambiental desactivada', [
        'tipo' => $alerta->tipo,
        'unidad_productiva_id' => $alerta->unidad_productiva_id,
        'duracion_horas' => $alerta->created_at->diffInHours(now()),
    ]);
}
```

---

### 8. ⚠️ BAJO: Falta Configuración de Umbrales

**Problema:**
Los umbrales están hardcodeados (15 días sin lluvia, 50mm, etc.). Deberían ser configurables.

**Solución Futura (Fase 3 o posterior):**
Crear tabla de configuración de umbrales:

```sql
CREATE TABLE configuracion_alertas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tipo_alerta VARCHAR(50),
    parametro VARCHAR(50),
    valor_umbral DECIMAL(10,2),
    activo BOOLEAN DEFAULT TRUE
);

INSERT INTO configuracion_alertas VALUES
(1, 'sequia', 'dias_sin_lluvia', 15, TRUE),
(2, 'sequia', 'temperatura_promedio', 32, TRUE),
(3, 'tormenta', 'precipitacion_mm', 50, TRUE),
(4, 'tormenta', 'velocidad_viento_kmh', 60, TRUE);
```

**Por ahora:** Usar constantes en el servicio:

```php
// Al inicio de AlertasAmbientalesService
private const UMBRAL_SEQUIA_DIAS = 15;
private const UMBRAL_SEQUIA_TEMP = 32;
private const UMBRAL_TORMENTA_LLUVIA = 50;
private const UMBRAL_TORMENTA_VIENTO = 60;
private const UMBRAL_ESTRES_TERMICO = 35;
private const UMBRAL_ESTRES_DIAS = 3;
private const UMBRAL_HELADA = 5;

// Usar en los métodos:
if ($diasSinLluvia >= self::UMBRAL_SEQUIA_DIAS || 
    $temperaturaPromedio > self::UMBRAL_SEQUIA_TEMP) {
    return true;
}
```

---

### 9. ✅ BIEN: Notificaciones están Opcionales

La guía marca las notificaciones como opcionales, lo cual es correcto porque:
- ✅ SMS requiere Twilio configurado (actualmente fake)
- ✅ Email podría saturar al productor
- ✅ La campana en UI es suficiente para MVP

---

### 10. ⚠️ BAJO: Falta Documentación de API

**Problema:**
Si en el futuro queremos exponer alertas vía API REST para móvil, falta documentar los endpoints.

**Solución Futura:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/alertas', [AlertasController::class, 'index']);
    Route::post('/alertas/{id}/marcar-leida', [AlertasController::class, 'marcarLeida']);
    Route::get('/alertas/resumen', [AlertasController::class, 'resumen']);
});
```

**Por ahora:** No es necesario, Fase 2 solo web.

---

## 🔧 CORRECCIONES A APLICAR

### Prioridad CRÍTICA (antes de implementar)

1. **Crear Factory de DatoClimaticoCache**
   - Archivo: `database/factories/DatoClimaticoCache Factory.php`
   - Con states: `sequia()`, `tormenta()`, `estreTermico()`, `helada()`

2. **Agregar validación de datos recientes en servicio**
   - En método `detectarAlertasParaUnidad`
   - Validar que fecha_consulta < 25 horas

3. **Agregar constantes de umbrales**
   - Al inicio de `AlertasAmbientalesService`
   - Para facilitar ajustes futuros

### Prioridad ALTA (después de implementar)

4. **Crear Factory de AlertaAmbiental**
   - Para tests más completos
   - Con states útiles

5. **Agregar validación de permisos en Livewire**
   - Método `perteneceAlProductor()`
   - En `marcarComoLeida()`

6. **Agregar Seeder de demo**
   - Para mostrar funcionamiento
   - Opcional para usar

### Prioridad MEDIA (durante refinamiento)

7. **Agregar logging de operaciones**
   - En crear/desactivar alertas
   - Para auditoría

8. **Mejorar tests**
   - Test de no duplicados
   - Test de desactivación
   - Test de permisos

---

## 📊 COMPARACIÓN CON FASE 1

| Aspecto | Fase 1 Clima | Fase 2 Alertas | Estado |
|---------|--------------|----------------|--------|
| Migración | ✅ Completa | ✅ Completa | OK |
| Modelo | ✅ Con métodos útiles | ✅ Con métodos útiles | OK |
| Factory | ❌ No creado | ❌ Falta crear | **A CORREGIR** |
| Servicio | ✅ Bien estructurado | ✅ Bien estructurado | OK |
| Comando | ✅ Funcional | ✅ Funcional | OK |
| Schedule | ✅ Configurado | ✅ Configurado | OK |
| Livewire | ✅ Responsive | ✅ Responsive | OK |
| Tests | ⚠️ Básicos | ⚠️ Básicos | MEJORABLE |
| Validaciones | ✅ Suficientes | ⚠️ Falta permisos | **A CORREGIR** |
| Logging | ⚠️ Mínimo | ⚠️ Falta agregar | MEJORABLE |
| Seeder Demo | ❌ No hay | ❌ Falta crear | OPCIONAL |

---

## ✅ CHECKLIST DE CORRECCIONES

Antes de empezar la implementación:

- [ ] Crear `DatoClimaticoCache Factory.php` con states
- [ ] Agregar constantes de umbrales al servicio
- [ ] Agregar validación de datos recientes en servicio
- [ ] Agregar validación de permisos en Livewire
- [ ] Crear `AlertaAmbientalFactory.php`
- [ ] Crear `AlertasAmbientalesDemoSeeder.php`
- [ ] Agregar logging de operaciones
- [ ] Actualizar la guía con estas correcciones

---

## 🎯 VEREDICTO

### Calificación General: **8.5/10**

**Fortalezas:**
- ✅ Arquitectura sólida
- ✅ Código completo y funcional
- ✅ Bien documentada
- ✅ Paso a paso claro

**Debilidades:**
- ⚠️ Falta factory para testing (crítico)
- ⚠️ Falta validación de permisos
- ⚠️ Umbrales hardcodeados
- ⚠️ Falta seeder de demo

**Recomendación:**
Aplicar las correcciones CRÍTICAS antes de empezar, y las de ALTA prioridad durante la implementación.

Con estas correcciones, la guía quedará en **9.5/10** ⭐

---

## 📝 ACTUALIZACIÓN DE LA GUÍA

Voy a crear un documento complementario:
`docs/GUIA_FASE2_CORRECCIONES.md`

Con:
1. Las factories que faltan
2. Las validaciones a agregar
3. El seeder de demo
4. Los tests mejorados

---

**¿Procedemos a:**

A) 🔧 **Aplicar correcciones a la guía** primero
B) 🚀 **Empezar implementación** y corregir sobre la marcha
C) 📖 **Revisar juntos** cada corrección antes de decidir

¿Qué prefieres?

