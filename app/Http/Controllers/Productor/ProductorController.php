<?php

namespace App\Http\Controllers\Productor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesWeatherData;
use App\Models\Municipio;
use Illuminate\Http\Request;
use App\Models\Productor;
use App\Models\StockAnimal;
use App\Models\Especie;
use Illuminate\Support\Facades\Auth;
use App\Actions\Productor\UpdateProductorProfile; // Added
use Illuminate\Validation\ValidationException; // Added
use Illuminate\Support\Facades\Log; // Added for error logging
use App\Services\EstadisticasService;
use App\Interfaces\ChartBuilderInterface;
use Throwable;

class ProductorController extends Controller
{
    use ManagesWeatherData;

    public function dashboard()
    {
        // Obtener el productor asociado al usuario autenticado
        $user = Auth::user();
        $productor = Productor::with('unidadesProductivas')->where('usuario_id', $user->id)->first();

        // Si el productor no existe o no tiene unidades productivas, redirigir al asistente.
        if ($productor && $productor->unidadesProductivas->isEmpty()) {
            return redirect()->route('productor.unidades-productivas.create');
        }
        
        // Estadísticas básicas
        $totalCampos = 0;
        $stockPorEspecie = collect();
        $ultimaActualizacion = null;
        
        if ($productor) {
            $totalCampos = $productor->unidadesProductivas->count();
            
            // Obtener stock animal por especie dinámicamente
            $unidadesProductivasIds = $productor->unidadesProductivas->pluck('id');
            $stockAnimal = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductivasIds)
                ->with('especie')
                ->get();
            
            // Agrupar por especie dinámicamente
            $stockPorEspecie = $stockAnimal->groupBy('especie.nombre')->map(function ($animales) {
                return $animales->sum('cantidad');
            });
            
            $ultimaActualizacion = $productor->updated_at;
            
            // Obtener datos históricos para gráficos (últimos 6 meses)
            $fechaInicio = now()->subMonths(6);
            $datosHistoricos = $this->obtenerDatosHistoricos($unidadesProductivasIds, $fechaInicio);
            
            // Obtener preview del stock actual (máximo 4 registros)
            $stockPreview = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductivasIds)
                ->with(['especie', 'categoria'])
                ->latest('fecha_registro')
                ->limit(4)
                ->get();

            // Cargar las unidades productivas con sus municipios para evitar N+1 queries
            $productor->load('unidadesProductivas.municipio');

            // Obtener los municipios únicos de las unidades productivas
            $municipios = $productor->unidadesProductivas->map(function ($up) {
                return $up->municipio;
            })->filter()->unique('id');

