<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesWeatherData;

class ClimaController extends Controller
{
    use ManagesWeatherData;

    /**
     * Devuelve los datos del clima más recientes para todos los municipios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $weatherData = $this->getAllWeatherData();
        return response()->json($weatherData);
    }
}
