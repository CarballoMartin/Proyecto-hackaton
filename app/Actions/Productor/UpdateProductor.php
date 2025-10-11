<?php

namespace App\Actions\Productor;

use App\Models\Productor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class UpdateProductor
{
    /**
     * Actualiza un Productor y su User asociado.
     *
     * @param int $productorId El ID del productor a actualizar.
     * @param array $data Los datos validados para la actualizaciÃ³n.
     * @return void
     * @throws Throwable
     */
    public function ejecutar(int $productorId, array $data): void
    {
        DB::transaction(function () use ($productorId, $data) {
            $productor = Productor::findOrFail($productorId);
            $user = $productor->usuario;

            // 1. Actualizar el usuario
            $user->update([
                'name' => $data['nombre'],
                'email' => $data['email'],
            ]);

            // 2. Actualizar el productor
            $productor->update([
                'nombre' => $data['nombre'],
                'dni' => $data['dni'],
                'cuil' => $data['cuil'] ?? null,
                'telefono' => $data['telefono'],
                'municipio' => $data['municipio'],
                'paraje' => $data['paraje'] ?? null,
                'direccion' => $data['direccion'] ?? null,
            ]);

            
        });
    }
}
