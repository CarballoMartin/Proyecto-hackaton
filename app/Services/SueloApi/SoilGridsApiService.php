<?php

namespace App\Services\SueloApi;

use App\Models\CaracteristicaSuelo;
use App\Models\UnidadProductiva;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SoilGridsApiService
{
    private const BASE_URL = 'https://rest.isric.org/soilgrids/v2.0/properties/query';
    private const CACHE_TTL = 2592000; // 30 días
    
    /**
     * Obtiene datos de suelo para una unidad productiva
     */
    public function obtenerDatosSuelo(UnidadProductiva $unidadProductiva): ?array
    {
        if (!$unidadProductiva->latitud || !$unidadProductiva->longitud) {
            Log::warning('Unidad productiva sin coordenadas', ['id' => $unidadProductiva->id]);
            return null;
        }

        $cacheKey = "suelo.{$unidadProductiva->id}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unidadProductiva) {
            return $this->consultarDatosSuelo($unidadProductiva);
        });
    }

    /**
     * Consulta datos de suelo directamente a la API
     */
    private function consultarDatosSuelo(UnidadProductiva $unidadProductiva): ?array
    {
        try {
            // Propiedades que queremos obtener de SoilGrids
            $properties = [
                'phh2o',           // pH en agua
                'soc',             // Carbono orgánico del suelo
                'clay',            // Arcilla
                'silt',            // Limo
                'sand',            // Arena
                'nitrogen',        // Nitrógeno total
                'phos',            // Fósforo disponible
                'k',               // Potasio intercambiable
                'cec',             // Capacidad de intercambio catiónico
                'bs',              // Saturación de bases
                'bdod',            // Densidad aparente
            ];

            $payload = [
                'lon' => $unidadProductiva->longitud,
                'lat' => $unidadProductiva->latitud,
                'property' => implode(',', $properties),
                'depth' => '0-30cm', // Profundidad estándar
                'value' => 'mean',   // Valor promedio
            ];

            $response = Http::timeout(30)->get(self::BASE_URL, $payload);

            if ($response->successful()) {
                return $this->procesarRespuestaSuelo($response->json(), $unidadProductiva);
            } else {
                Log::error('Error en API SoilGrids', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Excepción en consulta suelo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Procesa la respuesta de la API para extraer datos de suelo
     */
    private function procesarRespuestaSuelo(array $response, UnidadProductiva $unidadProductiva): ?array
    {
        try {
            if (!isset($response['properties']) || empty($response['properties'])) {
                Log::warning('Respuesta SoilGrids sin propiedades', ['response' => $response]);
                return null;
            }

            $properties = $response['properties'];
            
            // Extraer valores de las propiedades
            $ph = $this->extraerValor($properties, 'phh2o');
            $materiaOrganica = $this->extraerValor($properties, 'soc') * 1.724; // Convertir SOC a MO
            $arcilla = $this->extraerValor($properties, 'clay');
            $limo = $this->extraerValor($properties, 'silt');
            $arena = $this->extraerValor($properties, 'sand');
            $nitrogeno = $this->extraerValor($properties, 'nitrogen');
            $fosforo = $this->extraerValor($properties, 'phos');
            $potasio = $this->extraerValor($properties, 'k');
            $cic = $this->extraerValor($properties, 'cec');
            $saturacionBases = $this->extraerValor($properties, 'bs');
            $densidadAparente = $this->extraerValor($properties, 'bdod');

            // Normalizar porcentajes de textura
            $totalTextura = $arcilla + $limo + $arena;
            if ($totalTextura > 0) {
                $arcilla = ($arcilla / $totalTextura) * 100;
                $limo = ($limo / $totalTextura) * 100;
                $arena = ($arena / $totalTextura) * 100;
            }

            // Calcular capacidad de retención de agua (estimación)
            $capacidadRetencion = $this->calcularCapacidadRetencion($arcilla, $limo, $arena, $materiaOrganica);

            // Clasificar textura
            $texturaClasificacion = $this->clasificarTextura($arcilla, $limo, $arena);

            return [
                'ph_valor' => round($ph, 2),
                'materia_organica_porcentaje' => round($materiaOrganica, 2),
                'arcilla_porcentaje' => round($arcilla, 2),
                'limo_porcentaje' => round($limo, 2),
                'arena_porcentaje' => round($arena, 2),
                'capacidad_retencion_agua' => round($capacidadRetencion, 2),
                'nitrogeno_total' => round($nitrogeno, 2),
                'fosforo_disponible' => round($fosforo, 2),
                'potasio_intercambiable' => round($potasio, 2),
                'capacidad_intercambio_cationico' => round($cic, 2),
                'saturacion_bases' => round($saturacionBases, 2),
                'densidad_aparente' => round($densidadAparente, 2),
                'profundidad_cm' => 30,
                'textura_clasificacion' => $texturaClasificacion,
                'fuente_datos' => 'soilgrids',
                'fecha_consulta' => Carbon::now()->format('Y-m-d'),
                'datos_fuente_json' => $properties,
            ];

        } catch (\Exception $e) {
            Log::error('Error procesando respuesta suelo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extrae un valor específico de las propiedades de la respuesta
     */
    private function extraerValor(array $properties, string $property): float
    {
        foreach ($properties as $prop) {
            if ($prop['property'] === $property) {
                return (float) $prop['value'];
            }
        }
        return 0.0;
    }

    /**
     * Calcula la capacidad de retención de agua del suelo
     */
    private function calcularCapacidadRetencion(float $arcilla, float $limo, float $arena, float $materiaOrganica): float
    {
        // Fórmula empírica basada en textura y materia orgánica
        $baseRetencion = ($arcilla * 0.4) + ($limo * 0.2) + ($arena * 0.1);
        $bonusMateriaOrganica = $materiaOrganica * 0.1;
        
        return $baseRetencion + $bonusMateriaOrganica;
    }

    /**
     * Clasifica la textura del suelo según el triángulo textural
     */
    private function clasificarTextura(float $arcilla, float $limo, float $arena): string
    {
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
     * Obtiene datos históricos de suelo para una unidad productiva
     */
    public function obtenerHistorialSuelo(UnidadProductiva $unidadProductiva, int $meses = 12): array
    {
        return CaracteristicaSuelo::where('unidad_productiva_id', $unidadProductiva->id)
            ->where('fecha_consulta', '>=', Carbon::now()->subMonths($meses))
            ->orderBy('fecha_consulta')
            ->get()
            ->toArray();
    }

    /**
     * Guarda datos de suelo en la base de datos
     */
    public function guardarDatosSuelo(UnidadProductiva $unidadProductiva, array $sueloData): ?CaracteristicaSuelo
    {
        try {
            return CaracteristicaSuelo::create([
                'unidad_productiva_id' => $unidadProductiva->id,
                'ph_valor' => $sueloData['ph_valor'],
                'materia_organica_porcentaje' => $sueloData['materia_organica_porcentaje'],
                'arcilla_porcentaje' => $sueloData['arcilla_porcentaje'],
                'limo_porcentaje' => $sueloData['limo_porcentaje'],
                'arena_porcentaje' => $sueloData['arena_porcentaje'],
                'capacidad_retencion_agua' => $sueloData['capacidad_retencion_agua'],
                'nitrogeno_total' => $sueloData['nitrogeno_total'],
                'fosforo_disponible' => $sueloData['fosforo_disponible'],
                'potasio_intercambiable' => $sueloData['potasio_intercambiable'],
                'capacidad_intercambio_cationico' => $sueloData['capacidad_intercambio_cationico'],
                'saturacion_bases' => $sueloData['saturacion_bases'],
                'densidad_aparente' => $sueloData['densidad_aparente'],
                'profundidad_cm' => $sueloData['profundidad_cm'],
                'textura_clasificacion' => $sueloData['textura_clasificacion'],
                'fuente_datos' => $sueloData['fuente_datos'],
                'fecha_consulta' => $sueloData['fecha_consulta'],
                'datos_fuente_json' => $sueloData['datos_fuente_json'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando datos suelo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza datos de suelo para todas las unidades productivas con coordenadas
     */
    public function actualizarDatosSuelo(): array
    {
        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();

        $resultados = [
            'exitosos' => 0,
            'fallidos' => 0,
            'errores' => []
        ];

        foreach ($unidades as $unidad) {
            try {
                $sueloData = $this->obtenerDatosSuelo($unidad);
                if ($sueloData) {
                    $this->guardarDatosSuelo($unidad, $sueloData);
                    $resultados['exitosos']++;
                } else {
                    $resultados['fallidos']++;
                    $resultados['errores'][] = "Sin datos para unidad {$unidad->id}";
                }
            } catch (\Exception $e) {
                $resultados['fallidos']++;
                $resultados['errores'][] = "Error unidad {$unidad->id}: " . $e->getMessage();
            }
        }

        return $resultados;
    }

    /**
     * Obtiene recomendaciones de mejoramiento de suelo
     */
    public function obtenerRecomendacionesMejoramiento(CaracteristicaSuelo $suelo): array
    {
        return $suelo->recomendaciones;
    }

    /**
     * Obtiene recomendaciones de pasturas según el suelo
     */
    public function obtenerRecomendacionesPasturas(CaracteristicaSuelo $suelo): array
    {
        return $suelo->recomendaciones_pasturas;
    }
}