<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConfiguracionActualizacion>
 */
class ConfiguracionActualizacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'frecuencia_dias' => 180,
            'ultima_actualizacion' => now()->subMonths(6),
            'proxima_actualizacion' => now(),
            'activo' => true,
            'fecha_configuracion' => now(),
        ];
    }
}
