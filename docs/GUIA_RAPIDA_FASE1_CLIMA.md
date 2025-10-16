# ⚡ GUÍA RÁPIDA: FASE 1 - DATOS CLIMÁTICOS

**Objetivo:** Integrar datos climáticos en tiempo real sin gastar un peso.  
**Tiempo estimado:** 1-2 semanas  
**Dificultad:** ⭐⭐☆☆☆ (Intermedia)

---

## 🎯 ¿Qué vamos a lograr?

Al terminar esta fase tendrás:

✅ Datos climáticos actualizados para cada unidad productiva  
✅ Pronóstico de 7 días visible en el dashboard  
✅ Actualización automática diaria  
✅ Caché para no exceder límites de las APIs  
✅ Widget visual en el panel del productor  

---

## 📚 PASO 1: Elegir la API

### Opción A: **Open-Meteo** (RECOMENDADA para empezar)

**Ventajas:**
- ✅ No requiere registro ni API key
- ✅ Sin límites estrictos
- ✅ Documentación excelente
- ✅ Respuestas en JSON simple

**Ejemplo de uso:**
```bash
# Clima actual en Misiones (lat: -27.3621, lon: -55.8969)
curl "https://api.open-meteo.com/v1/forecast?latitude=-27.3621&longitude=-55.8969&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=America/Argentina/Buenos_Aires"
```

### Opción B: **NASA POWER**

**Ventajas:**
- ✅ Datos muy precisos
- ✅ Datos históricos extensos
- ✅ Radiación solar incluida

**Desventajas:**
- ⚠️ Requiere registro (gratuito)
- ⚠️ Más complejo de usar

**Para esta guía usaremos Open-Meteo.**

---

## 📚 PASO 2: Probar la API manualmente

Abre tu terminal (PowerShell) y prueba:

```powershell
# Instalar curl si no lo tienes
# (Normalmente viene con Windows 10+)

# Probar API con coordenadas de prueba
curl "https://api.open-meteo.com/v1/forecast?latitude=-27.3621&longitude=-55.8969&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,windspeed_10m_max&timezone=America/Argentina/Buenos_Aires&forecast_days=7"
```

**Deberías recibir algo como:**
```json
{
  "latitude": -27.375,
  "longitude": -55.875,
  "current_weather": {
    "temperature": 18.5,
    "windspeed": 12.3,
    "weathercode": 0,
    "time": "2025-10-16T14:00"
  },
  "daily": {
    "time": ["2025-10-16", "2025-10-17", ...],
    "temperature_2m_max": [23.4, 25.1, ...],
    "temperature_2m_min": [15.2, 16.8, ...],
    "precipitation_sum": [0.0, 2.5, ...]
  }
}
```

✅ **Si funciona, ¡continúa al siguiente paso!**

---

## 📚 PASO 3: Crear la migración

Crea el archivo de migración:

```bash
php artisan make:migration create_datos_climaticos_cache_table
```

Edita el archivo en `database/migrations/2025_XX_XX_XXXXXX_create_datos_climaticos_cache_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_climaticos_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->onDelete('cascade');
            $table->string('fuente')->default('open_meteo'); // open_meteo, nasa_power
            
            // Datos actuales
            $table->decimal('temperatura_actual', 5, 2)->nullable();
            $table->decimal('velocidad_viento', 5, 2)->nullable();
            $table->integer('codigo_clima')->nullable(); // Weather code
            
            // Datos diarios (próximos 7 días)
            $table->json('temperaturas_max')->nullable(); // Array de 7 valores
            $table->json('temperaturas_min')->nullable();
            $table->json('precipitacion')->nullable();
            $table->json('probabilidad_lluvia')->nullable();
            $table->json('viento_max')->nullable();
            $table->json('fechas')->nullable(); // Array de fechas
            
            // Metadatos
            $table->json('datos_completos')->nullable(); // Respuesta completa de la API
            $table->timestamp('fecha_consulta');
            $table->timestamps();
            
            // Índices
            $table->index('unidad_productiva_id');
            $table->index('fecha_consulta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_climaticos_cache');
    }
};
```

**Ejecuta la migración:**
```bash
php artisan migrate
```

---

## 📚 PASO 4: Crear el modelo

Crea el modelo:

```bash
php artisan make:model DatoClimaticoCache
```

