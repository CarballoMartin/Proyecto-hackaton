<?php

namespace App\Services\ClimaApi;

use App\Models\UnidadProductiva;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NasaPowerService
{
    private const BASE_URL = 'https://power.larc.nasa.gov/api/temporal/daily/point';
    private const CACHE_TTL = 86400; // 24 horas
    
    /**
     * Obtiene datos de clima para una unidad productiva
     */
    public function obtenerDatosClima(UnidadProductiva $unidadProductiva): ?array
    {
        if (!$unidadProductiva->latitud || !$unidadProductiva->longitud) {
            Log::warning('Unidad productiva sin coordenadas', ['id' => $unidadProductiva->id]);
            return null;
        }

        $cacheKey = "clima_nasa.{$unidadProductiva->id}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unidadProductiva) {
            return $this->consultarDatosClima($unidadProductiva);
        });
    }

    /**
     * Consulta datos de clima directamente a la API de NASA POWER
     */
    private function consultarDatosClima(UnidadProductiva $unidadProductiva): ?array
    {
        try {
            $fechaInicio = Carbon::now()->subDays(7)->format('Ymd');
            $fechaFin = Carbon::now()->format('Ymd');

            $params = [
                'parameters' => 'T2M_MAX,T2M_MIN,T2M,PRECTOTCORR,RH2M,WS2M',
                'community' => 'AG',
                'longitude' => $unidadProductiva->longitud,
                'latitude' => $unidadProductiva->latitud,
                'start' => $fechaInicio,
                'end' => $fechaFin,
                'format' => 'JSON'
            ];

            $response = Http::timeout(30)->get(self::BASE_URL, $params);

            if ($response->successful()) {
                return $this->procesarRespuestaClima($response->json(), $unidadProductiva);
            } else {
                Log::error('Error en API NASA POWER', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Excepción en consulta clima NASA: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Procesa la respuesta de la API para extraer datos de clima
     */
    private function procesarRespuestaClima(array $response, UnidadProductiva $unidadProductiva): ?array
    {
        try {
            if (!isset($response['properties']['parameter'])) {
                Log::warning('Respuesta NASA POWER sin parámetros', ['response' => $response]);
                return null;
            }

            $parametros = $response['properties']['parameter'];
            
            // Extraer datos del último día disponible
            $fechas = array_keys($parametros['T2M']);
            $ultimaFecha = end($fechas);
            
            $temperaturaMaxima = $parametros['T2M_MAX'][$ultimaFecha] ?? 0;
            $temperaturaMinima = $parametros['T2M_MIN'][$ultimaFecha] ?? 0;
            $temperaturaPromedio = $parametros['T2M'][$ultimaFecha] ?? 0;
            $precipitacion24h = $parametros['PRECTOTCORR'][$ultimaFecha] ?? 0;
            $humedadRelativa = $parametros['RH2M'][$ultimaFecha] ?? 0;
            $vientoVelocidad = $parametros['WS2M'][$ultimaFecha] ?? 0;

            // Calcular precipitación de 7 días
            $precipitacion7dias = 0;
            $diasSinLluvia = 0;
            foreach ($fechas as $fecha) {
                $precip = $parametros['PRECTOTCORR'][$fecha] ?? 0;
                $precipitacion7dias += $precip;
                if ($precip < 1) { // Menos de 1mm considerado como sin lluvia
                    $diasSinLluvia++;
                }
            }

            // Determinar alertas
            $estresTermico = $temperaturaMaxima > config('ambiental.alertas.estres_termico_celsius', 35);
            $heladaRiesgo = $temperaturaMinima < config('ambiental.alertas.helada_celsius', 0);

            return [
                'temperatura_maxima' => round($temperaturaMaxima, 1),
                'temperatura_minima' => round($temperaturaMinima, 1),
                'temperatura_promedio' => round($temperaturaPromedio, 1),
                'precipitacion_24h' => round($precipitacion24h, 1),
                'precipitacion_7dias' => round($precipitacion7dias, 1),
                'precipitacion_total' => round($precipitacion7dias, 1),
                'humedad_relativa' => round($humedadRelativa, 1),
                'viento_velocidad' => round($vientoVelocidad, 1),
                'viento_direccion' => 'N/A', // NASA POWER no proporciona dirección
                'dias_sin_lluvia' => $diasSinLluvia,
                'estres_termico' => $estresTermico,
                'helada_riesgo' => $heladaRiesgo,
                'probabilidad_lluvia' => 0, // No disponible en NASA POWER
                'humedad_suelo' => 0, // No disponible en NASA POWER
                'rafagas_maximas' => round($vientoVelocidad * 1.3, 1), // Estimación
                'fecha_consulta' => Carbon::now()->format('Y-m-d'),
                'fuente_datos' => 'nasa_power',
            ];

        } catch (\Exception $e) {
            Log::error('Error procesando respuesta clima NASA: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene datos históricos de clima para una unidad productiva
     */
    public function obtenerHistorialClima(UnidadProductiva $unidadProductiva, int $dias = 30): array
    {
        $cacheKey = "clima_historico_nasa.{$unidadProductiva->id}.{$dias}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unidadProductiva, $dias) {
            return $this->consultarHistorialClima($unidadProductiva, $dias);
        });
    }

    /**
     * Consulta datos históricos de clima
     */
    private function consultarHistorialClima(UnidadProductiva $unidadProductiva, int $dias): array
    {
        try {
            $fechaInicio = Carbon::now()->subDays($dias)->format('Ymd');
            $fechaFin = Carbon::now()->format('Ymd');

            $params = [
                'parameters' => 'T2M_MAX,T2M_MIN,T2M,PRECTOTCORR,RH2M,WS2M',
                'community' => 'AG',
                'longitude' => $unidadProductiva->longitud,
                'latitude' => $unidadProductiva->latitud,
                'start' => $fechaInicio,
                'end' => $fechaFin,
                'format' => 'JSON'
            ];

            $response = Http::timeout(30)->get(self::BASE_URL, $params);

            if ($response->successful()) {
                return $this->procesarHistorialClima($response->json());
            } else {
                Log::error('Error en API NASA POWER histórico', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

        } catch (\Exception $e) {
            Log::error('Excepción en consulta clima histórico NASA: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Procesa respuesta de datos históricos
     */
    private function procesarHistorialClima(array $response): array
    {
        try {
            if (!isset($response['properties']['parameter'])) {
                return [];
            }

            $parametros = $response['properties']['parameter'];
            $historial = [];

            foreach ($parametros['T2M'] as $fecha => $temperatura) {
                $historial[] = [
                    'fecha' => Carbon::createFromFormat('Ymd', $fecha)->format('Y-m-d'),
                    'temperatura_maxima' => round($parametros['T2M_MAX'][$fecha] ?? 0, 1),
                    'temperatura_minima' => round($parametros['T2M_MIN'][$fecha] ?? 0, 1),
                    'temperatura_promedio' => round($temperatura, 1),
                    'precipitacion' => round($parametros['PRECTOTCORR'][$fecha] ?? 0, 1),
                    'humedad_relativa' => round($parametros['RH2M'][$fecha] ?? 0, 1),
                    'viento_velocidad' => round($parametros['WS2M'][$fecha] ?? 0, 1),
                ];
            }

            return $historial;

        } catch (\Exception $e) {
            Log::error('Error procesando historial clima NASA: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si el servicio está disponible
     */
    public function verificarDisponibilidad(): bool
    {
        try {
            $response = Http::timeout(10)->get('https://power.larc.nasa.gov/api/temporal/daily/point', [
                'parameters' => 'T2M',
                'community' => 'AG',
                'longitude' => -60,
                'latitude' => -30,
                'start' => Carbon::now()->format('Ymd'),
                'end' => Carbon::now()->format('Ymd'),
                'format' => 'JSON'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('NASA POWER no disponible: ' . $e->getMessage());
            return false;
        }
    }
}
