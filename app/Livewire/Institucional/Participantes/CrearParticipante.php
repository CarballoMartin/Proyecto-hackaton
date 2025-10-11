<?php

namespace App\Livewire\Institucional\Participantes;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\InstitucionalParticipante;
use App\Models\User;
use App\Models\Institucion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.institucional')]
class CrearParticipante extends Component
{
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
        'email' => 'required|email|unique:users,email',
        'telefono' => 'nullable|string|max:20',
        'cargo' => 'nullable|string|max:100',
        'fecha_ingreso' => 'required|date|before_or_equal:today',
        'activo' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Debe ser un email válido.',
        'email.unique' => 'Este email ya está registrado.',
        'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
        'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser futura.',
    ];

    public function mount()
    {
        $this->fecha_ingreso = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function guardar()
    {
        $this->validate();
        $this->isLoading = true;

        try {
            $user = Auth::user();
            $institucion = $user->institucionParticipante?->institucion;

            if (!$institucion) {
                session()->flash('error', 'No se encontró la institución asociada.');
                return;
            }

            // Crear usuario
            $nuevoUsuario = User::create([
                'name' => $this->nombre,
                'email' => $this->email,
                'password' => Hash::make('password123'), // Contraseña temporal
                'telefono' => $this->telefono,
                'rol' => 'institucional',
                'email_verified_at' => now(),
            ]);

            // Crear participante institucional
            InstitucionalParticipante::create([
                'usuario_id' => $nuevoUsuario->id,
                'institucion_id' => $institucion->id,
                'activo' => $this->activo,
            ]);

            session()->flash('success', 'Participante creado exitosamente. La contraseña temporal es: password123');

            // Emitir evento para actualizar la lista
            $this->dispatch('participante-creado');
            
            // Resetear formulario
            $this->resetForm();
            
            // Cerrar modal
            $this->dispatch('cerrar-modal-crear');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el participante: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function cancelar()
    {
        $this->resetForm();
        $this->dispatch('cerrar-modal-crear');
    }

    private function resetForm()
    {
        $this->nombre = '';
        $this->email = '';
        $this->telefono = '';
        $this->cargo = '';
        $this->fecha_ingreso = now()->format('Y-m-d');
        $this->activo = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.institucional.participantes.crear-participante');
    }
}
