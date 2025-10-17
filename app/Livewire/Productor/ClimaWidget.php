<?php

namespace App\Livewire\Productor;

use App\Models\DatoClimaticoCache;
use App\Models\Productor;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ClimaWidget extends Component
{
    public $datosClima = null;
    public $unidadSeleccionada = null;

    public function mount()
    {
        $this->cargarDatosClima();
    }

    public function cargarDatosClima()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        // Obtener la primera unidad con datos de clima
        $this->unidadSeleccionada = $productor->unidadesProductivas()
            ->whereHas('datosClimaticos')
            ->with('datosClimaticos')
            ->first();

        if ($this->unidadSeleccionada) {
            $this->datosClima = $this->unidadSeleccionada->datosClimaticos()
                ->latest('fecha_consulta')
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.productor.clima-widget');
    }
}
