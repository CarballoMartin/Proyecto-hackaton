<?php

namespace Database\Factories;

use App\Models\DatoClimaticoCache;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatoClimaticoCache Factory extends Factory
{
    protected $model = DatoClimaticoCache::class;

    public function definition(): array
    {
        $temp = $this->faker->randomFloat(1, 10, 35);
        
        return [
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'fuente' => 'open_meteo',
            'temperatura_actual' => $temp,
            'velocidad_viento' => $this->faker->randomFloat(1, 5, 40),
            'codigo_clima' => $this->faker->numberBetween(0, 99),
            'temperaturas_max' => $this->generarTemperaturas($temp + 5, $temp + 10),
            'temperaturas_min' => $this->generarTemperaturas($temp - 10, $temp - 5),
            'precipitacion' => $this->generarPrecipitacion(),
            'probabilidad_lluvia' => [80, 70, 60, 50, 40, 30, 20],
            'viento_max' => [40, 35, 30, 25, 20, 15, 10],
            'fechas' => $this->generarFechas(),
            'datos_completos' => [],
            'fecha_consulta' => now()->subHours($this->faker->numberBetween(0, 23)),
        ];
    }

    private function generarTemperaturas($min, $max): array
    {
        return [
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
            $this->faker->randomFloat(1, $min, $max),
        ];
    }

    private function generarPrecipitacion(): array
    {
        return [
            $this->faker->randomFloat(1, 0, 20),
            $this->faker->randomFloat(1, 0, 20),
            $this->faker->randomFloat(1, 0, 15),
            $this->faker->randomFloat(1, 0, 10),
            $this->faker->randomFloat(1, 0, 5),
            $this->faker->randomFloat(1, 0, 5),
            $this->faker->randomFloat(1, 0, 5),
        ];
    }

    private function generarFechas(): array
    {
        $fechas = [];
        for ($i = 0; $i < 7; $i++) {
            $fechas[] = now()->addDays($i)->format('Y-m-d');
        }
        return $fechas;
    }

    /**
     * State: Condición de sequía
     */
    public function sequia(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 36,
            'temperaturas_max' => [36, 37, 38, 36, 37, 35, 36],
            'temperaturas_min' => [22, 23, 24, 22, 23, 21, 22],
            'precipitacion' => [0, 0, 0, 0, 0, 0, 0],
            'probabilidad_lluvia' => [0, 0, 0, 0, 5, 10, 15],
        ]);
    }

    /**
     * State: Tormenta inminente
     */
    public function tormenta(): static
    {
        return $this->state(fn (array $attributes) => [
            'precipitacion' => [60, 25, 10, 5, 2, 0, 0],
            'probabilidad_lluvia' => [95, 85, 70, 50, 30, 20, 10],
            'viento_max' => [70, 55, 40, 30, 25, 20, 15],
        ]);
    }

    /**
     * State: Estrés térmico
     */
    public function estreTermico(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 38,
            'temperaturas_max' => [38, 39, 37, 38, 37, 36, 35],
            'temperaturas_min' => [24, 25, 23, 24, 23, 22, 21],
        ]);
    }

    /**
     * State: Riesgo de helada
     */
    public function helada(): static
    {
        return $this->state(fn (array $attributes) => [
            'temperatura_actual' => 8,
            'temperaturas_max' => [12, 13, 14, 15, 16, 17, 18],
            'temperaturas_min' => [3, 4, 5, 6, 7, 9, 10],
        ]);
    }

    /**
     * State: Datos recientes (útil para tests)
     */
    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_consulta' => now()->subHours(2),
        ]);
    }

    /**
     * State: Datos antiguos (útil para tests de validación)
     */
    public function antiguo(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_consulta' => now()->subDays(2),
        ]);
    }
}

