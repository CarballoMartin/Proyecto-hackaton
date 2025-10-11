<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FuenteAgua;

class FuenteAguaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FuenteAgua::create(['nombre' => 'Pozo']);
        FuenteAgua::create(['nombre' => 'Arroyo']);
        FuenteAgua::create(['nombre' => 'Río']);
        FuenteAgua::create(['nombre' => 'Laguna']);
        FuenteAgua::create(['nombre' => 'Agua de red']);
        FuenteAgua::create(['nombre' => 'Tanque de agua']);
        FuenteAgua::create(['nombre' => 'Aljibe']);
        FuenteAgua::create(['nombre' => 'Canal']);
        FuenteAgua::create(['nombre' => 'Vertiente']);
        FuenteAgua::create(['nombre' => 'Otro']);
    }
}
