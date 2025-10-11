<?php

namespace App\Livewire\Productor;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use App\Models\StockAnimal;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        // Obtener el productor asociado al usuario autenticado
        $user = auth()->user();
        $productor = Productor::where('usuario_id', $user->id)->first();
        
        // Estadísticas básicas
        $totalCampos = 0;
        $totalOvinos = 0;
        $totalCaprinos = 0;
        $ultimaActualizacion = null;
        
        if ($productor) {
            $totalCampos = $productor->unidadesProductivas->count();
            
            // Obtener stock animal por especie
            $unidadesProductivasIds = $productor->unidadesProductivas->pluck('id');
            $stockAnimal = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductivasIds)->get();
            $totalOvinos = $stockAnimal->where('especie_id', 1)->sum('cantidad'); // Asumiendo que 1 es ovino
            $totalCaprinos = $stockAnimal->where('especie_id', 2)->sum('cantidad'); // Asumiendo que 2 es caprino
            
            $ultimaActualizacion = $productor->updated_at;
        }
        
        return view('livewire.productor.dashboard', [
            'productor' => $productor,
            'totalCampos' => $totalCampos,
            'totalOvinos' => $totalOvinos,
            'totalCaprinos' => $totalCaprinos,
            'ultimaActualizacion' => $ultimaActualizacion,
        ]);
    }
}
