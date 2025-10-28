<?php

namespace App\Console\Commands;

use App\Services\SatelitalApi\CopernicusApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarNDVICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'satelital:actualizar-ndvi 
                            {--unidad= : ID específico de unidad productiva}
                            {--forzar : Forzar actualización ignorando caché}
                            {--historico : Actualizar datos históricos (últimos 3 meses)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los índices de vegetación (NDVI) desde Copernicus/Sentinel-2';

    protected CopernicusApiService $copernicusService;

    public function __construct(CopernicusApiService $copernicusService)
    {
        parent::__construct();
        $this->copernicusService = $copernicusService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Iniciando actualización de datos NDVI...');

        // Verificar configuración
        if (!config('ambiental.apis.copernicus.enabled')) {
            $this->error('❌ API de Copernicus está deshabilitada en configuración');
            return 1;
        }

        if (!config('ambiental.apis.copernicus.client_id') || !config('ambiental.apis.copernicus.client_secret')) {
            $this->error('❌ Credenciales de Copernicus no configuradas');
            $this->info('💡 Configura COPERNICUS_CLIENT_ID y COPERNICUS_CLIENT_SECRET en .env');
            return 1;
        }

        try {
            if ($this->option('unidad')) {
                $this->actualizarUnidadEspecifica();
            } elseif ($this->option('historico')) {
                $this->actualizarDatosHistoricos();
            } else {
                $this->actualizarDatosActuales();
            }

            $this->info('✅ Actualización de NDVI completada');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la actualización: ' . $e->getMessage());
            Log::error('Error en comando actualizar NDVI: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Actualiza datos para una unidad específica
     */
    private function actualizarUnidadEspecifica(): void
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

        $this->info("🔄 Actualizando NDVI para unidad: {$unidad->nombre}");

        $ndviData = $this->copernicusService->obtenerNDVI($unidad);
        
        if ($ndviData) {
            $indice = $this->copernicusService->guardarDatosNDVI($unidad, $ndviData);
            
            if ($indice) {
                $this->info("✅ NDVI actualizado: {$ndviData['ndvi']} ({$ndviData['clasificacion']})");
                $this->table(
                    ['Métrica', 'Valor'],
                    [
                        ['NDVI', $ndviData['ndvi']],
                        ['Clasificación', $ndviData['clasificacion']],
                        ['Fecha Imagen', $ndviData['fecha_imagen']],
                        ['Satélite', $ndviData['satelite']],
                        ['Nubosidad', $ndviData['nubosidad_porcentaje'] . '%'],
                    ]
                );
            } else {
                $this->error("❌ Error guardando datos NDVI");
            }
        } else {
            $this->error("❌ No se pudieron obtener datos NDVI");
        }
    }

    /**
     * Actualiza datos históricos para todas las unidades
     */
    private function actualizarDatosHistoricos(): void
    {
        $this->info('📊 Actualizando datos históricos de NDVI (últimos 3 meses)...');

        $unidades = \App\Models\UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();

        $bar = $this->output->createProgressBar($unidades->count());
        $bar->start();

        $resultados = [
            'exitosos' => 0,
            'fallidos' => 0,
            'errores' => []
        ];

        foreach ($unidades as $unidad) {
            try {
                $historial = $this->copernicusService->obtenerHistorialNDVI($unidad, 3);
                
                foreach ($historial as $ndviData) {
                    $this->copernicusService->guardarDatosNDVI($unidad, $ndviData);
                }
                
                $resultados['exitosos']++;
            } catch (\Exception $e) {
                $resultados['fallidos']++;
                $resultados['errores'][] = "Unidad {$unidad->id}: " . $e->getMessage();
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("📈 Resultados históricos:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Unidades procesadas', $unidades->count()],
                ['Exitosas', $resultados['exitosos']],
                ['Fallidas', $resultados['fallidos']],
            ]
        );

        if (!empty($resultados['errores'])) {
            $this->warn('⚠️ Errores encontrados:');
            foreach ($resultados['errores'] as $error) {
                $this->line("  - {$error}");
            }
        }
    }

    /**
     * Actualiza datos actuales para todas las unidades
     */
    private function actualizarDatosActuales(): void
    {
        $this->info('🔄 Actualizando datos actuales de NDVI...');

        $resultados = $this->copernicusService->actualizarDatosNDVI();

        $this->info("📊 Resultados de actualización:");
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Actualizaciones exitosas', $resultados['exitosos']],
                ['Actualizaciones fallidas', $resultados['fallidos']],
            ]
        );

        if (!empty($resultados['errores'])) {
            $this->warn('⚠️ Errores encontrados:');
            foreach ($resultados['errores'] as $error) {
                $this->line("  - {$error}");
            }
        }
    }
}
