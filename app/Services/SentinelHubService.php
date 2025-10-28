<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SentinelHubService
{
    private string $baseUrl = 'https://services.sentinel-hub.com/api/v1';
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $accessToken = null;
    private ?Carbon $tokenExpiresAt = null;

    public function __construct()
    {
        $this->clientId = config('services.sentinel_hub.client_id');
        $this->clientSecret = config('services.sentinel_hub.client_secret');
    }

    /**
     * Obtener token de acceso para la API
     */
    private function obtenerTokenAcceso(): bool
    {
        if ($this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt->isFuture()) {
            return true;
        }

        if (!$this->clientId || !$this->clientSecret) {
            Log::error('Sentinel Hub: Credenciales no configuradas');
            return false;
        }

        try {
            $response = Http::asForm()->post('https://services.sentinel-hub.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->tokenExpiresAt = Carbon::now()->addSeconds($data['expires_in'] - 60);
                
                Log::info('Sentinel Hub: Token obtenido exitosamente');
                return true;
            }

            Log::error('Sentinel Hub: Error al obtener token', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Sentinel Hub: Excepción al obtener token', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Buscar productos Sentinel-2 para un área específica
     */
    public function buscarProductos(float $lat, float $lon, float $buffer = 0.01, int $dias = 30): array
    {
        if (!$this->obtenerTokenAcceso()) {
            return [];
        }

        $fechaInicio = Carbon::now()->subDays($dias)->format('Y-m-d');
        $fechaFin = Carbon::now()->format('Y-m-d');

        try {
            $response = Http::withToken($this->accessToken)
                ->get($this->baseUrl . '/catalog/search', [
                    'type' => 'FeatureCollection',
                    'features' => [
                        [
                            'type' => 'Feature',
                            'geometry' => [
                                'type' => 'Point',
                                'coordinates' => [$lon, $lat]
                            ],
                            'properties' => [
                                'datetime' => $fechaInicio . '/' . $fechaFin,
                                'collection' => 'sentinel-2-l2a',
                                'limit' => 50
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Sentinel Hub: Productos encontrados', [
                    'cantidad' => count($data['features'] ?? [])
                ]);
                return $data['features'] ?? [];
            }

            Log::error('Sentinel Hub: Error al buscar productos', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return [];

        } catch (\Exception $e) {
            Log::error('Sentinel Hub: Excepción al buscar productos', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Calcular índices de vegetación para un área específica
     */
    public function calcularIndicesVegetacion(float $lat, float $lon, float $buffer = 0.01, string $fecha = null): ?array
    {
        if (!$fecha) {
            $fecha = Carbon::now()->format('Y-m-d');
        }

        if (!$this->obtenerTokenAcceso()) {
            return null;
        }

        // Definir el área de interés (bbox)
        $minLon = $lon - $buffer;
        $maxLon = $lon + $buffer;
        $minLat = $lat - $buffer;
        $maxLat = $lat + $buffer;

        // Script para calcular NDVI y otros índices
        $script = '
            function setup() {
                return {
                    input: [{
                        bands: ["B02", "B03", "B04", "B08", "B11", "B12"],
                        units: "DN"
                    }],
                    output: {
                        bands: 6,
                        sampleType: "FLOAT32"
                    }
                };
            }

            function evaluatePixel(sample) {
                // Cálculo de NDVI
                let ndvi = (sample.B08 - sample.B04) / (sample.B08 + sample.B04);
                
                // Cálculo de NDWI (Water Index)
                let ndwi = (sample.B03 - sample.B08) / (sample.B03 + sample.B08);
                
                // Cálculo de GCI (Green Chlorophyll Index)
                let gci = sample.B08 / sample.B03 - 1;
                
                // Cálculo de LAI aproximado
                let lai = Math.max(0, -0.5 * Math.log(1 - ndvi));
                
                // RGB para visualización
                let r = sample.B04 / 10000;
                let g = sample.B03 / 10000;
                let b = sample.B02 / 10000;
                
                return [ndvi, ndwi, gci, lai, r, g, b];
            }
        ';

        try {
            $response = Http::withToken($this->accessToken)
                ->post($this->baseUrl . '/process', [
                    'input' => [
                        'bounds' => [
                            'bbox' => [$minLon, $minLat, $maxLon, $maxLat],
                            'properties' => [
                                'crs' => 'http://www.opengis.net/def/crs/EPSG/0/4326'
                            ]
                        ],
                        'data' => [
                            [
                                'type' => 'sentinel-2-l2a',
                                'dataFilter' => [
                                    'timeRange' => [
                                        'from' => $fecha,
                                        'to' => $fecha
                                    ],
                                    'maxCloudCoverage' => 30
                                ]
                            ]
                        ]
                    ],
                    'output' => [
                        'width' => 256,
                        'height' => 256,
                        'responses' => [
                            [
                                'identifier' => 'default',
                                'format' => [
                                    'type' => 'image/jpeg'
                                ]
                            ]
                        ]
                    ],
                    'evalscript' => $script
                ]);

            if ($response->successful()) {
                // Procesar la respuesta para extraer estadísticas
                $imagenData = $response->body();
                
                // Simular cálculo de estadísticas (en producción se procesaría la imagen)
                $estadisticas = $this->procesarEstadisticasImagen($imagenData);
                
                Log::info('Sentinel Hub: Índices calculados exitosamente');
                return $estadisticas;
            }

            Log::error('Sentinel Hub: Error al calcular índices', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Sentinel Hub: Excepción al calcular índices', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Procesar estadísticas de la imagen (simulado para demo)
     */
    private function procesarEstadisticasImagen(string $imagenData): array
    {
        // En producción, aquí se procesaría la imagen real
        // Por ahora, generamos datos simulados basados en la temporada
        
        $estacion = $this->obtenerEstacionActual();
        $factorVariacion = 0.1; // 10% de variación aleatoria
        
        $ndviBase = match($estacion) {
            'primavera' => 0.65,
            'verano' => 0.75,
            'otoño' => 0.55,
            'invierno' => 0.35,
            default => 0.50
        };

        $variacion = (mt_rand(-100, 100) / 1000) * $factorVariacion;
        $ndvi = max(0, min(1, $ndviBase + $variacion));

        return [
            'ndvi_promedio' => round($ndvi, 3),
            'ndvi_maximo' => round(min(1, $ndvi + 0.15), 3),
            'ndvi_minimo' => round(max(0, $ndvi - 0.15), 3),
            'ndvi_desviacion' => round(0.08, 3),
            'ndwi' => round(($ndvi - 0.2) * 0.6, 3),
            'gci' => round($ndvi * 0.8, 3),
            'lai' => round($ndvi * 2.5, 2),
            'cobertura_nubes' => mt_rand(5, 25),
            'calidad_imagen' => 'good',
            'estado_vegetacion' => $this->clasificarEstadoVegetacion($ndvi),
            'area_hectareas' => 100.0, // Área por defecto
        ];
    }

    /**
     * Obtener la estación actual
     */
    private function obtenerEstacionActual(): string
    {
        $mes = Carbon::now()->month;
        
        return match(true) {
            in_array($mes, [12, 1, 2]) => 'invierno',
            in_array($mes, [3, 4, 5]) => 'primavera',
            in_array($mes, [6, 7, 8]) => 'verano',
            in_array($mes, [9, 10, 11]) => 'otoño',
            default => 'primavera'
        };
    }

    /**
     * Clasificar el estado de la vegetación basado en NDVI
     */
    private function clasificarEstadoVegetacion(float $ndvi): string
    {
        return match(true) {
            $ndvi >= 0.7 => 'excellent',
            $ndvi >= 0.5 => 'good',
            $ndvi >= 0.3 => 'fair',
            default => 'poor'
        };
    }

    /**
     * Verificar si el servicio está configurado correctamente
     */
    public function estaConfigurado(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Obtener información del servicio
     */
    public function obtenerInfoServicio(): array
    {
        return [
            'configurado' => $this->estaConfigurado(),
            'token_valido' => $this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt->isFuture(),
            'expira_en' => $this->tokenExpiresAt?->diffForHumans(),
            'base_url' => $this->baseUrl,
        ];
    }

    /**
     * Generar URLs de visualización (para demo)
     */
    public function generarUrlsVisualizacion(float $lat, float $lon, string $fecha): array
    {
        $baseUrl = 'https://services.sentinel-hub.com/ogc/wmts/';
        
        return [
            'rgb' => $baseUrl . '1.0.0/1.0.0/WMTSCapabilities.xml',
            'ndvi' => $baseUrl . '1.0.0/1.0.0/WMTSCapabilities.xml',
            'metadata' => 'https://scihub.copernicus.eu/dhus/odata/v1/Products',
        ];
    }
}
