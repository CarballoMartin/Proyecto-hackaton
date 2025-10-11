<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\StockAnimal;
use App\Models\Especie;
use App\Models\Raza;
use App\Models\CategoriaAnimal;
use App\Models\TipoRegistro;
use App\Models\DeclaracionStock;
use App\Models\ConfiguracionActualizacion;

class StockAnimalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get prerequisite data with robust checks
        $productor = Productor::first();
        if (!$productor) {
            $this->command->error('StockAnimalSeeder: No se encontró ningún Productor. Ejecuta ProductorSeeder primero.');
            return;
        }

        $periodo = ConfiguracionActualizacion::first();
        if (!$periodo) {
            $this->command->error('StockAnimalSeeder: No se encontró ninguna Configuración de Actualización. Ejecuta ConfiguracionActualizacionSeeder primero.');
            return;
        }

        $tipoRegistro = TipoRegistro::where('nombre', 'Declaración Inicial')->first();
        if (!$tipoRegistro) {
            $this->command->error('StockAnimalSeeder: No se encontró el Tipo de Registro "Declaración Inicial". Revisa TipoRegistroSeeder.');
            return;
        }

        $unidadProductiva = $productor->unidadesProductivas()->first();
        if (!$unidadProductiva) {
            $this->command->error('StockAnimalSeeder: El productor de prueba no tiene Unidades Productivas. Ejecuta UnidadProductivaSeeder primero.');
            return;
        }

        // 2. Create a single Declaration for the first producer
        $declaracion = DeclaracionStock::create([
            'productor_id' => $productor->id,
            'periodo_id' => $periodo->id,
            'unidad_productiva_id' => $unidadProductiva->id,
            'fecha_declaracion' => now(),
            'estado' => 'completada',
            'observaciones' => 'Declaración inicial generada por el seeder.',
        ]);

        // 3. Get catalog data for animals, making it resilient
        $especie1 = Especie::first();
        $especie2 = Especie::skip(1)->first() ?? $especie1;
        $categoria1 = CategoriaAnimal::first();
        $categoria2 = CategoriaAnimal::skip(1)->first() ?? $categoria1;
        $raza1 = Raza::first();
        $raza2 = Raza::skip(1)->first() ?? $raza1;

        if (!$especie1 || !$categoria1 || !$raza1) {
            $this->command->error('StockAnimalSeeder: No se encontraron suficientes datos en los catálogos (Especie, Categoria, Raza). Ejecuta sus seeders primero.');
            return;
        }

        // 4. Create stock animals associated with the declaration
        $stockData = [
            [
                'especie_id' => $especie1->id,
                'categoria_id' => $categoria1->id,
                'raza_id' => $raza1->id,
                'cantidad' => 150,
            ],
            [
                'especie_id' => $especie2->id,
                'categoria_id' => $categoria2->id,
                'raza_id' => $raza2->id,
                'cantidad' => 80,
            ],
        ];

        foreach ($stockData as $data) {
            StockAnimal::create(array_merge($data, [
                'declaracion_id' => $declaracion->id,
                'unidad_productiva_id' => $unidadProductiva->id,
                'tipo_registro_id' => $tipoRegistro->id,
                'fecha_registro' => now(),
                'observaciones' => 'Registro de stock generado por seeder.',
            ]));
        }
    }
}