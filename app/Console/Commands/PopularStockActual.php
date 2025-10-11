<?php

namespace App\Console\Commands;

use App\Models\StockActual;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopularStockActual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:popular-actual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula el stock actual desde el historial de movimientos y lo guarda en la tabla stock_actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('¿Está seguro de que desea vaciar la tabla stock_actual y repoblarla desde el historial? Esta acción no se puede deshacer.')) {
            $this->info('Iniciando el proceso de población de stock actual...');

            // Paso 1: Vaciar la tabla
            $this->line('Paso 1/3: Vaciando la tabla stock_actual...');
            StockActual::truncate();
            $this->info('Tabla vaciada.');

            // Paso 2: Calcular el stock actual desde el historial
            $this->line('Paso 2/3: Calculando el stock actual desde el historial de movimientos...');
            
            $stockCalculado = DB::table('stock_animals')
                ->join('motivo_movimientos', 'stock_animals.motivo_movimiento_id', '=', 'motivo_movimientos.id')
                ->select(
                    'stock_animals.unidad_productiva_id',
                    'stock_animals.especie_id',
                    'stock_animals.categoria_id',
                    'stock_animals.raza_id',
                    DB::raw("SUM(CASE WHEN motivo_movimientos.tipo = 'alta' THEN stock_animals.cantidad ELSE -stock_animals.cantidad END) as total")
                )
                ->groupBy(
                    'stock_animals.unidad_productiva_id',
                    'stock_animals.especie_id',
                    'stock_animals.categoria_id',
                    'stock_animals.raza_id'
                )
                ->get();

            $this->info('Cálculo completado. Se procesarán ' . $stockCalculado->count() . ' registros.');

            // Paso 3: Insertar los datos en la nueva tabla
            $this->line('Paso 3/3: Insertando los datos en la tabla stock_actual...');
            $bar = $this->output->createProgressBar($stockCalculado->count());
            $bar->start();

            foreach ($stockCalculado as $stock) {
                // Solo insertamos si el total es mayor a 0 para no tener filas con stock 0 o negativo.
                if ($stock->total > 0) {
                    StockActual::create([
                        'unidad_productiva_id' => $stock->unidad_productiva_id,
                        'especie_id' => $stock->especie_id,
                        'categoria_id' => $stock->categoria_id,
                        'raza_id' => $stock->raza_id,
                        'cantidad_actual' => $stock->total,
                    ]);
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info('¡Proceso completado! La tabla stock_actual ha sido poblada exitosamente.');
        }
    }
}