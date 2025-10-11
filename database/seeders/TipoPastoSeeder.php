<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoPasto;

class TipoPastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPasto::create(['nombre' => 'Pastizal natural']);
        TipoPasto::create(['nombre' => 'Monte']);
        TipoPasto::create(['nombre' => 'Pastura implantada']);
        TipoPasto::create(['nombre' => 'Rastrojo']);
        TipoPasto::create(['nombre' => 'Campo natural']);
        TipoPasto::create(['nombre' => 'Pradera']);
        TipoPasto::create(['nombre' => 'Matorral']);
        TipoPasto::create(['nombre' => 'Otro']);
    }
}
