<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockAnimal;
use App\Models\UnidadProductiva;
use App\Models\Especie;
use App\Models\Raza;
use App\Models\CategoriaAnimal;
use App\Models\TipoRegistro;
use App\Models\Productor;
use App\Models\DeclaracionStock;
use App\Models\ConfiguracionActualizacion;
use Carbon\Carbon;

class StockAnimalHistoricoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidadesProductivas = UnidadProductiva::all();
        
        if ($unidadesProductivas->isEmpty()) {
            $this->command->error('⚠️ No hay unidades productivas en la BD');
            return;
        }

        $especies = Especie::all();
        $tiposRegistro = TipoRegistro::all();
        $productores = Productor::all();
        $periodo = ConfiguracionActualizacion::first();
        
        if ($especies->isEmpty() || $tiposRegistro->isEmpty()) {
            $this->command->error('⚠️ Necesitas especies y tipos de registro en la BD');
            return;
        }

        // Crear una declaración por cada unidad productiva
        $declaraciones = [];
        foreach ($unidadesProductivas as $up) {
            $productor = $up->productores()->first();
            if (!$productor) continue;
            
            $declaracion = DeclaracionStock::create([
                'productor_id' => $productor->id,
                'periodo_id' => $periodo ? $periodo->id : 1,
                'unidad_productiva_id' => $up->id,
                'fecha_declaracion' => now()->subMonths(rand(1, 3))->format('Y-m-d'),
                'estado' => rand(0, 10) > 2 ? 'completada' : 'en_progreso',
                'observaciones' => 'Declaración generada automáticamente con datos de prueba',
            ]);
            $declaraciones[$up->id] = $declaracion->id;
        }

        $totalMovimientos = 0;
        $mesesHistorico = 12; // Generaremos movimientos de los últimos 12 meses

        foreach ($unidadesProductivas as $up) {
            // Cada UP tendrá 1-2 especies
            $cantidadEspecies = rand(1, 2);
            $especiesSeleccionadas = $especies->random($cantidadEspecies);

            foreach ($especiesSeleccionadas as $especie) {
                // Obtener razas y categorías para esta especie
                $razas = Raza::where('especie_id', $especie->id)->get();
                $categorias = CategoriaAnimal::where('especie_id', $especie->id)->get();
                
                if ($razas->isEmpty() || $categorias->isEmpty()) {
                    continue;
                }

                // Por cada especie, crear 2-4 categorías diferentes
                $cantidadCategorias = rand(2, 4);
                $categoriasSeleccionadas = $categorias->random(min($cantidadCategorias, $categorias->count()));

                foreach ($categoriasSeleccionadas as $categoria) {
                    $raza = $razas->random();
                    
                    // Generar movimientos históricos para esta combinación
                    $stockActual = 0;
                    
                    // Crear movimientos distribuidos en los últimos 12 meses
                    $cantidadMovimientos = rand(3, 8);
                    
                    for ($i = 0; $i < $cantidadMovimientos; $i++) {
                        // Fecha del movimiento (más antiguos primero)
                        $mesesAtras = $mesesHistorico - ($i * ($mesesHistorico / $cantidadMovimientos));
                        $fechaRegistro = Carbon::now()->subMonths(intval($mesesAtras))
                            ->addDays(rand(-15, 15))
                            ->format('Y-m-d');

                        // Tipo de movimiento y cantidad
                        $movimiento = $this->generarMovimiento($especie->nombre, $categoria->nombre, $stockActual);
                        
                        StockAnimal::create([
                            'declaracion_id' => $declaraciones[$up->id],
                            'unidad_productiva_id' => $up->id,
                            'especie_id' => $especie->id,
                            'raza_id' => $raza->id,
                            'categoria_id' => $categoria->id,
                            'tipo_registro_id' => $movimiento['tipo_registro_id'],
                            'cantidad' => $movimiento['cantidad'],
                            'fecha_registro' => $fechaRegistro,
                            'observaciones' => $movimiento['observaciones'],
                            'destino_traslado' => $movimiento['destino_procedencia'],
                        ]);

                        $stockActual = $movimiento['stock_resultante'];
                        $totalMovimientos++;
                    }
                }
            }
        }

        $this->command->info("🎉 Creados $totalMovimientos movimientos históricos de stock");
        $this->command->info("📊 Distribuidos en los últimos $mesesHistorico meses");
    }

    private function generarMovimiento(string $especie, string $categoria, int $stockActual): array
    {
        // Determinar tipo de movimiento basado en stock actual
        if ($stockActual == 0) {
            // Si no hay stock, debe ser una entrada
            $tipoMovimiento = 'entrada';
        } elseif ($stockActual > 100) {
            // Si hay mucho stock, mayor probabilidad de venta
            $tipoMovimiento = rand(1, 10) <= 6 ? 'salida' : 'entrada';
        } else {
            // Balance normal
            $tipoMovimiento = rand(1, 10) <= 5 ? 'entrada' : 'salida';
        }

        $tiposRegistro = TipoRegistro::all();
        
        if ($tipoMovimiento == 'entrada') {
            return $this->generarEntrada($tiposRegistro, $stockActual, $especie, $categoria);
        } else {
            return $this->generarSalida($tiposRegistro, $stockActual, $especie, $categoria);
        }
    }

    private function generarEntrada($tiposRegistro, int $stockActual, string $especie, string $categoria): array
    {
        $tiposEntrada = ['Compra', 'Nacimiento', 'Donación', 'Traslado Interno'];
        $motivo = $tiposEntrada[array_rand($tiposEntrada)];
        
        // Tipo de registro (buscar por nombre o tomar el primero)
        $tipoRegistro = $tiposRegistro->firstWhere('nombre', 'Alta') 
                       ?? $tiposRegistro->first();
        
        // Cantidad según el tipo
        switch ($motivo) {
            case 'Nacimiento':
                $cantidad = rand(1, 15); // Nacimientos realistas
                $destino = null;
                $obs = "Nacimientos de $categoria de $especie en el período";
                break;
            case 'Compra':
                $cantidad = rand(5, 50); // Compras en lotes
                $destino = $this->generarNombreProveedor();
                $obs = "Compra de $cantidad cabezas de $categoria";
                break;
            case 'Donación':
                $cantidad = rand(2, 10);
                $destino = "Programa gubernamental";
                $obs = "Donación recibida";
                break;
            default: // Traslado
                $cantidad = rand(3, 20);
                $destino = "Campo propio";
                $obs = "Traslado desde otra unidad productiva";
        }

        return [
            'tipo_registro_id' => $tipoRegistro->id,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'destino_procedencia' => $destino,
            'observaciones' => $obs,
            'stock_resultante' => $stockActual + $cantidad,
        ];
    }

    private function generarSalida($tiposRegistro, int $stockActual, string $especie, string $categoria): array
    {
        $tiposSalida = ['Venta', 'Muerte', 'Faena', 'Traslado'];
        $motivo = $tiposSalida[array_rand($tiposSalida)];
        
        // Tipo de registro
        $tipoRegistro = $tiposRegistro->firstWhere('nombre', 'Baja') 
                       ?? $tiposRegistro->skip(1)->first() 
                       ?? $tiposRegistro->first();
        
        // Cantidad según el tipo (no puede exceder el stock)
        switch ($motivo) {
            case 'Muerte':
                $cantidad = min(rand(1, 5), $stockActual);
                $destino = null;
                $obs = "Pérdidas por causas naturales o enfermedad";
                break;
            case 'Venta':
                $cantidad = min(rand(5, 30), $stockActual);
                $destino = $this->generarNombreComprador();
                $obs = "Venta de $cantidad cabezas de $categoria";
                break;
            case 'Faena':
                $cantidad = min(rand(1, 8), $stockActual);
                $destino = "Consumo propio";
                $obs = "Faena para consumo";
                break;
            default: // Traslado
                $cantidad = min(rand(3, 15), $stockActual);
                $destino = "Otra unidad productiva";
                $obs = "Traslado a otro campo";
        }

        // Evitar stock negativo
        $cantidad = min($cantidad, $stockActual);
        
        return [
            'tipo_registro_id' => $tipoRegistro->id,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'destino_procedencia' => $destino,
            'observaciones' => $obs,
            'stock_resultante' => max(0, $stockActual - $cantidad),
        ];
    }

    private function generarNombreProveedor(): string
    {
        $proveedores = [
            'Cabaña Los Pinos',
            'Establecimiento San José',
            'Criadores Asociados del Sur',
            'Cabaña La Esperanza',
            'Feria Ganadera Regional',
            'Cooperativa de Productores',
            'Remate Feria Central',
        ];
        
        return $proveedores[array_rand($proveedores)];
    }

    private function generarNombreComprador(): string
    {
        $compradores = [
            'Frigorífico Regional',
            'Consignataria del Sur',
            'Exportadora Ganadera',
            'Comprador particular',
            'Feria de concentración',
            'Frigorífico La Querencia',
            'Matarifes locales',
        ];
        
        return $compradores[array_rand($compradores)];
    }
}

