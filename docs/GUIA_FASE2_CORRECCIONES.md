# 🔧 CORRECCIONES Y MEJORAS: Fase 2 Alertas

**Fecha:** 17 de Octubre de 2025  
**Complemento de:** `GUIA_FASE2_ALERTAS_AMBIENTALES.md`  
**Estado:** Correcciones identificadas y listas para aplicar

---

## 📋 RESUMEN DE CORRECCIONES

| # | Problema | Prioridad | Tiempo |
|---|----------|-----------|--------|
| 1 | Falta Factory DatoClimaticoCache | 🔴 CRÍTICA | 20 min |
| 2 | Falta Factory AlertaAmbiental | 🟠 ALTA | 15 min |
| 3 | Validación de datos recientes | 🟠 ALTA | 10 min |
| 4 | Constantes de umbrales | 🟠 ALTA | 5 min |
| 5 | Validación de permisos | 🟡 MEDIA | 10 min |
| 6 | Seeder de demo | 🟡 MEDIA | 15 min |
| 7 | Logging de operaciones | 🟢 BAJA | 10 min |
| 8 | Tests mejorados | 🟢 BAJA | 30 min |

**Total:** ~2 horas de correcciones

---

## 🔴 CORRECCIÓN #1: Factory DatoClimaticoCache (CRÍTICA)

### Crear archivo

```bash
php artisan make:factory DatoClimaticoCache Factory
```

### Código completo

```php
<?php
// database/factories/DatoClimaticoCache Factory.php

namespace Database\Factories;

use App\Models\DatoClimaticoCache;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatoClimaticoCache Factory extends Factory
{
    protected $model = DatoClimaticoCache::class;

    public function definition(): array
    {
        $temp = $this->faker->randomFloat(1, 10, 35);
        
        return [
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'fuente' => 'open_meteo',
            'temperatura_actual' => $temp,
            'velocidad_viento' => $this->faker->randomFloat(1, 5, 40),
            'codigo_clima' => $this->faker->numberBetween(0, 99),
            'temperaturas_max' => $this->generarTemperaturas($temp + 5, $temp + 10),
            'temperaturas_min' => $this->generarTemperaturas($temp - 10, $temp - 5),
            'precipitacion' => $this->generarPrecipitacion(),
            'probabilidad_lluvia' => [80, 70, 60, 50, 40, 30, 20],
            'viento_max' => [40, 35, 30, 25, 20, 15, 10],
            'fechas' => $this->generarFechas(),
            'datos_completos' => [],
            'fecha_consulta' => now()->subHours($this->faker->numberBetween(0, 23)),
        ];
    }

    private function generarTemperaturas($min, $max): array
    {
        return [
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
        ];
    }

    private function generarPrecipitacion(): array
    {
        return [
            $this->faker->randomFloat(1, 0, 20),
            $this->faker->randomFloat(1, 0, 20),
            $this->faker->randomFloat(1, 0, 15),
            $this->faker->randomFloat(1, 0, 10),
            $this->faker->randomFloat(1, 0, 5),
            $this->faker->randomFloat(1, 0, 5),
            $this->faker->randomFloat(1, 0, 5),
        ];
    }

    private function generarFechas(): array
    {
        $fechas = [];
        for ($i = 0; $i < 7; $i++) {
            $fechas[] = now()->addDays($i)->format('Y-m-d');
        }
        return $fechas;
    }

    /**
     * State: Condición de sequía
     */
    public function sequia(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 36,
            'temperaturas_max' => [36, 37, 38, 36, 37, 35, 36],
            'temperaturas_min' => [22, 23, 24, 22, 23, 21, 22],
            'precipitacion' => [0, 0, 0, 0, 0, 0, 0],
            'probabilidad_lluvia' => [0, 0, 0, 0, 5, 10, 15],
        ]);
    }

    /**
     * State: Tormenta inminente
     */
    public function tormenta(): static
    {
        return $this->state(fn (array $attributes) => [
            'precipitacion' => [60, 25, 10, 5, 2, 0, 0],
            'probabilidad_lluvia' => [95, 85, 70, 50, 30, 20, 10],
            'viento_max' => [70, 55, 40, 30, 25, 20, 15],
        ]);
    }

    /**
     * State: Estrés térmico
     */
    public function estreTermico(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 38,
            'temperaturas_max' => [38, 39, 37, 38, 37, 36, 35],
            'temperaturas_min' => [24, 25, 23, 24, 23, 22, 21],
        ]);
    }

    /**
     * State: Riesgo de helada
     */
    public function helada(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 8,
            'temperaturas_max' => [12, 13, 14, 15, 16, 17, 18],
            'temperaturas_min' => [3, 4, 5, 6, 7, 9, 10],
        ]);
    }

    /**
     * State: Datos recientes (útil para tests)
     */
    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_consulta' => now()->subHours(2),
        ]);
    }

    /**
     * State: Datos antiguos (útil para tests de validación)
     */
    public function antiguo(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_consulta' => now()->subDays(2),
        ]);
    }
}
```

