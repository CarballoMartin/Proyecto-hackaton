<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class IndiceVegetacion extends Model
{
    use HasFactory;

    protected $table = 'indices_vegetacion';

    protected $fillable = [
        'unidad_productiva_id',
        'ndvi',
        'evi',
        'evi2',
        'clasificacion',
        'fecha_imagen',
        'satelite',
        'nubosidad_porcentaje',
        'latitud',
        'longitud',
        'datos_completos',
    ];

    protected $casts = [
        'fecha_imagen' => 'date',
        'ndvi' => 'decimal:3',
        'evi' => 'decimal:3',
        'evi2' => 'decimal:3',
        'nubosidad_porcentaje' => 'decimal:2',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'datos_completos' => 'array',
    ];

    /**
     * Relación con UnidadProductiva
     */
    public function unidadProductiva(): BelongsTo
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    /**
     * Scope para obtener datos por clasificación
     */
    public function scopePorClasificacion($query, string $clasificacion)
    {
        return $query->where('clasificacion', $clasificacion);
    }

    /**
     * Scope para obtener datos por rango de fechas
     */
    public function scopePorRangoFechas($query, Carbon $fechaInicio, Carbon $fechaFin)
    {
        return $query->whereBetween('fecha_imagen', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para obtener datos recientes
     */
    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('fecha_imagen', '>=', Carbon::now()->subDays($dias));
    }

    /**
     * Scope para obtener datos con baja nubosidad
     */
    public function scopeBajaNubosidad($query, float $maxNubosidad = 20.0)
    {
        return $query->where('nubosidad_porcentaje', '<=', $maxNubosidad);
    }

    /**
     * Obtiene la clasificación del NDVI
     */
    public static function clasificarNdvi(float $ndvi): string
    {
        if ($ndvi < 0.2) {
            return 'baja';
        } elseif ($ndvi < 0.4) {
            return 'media';
        } else {
            return 'alta';
        }
    }

    /**
     * Obtiene el color asociado a la clasificación
     */
    public function getColorClasificacionAttribute(): string
    {
        return match($this->clasificacion) {
            'baja' => 'red',
            'media' => 'yellow',
            'alta' => 'green',
            default => 'gray'
        };
    }

    /**
     * Obtiene la descripción de la clasificación
     */
    public function getDescripcionClasificacionAttribute(): string
    {
        return match($this->clasificacion) {
            'baja' => 'Vegetación escasa o suelo desnudo',
            'media' => 'Vegetación moderada',
            'alta' => 'Vegetación densa y saludable',
            default => 'Sin clasificación'
        };
    }

    /**
     * Obtiene el estado de salud de la vegetación
     */
    public function getEstadoSaludAttribute(): string
    {
        if ($this->ndvi >= 0.6) {
            return 'Excelente';
        } elseif ($this->ndvi >= 0.4) {
            return 'Buena';
        } elseif ($this->ndvi >= 0.2) {
            return 'Regular';
        } else {
            return 'Crítica';
        }
    }

    /**
     * Verifica si el dato es confiable (baja nubosidad)
     */
    public function getEsConfiableAttribute(): bool
    {
        return $this->nubosidad_porcentaje <= 20.0;
    }

    /**
     * Obtiene el promedio de NDVI para una unidad productiva en un período
     */
    public static function promedioNdviUnidad(int $unidadProductivaId, Carbon $fechaInicio, Carbon $fechaFin): ?float
    {
        return static::where('unidad_productiva_id', $unidadProductivaId)
            ->porRangoFechas($fechaInicio, $fechaFin)
            ->bajaNubosidad()
            ->avg('ndvi');
    }

    /**
     * Obtiene la tendencia del NDVI para una unidad productiva
     */
    public static function tendenciaNdviUnidad(int $unidadProductivaId, int $dias = 90): array
    {
        $datos = static::where('unidad_productiva_id', $unidadProductivaId)
            ->recientes($dias)
            ->bajaNubosidad()
            ->orderBy('fecha_imagen')
            ->get(['fecha_imagen', 'ndvi']);

        if ($datos->count() < 2) {
            return ['tendencia' => 'insuficientes_datos', 'cambio' => 0];
        }

        $primero = $datos->first()->ndvi;
        $ultimo = $datos->last()->ndvi;
        $cambio = $ultimo - $primero;

        $tendencia = match(true) {
            $cambio > 0.1 => 'mejorando',
            $cambio < -0.1 => 'empeorando',
            default => 'estable'
        };

        return [
            'tendencia' => $tendencia,
            'cambio' => round($cambio, 3),
            'datos' => $datos->toArray()
        ];
    }
}