Edita `app/Models/DatoClimaticoCache.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoClimaticoCache extends Model
{
    protected $table = 'datos_climaticos_cache';

    protected $fillable = [
        'unidad_productiva_id',
        'fuente',
        'temperatura_actual',
        'velocidad_viento',
        'codigo_clima',
        'temperaturas_max',
        'temperaturas_min',
        'precipitacion',
        'probabilidad_lluvia',
        'viento_max',
        'fechas',
        'datos_completos',
        'fecha_consulta',
    ];

    protected $casts = [
        'temperaturas_max' => 'array',
        'temperaturas_min' => 'array',
        'precipitacion' => 'array',
        'probabilidad_lluvia' => 'array',
        'viento_max' => 'array',
        'fechas' => 'array',
        'datos_completos' => 'array',
        'fecha_consulta' => 'datetime',
    ];

    /**
     * Relación con UnidadProductiva
     */
    public function unidadProductiva()
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    /**
     * Verifica si los datos están vigentes (menos de 24 horas)
     */
    public function esVigente(): bool
    {
        return $this->fecha_consulta->gt(now()->subHours(24));
    }

    /**
     * Obtiene el ícono del clima según el código
     */
    public function obtenerIconoClima(): string
    {
        // Weather codes de Open-Meteo
        return match(true) {
            $this->codigo_clima === 0 => '☀️', // Despejado
            $this->codigo_clima <= 3 => '⛅', // Parcialmente nublado
            $this->codigo_clima <= 48 => '☁️', // Nublado
            $this->codigo_clima <= 67 => '🌧️', // Lluvia
            $this->codigo_clima <= 77 => '❄️', // Nieve
            $this->codigo_clima <= 99 => '⛈️', // Tormenta
            default => '🌡️', // Desconocido
        };
    }
}
```

---

## 📚 PASO 5: Crear el servicio de API

Crea el directorio y archivo:

```bash
mkdir app/Services/ClimaApi
```

Crea `app/Services/ClimaApi/OpenMeteoApiService.php`:

```php
<?php

namespace App\Services\ClimaApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoApiService
{
    private string $baseUrl = 'https://api.open-meteo.com/v1';

    /**
     * Obtiene el pronóstico completo para una ubicación
     *
     * @param float $latitud
     * @param float $longitud
     * @param int $dias Días de pronóstico (default 7)
     * @return array|null
     */
    public function obtenerPronostico(float $latitud, float $longitud, int $dias = 7): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/forecast", [
                'latitude' => $latitud,
                'longitude' => $longitud,
                'current_weather' => 'true',
                'daily' => implode(',', [
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'precipitation_sum',
                    'precipitation_probability_max',
                    'windspeed_10m_max',
                ]),
                'timezone' => 'America/Argentina/Buenos_Aires',
                'forecast_days' => $dias,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Error en Open-Meteo API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Excepción en Open-Meteo API', [
                'mensaje' => $e->getMessage(),
                'latitud' => $latitud,
                'longitud' => $longitud,
            ]);

            return null;
        }
    }

    /**
     * Obtiene datos históricos
     *
     * @param float $latitud
     * @param float $longitud
     * @param string $fechaInicio Formato: YYYY-MM-DD
     * @param string $fechaFin Formato: YYYY-MM-DD
     * @return array|null
     */
    public function obtenerHistorico(float $latitud, float $longitud, string $fechaInicio, string $fechaFin): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/archive", [
                'latitude' => $latitud,
                'longitude' => $longitud,
                'start_date' => $fechaInicio,
                'end_date' => $fechaFin,
                'daily' => implode(',', [
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'precipitation_sum',
                ]),
                'timezone' => 'America/Argentina/Buenos_Aires',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error obteniendo datos históricos', [
                'mensaje' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Formatea los datos de la API al formato de nuestro modelo
     *
     * @param array $datosApi
     * @return array
     */
    public function formatearDatos(array $datosApi): array
    {
        $current = $datosApi['current_weather'] ?? [];
        $daily = $datosApi['daily'] ?? [];

        return [
            'fuente' => 'open_meteo',
            'temperatura_actual' => $current['temperature'] ?? null,
            'velocidad_viento' => $current['windspeed'] ?? null,
            'codigo_clima' => $current['weathercode'] ?? null,
            'temperaturas_max' => $daily['temperature_2m_max'] ?? [],
            'temperaturas_min' => $daily['temperature_2m_min'] ?? [],
            'precipitacion' => $daily['precipitation_sum'] ?? [],
            'probabilidad_lluvia' => $daily['precipitation_probability_max'] ?? [],
            'viento_max' => $daily['windspeed_10m_max'] ?? [],
            'fechas' => $daily['time'] ?? [],
            'datos_completos' => $datosApi,
            'fecha_consulta' => now(),
        ];
    }
}
```

---

## 📚 PASO 6: Crear el comando Artisan

```bash
php artisan make:command ActualizarDatosClimaticos
```

