<?php

namespace App\Livewire\Productor;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class Perfil extends Component
{
    public $productor;

    // campos editables
    public $nombre, $dni, $cuil, $fecha_nacimiento, $municipio, $paraje, $direccion, $telefono, $email;

    // original values para lógica de "no borrar"
    public $originalDni, $originalCuil, $originalTelefono;

    // control UX para edición de teléfono
    public $editingTelefono = false;

    public function mount()
    {
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();

        if ($this->productor) {
            $this->nombre = $this->productor->nombre;
            $this->dni = $this->productor->dni;
            $this->cuil = $this->productor->cuil;
            $this->fecha_nacimiento = $this->productor->fecha_nacimiento;
            $this->municipio = $this->productor->municipio;
            $this->paraje = $this->productor->paraje;
            $this->direccion = $this->productor->direccion;
            $this->telefono = $this->productor->telefono;
            $this->email = $user->email;

            // guardo originales para lógica
            $this->originalDni = $this->productor->dni;
            $this->originalCuil = $this->productor->cuil;
            $this->originalTelefono = $this->productor->telefono;
        }
    }

    // helpers
    protected function onlyDigits($value)
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    protected function rulesBase()
    {
        return [
            'nombre' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'municipio' => 'nullable|string|max:255',
            'paraje' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'email' => 'nullable|email',
            // dni/cuil/telefono validación más fina en actualizarPerfil()
        ];
    }

    public function actualizarPerfil()
    {
        // validación básica de campos no problemáticos
        $this->validate($this->rulesBase());

        // Normalizo
        $dniDigits = $this->dni ? $this->onlyDigits($this->dni) : null;
        $cuilDigits = $this->cuil ? $this->onlyDigits($this->cuil) : null;
        $telefonoDigits = $this->telefono ? $this->onlyDigits($this->telefono) : null;

        // 1) Reglas de presencia: al menos dni o cuil
        if (empty($dniDigits) && empty($cuilDigits)) {
            $this->addError('dni', 'Debe completar DNI o CUIL (al menos uno).');
            $this->addError('cuil', 'Debe completar DNI o CUIL (al menos uno).');
            return;
        }

        // 2) Validaciones de formato (longitudes)
        if ($dniDigits && (strlen($dniDigits) < 7 || strlen($dniDigits) > 8)) {
            $this->addError('dni', 'El DNI debe tener entre 7 y 8 dígitos.');
            return;
        }

        if ($cuilDigits && strlen($cuilDigits) !== 11) {
            $this->addError('cuil', 'El CUIL debe contener 11 dígitos (ej: 20-12345678-3).');
            return;
        }

        // 3) Validaciones de unicidad (ignorando el propio registro)
        if ($dniDigits) {
            $exists = Productor::where('dni', $dniDigits)
                ->where('id', '<>', $this->productor->id)
                ->exists();
            if ($exists) {
                $this->addError('dni', 'El DNI ya está en uso por otro productor.');
                return;
            }
        }

        if ($cuilDigits) {
            $exists = Productor::where('cuil', $cuilDigits)
                ->where('id', '<>', $this->productor->id)
                ->exists();
            if ($exists) {
                $this->addError('cuil', 'El CUIL ya está en uso por otro productor.');
                return;
            }
        }

        // 4) Lógica teléfono:
        // - Si el productor tenía teléfono y ahora intenta borrarlo -> no permitir
        // - Si no tiene y no carga uno -> sugerir (requerir)
        if ($this->originalTelefono && empty($telefonoDigits)) {
            $this->addError('telefono', 'No puede eliminar el teléfono. Si desea cambiarlo, use "Cambiar teléfono" y guarde un nuevo número.');
            return;
        }

        if (empty($this->originalTelefono) && empty($telefonoDigits)) {
            $this->addError('telefono', 'Se sugiere completar un teléfono de contacto (obligatorio si no existe uno previo).');
            return;
        }

        // validar longitud teléfono (acepta prefijo +54 u 0, pero medimos dígitos)
        if ($telefonoDigits && (strlen($telefonoDigits) < 8 || strlen($telefonoDigits) > 13)) {
            $this->addError('telefono', 'El teléfono parece corto o demasiado largo. Ingrese entre 8 y 13 dígitos (puede incluir prefijo +54).');
            return;
        }

        // 5) Si todo OK => guardo (normalizando a strings con formato "solo dígitos")
        $data = [
            'nombre' => $this->nombre,
            'dni' => $dniDigits ?: null,
            'cuil' => $cuilDigits ?: null,
            'fecha_nacimiento' => $this->fecha_nacimiento ?: null,
            'municipio' => $this->municipio ?: null,
            'paraje' => $this->paraje ?: null,
            'direccion' => $this->direccion ?: null,
            'telefono' => $telefonoDigits ?: null,
        ];

        $this->productor->update($data);

        // opcional: actualizar email en users si lo quieres (aquí lo ignoramos por seguridad)

        // refrescar originales y UI flags
        $this->originalDni = $this->productor->dni;
        $this->originalCuil = $this->productor->cuil;
        $this->originalTelefono = $this->productor->telefono;
        $this->editingTelefono = false;

        $this->dispatch('banner-message', style: 'success', message: 'Perfil actualizado exitosamente.');
    }

    // método para activar edición del teléfono (UX)
    public function enableEditTelefono()
    {
        $this->editingTelefono = true;
    }

    public function render()
    {
        return view('livewire.productor.perfil');
    }
}
