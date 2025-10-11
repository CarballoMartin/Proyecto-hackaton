<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        // Verifica si la cuenta del usuario está inactiva
        if (!$user->activo) {
            // Opción: log para auditoría futura
            Log::warning("Intento de acceso con cuenta desactivada: usuario {$user->email}");

            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'Tu cuenta está desactivada. Contactá a un administrador.',
            ]);
        }

        // Define las URLs de redirección basadas en el rol del usuario
        $redirectUrl = match ($user->rol) {
            'superadmin' => '/superadmin/panel',
            'institucional' => '/institucional/dashboard',
            'productor' => '/productor/panel',
            default => '/dashboard', // Una ruta por defecto por si el rol no coincide
        };

        if ($user->rol === 'superadmin') {
            // session()->flash('flash.banner', '¡Bienvenido, ' . $user->name . '!');
        // session()->flash('flash.bannerStyle', 'success');
        }

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false])
            : redirect()->intended($redirectUrl);
    }
}