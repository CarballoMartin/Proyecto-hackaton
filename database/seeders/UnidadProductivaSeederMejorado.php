<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnidadProductiva;
use App\Models\Productor;
use App\Models\Municipio;
use App\Models\Paraje;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;
use App\Models\TipoIdentificador;

class UnidadProductivaSeederMejorado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productores = Productor::all();
        
        if ($productores->isEmpty()) {
            $this->command->warn('No hay productores en la base de datos. Ejecuta primero ProductorSeederMejorado.');
            return;
        }

        // Obtener datos de catálogos
        $municipios = Municipio::all();
        $parajes = Paraje::all();
        $fuentesAgua = FuenteAgua::all();
        $tiposPasto = TipoPasto::all();
        $tiposSuelo = TipoSuelo::all();
        $tiposIdentificador = TipoIdentificador::all();

        $unidadesProductivas = [
            [
                'nombre' => 'Estancia San Juan',
                'identificador_local' => 'EST-001',
                'superficie' => 150.5,
                'habita' => true,
                'latitud' => -27.3688,
                'longitud' => -55.8968,
                'observaciones' => 'Estancia dedicada a la cría de ovinos y caprinos',
            ],
            [
                'nombre' => 'Campo Los Pinos',
                'identificador_local' => 'CAM-002',
                'superficie' => 200.0,
                'habita' => true,
                'latitud' => -27.4200,
                'longitud' => -55.9000,
                'observaciones' => 'Campo mixto con ganadería y agricultura',
            ],
            [
                'nombre' => 'Chacra El Progreso',
                'identificador_local' => 'CHA-003',
                'superficie' => 75.3,
                'habita' => false,
                'latitud' => -27.3800,
                'longitud' => -55.8800,
                'observaciones' => 'Chacra familiar especializada en caprinos',
            ],
            [
                'nombre' => 'Establecimiento La Esperanza',
                'identificador_local' => 'EST-004',
                'superficie' => 300.8,
                'habita' => true,
                'latitud' => -27.3500,
                'longitud' => -55.9200,
                'observaciones' => 'Gran establecimiento ganadero',
            ],
            [
                'nombre' => 'Campo San Antonio',
                'identificador_local' => 'CAM-005',
                'superficie' => 120.7,
                'habita' => true,
                'latitud' => -27.4000,
                'longitud' => -55.8500,
                'observaciones' => 'Campo con pasturas mejoradas',
            ],
            [
                'nombre' => 'Chacra Los Robles',
                'identificador_local' => 'CHA-006',
                'superficie' => 90.2,
                'habita' => false,
                'latitud' => -27.3600,
                'longitud' => -55.8700,
                'observaciones' => 'Chacra con sistema de riego',
            ],
            [
                'nombre' => 'Estancia El Refugio',
                'identificador_local' => 'EST-007',
                'superficie' => 180.4,
                'habita' => true,
                'latitud' => -27.3900,
                'longitud' => -55.9100,
                'observaciones' => 'Estancia con manejo sustentable',
            ],
            [
                'nombre' => 'Campo Las Flores',
                'identificador_local' => 'CAM-008',
                'superficie' => 110.6,
                'habita' => true,
                'latitud' => -27.3700,
                'longitud' => -55.8900,
                'observaciones' => 'Campo con diversificación productiva',
            ],
            [
                'nombre' => 'Chacra El Paraíso',
                'identificador_local' => 'CHA-009',
                'superficie' => 65.8,
                'habita' => false,
                'latitud' => -27.4100,
                'longitud' => -55.8600,
                'observaciones' => 'Chacra orgánica certificada',
            ],
            [
                'nombre' => 'Establecimiento San Miguel',
                'identificador_local' => 'EST-010',
                'superficie' => 250.3,
                'habita' => true,
                'latitud' => -27.3300,
                'longitud' => -55.9400,
                'observaciones' => 'Establecimiento con tecnología de punta',
            ]
        ];

        foreach ($productores as $index => $productor) {
            // Crear 2-3 unidades productivas por productor
            $cantidadUnidades = rand(2, 3);
            
            for ($i = 0; $i < $cantidadUnidades; $i++) {
                $unidadData = $unidadesProductivas[($index * 3 + $i) % count($unidadesProductivas)];
                
                $unidad = UnidadProductiva::create([
                    'nombre' => $unidadData['nombre'] . ' - ' . $productor->nombre,
                    'identificador_local' => $unidadData['identificador_local'] . '-' . ($index * 3 + $i + 1),
                    'tipo_identificador_id' => $tiposIdentificador->random()->id,
                    'activo' => true,
                    'completo' => true,
                    'superficie' => $unidadData['superficie'],
                    'habita' => $unidadData['habita'],
                    'municipio_id' => $municipios->random()->id,
                    'paraje_id' => $parajes->random()->id,
                    'agua_humano_fuente_id' => $fuentesAgua->random()->id,
                    'agua_humano_en_casa' => rand(0, 1) == 1,
                    'agua_humano_distancia' => rand(50, 2000),
                    'agua_animal_fuente_id' => $fuentesAgua->random()->id,
                    'agua_animal_distancia' => rand(100, 3000),
                    'tipo_pasto_predominante_id' => $tiposPasto->random()->id,
                    'tipo_suelo_predominante_id' => $tiposSuelo->random()->id,
                    'forrajeras_predominante' => rand(0, 1) == 1,
                    'latitud' => $unidadData['latitud'] + (rand(-100, 100) / 10000),
                    'longitud' => $unidadData['longitud'] + (rand(-100, 100) / 10000),
                    'observaciones' => $unidadData['observaciones'],
                ]);

                // Asociar la unidad productiva al productor
                $productor->unidadesProductivas()->attach($unidad->id);
            }
        }
    }
}




















