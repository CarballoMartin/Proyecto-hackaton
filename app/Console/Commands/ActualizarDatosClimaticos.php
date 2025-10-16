<?php

namespace App\Console\Commands;

use App\Models\UnidadProductiva;
use App\Models\DatoClimaticoCache;
use App\Services\ClimaApi\OpenMeteoApiService;
use Illuminate\Console\Command;

class ActualizarDatosClimaticos extends Command
{
    protected $signature = 'clima:actualizar-datos 
                            {--unidad-id= : ID específico de unidad productiva}
                            {--forzar : Forzar actualización aunque tenga datos recientes}';

    protected $description = 'Actualiza los datos climáticos de las unidades productivas';

    public function handle(OpenMeteoApiService $climaService): int
    {
        $this->info('🌦️  Actualizando datos climáticos...');

        // Obtener unidades con coordenadas
        $query = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('activo', true);

        // Filtrar por ID si se especificó
        if ($this->option('unidad-id')) {
            $query->where('id', $this->option('unidad-id'));
        }

        $unidades = $query->get();

        if ($unidades->isEmpty()) {
            $this->warn('No se encontraron unidades productivas con coordenadas.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$unidades->count()} unidades productivas...");

        $bar = $this->output->createProgressBar($unidades->count());
        $bar->start();

        $actualizadas = 0;
        $omitidas = 0;
        $errores = 0;

        foreach ($unidades as $unidad) {
            // Verificar si ya tiene datos recientes
            $datosExistentes = DatoClimaticoCache::where('unidad_productiva_id', $unidad->id)
                ->latest('fecha_consulta')
                ->first();

            if ($datosExistentes && $datosExistentes->esVigente() && !$this->option('forzar')) {
                $omitidas++;
                $bar->advance();
                continue;
            }

            // Consultar API
            $datosApi = $climaService->obtenerPronostico($unidad->latitud, $unidad->longitud);

            if ($datosApi) {
                $datosFormateados = $climaService->formatearDatos($datosApi);
                $datosFormateados['unidad_productiva_id'] = $unidad->id;

                DatoClimaticoCache::updateOrCreate(
                    ['unidad_productiva_id' => $unidad->id],
                    $datosFormateados
                );

                $actualizadas++;
            } else {
                $errores++;
            }

            $bar->advance();

            // Pequeña pausa para no saturar la API
            usleep(100000); // 0.1 segundos
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Actualizadas: {$actualizadas}");
        $this->info("⏭️  Omitidas (datos recientes): {$omitidas}");
        
        if ($errores > 0) {
            $this->warn("❌ Errores: {$errores}");
        }

        return Command::SUCCESS;
    }
}