            // Obtener los datos del clima para cada municipio
            $weatherData = $municipios->map(function ($municipio) {
                return $this->getWeatherDataForMunicipio($municipio);
            })->filter()->values()->toArray();
        }

        return view('productor.dashboard', [
            'productor' => $productor,
            'totalCampos' => $totalCampos,
            'stockPorEspecie' => $stockPorEspecie ?? collect(),
            'ultimaActualizacion' => $ultimaActualizacion,
            'weatherData' => $weatherData,
            'datosHistoricos' => $datosHistoricos ?? [],
            'stockPreview' => $stockPreview ?? collect(),
        ]);
    }

    public function unidadesProductivasIndex()
    {
        $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();
        $unidadesProductivas = $productor->unidadesProductivas()->with('municipio')->paginate(10);

        return view('productor.unidades-productivas.index', [
            'unidadesProductivas' => $unidadesProductivas,
        ]);
    }

    public function stockIndex(Request $request)
    {
        $productor = Productor::with('unidadesProductivas')->where('usuario_id', Auth::id())->firstOrFail();
        $unidadesProductorasIds = $productor->unidadesProductivas->pluck('id');

        $query = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductorasIds)
                            ->with(['unidadProductiva', 'especie', 'categoria', 'raza']);

        if ($request->has('unidad_productiva_id') && $request->unidad_productiva_id != '') {
            $query->where('unidad_productiva_id', $request->unidad_productiva_id);
        }

        $stock = $query->paginate(15)->withQueryString();

        return view('productor.stock.index', [
            'stock' => $stock,
            'unidadesProductivas' => $productor->unidadesProductivas,
        ]);
    }

    public function showProfile()
    {
        $user = Auth::user();
        $productor = Productor::where('usuario_id', $user->id)->firstOrFail();

        return view('productor.profile.show', compact('productor'));
    }

    public function updateProfile(Request $request, UpdateProductorProfile $updateProductorProfile)
    {
        $user = Auth::user();
        $productor = Productor::where('usuario_id', $user->id)->firstOrFail();

        try {
            $updateProductorProfile->handle($productor, $request->all());

            // Update user email if it was changed and is different from current
            if ($request->has('email') && $request->input('email') !== $user->email) {
                $user->forceFill(['email' => $request->input('email')])->save();
            }

            session()->flash('message', 'Perfil actualizado exitosamente.');
            return redirect()->route('productor.profile.show');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (throwable $e) {
            Log::error('Error al actualizar el perfil del productor: ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'Ocurrió un error al actualizar el perfil. Por favor, intente de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function estadisticas(Request $request, EstadisticasService $estadisticasService, ChartBuilderInterface $chartBuilder)
    {
        $productor = Productor::with('unidadesProductivas')->where('usuario_id', Auth::id())->firstOrFail();
        $filtros = $request->only(['fecha_desde', 'fecha_hasta', 'unidad_productiva_id', 'especie_id']);

        // --- Obtener Datos de los Servicios ---
        $resumenPorUnidad = $estadisticasService->getResumenPorUnidadProductiva($productor, $filtros);
        $composicionEspecie = $estadisticasService->getComposicionPorEspecie($productor, $filtros);
        $composicionCategoria = $estadisticasService->getComposicionPorCategoria($productor, $filtros);
        $evolucionStock = $estadisticasService->getEvolucionStockMensual($productor, $filtros);

        // --- Calcular KPIs ---
        $totalAnimales = $resumenPorUnidad->sum('total_animales');
        $totalSuperficie = $resumenPorUnidad->sum('superficie');
        $cargaAnimal = $totalSuperficie > 0 ? number_format($totalAnimales / $totalSuperficie, 2) : 0;

        // --- Construir Gráficos (CORREGIDO) ---
        $pieChartComposicion = $chartBuilder->buildPieChart(
            'Composición por Especie',
            array_keys($composicionEspecie),
            array_values($composicionEspecie)
        );
        $barChartComposicion = $chartBuilder->buildBarChart(
            'Composición por Categoría',
            array_keys($composicionCategoria),
            array_values($composicionCategoria)
        );
        $lineChartEvolucion = $chartBuilder->buildLineChart(
            $evolucionStock['labels'],
            $evolucionStock['datasets']
        );

        return view('productor.estadisticas.index', [
            // Datos para filtros
            'filtros' => $filtros,
            'unidadesProductivas' => $productor->unidadesProductivas,
            'especies' => Especie::all(),
            
            // KPIs
            'totalAnimales' => $totalAnimales,
            'composicionEspecie' => $composicionEspecie,
            'cargaAnimal' => $cargaAnimal,
            
            // Datos para la tabla
            'resumenPorUnidadProductiva' => $resumenPorUnidad,

            // Datos JSON para los gráficos
            'pieChartComposicion' => json_encode($pieChartComposicion),
            'barChartComposicion' => json_encode($barChartComposicion),
            'lineChartEvolucion' => json_encode($lineChartEvolucion),
        ]);
    }

    public function reportes()
    {
        // Mockup: solo devuelve la vista.
        return view('productor.reportes.index');
    }

    /**
     * Obtiene datos históricos para los gráficos del dashboard
     */
    private function obtenerDatosHistoricos($unidadesProductivasIds, $fechaInicio)
    {
        // Obtener todas las especies dinámicamente
        $especies = \App\Models\Especie::all();
        
        // Obtener datos mensuales de los últimos 6 meses
        $meses = [];
        $datosPorEspecie = [];
        
        // Inicializar arrays para cada especie
        foreach ($especies as $especie) {
            $datosPorEspecie[$especie->nombre] = [];
        }
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = $fecha->format('M');
            
            // Obtener stock por especie para este mes
            foreach ($especies as $especie) {
                $cantidad = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductivasIds)
                    ->where('especie_id', $especie->id)
                    ->where('fecha_registro', '<=', $fecha->endOfMonth())
                    ->sum('cantidad');
                
                $datosPorEspecie[$especie->nombre][] = $cantidad;
            }
        }
        
        return [
            'meses' => $meses,
            'datosPorEspecie' => $datosPorEspecie
        ];
    }
}
