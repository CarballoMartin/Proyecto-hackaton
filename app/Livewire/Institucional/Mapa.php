<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\UnidadProductiva;
use App\Models\Productor;

class Mapa extends Component
{
    public $productorId = null;

    public function mount()
    {
        // Capturar el parámetro productorId de la URL si existe
        $this->productorId = request()->query('productor');
    }

    public function render()
    {
        $query = UnidadProductiva::whereNotNull('latitud')
                    ->whereNotNull('longitud');

        // Filtrar por productor si se proporciona
        if ($this->productorId) {
            $query->whereHas('productores', function($q) {
                $q->where('productors.id', $this->productorId);
            });
        }

        $totalUnidades = $query->count();
        
        $totalProductores = Productor::count();
        
        $superficieTotal = UnidadProductiva::whereNotNull('superficie')
                            ->sum('superficie');

        $productorNombre = null;
        if ($this->productorId) {
            $productor = Productor::find($this->productorId);
            $productorNombre = $productor ? $productor->nombre : null;
        }

        // Detectar el rol del usuario para usar el layout correcto
        $layout = auth()->user()->hasRole('superadmin') ? 'layouts.admin' : 'layouts.institucional';

        return view('livewire.institucional.mapa-funcional', [
            'totalUnidades' => $totalUnidades,
            'totalProductores' => $totalProductores,
            'superficieTotal' => $superficieTotal,
            'productorId' => $this->productorId,
            'productorNombre' => $productorNombre,
        ])->layout($layout);
    }
}
