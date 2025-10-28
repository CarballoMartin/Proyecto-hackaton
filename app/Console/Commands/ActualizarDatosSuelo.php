<?php

namespace App\Console\Commands;

use App\Models\UnidadProductiva;
use App\Models\CaracteristicaSuelo;
use App\Services\SueloApi\SoilGridsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActualizarDatosSuelo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suelo:actualizar-datos 
                            {--unidad= : ID de unidad productiva específica}
                            {--forzar : Forzar actualización incluso si hay datos recientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los datos de suelo para las unidades productivas usando SoilGrids API';

    protected SoilGridsApiService $sueloService;

    public function __construct(SoilGridsApiService $sueloService)
    {
        parent::__construct();
        $this->sueloService = $sueloService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🌍 Iniciando actualización de datos de suelo...');

        // Filtrar unidades productivas
        $query = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('activo', true);

        // Si se especifica una unidad, filtrar
        if ($unidadId = $this->option('unidad')) {
            $query->where('id', $unidadId);
        }

        $unidades = $query->get();

        if ($unidades->isEmpty()) {
            $this->warn('No se encontraron unidades productivas con coordenadas.');
            return 0;
        }

        $this->info("📊 Procesando {$unidades->count()} unidades productivas...");

        $estadisticas = [
            'procesadas' => 0,
            'actualizadas' => 0,
            'creadas' => 0,
            'omitidas' => 0,
            'errores' => 0,
        ];

        $bar = $this->output->createProgressBar($unidades->count());
        $bar->start();

        foreach ($unidades as $unidad) {
            try {
                $resultado = $this->procesarUnidad($unidad);
                
                $estadisticas['procesadas']++;
                $estadisticas['actualizadas'] += $resultado['actualizado'] ? 1 : 0;
                $estadisticas['creadas'] += $resultado['creado'] ? 1 : 0;
                $estadisticas['omitidas'] += $resultado['omitido'] ? 1 : 0;

            } catch (\Exception $e) {
                $estadisticas['errores']++;
                Log::error('Error actualizando datos de suelo', [
                    'unidad_productiva_id' => $unidad->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Mostrar estadísticas
        $this->displayEstadisticas($estadisticas);

        return 0;
    }

    /**
     * Procesa una unidad productiva
     */
    private function procesarUnidad(UnidadProductiva $unidad): array
    {
        // Verificar si ya hay datos recientes (últimos 90 días)
        if (!$this->option('forzar')) {
            $ultimoDato = $unidad->caracteristicasSuelo()
                ->where('fecha_consulta', '>=', now()->subDays(90))
                ->first();

            if ($ultimoDato) {
                return ['actualizado' => false, 'creado' => false, 'omitido' => true];
            }
        }

        // Consultar API de SoilGrids
        $datosSuelo = $this->sueloService->obtenerDatosSuelo(
            $unidad->latitud,
            $unidad->longitud
        );

        if (!$datosSuelo) {
            throw new \Exception('No se pudieron obtener datos de la API');
        }

        // Clasificar datos usando los métodos del modelo
        $caracteristica = new CaracteristicaSuelo($datosSuelo);
        $caracteristica->textura_clasificacion = $caracteristica->clasificarTextura();
        $caracteristica->ph_clasificacion = $caracteristica->clasificarPh();
        $caracteristica->calidad_clasificacion = $caracteristica->clasificarCalidad();

        // Guardar en base de datos
        $caracteristicaGuardada = CaracteristicaSuelo::create([
            'unidad_productiva_id' => $unidad->id,
            'ph' => $datosSuelo['ph'],
            'materia_organica_porcentaje' => $datosSuelo['materia_organica_porcentaje'],
            'capacidad_retencion_agua' => $datosSuelo['capacidad_retencion_agua'],
            'arcilla_porcentaje' => $datosSuelo['arcilla_porcentaje'],
            'limo_porcentaje' => $datosSuelo['limo_porcentaje'],
            'arena_porcentaje' => $datosSuelo['arena_porcentaje'],
            'datos_completos_json' => $datosSuelo['datos_completos_json'],
            'fecha_consulta' => now(),
            'textura_clasificacion' => $caracteristica->textura_clasificacion,
            'ph_clasificacion' => $caracteristica->ph_clasificacion,
            'calidad_clasificacion' => $caracteristica->calidad_clasificacion,
        ]);

        return ['actualizado' => true, 'creado' => true, 'omitido' => false];
    }

    /**
     * Muestra estadísticas del procesamiento
     */
    private function displayEstadisticas(array $estadisticas): void
    {
        $this->newLine();
        $this->info('📈 Estadísticas de actualización:');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Unidades procesadas', $estadisticas['procesadas']],
                ['Datos creados', $estadisticas['creadas']],
                ['Omitidas (ya actualizadas)', $estadisticas['omitidas']],
                ['Errores', $estadisticas['errores']],
            ]
        );
    }
}
