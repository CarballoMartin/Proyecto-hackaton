<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Raza;
use App\Models\Especie;

class RazaSeeder extends Seeder
{
    public function run(): void
    {
        // ← Obtiene las especies
        $ovino = Especie::where('nombre', 'Ovino')->first();
        $caprino = Especie::where('nombre', 'Caprino')->first();
        $bovino = Especie::where('nombre', 'Bovino')->first();

        // ← Razas de Ovinos
        if ($ovino) {
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Merino']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Corriedale']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Romney Marsh']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Lincoln']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Hampshire Down']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Suffolk']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Criollo']);
            Raza::create(['especie_id' => $ovino->id, 'nombre' => 'Otro']);
        }

        // ← Razas de Caprinos
        if ($caprino) {
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Angora']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Saanen']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Toggenburg']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Alpina']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Nubia']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Criollo']);
            Raza::create(['especie_id' => $caprino->id, 'nombre' => 'Otro']);
        }

        // ← Razas de Bovinos
        if ($bovino) {
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Holstein']);
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Jersey']);
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Hereford']);
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Angus']);
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Criollo']);
            Raza::create(['especie_id' => $bovino->id, 'nombre' => 'Otro']);
        }
    }
}
