<?php

namespace App\Actions\Cuaderno;

use App\Models\Productor;
use App\Models\StockAnimal;
use Carbon\Carbon;

class FiltrarMovimientosHistorialAction
{
    /**
     * Filtra los movimientos del historial de un productor.
     *
     * @param Productor $productor
     * @param array $filters
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Productor $productor, array $filters, bool $paginate = true)
    {
        $unidadesProductorasIds = $productor->unidadesProductivas->pluck('id');

        $query = StockAnimal::whereIn('unidad_productiva_id', $unidadesProductorasIds)
            ->with(['motivo', 'especie', 'categoria', 'unidadProductiva'])
            ->orderBy('fecha_registro', 'desc');

        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $filters['fecha_hasta']);
        }

        if (!empty($filters['up_id'])) {
            $query->where('unidad_productiva_id', $filters['up_id']);
        }

        // Filtro por tipo de movimiento (flujo: alta/baja)
        if (!empty($filters['flujo'])) {
            $query->whereHas('motivo', function ($q) use ($filters) {
                $q->where('tipo', $filters['flujo']);
            });
        }

        // Filtro por motivo específico
        if (!empty($filters['motivo_id'])) {
            $query->where('motivo_movimiento_id', $filters['motivo_id']);
        }

        if ($paginate) {
            return $query->paginate(10)->withQueryString();
        }

        return $query->get();
    }
}


