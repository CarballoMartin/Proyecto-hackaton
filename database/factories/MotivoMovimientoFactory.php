<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MotivoMovimiento;

class MotivoMovimientoFactory extends Factory
{
    protected $model = MotivoMovimiento::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'tipo' => $this->faker->randomElement(['alta', 'baja']),
        ];
    }
}
