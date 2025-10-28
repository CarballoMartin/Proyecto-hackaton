# 📊 FASE 2 - MEJORAS: Configuración + Gráficos Históricos

**Fecha:** 19 de Octubre de 2025  
**Objetivo:** Agregar panel de configuración de umbrales y gráficos históricos de alertas  
**Tiempo Estimado:** 2-3 días  
**Dificultad:** Media

---

## 🎯 VISIÓN GENERAL

### ¿Qué vamos a construir?

**1. Panel de Configuración de Umbrales** ⚙️
- Permitir al productor ajustar los umbrales de alertas
- Configuración por tipo de alerta
- Valores predeterminados + personalizados
- Guardar en base de datos

**2. Gráficos Históricos de Alertas** 📈
- Visualizar alertas de los últimos 30/60/90 días
- Tipos de gráficos:
  - Línea temporal de alertas por tipo
  - Barras: cantidad por mes
  - Dona: distribución por tipo
  - Calendario de calor (heatmap)
- Integración con Chart.js (ya está en el proyecto)

---

## 📋 ÍNDICE

1. [Parte 1: Panel de Configuración](#parte-1-panel-de-configuración)
   - Migración
   - Modelo
   - Componente Livewire
   - Vista UI
2. [Parte 2: Gráficos Históricos](#parte-2-gráficos-históricos)
   - Componente de gráficos
   - Queries optimizadas
   - Integración Chart.js
3. [Parte 3: Integración UI](#parte-3-integración-ui)
4. [Testing](#testing)

---

## 📊 PARTE 1: PANEL DE CONFIGURACIÓN

### Resultado Visual

```
┌─────────────────────────────────────────────────────┐
│  ⚙️ Configuración de Alertas Ambientales           │
├─────────────────────────────────────────────────────┤
│                                                      │
│  🔴 Sequía                                          │
│  ├─ Días sin lluvia:     [15] días                 │
│  ├─ Temperatura umbral:  [32] °C                   │
│  └─ Días consecutivos:   [5]  días                 │
│                                                      │
│  ⛈️ Tormenta                                        │
│  ├─ Lluvia esperada:     [50] mm                   │
│  └─ Viento umbral:       [60] km/h                 │
│                                                      │
│  🌡️ Estrés Térmico                                 │
│  ├─ Temperatura máxima:  [35] °C                   │
│  └─ Días consecutivos:   [3]  días                 │
│                                                      │
│  ❄️ Helada                                          │
│  └─ Temperatura mínima:  [5]  °C                   │
│                                                      │
│  [Restablecer Valores Predeterminados] [Guardar]   │
└─────────────────────────────────────────────────────┘
```

---

### 1.1. Migración: `configuracion_alertas`

**Archivo:** `database/migrations/YYYY_MM_DD_HHMMSS_create_configuracion_alertas_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productor_id')->constrained('productores')->cascadeOnDelete();
            
            // Umbrales de Sequía
            $table->integer('sequia_dias_sin_lluvia')->default(15);
            $table->decimal('sequia_temperatura_umbral', 4, 1)->default(32.0);
            $table->integer('sequia_dias_consecutivos')->default(5);
            
            // Umbrales de Tormenta
            $table->decimal('tormenta_lluvia_umbral', 5, 1)->default(50.0);
            $table->decimal('tormenta_viento_umbral', 5, 1)->default(60.0);
            
            // Umbrales de Estrés Térmico
            $table->decimal('estres_temperatura_umbral', 4, 1)->default(35.0);
            $table->integer('estres_dias_consecutivos')->default(3);
            
            // Umbrales de Helada
            $table->decimal('helada_temperatura_umbral', 4, 1)->default(5.0);
            
            // Preferencias
            $table->boolean('notificaciones_email')->default(true);
            $table->boolean('notificaciones_sms')->default(false);
            
            $table->timestamps();
            
            // Un solo registro de configuración por productor
            $table->unique('productor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_alertas');
    }
};
```

---

### 1.2. Modelo: `ConfiguracionAlerta`

**Archivo:** `app/Models/ConfiguracionAlerta.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionAlerta extends Model
{
    protected $table = 'configuracion_alertas';

    protected $fillable = [
        'productor_id',
        // Sequía
        'sequia_dias_sin_lluvia',
        'sequia_temperatura_umbral',
        'sequia_dias_consecutivos',
        // Tormenta
        'tormenta_lluvia_umbral',
        'tormenta_viento_umbral',
        // Estrés Térmico
        'estres_temperatura_umbral',
        'estres_dias_consecutivos',
        // Helada
        'helada_temperatura_umbral',
        // Notificaciones
        'notificaciones_email',
        'notificaciones_sms',
    ];

    protected $casts = [
        'sequia_dias_sin_lluvia' => 'integer',
        'sequia_temperatura_umbral' => 'decimal:1',
        'sequia_dias_consecutivos' => 'integer',
        'tormenta_lluvia_umbral' => 'decimal:1',
        'tormenta_viento_umbral' => 'decimal:1',
        'estres_temperatura_umbral' => 'decimal:1',
        'estres_dias_consecutivos' => 'integer',
        'helada_temperatura_umbral' => 'decimal:1',
        'notificaciones_email' => 'boolean',
        'notificaciones_sms' => 'boolean',
    ];

    // Relaciones
    public function productor(): BelongsTo
    {
        return $this->belongsTo(Productor::class);
    }

    // Métodos helper
    public static function obtenerOCrearParaProductor($productorId): self
    {
        return static::firstOrCreate(
            ['productor_id' => $productorId],
            static::valoresPredeterminados()
        );
    }

    public static function valoresPredeterminados(): array
    {
        return [
            'sequia_dias_sin_lluvia' => 15,
            'sequia_temperatura_umbral' => 32.0,
            'sequia_dias_consecutivos' => 5,
            'tormenta_lluvia_umbral' => 50.0,
            'tormenta_viento_umbral' => 60.0,
            'estres_temperatura_umbral' => 35.0,
            'estres_dias_consecutivos' => 3,
            'helada_temperatura_umbral' => 5.0,
            'notificaciones_email' => true,
            'notificaciones_sms' => false,
        ];
    }

    public function restablecerPredeterminados(): void
    {
        $this->update(static::valoresPredeterminados());
    }

    // Validación de rangos
    public function validarRangos(): array
    {
        $errores = [];

        if ($this->sequia_dias_sin_lluvia < 5 || $this->sequia_dias_sin_lluvia > 60) {
            $errores[] = 'Días sin lluvia debe estar entre 5 y 60';
        }

        if ($this->sequia_temperatura_umbral < 25 || $this->sequia_temperatura_umbral > 45) {
            $errores[] = 'Temperatura de sequía debe estar entre 25°C y 45°C';
        }

        if ($this->tormenta_lluvia_umbral < 20 || $this->tormenta_lluvia_umbral > 200) {
            $errores[] = 'Lluvia de tormenta debe estar entre 20mm y 200mm';
        }

        if ($this->tormenta_viento_umbral < 30 || $this->tormenta_viento_umbral > 120) {
            $errores[] = 'Viento de tormenta debe estar entre 30km/h y 120km/h';
        }

        if ($this->estres_temperatura_umbral < 30 || $this->estres_temperatura_umbral > 50) {
            $errores[] = 'Temperatura de estrés debe estar entre 30°C y 50°C';
        }

        if ($this->helada_temperatura_umbral < 0 || $this->helada_temperatura_umbral > 15) {
            $errores[] = 'Temperatura de helada debe estar entre 0°C y 15°C';
        }

        return $errores;
    }
}
```

---

### 1.3. Componente Livewire: `ConfiguracionAlertas`

**Archivo:** `app/Livewire/Productor/ConfiguracionAlertas.php`

```php
<?php

namespace App\Livewire\Productor;

use App\Models\ConfiguracionAlerta;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConfiguracionAlertas extends Component
{
    public ConfiguracionAlerta $configuracion;
    
    // Sequía
    public $sequia_dias_sin_lluvia;
    public $sequia_temperatura_umbral;
    public $sequia_dias_consecutivos;
    
    // Tormenta
    public $tormenta_lluvia_umbral;
    public $tormenta_viento_umbral;
    
    // Estrés Térmico
    public $estres_temperatura_umbral;
    public $estres_dias_consecutivos;
    
    // Helada
    public $helada_temperatura_umbral;
    
    // Notificaciones
    public $notificaciones_email;
    public $notificaciones_sms;

    public $mensajeExito = '';
    public $mensajeError = '';

    public function mount()
    {
        $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();
        $this->configuracion = ConfiguracionAlerta::obtenerOCrearParaProductor($productor->id);
        
        $this->cargarValores();
    }

    protected function cargarValores()
    {
        // Sequía
        $this->sequia_dias_sin_lluvia = $this->configuracion->sequia_dias_sin_lluvia;
        $this->sequia_temperatura_umbral = $this->configuracion->sequia_temperatura_umbral;
        $this->sequia_dias_consecutivos = $this->configuracion->sequia_dias_consecutivos;
        
        // Tormenta
        $this->tormenta_lluvia_umbral = $this->configuracion->tormenta_lluvia_umbral;
        $this->tormenta_viento_umbral = $this->configuracion->tormenta_viento_umbral;
        
        // Estrés Térmico
        $this->estres_temperatura_umbral = $this->configuracion->estres_temperatura_umbral;
        $this->estres_dias_consecutivos = $this->configuracion->estres_dias_consecutivos;
        
        // Helada
        $this->helada_temperatura_umbral = $this->configuracion->helada_temperatura_umbral;
        
        // Notificaciones
        $this->notificaciones_email = $this->configuracion->notificaciones_email;
        $this->notificaciones_sms = $this->configuracion->notificaciones_sms;
    }

    public function guardar()
    {
        $this->mensajeExito = '';
        $this->mensajeError = '';

        // Validar
        $this->validate([
            'sequia_dias_sin_lluvia' => 'required|integer|min:5|max:60',
            'sequia_temperatura_umbral' => 'required|numeric|min:25|max:45',
            'sequia_dias_consecutivos' => 'required|integer|min:1|max:30',
            'tormenta_lluvia_umbral' => 'required|numeric|min:20|max:200',
            'tormenta_viento_umbral' => 'required|numeric|min:30|max:120',
            'estres_temperatura_umbral' => 'required|numeric|min:30|max:50',
            'estres_dias_consecutivos' => 'required|integer|min:1|max:15',
            'helada_temperatura_umbral' => 'required|numeric|min:0|max:15',
        ], [
            '*.required' => 'Este campo es obligatorio',
            '*.integer' => 'Debe ser un número entero',
            '*.numeric' => 'Debe ser un número',
            '*.min' => 'El valor es demasiado bajo',
            '*.max' => 'El valor es demasiado alto',
        ]);

        try {
            $this->configuracion->update([
                'sequia_dias_sin_lluvia' => $this->sequia_dias_sin_lluvia,
                'sequia_temperatura_umbral' => $this->sequia_temperatura_umbral,
                'sequia_dias_consecutivos' => $this->sequia_dias_consecutivos,
                'tormenta_lluvia_umbral' => $this->tormenta_lluvia_umbral,
                'tormenta_viento_umbral' => $this->tormenta_viento_umbral,
                'estres_temperatura_umbral' => $this->estres_temperatura_umbral,
                'estres_dias_consecutivos' => $this->estres_dias_consecutivos,
                'helada_temperatura_umbral' => $this->helada_temperatura_umbral,
                'notificaciones_email' => $this->notificaciones_email,
                'notificaciones_sms' => $this->notificaciones_sms,
            ]);

            $this->mensajeExito = '✅ Configuración guardada correctamente';
        } catch (\Exception $e) {
            $this->mensajeError = '❌ Error al guardar: ' . $e->getMessage();
        }
    }

    public function restablecer()
    {
        $this->configuracion->restablecerPredeterminados();
        $this->cargarValores();
        $this->mensajeExito = '✅ Valores predeterminados restablecidos';
    }

    public function render()
    {
        return view('livewire.productor.configuracion-alertas');
    }
}
```

---

### 1.4. Vista: `configuracion-alertas.blade.php`

**Archivo:** `resources/views/livewire/productor/configuracion-alertas.blade.php`

```blade
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">⚙️ Configuración de Alertas</h2>
                <p class="text-sm text-gray-500 mt-1">Personaliza los umbrales para la detección de alertas ambientales</p>
            </div>
        </div>

        {{-- Mensajes --}}
        @if($mensajeExito)
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ $mensajeExito }}
            </div>
        @endif

        @if($mensajeError)
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $mensajeError }}
            </div>
        @endif

        <form wire:submit.prevent="guardar">
            {{-- Sequía --}}
            <div class="mb-8 p-4 bg-red-50 rounded-lg border border-red-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔴</span> Sequía
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Días sin lluvia
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                wire:model="sequia_dias_sin_lluvia"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                min="5" 
                                max="60"
                            >
                            <span class="ml-2 text-sm text-gray-500">días</span>
                        </div>
                        @error('sequia_dias_sin_lluvia') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Temperatura umbral
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                step="0.1"
                                wire:model="sequia_temperatura_umbral"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                min="25" 
                                max="45"
                            >
                            <span class="ml-2 text-sm text-gray-500">°C</span>
                        </div>
                        @error('sequia_temperatura_umbral') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Días consecutivos
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                wire:model="sequia_dias_consecutivos"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                min="1" 
                                max="30"
                            >
                            <span class="ml-2 text-sm text-gray-500">días</span>
                        </div>
                        @error('sequia_dias_consecutivos') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Tormenta --}}
            <div class="mb-8 p-4 bg-orange-50 rounded-lg border border-orange-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">⛈️</span> Tormenta
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lluvia esperada
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                step="0.1"
                                wire:model="tormenta_lluvia_umbral"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                min="20" 
                                max="200"
                            >
                            <span class="ml-2 text-sm text-gray-500">mm</span>
                        </div>
                        @error('tormenta_lluvia_umbral') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Viento umbral
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                step="0.1"
                                wire:model="tormenta_viento_umbral"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                                min="30" 
                                max="120"
                            >
                            <span class="ml-2 text-sm text-gray-500">km/h</span>
                        </div>
                        @error('tormenta_viento_umbral') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Estrés Térmico --}}
            <div class="mb-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">🌡️</span> Estrés Térmico
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Temperatura máxima
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                step="0.1"
                                wire:model="estres_temperatura_umbral"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                min="30" 
                                max="50"
                            >
                            <span class="ml-2 text-sm text-gray-500">°C</span>
                        </div>
                        @error('estres_temperatura_umbral') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Días consecutivos
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                wire:model="estres_dias_consecutivos"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                min="1" 
                                max="15"
                            >
                            <span class="ml-2 text-sm text-gray-500">días</span>
                        </div>
                        @error('estres_dias_consecutivos') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Helada --}}
            <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">❄️</span> Helada
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Temperatura mínima
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="number" 
                                step="0.1"
                                wire:model="helada_temperatura_umbral"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" 
                                max="15"
                            >
                            <span class="ml-2 text-sm text-gray-500">°C</span>
                        </div>
                        @error('helada_temperatura_umbral') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Notificaciones --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔔</span> Notificaciones
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="notificaciones_email"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Recibir notificaciones por email</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="notificaciones_sms"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Recibir notificaciones por SMS</span>
                    </label>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-between pt-4 border-t">
                <button 
                    type="button"
                    wire:click="restablecer"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                >
                    🔄 Restablecer Predeterminados
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium"
                >
                    💾 Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
```

---

## 📈 PARTE 2: GRÁFICOS HISTÓRICOS

### Resultado Visual

```
┌──────────────────────────────────────────────────────────┐
│  📊 Historial de Alertas - Últimos 30 días              │
├──────────────────────────────────────────────────────────┤
│                                                           │
│  [30 días] [60 días] [90 días] [Todo]                   │
│                                                           │
│  ┌─────────────────────────────────────────────────┐    │
│  │        Alertas por Fecha (Línea Temporal)       │    │
│  │  20 ┤                                            │    │
│  │  15 ┤        ╭─╮                                 │    │
│  │  10 ┤    ╭───╯ ╰─╮                              │    │
│  │   5 ┤ ╭──╯       ╰───╮                          │    │
│  │   0 ┼────────────────────────────────────────   │    │
│  │      1  5  10 15 20 25 30                       │    │
│  └─────────────────────────────────────────────────┘    │
│                                                           │
│  ┌──────────────────┐  ┌──────────────────────────┐    │
│  │  Por Tipo (Dona) │  │  Por Mes (Barras)        │    │
│  │      🔴 50%      │  │  Ene ████████            │    │
│  │      ⛈️ 30%     │  │  Feb ██████              │    │
│  │      🌡️ 15%     │  │  Mar ████                │    │
│  │      ❄️ 5%      │  │                           │    │
│  └──────────────────┘  └──────────────────────────┘    │
│                                                           │
│  📋 Resumen:                                             │
│  • Total alertas: 45                                     │
│  • Más frecuente: Sequía (23)                           │
│  • Mes con más alertas: Enero (18)                      │
└──────────────────────────────────────────────────────────┘
```

---

### 2.1. Componente Livewire: `HistorialAlertas`

**Archivo:** `app/Livewire/Productor/HistorialAlertas.php`

```php
<?php

namespace App\Livewire\Productor;

use App\Models\AlertaAmbiental;
use App\Models\Productor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HistorialAlertas extends Component
{
    public $periodo = 30; // días por defecto
    public $datosGraficoLinea = [];
    public $datosGraficoDona = [];
    public $datosGraficoBarras = [];
    public $estadisticas = [];

    public function mount()
    {
        $this->cargarDatos();
    }

    public function updatedPeriodo()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        $fechaInicio = Carbon::now()->subDays($this->periodo);

        // Obtener todas las alertas del periodo
        $alertas = AlertaAmbiental::whereHas('unidadProductiva', function ($query) use ($productor) {
                $query->where('productor_id', $productor->id);
            })
            ->where('created_at', '>=', $fechaInicio)
            ->orderBy('created_at')
            ->get();

        // Preparar datos para gráfico de línea (alertas por día)
        $this->datosGraficoLinea = $this->prepararDatosLinea($alertas, $fechaInicio);

        // Preparar datos para gráfico de dona (alertas por tipo)
        $this->datosGraficoDona = $this->prepararDatosDona($alertas);

        // Preparar datos para gráfico de barras (alertas por mes)
        $this->datosGraficoBarras = $this->prepararDatosBarras($alertas);

        // Calcular estadísticas
        $this->estadisticas = $this->calcularEstadisticas($alertas);
    }

    protected function prepararDatosLinea($alertas, $fechaInicio)
    {
        $datos = [];
        $labels = [];

        // Agrupar por día
        $alertasPorDia = $alertas->groupBy(function ($alerta) {
            return $alerta->created_at->format('Y-m-d');
        });

        // Generar array de días
        $fecha = $fechaInicio->copy();
        while ($fecha <= Carbon::now()) {
            $fechaStr = $fecha->format('Y-m-d');
            $labels[] = $fecha->format('d/m');
            $datos[] = $alertasPorDia->get($fechaStr, collect())->count();
            $fecha->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $datos,
        ];
    }

    protected function prepararDatosDona($alertas)
    {
        $tipos = $alertas->groupBy('tipo')->map->count();

        $emojis = [
            'sequia' => '🔴',
            'tormenta' => '⛈️',
            'estres_termico' => '🌡️',
            'helada' => '❄️',
        ];

        $colores = [
            'sequia' => '#EF4444',
            'tormenta' => '#F97316',
            'estres_termico' => '#EAB308',
            'helada' => '#3B82F6',
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];

        foreach ($tipos as $tipo => $cantidad) {
            $labels[] = ($emojis[$tipo] ?? '') . ' ' . ucfirst(str_replace('_', ' ', $tipo));
            $data[] = $cantidad;
            $backgroundColor[] = $colores[$tipo] ?? '#6B7280';
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColor,
        ];
    }

    protected function prepararDatosBarras($alertas)
    {
        // Agrupar por mes
        $alertasPorMes = $alertas->groupBy(function ($alerta) {
            return $alerta->created_at->format('Y-m');
        });

        $labels = [];
        $data = [];

        foreach ($alertasPorMes as $mes => $alertasMes) {
            $labels[] = Carbon::createFromFormat('Y-m', $mes)->locale('es')->isoFormat('MMM YYYY');
            $data[] = $alertasMes->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function calcularEstadisticas($alertas)
    {
        $total = $alertas->count();
        $tipos = $alertas->groupBy('tipo')->map->count();
        $tipoMasFrecuente = $tipos->sortDesc()->keys()->first();

        $alertasPorMes = $alertas->groupBy(function ($alerta) {
            return $alerta->created_at->format('Y-m');
        });
        $mesMasAlertas = $alertasPorMes->sortByDesc(function ($alertas) {
            return $alertas->count();
        })->keys()->first();

        $emojis = [
            'sequia' => '🔴 Sequía',
            'tormenta' => '⛈️ Tormenta',
            'estres_termico' => '🌡️ Estrés Térmico',
            'helada' => '❄️ Helada',
        ];

        return [
            'total' => $total,
            'tipo_mas_frecuente' => $emojis[$tipoMasFrecuente] ?? 'N/A',
            'cantidad_mas_frecuente' => $tipos[$tipoMasFrecuente] ?? 0,
            'mes_mas_alertas' => $mesMasAlertas ? Carbon::createFromFormat('Y-m', $mesMasAlertas)->locale('es')->isoFormat('MMMM YYYY') : 'N/A',
            'cantidad_mes' => $mesMasAlertas ? $alertasPorMes[$mesMasAlertas]->count() : 0,
        ];
    }

    public function render()
    {
        return view('livewire.productor.historial-alertas');
    }
}
```

---

### 2.2. Vista: `historial-alertas.blade.php`

**Archivo:** `resources/views/livewire/productor/historial-alertas.blade.php`

```blade
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">📊 Historial de Alertas</h2>
                <p class="text-sm text-gray-500 mt-1">Análisis temporal de alertas ambientales</p>
            </div>
            
            {{-- Selector de periodo --}}
            <div class="flex gap-2">
                <button 
                    wire:click="$set('periodo', 30)"
                    class="px-4 py-2 rounded-md {{ $periodo == 30 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors"
                >
                    30 días
                </button>
                <button 
                    wire:click="$set('periodo', 60)"
                    class="px-4 py-2 rounded-md {{ $periodo == 60 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors"
                >
                    60 días
                </button>
                <button 
                    wire:click="$set('periodo', 90)"
                    class="px-4 py-2 rounded-md {{ $periodo == 90 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors"
                >
                    90 días
                </button>
            </div>
        </div>

        @if($estadisticas['total'] > 0)
            {{-- Estadísticas resumen --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600">Total de alertas</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $estadisticas['total'] }}</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-sm text-gray-600">Más frecuente</p>
                    <p class="text-xl font-bold text-purple-600">{{ $estadisticas['tipo_mas_frecuente'] }}</p>
                    <p class="text-sm text-gray-500">{{ $estadisticas['cantidad_mas_frecuente'] }} alertas</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm text-gray-600">Mes con más alertas</p>
                    <p class="text-lg font-bold text-green-600">{{ $estadisticas['mes_mas_alertas'] }}</p>
                    <p class="text-sm text-gray-500">{{ $estadisticas['cantidad_mes'] }} alertas</p>
                </div>
            </div>

            {{-- Gráfico de línea --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Alertas por Día</h3>
                <div class="h-64">
                    <canvas id="graficoLinea"></canvas>
                </div>
            </div>

            {{-- Gráficos dona y barras --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Gráfico dona --}}
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🍩 Distribución por Tipo</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="graficoDona"></canvas>
                    </div>
                </div>

                {{-- Gráfico barras --}}
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Alertas por Mes</h3>
                    <div class="h-64">
                        <canvas id="graficoBarras"></canvas>
                    </div>
                </div>
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="py-12 text-center">
                <div class="text-6xl mb-4">📊</div>
                <p class="text-xl text-gray-600 mb-2">No hay alertas en este periodo</p>
                <p class="text-sm text-gray-500">Intenta seleccionar un periodo más amplio</p>
            </div>
        @endif
    </div>

    @if($estadisticas['total'] > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                // Gráfico de línea
                const ctxLinea = document.getElementById('graficoLinea');
                if (ctxLinea) {
                    new Chart(ctxLinea, {
                        type: 'line',
                        data: {
                            labels: @js($datosGraficoLinea['labels'] ?? []),
                            datasets: [{
                                label: 'Alertas',
                                data: @js($datosGraficoLinea['data'] ?? []),
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }

                // Gráfico dona
                const ctxDona = document.getElementById('graficoDona');
                if (ctxDona) {
                    new Chart(ctxDona, {
                        type: 'doughnut',
                        data: {
                            labels: @js($datosGraficoDona['labels'] ?? []),
                            datasets: [{
                                data: @js($datosGraficoDona['data'] ?? []),
                                backgroundColor: @js($datosGraficoDona['backgroundColor'] ?? []),
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // Gráfico barras
                const ctxBarras = document.getElementById('graficoBarras');
                if (ctxBarras) {
                    new Chart(ctxBarras, {
                        type: 'bar',
                        data: {
                            labels: @js($datosGraficoBarras['labels'] ?? []),
                            datasets: [{
                                label: 'Alertas',
                                data: @js($datosGraficoBarras['data'] ?? []),
                                backgroundColor: '#10B981',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            });

            // Recargar gráficos cuando cambia el periodo
            Livewire.on('periodo-changed', () => {
                location.reload();
            });
        </script>
        @endpush
    @endif
</div>
```

---

## 🔗 PARTE 3: INTEGRACIÓN UI

### 3.1. Agregar al menú de navegación

**Archivo:** `resources/views/components/panel-layout.blade.php`

Agregar botones en el sidebar o header:

```blade
{{-- En el menú del productor --}}
<nav class="space-y-2">
    {{-- ... menús existentes ... --}}
    
    <a href="{{ route('productor.alertas.configuracion') }}" 
       class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
        <span class="text-xl mr-3">⚙️</span>
        <span>Configuración de Alertas</span>
    </a>
    
    <a href="{{ route('productor.alertas.historial') }}" 
       class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
        <span class="text-xl mr-3">📊</span>
        <span>Historial de Alertas</span>
    </a>
</nav>
```

### 3.2. Rutas

**Archivo:** `routes/web.php`

```php
// Rutas de alertas ambientales (requiere autenticación de productor)
Route::middleware(['auth', 'verified'])->prefix('productor')->name('productor.')->group(function () {
    // ... rutas existentes ...
    
    // Alertas ambientales
    Route::get('/alertas/configuracion', \App\Livewire\Productor\ConfiguracionAlertas::class)
        ->name('alertas.configuracion');
    
    Route::get('/alertas/historial', \App\Livewire\Productor\HistorialAlertas::class)
        ->name('alertas.historial');
});
```

---

## 🧪 TESTING

### Test: `ConfiguracionAlertasTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\ConfiguracionAlerta;
use App\Models\Productor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfiguracionAlertasTest extends TestCase
{
    use RefreshDatabase;

    public function test_productor_puede_ver_configuracion()
    {
        $user = User::factory()->create();
        $productor = Productor::factory()->create(['usuario_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('productor.alertas.configuracion'))
            ->assertStatus(200);
    }

    public function test_configuracion_se_crea_con_valores_predeterminados()
    {
        $user = User::factory()->create();
        $productor = Productor::factory()->create(['usuario_id' => $user->id]);

        $config = ConfiguracionAlerta::obtenerOCrearParaProductor($productor->id);

        $this->assertEquals(15, $config->sequia_dias_sin_lluvia);
        $this->assertEquals(32.0, $config->sequia_temperatura_umbral);
        $this->assertEquals(50.0, $config->tormenta_lluvia_umbral);
    }

    public function test_productor_puede_actualizar_configuracion()
    {
        $user = User::factory()->create();
        $productor = Productor::factory()->create(['usuario_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Productor\ConfiguracionAlertas::class)
            ->set('sequia_dias_sin_lluvia', 20)
            ->set('tormenta_lluvia_umbral', 60)
            ->call('guardar')
            ->assertSee('guardada correctamente');

        $config = ConfiguracionAlerta::where('productor_id', $productor->id)->first();
        $this->assertEquals(20, $config->sequia_dias_sin_lluvia);
        $this->assertEquals(60, $config->tormenta_lluvia_umbral);
    }

    public function test_validacion_rechaza_valores_fuera_de_rango()
    {
        $user = User::factory()->create();
        Productor::factory()->create(['usuario_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Productor\ConfiguracionAlertas::class)
            ->set('sequia_dias_sin_lluvia', 100) // Fuera de rango (max 60)
            ->call('guardar')
            ->assertHasErrors(['sequia_dias_sin_lluvia']);
    }
}
```

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Backend
- [ ] Crear migración `configuracion_alertas`
- [ ] Ejecutar migración
- [ ] Crear modelo `ConfiguracionAlerta`
- [ ] Agregar relación en modelo `Productor`
- [ ] Crear componente `ConfiguracionAlertas`
- [ ] Crear componente `HistorialAlertas`

### Frontend
- [ ] Crear vista `configuracion-alertas.blade.php`
- [ ] Crear vista `historial-alertas.blade.php`
- [ ] Agregar menús de navegación
- [ ] Integrar Chart.js
- [ ] Probar responsive

### Rutas y Testing
- [ ] Agregar rutas en `web.php`
- [ ] Crear tests Feature
- [ ] Probar en navegador
- [ ] Validar en diferentes dispositivos

---

## ⏱️ CRONOGRAMA

### Día 1 (3-4 horas)
- ✅ Migración y modelo `ConfiguracionAlerta`
- ✅ Componente Livewire `ConfiguracionAlertas`
- ✅ Vista del panel de configuración
- ✅ Pruebas básicas

### Día 2 (3-4 horas)
- ✅ Componente `HistorialAlertas`
- ✅ Integración Chart.js
- ✅ Queries optimizadas
- ✅ Vista de gráficos

### Día 3 (2 horas)
- ✅ Integración en menús
- ✅ Testing completo
- ✅ Ajustes finales
- ✅ Commit y documentación

---

## 🚀 PRÓXIMOS PASOS

**¿Quieres que empiece con la implementación?**

Te propongo:

1. **Empezar ahora** con la migración y el modelo
2. **Ver un prototipo** antes de implementar
3. **Ajustar el diseño** de la UI primero
4. **Otra cosa** que prefieras

**¿Qué prefieres hacer?** 😊


