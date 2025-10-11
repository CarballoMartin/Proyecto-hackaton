<?php

namespace App\Actions\Cuaderno;

use App\Models\Productor;
use App\Models\StockAnimal;
use App\Models\DeclaracionStock;
use App\Models\ConfiguracionActualizacion;
use App\Models\MotivoMovimiento;
use App\Models\StockActual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuardarMovimientosAction
{
    /**
     * Guarda los movimientos de stock y actualiza la tabla de stock agregado.
     *
     * @param array $movimientos
     * @param integer $upId
     * @return void
     * @throws \Exception
     */
    public function __invoke(array $movimientos, int $upId)
    {
        if (empty($movimientos) || is_null($upId)) {
            throw new \InvalidArgumentException('No hay movimientos para guardar o no se ha seleccionado una chacra.');
        }

        DB::transaction(function () use ($movimientos, $upId) {
            $productor = Productor::where('usuario_id', Auth::id())->firstOrFail();

            $periodoActivo = ConfiguracionActualizacion::where('activo', true)
                ->where('proxima_actualizacion', '>=', now())
                ->orderBy('proxima_actualizacion', 'asc')
                ->first();

            if (!$periodoActivo) {
                throw new \InvalidArgumentException('No hay un período de declaración activo. Por favor, contacte a un administrador para que configure uno.');
            }

            $declaracion = DeclaracionStock::firstOrCreate(
                [
                    'productor_id' => $productor->id,
                    'periodo_id' => $periodoActivo->id,
                    'unidad_productiva_id' => $upId,
                ],
                [
                    'fecha_declaracion' => now(),
                    'estado' => 'en_progreso',
                ]
            );

            foreach ($movimientos as $movimiento) {
                $motivo = MotivoMovimiento::find($movimiento['motivo_movimiento_id']);
                if (!$motivo) {
                    throw new \InvalidArgumentException('El motivo de movimiento seleccionado no es válido.');
                }

                if (str_contains(strtolower($motivo->nombre), 'traslado') && empty($movimiento['destino_traslado'])) {
                    throw new \InvalidArgumentException('El destino del traslado es obligatorio para el motivo "' . $motivo->nombre . '".');
                }

                // Paso 1: Grabar el movimiento en el historial
                StockAnimal::create([
                    'declaracion_id' => $declaracion->id,
                    'unidad_productiva_id' => $upId,
                    'especie_id' => $movimiento['especie_id'],
                    'categoria_id' => $movimiento['categoria_id'],
                    'raza_id' => $movimiento['raza_id'],
                    'cantidad' => $movimiento['cantidad'],
                    'motivo_movimiento_id' => $movimiento['motivo_movimiento_id'],
                    'destino_traslado' => $movimiento['destino_traslado'] ?? null,
                    'tipo_registro_id' => 2, // ID 2 = Declaración Periódica
                    'fecha_registro' => now(),
                ]);

                // Paso 2: Actualizar la tabla agregada 'stock_actual'
                $attributes = [
                    'unidad_productiva_id' => $upId,
                    'especie_id' => $movimiento['especie_id'],
                    'categoria_id' => $movimiento['categoria_id'],
                    'raza_id' => $movimiento['raza_id'],
                ];

                $cantidad = (int) $movimiento['cantidad'];

                if ($motivo->tipo === 'alta') {
                    $updated = StockActual::where($attributes)->increment('cantidad_actual', $cantidad);
                    if ($updated === 0) { // Si no se actualizó ninguna fila, no existía.
                        StockActual::create(array_merge($attributes, ['cantidad_actual' => $cantidad]));
                    }
                } else { // 'baja'
                    $updated = StockActual::where($attributes)->decrement('cantidad_actual', $cantidad);
                    if ($updated === 0) { // Si no se actualizó ninguna fila, no existía.
                        StockActual::create(array_merge($attributes, ['cantidad_actual' => -$cantidad]));
                    }
                }
            }
        });
    }
}