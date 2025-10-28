<?php

namespace App\Services;

use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use App\Models\SolicitudVerificacion;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ReportesInstitucionalesService
{
    protected $estadisticasService;
    protected $chartBuilder;

    public function __construct(EstadisticasService $estadisticasService, ChartJsBuilder $chartBuilder)
    {
        $this->estadisticasService = $estadisticasService;
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * Genera un reporte completo para una institución.
     *
     * @param int $institucionId
     * @param array $filtros
     * @return array
     */
    public function generarReporteCompleto(int $institucionId, array $filtros = []): array
    {
        $institucion = Institucion::findOrFail($institucionId);
        
        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->subMonths(6)->startOfMonth();
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now()->endOfMonth();
        $incluirGraficos = $filtros['incluir_graficos'] ?? true;

        $cacheKey = "reporte.completo.institucion.{$institucionId}." . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, 300, function () use ($institucion, $fechaInicio, $fechaFin, $incluirGraficos) {
            $reporte = [
                'institucion' => [
                    'id' => $institucion->id,
                    'nombre' => $institucion->nombre,
                    'codigo' => $institucion->codigo,
                    'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
                    'periodo' => [
                        'inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                        'fin' => Carbon::parse($fechaFin)->format('d/m/Y'),
                    ],
                ],
                'resumen_ejecutivo' => $this->generarResumenEjecutivo($institucion->id, $fechaInicio, $fechaFin),
                'estadisticas_detalladas' => $this->generarEstadisticasDetalladas($institucion->id, $fechaInicio, $fechaFin),
                'analisis_tendencias' => $this->generarAnalisisTendencias($institucion->id, $fechaInicio, $fechaFin),
            ];

            if ($incluirGraficos) {
                $reporte['graficos'] = $this->generarGraficosReporte($institucion->id, $fechaInicio, $fechaFin);
            }

            return $reporte;
        });
    }

    /**
     * Genera el resumen ejecutivo del reporte.
     *
     * @param int $institucionId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    private function generarResumenEjecutivo(int $institucionId, string $fechaInicio, string $fechaFin): array
    {
        $estadisticas = $this->estadisticasService->getEstadisticasInstitucionales($institucionId);
        
        // Cálculo de crecimiento de participantes
        $participantesAnteriores = InstitucionalParticipante::where('institucion_id', $institucionId)
            ->where('created_at', '<', Carbon::parse($fechaInicio))
            ->count();
        
        $participantesNuevos = InstitucionalParticipante::where('institucion_id', $institucionId)
            ->whereBetween('created_at', [Carbon::parse($fechaInicio), Carbon::parse($fechaFin)])
            ->count();
        
        $crecimientoParticipantes = $participantesAnteriores > 0 
            ? round(($participantesNuevos / $participantesAnteriores) * 100, 2)
            : 0;

        // Tasa de aprobación de solicitudes
        $totalSolicitudes = $estadisticas['total_solicitudes'];
        $solicitudesAprobadas = $estadisticas['solicitudes_aprobadas'];
        $tasaAprobacion = $totalSolicitudes > 0 
            ? round(($solicitudesAprobadas / $totalSolicitudes) * 100, 2)
            : 0;

        return [
            'participantes' => [
                'total' => $estadisticas['total_participantes'],
                'nuevos_periodo' => $participantesNuevos,
                'crecimiento_porcentual' => $crecimientoParticipantes,
            ],
            'solicitudes' => [
                'total' => $totalSolicitudes,
                'aprobadas' => $solicitudesAprobadas,
                'pendientes' => $estadisticas['solicitudes_pendientes'],
                'rechazadas' => $estadisticas['solicitudes_rechazadas'],
                'tasa_aprobacion' => $tasaAprobacion,
            ],
            'productores_verificados' => $estadisticas['productores_verificados'],
            'participantes_nuevos_mes' => $estadisticas['participantes_nuevos_mes'],
        ];
    }

    /**
     * Genera estadísticas detalladas del reporte.
     *
     * @param int $institucionId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    private function generarEstadisticasDetalladas(int $institucionId, string $fechaInicio, string $fechaFin): array
    {
        return [
            'composicion_especies' => $this->estadisticasService->getComposicionPorEspecie($institucionId),
            'composicion_categorias' => $this->estadisticasService->getComposicionPorCategoria($institucionId),
            'evolucion_stock' => $this->estadisticasService->getEvolucionStockMensual($institucionId, 12),
            'resumen_unidades_productivas' => $this->estadisticasService->getResumenPorUnidadProductiva($institucionId),
            'participantes_por_mes' => $this->estadisticasService->getParticipantesPorMes($institucionId, 12),
            'solicitudes_por_estado' => $this->estadisticasService->getSolicitudesPorEstado($institucionId),
            'actividad_reciente' => $this->estadisticasService->getActividadReciente($institucionId, 20),
        ];
    }

    /**
     * Genera análisis de tendencias.
     *
     * @param int $institucionId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    private function generarAnalisisTendencias(int $institucionId, string $fechaInicio, string $fechaFin): array
    {
        // Análisis de crecimiento de participantes por mes
        $participantesPorMes = $this->estadisticasService->getParticipantesPorMes($institucionId, 12);
        $tendenciaParticipantes = $this->calcularTendencia(array_values($participantesPorMes));

        // Análisis de evolución del stock
        $evolucionStock = $this->estadisticasService->getEvolucionStockMensual($institucionId, 12);
        $tendenciaStock = $this->calcularTendencia(array_values($evolucionStock));

        // Análisis de solicitudes por mes
        $solicitudesPorMes = SolicitudVerificacion::where('institucion_id', $institucionId)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->whereBetween('created_at', [Carbon::parse($fechaInicio), Carbon::parse($fechaFin)])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('total', 'mes')
            ->toArray();
        
        $tendenciaSolicitudes = $this->calcularTendencia(array_values($solicitudesPorMes));

        return [
            'participantes' => [
                'tendencia' => $tendenciaParticipantes,
                'descripcion' => $this->describirTendencia($tendenciaParticipantes, 'participantes'),
            ],
            'stock' => [
                'tendencia' => $tendenciaStock,
                'descripcion' => $this->describirTendencia($tendenciaStock, 'stock'),
            ],
            'solicitudes' => [
                'tendencia' => $tendenciaSolicitudes,
                'descripcion' => $this->describirTendencia($tendenciaSolicitudes, 'solicitudes'),
            ],
        ];
    }

    /**
     * Genera gráficos para el reporte.
     *
     * @param int $institucionId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    private function generarGraficosReporte(int $institucionId, string $fechaInicio, string $fechaFin): array
    {
        $composicionEspecies = $this->estadisticasService->getComposicionPorEspecie($institucionId);
        $composicionCategorias = $this->estadisticasService->getComposicionPorCategoria($institucionId);
        $evolucionStock = $this->estadisticasService->getEvolucionStockMensual($institucionId, 12);
        $participantesPorMes = $this->estadisticasService->getParticipantesPorMes($institucionId, 12);
        $solicitudesPorEstado = $this->estadisticasService->getSolicitudesPorEstado($institucionId);

        return [
            'composicion_especies' => $this->chartBuilder->buildDoughnutChart(
                'Composición por Especies',
                array_keys($composicionEspecies),
                array_values($composicionEspecies)
            ),
            'composicion_categorias' => $this->chartBuilder->buildPieChart(
                'Composición por Categorías',
                array_keys($composicionCategorias),
                array_values($composicionCategorias)
            ),
            'evolucion_stock' => $this->chartBuilder->buildLineChart(
                'Evolución del Stock',
                array_keys($evolucionStock),
                [[
                    'label' => 'Stock Total',
                    'data' => array_values($evolucionStock),
                    'fill' => true,
                ]]
            ),
            'participantes_por_mes' => $this->chartBuilder->buildBarChart(
                'Participantes por Mes',
                array_keys($participantesPorMes),
                array_values($participantesPorMes)
            ),
            'solicitudes_por_estado' => $this->chartBuilder->buildHorizontalBarChart(
                'Solicitudes por Estado',
                array_keys($solicitudesPorEstado),
                array_values($solicitudesPorEstado)
            ),
        ];
    }

    /**
     * Calcula la tendencia de una serie de datos.
     *
     * @param array $datos
     * @return float
     */
    private function calcularTendencia(array $datos): float
    {
        if (count($datos) < 2) {
            return 0;
        }

        $n = count($datos);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i;
            $y = $datos[$i];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $pendiente = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        return round($pendiente, 4);
    }

    /**
     * Describe una tendencia en texto legible.
     *
     * @param float $tendencia
     * @param string $tipo
     * @return string
     */
    private function describirTendencia(float $tendencia, string $tipo): string
    {
        if ($tendencia > 0.1) {
            return "Crecimiento positivo en {$tipo}";
        } elseif ($tendencia < -0.1) {
            return "Decrecimiento en {$tipo}";
        } else {
            return "Estabilidad en {$tipo}";
        }
    }

    /**
     * Exporta el reporte a PDF.
     *
     * @param int $institucionId
     * @param array $filtros
     * @return string Ruta del archivo generado
     */
    public function exportarReportePDF(int $institucionId, array $filtros = []): string
    {
        $reporte = $this->generarReporteCompleto($institucionId, $filtros);
        $institucion = Institucion::findOrFail($institucionId);
        
        // Generar nombre de archivo único
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $nombreArchivo = "reporte_institucional_{$institucion->codigo}_{$timestamp}.pdf";
        
        // Por ahora retornamos la ruta, la implementación real de PDF se hará después
        return "reports/{$nombreArchivo}";
    }

    /**
     * Exporta el reporte a Excel.
     *
     * @param int $institucionId
     * @param array $filtros
     * @return string Ruta del archivo generado
     */
    public function exportarReporteExcel(int $institucionId, array $filtros = []): string
    {
        $reporte = $this->generarReporteCompleto($institucionId, $filtros);
        $institucion = Institucion::findOrFail($institucionId);
        
        // Generar nombre de archivo único
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $nombreArchivo = "reporte_institucional_{$institucion->codigo}_{$timestamp}.xlsx";
        
        // Por ahora retornamos la ruta, la implementación real de Excel se hará después
        return "reports/{$nombreArchivo}";
    }

    /**
     * Obtiene métricas de rendimiento de la institución.
     *
     * @param int $institucionId
     * @return array
     */
    public function obtenerMetricasRendimiento(int $institucionId): array
    {
        return Cache::remember("metricas.rendimiento.institucion.{$institucionId}", 600, function () use ($institucionId) {
            $estadisticas = $this->estadisticasService->getEstadisticasInstitucionales($institucionId);
            
            // Calcular métricas de rendimiento
            $totalSolicitudes = $estadisticas['total_solicitudes'];
            $solicitudesAprobadas = $estadisticas['solicitudes_aprobadas'];
            $tasaAprobacion = $totalSolicitudes > 0 ? ($solicitudesAprobadas / $totalSolicitudes) * 100 : 0;
            
            $totalParticipantes = $estadisticas['total_participantes'];
            $participantesNuevosMes = $estadisticas['participantes_nuevos_mes'];
            $tasaCrecimiento = $totalParticipantes > 0 ? ($participantesNuevosMes / $totalParticipantes) * 100 : 0;

            return [
                'tasa_aprobacion_solicitudes' => round($tasaAprobacion, 2),
                'tasa_crecimiento_participantes' => round($tasaCrecimiento, 2),
                'productores_por_participante' => $totalParticipantes > 0 ? round($estadisticas['productores_verificados'] / $totalParticipantes, 2) : 0,
                'eficiencia_gestion' => $this->calcularEficienciaGestion($institucionId),
            ];
        });
    }

    /**
     * Calcula la eficiencia de gestión de la institución.
     *
     * @param int $institucionId
     * @return float
     */
    private function calcularEficienciaGestion(int $institucionId): float
    {
        $estadisticas = $this->estadisticasService->getEstadisticasInstitucionales($institucionId);
        
        $totalSolicitudes = $estadisticas['total_solicitudes'];
        $solicitudesPendientes = $estadisticas['solicitudes_pendientes'];
        
        if ($totalSolicitudes == 0) {
            return 100;
        }
        
        $eficiencia = (($totalSolicitudes - $solicitudesPendientes) / $totalSolicitudes) * 100;
        return round($eficiencia, 2);
    }

    /**
     * Limpia caché de reportes de una institución.
     *
     * @param int $institucionId
     * @return void
     */
    public function limpiarCacheReportes(int $institucionId): void
    {
        $patterns = [
            "reporte.*.institucion.{$institucionId}",
            "metricas.rendimiento.institucion.{$institucionId}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        // También limpiar caché de estadísticas
        $this->estadisticasService->limpiarCacheInstitucion($institucionId);
    }
}
