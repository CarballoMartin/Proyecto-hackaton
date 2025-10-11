<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\SmsServiceInterface;
use App\Mail\CodigoAccesoMail;
use App\Models\Productor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Busca un usuario productor por email o teléfono.
     */
    private function findProductorUserByIdentificador($identificador): ?User
    {
        if (filter_var($identificador, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identificador)->first();
            return ($user && $user->hasRole('productor')) ? $user : null;
        }

        $sanitizedIdentificador = preg_replace('/[^0-9]/', '', $identificador);
        if (empty($sanitizedIdentificador)) {
            return null;
        }

        $productor = Productor::whereRaw('REPLACE(REPLACE(REPLACE(telefono, "+", ""), "-", ""), " ", "") = ?', [$sanitizedIdentificador])->first();

        return $productor ? $productor->usuario : null;
    }

    /**
     * Maneja la solicitud de un código de acceso para iniciar sesión.
     */
    public function solicitarCodigo(Request $request, SmsServiceInterface $smsService)
    {
        $validator = Validator::make($request->all(), [
            'identificador' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $identificador = $request->input('identificador');
        $user = $this->findProductorUserByIdentificador($identificador);

        if ($user) {
            $codigo = random_int(100000, 999999);
            Cache::put('codigo_acceso_' . $user->id, $codigo, now()->addMinutes(10));

            if (filter_var($identificador, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)->send(new CodigoAccesoMail($codigo));
            } else {
                $telefono = $user->productor->telefono ?? null;
                if ($telefono) {
                    $smsService->sendSms($telefono, "Su código de acceso es: {$codigo}");
                }
            }
        }

        // Por seguridad, siempre devolvemos la misma respuesta
        return response()->json(['message' => 'Si su cuenta de productor existe, se ha enviado un código de acceso.']);
    }

    /**
     * Maneja el inicio de sesión con el código de acceso.
     */
    public function iniciarSesion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identificador' => 'required|string',
            'codigo' => 'required|string|min:6|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $identificador = $request->input('identificador');
        $codigo = $request->input('codigo');

        $user = $this->findProductorUserByIdentificador($identificador);

        if (!$user || !Cache::has('codigo_acceso_' . $user->id) || Cache::get('codigo_acceso_' . $user->id) != $codigo) {
            return response()->json(['error' => 'El código de acceso es incorrecto o ha expirado.'], 401);
        }

        Cache::forget('codigo_acceso_' . $user->id);

        $user->tokens()->delete(); // Invalida tokens anteriores
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only('id', 'name', 'email', 'rol') // Devolver solo datos seguros
        ]);
    }
}