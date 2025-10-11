<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaginasEstaticasController extends Controller
{
    /**
     * Muestra la página de información para el registro de instituciones.
     */
    public function registroInstitucional()
    {
        return view('guest.registro-institucional');
    }
}
