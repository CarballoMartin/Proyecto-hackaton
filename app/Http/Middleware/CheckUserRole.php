<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            // Si no está autenticado, redirige al login
            return redirect('login');
        }

        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Comprueba si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->rol, $roles)) {
            abort(403, 'Acceso no autorizado.');
        }
        
        return $next($request);
    }
}
