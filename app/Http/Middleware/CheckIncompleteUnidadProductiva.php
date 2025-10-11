<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use Symfony\Component\HttpFoundation\Response;

class CheckIncompleteUnidadProductiva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si el usuario está autenticado y es un productor
        if (Auth::check() && $request->user()->rol === 'productor') {
            
            // 2. Obtener el perfil del productor
            $productor = Productor::where('usuario_id', $request->user()->id)->first();

            if ($productor) {
                // 3. Buscar si tiene al menos una unidad productiva incompleta (activa = false)
                $hasIncompleteUP = UnidadProductiva::whereHas('productores', function ($query) use ($productor) {
                    $query->where('productor_id', $productor->id);
                })->where('activo', false)->exists();

                // 4. Si tiene una UP incompleta y no está ya en la página de creación, redirigir
                if ($hasIncompleteUP && !$request->routeIs('productor.unidades-productivas.create')) {
                    return redirect()->route('productor.unidades-productivas.create')
                        ->with('warning', 'Por favor, complete el registro de su unidad productiva para continuar.');
                }
            }
        }

        return $next($request);
    }
}