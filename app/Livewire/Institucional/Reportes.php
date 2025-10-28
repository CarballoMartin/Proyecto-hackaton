<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use App\Services\ReportesInstitucionalesService;
use App\Services\EstadisticasService;
use App\Services\ChartJsBuilder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.institucional')]
class Reportes extends Component
{
    public $institucion;
    public $filtros = [
        'fecha_inicio' => '',
        'fecha_fin' => '',
        'incluir_graficos' => true,
        'tipo_reporte' => 'completo',
    ];
    
    public $estadisticas = [];
    public $reporte = [];
    public $graficos = [];
    public $metricasRendimiento = [];
    public $cargando = false;
    public $mostrarFiltros = false;

    protected $reportesService;
    protected $estadisticasService;
    protected $chartBuilder;

    public function boot(
        ReportesInstitucionalesService $reportesService,
        EstadisticasService $estadisticasService,
        ChartJsBuilder $chartBuilder
    ) {
        $this->reportesService = $reportesService;
        $this->estadisticasService = $estadisticasService;
        $this->chartBuilder = $chartBuilder;
    }

    public function mount()
    {
        $user = Auth::user();
        $this->institucion = $user->institucionParticipante?->institucion;
        
        if (!$this->institucion) {
            abort(403, 'No tienes acceso a esta institución');
        }

        // Establecer fechas por defecto (últimos 6 meses)
        $this->filtros['fecha_inicio'] = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        $this->filtros['fecha_fin'] = Carbon::now()->endOfMonth()->format('Y-m-d');

        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->cargando = true;
        
        try {
            // Cargar estadísticas básicas
            $this->estadisticas = $this->estadisticasService->getEstadisticasInstitucionales($this->institucion->id);
            
            // Cargar métricas de rendimiento
            $this->metricasRendimiento = $this->reportesService->obtenerMetricasRendimiento($this->institucion->id);
            
            // Cargar gráficos básicos
            $this->cargarGraficos();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los datos: ' . $e->getMessage());
        } finally {
            $this->cargando = false;
        }
    }

    public function generarReporte()
    {
        $this->cargando = true;
        
        try {
            $this->reporte = $this->reportesService->generarReporteCompleto(
                $this->institucion->id,
                $this->filtros
            );
            
            session()->flash('success', 'Reporte generado exitosamente');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        } finally {
            $this->cargando = false;
        }
    }

    public function exportarPDF()
    {
        try {
            $rutaArchivo = $this->reportesService->exportarReportePDF(
                $this->institucion->id,
                $this->filtros
            );
            
            session()->flash('success', "Reporte PDF generado: {$rutaArchivo}");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar PDF: ' . $e->getMessage());
        }
    }

    public function exportarExcel()
    {
        try {
            $rutaArchivo = $this->reportesService->exportarReporteExcel(
                $this->institucion->id,
                $this->filtros
            );
            
            session()->flash('success', "Reporte Excel generado: {$rutaArchivo}");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar Excel: ' . $e->getMessage());
        }
    }

    public function actualizarFiltros()
    {
        $this->cargarDatos();
        $this->mostrarFiltros = false;
    }

    public function limpiarFiltros()
    {
        $this->filtros = [
            'fecha_inicio' => Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->endOfMonth()->format('Y-m-d'),
            'incluir_graficos' => true,
            'tipo_reporte' => 'completo',
        ];
        
        $this->cargarDatos();
    }

    public function refrescarDatos()
    {
        // Limpiar caché y recargar datos
        $this->reportesService->limpiarCacheReportes($this->institucion->id);
        $this->cargarDatos();
        
        session()->flash('success', 'Datos actualizados correctamente');
    }

    private function cargarGraficos()
    {
        try {
            // Composición por especies
            $composicionEspecies = $this->estadisticasService->getComposicionPorEspecie($this->institucion->id);
            if (!empty($composicionEspecies)) {
                $this->graficos['composicion_especies'] = $this->chartBuilder->buildDoughnutChart(
                    'Composición por Especies',
                    array_keys($composicionEspecies),
                    array_values($composicionEspecies)
                );
            }

            // Composición por categorías
            $composicionCategorias = $this->estadisticasService->getComposicionPorCategoria($this->institucion->id);
            if (!empty($composicionCategorias)) {
                $this->graficos['composicion_categorias'] = $this->chartBuilder->buildPieChart(
                    'Composición por Categorías',
                    array_keys($composicionCategorias),
                    array_values($composicionCategorias)
                );
            }

            // Evolución del stock
            $evolucionStock = $this->estadisticasService->getEvolucionStockMensual($this->institucion->id, 12);
            if (!empty($evolucionStock)) {
                $this->graficos['evolucion_stock'] = $this->chartBuilder->buildLineChart(
                    'Evolución del Stock (Últimos 12 meses)',
                    array_keys($evolucionStock),
                    [[
                        'label' => 'Stock Total',
                        'data' => array_values($evolucionStock),
                        'fill' => true,
                    ]]
                );
            }

            // Participantes por mes
            $participantesPorMes = $this->estadisticasService->getParticipantesPorMes($this->institucion->id, 12);
            if (!empty($participantesPorMes)) {
                $this->graficos['participantes_por_mes'] = $this->chartBuilder->buildBarChart(
                    'Participantes por Mes',
                    array_keys($participantesPorMes),
                    array_values($participantesPorMes)
                );
            }

            // Solicitudes por estado
            $solicitudesPorEstado = $this->estadisticasService->getSolicitudesPorEstado($this->institucion->id);
            if (!empty($solicitudesPorEstado)) {
                $this->graficos['solicitudes_por_estado'] = $this->chartBuilder->buildHorizontalBarChart(
                    'Solicitudes por Estado',
                    array_keys($solicitudesPorEstado),
                    array_values($solicitudesPorEstado)
                );
            }

        } catch (\Exception $e) {
            // Log del error pero no interrumpir la carga
            \Log::error('Error al cargar gráficos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.institucional.reportes');
    }
}
