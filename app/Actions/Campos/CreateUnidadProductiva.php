<?php

namespace App\Actions\Campos;

use App\Models\Productor;
use App\Models\TipoIdentificador;
use App\Models\UnidadProductiva;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CreateUnidadProductiva
{
    /**
     * Crea o actualiza una UnidadProductiva y la asocia al productor.
     *
     * @param array $data Datos validados del formulario.
     * @param Productor $productor El productor al que se asociará la UP.
     * @return array ['unidad' => UnidadProductiva, 'created' => bool]
     * @throws ValidationException|Throwable
     */
    public function handle(array $data, Productor $productor): array
    {
        // Si no se provee un tipo de identificador, usar el de la configuración por defecto.
        if (!isset($data['tipo_identificador_id'])) {
            $defaultCode = config('ganaderia.default_identifier_code', 'RNSPA');
            $data['tipo_identificador_id'] = TipoIdentificador::where('nombre', $defaultCode)->value('id');
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'identificador_local' => ['required', 'string', 'max:255', 'unique:unidades_productivas,identificador_local', 'regex:/^\d{2}\.\d{3}\.\d\.\d{5}\/\d{2}$/'],
            'tipo_identificador_id' => 'required|exists:tipos_identificador,id',
            'superficie' => 'required|numeric|min:0',
            'habita' => 'boolean',
            'municipio_id' => 'required|exists:municipios,id',
            'paraje_id' => 'nullable|exists:parajes,id',
            'condicion_tenencia_id' => 'required|exists:condicion_tenencias,id',
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'observaciones' => 'nullable|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        // Lógica para estado incompleto/activo
        $validated['completo'] = !empty($validated['latitud']) && !is_null($validated['longitud']);
        $validated['activo'] = $validated['completo'];

        try {
            return DB::transaction(function () use ($validated, $productor) {
                $unidad = UnidadProductiva::create($validated);

                $condicionId = $validated['condicion_tenencia_id'];
                $unidad->productores()->syncWithoutDetaching([
                    $productor->id => ['condicion_tenencia_id' => $condicionId]
                ]);

                return [
                    'unidad' => $unidad,
                    'created' => $unidad->wasRecentlyCreated,
                ];
            });
        } catch (Throwable $e) {
            Log::error('CreateUnidadProductivaAction error: ' . $e->getMessage(), ['data' => $validated]);
            throw $e;
        }
    }
}
