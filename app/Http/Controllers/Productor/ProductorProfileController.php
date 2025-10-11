<?php

namespace App\Http\Controllers\Productor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Productor\UpdateProductorProfile;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductorProfileController extends Controller
{
    /**
     * Fetch the producer's profile data.
     */
    public function show()
    {
        $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();
        $user = Auth::user();

        return response()->json([
            'nombre' => $productor->nombre,
            'fecha_nacimiento' => $productor->fecha_nacimiento,
            'dni' => $productor->dni,
            'cuil' => $productor->cuil,
            'municipio' => $productor->municipio,
            'paraje' => $productor->paraje,
            'direccion' => $productor->direccion,
            'telefono' => $productor->telefono,
            'email' => $user->email,
        ]);
    }

    /**
     * Update the producer's profile.
     */
    public function update(Request $request, UpdateProductorProfile $updater)
    {
        $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();

        try {
            $updater->handle($productor, $request->all());

            return response()->json([
                'message' => 'Perfil actualizado correctamente.'
            ]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => 'Ocurrió un error inesperado al actualizar el perfil.'], 500);
        }
    }
}