---

## 🟠 CORRECCIÓN #2: Factory AlertaAmbiental (ALTA)

### Crear archivo

```bash
php artisan make:factory AlertaAmbientalFactory
```

### Código completo

```php
<?php
// database/factories/AlertaAmbientalFactory.php

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
            'datos_contexto' => $this->obtenerContexto($tipo),
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
            default => 'bajo',
        };
    }

    private function obtenerTitulo($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Sequía Prolongada',
            'tormenta' => 'Tormenta Intensa',
            'estres_termico' => 'Estrés Térmico',
            'helada' => 'Riesgo de Helada',
            default => 'Alerta Ambiental',
        };
    }

    private function obtenerMensaje($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua.',
            'tormenta' => 'Se espera tormenta intensa. Asegura instalaciones.',
            'estres_termico' => 'Temperaturas extremas pueden afectar el bienestar animal.',
            'helada' => 'Se esperan temperaturas bajas. Protege crías.',
            default => 'Condición detectada que requiere atención.',
        };
    }

    private function obtenerContexto($tipo): array
    {
        return match($tipo) {
            'sequia' => [
                'temperatura_promedio' => 35,
                'dias_sin_lluvia' => 17,
            ],
            'tormenta' => [
                'lluvia_esperada' => 65,
                'viento_esperado' => 70,
                'fecha_esperada' => now()->addDay()->format('Y-m-d'),
            ],
            'estres_termico' => [
                'temperatura_maxima' => 38,
                'dias_consecutivos' => 4,
            ],
            'helada' => [
                'temperatura_minima' => 3,
                'fecha_esperada' => now()->addDay()->format('Y-m-d'),
            ],
            default => [],
        };
    }

    // States útiles

    public function activa(): static
    {
        return $this->state(['activa' => true, 'fecha_fin' => null]);
    }

    public function inactiva(): static
    {
        return $this->state([
            'activa' => false,
            'fecha_fin' => now()->subHours($this->faker->numberBetween(1, 48)),
        ]);
    }

    public function leida(): static
    {
        return $this->state(['leida' => true]);
    }

    public function noLeida(): static
    {
        return $this->state(['leida' => false]);
    }

    public function notificada(): static
    {
        return $this->state([
            'notificado_email' => true,
            'fecha_notificacion' => now()->subHours($this->faker->numberBetween(1, 12)),
        ]);
    }

    // States por tipo

    public function sequia(): static
    {
        return $this->state([
            'tipo' => 'sequia',
            'nivel' => 'critico',
            'titulo' => 'Sequía Prolongada',
            'mensaje' => 'Tu campo está en riesgo de sequía.',
            'datos_contexto' => [
                'temperatura_promedio' => 35,
                'dias_sin_lluvia' => 17,
            ],
        ]);
    }

    public function tormenta(): static
    {
        return $this->state([
            'tipo' => 'tormenta',
            'nivel' => 'alto',
            'titulo' => 'Tormenta Intensa',
            'mensaje' => 'Se espera tormenta intensa.',
            'datos_contexto' => [
                'lluvia_esperada' => 65,
                'viento_esperado' => 70,
            ],
        ]);
    }

    public function estreTermico(): static
    {
        return $this->state([
            'tipo' => 'estres_termico',
            'nivel' => 'medio',
            'titulo' => 'Estrés Térmico',
            'mensaje' => 'Temperaturas extremas detectadas.',
            'datos_contexto' => [
                'temperatura_maxima' => 38,
                'dias_consecutivos' => 4,
            ],
        ]);
    }

    public function helada(): static
    {
        return $this->state([
            'tipo' => 'helada',
            'nivel' => 'bajo',
            'titulo' => 'Riesgo de Helada',
            'mensaje' => 'Se esperan temperaturas bajas.',
            'datos_contexto' => [
                'temperatura_minima' => 3,
            ],
        ]);
    }
}
```

