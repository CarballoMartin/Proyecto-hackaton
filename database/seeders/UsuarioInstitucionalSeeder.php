<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institucion;
use App\Models\InstitucionalParticipante;
use Illuminate\Support\Facades\Hash;

class UsuarioInstitucionalSeeder extends Seeder
{
    /**
     * Crea usuarios institucionales para cada institución existente
     */
    public function run(): void
    {
        // Obtener todas las instituciones
        $instituciones = Institucion::all();
        
        if ($instituciones->isEmpty()) {
            $this->command->error('No se encontraron instituciones. Ejecuta InstitucionSeederMejorado primero.');
            return;
        }

        foreach ($instituciones as $institucion) {
            // Generar email y nombre basado en la institución
            $email = $this->generarEmailInstitucional($institucion);
            $nombre = $this->generarNombreAdmin($institucion);
            
            // Verificar si ya existe el usuario
            $usuarioExistente = User::where('email', $email)->first();
            if ($usuarioExistente) {
                $this->command->info("Usuario ya existe para {$institucion->nombre}: {$email}");
                continue;
            }

            // Crear usuario institucional
            $usuario = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make('password123'), // Contraseña común para pruebas
                'rol' => User::ROL_INSTITUCIONAL,
                'verificado' => true,
                'activo' => true,
            ]);

            // Vincular usuario con la institución como admin
            InstitucionalParticipante::create([
                'usuario_id' => $usuario->id,
                'institucion_id' => $institucion->id,
                'rol' => 'admin',
                'activo' => true,
            ]);

            $this->command->info("✅ Creado usuario para {$institucion->nombre}: {$email}");
        }

        $this->command->info("🎉 Se crearon usuarios institucionales para " . $instituciones->count() . " instituciones.");
    }

    /**
     * Genera un email institucional basado en el nombre de la institución
     */
    private function generarEmailInstitucional(Institucion $institucion): string
    {
        $nombres = [
            'INTA - Instituto Nacional de Tecnología Agropecuaria' => 'admin@inta.misiones.test',
            'Universidad Nacional de Misiones' => 'admin@unam.test',
            'Ministerio del Agro y la Producción de Misiones' => 'admin@agro.misiones.test',
            'SENASA - Servicio Nacional de Sanidad y Calidad Agroalimentaria' => 'admin@senasa.misiones.test',
            'Cooperativa Agrícola de Misiones' => 'admin@coopmisiones.test',
            'Asociación de Ganaderos del Sur' => 'admin@ganaderossur.test',
            'Fundación para el Desarrollo Rural' => 'admin@fundacionrural.test',
            'Cámara de Productores Ovino-Caprinos' => 'admin@camaraovinocaprina.test',
            'Instituto de Investigación Agropecuaria Regional' => 'admin@iiar.test',
            'Asociación de Técnicos Agropecuarios' => 'admin@atecnicos.test',
        ];

        return $nombres[$institucion->nombre] ?? 'admin@' . strtolower(str_replace(' ', '', $institucion->nombre)) . '.test';
    }

    /**
     * Genera un nombre de admin basado en la institución
     */
    private function generarNombreAdmin(Institucion $institucion): string
    {
        $nombres = [
            'INTA - Instituto Nacional de Tecnología Agropecuaria' => 'Admin INTA Misiones',
            'Universidad Nacional de Misiones' => 'Admin UNaM',
            'Ministerio del Agro y la Producción de Misiones' => 'Admin Ministerio Agro',
            'SENASA - Servicio Nacional de Sanidad y Calidad Agroalimentaria' => 'Admin SENASA Misiones',
            'Cooperativa Agrícola de Misiones' => 'Admin Cooperativa Agrícola',
            'Asociación de Ganaderos del Sur' => 'Admin Ganaderos del Sur',
            'Fundación para el Desarrollo Rural' => 'Admin Fundación Rural',
            'Cámara de Productores Ovino-Caprinos' => 'Admin Cámara Ovino-Caprinos',
            'Instituto de Investigación Agropecuaria Regional' => 'Admin IIAR',
            'Asociación de Técnicos Agropecuarios' => 'Admin ATA',
        ];

        return $nombres[$institucion->nombre] ?? 'Admin ' . $institucion->nombre;
    }
}















