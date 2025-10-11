<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\PdfExportServiceInterface;
use App\Services\PdfExportService;
use App\Interfaces\FileProcessorInterface;
use App\Services\CsvExcelProcessor;
use App\Interfaces\SmsServiceInterface;
use App\Services\TwilioSmsService;
use App\Services\FakeSmsService;
use App\Interfaces\ChartBuilderInterface;
use App\Services\ChartJsBuilder;

use Illuminate\Support\Facades\View;
use App\Models\ConfiguracionActualizacion;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileProcessorInterface::class, CsvExcelProcessor::class);
        //$this->app->bind(SmsServiceInterface::class, TwilioSmsService::class);
        $this->app->bind(SmsServiceInterface::class, FakeSmsService::class);

        $this->app->bind(ChartBuilderInterface::class, ChartJsBuilder::class);

        $this->app->bind(PdfExportServiceInterface::class, PdfExportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin'], function ($view) {
            if (auth()->check() && auth()->user()->rol === 'superadmin') {
                $view->with('configuracion', ConfiguracionActualizacion::firstOrNew());
            }
        });

        Blade::component('layouts.admin', 'admin-layout');
        Blade::component('components.panel-layout', 'panel-layout');
        Blade::component('layouts.productor', 'productor-layout');
        Blade::component('layouts.productor-wizard', 'productor-wizard-layout');
        Blade::component('layouts.institucional', 'institucional-layout');
    }
}