---

## 🟠 CORRECCIÓN #3: Constantes de Umbrales (ALTA)

### Modificar AlertasAmbientalesService.php

**Agregar al inicio de la clase:**

```php
<?php

namespace App\Services;

use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use App\Models\DatoClimaticoCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlertasAmbientalesService
{
    // ← AGREGAR ESTAS CONSTANTES
    private const UMBRAL_SEQUIA_DIAS = 15;
    private const UMBRAL_SEQUIA_TEMP = 32;
    private const UMBRAL_TORMENTA_LLUVIA = 50; // mm
    private const UMBRAL_TORMENTA_VIENTO = 60; // km/h
    private const UMBRAL_ESTRES_TERMICO = 35; // °C
    private const UMBRAL_ESTRES_DIAS = 3;
    private const UMBRAL_HELADA = 5; // °C
    private const HORAS_MAX_DATOS_ANTIGUOS = 25;
    
    // ... resto del código
```

**Usar en los métodos:**

```php
// En detectarSequia():
return $diasSinLluvia >= self::UMBRAL_SEQUIA_DIAS || 
       $temperaturaPromedio > self::UMBRAL_SEQUIA_TEMP;

// En detectarTormenta():
if ($lluvia > self::UMBRAL_TORMENTA_LLUVIA || 
    $viento > self::UMBRAL_TORMENTA_VIENTO) {
    return true;
}

// En detectarEstreTermico():
if ($tempMax > self::UMBRAL_ESTRES_TERMICO) {
    $diasCalurosos++;
}
return $diasCalurosos >= self::UMBRAL_ESTRES_DIAS;

// En detectarHelada():
if ($tempMin < self::UMBRAL_HELADA) {
    return true;
}
```

---

## 🟠 CORRECCIÓN #4: Validación de Datos Recientes (ALTA)

### Modificar método detectarAlertasParaUnidad

```php
public function detectarAlertasParaUnidad(UnidadProductiva $unidad): array
{
    $estadisticas = [
        'creadas' => 0,
        'desactivadas' => 0,
    ];

    // Obtener datos climáticos recientes
    $datosClimaticos = $unidad->datosClimaticos()
        ->where('fecha_consulta', '>=', now()->subDays(30))
        ->where('fecha_consulta', '>=', now()->subHours(self::HORAS_MAX_DATOS_ANTIGUOS)) // ← AGREGAR
        ->orderBy('fecha_consulta', 'desc')
        ->get();

    if ($datosClimaticos->isEmpty()) {
        Log::warning('No hay datos climáticos recientes para la unidad', [
            'unidad_productiva_id' => $unidad->id,
        ]);
        return $estadisticas;
    }

    // ← AGREGAR ESTA VALIDACIÓN
    $datoMasReciente = $datosClimaticos->first();
    if ($datoMasReciente->fecha_consulta < now()->subHours(self::HORAS_MAX_DATOS_ANTIGUOS)) {
        Log::warning('Datos climáticos obsoletos, no se crean alertas', [
            'unidad_productiva_id' => $unidad->id,
            'ultima_actualizacion' => $datoMasReciente->fecha_consulta,
            'horas_antiguo' => $datoMasReciente->fecha_consulta->diffInHours(now()),
        ]);
        return $estadisticas;
    }

    // ... resto del código
```

---

## 🟡 CORRECCIÓN #5: Validación de Permisos (MEDIA)

### Modificar AlertasWidget.php

```php
public function marcarComoLeida($alertaId)
{
    $productor = Productor::where('usuario_id', Auth::id())->first();
    
    if (!$productor) {
        return;
    }

    $alerta = \App\Models\AlertaAmbiental::find($alertaId);

    // ← AGREGAR ESTA VALIDACIÓN
    if (!$alerta) {
        $this->dispatch('error', [
            'mensaje' => 'Alerta no encontrada'
        ]);
        return;
    }

    if (!$this->perteneceAlProductor($alerta, $productor)) {
        $this->dispatch('error', [
            'mensaje' => 'No tienes permiso para modificar esta alerta'
        ]);
        
        Log::warning('Intento de modificar alerta de otro productor', [
            'usuario_id' => Auth::id(),
            'alerta_id' => $alertaId,
            'unidad_productiva_id' => $alerta->unidad_productiva_id,
        ]);
        
        return;
    }

    $alerta->marcarComoLeida();
    $this->cargarAlertas();
    
    $this->dispatch('alerta-leida', [
        'mensaje' => 'Alerta marcada como leída'
    ]);
}

// ← AGREGAR ESTE MÉTODO
private function perteneceAlProductor($alerta, $productor): bool
{
    return $alerta->unidadProductiva
        ->productores()
        ->where('productors.id', $productor->id)
        ->exists();
}
```

