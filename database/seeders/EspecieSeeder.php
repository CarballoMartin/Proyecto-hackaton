<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Especie;

class EspecieSeeder extends Seeder
{
    public function run(): void
    {
        // ← Crea especies comunes de ovinos y caprinos
        Especie::create(['nombre' => 'Ovino']);
        Especie::create(['nombre' => 'Caprino']);
        Especie::create(['nombre' => 'Bovino']);
        Especie::create(['nombre' => 'Porcino']);
        Especie::create(['nombre' => 'Equino']);
    }
}
