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
