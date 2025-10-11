<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Especie;
class EspecieFactory extends Factory
{
    protected $model = Especie::class;
    public function definition()
    {
        return ['nombre' => 'Ovino'];
    }
}