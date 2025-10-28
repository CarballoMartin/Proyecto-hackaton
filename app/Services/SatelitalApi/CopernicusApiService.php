<?php

namespace App\Services\SatelitalApi;

use App\Models\IndiceVegetacion;
use App\Models\UnidadProductiva;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CopernicusApiService
{
    private const BASE_URL = 'https://services.sentinel-hub.com/api/v1/';
    private const CACHE_TTL = 604800; // 7 días
    
    private string $clientId;
    private string $clientSecret;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('ambiental.copernicus.client_id', '');
        $this->clientSecret = config('ambiental.copernicus.client_secret', '');
    }

    /**
     * Obtiene el token de acceso de Copernicus
     */
    private function obtenerTokenAcceso(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cacheKey = 'copernicus.access_token';
        $this->accessToken = Cache::get($cacheKey);

        if (!$this->accessToken) {
            try {
                $response = Http::asForm()->post('https://services.sentinel-hub.com/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->accessToken = $data['access_token'];
                    Cache::put($cacheKey, $this->accessToken, $data['expires_in'] - 60);
                }
            } catch (\Exception $e) {
                Log::error('Error obteniendo token Copernicus: ' . $e->getMessage());
                return null;
            }
        }

        return $this->accessToken;
    }

    /**
     * Obtiene datos NDVI para una unidad productiva
     */
    public function obtenerNDVI(UnidadProductiva $unidadProductiva, Carbon $fecha = null): ?array
    {
        if (!$unidadProductiva->latitud || !$unidadProductiva->longitud) {
            Log::warning('Unidad productiva sin coordenadas', ['id' => $unidadProductiva->id]);
            return null;
        }

        $fecha = $fecha ?? Carbon::now()->subDays(5); // Sentinel-2 tiene ciclo de 5 días
        $cacheKey = "ndvi.{$unidadProductiva->id}.{$fecha->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unidadProductiva, $fecha) {
            return $this->consultarNDVI($unidadProductiva, $fecha);
        });
    }

    /**
     * Consulta NDVI directamente a la API
     */
    private function consultarNDVI(UnidadProductiva $unidadProductiva, Carbon $fecha): ?array
    {
        $token = $this->obtenerTokenAcceso();
        if (!$token) {
            return null;
        }

        try {
            // Configurar el área de interés (buffer de 1km alrededor del punto)
            $bbox = $this->calcularBbox($unidadProductiva->latitud, $unidadProductiva->longitud, 0.01);

            // Script de procesamiento para NDVI
            $script = "
                //VERSION=3
                function setup() {
                    return {
                        input: [{
                            bands: ['B04', 'B08', 'CLM'],
                            units: 'DN'
                        }],
                        output: {
                            bands: 1,
                            sampleType: 'FLOAT32'
                        }
                    };
                }

                function evaluatePixel(sample) {
                    let ndvi = (sample.B08 - sample.B04) / (sample.B08 + sample.B04);
                    return [ndvi];
                }
            ";

            $payload = [
                'input' => [
                    'bounds' => [
                        'bbox' => $bbox,
                        'properties' => [
                            'crs' => 'http://www.opengis.net/def/crs/EPSG/0/4326'
                        ]
                    ],
                    'data' => [[
                        'type' => 'sentinel-2-l2a',
                        'dataFilter' => [
                            'timeRange' => [
                                'from' => $fecha->subDays(10)->toISOString(),
                                'to' => $fecha->toISOString()
                            ],
                            'maxCloudCoverage' => 30
                        ]
                    ]]
                ],
                'evalscript' => $script,
                'output' => [
                    'width' => 100,
                    'height' => 100,
                    'responses' => [[
                        'identifier' => 'default',
                        'format' => [
                            'type' => 'image/png'
                        ]
                    ]]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post(self::BASE_URL . 'process', $payload);

            if ($response->successful()) {
                return $this->procesarRespuestaNDVI($response->body(), $unidadProductiva, $fecha);
            } else {
                Log::error('Error en API Copernicus', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Excepción en consulta NDVI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Procesa la respuesta de la API para extraer NDVI
     */
    private function procesarRespuestaNDVI(string $imageData, UnidadProductiva $unidadProductiva, Carbon $fecha): ?array
    {
        try {
            // Decodificar imagen PNG y extraer valores NDVI
            $image = imagecreatefromstring($imageData);
            if (!$image) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $ndviValues = [];

            // Extraer valores NDVI del centro de la imagen
            $centerX = intval($width / 2);
            $centerY = intval($height / 2);
            $sampleSize = 10; // Muestra de 10x10 píxeles del centro

            for ($x = $centerX - $sampleSize/2; $x < $centerX + $sampleSize/2; $x++) {
                for ($y = $centerY - $sampleSize/2; $y < $centerY + $sampleSize/2; $y++) {
                    if ($x >= 0 && $x < $width && $y >= 0 && $y < $height) {
                        $rgb = imagecolorat($image, $x, $y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                        
                        // Convertir RGB a NDVI (asumiendo que el script devuelve valores normalizados)
                        $ndvi = ($r / 255.0) * 2 - 1; // Escalar de 0-255 a -1 a 1
                        $ndviValues[] = $ndvi;
                    }
                }
            }

            imagedestroy($image);

            if (empty($ndviValues)) {
                return null;
            }

            $ndviPromedio = array_sum($ndviValues) / count($ndviValues);
            $clasificacion = IndiceVegetacion::clasificarNdvi($ndviPromedio);

            return [
                'ndvi' => round($ndviPromedio, 3),
                'clasificacion' => $clasificacion,
                'fecha_imagen' => $fecha->format('Y-m-d'),
                'satelite' => 'sentinel-2',
                'nubosidad_porcentaje' => 0, // Se obtendría de metadatos adicionales
                'latitud' => $unidadProductiva->latitud,
                'longitud' => $unidadProductiva->longitud,
                'datos_completos' => [
                    'valores_muestra' => $ndviValues,
                    'promedio' => $ndviPromedio,
                    'desviacion' => $this->calcularDesviacionEstandar($ndviValues),
                    'fuente' => 'copernicus-sentinel-2'
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error procesando respuesta NDVI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcula el bbox para una coordenada con buffer
     */
    private function calcularBbox(float $lat, float $lon, float $buffer): array
    {
        return [
            $lon - $buffer, // minLon
            $lat - $buffer, // minLat
            $lon + $buffer, // maxLon
            $lat + $buffer  // maxLat
        ];
    }

    /**
     * Calcula la desviación estándar de un array de valores
     */
    private function calcularDesviacionEstandar(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        return sqrt($variance);
    }

    /**
     * Obtiene datos históricos de NDVI para una unidad productiva
     */
    public function obtenerHistorialNDVI(UnidadProductiva $unidadProductiva, int $meses = 6): array
    {
        $fechaInicio = Carbon::now()->subMonths($meses);
        $fechaFin = Carbon::now();
        $datos = [];

        // Obtener datos cada 5 días (ciclo de Sentinel-2)
        $fechaActual = $fechaInicio->copy();
        while ($fechaActual->lte($fechaFin)) {
            $ndviData = $this->obtenerNDVI($unidadProductiva, $fechaActual);
            if ($ndviData) {
                $datos[] = $ndviData;
            }
            $fechaActual->addDays(5);
        }

        return $datos;
    }

    /**
     * Guarda datos NDVI en la base de datos
     */
    public function guardarDatosNDVI(UnidadProductiva $unidadProductiva, array $ndviData): ?IndiceVegetacion
    {
        try {
            return IndiceVegetacion::create([
                'unidad_productiva_id' => $unidadProductiva->id,
                'ndvi' => $ndviData['ndvi'],
                'clasificacion' => $ndviData['clasificacion'],
                'fecha_imagen' => $ndviData['fecha_imagen'],
                'satelite' => $ndviData['satelite'],
                'nubosidad_porcentaje' => $ndviData['nubosidad_porcentaje'],
                'latitud' => $ndviData['latitud'],
                'longitud' => $ndviData['longitud'],
                'datos_completos' => $ndviData['datos_completos']
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando datos NDVI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza datos NDVI para todas las unidades productivas con coordenadas
     */
    public function actualizarDatosNDVI(): array
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
                $ndviData = $this->obtenerNDVI($unidad);
                if ($ndviData) {
                    $this->guardarDatosNDVI($unidad, $ndviData);
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
}
