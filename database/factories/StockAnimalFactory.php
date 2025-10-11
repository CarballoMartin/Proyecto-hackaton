<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StockAnimal;
use App\Models\UnidadProductiva;
use App\Models\MotivoMovimiento;
use App\Models\Especie;
use App\Models\CategoriaAnimal;
use App\Models\Raza;
use App\Models\TipoRegistro;
use App\Models\DeclaracionStock;

class StockAnimalFactory extends Factory
{
    protected $model = StockAnimal::class;

    public function definition()
    {
        return [
            'declaracion_id' => DeclaracionStock::factory(),
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'especie_id' => Especie::factory(),
            'categoria_id' => CategoriaAnimal::factory(),
            'raza_id' => Raza::factory(),
            'tipo_registro_id' => TipoRegistro::factory(),
            'cantidad' => $this->faker->numberBetween(1, 50),
            'fecha_registro' => $this->faker->dateTimeThisYear(),
        ];
    }
}