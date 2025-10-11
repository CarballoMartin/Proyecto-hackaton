<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutButton extends Component
{
    public function logout()
    {
        // Limpiar todas las sesiones
        Session::flush();
        
        // Cerrar sesión
        Auth::logout();
        
        // Invalidar la sesión actual
        request()->session()->invalidate();
        
        // Regenerar el token CSRF
        request()->session()->regenerateToken();
        
        // Redirigir al login
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.institucional.logout-button');
    }
}
