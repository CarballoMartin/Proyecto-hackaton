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

class UnidadesProductivasMasivasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productores = Productor::all();
        
        if ($productores->isEmpty()) {
            $this->command->error('⚠️ No hay productores en la BD');
            return;
        }

        $municipios = Municipio::all();
        $parajes = Paraje::all();
        $fuentesAgua = FuenteAgua::all();
        $tiposPasto = TipoPasto::all();
        $tiposSuelo = TipoSuelo::all();
        $tiposIdentificador = TipoIdentificador::all();

        $nombresEstablecimientos = [
            'Estancia', 'Campo', 'Chacra', 'Establecimiento', 'Finca', 
            'Quinta', 'Granja', 'Rancho', 'Hacienda'
        ];

        $nombresPropios = [
            'San José', 'La Esperanza', 'El Progreso', 'Los Pinos', 'Las Flores',
            'Santa Rita', 'El Paraíso', 'San Antonio', 'La Aurora', 'El Refugio',
            'Los Álamos', 'San Miguel', 'La Victoria', 'El Mirador', 'Las Rosas',
            'Santa María', 'Los Robles', 'San Juan', 'La Primavera', 'El Remanso',
            'Los Ceibos', 'Santa Elena', 'El Trébol', 'La Armonía', 'San Pedro',
            'Los Lapachos', 'La Querencia', 'El Descanso', 'Santa Clara', 'El Manantial'
        ];

        $contador = 1;
        $totalCreadas = 0;

        foreach ($productores as $productor) {
            // Cada productor tendrá 2-4 unidades productivas
            $cantidadUPs = rand(2, 4);
            
            for ($i = 0; $i < $cantidadUPs; $i++) {
                $nombreEstablecimiento = $nombresEstablecimientos[array_rand($nombresEstablecimientos)];
                $nombrePropio = $nombresPropios[array_rand($nombresPropios)];
                
                $unidad = UnidadProductiva::create([
                    'nombre' => "$nombreEstablecimiento $nombrePropio",
                    'identificador_local' => 'UP-' . str_pad($contador, 6, '0', STR_PAD_LEFT),
                    'tipo_identificador_id' => $tiposIdentificador->random()->id,
                    'activo' => rand(0, 10) > 1, // 90% activas
                    'completo' => rand(0, 10) > 2, // 80% completas
                    'superficie' => $this->generarSuperficie(),
                    'habita' => rand(0, 1) == 1,
                    'municipio_id' => $municipios->random()->id,
                    'paraje_id' => $parajes->random()->id,
                    'agua_humano_fuente_id' => $fuentesAgua->random()->id,
                    'agua_humano_en_casa' => rand(0, 1) == 1,
                    'agua_humano_distancia' => rand(0, 3000),
                    'agua_animal_fuente_id' => $fuentesAgua->random()->id,
                    'agua_animal_distancia' => rand(50, 5000),
                    'tipo_pasto_predominante_id' => $tiposPasto->random()->id,
                    'tipo_suelo_predominante_id' => $tiposSuelo->random()->id,
                    'forrajeras_predominante' => rand(0, 1) == 1,
                    'latitud' => $this->generarLatitud(),
                    'longitud' => $this->generarLongitud(),
                    'observaciones' => $this->generarObservaciones(),
                ]);

                // Asociar la unidad productiva al productor
                $productor->unidadesProductivas()->attach($unidad->id);
                
                $contador++;
                $totalCreadas++;
            }
        }

        $this->command->info("🎉 Creadas $totalCreadas unidades productivas para " . $productores->count() . " productores");
    }

    private function generarSuperficie(): float
    {
        // Superficies realistas entre 10 y 500 hectáreas
        return round(rand(1000, 50000) / 100, 2);
    }

    private function generarLatitud(): float
    {
        // Coordenadas de la región de Misiones, Argentina
        return round(-27.4 + (rand(-1000, 1000) / 10000), 6);
    }

    private function generarLongitud(): float
    {
        // Coordenadas de la región de Misiones, Argentina
        return round(-55.9 + (rand(-1000, 1000) / 10000), 6);
    }

    private function generarObservaciones(): ?string
    {
        $observaciones = [
            'Campo con pasturas mejoradas y acceso vehicular todo el año',
            'Establecimiento ganadero con infraestructura completa',
            'Chacra familiar con diversificación productiva',
            'Propiedad con monte nativo y pasturas implantadas',
            'Campo con sistema de riego y aguadas naturales',
            null, // Algunas sin observaciones
            'Establecimiento en proceso de mejoramiento de pasturas',
            'Campo con instalaciones de manejo y corrales',
            'Propiedad con buena disponibilidad de agua',
            'Establecimiento con potencial de crecimiento',
        ];

        return $observaciones[array_rand($observaciones)];
    }
}







