<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudVerificacion;

class SolicitudController extends Controller
{
    /**
     * Almacena una nueva solicitud de verificación de institución.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_institucion' => 'required|string|max:255',
            'email_contacto' => [
                'required',
                'email',
                'max:255',
                'unique:solicitud_verificacions,email_contacto', // Único en las solicitudes pendientes
                'unique:institucions,contacto_email', // No debe existir en instituciones ya registradas
            ],
            'cuit' => 'nullable|string|max:20|unique:solicitud_verificacions,cuit|unique:institucions,cuit',
            'nombre_solicitante' => 'required|string|max:255',
            'telefono_contacto' => 'required|string|max:255',
            'mensaje' => 'nullable|string|max:1000',
            'localidad' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
        ], [
            'email_contacto.unique' => 'Este correo electrónico ya ha sido registrado o tiene una solicitud pendiente.',
            'cuit.unique' => 'Este CUIT ya ha sido registrado o tiene una solicitud pendiente.',
        ]);

        SolicitudVerificacion::create([
            'nombre_institucion' => $validatedData['nombre_institucion'],
            'email_contacto' => $validatedData['email_contacto'],
            'cuit' => $validatedData['cuit'] ?? null,
            'nombre_solicitante' => $validatedData['nombre_solicitante'],
            'telefono_contacto' => $validatedData['telefono_contacto'],
            'mensaje' => $validatedData['mensaje'],
            'localidad' => $validatedData['localidad'],
            'provincia' => $validatedData['provincia'],
            'estado' => 'pendiente',
        ]);

        return redirect()->route('solicitud-exitosa');
    }
}
