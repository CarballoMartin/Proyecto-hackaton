<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CaracteristicaSuelo extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas_suelo';

    protected $fillable = [
        'unidad_productiva_id',
        'ph_valor',
        'materia_organica_porcentaje',
        'arcilla_porcentaje',
        'limo_porcentaje',
        'arena_porcentaje',
        'capacidad_retencion_agua',
        'nitrogeno_total',
        'fosforo_disponible',
        'potasio_intercambiable',
        'capacidad_intercambio_cationico',
        'saturacion_bases',
        'densidad_aparente',
        'profundidad_cm',
        'textura_clasificacion',
        'fuente_datos',
        'datos_fuente_json',
        'fecha_consulta',
    ];

    protected $casts = [
        'fecha_consulta' => 'date',
        'ph_valor' => 'decimal:2',
        'materia_organica_porcentaje' => 'decimal:2',
        'arcilla_porcentaje' => 'decimal:2',
        'limo_porcentaje' => 'decimal:2',
        'arena_porcentaje' => 'decimal:2',
        'capacidad_retencion_agua' => 'decimal:2',
        'nitrogeno_total' => 'decimal:2',
        'fosforo_disponible' => 'decimal:2',
        'potasio_intercambiable' => 'decimal:2',
        'capacidad_intercambio_cationico' => 'decimal:2',
        'saturacion_bases' => 'decimal:2',
        'densidad_aparente' => 'decimal:2',
        'datos_fuente_json' => 'array',
    ];

    /**
     * Relación con UnidadProductiva
     */
    public function unidadProductiva(): BelongsTo
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    /**
     * Scope para obtener datos por fuente
     */
    public function scopePorFuente($query, string $fuente)
    {
        return $query->where('fuente_datos', $fuente);
    }

    /**
     * Scope para obtener datos por rango de fechas
     */
    public function scopePorRangoFechas($query, Carbon $fechaInicio, Carbon $fechaFin)
    {
        return $query->whereBetween('fecha_consulta', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para obtener datos recientes
     */
    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('fecha_consulta', '>=', Carbon::now()->subDays($dias));
    }

    /**
     * Obtiene la clasificación del pH
     */
    public function getClasificacionPhAttribute(): string
    {
        if ($this->ph_valor < 5.5) {
            return 'Muy ácido';
        } elseif ($this->ph_valor < 6.5) {
            return 'Ligeramente ácido';
        } elseif ($this->ph_valor < 7.5) {
            return 'Neutro';
        } elseif ($this->ph_valor < 8.5) {
            return 'Ligeramente alcalino';
        } else {
            return 'Alcalino';
        }
    }

    /**
     * Obtiene el color asociado al pH
     */
    public function getColorPhAttribute(): string
    {
        if ($this->ph_valor < 5.5) {
            return 'red';
        } elseif ($this->ph_valor < 6.5) {
            return 'orange';
        } elseif ($this->ph_valor < 7.5) {
            return 'green';
        } elseif ($this->ph_valor < 8.5) {
            return 'blue';
        } else {
            return 'purple';
        }
    }

    /**
     * Obtiene la clasificación de la materia orgánica
     */
    public function getClasificacionMateriaOrganicaAttribute(): string
    {
        if ($this->materia_organica_porcentaje < 1.0) {
            return 'Muy baja';
        } elseif ($this->materia_organica_porcentaje < 2.0) {
            return 'Baja';
        } elseif ($this->materia_organica_porcentaje < 3.5) {
            return 'Moderada';
        } elseif ($this->materia_organica_porcentaje < 5.0) {
            return 'Alta';
        } else {
            return 'Muy alta';
        }
    }

    /**
     * Obtiene la clasificación de la textura
     */
    public function getClasificacionTexturaAttribute(): string
    {
        $arcilla = $this->arcilla_porcentaje;
        $limo = $this->limo_porcentaje;
        $arena = $this->arena_porcentaje;

        // Clasificación según el triángulo textural
        if ($arcilla >= 40) {
            return 'Arcilloso';
        } elseif ($arcilla >= 27 && $limo >= 40) {
            return 'Franco arcilloso';
        } elseif ($arcilla >= 27 && $arena >= 45) {
            return 'Franco arcilloso arenoso';
        } elseif ($arcilla >= 20 && $limo >= 40) {
            return 'Franco limoso';
        } elseif ($arcilla >= 20 && $arena >= 45) {
            return 'Franco arenoso';
        } elseif ($limo >= 50) {
            return 'Limoso';
        } elseif ($arena >= 70) {
            return 'Arenoso';
        } else {
            return 'Franco';
        }
    }

    /**
     * Obtiene el estado general del suelo
     */
    public function getEstadoGeneralAttribute(): string
    {
        $puntos = 0;
        
        // Evaluar pH (0-3 puntos)
        if ($this->ph_valor >= 6.0 && $this->ph_valor <= 7.5) {
            $puntos += 3;
        } elseif ($this->ph_valor >= 5.5 && $this->ph_valor <= 8.0) {
            $puntos += 2;
        } elseif ($this->ph_valor >= 5.0 && $this->ph_valor <= 8.5) {
            $puntos += 1;
        }

        // Evaluar materia orgánica (0-3 puntos)
        if ($this->materia_organica_porcentaje >= 3.0) {
            $puntos += 3;
        } elseif ($this->materia_organica_porcentaje >= 2.0) {
            $puntos += 2;
        } elseif ($this->materia_organica_porcentaje >= 1.0) {
            $puntos += 1;
        }

        // Evaluar CIC (0-2 puntos)
        if ($this->capacidad_intercambio_cationico >= 15) {
            $puntos += 2;
        } elseif ($this->capacidad_intercambio_cationico >= 10) {
            $puntos += 1;
        }

        // Evaluar saturación de bases (0-2 puntos)
        if ($this->saturacion_bases >= 60 && $this->saturacion_bases <= 80) {
            $puntos += 2;
        } elseif ($this->saturacion_bases >= 50 && $this->saturacion_bases <= 90) {
            $puntos += 1;
        }

        return match(true) {
            $puntos >= 8 => 'Excelente',
            $puntos >= 6 => 'Bueno',
            $puntos >= 4 => 'Regular',
            $puntos >= 2 => 'Deficiente',
            default => 'Crítico'
        };
    }

    /**
     * Obtiene recomendaciones de mejoramiento
     */
    public function getRecomendacionesAttribute(): array
    {
        $recomendaciones = [];

        // Recomendaciones de pH
        if ($this->ph_valor < 5.5) {
            $recomendaciones[] = [
                'tipo' => 'pH',
                'problema' => 'Suelo muy ácido',
                'solucion' => 'Aplicar cal agrícola (2-4 ton/ha)',
                'prioridad' => 'alta'
            ];
        } elseif ($this->ph_valor > 8.0) {
            $recomendaciones[] = [
                'tipo' => 'pH',
                'problema' => 'Suelo alcalino',
                'solucion' => 'Aplicar azufre elemental o materia orgánica',
                'prioridad' => 'media'
            ];
        }

        // Recomendaciones de materia orgánica
        if ($this->materia_organica_porcentaje < 2.0) {
            $recomendaciones[] = [
                'tipo' => 'materia_organica',
                'problema' => 'Baja materia orgánica',
                'solucion' => 'Incorporar compost, estiércol o abonos verdes',
                'prioridad' => 'alta'
            ];
        }

        // Recomendaciones de textura
        if ($this->arena_porcentaje > 70) {
            $recomendaciones[] = [
                'tipo' => 'textura',
                'problema' => 'Suelo muy arenoso',
                'solucion' => 'Mejorar con materia orgánica y riego frecuente',
                'prioridad' => 'media'
            ];
        } elseif ($this->arcilla_porcentaje > 50) {
            $recomendaciones[] = [
                'tipo' => 'textura',
                'problema' => 'Suelo muy arcilloso',
                'solucion' => 'Mejorar drenaje y agregar arena',
                'prioridad' => 'media'
            ];
        }

        // Recomendaciones de fertilidad
        if ($this->fosforo_disponible < 15) {
            $recomendaciones[] = [
                'tipo' => 'fertilidad',
                'problema' => 'Bajo fósforo disponible',
                'solucion' => 'Aplicar fertilizante fosfatado',
                'prioridad' => 'alta'
            ];
        }

        if ($this->potasio_intercambiable < 0.2) {
            $recomendaciones[] = [
                'tipo' => 'fertilidad',
                'problema' => 'Bajo potasio intercambiable',
                'solucion' => 'Aplicar fertilizante potásico',
                'prioridad' => 'alta'
            ];
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones de pasturas según el suelo
     */
    public function getRecomendacionesPasturasAttribute(): array
    {
        $pasturas = [];

        // Pasturas para suelos ácidos
        if ($this->ph_valor < 6.0) {
            $pasturas[] = [
                'nombre' => 'Trébol blanco',
                'tolerancia_ph' => 'Ácido',
                'descripcion' => 'Tolerante a suelos ácidos, fija nitrógeno'
            ];
            $pasturas[] = [
                'nombre' => 'Lotus corniculatus',
                'tolerancia_ph' => 'Ácido',
                'descripcion' => 'Adaptado a suelos ácidos y pobres'
            ];
        }

        // Pasturas para suelos alcalinos
        if ($this->ph_valor > 8.0) {
            $pasturas[] = [
                'nombre' => 'Alfalfa',
                'tolerancia_ph' => 'Alcalino',
                'descripcion' => 'Tolerante a suelos alcalinos, alta proteína'
            ];
            $pasturas[] = [
                'nombre' => 'Festuca alta',
                'tolerancia_ph' => 'Alcalino',
                'descripcion' => 'Resistente a suelos alcalinos'
            ];
        }

        // Pasturas para suelos arenosos
        if ($this->arena_porcentaje > 70) {
            $pasturas[] = [
                'nombre' => 'Cynodon dactylon',
                'tolerancia_textura' => 'Arenoso',
                'descripcion' => 'Resistente a sequía, ideal para suelos arenosos'
            ];
            $pasturas[] = [
                'nombre' => 'Panicum maximum',
                'tolerancia_textura' => 'Arenoso',
                'descripcion' => 'Alta producción en suelos arenosos'
            ];
        }

        // Pasturas para suelos arcillosos
        if ($this->arcilla_porcentaje > 50) {
            $pasturas[] = [
                'nombre' => 'Ryegrass perenne',
                'tolerancia_textura' => 'Arcilloso',
                'descripcion' => 'Adaptado a suelos pesados'
            ];
            $pasturas[] = [
                'nombre' => 'Dactylis glomerata',
                'tolerancia_textura' => 'Arcilloso',
                'descripcion' => 'Tolerante a suelos húmedos'
            ];
        }

        // Pasturas generales
        $pasturas[] = [
            'nombre' => 'Trébol rojo',
            'tolerancia_general' => 'Buena',
            'descripcion' => 'Fija nitrógeno, mejora la fertilidad'
        ];

        return $pasturas;
    }

    /**
     * Calcula el índice de calidad del suelo
     */
    public function calcularIndiceCalidad(): float
    {
        $puntos = 0;
        $maxPuntos = 10;

        // pH (0-2 puntos)
        if ($this->ph_valor >= 6.0 && $this->ph_valor <= 7.5) {
            $puntos += 2;
        } elseif ($this->ph_valor >= 5.5 && $this->ph_valor <= 8.0) {
            $puntos += 1;
        }

        // Materia orgánica (0-3 puntos)
        if ($this->materia_organica_porcentaje >= 3.0) {
            $puntos += 3;
        } elseif ($this->materia_organica_porcentaje >= 2.0) {
            $puntos += 2;
        } elseif ($this->materia_organica_porcentaje >= 1.0) {
            $puntos += 1;
        }

        // CIC (0-2 puntos)
        if ($this->capacidad_intercambio_cationico >= 15) {
            $puntos += 2;
        } elseif ($this->capacidad_intercambio_cationico >= 10) {
            $puntos += 1;
        }

        // Saturación de bases (0-2 puntos)
        if ($this->saturacion_bases >= 60 && $this->saturacion_bases <= 80) {
            $puntos += 2;
        } elseif ($this->saturacion_bases >= 50 && $this->saturacion_bases <= 90) {
            $puntos += 1;
        }

        // Densidad aparente (0-1 punto)
        if ($this->densidad_aparente >= 1.0 && $this->densidad_aparente <= 1.4) {
            $puntos += 1;
        }

        return round(($puntos / $maxPuntos) * 100, 1);
    }
}