<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesWeatherData;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductorClimaController extends Controller
{
    use ManagesWeatherData;

    /**
     * Devuelve los datos del clima más recientes para los municipios
     * de las unidades productivas del productor autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::debug('ProductorClimaController: index method started.');

        $user = Auth::user();
        if (!$user) {
            Log::debug('ProductorClimaController: Unauthenticated user.');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        Log::debug('ProductorClimaController: User authenticated.', ['user_id' => $user->id]);

        $productor = Productor::with('unidadesProductivas.municipio')
                              ->where('usuario_id', $user->id)
                              ->first();

        if (!$productor) {
            Log::debug('ProductorClimaController: Productor not found for user.', ['user_id' => $user->id]);
            return response()->json([]);
        }
        Log::debug('ProductorClimaController: Productor found.', ['productor_id' => $productor->id]);

        $municipios = $productor->unidadesProductivas->map(function ($up) {
            return $up->municipio;
        })->filter()->unique('id');
        Log::debug('ProductorClimaController: Found municipios.', ['count' => $municipios->count()]);

        $weatherData = $municipios->map(function ($municipio) {
            Log::debug('ProductorClimaController: Mapping municipio.', ['municipio_id' => $municipio ? $municipio->id : null]);
            $data = $this->getWeatherDataForMunicipio($municipio);
            Log::debug('ProductorClimaController: Got weather data for municipio.', ['municipio_id' => $municipio ? $municipio->id : null]);
            return $data;
        })->filter()->values();

        Log::debug('ProductorClimaController: Finished processing. Returning weather data.');
        return response()->json($weatherData);
    }
}