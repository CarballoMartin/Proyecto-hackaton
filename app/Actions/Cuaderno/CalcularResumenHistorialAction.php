<?php

namespace App\Actions\Cuaderno;

use App\Models\Productor;
use App\Models\StockAnimal;
use App\Models\MotivoMovimiento;
use Carbon\Carbon;

class CalcularResumenHistorialAction
{
    public function __invoke(Productor $productor, array $filters)
    {
        $unidadesProductorasIds = $productor->unidadesProductivas->pluck('id');

        $query = StockAnimal::query()
            ->join('motivo_movimientos', 'stock_animals.motivo_movimiento_id', '=', 'motivo_movimientos.id')
            ->whereIn('stock_animals.unidad_productiva_id', $unidadesProductorasIds);

        if (!empty($filters['fecha_desde'])) {
            $query->where('stock_animals.fecha_registro', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->where('stock_animals.fecha_registro', '<=', Carbon::parse($filters['fecha_hasta'])->endOfDay());
        }

        if (!empty($filters['up_id'])) {
            $query->where('stock_animals.unidad_productiva_id', $filters['up_id']);
        }

        if (!empty($filters['flujo'])) {
            $query->where('motivo_movimientos.tipo', $filters['flujo']);
        }

        if (!empty($filters['motivo_id'])) {
            $query->where('stock_animals.motivo_movimiento_id', $filters['motivo_id']);
        }

        $breakdown = $query
            ->select('motivo_movimientos.nombre as motivo_nombre', 'motivo_movimientos.tipo as motivo_tipo')
            ->selectRaw('SUM(stock_animals.cantidad) as total')
            ->groupBy('motivo_movimientos.nombre', 'motivo_movimientos.tipo')
            ->get()
            ->keyBy('motivo_nombre');

        $summary = [
            'altas' => ['total' => 0],
            'bajas' => ['total' => 0],
            'specific_total' => null,
            'motivo_filtrado' => null,
        ];

        foreach ($breakdown as $motivo_nombre => $data) {
            if ($data->motivo_tipo == 'alta') {
                $summary['altas'][$motivo_nombre] = $data->total;
                $summary['altas']['total'] += $data->total;
            } elseif ($data->motivo_tipo == 'baja') {
                $summary['bajas'][$motivo_nombre] = $data->total;
                $summary['bajas']['total'] += $data->total;
            }
        }

        if (!empty($filters['motivo_id'])) {
            $motivoFiltrado = MotivoMovimiento::find($filters['motivo_id']);
            if ($motivoFiltrado && isset($breakdown[$motivoFiltrado->nombre])) {
                $summary['specific_total'] = $breakdown[$motivoFiltrado->nombre]->total;
                $summary['motivo_filtrado'] = $motivoFiltrado->nombre;
            }
        }

        return $summary;
    }
}