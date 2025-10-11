<?php

namespace App\Livewire\Productor\UnidadesProductivas;

use Livewire\Component;
use App\Models\UnidadProductiva;
use Livewire\Attributes\Layout;

#[Layout('layouts.mapa')] // Using a new layout for the full-screen map
class MapaChacra extends Component
{
    public UnidadProductiva $up;
    public $locations = [];
    public $backUrl;

    public function mount($id)
    {
        $this->up = UnidadProductiva::findOrFail($id);
        $this->backUrl = route('productor.chacras.gestionar', ['id' => $id]);

        $productor = $this->up->productores->first();
        if ($productor) {
            $allUps = $productor->unidadesProductivas()->whereNotNull('latitud')->whereNotNull('longitud')->get();
            foreach ($allUps as $unidad) {
                $this->locations[] = [
                    'lat' => $unidad->latitud,
                    'lon' => $unidad->longitud,
                    'name' => $unidad->nombre,
                    'is_current' => $unidad->id === $this->up->id,
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.productor.unidades-productivas.mapa-chacra');
    }
}