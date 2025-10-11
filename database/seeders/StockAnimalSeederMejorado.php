<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockAnimal;
use App\Models\UnidadProductiva;
use App\Models\Especie;
use App\Models\Raza;
use App\Models\CategoriaAnimal;
use App\Models\TipoRegistro;
use App\Models\MotivoMovimiento;
use App\Models\DeclaracionStock;
use App\Models\ConfiguracionActualizacion;
use Carbon\Carbon;

class StockAnimalSeederMejorado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidadesProductivas = UnidadProductiva::with('productores')->get();
        
        if ($unidadesProductivas->isEmpty()) {
            $this->command->warn('No hay unidades productivas en la base de datos. Ejecuta primero UnidadProductivaSeederMejorado.');
            return;
        }

        // Obtener datos de catálogos
        $especies = Especie::all();
        $razas = Raza::all();
        $categorias = CategoriaAnimal::all();
        $tiposRegistro = TipoRegistro::all();
        $motivos = MotivoMovimiento::all();
        
        // Obtener o crear período de declaración activo
        $periodo = ConfiguracionActualizacion::where('activo', true)->first();
        if (!$periodo) {
            $periodo = ConfiguracionActualizacion::create([
                'nombre' => 'Período 2024-2025',
                'fecha_inicio' => Carbon::now()->subMonths(6),
                'fecha_fin' => Carbon::now()->addMonths(6),
                'proxima_actualizacion' => Carbon::now()->addMonths(3),
                'activo' => true,
            ]);
        }

        foreach ($unidadesProductivas as $unidad) {
            $productor = $unidad->productores->first();
            if (!$productor) continue;

            // Crear declaración de stock para el productor
            $declaracion = DeclaracionStock::create([
                'productor_id' => $productor->id,
                'unidad_productiva_id' => $unidad->id,
                'periodo_id' => $periodo->id,
                'fecha_declaracion' => Carbon::now()->subDays(rand(1, 30)),
                'estado' => 'completada',
            ]);

            // Crear stock para diferentes especies
            $especiesParaCrear = $especies->random(rand(1, 3));
            
            foreach ($especiesParaCrear as $especie) {
                $razasEspecie = $razas->where('especie_id', $especie->id);
                $categoriasEspecie = $categorias->where('especie_id', $especie->id);
                
                if ($razasEspecie->isEmpty() || $categoriasEspecie->isEmpty()) continue;

                // Crear 3-8 registros por especie
                $cantidadRegistros = rand(3, 8);
                
                for ($i = 0; $i < $cantidadRegistros; $i++) {
                    $raza = $razasEspecie->random();
                    $categoria = $categoriasEspecie->random();
                    $tipoRegistro = $tiposRegistro->random();
                    $motivo = $motivos->random();
                    
                    // Cantidad realista según la categoría
                    $cantidad = $this->getCantidadRealista($categoria->nombre, $especie->nombre);
                    
                    StockAnimal::create([
                        'declaracion_id' => $declaracion->id,
                        'unidad_productiva_id' => $unidad->id,
                        'especie_id' => $especie->id,
                        'categoria_id' => $categoria->id,
                        'raza_id' => $raza->id,
                        'tipo_registro_id' => $tipoRegistro->id,
                        'cantidad' => $cantidad,
                        'observaciones' => $this->getObservacionRealista($especie->nombre, $categoria->nombre),
                        'motivo_movimiento_id' => $motivo->id,
                        'destino_traslado' => $motivo->tipo === 'baja' ? $this->getDestinoTraslado() : null,
                        'fecha_registro' => Carbon::now()->subDays(rand(1, 90)),
                    ]);
                }
            }
        }
    }

    private function getCantidadRealista($categoria, $especie)
    {
        $cantidades = [
            'Ovino' => [
                'Cordero' => rand(5, 25),
                'Oveja' => rand(8, 40),
                'Carnero' => rand(2, 8),
                'Borrego' => rand(3, 15),
                'Capón' => rand(1, 5),
            ],
            'Caprino' => [
                'Cabra' => rand(6, 30),
                'Chivo' => rand(3, 12),
                'Cabrito' => rand(4, 20),
                'Macho reproductor' => rand(1, 4),
            ],
            'Bovino' => [
                'Vaca' => rand(3, 15),
                'Toro' => rand(1, 3),
                'Ternero' => rand(2, 10),
                'Novillo' => rand(1, 8),
            ]
        ];

        return $cantidades[$especie][$categoria] ?? rand(1, 10);
    }

    private function getObservacionRealista($especie, $categoria)
    {
        $observaciones = [
            'Ovino' => [
                'Cordero' => 'Corderos en crecimiento, buen estado sanitario',
                'Oveja' => 'Ovejas reproductoras, algunas preñadas',
                'Carnero' => 'Carneros reproductores, excelente genética',
            ],
            'Caprino' => [
                'Cabra' => 'Cabras lecheras, buena producción',
                'Chivo' => 'Chivos para engorde',
                'Cabrito' => 'Cabritos en desarrollo',
            ],
            'Bovino' => [
                'Vaca' => 'Vacas lecheras, algunas preñadas',
                'Toro' => 'Toros reproductores',
                'Ternero' => 'Terneros en crecimiento',
            ]
        ];

        return $observaciones[$especie][$categoria] ?? 'Animales en buen estado general';
    }

    private function getDestinoTraslado()
    {
        $destinos = [
            'Mercado local de Posadas',
            'Frigorífico de Oberá',
            'Mercado de Candelaria',
            'Comprador de Apóstoles',
            'Feria de San Javier',
            'Exportación a Brasil',
        ];

        return $destinos[array_rand($destinos)];
    }
}




















