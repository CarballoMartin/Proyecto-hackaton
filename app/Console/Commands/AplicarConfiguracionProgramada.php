<?php

namespace App\Console\Commands;

use App\Models\ConfiguracionActualizacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AplicarConfiguracionProgramada extends Command
{
    protected $signature = 'configuracion:aplicar-programada';
    protected $description = 'Aplica la configuración de actualización programada si el ciclo actual ha terminado.';

    public function handle()
    {
        $this->info('Verificando configuración programada...');

        $config = ConfiguracionActualizacion::first();

        if (!$config) {
            $this->info('No existe configuración para verificar.');
            return;
        }

        // Verificar si hay una configuración programada y si el ciclo actual terminó
        $tieneCambiosProgramados = !is_null($config->proxima_frecuencia_dias) && !is_null($config->proxima_activo);
        $cicloTerminado = $config->proxima_actualizacion && $config->proxima_actualizacion->isPast();

        if ($tieneCambiosProgramados && $cicloTerminado) {
            $this->info('Ciclo terminado y hay cambios programados. Aplicando...');
            Log::info('Aplicando configuración de actualización programada.');

            // Aplicar los cambios programados
            $config->frecuencia_dias = $config->proxima_frecuencia_dias;
            $config->activo = $config->proxima_activo;

            // Iniciar el nuevo ciclo si está activo
            if ($config->activo) {
                $config->ultima_actualizacion = now();
                $config->proxima_actualizacion = now()->addDays($config->frecuencia_dias);
                $this->info('Nuevo ciclo iniciado. Próxima actualización: ' . $config->proxima_actualizacion->format('d-m-Y'));
            } else {
                $config->proxima_actualizacion = null;
                $this->info('El sistema de actualización se ha desactivado.');
            }

            // Limpiar los campos de la configuración programada
            $config->proxima_frecuencia_dias = null;
            $config->proxima_activo = null;
            
            $config->save();
            $this->info('Configuración programada aplicada con éxito.');

        } else {
            $this->info('No hay acciones para realizar.');
        }
    }
}