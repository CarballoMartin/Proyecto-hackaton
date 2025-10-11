<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaAnimal;
use App\Models\Especie;

class CategoriaAnimalSeeder extends Seeder
{
    public function run(): void
    {
        // ← Obtiene las especies
        $ovino = Especie::where('nombre', 'Ovino')->first();
        $caprino = Especie::where('nombre', 'Caprino')->first();
        $bovino = Especie::where('nombre', 'Bovino')->first();

        // ← Categorías de Ovinos
        if ($ovino) {
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Cordero']);
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Oveja']);
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Carnero']);
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Borrego']);
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Capón']);
            CategoriaAnimal::create(['especie_id' => $ovino->id, 'nombre' => 'Otro']);
        }

        // ← Categorías de Caprinos
        if ($caprino) {
            CategoriaAnimal::create(['especie_id' => $caprino->id, 'nombre' => 'Cabra']);
            CategoriaAnimal::create(['especie_id' => $caprino->id, 'nombre' => 'Chivo']);
            CategoriaAnimal::create(['especie_id' => $caprino->id, 'nombre' => 'Cabrito']);
            CategoriaAnimal::create(['especie_id' => $caprino->id, 'nombre' => 'Macho reproductor']);
            CategoriaAnimal::create(['especie_id' => $caprino->id, 'nombre' => 'Otro']);
        }

        // ← Categorías de Bovinos
        if ($bovino) {
            CategoriaAnimal::create(['especie_id' => $bovino->id, 'nombre' => 'Vaca']);
            CategoriaAnimal::create(['especie_id' => $bovino->id, 'nombre' => 'Toro']);
            CategoriaAnimal::create(['especie_id' => $bovino->id, 'nombre' => 'Ternero']);
            CategoriaAnimal::create(['especie_id' => $bovino->id, 'nombre' => 'Novillo']);
            CategoriaAnimal::create(['especie_id' => $bovino->id, 'nombre' => 'Otro']);
        }
    }
}
