<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoggerService
{
    /**
     * Registra una acción en la tabla logs.
     *
     * @param string $accion       Acción realizada (ej: 'login', 'crear', 'editar', 'eliminar')
     * @param string|null $modelo  Modelo afectado (ej: 'Campo')
     * @param int|null $modeloId   ID del registro afectado
     * @param string|null $descripcion Texto descriptivo
     * @param int|null $userId     ID del usuario (opcional, si no hay usuario autenticado)
     * @return \App\Models\Log
     */
    public static function log(
        string $accion,
        ?string $modelo = null,
        ?int $modeloId = null,
        ?string $descripcion = null,
        ?int $userId = null
    ): Log {
        return Log::create([
            'user_id'    => $userId ?? Auth::id(),
            'accion'     => $accion,
            'modelo'     => $modelo,
            'modelo_id'  => $modeloId,
            'descripcion'=> $descripcion,
            'ip'         => Request::ip(),
            'navegador'  => Request::header('User-Agent'),
        ]);
    }
}
