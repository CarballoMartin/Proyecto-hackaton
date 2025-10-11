<?php

namespace App\Livewire\Institucional\Participantes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\InstitucionalParticipante;
use Carbon\Carbon;

#[Layout('layouts.institucional')]
class VerParticipante extends Component
{
    // Propiedades del componente
    public $participante;
    public $participanteId;

    public function mount($participante)
    {
        $this->participante = $participante;
        $this->participanteId = $participante->id;
        
        // Cargar relaciones necesarias
        $this->participante->load(['user', 'institucion']);
    }

    public function cerrar()
    {
        $this->dispatch('cerrar-modal-ver');
    }

    public function editar()
    {
        $this->dispatch('cerrar-modal-ver');
        $this->dispatch('editar-participante', $this->participanteId);
    }

    public function eliminar()
    {
        $this->dispatch('cerrar-modal-ver');
        $this->dispatch('eliminar-participante', $this->participanteId);
    }

    public function toggleEstado()
    {
        $this->participante->update(['activo' => !$this->participante->activo]);
        $this->participante->refresh();
        
        session()->flash('message', 'Estado del participante actualizado.');
    }

    public function getTiempoEnInstitucionProperty()
    {
        if (!$this->participante->fecha_ingreso) {
            return 'No especificado';
        }

        $fecha = Carbon::parse($this->participante->fecha_ingreso);
        return $fecha->diffForHumans();
    }

    public function getDiasActivoProperty()
    {
        if (!$this->participante->fecha_ingreso) {
            return null;
        }

        return Carbon::parse($this->participante->fecha_ingreso)->diffInDays(now());
    }

    public function render()
    {
        return view('livewire.institucional.participantes.ver-participante');
    }
}
