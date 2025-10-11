<?php

namespace App\Services;

use App\Models\Productor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class StockHistoryService
{
    /**
     * Calcula el stock total de un productor en una fecha específica.
     *
     * @param Productor $productor El productor para el cual se calcula el stock.
     * @param Carbon $fecha La fecha hasta la cual se calculará el stock.
     * @param array $filtros Filtros adicionales como 'unidad_productiva_id' o 'especie_id'.
     * @return int El stock total calculado.
     */
    public function getStockAt(Productor $productor, Carbon $fecha, array $filtros = []): int
    {
        $query = DB::table('stock_animals')
            ->join('tipo_registros', 'stock_animals.tipo_registro_id', '=', 'tipo_registros.id')
            ->join('declaraciones_stock', 'stock_animals.declaracion_id', '=', 'declaraciones_stock.id')
            ->where('declaraciones_stock.productor_id', $productor->id)
            ->where('stock_animals.fecha_registro', '<=', $fecha->endOfDay());

        // Aplicar filtros
        $this->applyFilters($query, $filtros);

        $altas = (clone $query)->where('tipo_registros.nombre', 'alta')->sum('cantidad');
        $bajas = (clone $query)->where('tipo_registros.nombre', 'baja')->sum('cantidad');

        return $altas - $bajas;
    }

    /**
     * Genera una serie de datos que representa la evolución del stock entre dos fechas.
     *
     * @param Productor $productor
     * @param Carbon $fechaDesde
     * @param Carbon $fechaHasta
     * @param array $filtros
     * @param string $granularity La granularidad de los puntos de datos ('monthly', 'daily').
     * @return array Formateado para ChartJsBuilder.
     */
    public function getEvolutionBetween(Productor $productor, Carbon $fechaDesde, Carbon $fechaHasta, array $filtros = [], string $granularity = 'monthly'): array
    {
        $labels = [];
        $stockData = [];

        // Crear el período basado en la granularidad
        $period = CarbonPeriod::create($fechaDesde, '1 month', $fechaHasta);

        // Calcular el stock inicial ANTES del primer punto del período.
        // Esto asegura que la línea no siempre empiece en cero.
        $stockAnterior = $this->getStockAt($productor, $fechaDesde->copy()->subDay(), $filtros);

        foreach ($period as $date) {
            $fechaPunto = $date->endOfMonth(); // Usamos el final del mes para cada punto
            $labels[] = $fechaPunto->format('M Y');
            
            // El stock en este punto es el stock anterior + los movimientos del mes actual.
            // Una forma más simple es recalcular todo hasta la fecha del punto.
            $stockAcumulado = $this->getStockAt($productor, $fechaPunto, $filtros);
            $stockData[] = $stockAcumulado;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Stock Total', 'data' => $stockData],
            ]
        ];
    }

    /**
     * Aplica filtros comunes a una consulta de stock.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $filtros
     */
    private function applyFilters($query, array $filtros)
    {
        $query->when(isset($filtros['unidad_productiva_id']) && $filtros['unidad_productiva_id'], function ($q) use ($filtros) {
            return $q->where('stock_animals.unidad_productiva_id', $filtros['unidad_productiva_id']);
        });

        $query->when(isset($filtros['especie_id']) && $filtros['especie_id'], function ($q) use ($filtros) {
            return $q->where('stock_animals.especie_id', $filtros['especie_id']);
        });
    }
}
