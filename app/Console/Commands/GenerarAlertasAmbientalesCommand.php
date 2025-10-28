<?php

namespace App\Console\Commands;

use App\Services\AlertaAmbientalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerarAlertasAmbientalesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ambiental:generar-alertas 
                            {--unidad= : ID específico de unidad productiva}
                            {--forzar : Forzar generación ignorando alertas existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera alertas ambientales automáticas basadas en datos de clima, NDVI y suelo';

    protected AlertaAmbientalService $alertaService;

    public function __construct(AlertaAmbientalService $alertaService)
    {
        parent::__construct();
        $this->alertaService = $alertaService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚨 Iniciando generación de alertas ambientales...');

        try {
            if ($this->option('unidad')) {
                $this->generarAlertasUnidadEspecifica();
            } else {
                $this->generarAlertasTodasLasUnidades();
            }

            $this->info('✅ Generación de alertas completada');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la generación: ' . $e->getMessage());
            Log::error('Error en comando generar alertas: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Genera alertas para una unidad específica
     */
    private function generarAlertasUnidadEspecifica(): void
    {
        $unidadId = $this->option('unidad');
        $unidad = \App\Models\UnidadProductiva::find($unidadId);

        if (!$unidad) {
            $this->error("❌ Unidad productiva {$unidadId} no encontrada");
            return;
        }

        if (!$unidad->latitud || !$unidad->longitud) {
            $this->error("❌ Unidad {$unidadId} no tiene coordenadas configuradas");
            return;
        }

        $this->info("🔄 Generando alertas para unidad: {$unidad->nombre}");

        $alertas = $this->alertaService->generarAlertasUnidad($unidad);
        $alertasGuardadas = $this->alertaService->guardarAlertas($unidad, $alertas);

        $this->info("✅ {$alertasGuardadas->count()} alertas generadas para {$unidad->nombre}");

        if (!empty($alertasGuardadas)) {
            $this->mostrarResumenAlertas($alertasGuardadas);
        }
    }

    /**
     * Genera alertas para todas las unidades
     */
    private function generarAlertasTodasLasUnidades(): void
    {
        $this->info('🔄 Generando alertas para todas las unidades productivas...');

        $unidades = \App\Models\UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();

        $bar = $this->output->createProgressBar($unidades->count());
        $bar->start();

        $resultados = [
            'unidades_procesadas' => 0,
            'alertas_generadas' => 0,
            'alertas_criticas' => 0,
            'alertas_altas' => 0,
            'alertas_medias' => 0,
            'alertas_bajas' => 0,
        ];

        foreach ($unidades as $unidad) {
            try {
                $alertas = $this->alertaService->generarAlertasUnidad($unidad);
                $alertasGuardadas = $this->alertaService->guardarAlertas($unidad, $alertas);
                
                $resultados['unidades_procesadas']++;
                $resultados['alertas_generadas'] += $alertasGuardadas->count();

                foreach ($alertasGuardadas as $alerta) {
                    switch ($alerta->severidad) {
                        case 'critica':
                            $resultados['alertas_criticas']++;
                            break;
                        case 'alta':
                            $resultados['alertas_altas']++;
                            break;
                        case 'media':
                            $resultados['alertas_medias']++;
                            break;
                        case 'baja':
                            $resultados['alertas_bajas']++;
                            break;
                    }
                }

            } catch (\Exception $e) {
                $this->error("Error procesando unidad {$unidad->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("📊 Resultados de generación de alertas:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Unidades procesadas', $resultados['unidades_procesadas']],
                ['Alertas generadas', $resultados['alertas_generadas']],
                ['Alertas críticas', $resultados['alertas_criticas']],
                ['Alertas altas', $resultados['alertas_altas']],
                ['Alertas medias', $resultados['alertas_medias']],
                ['Alertas bajas', $resultados['alertas_bajas']],
            ]
        );

        if ($resultados['alertas_criticas'] > 0) {
            $this->warn("⚠️ Se generaron {$resultados['alertas_criticas']} alertas críticas que requieren atención inmediata");
        }
    }

    /**
     * Muestra resumen de alertas generadas
     */
    private function mostrarResumenAlertas($alertas): void
    {
        $this->info('📋 Resumen de alertas generadas:');
        
        foreach ($alertas as $alerta) {
            $icono = match($alerta->tipo_alerta) {
                'sequia' => '🌵',
                'tormenta' => '⛈️',
                'estres_termico' => '🌡️',
                'helada' => '❄️',
                'viento' => '💨',
                'ndvi_bajo' => '🌱',
                'suelo_degradado' => '🏗️',
                default => '⚠️'
            };

            $color = match($alerta->severidad) {
                'critica' => 'red',
                'alta' => 'orange',
                'media' => 'yellow',
                'baja' => 'green',
                default => 'gray'
            };

            $this->line("  {$icono} {$alerta->titulo} ({$alerta->severidad})");
        }
    }
}
