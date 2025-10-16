<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadProductiva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Obtiene las ubicaciones de las unidades productivas con sus productores asociados.
     * Puede filtrar por productor si se proporciona el parámetro 'productor'.
     */
    public function getLocations(Request $request): JsonResponse
    {
        $query = UnidadProductiva::with('productores')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud');

        // Filtrar por productor si se proporciona el parámetro
        if ($request->has('productor') && $request->get('productor')) {
            $query->whereHas('productores', function($q) use ($request) {
                $q->where('productors.id', $request->get('productor'));
            });
        }

        $unidadesProductivas = $query->get();

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