<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstitucionBienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia del usuario recién creado.
     * @var \App\Models\User
     */
    public $user;

    /**
     * La contraseña temporal generada.
     * @var string
     */
    public $passwordTemporal;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @param string $passwordTemporal
     */
    public function __construct(User $user, string $passwordTemporal)
    {
        $this->user = $user;
        $this->passwordTemporal = $passwordTemporal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido/a a la Plataforma - Credenciales de Acceso',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.institucion.bienvenida',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}