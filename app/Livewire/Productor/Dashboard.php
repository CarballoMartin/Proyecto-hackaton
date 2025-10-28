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
        $stockPorEspecie = collect();
        $ultimaActualizacion = null;
        
        if ($productor) {
            $totalCampos = $productor->unidadesProductivas->count();
            
            // Obtener stock animal por especie dinámicamente
            $unidadesProductivasIds = $productor->unidadesProductivas->pluck('id');
            $stockAnimal = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductivasIds)
                ->with('especie')
                ->get();
            
            // Agrupar por especie dinámicamente
            $stockPorEspecie = $stockAnimal->groupBy('especie.nombre')->map(function ($animales) {
                return $animales->sum('cantidad');
            });
            
            $ultimaActualizacion = $productor->updated_at;
        }
        
        return view('livewire.productor.dashboard', [
            'productor' => $productor,
            'totalCampos' => $totalCampos,
            'stockPorEspecie' => $stockPorEspecie,
            'ultimaActualizacion' => $ultimaActualizacion,
        ]);
    }
}
