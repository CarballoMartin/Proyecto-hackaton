<?php

namespace App\Services\ClimaApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoApiService
{
    private string $baseUrl = 'https://api.open-meteo.com/v1';

    /**
     * Obtiene el pronóstico completo para una ubicación
     *
     * @param float $latitud
     * @param float $longitud
     * @param int $dias Días de pronóstico (default 7)
     * @return array|null
     */
    public function obtenerPronostico(float $latitud, float $longitud, int $dias = 7): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/forecast", [
                'latitude' => $latitud,
                'longitude' => $longitud,
                'current_weather' => 'true',
                'daily' => implode(',', [
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'precipitation_sum',
                    'precipitation_probability_max',
                    'windspeed_10m_max',
                ]),
                'timezone' => 'America/Argentina/Buenos_Aires',
                'forecast_days' => $dias,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Error en Open-Meteo API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Excepción en Open-Meteo API', [
                'mensaje' => $e->getMessage(),
                'latitud' => $latitud,
                'longitud' => $longitud,
            ]);

            return null;
        }
    }

    /**
     * Obtiene datos históricos
     *
     * @param float $latitud
     * @param float $longitud
     * @param string $fechaInicio Formato: YYYY-MM-DD
     * @param string $fechaFin Formato: YYYY-MM-DD
     * @return array|null
     */
    public function obtenerHistorico(float $latitud, float $longitud, string $fechaInicio, string $fechaFin): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/archive", [
                'latitude' => $latitud,
                'longitude' => $longitud,
                'start_date' => $fechaInicio,
                'end_date' => $fechaFin,
                'daily' => implode(',', [
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'precipitation_sum',
                ]),
                'timezone' => 'America/Argentina/Buenos_Aires',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error obteniendo datos históricos', [
                'mensaje' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Formatea los datos de la API al formato de nuestro modelo
     *
     * @param array $datosApi
     * @return array
     */
    public function formatearDatos(array $datosApi): array
    {
        $current = $datosApi['current_weather'] ?? [];
        $daily = $datosApi['daily'] ?? [];

        return [
            'fuente' => 'open_meteo',
            'temperatura_actual' => $current['temperature'] ?? null,
            'velocidad_viento' => $current['windspeed'] ?? null,
            'codigo_clima' => $current['weathercode'] ?? null,
            'temperaturas_max' => $daily['temperature_2m_max'] ?? [],
            'temperaturas_min' => $daily['temperature_2m_min'] ?? [],
            'precipitacion' => $daily['precipitation_sum'] ?? [],
            'probabilidad_lluvia' => $daily['precipitation_probability_max'] ?? [],
            'viento_max' => $daily['windspeed_10m_max'] ?? [],
            'fechas' => $daily['time'] ?? [],
            'datos_completos' => $datosApi,
            'fecha_consulta' => now(),
        ];
    }
}

