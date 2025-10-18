<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\SolicitudVerificacion;
use App\Models\Institucion;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.institucional')]
class Solicitudes extends Component
{
    public $solicitudes;
    public $institucion;
    
    public function mount()
    {
        $user = Auth::user();
        $this->institucion = $user->institucionParticipante?->institucion;
        
        if ($this->institucion) {
            $this->solicitudes = SolicitudVerificacion::where('institucion_id', $this->institucion->id)
                ->with(['productor', 'institucion'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->solicitudes = collect();
        }
    }
    
    public function aprobarSolicitud($solicitudId)
    {
        $solicitud = SolicitudVerificacion::findOrFail($solicitudId);
        $solicitud->update([
            'estado' => 'aprobada',
            'fecha_respuesta' => now(),
            'observaciones' => 'Solicitud aprobada por la institución.'
        ]);
        
        session()->flash('message', 'Solicitud aprobada correctamente.');
        $this->mount(); // Recargar datos
    }
    
    public function rechazarSolicitud($solicitudId)
    {
        $solicitud = SolicitudVerificacion::findOrFail($solicitudId);
        $solicitud->update([
            'estado' => 'rechazada',
            'fecha_respuesta' => now(),
            'observaciones' => 'Solicitud rechazada por la institución.'
        ]);
        
        session()->flash('message', 'Solicitud rechazada correctamente.');
        $this->mount(); // Recargar datos
    }
    
    public function render()
    {
        return view('livewire.institucional.solicitudes');
    }
}








