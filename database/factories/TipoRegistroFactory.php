<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TipoRegistro;
class TipoRegistroFactory extends Factory
{
    protected $model = TipoRegistro::class;
    public function definition()
    {
        return ['nombre' => $this->faker->word];
    }
}
