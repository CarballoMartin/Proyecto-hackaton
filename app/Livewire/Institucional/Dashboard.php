<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use App\Models\SolicitudVerificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.institucional')]
class Dashboard extends Component
{
    public $institucion;
    public $participantesCount = 0;
    public $solicitudesPendientes = 0;
    public $estadisticas = [];
    
    // Propiedades para optimización
    protected $listeners = ['refreshStats' => 'loadEstadisticas'];

    public function mount()
    {
        $this->loadInstitucion();
        $this->loadEstadisticas();
    }

    private function loadInstitucion()
    {
        $user = Auth::user();
        $this->institucion = $user->institucionParticipante?->institucion;
    }

    private function loadEstadisticas()
    {
        if (!$this->institucion) {
            return;
        }

        // Usar cache para optimizar consultas - aumentado a 10 minutos
        $cacheKey = "institucion_stats_{$this->institucion->id}";
        
        $this->estadisticas = Cache::remember($cacheKey, 600, function () {
            // Optimizar consultas con select específico y índices
            return [
                'participantes' => InstitucionalParticipante::where('institucion_id', $this->institucion->id)
                    ->where('activo', true)
                    ->selectRaw('COUNT(*) as count')
                    ->value('count') ?? 0,
                'solicitudes' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                    ->where('estado', 'pendiente')
                    ->selectRaw('COUNT(*) as count')
                    ->value('count') ?? 0,
                'estado' => $this->institucion->validada ? 'Verificada' : 'Pendiente',
                
                // Nuevas métricas de actividad
                'nuevos_miembros_mes' => InstitucionalParticipante::where('institucion_id', $this->institucion->id)
                    ->where('created_at', '>=', now()->subMonth())
                    ->count(),
                'actividad_ultima_semana' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                    ->where('updated_at', '>=', now()->subWeek())
                    ->count(),
                'solicitudes_procesadas' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                    ->whereIn('estado', ['aprobada', 'rechazada'])
                    ->count(),
                'solicitudes_pendientes_total' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                    ->where('estado', 'pendiente')
                    ->count(),
            ];
        });

        $this->participantesCount = $this->estadisticas['participantes'];
        $this->solicitudesPendientes = $this->estadisticas['solicitudes'];
    }

    // Métodos para acciones (optimizados)
    public function revisarSolicitudes()
    {
        $this->dispatch('openModal', 'revisar-solicitudes');
    }

    public function verReportes()
    {
        $this->dispatch('openModal', 'ver-reportes');
    }

    // Método para refrescar estadísticas
    public function refreshStats()
    {
        Cache::forget("institucion_stats_{$this->institucion->id}");
        $this->loadEstadisticas();
    }


    public function render()
    {
        return view('livewire.institucional.dashboard');
    }
}
