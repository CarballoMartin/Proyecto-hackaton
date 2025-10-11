<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Productor\CreateProductor;
use App\Actions\Productor\UpdateProductor;
use App\Http\Controllers\Controller;
use App\Models\Productor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductorController extends Controller
{
    /**
     * Muestra la página de listado de productores.
     *
     * @return \Illuminate\View\View
     */
    public function listado()
    {
        return view('admin.productores.listado');
    }

    /**
     * Muestra el panel de funciones de productores.
     *
     * @return \Illuminate\View\View
     */
    public function panel()
    {
        return view('admin.productores.panel');
    }

    /**
     * Muestra el formulario para importar productores.
     *
     * @return \Illuminate\View\View
     */
    public function formularioImportacion()
    {
        return view('admin.productores.importar');
    }

    public function store(Request $request, CreateProductor $creator)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'dni' => 'required|string|max:10|unique:productors,dni',
                'cuil' => 'nullable|string|max:20|unique:productors,cuil',
                'email' => 'required|email|max:255|unique:users,email',
                'telefono' => [
                    'required',
                    'string',
                    'max:25',
                    function ($attribute, $value, $fail) {
                        $sanitized = preg_replace('/[\s\-()+\/]/', '', $value);
                        if (!ctype_digit($sanitized)) {
                            $fail('El formato del teléfono no es válido. Solo se permiten números y caracteres como +, -, (, ).');
                            return;
                        }
                        if (strlen($sanitized) < 10 || strlen($sanitized) > 13) {
                            $fail('El número de teléfono debe tener entre 10 y 13 dígitos.');
                        }
                    },
                ],
                'municipio' => 'required|string|max:255',
                'paraje' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
            ]);

            $creator->ejecutar($validatedData);

            return response()->json(['message' => 'Productor registrado exitosamente.']);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            Log::error('Error al guardar productor: ' . $e->getMessage());
            return response()->json(['message' => 'Ha ocurrido un error inesperado al guardar el productor.'], 500);
        }
    }

    /**
     * Muestra los datos de un productor específico para edición.
     *
     * @param  \App\Models\Productor $productor
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Productor $productor)
    {
        $productor->load('usuario');
        return response()->json([
            'id' => $productor->id,
            'nombre' => $productor->nombre,
            'dni' => $productor->dni,
            'cuil' => $productor->cuil,
            'telefono' => $productor->telefono,
            'municipio' => $productor->municipio,
            'paraje' => $productor->paraje,
            'direccion' => $productor->direccion,
            'email' => $productor->usuario->email ?? '',
        ]);
    }

    /**
     * Actualiza los datos de un productor.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Productor $productor
     * @param  \App\Actions\Productor\UpdateProductor $updater
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Productor $productor, UpdateProductor $updater)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $productor->usuario->id,
                'dni' => 'required|string|max:10|unique:productors,dni,' . $productor->id,
                'cuil' => 'nullable|string|max:20|unique:productors,cuil,' . $productor->id,
                'telefono' => 'required|string|max:20',
                'municipio' => 'required|string|max:255',
                'paraje' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
            ]);

            $updater->ejecutar($productor->id, $validatedData);

            return response()->json([
                'message' => 'Productor actualizado exitosamente.',
                'dispatch_event' => 'productorSaved'
            ]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            Log::error('Error al actualizar productor: ' . $e->getMessage());
            return response()->json(['message' => 'Ha ocurrido un error inesperado al actualizar el productor.'], 500);
        }
    }
}
