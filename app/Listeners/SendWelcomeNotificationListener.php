<?php

namespace App\Listeners;

use App\Events\ProductorCreated;
use App\Mail\ProductorBienvenidaMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductorCreated  $event
     * @return void
     */
    public function handle(ProductorCreated $event)
    {
        if (isset($event->user->email)) {
            Mail::to($event->user->email)->send(new ProductorBienvenidaMail($event->user));
        }
        // Aquí se podría añadir lógica para enviar SMS o WhatsApp en el futuro
    }
}
