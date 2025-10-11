<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SolicitudVerificacion;

class NuevaSolicitudInstitucion extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SolicitudVerificacion $solicitud)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'solicitud_id' => $this->solicitud->id,
            'nombre_institucion' => $this->solicitud->nombre_institucion,
            'mensaje' => "Nueva solicitud recibida. La institución '{$this->solicitud->nombre_institucion}' ha solicitado su incorporación.",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
