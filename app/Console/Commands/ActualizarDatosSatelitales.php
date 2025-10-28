<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SentinelHubService;
use App\Models\UnidadProductiva;
use App\Models\DatoSatelital;
use App\Models\Productor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActualizarDatosSatelitales extends Command
{
    protected $signature = 'satelital:actualizar-datos 
                           {--productor= : ID del productor específico}
                           {--unidad= : ID de unidad productiva específica}
                           {--dias=30 : Días hacia atrás para buscar imágenes}
                           {--forzar : Forzar actualización incluso si ya existen datos recientes}';

    protected $description = 'Actualiza datos satelitales (NDVI) para unidades productivas';

    private SentinelHubService $sentinelHubService;

    public function __construct(SentinelHubService $sentinelHubService)
    {
        parent::__construct();
        $this->sentinelHubService = $sentinelHubService;
    }

    public function handle(): int
    {
        $this->info('🛰️ Iniciando actualización de datos satelitales...');

        // Verificar configuración
        if (!$this->sentinelHubService->estaConfigurado()) {
            $this->warn('⚠️ Sentinel Hub no está configurado. Usando datos simulados para demo.');
        }

        // Obtener unidades productivas a procesar
        $unidades = $this->obtenerUnidadesAProcesar();
        
        if ($unidades->isEmpty()) {
            $this->warn('No se encontraron unidades productivas para procesar.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$unidades->count()} unidades productivas...");

        $procesadas = 0;
        $errores = 0;

        foreach ($unidades as $unidad) {
            try {
                $this->line("Procesando: {$unidad->nombre} (ID: {$unidad->id})");
                
                $resultado = $this->procesarUnidadProductiva($unidad);
                
                if ($resultado) {
                    $procesadas++;
                    $this->info("✅ Datos actualizados para {$unidad->nombre}");
                } else {
                    $errores++;
                    $this->error("❌ Error al procesar {$unidad->nombre}");
                }

            } catch (\Exception $e) {
                $errores++;
                $this->error("❌ Excepción en {$unidad->nombre}: " . $e->getMessage());
                Log::error('Error al procesar unidad productiva', [
                    'unidad_id' => $unidad->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Resumen de procesamiento:");
        $this->line("✅ Unidades procesadas: {$procesadas}");
        $this->line("❌ Errores: {$errores}");
        $this->line("📈 Total: {$unidades->count()}");

        return Command::SUCCESS;
    }

    private function obtenerUnidadesAProcesar()
    {
        $query = UnidadProductiva::with('productores');

        // Filtros específicos
        if ($productorId = $this->option('productor')) {
            $query->where('productor_id', $productorId);
        }

        if ($unidadId = $this->option('unidad')) {
            $query->where('id', $unidadId);
        }

        return $query->get();
    }

    private function procesarUnidadProductiva(UnidadProductiva $unidad): bool
    {
        try {
            // Verificar si ya existen datos recientes (a menos que se fuerce la actualización)
            if (!$this->option('forzar')) {
                $ultimoDato = DatoSatelital::obtenerUltimoParaUnidad($unidad->id);
                
                if ($ultimoDato && !$ultimoDato->necesitaActualizacion()) {
                    $this->line("  ⏭️ Datos recientes encontrados, omitiendo...");
                    return true;
                }
            }

            // Verificar que la unidad tenga coordenadas
            if (!$unidad->latitud || !$unidad->longitud) {
                $this->warn("  ⚠️ Unidad sin coordenadas, generando datos simulados...");
                return $this->generarDatosSimulados($unidad);
            }

            // Buscar productos satelitales
            $productos = $this->sentinelHubService->buscarProductos(
                $unidad->latitud,
                $unidad->longitud,
                0.01, // buffer de ~1km
                $this->option('dias')
            );

            if (empty($productos)) {
                $this->warn("  ⚠️ No se encontraron imágenes satelitales, generando datos simulados...");
                return $this->generarDatosSimulados($unidad);
            }

            // Procesar cada producto encontrado
            $datosGuardados = 0;
            foreach ($productos as $producto) {
                if ($this->procesarProducto($unidad, $producto)) {
                    $datosGuardados++;
                }
            }

            return $datosGuardados > 0;

        } catch (\Exception $e) {
            Log::error('Error al procesar unidad productiva', [
                'unidad_id' => $unidad->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function procesarProducto(UnidadProductiva $unidad, array $producto): bool
    {
        try {
            $fechaImagen = Carbon::parse($producto['properties']['datetime']);
            
            // Verificar si ya existe este dato
            $existe = DatoSatelital::where('unidad_productiva_id', $unidad->id)
                ->where('fecha_imagen', $fechaImagen->format('Y-m-d'))
                ->exists();

            if ($existe) {
                return true; // Ya existe, consideramos éxito
            }

            // Calcular índices de vegetación
            $indices = $this->sentinelHubService->calcularIndicesVegetacion(
                $unidad->latitud,
                $unidad->longitud,
                0.01,
                $fechaImagen->format('Y-m-d')
            );

            if (!$indices) {
                return false;
            }

            // Crear registro en base de datos
            DatoSatelital::create([
                'unidad_productiva_id' => $unidad->id,
                'satelite' => 'Sentinel-2',
                'fecha_imagen' => $fechaImagen->format('Y-m-d'),
                'producto_id' => $producto['properties']['id'] ?? null,
                'ndvi_promedio' => $indices['ndvi_promedio'],
                'ndvi_maximo' => $indices['ndvi_maximo'],
                'ndvi_minimo' => $indices['ndvi_minimo'],
                'ndvi_desviacion' => $indices['ndvi_desviacion'],
                'ndwi' => $indices['ndwi'],
                'gci' => $indices['gci'],
                'lai' => $indices['lai'],
                'resolucion_metros' => 10,
                'cobertura_nubes' => $indices['cobertura_nubes'],
                'calidad_imagen' => $indices['calidad_imagen'],
                'latitud_centro' => $unidad->latitud,
                'longitud_centro' => $unidad->longitud,
                'area_hectareas' => $indices['area_hectareas'],
                'estado_vegetacion' => $indices['estado_vegetacion'],
                'datos_validos' => true,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al procesar producto satelital', [
                'unidad_id' => $unidad->id,
                'producto' => $producto,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function generarDatosSimulados(UnidadProductiva $unidad): bool
    {
        try {
            // Generar datos simulados directamente si el servicio no está configurado
            if (!$this->sentinelHubService->estaConfigurado()) {
                $indices = $this->generarIndicesSimulados();
            } else {
                // Generar datos simulados basados en la ubicación y temporada
                $indices = $this->sentinelHubService->calcularIndicesVegetacion(
                    $unidad->latitud ?? -34.6037, // Buenos Aires por defecto
                    $unidad->longitud ?? -58.3816,
                    0.01,
                    Carbon::now()->format('Y-m-d')
                );

                if (!$indices) {
                    $indices = $this->generarIndicesSimulados();
                }
            }

            DatoSatelital::create([
                'unidad_productiva_id' => $unidad->id,
                'satelite' => 'Sentinel-2 (Simulado)',
                'fecha_imagen' => Carbon::now()->format('Y-m-d'),
                'producto_id' => 'SIM-' . time(),
                'ndvi_promedio' => $indices['ndvi_promedio'],
                'ndvi_maximo' => $indices['ndvi_maximo'],
                'ndvi_minimo' => $indices['ndvi_minimo'],
                'ndvi_desviacion' => $indices['ndvi_desviacion'],
                'ndwi' => $indices['ndwi'],
                'gci' => $indices['gci'],
                'lai' => $indices['lai'],
                'resolucion_metros' => 10,
                'cobertura_nubes' => $indices['cobertura_nubes'],
                'calidad_imagen' => 'good',
                'latitud_centro' => $unidad->latitud ?? -34.6037,
                'longitud_centro' => $unidad->longitud ?? -58.3816,
                'area_hectareas' => $indices['area_hectareas'],
                'estado_vegetacion' => $indices['estado_vegetacion'],
                'datos_validos' => true,
                'notas' => 'Datos simulados - Sentinel Hub no configurado o sin imágenes disponibles',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al generar datos simulados', [
                'unidad_id' => $unidad->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function generarIndicesSimulados(): array
    {
        $estacion = $this->obtenerEstacionActual();
        $factorVariacion = 0.1; // 10% de variación aleatoria
        
        $ndviBase = match($estacion) {
            'primavera' => 0.65,
            'verano' => 0.75,
            'otoño' => 0.55,
            'invierno' => 0.35,
            default => 0.50
        };

        $variacion = (mt_rand(-100, 100) / 1000) * $factorVariacion;
        $ndvi = max(0, min(1, $ndviBase + $variacion));

        return [
            'ndvi_promedio' => round($ndvi, 3),
            'ndvi_maximo' => round(min(1, $ndvi + 0.15), 3),
            'ndvi_minimo' => round(max(0, $ndvi - 0.15), 3),
            'ndvi_desviacion' => round(0.08, 3),
            'ndwi' => round(($ndvi - 0.2) * 0.6, 3),
            'gci' => round($ndvi * 0.8, 3),
            'lai' => round($ndvi * 2.5, 2),
            'cobertura_nubes' => mt_rand(5, 25),
            'calidad_imagen' => 'good',
            'estado_vegetacion' => $this->clasificarEstadoVegetacion($ndvi),
            'area_hectareas' => 100.0, // Área por defecto
        ];
    }

    private function obtenerEstacionActual(): string
    {
        $mes = Carbon::now()->month;
        
        return match(true) {
            in_array($mes, [12, 1, 2]) => 'invierno',
            in_array($mes, [3, 4, 5]) => 'primavera',
            in_array($mes, [6, 7, 8]) => 'verano',
            in_array($mes, [9, 10, 11]) => 'otoño',
            default => 'primavera'
        };
    }

    private function clasificarEstadoVegetacion(float $ndvi): string
    {
        return match(true) {
            $ndvi >= 0.7 => 'excellent',
            $ndvi >= 0.5 => 'good',
            $ndvi >= 0.3 => 'fair',
            default => 'poor'
        };
    }
}