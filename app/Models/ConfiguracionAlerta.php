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
