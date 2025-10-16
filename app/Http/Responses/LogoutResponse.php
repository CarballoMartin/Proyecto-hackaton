<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        // Limpiar todas las sesiones
        session()->flush();
        
        // Invalidar la sesión actual
        $request->session()->invalidate();
        
        // Regenerar el token CSRF
        $request->session()->regenerateToken();
        
        // Redirigir al login con un mensaje de confirmación
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}





