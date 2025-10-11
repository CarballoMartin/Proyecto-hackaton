<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportFailedNotification extends Notification
{
    use Queueable;

    public $errorMessage;

    /**
     * Create a new notification instance.
     *
     * @param string $errorMessage El mensaje de error que causó el fallo.
     */
    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Error en la Importación',
            'mensaje' => 'Ocurrió un error crítico durante la importación de productores. Revisa los logs para más detalles. Error: ' . $this->errorMessage,
            'tipo' => 'error', //darle un estilo rojo en el frontend
        ];
    }
}