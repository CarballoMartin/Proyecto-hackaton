<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use App\Models\SolicitudVerificacion;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.institucional')]
class Reportes extends Component
{
    public $institucion;
    public $estadisticas = [];
    public $participantesPorMes = [];
    public $solicitudesPorEstado = [];
    
    public function mount()
    {
        $user = Auth::user();
        $this->institucion = $user->institucionParticipante?->institucion;
        $this->loadEstadisticas();
        $this->loadGraficos();
    }
    
    private function loadEstadisticas()
    {
        if (!$this->institucion) return;
        
        $this->estadisticas = [
            'total_participantes' => InstitucionalParticipante::where('institucion_id', $this->institucion->id)
                ->where('activo', true)->count(),
            'total_solicitudes' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)->count(),
            'solicitudes_aprobadas' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                ->where('estado', 'aprobada')->count(),
            'solicitudes_pendientes' => SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                ->where('estado', 'pendiente')->count(),
            'productores_verificados' => Productor::whereHas('solicitudesVerificacion', function($query) {
                $query->where('institucion_id', $this->institucion->id)
                      ->where('estado', 'aprobada');
            })->count(),
        ];
    }
    
    private function loadGraficos()
    {
        if (!$this->institucion) return;
        
        // Participantes por mes (últimos 6 meses)
        $this->participantesPorMes = InstitucionalParticipante::where('institucion_id', $this->institucion->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('total', 'mes')
            ->toArray();
            
        // Solicitudes por estado
        $this->solicitudesPorEstado = SolicitudVerificacion::where('institucion_id', $this->institucion->id)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
    }
    
    public function render()
    {
        return view('livewire.institucional.reportes');
    }
}