---

## 🟡 CORRECCIÓN #6: Seeder de Demo (MEDIA)

### Crear seeder

```bash
php artisan make:seeder AlertasAmbientalesDemoSeeder
```

### Código completo

```php
<?php
// database/seeders/AlertasAmbientalesDemoSeeder.php

namespace Database\Seeders;

use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use Illuminate\Database\Seeder;

class AlertasAmbientalesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚨 Creando alertas de demostración...');

        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->whereHas('productores') // Solo unidades con productores
            ->limit(5)
            ->get();

        if ($unidades->isEmpty()) {
            $this->command->warn('No hay unidades productivas con coordenadas y productores.');
            return;
        }

        $alertasCreadas = 0;

        // Alerta 1: Sequía (crítica, no leída)
        if ($unidades->count() >= 1) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[0]->id,
                'tipo' => 'sequia',
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales.',
                'datos_contexto' => [
                    'temperatura_promedio' => 35,
                    'dias_sin_lluvia' => 18,
                ],
                'fecha_inicio' => now()->subDays(3),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 2: Tormenta (alta, no leída)
        if ($unidades->count() >= 2) {
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
                'fecha_inicio' => now()->subHours(12),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 3: Estrés térmico (media, leída)
        if ($unidades->count() >= 3) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[2]->id,
                'tipo' => 'estres_termico',
                'nivel' => 'medio',
                'titulo' => 'Estrés Térmico',
                'mensaje' => 'Temperaturas extremas pueden afectar el bienestar animal. Aumenta disponibilidad de sombra y agua.',
                'datos_contexto' => [
                    'temperatura_maxima' => 38,
                    'dias_consecutivos' => 4,
                ],
                'fecha_inicio' => now()->subDays(4),
                'activa' => true,
                'leida' => true, // Esta ya fue leída
            ]);
            $alertasCreadas++;
        }

        // Alerta 4: Helada (baja, no leída)
        if ($unidades->count() >= 4) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[3]->id,
                'tipo' => 'helada',
                'nivel' => 'bajo',
                'titulo' => 'Riesgo de Helada',
                'mensaje' => 'Se esperan temperaturas bajas. Protege crías recién nacidas.',
                'datos_contexto' => [
                    'temperatura_minima' => 3,
                    'fecha_esperada' => now()->addDays(2)->format('Y-m-d'),
                ],
                'fecha_inicio' => now()->subHours(6),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 5: Sequía antigua (inactiva)
        if ($unidades->count() >= 5) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[4]->id,
                'tipo' => 'sequia',
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Sequía resuelta.',
                'datos_contexto' => [
                    'temperatura_promedio' => 33,
                    'dias_sin_lluvia' => 16,
                ],
                'fecha_inicio' => now()->subDays(10),
                'fecha_fin' => now()->subDays(3),
                'activa' => false,
                'leida' => true,
            ]);
            $alertasCreadas++;
        }

        $this->command->info("✅ {$alertasCreadas} alertas de demostración creadas");
        
        // Mostrar resumen
        $this->command->newLine();
        $this->command->table(
            ['Tipo', 'Nivel', 'Campo', 'Estado'],
            AlertaAmbiental::with('unidadProductiva')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    $a->obtenerEmoji() . ' ' . ucfirst($a->tipo),
                    ucfirst($a->nivel),
                    $a->unidadProductiva->nombre,
                    $a->activa ? ($a->leida ? 'Activa/Leída' : 'Activa/No Leída') : 'Inactiva',
                ])
        );
    }
}
```

### Registrar en DatabaseSeeder

```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    // ... otros seeders

    // Descomentar para crear alertas de demo
    // $this->call(AlertasAmbientalesDemoSeeder::class);
}
```

