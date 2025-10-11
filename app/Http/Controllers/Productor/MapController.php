<?php

namespace App\Http\Controllers\Productor;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Location\Coordinate;
use Location\Polygon;

class MapController extends Controller
{
    /**
     * Display the map for selecting a location.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show()
    {
        $formData = Session::get('form_data');

        if (!$formData || !isset($formData['municipio_id'])) {
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'Por favor, complete el Paso 1 antes de seleccionar la ubicación.');
        }

        $municipio = Municipio::find($formData['municipio_id']);
        if (!$municipio) {
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'El municipio seleccionado no es válido.');
        }

        // Create a simple object or array to pass to the view, mimicking the old structure if necessary.
        $viewData = [
            'latitud' => $formData['latitud'] ?? null,
            'longitud' => $formData['longitud'] ?? null,
            'municipio' => $municipio,
        ];

        $municipios = Municipio::whereNotNull('latitud')->whereNotNull('longitud')->get();

        return view('productor.map.ubicar-up', [
            'unidadProductiva' => (object)$viewData, // Cast to object to allow -> access in blade
            'municipios' => $municipios
        ]);
    }

    /**
     * Store the selected location in the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $formData = Session::get('form_data');
        if (!$formData) {
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'Su sesión ha expirado. Por favor, comience de nuevo.');
        }

        $municipio = Municipio::find($formData['municipio_id']);

        if ($municipio && $municipio->geojson_boundary) {
            try {
                $geometry = json_decode($municipio->geojson_boundary, true, 512, JSON_THROW_ON_ERROR);
                $userPoint = new Coordinate($validated['latitud'], $validated['longitud']);
                $isInside = false;

                if (empty($geometry['type']) || empty($geometry['coordinates'])) {
                    // Invalid geometry, skip validation
                } else {
                    if ($geometry['type'] === 'Polygon') {
                        $polygon = new Polygon();
                        foreach ($geometry['coordinates'][0] as $coord) {
                            $polygon->addPoint(new Coordinate($coord[1], $coord[0]));
                        }
                        if ($polygon->contains($userPoint)) {
                            $isInside = true;
                        }
                    } elseif ($geometry['type'] === 'MultiPolygon') {
                        foreach ($geometry['coordinates'] as $polygonCoordinates) {
                            $polygon = new Polygon();
                            foreach ($polygonCoordinates[0] as $coord) {
                                $polygon->addPoint(new Coordinate($coord[1], $coord[0]));
                            }
                            if ($polygon->contains($userPoint)) {
                                $isInside = true;
                                break;
                            }
                        }
                    }

                    if (!$isInside) {
                        return redirect()->back()->with('error', 'La ubicación seleccionada no parece estar dentro de los límites del municipio (' . $municipio->nombre . ') que seleccionó en el Paso 1. Por favor, ajuste la ubicación o regrese al paso anterior para cambiar el municipio.');
                    }
                }
            } catch (\JsonException $e) {
                Log::warning("Error al decodificar geojson_boundary para el municipio ID: {$municipio->id}", ['error' => $e->getMessage()]);
            }
        }

        // Update form data in session
        $formData['latitud'] = $validated['latitud'];
        $formData['longitud'] = $validated['longitud'];
        $formData['step'] = 2; // Mark step 2 as complete
        Session::put('form_data', $formData);

        return redirect()->route('productor.unidades-productivas.create.step2')
            ->with('message', 'Ubicación guardada correctamente.');
    }
}