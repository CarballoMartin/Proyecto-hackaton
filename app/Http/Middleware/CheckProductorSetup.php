<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckProductorSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        Log::info('CheckProductorSetup: Running for User ID: ' . ($user ? $user->id : 'Guest') . ' on route ' . $request->path());

        if (!$user || !$user->hasRole('productor')) {
            Log::info('CheckProductorSetup: User is not a productor or is a guest. Passing through.');
            return $next($request);
        }

        $productor = $user->productor;
        if (!$productor) {
            Log::info('CheckProductorSetup: Productor model not found for User ID: ' . $user->id . '. Passing through.');
            return $next($request); 
        }

        $hasUnidadesProductivas = $productor->unidadesProductivas()->exists();
        Log::info('CheckProductorSetup: User ID: ' . $user->id . ' - Has UPs: ' . ($hasUnidadesProductivas ? 'Yes' : 'No'));

        // Si no tienen UPs, solo pueden acceder a la bienvenida y al formulario de creación.
        if (!$hasUnidadesProductivas && !$request->routeIs([
            'productor.welcome', 
            'productor.unidades-productivas.create', 
            'productor.unidades-productivas.store.step1',
            'productor.unidades-productivas.create.step2',
            'productor.unidades-productivas.create.step3',
            'productor.unidades-productivas.store',
            'productor.up.ubicar', 
            'productor.up.ubicar.store',
            'productor.parajes.validar-temporal'
        ])) {
            Log::info('CheckProductorSetup: User ID: ' . $user->id . ' has no UPs and is on a restricted route. Redirecting to welcome.');
            return redirect()->route('productor.welcome');
        }

        if ($hasUnidadesProductivas && $request->routeIs('productor.welcome')) {
            Log::info('CheckProductorSetup: User ID: ' . $user->id . ' has UPs and is on welcome route. Redirecting to panel.');
            return redirect()->route('productor.panel');
        }

        Log::info('CheckProductorSetup: User ID: ' . $user->id . ' - All checks passed. Passing through.');
        return $next($request);
    }
}
