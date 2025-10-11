<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'productor.setup' => \App\Http\Middleware\CheckProductorSetup::class,
            'institucional.session' => \App\Http\Middleware\InstitucionalSessionMiddleware::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->redirectTo(
            users: function (Request $request) {
                /** @var \App\Models\User $user */
                $user = $request->user();

                // Redirige al usuario a su dashboard específico basado en su rol.
                return match ($user->rol) {
                    User::ROL_SUPERADMIN => route('admin.panel'),
                    User::ROL_INSTITUCIONAL => route('institucional.dashboard'),
                    User::ROL_PRODUCTOR => route('productor.panel'),
                    default => '/',
                };
            },
            guests: '/login'
        );
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
