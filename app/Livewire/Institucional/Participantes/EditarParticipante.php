<?php

namespace App\Livewire\Institucional\Participantes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\InstitucionalParticipante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.institucional')]
class EditarParticipante extends Component
{
    // Propiedades del componente
    public $participante;
    public $participanteId;
    
    // Datos del formulario
    public $nombre = '';
    public $email = '';
    public $telefono = '';
    public $cargo = '';
    public $fecha_ingreso = '';
    public $activo = true;
    
    // Estado del componente
    public $isLoading = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'email' => 'required|email',
        'telefono' => 'nullable|string|max:20',
        'cargo' => 'nullable|string|max:100',
        'fecha_ingreso' => 'required|date|before_or_equal:today',
        'activo' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Debe ser un email válido.',
        'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
        'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',
    ];

    public function mount($participante)
    {
        $this->participante = $participante;
        $this->participanteId = $participante->id;
        
        // Cargar datos del participante
        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        $this->participante->load('user');
        
        $this->nombre = $this->participante->user->name;
        $this->email = $this->participante->user->email;
        $this->telefono = $this->participante->user->telefono;
        $this->cargo = $this->participante->cargo;
        $this->fecha_ingreso = $this->participante->fecha_ingreso;
        $this->activo = $this->participante->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedEmail($value)
    {
        // Verificar si el email ya existe en otro usuario
        $existingUser = User::where('email', $value)
            ->where('id', '!=', $this->participante->usuario_id)
            ->first();
            
        if ($existingUser) {
            $this->addError('email', 'Este email ya está registrado por otro usuario.');
        }
    }

    public function actualizar()
    {
        $this->validate();
        $this->isLoading = true;

        try {
            // Actualizar datos del usuario
            $this->participante->user->update([
                'name' => $this->nombre,
                'email' => $this->email,
                'telefono' => $this->telefono,
            ]);

            // Actualizar datos del participante
            $this->participante->update([
                'cargo' => $this->cargo,
                'fecha_ingreso' => $this->fecha_ingreso,
                'activo' => $this->activo,
            ]);

            session()->flash('success', 'Participante actualizado exitosamente.');

            // Emitir evento para actualizar la lista
            $this->dispatch('participante-actualizado');
            
            // Cerrar modal
            $this->dispatch('cerrar-modal-editar');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el participante: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function cancelar()
    {
        // Recargar datos originales
        $this->cargarDatos();
        $this->resetErrorBag();
        $this->dispatch('cerrar-modal-editar');
    }

    public function render()
    {
        return view('livewire.institucional.participantes.editar-participante');
    }
}
