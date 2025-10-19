<?php

namespace App\Livewire\Productor;

use App\Models\Productor;
use App\Services\AlertasAmbientalesService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlertasPanel extends Component
{
    public $alertasActivas = [];
    public $mostrarTodas = false;

    public function mount()
    {
        $this->cargarAlertas();
    }

    public function cargarAlertas()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        $alertasService = app(AlertasAmbientalesService::class);
        $this->alertasActivas = $alertasService->obtenerAlertasActivasParaProductor($productor->id);
    }

    public function toggleMostrarTodas()
    {
        $this->mostrarTodas = !$this->mostrarTodas;
    }

    public function render()
    {
        return view('livewire.productor.alertas-panel');
    }
}
