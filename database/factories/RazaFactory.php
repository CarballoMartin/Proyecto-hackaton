<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Raza;
use App\Models\Especie;
class RazaFactory extends Factory
{
    protected $model = Raza::class;
    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'especie_id' => Especie::factory(),
        ];
    }
}
