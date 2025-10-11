<?php

namespace App\Http\Controllers\Productor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParajeController extends Controller
{
    public function validarTemporal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Validación exitosa.']);
    }
}
