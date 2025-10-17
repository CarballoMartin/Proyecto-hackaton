<?php

namespace App\Services;

use App\Models\Productor;
use Illuminate\Support\Facades\DB;

class HuellaCarbonService
{
    /**
     * Factores de emisión de metano por tipo de animal (kg CH4/animal/año)
     * Basado en datos del IPCC y estudios latinoamericanos
     */
    const FACTORES_EMISION = [
        'Ovino' => [
            'ch4_animal_anio' => 8.0,  // kg CH4 por ovino por año
            'co2_equivalente' => 28,    // Factor de conversión CH4 a CO2eq
            'descripcion' => 'Ovino adulto promedio'
        ],
        'Caprino' => [
            'ch4_animal_anio' => 5.0,  // kg CH4 por caprino por año
            'co2_equivalente' => 28,
            'descripcion' => 'Caprino adulto promedio'
        ],
        'Bovino' => [
            'ch4_animal_anio' => 57.0, // kg CH4 por bovino por año
            'co2_equivalente' => 28,
            'descripcion' => 'Bovino adulto promedio'
        ],
        'Equino' => [
            'ch4_animal_anio' => 18.0, // kg CH4 por equino por año
            'co2_equivalente' => 28,
            'descripcion' => 'Equino adulto promedio'
        ],
    ];

    /**
     * Benchmarks internacionales (kg CO2eq/animal/año)
     */
    const BENCHMARKS = [
        'excelente' => 150,  // Sistemas muy eficientes
        'bueno' => 250,      // Sistemas buenos
        'promedio' => 350,   // Promedio nacional
        'mejorable' => 500,  // Requiere mejoras
    ];

    /**
     * Calcula la huella de carbono total del productor
     */
    public function calcularHuellaTotal(Productor $productor): array
    {
        $unidades = $productor->unidadesProductivas;
        
        if ($unidades->isEmpty()) {
            return $this->respuestaVacia();
        }

        // Obtener stock actual por especie
        $stockPorEspecie = DB::table('stock_actual')
            ->join('especies', 'stock_actual.especie_id', '=', 'especies.id')
            ->whereIn('stock_actual.unidad_productiva_id', $unidades->pluck('id'))
            ->select('especies.nombre as especie', DB::raw('SUM(stock_actual.cantidad_actual) as total'))
            ->groupBy('especies.nombre')
            ->get();

        $emisionesTotales = 0;
        $detalleEspecies = [];
        $totalAnimales = 0;

        foreach ($stockPorEspecie as $stock) {
            $especie = $stock->especie;
            $cantidad = $stock->total;
            $totalAnimales += $cantidad;

            if (isset(self::FACTORES_EMISION[$especie])) {
                $factor = self::FACTORES_EMISION[$especie];
                
                // Cálculo: kg CH4/año * factor CO2eq * número de animales
                $ch4Anual = $factor['ch4_animal_anio'] * $cantidad;
                $co2Equivalente = $ch4Anual * $factor['co2_equivalente'];
                
                $emisionesTotales += $co2Equivalente;
                
                $detalleEspecies[] = [
                    'especie' => $especie,
                    'cantidad' => $cantidad,
                    'ch4_anual_kg' => round($ch4Anual, 2),
                    'co2_equivalente_kg' => round($co2Equivalente, 2),
                    'porcentaje' => 0, // Se calculará después
                ];
            }
        }

        // Calcular porcentajes
        foreach ($detalleEspecies as &$detalle) {
            $detalle['porcentaje'] = $emisionesTotales > 0 
                ? round(($detalle['co2_equivalente_kg'] / $emisionesTotales) * 100, 1)
                : 0;
        }

        // Calcular emisiones por hectárea
        $superficieTotal = $unidades->sum('superficie');
        $emisionesPorHectarea = $superficieTotal > 0 
            ? round($emisionesTotales / $superficieTotal, 2)
            : 0;

        // Emisiones por animal
        $emisionesPorAnimal = $totalAnimales > 0
            ? round($emisionesTotales / $totalAnimales, 2)
            : 0;

        // Clasificación según benchmarks
        $clasificacion = $this->clasificarEmisiones($emisionesPorAnimal);

        // Proyección anual y mensual
        $proyeccionAnual = round($emisionesTotales, 2);
        $proyeccionMensual = round($emisionesTotales / 12, 2);

        // Comparación con benchmarks
        $comparacionBenchmark = $this->compararConBenchmark($emisionesPorAnimal);

        // Recomendaciones personalizadas
        $recomendaciones = $this->generarRecomendaciones($productor, $emisionesPorAnimal, $detalleEspecies);

        // Potencial de reducción
        $potencialReduccion = $this->calcularPotencialReduccion($emisionesPorAnimal, $emisionesTotales);

        return [
            'emisiones_totales_co2eq_kg' => $proyeccionAnual,
            'emisiones_mensuales_co2eq_kg' => $proyeccionMensual,
            'emisiones_por_hectarea' => $emisionesPorHectarea,
            'emisiones_por_animal' => $emisionesPorAnimal,
            'total_animales' => $totalAnimales,
            'superficie_total' => $superficieTotal,
            'detalle_especies' => $detalleEspecies,
            'clasificacion' => $clasificacion,
            'comparacion_benchmark' => $comparacionBenchmark,
            'recomendaciones' => $recomendaciones,
            'potencial_reduccion' => $potencialReduccion,
            'equivalencias' => $this->calcularEquivalencias($proyeccionAnual),
        ];
    }

