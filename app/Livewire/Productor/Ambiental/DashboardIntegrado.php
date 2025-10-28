<?php

namespace App\Livewire\Productor\Ambiental;

use Livewire\Component;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use App\Services\DashboardAmbientalService;
use App\Services\AlertaAmbientalService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardIntegrado extends Component
{
    public $productor;
    public $dashboard = [];
    public $alertasActivas = [];
    public $cargando = false;
    public $actualizando = false;
    public $filtroUnidad = 'todas';
    public $filtroPeriodo = 30;

    protected $dashboardService;
    protected $alertaService;

    public function boot(
        DashboardAmbientalService $dashboardService,
        AlertaAmbientalService $alertaService
    ) {
        $this->dashboardService = $dashboardService;
        $this->alertaService = $alertaService;
    }

    public function mount()
    {
        $this->productor = Productor::where('usuario_id', Auth::id())->first();
        
        if ($this->productor) {
            $this->cargarDashboard();
        }
    }

    public function updatedFiltroUnidad()
    {
        $this->cargarDashboard();
    }

    public function updatedFiltroPeriodo()
    {
        $this->cargarDashboard();
    }

    public function cargarDashboard()
    {
        if (!$this->productor) {
            return;
        }

        $this->cargando = true;

        try {
            // Obtener dashboard completo
            $this->dashboard = $this->dashboardService->obtenerDashboardCompleto($this->productor);
            
            // Filtrar alertas según filtros
            $this->filtrarAlertas();

        } catch (\Exception $e) {
            $this->addError('error', 'Error cargando dashboard: ' . $e->getMessage());
        } finally {
            $this->cargando = false;
        }
    }

    public function actualizarDatos()
    {
        $this->actualizando = true;

        try {
            // Generar alertas para todas las unidades
            $unidades = $this->productor->unidadesProductivas()
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->get();

            $alertasGeneradas = 0;
            foreach ($unidades as $unidad) {
                $alertas = $this->alertaService->generarAlertasUnidad($unidad);
                $alertasGuardadas = $this->alertaService->guardarAlertas($unidad, $alertas);
                $alertasGeneradas += count($alertasGuardadas);
            }

            // Recargar dashboard
            $this->cargarDashboard();
            
            session()->flash('success', "Dashboard actualizado. {$alertasGeneradas} nuevas alertas generadas.");

        } catch (\Exception $e) {
            $this->addError('error', 'Error actualizando datos: ' . $e->getMessage());
        } finally {
            $this->actualizando = false;
        }
    }

    public function marcarAlertaComoLeida($alertaId)
    {
        try {
            $alerta = AlertaAmbiental::find($alertaId);
            if ($alerta) {
                $alerta->marcarComoNotificada();
                $this->filtrarAlertas();
                session()->flash('success', 'Alerta marcada como leída');
            }
        } catch (\Exception $e) {
            $this->addError('error', 'Error marcando alerta: ' . $e->getMessage());
        }
    }

    public function desactivarAlerta($alertaId)
    {
        try {
            $alerta = AlertaAmbiental::find($alertaId);
            if ($alerta) {
                $alerta->desactivar();
                $this->filtrarAlertas();
                session()->flash('success', 'Alerta desactivada');
            }
        } catch (\Exception $e) {
            $this->addError('error', 'Error desactivando alerta: ' . $e->getMessage());
        }
    }

    private function filtrarAlertas()
    {
        $unidades = $this->productor->unidadesProductivas();
        
        if ($this->filtroUnidad !== 'todas') {
            $unidades = $unidades->where('id', $this->filtroUnidad);
        }

        $unidades = $unidades->get();

        $this->alertasActivas = AlertaAmbiental::whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->activas()
            ->where('fecha_inicio', '>=', Carbon::now()->subDays($this->filtroPeriodo))
            ->orderBy('severidad', 'desc')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }

    public function obtenerUnidadesProductivas()
    {
        return $this->productor->unidadesProductivas()
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get();
    }

    public function render()
    {
        return view('livewire.productor.ambiental.dashboard-integrado');
    }
}
