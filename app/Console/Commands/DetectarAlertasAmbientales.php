<?php

namespace App\Console\Commands;

use App\Services\AlertasAmbientalesService;
use Illuminate\Console\Command;

class DetectarAlertasAmbientales extends Command
{
    protected $signature = 'alertas:detectar
                            {--unidad-id= : ID específico de unidad productiva}
                            {--forzar : Forzar detección incluso si ya se ejecutó hoy}';

    protected $description = 'Detecta condiciones de riesgo y crea alertas ambientales';

    public function handle(AlertasAmbientalesService $alertasService): int
    {
        $this->info('🚨 Detectando alertas ambientales...');
        $this->newLine();

        $inicio = microtime(true);

        if ($this->option('unidad-id')) {
            // Detectar para una unidad específica
            $unidadId = $this->option('unidad-id');
            $unidad = \App\Models\UnidadProductiva::find($unidadId);

            if (!$unidad) {
                $this->error("❌ No se encontró la unidad productiva con ID: {$unidadId}");
                return Command::FAILURE;
            }

            $this->info("Analizando: {$unidad->nombre}");
            $resultado = $alertasService->detectarAlertasParaUnidad($unidad);

            $this->newLine();
            $this->line("✅ Alertas creadas: {$resultado['creadas']}");
            $this->line("⏭️  Alertas desactivadas: {$resultado['desactivadas']}");

        } else {
            // Detectar para todas las unidades
            $estadisticas = $alertasService->detectarAlertasParaTodasLasUnidades();

            $this->newLine();
            $this->line("📊 Resultados:");
            $this->line("   • Unidades analizadas: {$estadisticas['total_unidades']}");
            $this->line("   • Alertas creadas: {$estadisticas['alertas_creadas']}");
            $this->line("   • Alertas desactivadas: {$estadisticas['alertas_desactivadas']}");
            
            if ($estadisticas['errores'] > 0) {
                $this->warn("   ⚠️  Errores: {$estadisticas['errores']}");
            }
        }

        $duracion = round(microtime(true) - $inicio, 2);
        $this->newLine();
        $this->info("✅ Proceso completado en {$duracion} segundos");

        return Command::SUCCESS;
    }
}
