<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InstitucionalSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar si el usuario tiene el rol institucional
        if ($user->rol !== 'institucional') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'No tienes permisos para acceder al panel institucional.'
            ]);
        }

        // Verificar si la cuenta está activa
        if (!$user->activo) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está desactivada. Contactá a un administrador.'
            ]);
        }

        // Regenerar el ID de sesión periódicamente para seguridad
        if ($request->session()->has('last_activity')) {
            $lastActivity = $request->session()->get('last_activity');
            $sessionLifetime = config('session.lifetime', 120) * 60; // Convertir a segundos
            
            if (time() - $lastActivity > $sessionLifetime) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.'
                ]);
            }
        }

        // Actualizar la última actividad
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}