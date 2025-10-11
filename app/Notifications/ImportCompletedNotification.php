<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportCompletedNotification extends Notification
{
    use Queueable;

    public $importados;
    public $errores;
    public array $listaErrores;
    public int $asociacionesFallidas;

    /**
     * Create a new notification instance.
     *
     * @param int $importados Número de productores importados correctamente.
     * @param int $errores Número de filas que fallaron.
     * @param array $listaErrores La lista detallada de mensajes de error.
     */
    public function __construct(int $importados, int $errores, int $asociacionesFallidas, array $listaErrores = [])
    {
        $this->importados = $importados;
        $this->errores = $errores;
        $this->listaErrores = $listaErrores;
        $this->asociacionesFallidas = $asociacionesFallidas;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Se guarda en la base de datos para mostrarla en la UI.
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $mensaje = "La importación de productores ha finalizado. ";
        $mensaje .= "Se importaron {$this->importados} productores correctamente.";

        if ($this->errores > 0) {
            $mensaje .= " Se encontraron {$this->errores} errores.";
        }

        if ($this->asociacionesFallidas > 0) {
            $mensaje .= " De esos errores, {$this->asociacionesFallidas} corresponden a Unidades Productivas que no se pudieron crear o asociar.";
        }

        return [
            'titulo' => 'Importación Completada',
            'mensaje' => $mensaje,
            'importados' => $this->importados, 
            'errores' => $this->errores,     
            'asociaciones_fallidas' => $this->asociacionesFallidas,  
            'lista_errores' => $this->listaErrores, 
            'tipo' => $this->errores > 0 ? 'advertencia' : 'exito', 
        ];
    }
}