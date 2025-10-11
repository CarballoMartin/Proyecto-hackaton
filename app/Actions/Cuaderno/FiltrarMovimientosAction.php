<?php

namespace App\Actions\Cuaderno;

use App\Models\StockAnimal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FiltrarMovimientosAction
{
    /**
     * Filtra y devuelve movimientos de stock según un array de criterios.
     *
     * @param array $filtros
     * @return Collection
     */
    public function __invoke(array $filtros = []): Collection
    {
        $query = StockAnimal::query()
            ->with(['especie', 'categoria', 'raza', 'motivo', 'unidadProductiva']);

        // Filtrar por un array de IDs de declaración
        if (!empty($filtros['declaracion_ids'])) {
            $query->whereIn('declaracion_id', $filtros['declaracion_ids']);
        }

        // Filtrar por un array de IDs de unidad productiva
        if (!empty($filtros['unidad_productiva_ids'])) {
            $query->whereIn('unidad_productiva_id', $filtros['unidad_productiva_ids']);
        }

        // Filtrar por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $query->where('fecha_registro', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->where('fecha_registro', '<=', $filtros['fecha_hasta']);
        }

        // Filtrar por día de la semana (0=Domingo, 1=Lunes, ...)
        if (isset($filtros['day_of_week'])) {
            $query->where(DB::raw('DAYOFWEEK(fecha_registro)'), '=', (int)$filtros['day_of_week'] + 1);
        }

        return $query->get();
    }
}
