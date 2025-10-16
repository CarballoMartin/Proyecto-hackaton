<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ManagesWeatherData;
use App\Models\Municipio;
use Illuminate\Http\Request;
use App\Models\Productor;
use App\Models\Institucion;
use App\Models\SolicitudVerificacion;
use App\Models\Campo;
use App\Models\UnidadProductiva;
use App\Services\EstadisticasService;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ManagesWeatherData;

    /**
     * Muestra el panel principal de administración con estadísticas.
     *
     * @return \Illuminate\View\View
     */
    public function panel()
    {
        // Realizar las consultas para obtener las estadísticas
        $totalProductores = Productor::count();
        $totalInstituciones = Institucion::count();
        $solicitudesPendientes = SolicitudVerificacion::where('estado', 'pendiente')->count();
        $totalUnidadesProductivas = \App\Models\UnidadProductiva::count();

        // Pasar los datos a la vista
        return view('admin.panel', [
            'totalProductores' => $totalProductores,
            'totalInstituciones' => $totalInstituciones,
            'solicitudesPendientes' => $solicitudesPendientes,
            'totalUnidadesProductivas' => $totalUnidadesProductivas,
        ]);
    }

    /**
     * Muestra la maqueta del nuevo panel de administración.
     *
     * @return \Illuminate\View\View
     */
    public function panelMaqueta(Request $request, EstadisticasService $estadisticasService)
    {
        // Obtener KPIs globales desde el servicio
        $stats = $estadisticasService->getKpisGlobales();
        
        // Obtener datos reales para los gráficos
        $productoresPorMunicipio = Productor::select('municipio', DB::raw('count(*) as total'))
            ->groupBy('municipio')
            ->orderBy('total', 'desc')
            ->limit(5) // Top 5 municipios
            ->get();
            
        $composicionGanadera = DB::table('stock_animals')
            ->join('especies', 'stock_animals.especie_id', '=', 'especies.id')
            ->select('especies.nombre', DB::raw('SUM(stock_animals.cantidad) as total'))
            ->groupBy('especies.nombre')
            ->get();

        // Obtener marcadores reales de las Unidades Productivas
        $mapMarkers = UnidadProductiva::with('productores')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function ($up) {
                $productoresNombres = $up->productores->pluck('nombre')->join(', ') ?: 'Sin productor';
                $superficie = $up->superficie ? number_format($up->superficie, 2) . ' ha' : 'N/A';
                
                return [
                    'lat' => (float) $up->latitud,
                    'lng' => (float) $up->longitud,
                    'popup' => "<b>UP:</b> {$up->rnspa}<br><b>Productor(es):</b> {$productoresNombres}<br><b>Superficie:</b> {$superficie}"
                ];
            })
            ->toArray();

        $activityItems = [
            ['icon' => 'heroicon-s-user-plus', 'text' => 'Nuevo productor registrado: Juan Perez', 'time' => 'hace 5 minutos'],
            ['icon' => 'heroicon-s-check-badge', 'text' => 'Institución aprobada: INTA', 'time' => 'hace 2 horas'],
            ['icon' => 'heroicon-s-arrow-up-on-square', 'text' => 'Se importaron 50 productores', 'time' => 'hace 1 día'],
            ['icon' => 'heroicon-s-user-plus', 'text' => 'Nuevo productor registrado: Maria Gomez', 'time' => 'hace 2 días'],
            ['icon' => 'heroicon-s-x-circle', 'text' => 'Solicitud rechazada: Coop. Agrícola', 'time' => 'hace 3 días'],
        ];

        $quickActions = [
            ['route' => '#', 'icon' => 'heroicon-s-user-plus', 'text' => 'Registrar Productor'],
            ['route' => '#', 'icon' => 'heroicon-s-building-office-2', 'text' => 'Registrar Institución'],
            ['route' => route('admin.productores.importar'), 'icon' => 'heroicon-s-arrow-up-on-square', 'text' => 'Importar Productores'],
            ['route' => '#', 'icon' => 'heroicon-s-cog-6-tooth', 'text' => 'Configuración del Sistema'],
        ];

        $pendingRequests = [
            ['institution' => 'Cooperativa Agrícola Misiones', 'date' => '2025-09-10'],
            ['institution' => 'Universidad Nacional de Misiones', 'date' => '2025-09-09'],
            ['institution' => 'Asociación de Ganaderos del Sur', 'date' => '2025-09-08'],
        ];

        $newsItems = [
            ['title' => 'Nuevas líneas de crédito para productores ovinos.', 'url' => '#', 'source' => 'Ministerio de Agricultura'],
            ['title' => 'El INTA presenta avances en pasturas resistentes a la sequía.', 'url' => '#', 'source' => 'INTA Informa'],
        ];

        // Obtener datos del clima por defecto para la carga inicial
        $defaultMunicipio = Municipio::where('nombre', 'Posadas')->first();
        $weatherData = $this->getWeatherDataForMunicipio($defaultMunicipio);

        return view('admin.panel-maqueta', [
            'stats' => $stats,
            'mapMarkers' => $mapMarkers,
            'activityItems' => $activityItems,
            'quickActions' => $quickActions,
            'pendingRequests' => $pendingRequests,
            'weatherData' => $weatherData,
            'newsItems' => $newsItems,
            'productoresPorMunicipio' => $productoresPorMunicipio,
            'composicionGanadera' => $composicionGanadera,
        ]);
    }
}