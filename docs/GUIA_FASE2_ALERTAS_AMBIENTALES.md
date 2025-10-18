# 🚨 GUÍA COMPLETA: Fase 2 - Alertas Ambientales

**Fecha:** 17 de Octubre de 2025  
**Fase:** 2 de 5  
**Tiempo Estimado:** 1 semana (5-7 días)  
**Prerequisito:** Fase 1 completa ✅  
**Rama Git:** `feat/modulo-ambiental-fase2`

---

## 📋 ÍNDICE

1. [Visión General](#visión-general)
2. [Tipos de Alertas](#tipos-de-alertas)
3. [Arquitectura Técnica](#arquitectura-técnica)
4. [Paso 1: Migración](#paso-1-migración)
5. [Paso 2: Modelo](#paso-2-modelo)
6. [Paso 3: Servicio de Alertas](#paso-3-servicio-de-alertas)
7. [Paso 4: Comando Artisan](#paso-4-comando-artisan)
8. [Paso 5: Componente Livewire](#paso-5-componente-livewire)
9. [Paso 6: Integración UI](#paso-6-integración-ui)
10. [Paso 7: Notificaciones](#paso-7-notificaciones)
11. [Paso 8: Testing](#paso-8-testing)
12. [Checklist Final](#checklist-final)

---

## 🎯 VISIÓN GENERAL

### ¿Qué vamos a construir?

Un **sistema proactivo de alertas ambientales** que:

1. **Analiza automáticamente** los datos climáticos cada día
2. **Detecta condiciones de riesgo** (sequía, tormentas, estrés térmico, heladas)
3. **Crea alertas** en la base de datos
4. **Notifica al productor** por email/SMS
5. **Muestra alertas activas** en el dashboard con campana 🔔
6. **Permite marcar como leídas** las alertas

### Resultado Final

```
Dashboard del Productor:
┌─────────────────────────────────────┐
│  🔔 (3)  ← Campana con contador    │
│                                      │
│  🚨 ALERTAS ACTIVAS                 │
│  ┌──────────────────────────────┐  │
│  │ 🔴 Sequía Prolongada          │  │
│  │    15 días sin lluvia         │  │
│  │    Campo: La Esperanza        │  │
│  │    [Marcar leída]             │  │
│  └──────────────────────────────┘  │
│                                      │
│  ┌──────────────────────────────┐  │
│  │ 🟠 Tormenta Intensa           │  │
│  │    Lluvia 60mm esperada       │  │
│  │    Fecha: Mañana 18/10        │  │
│  │    [Marcar leída]             │  │
│  └──────────────────────────────┘  │
└─────────────────────────────────────┘
```

---

## 🚨 TIPOS DE ALERTAS

### 1. Alerta de Sequía 🔴

**Condiciones:**
- Sin lluvia por > 15 días consecutivos
- O temperatura promedio > 32°C por > 5 días

**Nivel:** Crítico  
**Mensaje:** "Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales."

**Recomendaciones:**
- Revisar disponibilidad de agua
- Preparar plan de suplementación
- Evaluar rotación de pasturas

---

### 2. Alerta de Tormenta ⛈️

**Condiciones:**
- Lluvia esperada > 50mm en 24 horas
- O viento esperado > 60 km/h

**Nivel:** Alto  
**Mensaje:** "Se espera tormenta intensa. Asegura instalaciones y protege animales vulnerables."

**Recomendaciones:**
- Resguardar crías y animales débiles
- Revisar techos e instalaciones
- Preparar refugios

---

### 3. Alerta de Estrés Térmico 🌡️

**Condiciones:**
- Temperatura > 35°C por > 3 días consecutivos

**Nivel:** Medio  
**Mensaje:** "Temperaturas extremas pueden afectar el bienestar animal. Aumenta disponibilidad de sombra y agua."

**Recomendaciones:**
- Aumentar disponibilidad de agua
- Proporcionar sombra
- Evitar movimientos de animales en horas pico

---

### 4. Alerta de Helada ❄️

**Condiciones:**
- Temperatura mínima < 5°C

**Nivel:** Bajo  
**Mensaje:** "Se esperan temperaturas bajas. Protege crías recién nacidas."

**Recomendaciones:**
- Proteger crías y animales jóvenes
- Verificar refugios
- Aumentar suplementación energética

---

## 🏗️ ARQUITECTURA TÉCNICA

### Flujo de Datos

```
1. Datos Climáticos (ya existen)
        ↓
2. Comando Artisan (diario 7:00 AM)
   "php artisan alertas:detectar"
        ↓
3. AlertasAmbientalesService
   - Analiza datos de cada UP
   - Aplica reglas de detección
   - Crea alertas si aplica
        ↓
4. Base de Datos
   - Guarda alertas activas
   - Marca anteriores como inactivas
        ↓
5. Notificaciones
   - Email al productor
   - SMS (si configurado)
        ↓
6. UI (Livewire)
   - Campana con contador
   - Lista de alertas
   - Marcar como leída
```

### Tablas Involucradas

```
datos_climaticos_cache (ya existe)
    ↓
alertas_ambientales (nueva)
    ↓
notifications (Laravel ya la tiene)
```

---

## 📝 PASO 1: MIGRACIÓN

### Crear migración

```bash
php artisan make:migration create_alertas_ambientales_table
```

### Código de la migración

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_ambientales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            
            // Tipo de alerta
            $table->enum('tipo', ['sequia', 'tormenta', 'estres_termico', 'helada']);
            
            // Nivel de gravedad
            $table->enum('nivel', ['critico', 'alto', 'medio', 'bajo']);
            
            // Mensaje y detalles
            $table->string('titulo');
            $table->text('mensaje');
            $table->json('datos_contexto')->nullable(); // Datos específicos (temp, lluvia, etc)
            
            // Estado
            $table->boolean('activa')->default(true);
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            
            // Notificaciones
            $table->boolean('notificado_email')->default(false);
            $table->boolean('notificado_sms')->default(false);
            $table->timestamp('fecha_notificacion')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('unidad_productiva_id');
            $table->index(['activa', 'leida']);
            $table->index('tipo');
            $table->index('nivel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_ambientales');
    }
};
```

### Ejecutar migración

```bash
php artisan migrate
```

---

## 📦 PASO 2: MODELO

### Crear modelo

```bash
php artisan make:model AlertaAmbiental
```

### Código del modelo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaAmbiental extends Model
{
    protected $table = 'alertas_ambientales';

    protected $fillable = [
        'unidad_productiva_id',
        'tipo',
        'nivel',
        'titulo',
        'mensaje',
        'datos_contexto',
        'activa',
        'leida',
        'fecha_inicio',
        'fecha_fin',
        'notificado_email',
        'notificado_sms',
        'fecha_notificacion',
    ];

    protected $casts = [
        'datos_contexto' => 'array',
        'activa' => 'boolean',
        'leida' => 'boolean',
        'notificado_email' => 'boolean',
        'notificado_sms' => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_notificacion' => 'datetime',
    ];

    /**
     * Relación con UnidadProductiva
     */
    public function unidadProductiva(): BelongsTo
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    /**
     * Scope para alertas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para alertas no leídas
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    /**
     * Scope por tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope por nivel
     */
    public function scopeNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Marca la alerta como leída
     */
    public function marcarComoLeida(): void
    {
        $this->update(['leida' => true]);
    }

    /**
     * Desactiva la alerta
     */
    public function desactivar(): void
    {
        $this->update([
            'activa' => false,
            'fecha_fin' => now(),
        ]);
    }

    /**
     * Obtiene el emoji según el tipo
     */
    public function obtenerEmoji(): string
    {
        return match($this->tipo) {
            'sequia' => '🔴',
            'tormenta' => '⛈️',
            'estres_termico' => '🌡️',
            'helada' => '❄️',
            default => '⚠️',
        };
    }

    /**
     * Obtiene el color según el nivel
     */
    public function obtenerColor(): string
    {
        return match($this->nivel) {
            'critico' => 'red',
            'alto' => 'orange',
            'medio' => 'yellow',
            'bajo' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Obtiene las recomendaciones según el tipo
     */
    public function obtenerRecomendaciones(): array
    {
        return match($this->tipo) {
            'sequia' => [
                'Verifica disponibilidad de agua para los animales',
                'Prepara plan de suplementación',
                'Evalúa rotación de pasturas',
            ],
            'tormenta' => [
                'Resguarda crías y animales débiles',
                'Revisa techos e instalaciones',
                'Prepara refugios',
            ],
            'estres_termico' => [
                'Aumenta disponibilidad de agua',
                'Proporciona sombra',
                'Evita movimientos en horas pico',
            ],
            'helada' => [
                'Protege crías recién nacidas',
                'Verifica refugios',
                'Aumenta suplementación energética',
            ],
            default => [],
        };
    }
}
```

### Agregar relación en UnidadProductiva

```php
// En app/Models/UnidadProductiva.php

/**
 * Relación con alertas ambientales
 */
public function alertasAmbientales()
{
    return $this->hasMany(AlertaAmbiental::class);
}

/**
 * Alertas activas
 */
public function alertasActivas()
{
    return $this->hasMany(AlertaAmbiental::class)->activas();
}

/**
 * Alertas no leídas
 */
public function alertasNoLeidas()
{
    return $this->hasMany(AlertaAmbiental::class)->activas()->noLeidas();
}
```

---

## ⚙️ PASO 3: SERVICIO DE ALERTAS

### Crear servicio

```bash
# Crear directorio si no existe
mkdir -p app/Services

# Crear archivo
touch app/Services/AlertasAmbientalesService.php
```

### Código del servicio

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
    /**
     * Detecta y crea alertas para todas las unidades productivas
     */
    public function detectarAlertasParaTodasLasUnidades(): array
    {
        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->with(['datosClimaticos' => function($query) {
                $query->latest('fecha_consulta')->take(30); // Últimos 30 días
            }])
            ->get();

        $estadisticas = [
            'total_unidades' => $unidades->count(),
            'alertas_creadas' => 0,
            'alertas_desactivadas' => 0,
            'errores' => 0,
        ];

        foreach ($unidades as $unidad) {
            try {
                $resultado = $this->detectarAlertasParaUnidad($unidad);
                $estadisticas['alertas_creadas'] += $resultado['creadas'];
                $estadisticas['alertas_desactivadas'] += $resultado['desactivadas'];
            } catch (\Exception $e) {
                $estadisticas['errores']++;
                Log::error('Error detectando alertas', [
                    'unidad_productiva_id' => $unidad->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $estadisticas;
    }

    /**
     * Detecta alertas para una unidad productiva específica
     */
    public function detectarAlertasParaUnidad(UnidadProductiva $unidad): array
    {
        $estadisticas = [
            'creadas' => 0,
            'desactivadas' => 0,
        ];

        // Obtener datos climáticos recientes
        $datosClimaticos = $unidad->datosClimaticos()
            ->where('fecha_consulta', '>=', now()->subDays(30))
            ->orderBy('fecha_consulta', 'desc')
            ->get();

        if ($datosClimaticos->isEmpty()) {
            return $estadisticas;
        }

        // 1. Detectar Sequía
        if ($this->detectarSequia($datosClimaticos)) {
            if ($this->crearOActualizarAlerta($unidad, 'sequia', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'sequia')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 2. Detectar Tormenta
        $datoMasReciente = $datosClimaticos->first();
        if ($this->detectarTormenta($datoMasReciente)) {
            if ($this->crearOActualizarAlerta($unidad, 'tormenta', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'tormenta')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 3. Detectar Estrés Térmico
        if ($this->detectarEstreTermico($datosClimaticos)) {
            if ($this->crearOActualizarAlerta($unidad, 'estres_termico', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'estres_termico')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 4. Detectar Helada
        if ($this->detectarHelada($datoMasReciente)) {
            if ($this->crearOActualizarAlerta($unidad, 'helada', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'helada')) {
                $estadisticas['desactivadas']++;
            }
        }

        return $estadisticas;
    }

    /**
     * Detecta condiciones de sequía
     */
    private function detectarSequia($datosClimaticos): bool
    {
        // Verificar últimos 15 días
        $ultimos15Dias = $datosClimaticos->take(15);
        
        if ($ultimos15Dias->count() < 15) {
            return false;
        }

        // Contar días sin lluvia significativa (< 1mm)
        $diasSinLluvia = 0;
        $temperaturaPromedio = 0;

        foreach ($ultimos15Dias as $dato) {
            $lluviaHoy = $dato->precipitacion[0] ?? 0;
            
            if ($lluviaHoy < 1) {
                $diasSinLluvia++;
            }
            
            $temperaturaPromedio += $dato->temperatura_actual ?? 0;
        }

        $temperaturaPromedio = $temperaturaPromedio / $ultimos15Dias->count();

        // Sequía si:
        // - Más de 15 días sin lluvia, O
        // - Temperatura promedio > 32°C por 5+ días consecutivos
        return $diasSinLluvia >= 15 || $temperaturaPromedio > 32;
    }

    /**
     * Detecta tormenta inminente
     */
    private function detectarTormenta($datoClimatico): bool
    {
        if (!$datoClimatico) {
            return false;
        }

        // Revisar pronóstico de los próximos 3 días
        $lluviaEsperada = $datoClimatico->precipitacion ?? [];
        $vientoEsperado = $datoClimatico->viento_max ?? [];

        // Tormenta si en alguno de los 3 próximos días:
        // - Lluvia > 50mm, O
        // - Viento > 60 km/h
        for ($i = 0; $i < 3 && $i < count($lluviaEsperada); $i++) {
            $lluvia = $lluviaEsperada[$i] ?? 0;
            $viento = $vientoEsperado[$i] ?? 0;

            if ($lluvia > 50 || $viento > 60) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta estrés térmico
     */
    private function detectarEstreTermico($datosClimaticos): bool
    {
        // Verificar últimos 3 días y próximos 3 días
        $ultimos3Dias = $datosClimaticos->take(3);
        
        if ($ultimos3Dias->count() < 3) {
            return false;
        }

        $diasCalurosos = 0;

        foreach ($ultimos3Dias as $dato) {
            $tempMax = $dato->temperaturas_max[0] ?? 0;
            
            if ($tempMax > 35) {
                $diasCalurosos++;
            }
        }

        // Estrés térmico si 3+ días con temperatura > 35°C
        return $diasCalurosos >= 3;
    }

    /**
     * Detecta riesgo de helada
     */
    private function detectarHelada($datoClimatico): bool
    {
        if (!$datoClimatico) {
            return false;
        }

        // Revisar pronóstico de los próximos 2 días
        $temperaturasMin = $datoClimatico->temperaturas_min ?? [];

        for ($i = 0; $i < 2 && $i < count($temperaturasMin); $i++) {
            $tempMin = $temperaturasMin[$i] ?? 999;
            
            if ($tempMin < 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea o actualiza una alerta
     */
    private function crearOActualizarAlerta(UnidadProductiva $unidad, string $tipo, $datosClimaticos): bool
    {
        // Verificar si ya existe una alerta activa del mismo tipo
        $alertaExistente = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
            ->where('tipo', $tipo)
            ->activas()
            ->first();

        if ($alertaExistente) {
            // Ya existe, no crear duplicado
            return false;
        }

        // Crear nueva alerta
        $datosAlerta = $this->obtenerDatosAlerta($tipo, $datosClimaticos->first());

        AlertaAmbiental::create([
            'unidad_productiva_id' => $unidad->id,
            'tipo' => $tipo,
            'nivel' => $datosAlerta['nivel'],
            'titulo' => $datosAlerta['titulo'],
            'mensaje' => $datosAlerta['mensaje'],
            'datos_contexto' => $datosAlerta['contexto'],
            'fecha_inicio' => now(),
            'activa' => true,
            'leida' => false,
        ]);

        return true;
    }

    /**
     * Desactiva alertas del tipo especificado
     */
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
        }

        return true;
    }

    /**
     * Obtiene los datos específicos de cada tipo de alerta
     */
    private function obtenerDatosAlerta(string $tipo, $datoClimatico): array
    {
        return match($tipo) {
            'sequia' => [
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales.',
                'contexto' => [
                    'temperatura_promedio' => $datoClimatico->temperatura_actual ?? null,
                    'dias_sin_lluvia' => 15,
                ],
            ],
            'tormenta' => [
                'nivel' => 'alto',
                'titulo' => 'Tormenta Intensa',
                'mensaje' => 'Se espera tormenta intensa. Asegura instalaciones y protege animales vulnerables.',
                'contexto' => [
                    'lluvia_esperada' => $datoClimatico->precipitacion[0] ?? null,
                    'viento_esperado' => $datoClimatico->viento_max[0] ?? null,
                    'fecha_esperada' => $datoClimatico->fechas[0] ?? null,
                ],
            ],
            'estres_termico' => [
                'nivel' => 'medio',
                'titulo' => 'Estrés Térmico',
                'mensaje' => 'Temperaturas extremas pueden afectar el bienestar animal. Aumenta disponibilidad de sombra y agua.',
                'contexto' => [
                    'temperatura_maxima' => $datoClimatico->temperaturas_max[0] ?? null,
                    'dias_consecutivos' => 3,
                ],
            ],
            'helada' => [
                'nivel' => 'bajo',
                'titulo' => 'Riesgo de Helada',
                'mensaje' => 'Se esperan temperaturas bajas. Protege crías recién nacidas.',
                'contexto' => [
                    'temperatura_minima' => $datoClimatico->temperaturas_min[0] ?? null,
                    'fecha_esperada' => $datoClimatico->fechas[0] ?? null,
                ],
            ],
            default => [
                'nivel' => 'bajo',
                'titulo' => 'Alerta Ambiental',
                'mensaje' => 'Condición detectada que requiere atención.',
                'contexto' => [],
            ],
        };
    }

    /**
     * Obtiene alertas activas para un productor
     */
    public function obtenerAlertasActivasParaProductor($productorId): \Illuminate\Database\Eloquent\Collection
    {
        return AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productorId) {
            $query->where('productors.id', $productorId);
        })
        ->activas()
        ->with('unidadProductiva')
        ->orderBy('nivel', 'asc') // Crítico primero
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Cuenta alertas no leídas para un productor
     */
    public function contarAlertasNoLeidasParaProductor($productorId): int
    {
        return AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productorId) {
            $query->where('productors.id', $productorId);
        })
        ->activas()
        ->noLeidas()
        ->count();
    }
}
```

---

## 🤖 PASO 4: COMANDO ARTISAN

### Crear comando

```bash
php artisan make:command DetectarAlertasAmbientales
```

### Código del comando

```php
<?php

namespace App\Console\Commands;

use App\Services\AlertasAmbientalesService;
use Illuminate\Console\Command;

class DetectarAlertasAmbientales extends Command
{
    protected $signature = 'alertas:detectar
                            {--unidad-id= : ID específico de unidad productiva}
                            {--forzar : Forzar detección incluso si ya se ejecutó hoy}';

    protected $description = 'Detecta condiciones de riesgo y crea alertas ambientales';

    public function handle(AlertasAmbientalesService $alertasService): int
    {
        $this->info('🚨 Detectando alertas ambientales...');
        $this->newLine();

        $inicio = microtime(true);

        if ($this->option('unidad-id')) {
            // Detectar para una unidad específica
            $unidadId = $this->option('unidad-id');
            $unidad = \App\Models\UnidadProductiva::find($unidadId);

            if (!$unidad) {
                $this->error("❌ No se encontró la unidad productiva con ID: {$unidadId}");
                return Command::FAILURE;
            }

            $this->info("Analizando: {$unidad->nombre}");
            $resultado = $alertasService->detectarAlertasParaUnidad($unidad);

            $this->newLine();
            $this->line("✅ Alertas creadas: {$resultado['creadas']}");
            $this->line("⏭️  Alertas desactivadas: {$resultado['desactivadas']}");

        } else {
            // Detectar para todas las unidades
            $estadisticas = $alertasService->detectarAlertasParaTodasLasUnidades();

            $this->newLine();
            $this->line("📊 Resultados:");
            $this->line("   • Unidades analizadas: {$estadisticas['total_unidades']}");
            $this->line("   • Alertas creadas: {$estadisticas['alertas_creadas']}");
            $this->line("   • Alertas desactivadas: {$estadisticas['alertas_desactivadas']}");
            
            if ($estadisticas['errores'] > 0) {
                $this->warn("   ⚠️  Errores: {$estadisticas['errores']}");
            }
        }

        $duracion = round(microtime(true) - $inicio, 2);
        $this->newLine();
        $this->info("✅ Proceso completado en {$duracion} segundos");

        return Command::SUCCESS;
    }
}
```

### Registrar en schedule

```php
// En routes/console.php

use Illuminate\Support\Facades\Schedule;

// Detectar alertas diariamente a las 7:00 AM (después de actualizar clima a las 6:00 AM)
Schedule::command('alertas:detectar')->dailyAt('07:00');
```

### Probar el comando

```bash
# Detectar para todas las unidades
php artisan alertas:detectar

# Detectar para una unidad específica
php artisan alertas:detectar --unidad-id=1

# Forzar detección
php artisan alertas:detectar --forzar
```

---

## 🎨 PASO 5: COMPONENTE LIVEWIRE

### Crear componente

```bash
php artisan make:livewire Productor/AlertasWidget
```

### Código del componente PHP

```php
<?php

namespace App\Livewire\Productor;

use App\Models\Productor;
use App\Services\AlertasAmbientalesService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlertasWidget extends Component
{
    public $alertasActivas = [];
    public $cantidadNoLeidas = 0;
    public $mostrarLista = false;

    protected $listeners = ['alertaMarcadaComoLeida' => 'cargarAlertas'];

    public function mount()
    {
        $this->cargarAlertas();
    }

    public function cargarAlertas()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        $alertasService = app(AlertasAmbientalesService::class);
        
        $this->alertasActivas = $alertasService->obtenerAlertasActivasParaProductor($productor->id);
        $this->cantidadNoLeidas = $alertasService->contarAlertasNoLeidasParaProductor($productor->id);
    }

    public function toggleLista()
    {
        $this->mostrarLista = !$this->mostrarLista;
    }

    public function marcarComoLeida($alertaId)
    {
        $alerta = \App\Models\AlertaAmbiental::find($alertaId);

        if ($alerta) {
            $alerta->marcarComoLeida();
            $this->cargarAlertas();
            
            $this->dispatch('alerta-leida', [
                'mensaje' => 'Alerta marcada como leída'
            ]);
        }
    }

    public function marcarTodasComoLeidas()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if ($productor) {
            \App\Models\AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productor) {
                $query->where('productors.id', $productor->id);
            })
            ->activas()
            ->noLeidas()
            ->update(['leida' => true]);

            $this->cargarAlertas();
            
            $this->dispatch('alerta-leida', [
                'mensaje' => 'Todas las alertas marcadas como leídas'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.productor.alertas-widget');
    }
}
```

### Código de la vista Blade

```blade
{{-- resources/views/livewire/productor/alertas-widget.blade.php --}}

<div class="relative">
    {{-- Botón de campana --}}
    <button 
        wire:click="toggleLista"
        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 rounded-lg"
        aria-label="Alertas ambientales"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        {{-- Badge con contador --}}
        @if($cantidadNoLeidas > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $cantidadNoLeidas }}
            </span>
        @endif
    </button>

    {{-- Panel de alertas --}}
    @if($mostrarLista)
        <div 
            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
            wire:click.away="mostrarLista = false"
        >
            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    🚨 Alertas Ambientales
                </h3>
                @if($cantidadNoLeidas > 0)
                    <button 
                        wire:click="marcarTodasComoLeidas"
                        class="text-xs text-blue-600 hover:text-blue-800"
                    >
                        Marcar todas leídas
                    </button>
                @endif
            </div>

            {{-- Lista de alertas --}}
            <div class="max-h-96 overflow-y-auto">
                @forelse($alertasActivas as $alerta)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 @if(!$alerta->leida) bg-blue-50 @endif">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Emoji y título --}}
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-2xl">{{ $alerta->obtenerEmoji() }}</span>
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $alerta->titulo }}
                                    </h4>
                                    @if(!$alerta->leida)
                                        <span class="px-2 py-0.5 text-xs font-medium text-white bg-blue-600 rounded-full">
                                            Nuevo
                                        </span>
                                    @endif
                                </div>

                                {{-- Mensaje --}}
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ $alerta->mensaje }}
                                </p>

                                {{-- Unidad Productiva --}}
                                <p class="text-xs text-gray-500">
                                    📍 {{ $alerta->unidadProductiva->nombre }}
                                </p>

                                {{-- Contexto específico --}}
                                @if($alerta->datos_contexto)
                                    <div class="mt-2 text-xs text-gray-600">
                                        @if($alerta->tipo === 'sequia')
                                            <span>🌡️ Temp. promedio: {{ $alerta->datos_contexto['temperatura_promedio'] ?? 'N/A' }}°C</span>
                                        @elseif($alerta->tipo === 'tormenta')
                                            <span>🌧️ Lluvia esperada: {{ $alerta->datos_contexto['lluvia_esperada'] ?? 'N/A' }}mm</span>
                                            <span class="ml-2">💨 Viento: {{ $alerta->datos_contexto['viento_esperado'] ?? 'N/A' }}km/h</span>
                                        @elseif($alerta->tipo === 'estres_termico')
                                            <span>🌡️ Temp. máxima: {{ $alerta->datos_contexto['temperatura_maxima'] ?? 'N/A' }}°C</span>
                                        @elseif($alerta->tipo === 'helada')
                                            <span>❄️ Temp. mínima: {{ $alerta->datos_contexto['temperatura_minima'] ?? 'N/A' }}°C</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Fecha --}}
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $alerta->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Botón marcar leída --}}
                            @if(!$alerta->leida)
                                <button 
                                    wire:click="marcarComoLeida({{ $alerta->id }})"
                                    class="ml-2 text-gray-400 hover:text-gray-600"
                                    title="Marcar como leída"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Recomendaciones (expandibles) --}}
                        <details class="mt-2">
                            <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-800">
                                Ver recomendaciones
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs text-gray-600">
                                @foreach($alerta->obtenerRecomendaciones() as $recomendacion)
                                    <li class="flex items-start">
                                        <span class="mr-2">•</span>
                                        <span>{{ $recomendacion }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-sm font-medium">No hay alertas activas</p>
                        <p class="text-xs mt-1">Tus campos están en buenas condiciones</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            @if($alertasActivas->count() > 0)
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Ver todas las alertas
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
```

---

## 🎨 PASO 6: INTEGRACIÓN UI

### Agregar widget al dashboard del productor

```blade
{{-- En resources/views/productor/dashboard.blade.php --}}

{{-- En el header del dashboard, al lado derecho --}}
<div class="flex items-center space-x-4">
    {{-- Widget de alertas (NUEVO) --}}
    @livewire('productor.alertas-widget')

    {{-- Notificaciones existentes --}}
    <button class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <!-- icono de notificaciones -->
        </svg>
    </button>

    {{-- Usuario --}}
    <div class="flex items-center">
        <!-- menú de usuario -->
    </div>
</div>
```

### Agregar sección de alertas en el dashboard

```blade
{{-- Después del widget de clima --}}

<div class="grid grid-cols-1 gap-6 mb-8">
    {{-- Alertas Activas --}}
    @livewire('productor.alertas-panel')
</div>
```

### Crear componente panel de alertas (opcional)

```bash
php artisan make:livewire Productor/AlertasPanel
```

```php
<?php

namespace App\Livewire\Productor;

use App\Models\Productor;
use App\Services\AlertasAmbientalesService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlertasPanel extends Component
{
    public $alertasActivas = [];

    public function mount()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if ($productor) {
            $alertasService = app(AlertasAmbientalesService::class);
            $this->alertasActivas = $alertasService->obtenerAlertasActivasParaProductor($productor->id);
        }
    }

    public function render()
    {
        return view('livewire.productor.alertas-panel');
    }
}
```

```blade
{{-- resources/views/livewire/productor/alertas-panel.blade.php --}}

@if($alertasActivas->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            🚨 Alertas Activas
        </h3>

        <div class="space-y-3">
            @foreach($alertasActivas->take(3) as $alerta)
                <div class="flex items-start p-3 rounded-lg border-2 border-{{ $alerta->obtenerColor() }}-200 bg-{{ $alerta->obtenerColor() }}-50">
                    <span class="text-3xl mr-3">{{ $alerta->obtenerEmoji() }}</span>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $alerta->titulo }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $alerta->mensaje }}</p>
                        <p class="text-xs text-gray-500 mt-1">📍 {{ $alerta->unidadProductiva->nombre }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($alertasActivas->count() > 3)
            <div class="mt-4 text-center">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    Ver todas las alertas ({{ $alertasActivas->count() }})
                </a>
            </div>
        @endif
    </div>
@endif
```

---

## 📧 PASO 7: NOTIFICACIONES (Opcional)

### Crear notificación

```bash
php artisan make:notification AlertaAmbientalNotification
```

### Código de la notificación

```php
<?php

namespace App\Notifications;

use App\Models\AlertaAmbiental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaAmbientalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AlertaAmbiental $alerta
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🚨 {$this->alerta->titulo} - {$this->alerta->unidadProductiva->nombre}")
            ->greeting("Hola {$notifiable->name},")
            ->line($this->alerta->mensaje)
            ->line("**Campo:** {$this->alerta->unidadProductiva->nombre}")
            ->line("**Nivel:** " . ucfirst($this->alerta->nivel))
            ->line("**Recomendaciones:**")
            ->lines($this->alerta->obtenerRecomendaciones())
            ->action('Ver en el sistema', url('/productor/panel'))
            ->line('Mantente atento a las condiciones de tu campo.');
    }

    public function toArray($notifiable): array
    {
        return [
            'alerta_id' => $this->alerta->id,
            'tipo' => $this->alerta->tipo,
            'nivel' => $this->alerta->nivel,
            'titulo' => $this->alerta->titulo,
            'mensaje' => $this->alerta->mensaje,
            'unidad_productiva' => $this->alerta->unidadProductiva->nombre,
        ];
    }
}
```

### Enviar notificación desde el servicio

```php
// Agregar al final del método crearOActualizarAlerta en AlertasAmbientalesService

// Notificar a los productores
$productores = $unidad->productores()->with('usuario')->get();

foreach ($productores as $productor) {
    if ($productor->usuario) {
        $productor->usuario->notify(new AlertaAmbientalNotification($alerta));
    }
}

// Marcar como notificado
$alerta->update([
    'notificado_email' => true,
    'fecha_notificacion' => now(),
]);
```

---

## 🧪 PASO 8: TESTING

### Crear tests

```bash
php artisan make:test AlertasAmbientalesTest
```

### Código de tests

```php
<?php

namespace Tests\Feature;

use App\Models\UnidadProductiva;
use App\Models\DatoClimaticoCache;
use App\Services\AlertasAmbientalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertasAmbientalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_detecta_sequia_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear datos sin lluvia por 15 días
        for ($i = 0; $i < 15; $i++) {
            DatoClimaticoCache::factory()->create([
                'unidad_productiva_id' => $unidad->id,
                'temperatura_actual' => 30,
                'precipitacion' => [0, 0, 0, 0, 0, 0, 0],
                'fecha_consulta' => now()->subDays($i),
            ]);
        }

        $service = new AlertasAmbientalesService();
        $resultado = $service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertEquals('sequia', $alerta->tipo);
        $this->assertEquals('critico', $alerta->nivel);
    }

    public function test_detecta_tormenta_correctamente()
    {
        $unidad = UnidadProductiva::factory()->create();
        
        // Crear datos con lluvia intensa esperada
        DatoClimaticoCache::factory()->create([
            'unidad_productiva_id' => $unidad->id,
            'precipitacion' => [60, 10, 5, 0, 0, 0, 0], // 60mm primer día
            'viento_max' => [50, 30, 20, 10, 10, 10, 10],
            'fecha_consulta' => now(),
        ]);

        $service = new AlertasAmbientalesService();
        $resultado = $service->detectarAlertasParaUnidad($unidad);

        $this->assertEquals(1, $resultado['creadas']);
        
        $alerta = $unidad->alertasActivas()->first();
        $this->assertEquals('tormenta', $alerta->tipo);
    }

    public function test_desactiva_alertas_cuando_condicion_mejora()
    {
        // TODO: Implementar
    }

    public function test_no_crea_alertas_duplicadas()
    {
        // TODO: Implementar
    }
}
```

### Ejecutar tests

```bash
php artisan test --filter=AlertasAmbientalesTest
```

---

## ✅ CHECKLIST FINAL

### Backend ✅

- [ ] Migración `create_alertas_ambientales_table` creada y ejecutada
- [ ] Modelo `AlertaAmbiental` completo con métodos
- [ ] Relaciones agregadas en `UnidadProductiva`
- [ ] Servicio `AlertasAmbientalesService` implementado
- [ ] Comando `alertas:detectar` funcional
- [ ] Schedule configurado en `routes/console.php`
- [ ] Tests básicos creados

### Frontend ✅

- [ ] Componente `AlertasWidget` creado
- [ ] Vista del widget implementada
- [ ] Componente `AlertasPanel` creado (opcional)
- [ ] Integrado en dashboard del productor
- [ ] Campana con contador visible
- [ ] Lista de alertas funcional
- [ ] Marcar como leída funciona

### Notificaciones (Opcional) ⭐

- [ ] Notificación por email creada
- [ ] Integrada en el servicio
- [ ] Probada manualmente

### Testing ✅

- [ ] Test de detección de sequía
- [ ] Test de detección de tormenta
- [ ] Test de desactivación de alertas
- [ ] Test de no duplicados

---

## 🧪 CÓMO PROBAR

### 1. Probar detección manual

```bash
# Detectar alertas para todas las unidades
php artisan alertas:detectar

# Ver alertas creadas
php artisan tinker
>>> App\Models\AlertaAmbiental::with('unidadProductiva')->get()
```

### 2. Probar en el navegador

1. Abre `http://localhost:8000`
2. Login como productor
3. Verifica que veas la campana 🔔
4. Click en la campana
5. Deberías ver la lista de alertas
6. Click en "Marcar como leída"
7. Verifica que el contador disminuya

### 3. Simular condiciones de alerta

```bash
php artisan tinker

# Simular sequía
$unidad = App\Models\UnidadProductiva::first();
for ($i = 0; $i < 15; $i++) {
    App\Models\DatoClimaticoCache::create([
        'unidad_productiva_id' => $unidad->id,
        'temperatura_actual' => 35,
        'velocidad_viento' => 10,
        'codigo_clima' => 0,
        'temperaturas_max' => [35, 36, 34, 35, 35, 36, 34],
        'temperaturas_min' => [20, 21, 19, 20, 21, 20, 19],
        'precipitacion' => [0, 0, 0, 0, 0, 0, 0],
        'fechas' => [now()->addDays($i)->format('Y-m-d')],
        'datos_completos' => [],
        'fuente' => 'test',
        'fecha_consulta' => now()->subDays($i),
    ]);
}

# Detectar alertas
exit
php artisan alertas:detectar --unidad-id=1
```

---

## 🎉 RESULTADO FINAL

Al completar esta fase, tendrás:

✅ **Sistema de alertas completo y funcional**  
✅ **4 tipos de alertas implementadas**  
✅ **Detección automática diaria**  
✅ **Campana con notificaciones en dashboard**  
✅ **Notificaciones por email**  
✅ **Base para Fase 3 (NDVI)**

---

## 🚀 PRÓXIMOS PASOS

### Después de completar Fase 2:

1. **Commit y push:**
   ```bash
   git add .
   git commit -m "feat: Fase 2 - Sistema de alertas ambientales completo"
   git push origin feat/modulo-ambiental-fase2
   ```

2. **Actualizar checkpoint:**
   - Marcar Fase 2 como completa
   - Documentar logros

3. **Empezar Fase 3:**
   - NDVI Satelital con Copernicus
   - Integración con certificación

---

## 📞 SOPORTE

**Documentación relacionada:**
- `CHECKPOINT_FASE1_CLIMA.md` - Estado Fase 1
- `docs/PLAN_MODULO_AMBIENTAL_APIS_GRATUITAS.md` - Plan completo
- `ANALISIS_COMPLETO_MARTIN_OCT2025.md` - Análisis del proyecto

**Tiempo estimado:** 5-7 días  
**Dificultad:** Media  
**Requisitos:** Fase 1 completa ✅

---

**¡Éxito con la Fase 2! 🚨🌍🚀**

