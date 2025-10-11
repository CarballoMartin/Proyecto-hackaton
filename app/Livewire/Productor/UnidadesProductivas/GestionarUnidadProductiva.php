<?php

namespace App\Livewire\Productor\UnidadesProductivas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\UnidadProductiva;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;
use Illuminate\Support\Facades\Log;
use App\Models\Productor;

#[Layout('layouts.productor')]
class GestionarUnidadProductiva extends Component
{
    public UnidadProductiva $up;
    public $locations = [];

    // Optional Fields (from Step 3)
    public $agua_humano_fuente_id;
    public $agua_humano_en_casa = false;
    public $agua_humano_distancia;
    public $agua_animal_fuente_id;
    public $agua_animal_distancia;
    public $tipo_pasto_predominante_id;
    public $tipo_suelo_predominante_id;
    public $forrajeras_predominante = false;
    public $observaciones;

    // Data for Selects
    public $fuentes_agua = [];
    public $tipos_pasto = [];
    public $tipos_suelo = [];

    public function mount($id)
    {
        $this->up = UnidadProductiva::with(['municipio', 'paraje', 'condicionTenencia', 'productores'])->findOrFail($id);

        // Get all locations for the producer
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

        // Initialize editable properties
        $this->agua_humano_fuente_id = $this->up->agua_humano_fuente_id;
        $this->agua_humano_en_casa = (bool)$this->up->agua_humano_en_casa;
        $this->agua_humano_distancia = $this->up->agua_humano_distancia;
        $this->agua_animal_fuente_id = $this->up->agua_animal_fuente_id;
        $this->agua_animal_distancia = $this->up->agua_animal_distancia;
        $this->tipo_pasto_predominante_id = $this->up->tipo_pasto_predominante_id;
        $this->tipo_suelo_predominante_id = $this->up->tipo_suelo_predominante_id;
        $this->forrajeras_predominante = (bool)$this->up->forrajeras_predominante;
        $this->observaciones = $this->up->observaciones;

        // Load data for dropdowns
        $this->fuentes_agua = FuenteAgua::all();
        $this->tipos_pasto = TipoPasto::all();
        $this->tipos_suelo = TipoSuelo::all();
    }

    public function update()
    {
        $validatedData = $this->validate([
            'agua_humano_fuente_id' => 'nullable|exists:fuente_aguas,id',
            'agua_humano_en_casa' => 'boolean',
            'agua_humano_distancia' => 'nullable|integer|min:0',
            'agua_animal_fuente_id' => 'nullable|exists:fuente_aguas,id',
            'agua_animal_distancia' => 'nullable|integer|min:0',
            'tipo_pasto_predominante_id' => 'nullable|exists:tipo_pastos,id',
            'tipo_suelo_predominante_id' => 'nullable|exists:tipo_suelos,id',
            'forrajeras_predominante' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $this->up->update($validatedData);
            $this->up->update(['completo' => true]); // Mark as complete

            session()->flash('message', 'Datos de la chacra actualizados exitosamente.');
            $this->dispatch('chacra-updated');

        } catch (\Exception $e) {
            Log::error('Error al actualizar la chacra: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al actualizar los datos.');
        }
    }

    public function render()
    {
        return view('livewire.productor.unidades-productivas.gestionar-unidad-productiva');
    }
}
