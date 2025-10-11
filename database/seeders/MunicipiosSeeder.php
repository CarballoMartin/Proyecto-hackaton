<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipios = [
            // Departamento de Leandro N. Alem
            'Almafuerte',
            'Arroyo del Medio',
            'Caá Yarí',
            'Cerro Azul',
            'Dos Arroyos',
            'Gobernador López',
            'Leandro N. Alem',
            'Olegario Víctor Andrade',

            // Departamento de Oberá
            'Campo Ramón',
            'Campo Viera',
            'Colonia Alberdi',
            'General Alvear',
            'Guaraní',
            'Los Helechos',
            'Oberá',
            'Panambí',
            'San Martín',

            // Departamento de San Javier
            'Florentino Ameghino',
            'Itacaruaré',
            'Mojón Grande',
            'San Javier',

            // Departamento Capital
            'Posadas'
        ];

        foreach ($municipios as $municipio) {
            DB::table('municipios')->updateOrInsert(
                ['nombre' => $municipio],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
