<?php

namespace App\Livewire\Productor\Ambiental;

use App\Models\AlertaAmbiental;
use App\Models\DatoClimaticoCache;
use App\Models\Productor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class General extends Component
{
    public $alertasActivas = [];
    public $alertasCriticas = [];
    public $datosClima;
    public $unidadesConAlertas = [];
    public $estadisticas = [];

    public function mount()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $productor = Productor::where('usuario_id', Auth::id())->first();

        if (!$productor) {
            return;
        }

        // Obtener IDs de unidades productivas del productor
        $unidadIds = $productor->unidadesProductivas()->pluck('unidades_productivas.id');
        
        // Obtener alertas activas
        $this->alertasActivas = AlertaAmbiental::activas()
            ->whereIn('unidad_productiva_id', $unidadIds)
            ->with('unidadProductiva')
            ->orderByRaw("CASE nivel 
                WHEN 'critico' THEN 1 
                WHEN 'alto' THEN 2 
                WHEN 'medio' THEN 3 
                WHEN 'bajo' THEN 4 
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener alertas críticas (nivel crítico o alto)
        $this->alertasCriticas = $this->alertasActivas->whereIn('nivel', ['critico', 'alto']);

        // Obtener datos climáticos más recientes
        $this->datosClima = DatoClimaticoCache::latest('fecha_consulta')->first();

        // Unidades productivas con alertas
        $this->unidadesConAlertas = $this->alertasActivas
            ->groupBy('unidad_productiva_id')
            ->map(function ($alertas) {
                $unidad = $alertas->first()->unidadProductiva;
                return [
                    'id' => $unidad->id,
                    'nombre' => $unidad->nombre,
                    'localidad' => $unidad->localidad,
                    'cantidad_alertas' => $alertas->count(),
                    'nivel_maximo' => $alertas->sortByDesc(function ($alerta) {
                        return match($alerta->nivel) {
                            'critico' => 4,
                            'alto' => 3,
                            'medio' => 2,
                            'bajo' => 1,
                            default => 0
                        };
                    })->first()->nivel,
                    'alertas' => $alertas
                ];
            })
            ->values();

        // Estadísticas generales
        $this->estadisticas = [
            'total_alertas' => $this->alertasActivas->count(),
            'alertas_criticas' => $this->alertasActivas->where('nivel', 'critico')->count(),
            'alertas_altas' => $this->alertasActivas->where('nivel', 'alto')->count(),
            'unidades_afectadas' => $this->unidadesConAlertas->count(),
            'tipo_mas_comun' => $this->alertasActivas->groupBy('tipo')->sortByDesc->count()->keys()->first(),
        ];
    }

    public function render()
    {
        return view('livewire.productor.ambiental.general')->layout('layouts.productor');
    }
}
