<?php

namespace App\Console\Commands;

use App\Services\SueloApi\SoilGridsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarDatosSueloCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suelo:sincronizar-fao 
                            {--unidad= : ID específico de unidad productiva}
                            {--forzar : Forzar actualización ignorando caché}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza datos de suelo desde FAO SoilGrids';

    protected SoilGridsApiService $soilGridsService;

    public function __construct(SoilGridsApiService $soilGridsService)
    {
        parent::__construct();
        $this->soilGridsService = $soilGridsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌍 Iniciando sincronización de datos de suelo...');

        // Verificar configuración
        if (!config('ambiental.apis.soilgrids.enabled')) {
            $this->error('❌ API de SoilGrids está deshabilitada en configuración');
            return 1;
        }

        try {
            if ($this->option('unidad')) {
                $this->actualizarUnidadEspecifica();
            } else {
                $this->actualizarTodasLasUnidades();
            }

            $this->info('✅ Sincronización de datos de suelo completada');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            Log::error('Error en comando actualizar suelo: ' . $e->getMessage());
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

        $this->info("🔄 Actualizando datos de suelo para unidad: {$unidad->nombre}");

        $sueloData = $this->soilGridsService->obtenerDatosSuelo($unidad);
        
        if ($sueloData) {
            $caracteristica = $this->soilGridsService->guardarDatosSuelo($unidad, $sueloData);
            
            if ($caracteristica) {
                $this->info("✅ Datos de suelo actualizados correctamente");
                $this->mostrarResumenSuelo($caracteristica);
            } else {
                $this->error("❌ Error guardando datos de suelo");
            }
        } else {
            $this->error("❌ No se pudieron obtener datos de suelo");
        }
    }

    /**
     * Actualiza datos para todas las unidades
     */
    private function actualizarTodasLasUnidades(): void
    {
        $this->info('🔄 Actualizando datos de suelo para todas las unidades productivas...');

        $resultados = $this->soilGridsService->actualizarDatosSuelo();

        $this->info("📊 Resultados de sincronización:");
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

    /**
     * Muestra un resumen de las características del suelo
     */
    private function mostrarResumenSuelo(\App\Models\CaracteristicaSuelo $suelo): void
    {
        $this->table(
            ['Propiedad', 'Valor', 'Clasificación'],
            [
                ['pH', $suelo->ph_valor, $suelo->clasificacion_ph],
                ['Materia Orgánica', $suelo->materia_organica_porcentaje . '%', $suelo->clasificacion_materia_organica],
                ['Textura', $suelo->textura_clasificacion, $suelo->clasificacion_textura],
                ['Arcilla', $suelo->arcilla_porcentaje . '%', ''],
                ['Limo', $suelo->limo_porcentaje . '%', ''],
                ['Arena', $suelo->arena_porcentaje . '%', ''],
                ['CIC', $suelo->capacidad_intercambio_cationico . ' cmol/kg', ''],
                ['Saturación Bases', $suelo->saturacion_bases . '%', ''],
                ['Densidad Aparente', $suelo->densidad_aparente . ' g/cm³', ''],
                ['Estado General', $suelo->estado_general, ''],
                ['Índice Calidad', $suelo->calcularIndiceCalidad() . '%', ''],
            ]
        );

        // Mostrar recomendaciones
        $recomendaciones = $suelo->recomendaciones;
        if (!empty($recomendaciones)) {
            $this->info('💡 Recomendaciones de mejoramiento:');
            foreach ($recomendaciones as $rec) {
                $prioridad = match($rec['prioridad']) {
                    'alta' => '🔴',
                    'media' => '🟡',
                    'baja' => '🟢',
                    default => '⚪'
                };
                $this->line("  {$prioridad} {$rec['problema']}: {$rec['solucion']}");
            }
        }

        // Mostrar recomendaciones de pasturas
        $pasturas = $suelo->recomendaciones_pasturas;
        if (!empty($pasturas)) {
            $this->info('🌱 Recomendaciones de pasturas:');
            foreach ($pasturas as $pastura) {
                $this->line("  • {$pastura['nombre']}: {$pastura['descripcion']}");
            }
        }
    }
}
