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
                'nombre' => 'Instituto Tecnológico Agropecuario',
                'cuit' => '30-11111111-1',
                'contacto_email' => 'contacto@instituto-tech.test',
                'logo_path' => 'logos/logo-instituciones.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => true,
                'descripcion' => 'Organismo público dedicado a la investigación y desarrollo agropecuario',
            ],
            [
                'nombre' => 'Universidad Estatal de Agricultura',
                'cuit' => '30-22222222-2',
                'contacto_email' => 'info@universidad-agro.test',
                'logo_path' => 'logos/logo-universidad.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => true,
                'descripcion' => 'Universidad pública con carreras relacionadas al sector agropecuario',
            ],
            [
                'nombre' => 'Ministerio de Agricultura y Ganadería',
                'cuit' => '30-33333333-3',
                'contacto_email' => 'info@ministerio-agro.test',
                'logo_path' => 'logos/logo-ministerio.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => true,
                'descripcion' => 'Organismo gubernamental provincial del sector agropecuario',
            ],
            [
                'nombre' => 'Servicio Nacional Sanitario',
                'cuit' => '30-44444444-4',
                'contacto_email' => 'contacto@servicio-sanitario.test',
                'logo_path' => 'logos/logo-placeholder.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => true,
                'descripcion' => 'Organismo nacional de sanidad animal y vegetal',
            ],

            // Instituciones Pendientes
            [
                'nombre' => 'Cooperativa Agrícola Regional',
                'cuit' => '30-55555555-5',
                'contacto_email' => 'admin@cooperativa-regional.test',
                'logo_path' => 'logos/logo-cooperativa.svg',
                'localidad' => 'Ciudad del Sur',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Cooperativa de productores agropecuarios de la región',
            ],
            [
                'nombre' => 'Asociación de Productores del Sur',
                'cuit' => null,
                'contacto_email' => 'secretaria@asociacion-sur.test',
                'logo_path' => 'logos/logo-placeholder.svg',
                'localidad' => 'Ciudad del Norte',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Asociación de productores ganaderos de la región sur',
            ],
            [
                'nombre' => 'Fundación para el Desarrollo Rural',
                'cuit' => '30-66666666-6',
                'contacto_email' => 'info@fundacionrural.test',
                'logo_path' => 'logos/logo-instituciones.svg',
                'localidad' => 'Ciudad del Este',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Fundación dedicada al desarrollo rural y agropecuario',
            ],
            [
                'nombre' => 'Cámara de Productores Ganaderos',
                'cuit' => '30-77777777-7',
                'contacto_email' => 'presidencia@camara-productores.test',
                'logo_path' => 'logos/logo-placeholder.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Cámara empresarial del sector ganadero',
            ],
            [
                'nombre' => 'Instituto de Investigación Agropecuaria',
                'cuit' => '30-88888888-8',
                'contacto_email' => 'investigacion@instituto-investigacion.test',
                'logo_path' => 'logos/logo-instituciones.svg',
                'localidad' => 'Ciudad del Sur',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Instituto privado de investigación agropecuaria',
            ],
            [
                'nombre' => 'Asociación de Técnicos Agropecuarios',
                'cuit' => '30-99999999-9',
                'contacto_email' => 'secretaria@asociacion-tecnicos.test',
                'logo_path' => 'logos/logo-placeholder.svg',
                'localidad' => 'Capital Provincial',
                'provincia' => 'Provincia del Valle',
                'validada' => false,
                'descripcion' => 'Asociación de profesionales del sector agropecuario',
            ]
        ];

        foreach ($instituciones as $institucion) {
            Institucion::create($institucion);
        }
    }
}
