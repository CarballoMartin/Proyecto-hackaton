<?php

namespace App\Observers;

use App\Models\Productor;
use App\Services\LoggerService;
use Illuminate\Support\Facades\Auth;

class ProductorObserver
{
    /**
     * Handle the Productor "created" event.
     */
    public function created(Productor $productor): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'un proceso automático';

        LoggerService::log(
            'creacion_productor',
            'Productor',
            $productor->id,
            "Usuario {$userName} registró al productor '{$productor->nombre}' (ID: {$productor->id})."
        );
    }
}
