<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CategoriaAnimal;
use App\Models\Especie;
class CategoriaAnimalFactory extends Factory
{
    protected $model = CategoriaAnimal::class;
    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'especie_id' => Especie::factory(),
        ];
    }
}
