<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AlertaAmbiental extends Model
{
    use HasFactory;

    protected $table = 'alertas_ambientales';

    protected $fillable = [
        'unidad_productiva_id',
        'tipo_alerta',
        'severidad',
        'titulo',
        'descripcion',
        'datos_alerta',
        'recomendaciones',
        'fecha_inicio',
        'fecha_fin',
        'activa',
        'notificada',
        'fecha_notificacion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_notificacion' => 'datetime',
        'datos_alerta' => 'array',
        'recomendaciones' => 'array',
        'activa' => 'boolean',
        'notificada' => 'boolean',
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
     * Scope para alertas por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
    }

    /**
     * Scope para alertas por severidad
     */
    public function scopePorSeveridad($query, string $severidad)
    {
        return $query->where('severidad', $severidad);
    }

    /**
     * Scope para alertas recientes
     */
    public function scopeRecientes($query, int $dias = 7)
    {
        return $query->where('fecha_inicio', '>=', Carbon::now()->subDays($dias));
    }

    /**
     * Scope para alertas no notificadas
     */
    public function scopeNoNotificadas($query)
    {
        return $query->where('notificada', false);
    }

    /**
     * Obtiene el color asociado a la severidad
     */
    public function getColorSeveridadAttribute(): string
    {
        return match($this->severidad) {
            'critica' => 'red',
            'alta' => 'orange',
            'media' => 'yellow',
            'baja' => 'green',
            default => 'gray'
        };
    }

    /**
     * Obtiene el icono asociado al tipo de alerta
     */
    public function getIconoTipoAttribute(): string
    {
        return match($this->tipo_alerta) {
            'sequia' => '🌵',
            'tormenta' => '⛈️',
            'estres_termico' => '🌡️',
            'helada' => '❄️',
            'viento' => '💨',
            'ndvi_bajo' => '🌱',
            'suelo_degradado' => '🏗️',
            default => '⚠️'
        };
    }

    /**
     * Obtiene la descripción del tipo de alerta
     */
    public function getDescripcionTipoAttribute(): string
    {
        return match($this->tipo_alerta) {
            'sequia' => 'Sequía prolongada',
            'tormenta' => 'Tormenta intensa',
            'estres_termico' => 'Estrés térmico',
            'helada' => 'Helada',
            'viento' => 'Vientos fuertes',
            'ndvi_bajo' => 'Vegetación degradada',
            'suelo_degradado' => 'Degradación del suelo',
            default => 'Alerta ambiental'
        };
    }

    /**
     * Obtiene la duración de la alerta
     */
    public function getDuracionAttribute(): int
    {
        $fin = $this->fecha_fin ?? Carbon::now();
        return $this->fecha_inicio->diffInDays($fin);
    }

    /**
     * Verifica si la alerta es crítica
     */
    public function getEsCriticaAttribute(): bool
    {
        return $this->severidad === 'critica';
    }

    /**
     * Verifica si la alerta es urgente
     */
    public function getEsUrgenteAttribute(): bool
    {
        return in_array($this->severidad, ['critica', 'alta']);
    }

    /**
     * Marca la alerta como notificada
     */
    public function marcarComoNotificada(): void
    {
        $this->update([
            'notificada' => true,
            'fecha_notificacion' => Carbon::now()
        ]);
    }

    /**
     * Desactiva la alerta
     */
    public function desactivar(): void
    {
        $this->update([
            'activa' => false,
            'fecha_fin' => Carbon::now()
        ]);
    }

    /**
     * Obtiene alertas activas para una unidad productiva
     */
    public static function alertasActivasUnidad(int $unidadProductivaId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('unidad_productiva_id', $unidadProductivaId)
            ->activas()
            ->orderBy('severidad', 'desc')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }

    /**
     * Obtiene estadísticas de alertas para una unidad productiva
     */
    public static function estadisticasAlertasUnidad(int $unidadProductivaId, int $dias = 30): array
    {
        $fechaInicio = Carbon::now()->subDays($dias);
        
        $alertas = static::where('unidad_productiva_id', $unidadProductivaId)
            ->where('fecha_inicio', '>=', $fechaInicio)
            ->get();

        $estadisticas = [
            'total' => $alertas->count(),
            'activas' => $alertas->where('activa', true)->count(),
            'por_tipo' => $alertas->groupBy('tipo_alerta')->map->count(),
            'por_severidad' => $alertas->groupBy('severidad')->map->count(),
            'criticas' => $alertas->where('severidad', 'critica')->count(),
            'no_notificadas' => $alertas->where('notificada', false)->count(),
        ];

        return $estadisticas;
    }

    /**
     * Obtiene alertas críticas recientes
     */
    public static function alertasCriticasRecientes(int $dias = 7): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('severidad', 'critica')
            ->where('fecha_inicio', '>=', Carbon::now()->subDays($dias))
            ->activas()
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }
}