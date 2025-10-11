<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institucion;

use Illuminate\Support\Facades\Schema;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate the table to start from a clean state
        Institucion::truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // Instituciones Aprobadas
        Institucion::create([
            'nombre' => 'INTA',
            'cuit' => '30-54668326-5',
            'contacto_email' => 'contacto@inta.gob.ar',
            'logo_path' => 'logos/inta1.png',
            'localidad' => 'Posadas',
            'provincia' => 'Misiones',
            'validada' => true,
        ]);

        Institucion::create([
            'nombre' => 'Universidad Nacional de Misiones',
            'cuit' => '30-67890123-4',
            'contacto_email' => 'info@unam.edu.ar',
            'logo_path' => 'logos/unam.jpg',
            'localidad' => 'Posadas',
            'provincia' => 'Misiones',
            'validada' => true,
        ]);

        // Instituciones No Aprobadas (Pendientes)
        Institucion::create([
            'nombre' => 'Cooperativa Agrícola de Misiones',
            'cuit' => '30-12345678-9',
            'contacto_email' => 'admin@coopmisiones.com.ar',
            'logo_path' => 'logos/todostenemos.jpg',
            'localidad' => 'Oberá',
            'provincia' => 'Misiones',
            'validada' => false,
        ]);

        Institucion::create([
            'nombre' => 'Asociación de Ganaderos del Sur',
            'cuit' => null, // Ejemplo de CUIT nulo
            'contacto_email' => 'secretaria@ganaderossur.org',
            'logo_path' => 'logos/Logo SRM.jpg',
            'localidad' => 'Apóstoles',
            'provincia' => 'Misiones',
            'validada' => false,
        ]);
    }
}
