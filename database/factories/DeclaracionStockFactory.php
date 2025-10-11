<?php

namespace Database\Factories;

use App\Models\ConfiguracionActualizacion;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeclaracionStock>
 */
class DeclaracionStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Asegurarnos de que exista un periodo, si no, crearlo.
        $periodo = ConfiguracionActualizacion::first() ?? ConfiguracionActualizacion::factory()->create();

        return [
            'productor_id' => Productor::factory(),
            'periodo_id' => $periodo->id,
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'fecha_declaracion' => $this->faker->dateTimeThisYear(),
            'estado' => 'completada',
            'observaciones' => $this->faker->sentence(),
        ];
    }
}
