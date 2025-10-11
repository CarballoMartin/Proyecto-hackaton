<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\InstitucionBienvenidaMail;
use Illuminate\Support\Facades\Mail;
use App\Events\InstitucionCreated;

class SendWelcomeEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InstitucionCreated $event): void
    {
        Mail::to($event->user->email)->send(new InstitucionBienvenidaMail($event->user, $event->passwordTemporal));
    }
}
