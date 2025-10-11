<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParajesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parajesPorMunicipio = [

            // Oberá
            'Oberá' => [
                'Colonia Chapa',
                'Colonia Florida',
                'Colonia Yabebirí',
                'Arroyo Fedor',
                'Barra Bonita',
                'El Salto',
                'Paraje Itá Chica',
                'Picada Finlandesa',
                'Picada Guaraypo'
            ],

            // Colonia Alberdi
            'Colonia Alberdi' => [
                'Furtz (Zona 309)',
                'Picaza',
                'Don Carlos (Soberbio Oeste)',
                'Yerbal Viejo',
                'Ex Resper',
                'Ruta 6',
                'Irupé',
                'Pirilén',
                'La Cancha Rana',
                'La Franja',
                'La Recta'
            ],

            // Cerro Azul
            'Cerro Azul' => [
                'Arroyo Tomás',
                'Bernardino Rivadavia',
                'Campiñas',
                'Güemes',
                'La Torre',
                'Picada Belgrano',
                'Picada Polaca',
                'Taranco Chico',
                'Taranco Grande',
                'Villa INTA'
            ],

            // San Javier
            'San Javier' => [
                'Paraje Paso de la Barca',
                'Paraje Corredera',
                'Paraje Tres Esquinas',
                'Paraje El Guerrero',
                'Paraje Procediño',
                'Paraje Santa Irene',
                'Paraje Guatambú',
                'Paraje Cinco Mil'
            ]
        ];

        foreach ($parajesPorMunicipio as $municipioNombre => $parajes) {
            $municipioId = DB::table('municipios')->where('nombre', $municipioNombre)->value('id');

            if ($municipioId) {
                foreach ($parajes as $parajeNombre) {
                    DB::table('parajes')->updateOrInsert(
                        [
                            'municipio_id' => $municipioId,
                            'nombre' => $parajeNombre
                        ],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
