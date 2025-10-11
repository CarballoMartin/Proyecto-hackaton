<?php

namespace App\Http\Controllers\Productor;

use App\Actions\Cuaderno\FiltrarMovimientosAction;
use App\Actions\Cuaderno\GuardarMovimientosAction;
use App\Http\Controllers\Controller;
use App\Actions\Cuaderno\FiltrarMovimientosHistorialAction;
use App\Actions\Cuaderno\CalcularResumenHistorialAction;
use App\Interfaces\PdfExportServiceInterface;
use App\Models\CategoriaAnimal;
use App\Models\ConfiguracionActualizacion;
use App\Models\DeclaracionStock;
use App\Models\Especie;
use App\Models\MotivoMovimiento;
use App\Models\Productor;
use App\Models\Raza;
use App\Models\StockAnimal;
use App\Models\StockActual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CuadernoDeCampoController extends Controller
{
    public function __construct()
    {
        $proximoPeriodo = ConfiguracionActualizacion::where('proxima_actualizacion', '>=', now())
            ->orderBy('proxima_actualizacion', 'asc')
            ->first();

        $tiempoRestante = 'No hay periodo activo';
        if ($proximoPeriodo) {
            $fechaFin = \Carbon\Carbon::parse($proximoPeriodo->proxima_actualizacion);
            $ahora = \Carbon\Carbon::now();
            $diferencia = $ahora->diff($fechaFin);
            $tiempoRestante = $diferencia->format('%ad %hh %im');
        }

        View::share('tiempoRestante', $tiempoRestante);
    }

    public function index()
    {
        return redirect()->route('cuaderno.inicio');
    }

    public function inicio()
    {
        return view('productor.cuaderno.inicio');
    }

    public function registro(Request $request, FiltrarMovimientosAction $filtrarMovimientos)
    {
        $productor = Productor::where('usuario_id', Auth::id())
            ->with('unidadesProductivas')
            ->firstOrFail();

        $unidadesProductivas = $productor->unidadesProductivas()->with('municipio')->get();

        $stockActualPorUP = collect();
        $selectedUpId = $request->input('selected_up_id');

        if ($selectedUpId) {
            $stockActualData = StockActual::where('unidad_productiva_id', $selectedUpId)
                ->with(['especie', 'categoria', 'raza'])
                ->get();
            $stockActualPorUP = $stockActualData->groupBy('unidad_productiva_id');
        }

        // --- OBTENER MOVIMIENTOS GUARDADOS DE LA DECLARACIÓN VIGENTE ---
        $movimientosGuardados = collect(); // Default to empty collection
        $periodoActivo = ConfiguracionActualizacion::where('activo', true)
            ->where('proxima_actualizacion', '>=', now())
            ->orderBy('proxima_actualizacion', 'asc')
            ->first();

        if ($periodoActivo) {
            $declaracionesActivas = DeclaracionStock::where('productor_id', $productor->id)
                ->where('periodo_id', $periodoActivo->id)
                ->pluck('id');

            if ($declaracionesActivas->isNotEmpty()) {
                $filtros = ['declaracion_ids' => $declaracionesActivas->toArray()];
                
                // Añadir filtro de día si está presente en la request
                if ($request->has('day_filter') && $request->day_filter !== 'todos') {
                    $filtros['day_of_week'] = $request->day_filter;
                }

                $movimientosGuardados = $filtrarMovimientos($filtros);
            }
        }

        // --- Datos para los formularios ---
        $especies = Especie::all();
        $categorias = CategoriaAnimal::all();
        $razas = Raza::all();
        $motivos = MotivoMovimiento::all()->groupBy('tipo');

        return view('productor.cuaderno.registro', [
            'unidadesProductivasData' => $unidadesProductivas,
            'stockActualPorUPData' => $stockActualPorUP,
            'movimientosGuardadosData' => $movimientosGuardados, // Pass new data
            'especiesData' => $especies,
            'categoriasData' => $categorias,
            'razasData' => $razas,
            'motivosData' => $motivos,
        ]);
    }

    public function store(Request $request, GuardarMovimientosAction $guardarMovimientos)
    {
        try {
            $validated = $request->validate([
                'movimientos_json' => 'required|json',
                'upId' => 'required|integer|exists:unidades_productivas,id',
            ]);

            $movimientos = json_decode($validated['movimientos_json'], true);

            // Validar que el array de movimientos no esté vacío después de decodificar
            if (empty($movimientos)) {
                return back()->with('error', 'No hay movimientos para guardar.');
            }

            $guardarMovimientos($movimientos, $validated['upId']);

            return redirect()->route('cuaderno.registro', ['selected_up_id' => $validated['upId']])
                         ->with('success', 'Cambios guardados en el cuaderno exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error al guardar cambios en cuaderno: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar los cambios.');
        }
    }

    public function filtrarMovimientosGuardados(Request $request, FiltrarMovimientosAction $filtrarMovimientos)
    {
        $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();
        $allUPIds = $productor->unidadesProductivas->pluck('id');

        $periodoActivo = ConfiguracionActualizacion::where('activo', true)
            ->where('proxima_actualizacion', '>=', now())
            ->orderBy('proxima_actualizacion', 'asc')
            ->first();

        $movimientos = collect();
        if ($periodoActivo) {
            $declaracionesActivas = DeclaracionStock::where('productor_id', $productor->id)
                ->where('periodo_id', $periodoActivo->id)
                ->pluck('id');

            if ($declaracionesActivas->isNotEmpty()) {
                $filtros = ['declaracion_ids' => $declaracionesActivas->toArray()];
                
                if ($request->has('day_filter') && $request->day_filter !== 'todos') {
                    $filtros['day_of_week'] = $request->day_filter;
                }

                $movimientos = $filtrarMovimientos($filtros);
            }
        }

        return view('productor.cuaderno.partials.movimientos-guardados-filas', ['movimientos' => $movimientos]);
    }

    public function historial(Request $request, FiltrarMovimientosHistorialAction $filtrarMovimientos, CalcularResumenHistorialAction $calcularResumen)
    {
        $productor = Productor::with('unidadesProductivas')->where('usuario_id', Auth::id())->firstOrFail();
        $unidadesProductivas = $productor->unidadesProductivas;

        // Obtener todos los períodos de declaración para el selector de "accesos directos"
        $periodos = DeclaracionStock::with('periodo')
            ->where('productor_id', $productor->id)
            ->get()
            ->pluck('periodo')
            ->unique('id')
            ->sortByDesc('proxima_actualizacion');

        // Obtener todos los motivos para el nuevo desplegable de filtro
        $motivos = MotivoMovimiento::orderBy('tipo')->orderBy('nombre')->get();

        $filters = [
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta'),
            'up_id' => $request->input('up_id'),
            'flujo' => $request->input('flujo'),
            'motivo_id' => $request->input('motivo_id'),
        ];

        // Comprueba si se ha aplicado algún filtro
        $hasFilters = !empty(array_filter($filters));

        if ($hasFilters) {
            $movimientos = $filtrarMovimientos($productor, $filters);
            $resumen = $calcularResumen($productor, $filters);
        } else {
            // Si no hay filtros, devuelve datos vacíos
            $movimientos = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $resumen = [
                'altas' => ['total' => 0],
                'bajas' => ['total' => 0],
                'specific_total' => null,
                'motivo_filtrado' => null,
            ];
        }

        return view('productor.cuaderno.historial', [
            'periodos' => $periodos,
            'unidadesProductivas' => $unidadesProductivas,
            'motivos' => $motivos,
            'movimientos' => $movimientos,
            'resumen' => $resumen,
            'filters' => $filters,
            'hasFilters' => $hasFilters, // Variable para que la vista sepa si se filtró algo
        ]);
    }

    public function exportarHistorialPdf(Request $request, FiltrarMovimientosHistorialAction $filtrarMovimientos, CalcularResumenHistorialAction $calcularResumen, PdfExportServiceInterface $pdfService)
    {
        $productor = Productor::with('unidadesProductivas')->where('usuario_id', Auth::id())->firstOrFail();
        $filters = [
            'fecha_desde' => $request->input('fecha_desde'),
            'fecha_hasta' => $request->input('fecha_hasta'),
            'up_id' => $request->input('up_id'),
            'flujo' => $request->input('flujo'),
            'motivo_id' => $request->input('motivo_id'),
        ];

        // Obtenemos los movimientos SIN paginar
        $movimientos = $filtrarMovimientos($productor, $filters, false);
        $resumen = $calcularResumen($productor, $filters);
        
        $data = [
            'movimientos' => $movimientos,
            'resumen' => $resumen,
            'filters' => $filters,
            'productor' => $productor,
        ];

        $view = 'productor.cuaderno.historial-pdf'; // Usaremos una vista específica para el PDF
        $filename = 'historial-movimientos-' . now()->format('Y-m-d') . '.pdf';

        return $pdfService->generateFromView($view, $data, $filename);
    }
}
