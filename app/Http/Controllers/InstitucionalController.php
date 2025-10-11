<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Institucion;

class InstitucionalController extends Controller
{
    /**
     * Página de información institucional estática
     */
    public function informacion()
    {
        $user = Auth::user();
        $institucion = $user->institucionParticipante?->institucion;
        
        return view('institucional.informacion', compact('institucion'));
    }

    /**
     * Página de contacto institucional
     */
    public function contacto()
    {
        $user = Auth::user();
        $institucion = $user->institucionParticipante?->institucion;
        
        return view('institucional.contacto', compact('institucion'));
    }

    /**
     * Página de políticas institucionales
     */
    public function politicas()
    {
        return view('institucional.politicas');
    }

    /**
     * Página de términos y condiciones
     */
    public function terminos()
    {
        return view('institucional.terminos');
    }

    /**
     * Página de ayuda/instructivos
     */
    public function ayuda()
    {
        return view('institucional.ayuda');
    }
}