    /**
     * Clasifica las emisiones según benchmarks
     */
    private function clasificarEmisiones(float $emisionesPorAnimal): array
    {
        if ($emisionesPorAnimal <= self::BENCHMARKS['excelente']) {
            return [
                'nivel' => 'excelente',
                'color' => 'green',
                'icono' => '🌟',
                'mensaje' => '¡Excelente! Emisiones muy por debajo del promedio'
            ];
        } elseif ($emisionesPorAnimal <= self::BENCHMARKS['bueno']) {
            return [
                'nivel' => 'bueno',
                'color' => 'blue',
                'icono' => '✓',
                'mensaje' => 'Buen desempeño, por debajo del promedio nacional'
            ];
        } elseif ($emisionesPorAnimal <= self::BENCHMARKS['promedio']) {
            return [
                'nivel' => 'promedio',
                'color' => 'yellow',
                'icono' => '○',
                'mensaje' => 'Emisiones dentro del promedio nacional'
            ];
        } else {
            return [
                'nivel' => 'mejorable',
                'color' => 'red',
                'icono' => '!',
                'mensaje' => 'Oportunidad de mejora significativa'
            ];
        }
    }

    /**
     * Compara con benchmarks internacionales
     */
    private function compararConBenchmark(float $emisionesPorAnimal): array
    {
        $diferencias = [];
        
        foreach (self::BENCHMARKS as $nivel => $valor) {
            $diferencia = $emisionesPorAnimal - $valor;
            $porcentaje = $valor > 0 ? round(($diferencia / $valor) * 100, 1) : 0;
            
            $diferencias[$nivel] = [
                'valor_benchmark' => $valor,
                'diferencia' => round($diferencia, 2),
                'porcentaje' => $porcentaje,
                'estado' => $diferencia <= 0 ? 'mejor' : 'peor',
            ];
        }

        return $diferencias;
    }

    /**
     * Genera recomendaciones personalizadas
     */
    private function generarRecomendaciones(Productor $productor, float $emisionesPorAnimal, array $detalleEspecies): array
    {
        $recomendaciones = [];

        // Recomendación según nivel de emisiones
        if ($emisionesPorAnimal > self::BENCHMARKS['promedio']) {
            $recomendaciones[] = [
                'titulo' => 'Mejora el manejo de pasturas',
                'descripcion' => 'Implementa rotación de pasturas para mejorar la digestibilidad y reducir emisiones de metano',
                'impacto' => 'alto',
                'reduccion_estimada' => '15-20%'
            ];
        }

        // Recomendación por diversidad
        if (count($detalleEspecies) == 1) {
            $recomendaciones[] = [
                'titulo' => 'Diversifica tu producción',
                'descripcion' => 'La diversificación de especies puede optimizar el uso de recursos y reducir la huella por animal',
                'impacto' => 'medio',
                'reduccion_estimada' => '10-15%'
            ];
        }

        // Recomendación general
        $recomendaciones[] = [
            'titulo' => 'Suplementación estratégica',
            'descripcion' => 'El uso de suplementos puede mejorar la eficiencia digestiva y reducir emisiones de metano entérico',
            'impacto' => 'medio',
            'reduccion_estimada' => '8-12%'
        ];

        $recomendaciones[] = [
            'titulo' => 'Manejo de residuos',
            'descripcion' => 'Implementa sistemas de compostaje para reducir emisiones de óxido nitroso del estiércol',
            'impacto' => 'bajo',
            'reduccion_estimada' => '5-8%'
        ];

        return $recomendaciones;
    }

    /**
     * Calcula el potencial de reducción
     */
    private function calcularPotencialReduccion(float $emisionesPorAnimal, float $emisionesTotales): array
    {
        $reduccionOptimista = 0.25; // 25%
        $reduccionConservadora = 0.15; // 15%

        return [
            'reduccion_optimista' => [
                'porcentaje' => 25,
                'kg_co2_anual' => round($emisionesTotales * $reduccionOptimista, 2),
                'plazo' => '2-3 años'
            ],
            'reduccion_conservadora' => [
                'porcentaje' => 15,
                'kg_co2_anual' => round($emisionesTotales * $reduccionConservadora, 2),
                'plazo' => '1-2 años'
            ],
        ];
    }

    /**
     * Calcula equivalencias para mejor comprensión
     */
    private function calcularEquivalencias(float $co2KgAnual): array
    {
        return [
            'arboles_necesarios' => round($co2KgAnual / 21, 0), // Un árbol absorbe ~21kg CO2/año
            'km_auto' => round($co2KgAnual / 0.12, 0), // Auto promedio emite 0.12kg CO2/km
            'hogares_anual' => round($co2KgAnual / 4500, 2), // Hogar promedio 4.5 ton CO2/año
        ];
    }

    /**
     * Respuesta vacía cuando no hay datos
     */
    private function respuestaVacia(): array
    {
        return [
            'emisiones_totales_co2eq_kg' => 0,
            'emisiones_mensuales_co2eq_kg' => 0,
            'emisiones_por_hectarea' => 0,
            'emisiones_por_animal' => 0,
            'total_animales' => 0,
            'superficie_total' => 0,
            'detalle_especies' => [],
            'clasificacion' => [
                'nivel' => 'sin_datos',
                'color' => 'gray',
                'icono' => '?',
                'mensaje' => 'No hay datos suficientes para calcular'
            ],
            'comparacion_benchmark' => [],
            'recomendaciones' => [],
            'potencial_reduccion' => [],
            'equivalencias' => [],
        ];
    }
}





