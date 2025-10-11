<?php

namespace App\Services;

use App\Models\Institucion;
use App\Models\Productor;
use App\Models\SolicitudVerificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadisticasService
{
    protected $stockHistoryService;

    public function __construct(StockHistoryService $stockHistoryService)
    {
        $this->stockHistoryService = $stockHistoryService;
    }

    /**
     * Obtiene los KPIs globales para el panel de administración.
     *
     * @return array
     */
    public function getKpisGlobales(): array
    {
        $totalProductores = Productor::count();
        $productoresActivos = User::where('rol', 'productor')
                                  ->where('last_login_at', '>=', Carbon::now()->subDays(90))
                                  ->count();

        return [
            'totalProductores' => $totalProductores,
            'totalInstituciones' => Institucion::count(),
            'solicitudesPendientes' => SolicitudVerificacion::where('estado', 'pendiente')->count(),
            'productoresActivos' => $productoresActivos,
            'productoresInactivos' => $totalProductores - $productoresActivos,
            'totalAnimales' => DB::table('stock_animals')->sum('cantidad'), // Corregido para usar stock_animals
        ];
    }

    /**
     * Obtiene la composición de stock por especie.
     *
     * @param Productor|null $productor
     * @param array $filtros
     * @return array
     */
    public function getComposicionPorEspecie(Productor $productor = null, array $filtros = []): array
    {
        $query = DB::table('stock_animals')
            ->join('especies', 'stock_animals.especie_id', '=', 'especies.id')
            ->select('especies.nombre as especie', DB::raw('SUM(stock_animals.cantidad) as total'));

        if ($productor) {
            $unidadesProductorasIds = $productor->unidadesProductivas()->pluck('unidades_productivas.id');
            $query->whereIn('stock_animals.unidad_productiva_id', $unidadesProductorasIds);
        }

        $query->when($filtros['unidad_productiva_id'] ?? null, function ($q, $unidadProductivaId) {
            return $q->where('stock_animals.unidad_productiva_id', $unidadProductivaId);
        });

        // Los filtros de fecha no aplican a stock_animals, se eliminan.

        return $query->groupBy('especies.nombre')->pluck('total', 'especie')->toArray();
    }

    /**
     * Obtiene la composición de stock por categoría.
     *
     * @param Productor|null $productor
     * @param array $filtros
     * @return array
     */
    public function getComposicionPorCategoria(Productor $productor = null, array $filtros = []): array
    {
        $query = DB::table('stock_animals')
            ->join('categoria_animals', 'stock_animals.categoria_id', '=', 'categoria_animals.id')
            ->select('categoria_animals.nombre as categoria', DB::raw('SUM(stock_animals.cantidad) as total'));

        if ($productor) {
            $unidadesProductorasIds = $productor->unidadesProductivas()->pluck('unidades_productivas.id');
            $query->whereIn('stock_animals.unidad_productiva_id', $unidadesProductorasIds);
        }

        $query->when($filtros['unidad_productiva_id'] ?? null, function ($q, $unidadProductivaId) {
            return $q->where('stock_animals.unidad_productiva_id', $unidadProductivaId);
        });

        $query->when($filtros['especie_id'] ?? null, function ($q, $especieId) {
            return $q->where('stock_animals.especie_id', $especieId);
        });

        // Los filtros de fecha no aplican a stock_animals, se eliminan.

        return $query->groupBy('categoria_animals.nombre')->pluck('total', 'categoria')->toArray();
    }

    /**
     * Obtiene la evolución mensual del stock (altas y bajas).
     *
     * @param Productor|null $productor
     * @param array $filtros
     * @return array
     */
    public function getEvolucionStockMensual(Productor $productor, array $filtros = []): array
    {
        // Definir el rango de fechas (por defecto, últimos 12 meses)
        $fechaHasta = Carbon::parse($filtros['fecha_hasta'] ?? Carbon::now());
        $fechaDesde = Carbon::parse($filtros['fecha_desde'] ?? $fechaHasta->copy()->subYear()->addDay());

        return $this->stockHistoryService->getEvolutionBetween($productor, $fechaDesde, $fechaHasta, $filtros);
    }

    /**
     * Obtiene un resumen de stock y carga animal por unidad productiva.
     *
     * @param Productor $productor
     * @param array $filtros
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResumenPorUnidadProductiva(Productor $productor, array $filtros = [])
    {
        // 1. Obtener las Unidades Productivas del productor
        $unidadesProductivas = $productor->unidadesProductivas();

        if (!empty($filtros['unidad_productiva_id'])) {
            $unidadesProductivas->where('unidades_productivas.id', $filtros['unidad_productiva_id']);
        }

        $unidades = $unidadesProductivas->get();
        $upIds = $unidades->pluck('id');

        // 2. Obtener los totales de stock para esas unidades, aplicando filtros
        $stockQuery = DB::table('stock_animals')
            ->select('unidad_productiva_id', DB::raw('SUM(cantidad) as total_animales'))
            ->whereIn('unidad_productiva_id', $upIds)
            ->groupBy('unidad_productiva_id');

        $stockQuery->when($filtros['especie_id'] ?? null, function ($q, $especieId) {
            return $q->where('especie_id', $especieId);
        });

        $stockQuery->when($filtros['fecha_desde'] ?? null, function ($q, $fechaDesde) {
            return $q->where('fecha_registro', '>=', $fechaDesde);
        });

        $stockQuery->when($filtros['fecha_hasta'] ?? null, function ($q, $fechaHasta) {
            return $q->where('fecha_registro', '<=', $fechaHasta);
        });

        $stockTotals = $stockQuery->pluck('total_animales', 'unidad_productiva_id');

        // 3. Unir los resultados en la colección de Unidades Productivas
        $unidades->each(function ($up) use ($stockTotals) {
            $up->total_animales = $stockTotals[$up->id] ?? 0;
            $up->carga_animal = $up->superficie > 0 ? number_format(($up->total_animales / $up->superficie), 2) : 0;
        });

        return $unidades;
    }
}
