<?php

namespace App\Listeners;

use App\Events\ContactMessageSent;
use App\Mail\ContactFormMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendContactEmail implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(ContactMessageSent $event): void
    {
        Mail::to('ProyectoSINC@gmail.com')->send(new ContactFormMail($event->data));
    }
}