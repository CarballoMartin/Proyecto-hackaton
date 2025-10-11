<?php

namespace App\Actions\Productor;

use App\Events\ProductorCreated;
use App\Models\User;
use App\Models\Productor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class CreateProductor
{
    /**
     * Crea un nuevo Productor, su User asociado y dispara un evento.
     * La acción confía en que los datos ya han sido validados por el componente.
     *
     * @param array $data Los datos validados para la creación.
     * @return void
     * @throws Throwable
     */
    public function ejecutar(array $data): void
    {
        DB::transaction(function () use ($data) {
            // 1. Crear el usuario con una contraseña interna y segura (no se usará para login)
            $user = User::create([
                'name' => $data['nombre'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(32)), // Contraseña segura y desconocida
                'rol' => 'productor',
                'activo' => true,
                'verificado' => true, // Se asume verificado al ser creado por un admin
            ]);

            // 2. Crear el productor
            $productor = Productor::create([
                'usuario_id' => $user->id,
                'nombre' => $data['nombre'],
                'dni' => $data['dni'],
                'cuil' => $data['cuil'] ?? null,
                'telefono' => $data['telefono'],
                'municipio' => $data['municipio'],
                'paraje' => $data['paraje'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'activo' => true,
            ]);

            // 3. Disparar el evento solo con el usuario (sin contraseña)
            ProductorCreated::dispatch($user);
        });
    }
}
