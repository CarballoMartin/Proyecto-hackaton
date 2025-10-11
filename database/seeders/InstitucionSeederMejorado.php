<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institucion;
use Illuminate\Support\Facades\Schema;

class InstitucionSeederMejorado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate the table to start from a clean state
        Institucion::truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $instituciones = [
            // Instituciones Validadas
            [
                'nombre' => 'INTA - Instituto Nacional de Tecnología Agropecuaria',
                'cuit' => '30-54668326-5',
                'contacto_email' => 'contacto@inta.gob.ar',
                'logo_path' => 'logos/inta1.png',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => true,
                'descripcion' => 'Organismo público dedicado a la investigación y desarrollo agropecuario',
            ],
            [
                'nombre' => 'Universidad Nacional de Misiones',
                'cuit' => '30-67890123-4',
                'contacto_email' => 'info@unam.edu.ar',
                'logo_path' => 'logos/unam.jpg',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => true,
                'descripcion' => 'Universidad pública con carreras relacionadas al sector agropecuario',
            ],
            [
                'nombre' => 'Ministerio del Agro y la Producción de Misiones',
                'cuit' => '30-12345678-9',
                'contacto_email' => 'info@agro.misiones.gov.ar',
                'logo_path' => 'logos/municipios/candelaria.png',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => true,
                'descripcion' => 'Organismo gubernamental provincial del sector agropecuario',
            ],
            [
                'nombre' => 'SENASA - Servicio Nacional de Sanidad y Calidad Agroalimentaria',
                'cuit' => '30-98765432-1',
                'contacto_email' => 'misiones@senasa.gob.ar',
                'logo_path' => 'logos/logoovinos.png',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => true,
                'descripcion' => 'Organismo nacional de sanidad animal y vegetal',
            ],

            // Instituciones Pendientes
            [
                'nombre' => 'Cooperativa Agrícola de Misiones',
                'cuit' => '30-11111111-1',
                'contacto_email' => 'admin@coopmisiones.com.ar',
                'logo_path' => 'logos/todostenemos.jpg',
                'localidad' => 'Oberá',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Cooperativa de productores agropecuarios de la zona centro',
            ],
            [
                'nombre' => 'Asociación de Ganaderos del Sur',
                'cuit' => null,
                'contacto_email' => 'secretaria@ganaderossur.org',
                'logo_path' => 'logos/Logo SRM.jpg',
                'localidad' => 'Apóstoles',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Asociación de productores ganaderos del sur de Misiones',
            ],
            [
                'nombre' => 'Fundación para el Desarrollo Rural',
                'cuit' => '30-22222222-2',
                'contacto_email' => 'info@fundacionrural.org',
                'logo_path' => 'logos/efa-sancristobal.jpg',
                'localidad' => 'Candelaria',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Fundación dedicada al desarrollo rural y agropecuario',
            ],
            [
                'nombre' => 'Cámara de Productores Ovino-Caprinos',
                'cuit' => '30-33333333-3',
                'contacto_email' => 'presidencia@camaraovinocaprina.com',
                'logo_path' => 'logos/logoovinos.png',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Cámara empresarial del sector ovino-caprino',
            ],
            [
                'nombre' => 'Instituto de Investigación Agropecuaria Regional',
                'cuit' => '30-44444444-4',
                'contacto_email' => 'investigacion@iiar.org.ar',
                'logo_path' => 'logos/efa-sancristobal.jpg',
                'localidad' => 'Oberá',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Instituto privado de investigación agropecuaria',
            ],
            [
                'nombre' => 'Asociación de Técnicos Agropecuarios',
                'cuit' => '30-55555555-5',
                'contacto_email' => 'secretaria@atecnicos.com',
                'logo_path' => 'logos/efa-sancristobal.jpg',
                'localidad' => 'Posadas',
                'provincia' => 'Misiones',
                'validada' => false,
                'descripcion' => 'Asociación de profesionales del sector agropecuario',
            ]
        ];

        foreach ($instituciones as $institucion) {
            Institucion::create($institucion);
        }
    }
}




