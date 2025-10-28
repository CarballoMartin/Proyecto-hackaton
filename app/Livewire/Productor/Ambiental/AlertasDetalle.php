<?php

namespace App\Livewire\Productor\Ambiental;

use App\Models\AlertaAmbiental;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AlertasDetalle extends Component
{
    use WithPagination;

    public $filtroTipo = '';
    public $filtroNivel = '';
    public $filtroEstado = 'activas'; // activas, todas, inactivas
    public $busqueda = '';

    protected $queryString = ['filtroTipo', 'filtroNivel', 'filtroEstado', 'busqueda'];

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo()
    {
        $this->resetPage();
    }

    public function updatingFiltroNivel()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function marcarLeida($alertaId)
    {
        $alerta = AlertaAmbiental::find($alertaId);
        
        if ($alerta && $this->perteneceAlProductor($alerta)) {
            $alerta->marcarComoLeida();
            $this->dispatch('alerta-actualizada');
        }
    }

    public function desactivarAlerta($alertaId)
    {
        $alerta = AlertaAmbiental::find($alertaId);
        
        if ($alerta && $this->perteneceAlProductor($alerta)) {
            $alerta->desactivar();
            $this->dispatch('alerta-actualizada');
        }
    }

    public function reactivarAlerta($alertaId)
    {
        $alerta = AlertaAmbiental::find($alertaId);
        
        if ($alerta && $this->perteneceAlProductor($alerta)) {
            $alerta->update(['activa' => true]);
            $this->dispatch('alerta-actualizada');
        }
    }

    protected function perteneceAlProductor(AlertaAmbiental $alerta): bool
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();
        
        if (!$productor) {
            return false;
        }

        return $alerta->unidadProductiva->productor_id === $productor->id;
    }

    public function limpiarFiltros()
    {
        $this->filtroTipo = '';
        $this->filtroNivel = '';
        $this->filtroEstado = 'activas';
        $this->busqueda = '';
        $this->resetPage();
    }

    public function render()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        $query = AlertaAmbiental::query()
            ->whereHas('unidadProductiva', function ($q) use ($productor) {
                $q->where('productor_id', $productor->id);
            })
            ->with('unidadProductiva');

        // Filtros
        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroNivel) {
            $query->where('nivel', $this->filtroNivel);
        }

        if ($this->filtroEstado === 'activas') {
            $query->where('activa', true);
        } elseif ($this->filtroEstado === 'inactivas') {
            $query->where('activa', false);
        }

        if ($this->busqueda) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('mensaje', 'like', '%' . $this->busqueda . '%')
                  ->orWhereHas('unidadProductiva', function ($subQ) {
                      $subQ->where('nombre', 'like', '%' . $this->busqueda . '%');
                  });
            });
        }

        $alertas = $query->orderByRaw("CASE nivel 
                WHEN 'critico' THEN 1 
                WHEN 'alto' THEN 2 
                WHEN 'medio' THEN 3 
                WHEN 'bajo' THEN 4 
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.productor.ambiental.alertas-detalle', [
            'alertas' => $alertas,
        ])->layout('layouts.productor');
    }
}
