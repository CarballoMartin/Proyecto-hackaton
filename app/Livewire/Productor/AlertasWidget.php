<?php

namespace App\Livewire\Productor;

use App\Models\Productor;
use App\Services\AlertasAmbientalesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AlertasWidget extends Component
{
    public $alertasActivas = [];
    public $cantidadNoLeidas = 0;
    public $mostrarLista = false;

    protected $listeners = ['alertaMarcadaComoLeida' => 'cargarAlertas'];

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
        $this->cantidadNoLeidas = $alertasService->contarAlertasNoLeidasParaProductor($productor->id);
    }

    public function toggleLista()
    {
        $this->mostrarLista = !$this->mostrarLista;
    }

    public function marcarComoLeida($alertaId)
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();
        
        if (!$productor) {
            return;
        }

        $alerta = \App\Models\AlertaAmbiental::find($alertaId);

        // ✅ Validación de permisos
        if (!$alerta) {
            $this->dispatch('error', [
                'mensaje' => 'Alerta no encontrada'
            ]);
            return;
        }

        if (!$this->perteneceAlProductor($alerta, $productor)) {
            $this->dispatch('error', [
                'mensaje' => 'No tienes permiso para modificar esta alerta'
            ]);
            
            Log::warning('Intento de modificar alerta de otro productor', [
                'usuario_id' => Auth::id(),
                'alerta_id' => $alertaId,
                'unidad_productiva_id' => $alerta->unidad_productiva_id,
            ]);
            
            return;
        }

        $alerta->marcarComoLeida();
        $this->cargarAlertas();
        
        $this->dispatch('alerta-leida', [
            'mensaje' => 'Alerta marcada como leída'
        ]);
    }

    public function marcarTodasComoLeidas()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if ($productor) {
            \App\Models\AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productor) {
                $query->where('productors.id', $productor->id);
            })
            ->activas()
            ->noLeidas()
            ->update(['leida' => true]);

            $this->cargarAlertas();
            
            $this->dispatch('alerta-leida', [
                'mensaje' => 'Todas las alertas marcadas como leídas'
            ]);
        }
    }

    private function perteneceAlProductor($alerta, $productor): bool
    {
        return $alerta->unidadProductiva
            ->productores()
            ->where('productors.id', $productor->id)
            ->exists();
    }

    public function render()
    {
        return view('livewire.productor.alertas-widget');
    }
}