Edita `app/Console/Commands/ActualizarDatosClimaticos.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\UnidadProductiva;
use App\Models\DatoClimaticoCache;
use App\Services\ClimaApi\OpenMeteoApiService;
use Illuminate\Console\Command;

class ActualizarDatosClimaticos extends Command
{
    protected $signature = 'clima:actualizar-datos 
                            {--unidad-id= : ID específico de unidad productiva}
                            {--forzar : Forzar actualización aunque tenga datos recientes}';

    protected $description = 'Actualiza los datos climáticos de las unidades productivas';

    public function handle(OpenMeteoApiService $climaService): int
    {
        $this->info('🌦️  Actualizando datos climáticos...');

        // Obtener unidades con coordenadas
        $query = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('activo', true);

        // Filtrar por ID si se especificó
        if ($this->option('unidad-id')) {
            $query->where('id', $this->option('unidad-id'));
        }

        $unidades = $query->get();

        if ($unidades->isEmpty()) {
            $this->warn('No se encontraron unidades productivas con coordenadas.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$unidades->count()} unidades productivas...");

        $bar = $this->output->createProgressBar($unidades->count());
        $bar->start();

        $actualizadas = 0;
        $omitidas = 0;
        $errores = 0;

        foreach ($unidades as $unidad) {
            // Verificar si ya tiene datos recientes
            $datosExistentes = DatoClimaticoCache::where('unidad_productiva_id', $unidad->id)
                ->latest('fecha_consulta')
                ->first();

            if ($datosExistentes && $datosExistentes->esVigente() && !$this->option('forzar')) {
                $omitidas++;
                $bar->advance();
                continue;
            }

            // Consultar API
            $datosApi = $climaService->obtenerPronostico($unidad->latitud, $unidad->longitud);

            if ($datosApi) {
                $datosFormateados = $climaService->formatearDatos($datosApi);
                $datosFormateados['unidad_productiva_id'] = $unidad->id;

                DatoClimaticoCache::updateOrCreate(
                    ['unidad_productiva_id' => $unidad->id],
                    $datosFormateados
                );

                $actualizadas++;
            } else {
                $errores++;
            }

            $bar->advance();

            // Pequeña pausa para no saturar la API
            usleep(100000); // 0.1 segundos
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Actualizadas: {$actualizadas}");
        $this->info("⏭️  Omitidas (datos recientes): {$omitidas}");
        
        if ($errores > 0) {
            $this->warn("❌ Errores: {$errores}");
        }

        return Command::SUCCESS;
    }
}
```

**Probar el comando:**
```bash
php artisan clima:actualizar-datos --forzar
```

---

## 📚 PASO 7: Programar actualización automática

Edita `app/Console/Kernel.php` o `routes/console.php` (Laravel 12):

```php
// routes/console.php

use Illuminate\Support\Facades\Schedule;

Schedule::command('clima:actualizar-datos')->dailyAt('06:00');
```

---

## 📚 PASO 8: Crear el componente Livewire

```bash
php artisan make:livewire Productor/ClimaWidget
```

Edita `app/Livewire/Productor/ClimaWidget.php`:

```php
<?php

namespace App\Livewire\Productor;

use App\Models\DatoClimaticoCache;
use App\Models\Productor;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ClimaWidget extends Component
{
    public $datosClima = null;
    public $unidadSeleccionada = null;

    public function mount()
    {
        $this->cargarDatosClima();
    }

    public function cargarDatosClima()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        // Obtener la primera unidad con datos de clima
        $this->unidadSeleccionada = $productor->unidadesProductivas()
            ->whereHas('datosClimaticos')
            ->with('datosClimaticos')
            ->first();

        if ($this->unidadSeleccionada) {
            $this->datosClima = $this->unidadSeleccionada->datosClimaticos()
                ->latest('fecha_consulta')
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.productor.clima-widget');
    }
}
```

Edita `resources/views/livewire/productor/clima-widget.blade.php`:

