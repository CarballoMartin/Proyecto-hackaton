<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoIdentificador;

class TipoIdentificadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoIdentificador::create(['nombre' => 'RNSPA']);
    }
}