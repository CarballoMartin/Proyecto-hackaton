<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Institucion\CreateInstitucion;
use App\Http\Controllers\Controller;
use App\Models\Institucion; // Importar el modelo
use App\Models\SolicitudVerificacion;
use App\Services\LoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Models\Log as AppLog;

class InstitucionController extends Controller
{
    protected $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Muestra el panel de funciones de instituciones.
     *
     * @return \Illuminate\View\View
     */
    public function panel(Request $request)
    {
        $status = $request->input('status', 'aprobada');
        $search = $request->input('search');

        $itemType = 'institucion';
        $itemsQuery = Institucion::query()->where('eliminada', false);

        if ($status === 'pendiente') {
            $itemType = 'solicitud';
            $itemsQuery = SolicitudVerificacion::query()->where('estado', 'pendiente');

            if ($search) {
                $itemsQuery->where(function ($q) use ($search) {
                    $q->where('nombre_institucion', 'like', "%{$search}%")
                      ->orWhere('cuit', 'like', "%{$search}%")
                      ->orWhere('localidad', 'like', "%{$search}%");
                });
            }
        } else {
            if ($search) {
                $itemsQuery->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('cuit', 'like', "%{$search}%")
                      ->orWhere('localidad', 'like', "%{$search}%");
                });
            }

            if ($status === 'no_aprobada') {
                $itemsQuery->where('validada', false);
            } elseif ($status === 'aprobada') {
                $itemsQuery->where('validada', true);
            }
            // 'todos' no necesita filtro de 'validada'
        }

        $items = $itemsQuery->latest()->paginate(10);
        
        // Siempre obtener las solicitudes pendientes para la tabla de gestión
        $solicitudesPendientes = SolicitudVerificacion::where('estado', 'pendiente')->latest()->get();

        $recentActivities = AppLog::whereIn('modelo', ['Institucion', 'SolicitudVerificacion'])
                                ->latest()
                                ->take(3)
                                ->get();

        return view('admin.instituciones.maqueta-panel', [
            'items' => $items,
            'itemType' => $itemType,
            'solicitudesPendientes' => $solicitudesPendientes,
            'recentActivities' => $recentActivities,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    /**
     * Almacena una nueva institución en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actions\Institucion\CreateInstitucion $creator
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, CreateInstitucion $creator)
    {
        try {
            $institucion = $creator->ejecutar($request->all());

            if ($request->has('solicitud_id') && $request->solicitud_id) {
                $solicitud = SolicitudVerificacion::find($request->solicitud_id);
                if ($solicitud) {
                    $solicitud->delete();
                }
            }

            $this->logger->log('institucion_creada', 'Institucion', $institucion->id, "Se registró la nueva institución '{$institucion->nombre}'.");

            return redirect()->route('admin.instituciones.panel')->with('success', 'Institución registrada exitosamente.');

        } catch (ValidationException $e) {
            // Laravel maneja la redirección y los errores de validación automáticamente.
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error al procesar la creación de la institución: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Ha ocurrido un error inesperado al crear la institución. Por favor, revise los logs para más detalles.');
        }
    }

    /**
     * Valida una institución.
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validar(Institucion $institucion)
    {
        $institucion->update(['validada' => true]);
        $this->logger->log('institucion_validada', 'Institucion', $institucion->id, "La institución '{$institucion->nombre}' fue validada.");
        return redirect()->route('admin.instituciones.panel')->with('success', 'Institución validada exitosamente.');
    }

    /**
     * Desactiva una institución (la mueve a 'No Aprobada').
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Institucion $institucion)
    {
        $institucion->update(['validada' => false]);
        $this->logger->log('institucion_desactivada', 'Institucion', $institucion->id, "La institución '{$institucion->nombre}' fue desactivada.");
        return redirect()->route('admin.instituciones.panel')->with('success', 'Institución desactivada exitosamente.');
    }

    /**
     * Elimina lógicamente una institución.
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Institucion $institucion)
    {
        $institucion->update(['eliminada' => true]);
        $this->logger->log('institucion_eliminada', 'Institucion', $institucion->id, "La institución '{$institucion->nombre}' fue eliminada.");
        return redirect()->route('admin.instituciones.panel')->with('success', 'Institución eliminada exitosamente.');
    }
}

