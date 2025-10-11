<?php

namespace App\Actions\Institucion;

use App\Models\User;
use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Events\InstitucionCreated;
use Illuminate\Support\Facades\DB;

class CreateInstitucion
{
    public function ejecutar(array $datos): Institucion
    {
        // 1. Validar los datos de entrada
        Validator::make($datos, [
            'nombre' => ['required', 'string', 'max:255'],
            'cuit' => ['nullable', 'string', 'max:255', 'unique:instituciones'],
            'contacto_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Institucion::class, 'contacto_email'),
                Rule::unique(User::class, 'email'),
            ],
            'email_secundario' => ['nullable', 'email', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'localidad' => ['required', 'string', 'max:255'],
            'provincia' => ['required', 'string', 'max:255'],
        ], [
            'contacto_email.unique' => 'Este email ya está en uso por otra institución o usuario.',
        ])->validate();

        return DB::transaction(function () use ($datos) {
            // 2. Generar una contraseña temporal
            $passwordTemporal = Str::random(12);

            // 3. Crear la institución con todos los datos
            $institucion = Institucion::create([
                'nombre' => $datos['nombre'],
                'cuit' => $datos['cuit'] ?? null,
                'contacto_email' => $datos['contacto_email'],
                'email_secundario' => $datos['email_secundario'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
                'localidad' => $datos['localidad'] ?? null,
                'provincia' => $datos['provincia'] ?? null,
                'validada' => true,
            ]);

            // 4. Crear el usuario institucional asociado
            $user = User::create([
                'name' => $datos['nombre'] . ' (Admin Institucional)',
                'email' => $datos['contacto_email'],
                'password' => Hash::make($passwordTemporal),
                'rol' => User::ROL_INSTITUCIONAL,
                'activo' => true,
                'verificado' => true,
            ]);

            // 5. Vincular el usuario como 'admin' de la institución
            InstitucionalParticipante::create([
                'usuario_id' => $user->id,
                'institucion_id' => $institucion->id,
                'rol' => 'admin',
                'activo' => true,
            ]);

            // 6. Disparar el evento de creación
            InstitucionCreated::dispatch($institucion, $user, $passwordTemporal);

            return $institucion;
        });
    }
}