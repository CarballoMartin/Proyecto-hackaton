<?php

namespace App\Actions\Institucion;

use App\Models\Institucion;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateInstitucion
{
    /**
     * Actualiza una Institución.
     *
     * @param int $institucionId El ID de la institución a actualizar.
     * @param array $data Los datos validados para la actualización.
     * @return void
     * @throws Throwable
     */
    public function ejecutar(int $institucionId, array $data): void
    {
        DB::transaction(function () use ($institucionId, $data) {
            $institucion = Institucion::findOrFail($institucionId);

            // Actualizar la institución
            $institucion->update([
                'nombre' => $data['nombre'],
                'cuit' => $data['cuit'] ?? null,
                'contacto_email' => $data['contacto_email'],
                'localidad' => $data['localidad'] ?? null,
                'provincia' => $data['provincia'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
            ]);
        });
    }
}











