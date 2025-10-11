<?php

namespace App\Livewire\Productor\UnidadesProductivas;

use Livewire\Component;
use Livewire\Attributes\On; // ← Importa el atributo On
use App\Models\Productor;
use App\Models\UnidadProductiva;

#[On('eliminarUnidadProductiva')] // ← Escucha el evento 'eliminarUnidadProductiva'

class EliminarUnidadProductiva extends Component
{
    // ← Propiedades para el modal
    public $unidad_productiva_id;
    public $unidadProductiva;
    public $productor;
    public $showModal = false; // ← Controla si el modal está abierto
    public $nombre_unidad_productiva; // ← Nombre de la unidad productiva para mostrar en confirmación

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function confirmarEliminacion($id)
    {
        // ← Verifica que la unidad productiva pertenezca al productor
        $this->unidadProductiva = UnidadProductiva::where('id', $id)
                                               ->whereHas('productores', function ($query) {
                                                   $query->where('productor_id', $this->productor->id);
                                               })
                                               ->first();
        
        if ($this->unidadProductiva) {
            // ← Carga datos de la unidad productiva para mostrar en confirmación
            $this->unidad_productiva_id = $this->unidadProductiva->id;
            $this->nombre_unidad_productiva = $this->unidadProductiva->identificador_local ?: 'Unidad Productiva ID: ' . $this->unidadProductiva->id;
            
            // ← Abre el modal de confirmación
            $this->showModal = true;
        } else {
            // ← Si no encuentra la unidad productiva, muestra error
            session()->flash('error', 'Unidad productiva no encontrada o no tienes permisos para eliminarla.');
        }
    }

    public function eliminarUnidadProductiva()
    {
        try {
            // ← Verifica nuevamente que la unidad productiva pertenezca al productor
            $unidadProductiva = UnidadProductiva::where('id', $this->unidad_productiva_id)
                                             ->whereHas('productores', function ($query) {
                                                 $query->where('productor_id', $this->productor->id);
                                             })
                                             ->first();
            
            if ($unidadProductiva) {
                // ← Elimina la unidad productiva
                $unidadProductiva->productores()->detach($this->productor->id);
                $unidadProductiva->delete();
                
                // ← Mensaje de éxito
                session()->flash('message', 'Unidad productiva eliminada exitosamente.');
                
                // ← Cierra el modal
                $this->cerrarModal();
                
                // ← Emite evento para refrescar la lista
                $this->dispatch('unidadProductivaEliminada');
                
            } else {
                session()->flash('error', 'Unidad productiva no encontrada o no tienes permisos para eliminarla.');
            }
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al eliminar la unidad productiva: ' . $e->getMessage());
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
        return view('livewire.productor.unidades-productivas.eliminar-unidad-productiva');
    }
}
