<?php

namespace App\Providers;

// Laravel Events
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

// Application Events
use App\Events\InstitucionCreated;
use App\Events\ContactMessageSent;
use App\Listeners\SendContactEmail;
use App\Events\ProductorCreated;

// Application Listeners
use App\Listeners\SendWelcomeEmailListener;
use App\Listeners\SendWelcomeNotificationListener;
use App\Listeners\UpdateLastLoginAt;

// Models
use App\Models\Institucion;
use App\Models\Productor;
use App\Models\SolicitudVerificacion;

// Observers
use App\Observers\InstitucionObserver;
use App\Observers\ProductorObserver;
use App\Observers\SolicitudVerificacionObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * --------------------------------------------------------------------
     * Event/Listener Mappings
     * --------------------------------------------------------------------
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ContactMessageSent::class => [
            SendContactEmail::class,
        ],
        InstitucionCreated::class => [
            SendWelcomeEmailListener::class,
        ],
        ProductorCreated::class => [
            SendWelcomeNotificationListener::class,
        ],
        Login::class => [
            UpdateLastLoginAt::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any events for your application.
     */
    public function boot(): void
    {
        /**
         * --------------------------------------------------------------------
         * Model Observers
         * --------------------------------------------------------------------
         * Register observers for application models. These observers will
         * listen for model events and perform actions accordingly.
         */

        // Observer for new institutions.
        Institucion::observe(InstitucionObserver::class);

        // Observer for institutional verification requests.
        SolicitudVerificacion::observe(SolicitudVerificacionObserver::class);

        // Observer for producer creation.
        Productor::observe(ProductorObserver::class);
    }
}
