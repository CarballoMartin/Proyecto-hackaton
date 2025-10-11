<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CondicionTenencia;

class CondicionTenenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CondicionTenencia::create([
            'nombre' => 'Propietario',
            'descripcion' => 'Productor que posee la tierra donde trabaja.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Arrendatario',
            'descripcion' => 'Productor que arrienda la tierra para trabajarla.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Comodato',
            'descripcion' => 'Productor que trabaja la tierra en comodato.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Titulo en trámite',
            'descripcion' => 'Productor que está en proceso de obtener el título de propiedad.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Con permiso/tierra fiscal',
            'descripcion' => 'Productor que trabaja en tierras fiscales con permiso.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Indefinido/no sabe',
            'descripcion' => 'Productor cuya condición de tenencia no está definida.',
        ]);

        CondicionTenencia::create([
            'nombre' => 'Otro',
            'descripcion' => 'Otra condición de tenencia no especificada.',
        ]);
    }
}
