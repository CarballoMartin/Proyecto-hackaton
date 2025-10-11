<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoRegistro;
use Illuminate\Support\Facades\Schema;

class TipoRegistroSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        TipoRegistro::truncate();
        Schema::enableForeignKeyConstraints();

        $tipos = [
            ['nombre' => 'Declaración Inicial'],
            ['nombre' => 'Declaración Periódica'],
            ['nombre' => 'Corrección'],
        ];

        foreach ($tipos as $tipo) {
            TipoRegistro::create($tipo);
        }
    }
}
