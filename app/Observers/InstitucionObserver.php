<?php

namespace App\Observers;

use App\Models\Institucion;
use App\Services\LoggerService;
use Illuminate\Support\Facades\Auth;

class InstitucionObserver
{
    /**
     * Handle the Institucion "created" event.
     */
    public function created(Institucion $institucion): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'un proceso automático';

        LoggerService::log(
            'creacion_institucion',
            'Institucion',
            $institucion->id,
            "Usuario {$userName} registró la institución '{$institucion->nombre}' (ID: {$institucion->id})."
        );
    }
}
