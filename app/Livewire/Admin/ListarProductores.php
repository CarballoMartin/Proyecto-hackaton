<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Productor;
use App\Models\User;
class ListarProductores extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = [
        'productorSaved' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    

    public function toggleProductorStatus($productorId)
    {
        $productor = Productor::find($productorId);

        if ($productor && $productor->usuario) {
            $usuario = $productor->usuario;
            $usuario->activo = !$usuario->activo;
            $productor->activo = $usuario->activo;
            $productor->save();
            $usuario->save();

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Estado cambiado correctamente'
            );
        }

        // Refresca la lista
        $this->resetPage();
    }

    public function render()
    {
        $productores = Productor::with('usuario')
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('dni', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.admin.listar-productores', [
            'productores' => $productores
        ]);
    }
}