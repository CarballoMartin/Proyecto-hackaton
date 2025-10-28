<?php

namespace App\Services;

use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\IndiceVegetacion;
use App\Models\CaracteristicaSuelo;
use App\Models\AlertaAmbiental;
use App\Services\SatelitalApi\CopernicusApiService;
use App\Services\SueloApi\SoilGridsApiService;
use App\Services\ClimaApi\NasaPowerService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardAmbientalService
{
    private CopernicusApiService $copernicusService;
    private SoilGridsApiService $soilGridsService;
    private NasaPowerService $nasaPowerService;

    public function __construct(
        CopernicusApiService $copernicusService,
        SoilGridsApiService $soilGridsService,
        NasaPowerService $nasaPowerService
    ) {
        $this->copernicusService = $copernicusService;
        $this->soilGridsService = $soilGridsService;
        $this->nasaPowerService = $nasaPowerService;
    }

    /**
     * Obtiene el dashboard completo para un productor
     */
    public function obtenerDashboardCompleto(Productor $productor): array
    {
        $cacheKey = "dashboard_ambiental.{$productor->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($productor) {
            return $this->generarDashboardCompleto($productor);
        });
    }

    /**
     * Genera el dashboard completo
     */
    private function generarDashboardCompleto(Productor $productor): array
    {
        $unidades = $productor->unidadesProductivas()
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();

        $dashboard = [
            'productor' => $productor,
            'resumen_general' => $this->obtenerResumenGeneral($unidades),
            'metricas_clima' => $this->obtenerMetricasClima($unidades),
            'metricas_ndvi' => $this->obtenerMetricasNdvi($unidades),
            'metricas_suelo' => $this->obtenerMetricasSuelo($unidades),
            'alertas' => $this->obtenerAlertasActivas($unidades),
            'certificacion' => $this->calcularCertificacionAmbiental($unidades),
            'recomendaciones' => $this->obtenerRecomendacionesGenerales($unidades),
            'tendencias' => $this->obtenerTendencias($unidades),
        ];

        return $dashboard;
    }

    /**
     * Obtiene resumen general del dashboard
     */
    private function obtenerResumenGeneral($unidades): array
    {
        $totalUnidades = $unidades->count();
        $unidadesConDatos = $unidades->filter(function ($unidad) {
            return $this->tieneDatosCompletos($unidad);
        })->count();

        $alertasCriticas = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->activas()
            ->where('severidad', 'critica')
            ->count();

        $indiceCalidadPromedio = $this->calcularIndiceCalidadPromedio($unidades);

        return [
            'total_unidades' => $totalUnidades,
            'unidades_con_datos' => $unidadesConDatos,
            'cobertura_datos' => $totalUnidades > 0 ? round(($unidadesConDatos / $totalUnidades) * 100, 1) : 0,
            'alertas_criticas' => $alertasCriticas,
            'indice_calidad_promedio' => $indiceCalidadPromedio,
            'estado_general' => $this->determinarEstadoGeneral($indiceCalidadPromedio, $alertasCriticas),
        ];
    }

    /**
     * Obtiene métricas de clima
     */
    private function obtenerMetricasClima($unidades): array
    {
        $metricas = [
            'temperatura_actual' => 0,
            'precipitacion_7dias' => 0,
            'humedad_relativa' => 0,
            'viento_velocidad' => 0,
            'indice_sequia' => 0,
            'estres_termico' => false,
            'helada_riesgo' => false,
        ];

        foreach ($unidades as $unidad) {
            $climaData = $this->nasaPowerService->obtenerDatosClima($unidad);
            if ($climaData) {
                $metricas['temperatura_actual'] += $climaData['temperatura_promedio'];
                $metricas['precipitacion_7dias'] += $climaData['precipitacion_7dias'];
                $metricas['humedad_relativa'] += $climaData['humedad_relativa'];
                $metricas['viento_velocidad'] += $climaData['viento_velocidad'];
                
                if ($climaData['estres_termico']) {
                    $metricas['estres_termico'] = true;
                }
                if ($climaData['helada_riesgo']) {
                    $metricas['helada_riesgo'] = true;
                }
            }
        }

        $totalUnidades = $unidades->count();
        if ($totalUnidades > 0) {
            $metricas['temperatura_actual'] = round($metricas['temperatura_actual'] / $totalUnidades, 1);
            $metricas['precipitacion_7dias'] = round($metricas['precipitacion_7dias'] / $totalUnidades, 1);
            $metricas['humedad_relativa'] = round($metricas['humedad_relativa'] / $totalUnidades, 1);
            $metricas['viento_velocidad'] = round($metricas['viento_velocidad'] / $totalUnidades, 1);
        }

        // Calcular índice de sequía
        $metricas['indice_sequia'] = $this->calcularIndiceSequia($metricas['precipitacion_7dias']);

        return $metricas;
    }

    /**
     * Obtiene métricas de NDVI
     */
    private function obtenerMetricasNdvi($unidades): array
    {
        $metricas = [
            'ndvi_promedio' => 0,
            'vegetacion_saludable' => 0,
            'vegetacion_degradada' => 0,
            'tendencia_mejorando' => 0,
            'tendencia_empeorando' => 0,
            'datos_recientes' => 0,
        ];

        $totalUnidades = 0;
        foreach ($unidades as $unidad) {
            $ndviReciente = IndiceVegetacion::where('unidad_productiva_id', $unidad->id)
                ->where('fecha_imagen', '>=', Carbon::now()->subDays(30))
                ->orderBy('fecha_imagen', 'desc')
                ->first();

            if ($ndviReciente) {
                $metricas['ndvi_promedio'] += $ndviReciente->ndvi;
                $metricas['datos_recientes']++;

                if ($ndviReciente->clasificacion === 'alta') {
                    $metricas['vegetacion_saludable']++;
                } elseif ($ndviReciente->clasificacion === 'baja') {
                    $metricas['vegetacion_degradada']++;
                }

                $tendencia = IndiceVegetacion::tendenciaNdviUnidad($unidad->id, 90);
                if ($tendencia['tendencia'] === 'mejorando') {
                    $metricas['tendencia_mejorando']++;
                } elseif ($tendencia['tendencia'] === 'empeorando') {
                    $metricas['tendencia_empeorando']++;
                }
            }
            $totalUnidades++;
        }

        if ($totalUnidades > 0) {
            $metricas['ndvi_promedio'] = round($metricas['ndvi_promedio'] / $totalUnidades, 3);
            $metricas['vegetacion_saludable'] = round(($metricas['vegetacion_saludable'] / $totalUnidades) * 100, 1);
            $metricas['vegetacion_degradada'] = round(($metricas['vegetacion_degradada'] / $totalUnidades) * 100, 1);
            $metricas['tendencia_mejorando'] = round(($metricas['tendencia_mejorando'] / $totalUnidades) * 100, 1);
            $metricas['tendencia_empeorando'] = round(($metricas['tendencia_empeorando'] / $totalUnidades) * 100, 1);
        }

        return $metricas;
    }

    /**
     * Obtiene métricas de suelo
     */
    private function obtenerMetricasSuelo($unidades): array
    {
        $metricas = [
            'ph_promedio' => 0,
            'materia_organica_promedio' => 0,
            'suelos_acidicos' => 0,
            'suelos_alcalinos' => 0,
            'textura_optima' => 0,
            'fertilidad_alta' => 0,
            'necesita_mejoras' => 0,
        ];

        $totalUnidades = 0;
        foreach ($unidades as $unidad) {
            $sueloReciente = CaracteristicaSuelo::where('unidad_productiva_id', $unidad->id)
                ->orderBy('fecha_consulta', 'desc')
                ->first();

            if ($sueloReciente) {
                $metricas['ph_promedio'] += $sueloReciente->ph_valor;
                $metricas['materia_organica_promedio'] += $sueloReciente->materia_organica_porcentaje;

                if ($sueloReciente->ph_valor < 6.0) {
                    $metricas['suelos_acidicos']++;
                } elseif ($sueloReciente->ph_valor > 8.0) {
                    $metricas['suelos_alcalinos']++;
                }

                if (in_array($sueloReciente->textura_clasificacion, ['Franco', 'Franco arcilloso', 'Franco limoso'])) {
                    $metricas['textura_optima']++;
                }

                if ($sueloReciente->capacidad_intercambio_cationico >= 15) {
                    $metricas['fertilidad_alta']++;
                }

                if ($sueloReciente->estado_general === 'Deficiente' || $sueloReciente->estado_general === 'Crítico') {
                    $metricas['necesita_mejoras']++;
                }
            }
            $totalUnidades++;
        }

        if ($totalUnidades > 0) {
            $metricas['ph_promedio'] = round($metricas['ph_promedio'] / $totalUnidades, 2);
            $metricas['materia_organica_promedio'] = round($metricas['materia_organica_promedio'] / $totalUnidades, 2);
            $metricas['suelos_acidicos'] = round(($metricas['suelos_acidicos'] / $totalUnidades) * 100, 1);
            $metricas['suelos_alcalinos'] = round(($metricas['suelos_alcalinos'] / $totalUnidades) * 100, 1);
            $metricas['textura_optima'] = round(($metricas['textura_optima'] / $totalUnidades) * 100, 1);
            $metricas['fertilidad_alta'] = round(($metricas['fertilidad_alta'] / $totalUnidades) * 100, 1);
            $metricas['necesita_mejoras'] = round(($metricas['necesita_mejoras'] / $totalUnidades) * 100, 1);
        }

        return $metricas;
    }

    /**
     * Obtiene alertas activas
     */
    private function obtenerAlertasActivas($unidades): array
    {
        $alertas = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->activas()
            ->orderBy('severidad', 'desc')
            ->orderBy('fecha_inicio', 'desc')
            ->limit(10)
            ->get();

        return [
            'total' => $alertas->count(),
            'criticas' => $alertas->where('severidad', 'critica')->count(),
            'altas' => $alertas->where('severidad', 'alta')->count(),
            'medias' => $alertas->where('severidad', 'media')->count(),
            'bajas' => $alertas->where('severidad', 'baja')->count(),
            'alertas' => $alertas->toArray(),
        ];
    }

    /**
     * Calcula certificación ambiental
     */
    private function calcularCertificacionAmbiental($unidades): array
    {
        $puntosBase = 200; // Puntos base
        $puntosNdvi = 0;
        $puntosSuelo = 0;
        $puntosClima = 0;
        $puntosAlertas = 0;

        $totalUnidades = $unidades->count();
        if ($totalUnidades === 0) {
            return [
                'puntos_totales' => 0,
                'puntos_maximos' => 400,
                'porcentaje' => 0,
                'categoria' => 'Sin datos',
                'nivel' => 'inicial'
            ];
        }

        foreach ($unidades as $unidad) {
            // Puntos por NDVI
            $ndviReciente = IndiceVegetacion::where('unidad_productiva_id', $unidad->id)
                ->orderBy('fecha_imagen', 'desc')
                ->first();
            
            if ($ndviReciente) {
                $puntosNdvi += match($ndviReciente->clasificacion) {
                    'alta' => 20,
                    'media' => 10,
                    'baja' => 0,
                    default => 0
                };
            }

            // Puntos por suelo
            $sueloReciente = CaracteristicaSuelo::where('unidad_productiva_id', $unidad->id)
                ->orderBy('fecha_consulta', 'desc')
                ->first();
            
            if ($sueloReciente) {
                $puntosSuelo += match($sueloReciente->estado_general) {
                    'Excelente' => 30,
                    'Bueno' => 20,
                    'Regular' => 10,
                    'Deficiente' => 5,
                    'Crítico' => 0,
                    default => 0
                };
            }

            // Puntos por clima (sin alertas críticas)
            $alertasCriticas = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
                ->activas()
                ->where('severidad', 'critica')
                ->count();
            
            if ($alertasCriticas === 0) {
                $puntosClima += 15;
            }
        }

        // Puntos por gestión de alertas
        $alertasTotales = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->where('fecha_inicio', '>=', Carbon::now()->subDays(30))
            ->count();
        
        if ($alertasTotales === 0) {
            $puntosAlertas = 20;
        } elseif ($alertasTotales <= 2) {
            $puntosAlertas = 15;
        } elseif ($alertasTotales <= 5) {
            $puntosAlertas = 10;
        } else {
            $puntosAlertas = 5;
        }

        $puntosTotales = $puntosBase + $puntosNdvi + $puntosSuelo + $puntosClima + $puntosAlertas;
        $porcentaje = round(($puntosTotales / 400) * 100, 1);

        $categoria = match(true) {
            $porcentaje >= 90 => 'Excelente',
            $porcentaje >= 80 => 'Muy Bueno',
            $porcentaje >= 70 => 'Bueno',
            $porcentaje >= 60 => 'Satisfactorio',
            $porcentaje >= 50 => 'Regular',
            default => 'Necesita Mejoras'
        };

        $nivel = match(true) {
            $porcentaje >= 80 => 'avanzado',
            $porcentaje >= 60 => 'intermedio',
            default => 'inicial'
        };

        return [
            'puntos_totales' => $puntosTotales,
            'puntos_maximos' => 400,
            'porcentaje' => $porcentaje,
            'categoria' => $categoria,
            'nivel' => $nivel,
            'desglose' => [
                'base' => $puntosBase,
                'ndvi' => $puntosNdvi,
                'suelo' => $puntosSuelo,
                'clima' => $puntosClima,
                'alertas' => $puntosAlertas,
            ]
        ];
    }

    /**
     * Obtiene recomendaciones generales
     */
    private function obtenerRecomendacionesGenerales($unidades): array
    {
        $recomendaciones = [];

        // Analizar alertas críticas
        $alertasCriticas = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->activas()
            ->where('severidad', 'critica')
            ->count();

        if ($alertasCriticas > 0) {
            $recomendaciones[] = [
                'tipo' => 'urgente',
                'titulo' => 'Alertas Críticas Activas',
                'descripcion' => "Tienes {$alertasCriticas} alertas críticas que requieren atención inmediata.",
                'accion' => 'Revisar y gestionar alertas críticas'
            ];
        }

        // Analizar NDVI bajo
        $ndviBajo = IndiceVegetacion::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->where('fecha_imagen', '>=', Carbon::now()->subDays(30))
            ->where('clasificacion', 'baja')
            ->count();

        if ($ndviBajo > 0) {
            $recomendaciones[] = [
                'tipo' => 'importante',
                'titulo' => 'Vegetación Degradada',
                'descripcion' => "{$ndviBajo} unidades productivas muestran vegetación degradada.",
                'accion' => 'Implementar prácticas de mejoramiento de pasturas'
            ];
        }

        // Analizar suelos ácidos
        $suelosAcidos = CaracteristicaSuelo::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->where('ph_valor', '<', 6.0)
            ->count();

        if ($suelosAcidos > 0) {
            $recomendaciones[] = [
                'tipo' => 'mejora',
                'titulo' => 'Suelos Ácidos',
                'descripcion' => "{$suelosAcidos} unidades productivas tienen suelos ácidos.",
                'accion' => 'Considerar aplicación de cal agrícola'
            ];
        }

        return $recomendaciones;
    }

    /**
     * Obtiene tendencias
     */
    private function obtenerTendencias($unidades): array
    {
        return [
            'ndvi_tendencia' => $this->calcularTendenciaNdvi($unidades),
            'clima_tendencia' => $this->calcularTendenciaClima($unidades),
            'alertas_tendencia' => $this->calcularTendenciaAlertas($unidades),
        ];
    }

    /**
     * Verifica si una unidad tiene datos completos
     */
    private function tieneDatosCompletos(UnidadProductiva $unidad): bool
    {
        $tieneNdvi = IndiceVegetacion::where('unidad_productiva_id', $unidad->id)->exists();
        $tieneSuelo = CaracteristicaSuelo::where('unidad_productiva_id', $unidad->id)->exists();
        
        return $tieneNdvi && $tieneSuelo;
    }

    /**
     * Calcula índice de calidad promedio
     */
    private function calcularIndiceCalidadPromedio($unidades): float
    {
        $totalIndice = 0;
        $unidadesConIndice = 0;

        foreach ($unidades as $unidad) {
            $sueloReciente = CaracteristicaSuelo::where('unidad_productiva_id', $unidad->id)
                ->orderBy('fecha_consulta', 'desc')
                ->first();
            
            if ($sueloReciente) {
                $totalIndice += $sueloReciente->calcularIndiceCalidad();
                $unidadesConIndice++;
            }
        }

        return $unidadesConIndice > 0 ? round($totalIndice / $unidadesConIndice, 1) : 0;
    }

    /**
     * Determina estado general
     */
    private function determinarEstadoGeneral(float $indiceCalidad, int $alertasCriticas): string
    {
        if ($alertasCriticas > 0) {
            return 'Crítico';
        } elseif ($indiceCalidad >= 80) {
            return 'Excelente';
        } elseif ($indiceCalidad >= 60) {
            return 'Bueno';
        } elseif ($indiceCalidad >= 40) {
            return 'Regular';
        } else {
            return 'Deficiente';
        }
    }

    /**
     * Calcula índice de sequía
     */
    private function calcularIndiceSequia(float $precipitacion7dias): int
    {
        if ($precipitacion7dias < 5) return 4; // Sequía extrema
        if ($precipitacion7dias < 15) return 3; // Sequía severa
        if ($precipitacion7dias < 25) return 2; // Sequía moderada
        if ($precipitacion7dias < 40) return 1; // Sequía leve
        return 0; // Sin sequía
    }

    /**
     * Calcula tendencia de NDVI
     */
    private function calcularTendenciaNdvi($unidades): string
    {
        $mejorando = 0;
        $empeorando = 0;
        $estable = 0;

        foreach ($unidades as $unidad) {
            $tendencia = IndiceVegetacion::tendenciaNdviUnidad($unidad->id, 90);
            switch ($tendencia['tendencia']) {
                case 'mejorando':
                    $mejorando++;
                    break;
                case 'empeorando':
                    $empeorando++;
                    break;
                default:
                    $estable++;
            }
        }

        $total = $unidades->count();
        if ($total === 0) return 'sin_datos';

        $porcentajeMejorando = ($mejorando / $total) * 100;
        $porcentajeEmpeorando = ($empeorando / $total) * 100;

        if ($porcentajeMejorando > 50) return 'mejorando';
        if ($porcentajeEmpeorando > 50) return 'empeorando';
        return 'estable';
    }

    /**
     * Calcula tendencia de clima
     */
    private function calcularTendenciaClima($unidades): string
    {
        // Implementar lógica de tendencia climática
        return 'estable';
    }

    /**
     * Calcula tendencia de alertas
     */
    private function calcularTendenciaAlertas($unidades): string
    {
        $alertasActuales = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->where('fecha_inicio', '>=', Carbon::now()->subDays(7))
            ->count();

        $alertasAnteriores = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->whereBetween('fecha_inicio', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])
            ->count();

        if ($alertasActuales > $alertasAnteriores) return 'aumentando';
        if ($alertasActuales < $alertasAnteriores) return 'disminuyendo';
        return 'estable';
    }
}
