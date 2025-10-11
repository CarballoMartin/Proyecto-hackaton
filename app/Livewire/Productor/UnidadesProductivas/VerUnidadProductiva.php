<?php

namespace App\Livewire\Productor\UnidadesProductivas;

use Livewire\Component;
use Livewire\Attributes\On; // ← Importa el atributo On
use App\Models\Productor;
use App\Models\UnidadProductiva;

#[On('verUnidadProductiva')] // ← Escucha el evento 'verUnidadProductiva'

class VerUnidadProductiva extends Component
{
    // ← Propiedades para el modal
    public $unidad_productiva_id;
    public $unidadProductiva;
    public $productor;
    public $showModal = false; // ← Controla si el modal está abierto

    // ← Propiedades para mostrar datos
    public $campo, $identificador_local, $tipo_identificador, $superficie, $habita;
    public $agua_humano_fuente, $agua_humano_en_casa, $agua_humano_distancia;
    public $agua_animal_fuente, $agua_animal_distancia;
    public $tipo_pasto, $tipo_suelo, $forrajeras_predominante;
    public $latitud, $longitud, $observaciones, $activo, $created_at, $updated_at;

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function verUnidadProductiva($id)
    {
        // ← Verifica que la unidad productiva pertenezca al productor
        $this->unidadProductiva = UnidadProductiva::where('id', $id)
                                               ->whereHas('productores', function ($query) {
                                                   $query->where('productor_id', $this->productor->id);
                                               })
                                               ->with(['campo', 'tipoIdentificador', 'fuenteAguaHumano', 'fuenteAguaAnimal', 'tipoPasto', 'tipoSuelo'])
                                               ->first();
        
        if ($this->unidadProductiva) {
            // ← Carga todos los datos de la unidad productiva
            $this->unidad_productiva_id = $this->unidadProductiva->id;
            $this->campo = $this->unidadProductiva->campo ? $this->unidadProductiva->campo->localidad : 'No especificado';
            $this->identificador_local = $this->unidadProductiva->identificador_local;
            $this->tipo_identificador = $this->unidadProductiva->tipoIdentificador ? $this->unidadProductiva->tipoIdentificador->nombre : 'No especificado';
            $this->superficie = $this->unidadProductiva->superficie;
            $this->habita = $this->unidadProductiva->habita;
            $this->latitud = $this->unidadProductiva->latitud;
            $this->longitud = $this->unidadProductiva->longitud;
            
            // ← Datos de agua para humanos
            $this->agua_humano_fuente = $this->unidadProductiva->fuenteAguaHumano ? $this->unidadProductiva->fuenteAguaHumano->nombre : 'No especificado';
            $this->agua_humano_en_casa = $this->unidadProductiva->agua_humano_en_casa;
            $this->agua_humano_distancia = $this->unidadProductiva->agua_humano_distancia;
            
            // ← Datos de agua para animales
            $this->agua_animal_fuente = $this->unidadProductiva->fuenteAguaAnimal ? $this->unidadProductiva->fuenteAguaAnimal->nombre : 'No especificado';
            $this->agua_animal_distancia = $this->unidadProductiva->agua_animal_distancia;
            
            // ← Datos de pasto y suelo
            $this->tipo_pasto = $this->unidadProductiva->tipoPasto ? $this->unidadProductiva->tipoPasto->nombre : 'No especificado';
            $this->tipo_suelo = $this->unidadProductiva->tipoSuelo ? $this->unidadProductiva->tipoSuelo->nombre : 'No especificado';
            $this->forrajeras_predominante = $this->unidadProductiva->forrajeras_predominante;
            
            // ← Otros datos
            $this->observaciones = $this->unidadProductiva->observaciones;
            $this->activo = $this->unidadProductiva->activo;
            $this->created_at = $this->unidadProductiva->created_at;
            $this->updated_at = $this->unidadProductiva->updated_at;
            
            // ← Abre el modal
            $this->showModal = true;
        } else {
            // ← Si no encuentra la unidad productiva, muestra error
            session()->flash('error', 'Unidad productiva no encontrada o no tienes permisos para verla.');
        }
    }

    public function cerrarModal()
    {
        // ← Cierra el modal
        $this->showModal = false;
        $this->resetExcept(['productor']); // ← Limpia todas las propiedades excepto productor
    }

    public function render()
    {
        return view('livewire.productor.unidades-productivas.ver-unidad-productiva');
    }
}
