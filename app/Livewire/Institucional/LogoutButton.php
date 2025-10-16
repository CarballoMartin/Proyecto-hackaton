<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutButton extends Component
{
    public function logout()
    {
        try {
            // Cerrar sesión
            Auth::logout();
            
            // Limpiar sesión
            session()->flush();
            
            // Redirigir al login
            return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
            
        } catch (\Exception $e) {
            // Si hay error, redirigir de todas formas
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.institucional.logout-button');
    }
}
