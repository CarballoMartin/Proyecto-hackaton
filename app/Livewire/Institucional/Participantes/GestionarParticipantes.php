<?php

namespace App\Livewire\Institucional\Participantes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\InstitucionalParticipante;
use App\Models\Institucion;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.institucional')]
class GestionarParticipantes extends Component
{
    use WithPagination;

    // Propiedades para búsqueda y filtros
    public $search = '';
    public $filtroEstado = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    // Propiedades para modales
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showDeleteModal = false;
    
    // Participante seleccionado
    public $participanteSeleccionado = null;

    // Event listeners
    protected $listeners = [
        'participante-creado' => '$refresh',
        'participante-actualizado' => '$refresh',
        'editar-participante' => 'editar',
        'eliminar-participante' => 'eliminar',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Resetear paginación cuando cambien los filtros
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function crear()
    {
        $this->showCreateModal = true;
    }

    public function ver($id)
    {
        $this->participanteSeleccionado = InstitucionalParticipante::find($id);
        $this->showViewModal = true;
    }

    public function editar($id)
    {
        $this->participanteSeleccionado = InstitucionalParticipante::find($id);
        $this->showEditModal = true;
    }

    public function eliminar($id)
    {
        $this->participanteSeleccionado = InstitucionalParticipante::find($id);
        $this->showDeleteModal = true;
    }

    public function confirmarEliminacion()
    {
        if ($this->participanteSeleccionado) {
            $this->participanteSeleccionado->delete();
            
            session()->flash('message', 'Participante eliminado exitosamente.');
            $this->showDeleteModal = false;
            $this->participanteSeleccionado = null;
        }
    }

    public function toggleEstado($id)
    {
        $participante = InstitucionalParticipante::find($id);
        if ($participante) {
            $participante->update(['activo' => !$participante->activo]);
            session()->flash('message', 'Estado del participante actualizado.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $institucion = $user->institucionParticipante?->institucion;

        if (!$institucion) {
            return view('livewire.institucional.participantes.gestionar-participantes', [
                'participantes' => collect(),
                'institucion' => null
            ]);
        }

        $query = InstitucionalParticipante::where('institucion_id', $institucion->id)
            ->with(['user', 'institucion']);

        // Aplicar búsqueda
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->filtroEstado) {
            $query->where('activo', $this->filtroEstado === 'activo');
        }

        // Aplicar ordenamiento
        if ($this->sortBy === 'nombre' || $this->sortBy === 'email') {
            $query->join('users', 'institucional_participantes.usuario_id', '=', 'users.id')
                  ->orderBy('users.' . $this->sortBy, $this->sortDirection)
                  ->select('institucional_participantes.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $participantes = $query->paginate(15);

        return view('livewire.institucional.participantes.gestionar-participantes', [
            'participantes' => $participantes,
            'institucion' => $institucion
        ]);
    }
}
