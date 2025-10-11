<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SolicitudVerificacion;
use Livewire\WithPagination;

class GestionarSolicitudes extends Component
{
    use WithPagination;

    protected $listeners = ['institucionCreated' => 'handleInstitucionCreated'];

    public function aprobarSolicitud($solicitudId)
    {
        $solicitud = SolicitudVerificacion::findOrFail($solicitudId);

        // En lugar de redirigir, disparamos el evento para abrir el modal
        $this->dispatch('openInstitucionModal', solicitud: $solicitud->toArray());
    }

    public function rechazarSolicitud($solicitudId)
    {
        $solicitud = SolicitudVerificacion::findOrFail($solicitudId);
        $solicitud->estado = 'rechazada';
        $solicitud->save();

        $this->dispatch('banner-message', style: 'danger', message: 'Solicitud rechazada.');
    }

    public function handleInstitucionCreated()
    {
        // Simplemente refresca el componente para que la solicitud aprobada
        // ya no aparezca en la lista de pendientes.
        $this->render();
    }

    public function render()
    {
        $solicitudes = SolicitudVerificacion::where('estado', 'pendiente')->paginate(10);
        return view('livewire.admin.gestionar-solicitudes', [
            'solicitudes' => $solicitudes,
        ]);
    }
}