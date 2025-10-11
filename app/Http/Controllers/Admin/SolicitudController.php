<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\SolicitudVerificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    /**
     * Muestra la página para gestionar las solicitudes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.solicitudes.index');
    }

    /**
     * Rechaza una solicitud de verificación.
     *
     * @param  \App\Models\SolicitudVerificacion  $solicitud
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(SolicitudVerificacion $solicitud)
    {
        // Iniciar una transacción para asegurar la atomicidad
        DB::transaction(function () use ($solicitud) {
            // 1. Actualizar el estado de la solicitud
            $solicitud->update(['estado' => 'rechazada']);

            // 2. Crear una nueva institución con estado no validado
            Institucion::create([
                'nombre' => $solicitud->nombre_institucion,
                'contacto_email' => $solicitud->email_contacto,
                'cuit' => $solicitud->cuit_institucion,
                // Asumiendo que estos campos pueden ser nulos o tener un valor por defecto
                'email_secundario' => null,
                'telefono' => null,
                'localidad' => $solicitud->localidad_institucion,
                'provincia' => 'Neuquén', // O obtenerlo de algún otro lado si es variable
                'validada' => false,
                'eliminada' => false, // Asegurarse de que no esté marcada como eliminada
            ]);

            // 3. Eliminar la solicitud original
            $solicitud->delete();
        });

        return redirect()->route('admin.instituciones.panel')->with('success', 'La solicitud ha sido rechazada y la institución ha sido registrada como No Aprobada.');
    }
}