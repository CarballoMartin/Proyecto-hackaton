<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaAmbiental extends Model
{
    use HasFactory;
    
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
