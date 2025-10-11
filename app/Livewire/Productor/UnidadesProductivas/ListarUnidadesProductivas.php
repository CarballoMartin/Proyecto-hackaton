<?php

namespace App\Livewire\Productor\UnidadesProductivas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On; // ← Importa el atributo On
use App\Models\Productor;
use App\Models\UnidadProductiva;

#[On('unidadProductivaEliminada')] // ← Escucha el evento 'unidadProductivaEliminada'

#[Layout('layouts.app')]
class ListarUnidadesProductivas extends Component
{
    public $productor;
    public $unidadesProductivas;

    public function mount()
    {
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        if ($this->productor) {
            $this->unidadesProductivas = $this->productor->unidadesProductivas()
                                ->with(['campo', 'tipoPasto', 'tipoSuelo', 'fuenteAguaHumano', 'fuenteAguaAnimal'])
                                ->get();
        } else {
            $this->unidadesProductivas = collect();
        }
    }

    public function render()
    {
        return view('livewire.productor.unidades-productivas.listar-unidades-productivas');
    }
}
