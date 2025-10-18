<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('clima:actualizar-datos')->dailyAt('06:00');
Schedule::command('alertas:detectar')->dailyAt('07:00'); // Detectar alertas después de actualizar clima
Schedule::command('configuracion:aplicar-programada')->daily();