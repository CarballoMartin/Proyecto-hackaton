<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadProductiva;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    /**
     * Obtiene las ubicaciones de las unidades productivas con sus productores asociados.
     */
    public function getLocations(): JsonResponse
    {
        $unidadesProductivas = UnidadProductiva::with('productores')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();

        $locations = $unidadesProductivas->map(function ($unidadProductiva) {
            return [
                'id' => $unidadProductiva->id,
                'rnspa' => $unidadProductiva->identificador_local,
                'superficie' => $unidadProductiva->superficie,
                'coordenadas' => [
                    'lat' => (float) $unidadProductiva->latitud,
                    'lng' => (float) $unidadProductiva->longitud,
                ],
                'productores' => $unidadProductiva->productores->map(function ($productor) {
                    return [
                        'id' => $productor->id,
                        'nombre' => $productor->nombre,
                    ];
                }),
            ];
        });

        return response()->json($locations);
    }
}