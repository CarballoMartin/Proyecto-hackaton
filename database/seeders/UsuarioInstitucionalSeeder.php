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
            'Instituto Tecnológico Agropecuario' => 'admin@instituto-tech.test',
            'Universidad Estatal de Agricultura' => 'admin@universidad-agro.test',
            'Ministerio de Agricultura y Ganadería' => 'admin@ministerio-agro.test',
            'Servicio Nacional Sanitario' => 'admin@servicio-sanitario.test',
            'Cooperativa Agrícola Regional' => 'admin@cooperativa-regional.test',
            'Asociación de Productores del Sur' => 'admin@asociacion-sur.test',
            'Fundación para el Desarrollo Rural' => 'admin@fundacionrural.test',
            'Cámara de Productores Ganaderos' => 'admin@camara-productores.test',
            'Instituto de Investigación Agropecuaria' => 'admin@instituto-investigacion.test',
            'Asociación de Técnicos Agropecuarios' => 'admin@asociacion-tecnicos.test',
        ];

        return $nombres[$institucion->nombre] ?? 'admin@' . strtolower(str_replace(' ', '', $institucion->nombre)) . '.test';
    }

    /**
     * Genera un nombre de admin basado en la institución
     */
    private function generarNombreAdmin(Institucion $institucion): string
    {
        $nombres = [
            'Instituto Tecnológico Agropecuario' => 'Admin Instituto Tecnológico',
            'Universidad Estatal de Agricultura' => 'Admin Universidad Agricultura',
            'Ministerio de Agricultura y Ganadería' => 'Admin Ministerio Agricultura',
            'Servicio Nacional Sanitario' => 'Admin Servicio Sanitario',
            'Cooperativa Agrícola Regional' => 'Admin Cooperativa Regional',
            'Asociación de Productores del Sur' => 'Admin Productores Sur',
            'Fundación para el Desarrollo Rural' => 'Admin Fundación Rural',
            'Cámara de Productores Ganaderos' => 'Admin Cámara Productores',
            'Instituto de Investigación Agropecuaria' => 'Admin Instituto Investigación',
            'Asociación de Técnicos Agropecuarios' => 'Admin Técnicos Agropecuarios',
        ];

        return $nombres[$institucion->nombre] ?? 'Admin ' . $institucion->nombre;
    }
}