```php
<div class="bg-white rounded-lg shadow p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            🌦️ Clima Actual
        </h3>
        @if($datosClima)
            <span class="text-xs text-gray-500">
                Actualizado: {{ $datosClima->fecha_consulta->diffForHumans() }}
            </span>
        @endif
    </div>

    @if($datosClima && $datosClima->esVigente())
        {{-- Clima actual --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-5xl font-bold text-gray-900">
                        {{ number_format($datosClima->temperatura_actual, 1) }}°C
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        @if($unidadSeleccionada)
                            {{ $unidadSeleccionada->nombre }}
                        @endif
                    </div>
                </div>
                <div class="text-6xl">
                    {{ $datosClima->obtenerIconoClima() }}
                </div>
            </div>

            {{-- Viento --}}
            @if($datosClima->velocidad_viento)
                <div class="mt-3 text-sm text-gray-600">
                    💨 Viento: {{ number_format($datosClima->velocidad_viento, 1) }} km/h
                </div>
            @endif
        </div>

        {{-- Pronóstico 7 días --}}
        @if($datosClima->fechas && count($datosClima->fechas) > 0)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                    Pronóstico 7 días
                </h4>
                <div class="space-y-2">
                    @foreach($datosClima->fechas as $index => $fecha)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m') }} - 
                                {{ \Carbon\Carbon::parse($fecha)->locale('es')->dayName }}
                            </span>
                            <div class="flex items-center space-x-3">
                                @if(isset($datosClima->precipitacion[$index]) && $datosClima->precipitacion[$index] > 0)
                                    <span class="text-blue-600 text-xs">
                                        🌧️ {{ number_format($datosClima->precipitacion[$index], 1) }}mm
                                    </span>
                                @endif
                                <span class="text-gray-900 font-medium">
                                    {{ number_format($datosClima->temperaturas_max[$index] ?? 0, 0) }}° / 
                                    {{ number_format($datosClima->temperaturas_min[$index] ?? 0, 0) }}°
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        {{-- Sin datos --}}
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-2">🌡️</div>
            <p class="text-sm">
                No hay datos climáticos disponibles.
            </p>
            <p class="text-xs mt-1">
                Los datos se actualizan automáticamente cada 24 horas.
            </p>
        </div>
    @endif
</div>
```

---

## 📚 PASO 9: Agregar relación en UnidadProductiva

Edita `app/Models/UnidadProductiva.php` y agrega:

```php
/**
 * Relación con datos climáticos
 */
public function datosClimaticos()
{
    return $this->hasMany(DatoClimaticoCache::class);
}

/**
 * Obtiene los datos climáticos más recientes
 */
public function climaActual()
{
    return $this->hasOne(DatoClimaticoCache::class)->latestOfMany('fecha_consulta');
}
```

---

## 📚 PASO 10: Integrar en el dashboard

Edita `resources/views/livewire/productor/dashboard.blade.php` y agrega:

```php
{{-- Donde quieras mostrar el widget --}}
<div class="col-span-1">
    @livewire('productor.clima-widget')
</div>
```

---

## ✅ CHECKLIST DE VALIDACIÓN

Antes de dar por terminada la Fase 1, verifica:

- [ ] ✅ La migración se ejecutó sin errores
- [ ] ✅ El modelo `DatoClimaticoCache` existe
- [ ] ✅ El servicio `OpenMeteoApiService` funciona
- [ ] ✅ El comando `clima:actualizar-datos` se ejecuta correctamente
- [ ] ✅ Los datos se guardan en la base de datos
- [ ] ✅ El widget se muestra en el dashboard
- [ ] ✅ Los datos se ven correctos (temperatura, pronóstico)
- [ ] ✅ La actualización automática está programada

---

## 🐛 TROUBLESHOOTING

### Problema: "Call to undefined method"
**Solución:** Ejecuta `composer dump-autoload`

### Problema: API devuelve error 429 (Too Many Requests)
**Solución:** Agrega un delay entre requests: `usleep(200000);`

### Problema: No se muestran datos en el widget
**Solución:** 
1. Verifica que la unidad tenga lat/lon: `SELECT * FROM unidades_productivas WHERE latitud IS NOT NULL`
2. Ejecuta manualmente: `php artisan clima:actualizar-datos --forzar`
3. Verifica los logs: `tail -f storage/logs/laravel.log`

### Problema: Coordenadas NULL
**Solución:** Actualiza manualmente una unidad de prueba:
```sql
UPDATE unidades_productivas 
SET latitud = -27.3621, longitud = -55.8969 
WHERE id = 1;
```

---

## 📈 PRÓXIMOS PASOS

Una vez que esto funcione:

1. ✅ **Testear con datos reales** de productores
2. ✅ **Agregar más unidades** al widget (selector)
3. ✅ **Mejorar el diseño** del widget
4. ✅ **Continuar con Fase 2:** Alertas Ambientales

---

## 🎓 APRENDIZAJES CLAVE

Al completar esta fase habrás aprendido:

✅ Integración con APIs REST externas  
✅ Manejo de caché en Laravel  
✅ Comandos Artisan personalizados  
✅ Componentes Livewire  
✅ Jobs programados  
✅ Manejo de datos geoespaciales  

---

**¡Éxito en esta primera fase! 🚀**

Si tienes dudas, revisa:
- Documentación Open-Meteo: https://open-meteo.com/en/docs
- Laravel HTTP Client: https://laravel.com/docs/12.x/http-client
- Laravel Scheduling: https://laravel.com/docs/12.x/scheduling

