<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoSuelo;

class TipoSueloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoSuelo::create(['nombre' => 'Arenoso']);
        TipoSuelo::create(['nombre' => 'Arcilloso']);
        TipoSuelo::create(['nombre' => 'Limoso']);
        TipoSuelo::create(['nombre' => 'Franco']);
        TipoSuelo::create(['nombre' => 'Pedregoso']);
        TipoSuelo::create(['nombre' => 'Rocoso']);
        TipoSuelo::create(['nombre' => 'Salino']);
        TipoSuelo::create(['nombre' => 'Otro']);
    }
}