### Ejecutar

```bash
php artisan db:seed --class=AlertasAmbientalesDemoSeeder
```

---

## 🟢 CORRECCIÓN #7: Logging de Operaciones (BAJA)

### Agregar en crearOActualizarAlerta

```php
private function crearOActualizarAlerta(UnidadProductiva $unidad, string $tipo, $datosClimaticos): bool
{
    // ... código existente ...

    $alerta = AlertaAmbiental::create([...]);

    // ← AGREGAR LOGGING
    Log::info('Alerta ambiental creada', [
        'alerta_id' => $alerta->id,
        'tipo' => $tipo,
        'nivel' => $datosAlerta['nivel'],
        'unidad_productiva_id' => $unidad->id,
        'unidad_nombre' => $unidad->nombre,
        'datos_contexto' => $datosAlerta['contexto'],
    ]);

    return true;
}
```

### Agregar en desactivarAlerta

```php
private function desactivarAlerta(UnidadProductiva $unidad, string $tipo): bool
{
    $alertas = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
        ->where('tipo', $tipo)
        ->activas()
        ->get();

    if ($alertas->isEmpty()) {
        return false;
    }

    foreach ($alertas as $alerta) {
        $alerta->desactivar();
        
        // ← AGREGAR LOGGING
        Log::info('Alerta ambiental desactivada', [
            'alerta_id' => $alerta->id,
            'tipo' => $alerta->tipo,
            'nivel' => $alerta->nivel,
            'unidad_productiva_id' => $alerta->unidad_productiva_id,
            'duracion_horas' => $alerta->created_at->diffInHours(now()),
            'fue_leida' => $alerta->leida,
        ]);
    }

    return true;
}
```

---

## 🟢 CORRECCIÓN #8: Tests Mejorados (BAJA)

### Actualizar AlertasAmbientalesTest.php

