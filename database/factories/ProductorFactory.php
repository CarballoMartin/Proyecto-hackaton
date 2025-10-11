<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Productor;

class ProductorFactory extends Factory
{
    protected $model = Productor::class;

    public function definition()
    {
        return [
            'usuario_id' => \App\Models\User::factory(),
            'nombre' => $this->faker->name,
            'dni' => $this->faker->unique()->numerify('########'),
            'cuil' => $this->faker->unique()->numerify('###########'),
            'activo' => true,
        ];
    }
}
