<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DatoSatelital extends Model
{
    protected $table = 'datos_satelitales';

    protected $fillable = [
        'unidad_productiva_id',
        'satelite',
        'fecha_imagen',
        'producto_id',
        'ndvi_promedio',
        'ndvi_maximo',
        'ndvi_minimo',
        'ndvi_desviacion',
        'ndwi',
        'gci',
        'lai',
        'resolucion_metros',
        'cobertura_nubes',
        'calidad_imagen',
        'latitud_centro',
        'longitud_centro',
        'area_hectareas',
        'estado_vegetacion',
        'distribucion_ndvi',
        'alertas_vegetacion',
        'url_imagen_rgb',
        'url_imagen_ndvi',
        'url_metadata',
        'datos_validos',
        'notas',
    ];

    protected $casts = [
        'fecha_imagen' => 'date',
        'ndvi_promedio' => 'decimal:3',
        'ndvi_maximo' => 'decimal:3',
        'ndvi_minimo' => 'decimal:3',
        'ndvi_desviacion' => 'decimal:3',
        'ndwi' => 'decimal:3',
        'gci' => 'decimal:3',
        'lai' => 'decimal:2',
        'cobertura_nubes' => 'decimal:2',
        'latitud_centro' => 'decimal:8',
        'longitud_centro' => 'decimal:8',
        'area_hectareas' => 'decimal:2',
        'distribucion_ndvi' => 'array',
        'alertas_vegetacion' => 'array',
        'datos_validos' => 'boolean',
    ];

    // Relaciones
    public function unidadProductiva(): BelongsTo
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    // Scopes
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_imagen', '>=', Carbon::now()->subDays($dias));
    }

    public function scopePorSatelite($query, $satelite)
    {
        return $query->where('satelite', $satelite);
    }

    public function scopeDatosValidos($query)
    {
        return $query->where('datos_validos', true);
    }

    public function scopePorEstadoVegetacion($query, $estado)
    {
        return $query->where('estado_vegetacion', $estado);
    }

    // Métodos helper
    public function obtenerDescripcionNDVI(): string
    {
        if (!$this->ndvi_promedio) {
            return 'Sin datos';
        }

        return match(true) {
            $this->ndvi_promedio >= 0.8 => 'Vegetación muy densa',
            $this->ndvi_promedio >= 0.6 => 'Vegetación densa',
            $this->ndvi_promedio >= 0.4 => 'Vegetación moderada',
            $this->ndvi_promedio >= 0.2 => 'Vegetación escasa',
            default => 'Suelo desnudo/agua'
        };
    }

    public function obtenerColorNDVI(): string
    {
        if (!$this->ndvi_promedio) {
            return 'gray';
        }

        return match(true) {
            $this->ndvi_promedio >= 0.7 => 'green',
            $this->ndvi_promedio >= 0.5 => 'yellow',
            $this->ndvi_promedio >= 0.3 => 'orange',
            default => 'red'
        };
    }

    public function obtenerEmojiNDVI(): string
    {
        if (!$this->ndvi_promedio) {
            return '❓';
        }

        return match(true) {
            $this->ndvi_promedio >= 0.7 => '🟢',
            $this->ndvi_promedio >= 0.5 => '🟡',
            $this->ndvi_promedio >= 0.3 => '🟠',
            default => '🔴'
        };
    }

    public function obtenerEstadoVegetacion(): string
    {
        return match($this->estado_vegetacion) {
            'excellent' => 'Excelente',
            'good' => 'Bueno',
            'fair' => 'Regular',
            'poor' => 'Pobre',
            default => 'Sin clasificar'
        };
    }

    public function obtenerRecomendaciones(): array
    {
        if (!$this->ndvi_promedio) {
            return ['No hay datos suficientes para generar recomendaciones'];
        }

        $recomendaciones = [];

        if ($this->ndvi_promedio < 0.3) {
            $recomendaciones[] = 'Vegetación muy deteriorada - considerar rotación inmediata';
            $recomendaciones[] = 'Evaluar fertilización y riego';
            $recomendaciones[] = 'Revisar carga animal (posible sobrepastoreo)';
        } elseif ($this->ndvi_promedio < 0.5) {
            $recomendaciones[] = 'Vegetación en deterioro - monitorear de cerca';
            $recomendaciones[] = 'Considerar reducir carga animal temporalmente';
            $recomendaciones[] = 'Evaluar necesidad de fertilización';
        } elseif ($this->ndvi_promedio >= 0.7) {
            $recomendaciones[] = 'Excelente estado de vegetación';
            $recomendaciones[] = 'Puede soportar mayor carga animal';
            $recomendaciones[] = 'Mantener prácticas actuales';
        } else {
            $recomendaciones[] = 'Estado vegetativo aceptable';
            $recomendaciones[] = 'Continuar monitoreo regular';
        }

        // Recomendaciones basadas en cobertura de nubes
        if ($this->cobertura_nubes > 30) {
            $recomendaciones[] = '⚠️ Alta cobertura de nubes - datos pueden ser menos precisos';
        }

        return $recomendaciones;
    }

    public function esDatosRecientes(): bool
    {
        return $this->fecha_imagen >= Carbon::now()->subDays(14); // 2 semanas
    }

    public function necesitaActualizacion(): bool
    {
        return $this->fecha_imagen < Carbon::now()->subDays(7); // 1 semana
    }

    // Métodos estáticos
    public static function obtenerUltimoParaUnidad($unidadProductivaId): ?self
    {
        return static::where('unidad_productiva_id', $unidadProductivaId)
            ->datosValidos()
            ->orderBy('fecha_imagen', 'desc')
            ->first();
    }

    public static function obtenerPromedioNDVI($unidadProductivaId, $dias = 30): ?float
    {
        return static::where('unidad_productiva_id', $unidadProductivaId)
            ->datosValidos()
            ->recientes($dias)
            ->avg('ndvi_promedio');
    }

    public static function obtenerTendenciaNDVI($unidadProductivaId, $dias = 30): string
    {
        $datos = static::where('unidad_productiva_id', $unidadProductivaId)
            ->datosValidos()
            ->recientes($dias)
            ->orderBy('fecha_imagen', 'asc')
            ->get(['ndvi_promedio']);

        if ($datos->count() < 2) {
            return 'insuficiente';
        }

        $primero = $datos->first()->ndvi_promedio;
        $ultimo = $datos->last()->ndvi_promedio;
        $diferencia = $ultimo - $primero;

        if ($diferencia > 0.05) {
            return 'mejorando';
        } elseif ($diferencia < -0.05) {
            return 'deteriorando';
        } else {
            return 'estable';
        }
    }
}