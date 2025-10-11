<?php

namespace App\Observers;

use App\Models\SolicitudVerificacion;
use App\Models\User;
use App\Notifications\NuevaSolicitudInstitucion;
use Illuminate\Support\Facades\Notification;
use App\Services\LoggerService;
use Illuminate\Support\Facades\Auth;

class SolicitudVerificacionObserver
{
    /**
     * Handle the SolicitudVerificacion "created" event.
     */
    public function created(SolicitudVerificacion $solicitudVerificacion): void
    {
        // Loggear la creación de la solicitud
        LoggerService::log(
            'nueva_solicitud',
            'SolicitudVerificacion',
            $solicitudVerificacion->id,
            "Se recibió una nueva solicitud de la institución '{$solicitudVerificacion->nombre_institucion}'."
        );

        // Notificar a los superadministradores sobre la nueva solicitud
        $superadmins = User::where('rol', User::ROL_SUPERADMIN)->get();

        if ($superadmins->isEmpty()) {
            return;
        }

        $notificacion = new NuevaSolicitudInstitucion($solicitudVerificacion);

        Notification::send($superadmins, $notificacion);
    }

    /**
     * Handle the SolicitudVerificacion "updated" event.
     */
    public function updated(SolicitudVerificacion $solicitudVerificacion): void
    {
        // Solo actuamos si el estado ha cambiado
        if ($solicitudVerificacion->isDirty('estado')) {
            $user = Auth::user();
            $userName = $user ? $user->name : 'un usuario desconocido';
            $institucionName = $solicitudVerificacion->nombre_institucion;

            switch ($solicitudVerificacion->estado) {
                case 'aprobada':
                    $accion = 'aprobó';
                    $tipo_accion = 'aprobo_solicitud';
                    break;

                case 'rechazada':
                    $accion = 'rechazó';
                    $tipo_accion = 'rechazo_solicitud';
                    break;

                default:
                    // No loggear otros cambios de estado
                    return;
            }

            $descripcion = "Usuario {$userName} {$accion} la solicitud de {$institucionName}";

            LoggerService::log(
                $tipo_accion,
                'SolicitudVerificacion',
                $solicitudVerificacion->id,
                $descripcion
            );
        }
    }

    /**
     * Handle the SolicitudVerificacion "deleted" event.
     */
    public function deleted(SolicitudVerificacion $solicitudVerificacion): void
    {
        //
    }

    /**
     * Handle the SolicitudVerificacion "restored" event.
     */
    public function restored(SolicitudVerificacion $solicitudVerificacion): void
    {
        //
    }

    /**
     * Handle the SolicitudVerificacion "force deleted" event.
     */
    public function forceDeleted(SolicitudVerificacion $solicitudVerificacion): void
    {
        //
    }
}
