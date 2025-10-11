<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UnidadProductiva;

class UnidadProductivaFactory extends Factory
{
    protected $model = UnidadProductiva::class;

    public function definition()
    {
        return [
            // 'productor_id' is not a direct attribute, it is a relationship
            'nombre' => 'UP ' . $this->faker->word,
            'identificador_local' => $this->faker->unique()->numerify('##.###.#.#####/##'),
            'activo' => true,
            'completo' => true,
        ];
    }
}
