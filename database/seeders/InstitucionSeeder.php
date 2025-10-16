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
            'nombre' => 'Instituto Tecnológico Agropecuario',
            'cuit' => '30-11111111-1',
            'contacto_email' => 'contacto@instituto-tech.test',
            'logo_path' => null,
            'localidad' => 'Capital Provincial',
            'provincia' => 'Provincia del Valle',
            'validada' => true,
        ]);

        Institucion::create([
            'nombre' => 'Universidad Estatal de Agricultura',
            'cuit' => '30-22222222-2',
            'contacto_email' => 'info@universidad-agro.test',
            'logo_path' => null,
            'localidad' => 'Capital Provincial',
            'provincia' => 'Provincia del Valle',
            'validada' => true,
        ]);

        // Instituciones No Aprobadas (Pendientes)
        Institucion::create([
            'nombre' => 'Cooperativa Agrícola Regional',
            'cuit' => '30-33333333-3',
            'contacto_email' => 'admin@cooperativa-regional.test',
            'logo_path' => null,
            'localidad' => 'Ciudad del Sur',
            'provincia' => 'Provincia del Valle',
            'validada' => false,
        ]);

        Institucion::create([
            'nombre' => 'Asociación de Productores del Sur',
            'cuit' => null, // Ejemplo de CUIT nulo
            'contacto_email' => 'secretaria@asociacion-sur.test',
            'logo_path' => null,
            'localidad' => 'Ciudad del Norte',
            'provincia' => 'Provincia del Valle',
            'validada' => false,
        ]);
    }
}
