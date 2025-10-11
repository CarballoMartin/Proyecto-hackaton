<?php

namespace App\Http\Controllers\Traits;

use App\Models\Clima;
use App\Models\Municipio;
use Illuminate\Support\Facades\Log;

trait ManagesWeatherData
{
    public function getAllWeatherData(): array
    {
        // 1. Encontrar el ID del último registro de clima para cada municipio.
        $latestClimaIds = Clima::selectRaw('max(id) as id')
            ->groupBy('localidad_id');

        // 2. Obtener los registros de clima completos usando esos IDs y cargar el municipio asociado.
        $climas = Clima::joinSub($latestClimaIds, 'latest_clima', function ($join) {
                $join->on('clima.id', '=', 'latest_clima.id');
            })
            ->with('municipio')
            ->get();

        // 3. Mapear los resultados al formato que espera la vista.
        return $climas->map(function ($clima) {
            if (!$clima->municipio) {
                return null; // Omitir si no hay municipio asociado
            }

            $datosClima = json_decode($clima->datos_json);

            if (!$datosClima || !isset($datosClima->main)) {
                return [
                    'id' => $clima->municipio->id,
                    'location' => $clima->municipio->nombre,
                    'temperature' => '--',
                    'condition' => 'Sin datos',
                    'high' => '--',
                    'low' => '--',
                    'icon' => 'heroicon-o-exclamation-circle',
                    'iconColor' => 'text-gray-400'
                ];
            }

            $iconInfo = $this->getWeatherIcon($datosClima->weather[0]->icon ?? '01d');

            return [
                'id' => $clima->municipio->id,
                'location' => $clima->municipio->nombre,
                'temperature' => round($datosClima->main->temp),
                'condition' => ucfirst($datosClima->weather[0]->description ?? '--'),
                'high' => round($datosClima->main->temp_max),
                'low' => round($datosClima->main->temp_min),
                'icon' => $iconInfo['icon'],
                'iconColor' => $iconInfo['color']
            ];
        })->filter()->sortBy('location')->values()->all();
    }

    public function getWeatherDataForMunicipio(?Municipio $municipio): array
    {
        Log::debug('ManagesWeatherData: getWeatherDataForMunicipio called.', ['municipio_id' => $municipio ? $municipio->id : null]);

        $defaultData = [
            'location' => $municipio ? $municipio->nombre : 'No disponible',
            'temperature' => '--',
            'condition' => '--',
            'high' => '--',
            'low' => '--',
            'icon' => 'heroicon-o-exclamation-circle',
            'iconColor' => 'text-gray-400'
        ];

        if (!$municipio) {
            Log::debug('ManagesWeatherData: Municipio is null, returning default data.');
            return $defaultData;
        }

        $clima = Clima::where('localidad_id', $municipio->id)->latest('fecha_hora_consulta')->first();

        if (!$clima) {
            Log::debug('ManagesWeatherData: Clima record not found, returning default data.', ['municipio_id' => $municipio->id]);
            return $defaultData;
        }
        Log::debug('ManagesWeatherData: Found clima record.', ['clima_id' => $clima->id]);

        $datosClima = json_decode($clima->datos_json);

        if (!$datosClima || !isset($datosClima->main)) {
            Log::debug('ManagesWeatherData: JSON data is invalid or missing main section.', ['clima_id' => $clima->id]);
            return $defaultData;
        }

        Log::debug('ManagesWeatherData: Successfully decoded JSON, preparing final data.');
        $iconInfo = $this->getWeatherIcon($datosClima->weather[0]->icon ?? '01d');

        return [
            'id' => $municipio->id,
            'location' => $datosClima->name ?? $municipio->nombre,
            'temperature' => round($datosClima->main->temp),
            'condition' => ucfirst($datosClima->weather[0]->description ?? '--'),
            'high' => round($datosClima->main->temp_max),
            'low' => round($datosClima->main->temp_min),
            'icon' => $iconInfo['icon'],
            'iconColor' => $iconInfo['color']
        ];
    }

    public function getWeatherIcon(string $iconCode): array
    {
        $map = [
            '01d' => ['icon' => 'heroicon-s-sun', 'color' => 'text-yellow-500'],
            '01n' => ['icon' => 'heroicon-s-moon', 'color' => 'text-indigo-400'],
            '02d' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-400'],
            '02n' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-400'],
            '03d' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-500'],
            '03n' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-500'],
            '04d' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-600'],
            '04n' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-gray-600'],
            '09d' => ['icon' => 'heroicon-s-cloud-arrow-down', 'color' => 'text-blue-500'],
            '09n' => ['icon' => 'heroicon-s-cloud-arrow-down', 'color' => 'text-blue-500'],
            '10d' => ['icon' => 'heroicon-s-cloud-arrow-down', 'color' => 'text-cyan-500'],
            '10n' => ['icon' => 'heroicon-s-cloud-arrow-down', 'color' => 'text-cyan-500'],
            '11d' => ['icon' => 'heroicon-s-bolt', 'color' => 'text-yellow-600'],
            '11n' => ['icon' => 'heroicon-s-bolt', 'color' => 'text-yellow-600'],
            '13d' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-light-blue-400'],
            '13n' => ['icon' => 'heroicon-s-cloud', 'color' => 'text-light-blue-400'],
            '50d' => ['icon' => 'heroicon-s-bars-3', 'color' => 'text-gray-400'],
            '50n' => ['icon' => 'heroicon-s-bars-3', 'color' => 'text-gray-400'],
        ];

        return $map[$iconCode] ?? $map['01d'];
    }
}