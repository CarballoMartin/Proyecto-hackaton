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
        // Excluir rutas que no necesitan verificación de rol
        $excludedRoutes = ['logout', 'login', 'register', 'password.reset', 'password.confirm'];
        
        foreach ($excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        if (!Auth::check()) {
            // Si no está autenticado, redirige al login
            return redirect()->route('login');
        }

        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Verificar que el usuario tenga un rol válido
        if (!$user->rol) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Usuario sin rol asignado. Contactá a un administrador.'
            ]);
        }

        // Comprueba si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->rol, $roles)) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'No tienes permisos para acceder a esta sección.'
            ]);
        }
        
        return $next($request);
    }
}
