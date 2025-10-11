<?php

namespace App\Http\Controllers\Productor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Municipio;
use App\Models\CondicionTenencia;
use App\Models\TipoIdentificador;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;
use App\Models\Paraje;
use App\Actions\Campos\CreateUnidadProductiva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class UnidadProductivaController extends Controller
{
    public function createStep1(Request $request)
    {
        $formData = $request->session()->get('form_data', []);

        $defaultCode = config('ovino.default_identifier_code', 'RNSPA');
        $defaultIdentificador = TipoIdentificador::where('nombre', $defaultCode)->first();

        return view('productor.unidades-productivas.create.step-1', [
            'formData' => $formData,
            'municipios' => Municipio::all(),
            'condiciones_tenencia' => CondicionTenencia::all(),
            'tipo_identificador_nombre' => $defaultIdentificador->nombre ?? ''
        ]);
    }

    public function storeStep1(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'identificador_local' => 'required|string|max:50',
            'superficie' => 'required|numeric|min:0',
            'habita' => 'nullable|boolean',
            'municipio_id' => 'required|exists:municipios,id',
            'paraje_id' => 'nullable', // Validation for new paraje will be handled in the final store method
            'condicion_tenencia_id' => 'required|exists:condicion_tenencias,id',
        ]);

        $validatedData['habita'] = $request->has('habita');
        $validatedData['tipo_identificador_id'] = TipoIdentificador::where('nombre', config('ovino.default_identifier_code', 'RNSPA'))->first()->id;

        $request->session()->put('form_data', $validatedData);

        return redirect()->route('productor.unidades-productivas.create.step2');
    }

    public function createStep2(Request $request)
    {
        $formData = $request->session()->get('form_data');

        if (empty($formData)) {
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'Por favor, complete el Paso 1 primero.');
        }

        return view('productor.unidades-productivas.create.step-2', [
            'formData' => $formData,
        ]);
    }

    public function createStep3(Request $request)
    {
        $formData = $request->session()->get('form_data');

        // Although location is optional, we ensure they came from step 2
        if (empty($formData)) {
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'Por favor, complete los pasos en orden.');
        }

        return view('productor.unidades-productivas.create.step-3', [
            'formData' => $formData,
            'fuentes_agua' => FuenteAgua::all(),
            'tipos_pasto' => TipoPasto::all(),
            'tipos_suelo' => TipoSuelo::all(),
        ]);
    }

    public function store(Request $request)
    {
        $formData = $request->session()->get('form_data', []);
        $step3Data = $request->validate([
            'agua_humano_fuente_id' => 'nullable|exists:fuente_aguas,id',
            'agua_humano_en_casa' => 'nullable|boolean',
            'agua_humano_distancia' => 'nullable|integer|min:0',
            'agua_animal_fuente_id' => 'nullable|exists:fuente_aguas,id',
            'agua_animal_distancia' => 'nullable|integer|min:0',
            'tipo_pasto_predominante_id' => 'nullable|exists:tipo_pastos,id',
            'tipo_suelo_predominante_id' => 'nullable|exists:tipo_suelos,id',
            'forrajeras_predominante' => 'nullable|boolean',
            'observaciones' => 'nullable|string',
        ]);

        $step3Data['agua_humano_en_casa'] = $request->has('agua_humano_en_casa');
        $step3Data['forrajeras_predominante'] = $request->has('forrajeras_predominante');

        $fullFormData = array_merge($formData, $step3Data);

        // Handle temporary paraje if it exists
        if (!is_numeric($fullFormData['paraje_id']) && !empty($fullFormData['paraje_id'])) {
            $paraje = Paraje::firstOrCreate(
                ['municipio_id' => $fullFormData['municipio_id'], 'nombre' => $fullFormData['paraje_id']]
            );
            $fullFormData['paraje_id'] = $paraje->id;
        }

        try {
            $action = app(CreateUnidadProductiva::class);
            $action->handle($fullFormData, Auth::user()->productor);

            $request->session()->forget('form_data');
            session()->flash('message', 'Unidad productiva registrada exitosamente.');
            return redirect()->route('productor.unidades-productivas.index');

        } catch (Throwable $e) {
            Log::error('Error en el guardado final de la UP: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('productor.unidades-productivas.create')->with('error', 'Ocurrió un error al guardar la unidad productiva. Por favor, intente de nuevo.');
        }
    }
}