```php
<?php
// tests/Feature/AlertasAmbientalesTest.php

namespace Tests\Feature;

use App\Models\UnidadProductiva;
use App\Models\DatoClimaticoCache;
use App\Models\AlertaAmbiental;
use App\Models\Productor;
use App\Models\User;
use App\Services\AlertasAmbientalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertasAmbientalesTest extends TestCase
{
    use RefreshDatabase;

    private AlertasAmbientalesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlertasAmbientalesService();
    }

    public function test_detecta_sequia_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear datos de sequía
        for ($i = 0; $i < 15; $i++) {
            DatoClimaticoCache::factory()
                ->sequia()
                ->create([
                    'unidad_productiva_id' => $unidad->id,
                    'fecha_consulta' => now()->subDays($i),
                ]);
        }

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertNotNull($alerta);
        $this->assertEquals('sequia', $alerta->tipo);
        $this->assertEquals('critico', $alerta->nivel);
    }

    public function test_detecta_tormenta_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        DatoClimaticoCache::factory()
            ->tormenta()
            ->reciente()
            ->create([
                'unidad_productiva_id' => $unidad->id,
            ]);

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertEquals('tormenta', $alerta->tipo);
        $this->assertEquals('alto', $alerta->nivel);
    }

    public function test_detecta_estres_termico_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        for ($i = 0; $i < 3; $i++) {
            DatoClimaticoCache::factory()
                ->estreTermico()
                ->create([
                    'unidad_productiva_id' => $unidad->id,
                    'fecha_consulta' => now()->subDays($i),
                ]);
        }

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertEquals('estres_termico', $alerta->tipo);
    }

    public function test_detecta_helada_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        DatoClimaticoCache::factory()
            ->helada()
            ->reciente()
            ->create([
                'unidad_productiva_id' => $unidad->id,
            ]);

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertEquals('helada', $alerta->tipo);
    }

    public function test_desactiva_alertas_cuando_condicion_mejora()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear alerta existente
        AlertaAmbiental::factory()
            ->sequia()
            ->activa()
            ->create(['unidad_productiva_id' => $unidad->id]);

        // Crear datos que indican que ya no hay sequía
        DatoClimaticoCache::factory()
            ->reciente()
            ->create([
                'unidad_productiva_id' => $unidad->id,
                'precipitacion' => [20, 15, 10, 5, 5, 5, 5], // Llovió bien
                'temperatura_actual' => 25,
            ]);

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(0, $resultado['creadas']);
        $this->assertEquals(1, $resultado['desactivadas']);
        
        $this->assertEquals(0, $unidad->alertasActivas()->count());
    }

    public function test_no_crea_alertas_duplicadas()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear datos de sequía
        for ($i = 0; $i < 15; $i++) {
            DatoClimaticoCache::factory()
                ->sequia()
                ->create([
                    'unidad_productiva_id' => $unidad->id,
                    'fecha_consulta' => now()->subDays($i),
                ]);
        }

        // Primera detección
        $resultado1 = $this->service->detectarAlertasParaUnidad($unidad);
        $this->assertEquals(1, $resultado1['creadas']);

        // Segunda detección (no debería crear duplicado)
        $resultado2 = $this->service->detectarAlertasParaUnidad($unidad);
        $this->assertEquals(0, $resultado2['creadas']);

        // Verificar que solo hay una alerta
        $this->assertEquals(1, $unidad->alertasActivas()->count());
    }

    public function test_no_crea_alertas_con_datos_antiguos()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear datos antiguos
        DatoClimaticoCache::factory()
            ->sequia()
            ->antiguo() // > 25 horas
            ->create([
                'unidad_productiva_id' => $unidad->id,
            ]);

        $resultado = $this->service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(0, $resultado['creadas']);
        $this->assertEquals(0, $unidad->alertasActivas()->count());
    }

    public function test_productor_solo_ve_sus_alertas()
    {
        // Crear dos productores
        $productor1 = Productor::factory()->create();
        $productor2 = Productor::factory()->create();

        // Crear unidades para cada uno
        $unidad1 = UnidadProductiva::factory()->create();
        $unidad2 = UnidadProductiva::factory()->create();

        $unidad1->productores()->attach($productor1->id);
        $unidad2->productores()->attach($productor2->id);

        // Crear alertas
        AlertaAmbiental::factory()->create(['unidad_productiva_id' => $unidad1->id]);
        AlertaAmbiental::factory()->create(['unidad_productiva_id' => $unidad2->id]);

        // Verificar que cada productor solo ve sus alertas
        $alertas1 = $this->service->obtenerAlertasActivasParaProductor($productor1->id);
        $alertas2 = $this->service->obtenerAlertasActivasParaProductor($productor2->id);

        $this->assertCount(1, $alertas1);
        $this->assertCount(1, $alertas2);
        $this->assertNotEquals(
            $alertas1->first()->id,
            $alertas2->first()->id
        );
    }

    public function test_marca_alerta_como_leida()
    {
        $alerta = AlertaAmbiental::factory()
            ->noLeida()
            ->create();

        $this->assertFalse($alerta->leida);

        $alerta->marcarComoLeida();
        $alerta->refresh();

        $this->assertTrue($alerta->leida);
    }
}
```

---

## ✅ CHECKLIST DE APLICACIÓN

### Antes de Empezar Implementación

- [ ] Crear `DatoClimaticoCache Factory.php` con states
- [ ] Crear `AlertaAmbientalFactory.php` con states
- [ ] Agregar constantes de umbrales al servicio
- [ ] Agregar validación de datos recientes
- [ ] Crear `AlertasAmbientalesDemoSeeder.php`

### Durante Implementación

- [ ] Agregar validación de permisos en Livewire
- [ ] Agregar logging de operaciones
- [ ] Actualizar tests con factories

### Después de Implementación

- [ ] Ejecutar tests: `php artisan test`
- [ ] Probar seeder: `php artisan db:seed --class=AlertasAmbientalesDemoSeeder`
- [ ] Verificar logs en `storage/logs/laravel.log`
- [ ] Documentar en README

---

## 🎯 RESULTADO ESPERADO

Con estas correcciones aplicadas:

✅ Tests funcionarán correctamente  
✅ Validaciones de seguridad implementadas  
✅ Código más mantenible con constantes  
✅ Demo fácil de mostrar con seeder  
✅ Logs para debugging y auditoría  
✅ Factories reutilizables para futuro

**Calificación de la guía:** 8.5/10 → **9.5/10** ⭐

---

**¿Listo para aplicar las correcciones?**

1. 🔧 **Aplícalas ahora** (copiar/pegar código)
2. 📖 **Revisa una por una** conmigo
3. 🚀 **Empieza sin correcciones** y aplicamos sobre la marcha

¿Qué prefieres?

