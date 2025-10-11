<?php

namespace App\Actions\Campos;

use App\Models\Campo;
use Illuminate\Support\Facades\DB;

class UpdateCampo
{
    /**
     * Actualiza una unidad productiva existente.
     *
     * @param Campo $campo
     * @param array $data Los datos validados para la actualización.
     * @return Campo
     */
    
    public function update(Campo $campo, array $data): void
    {   
        DB::transaction(function () use ($campo, $data) {
            // Aquí podrías realizar alguna lógica adicional antes de actualizar
        });
    }
}