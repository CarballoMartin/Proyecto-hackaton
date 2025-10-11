<?php

namespace App\Actions\Campos;

use App\Models\Campo;
use Illuminate\Support\Facades\DB;

class CreateCampo
{
    /**
     * Crea un nuevo campo.
     *
     * @param array $data Los datos validados para la creación.
     * @return Campo
     */
    public function ejecutar(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Aquí podrías realizar alguna lógica adicional antes de crear la unidad productiva
        });
    }
}