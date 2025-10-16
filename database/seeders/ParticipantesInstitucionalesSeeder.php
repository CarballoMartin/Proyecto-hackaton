<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institucion;
use App\Models\User;
use App\Models\InstitucionalParticipante;
use Illuminate\Support\Facades\Hash;

class ParticipantesInstitucionalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instituciones = Institucion::all();
        
        $cargos = [
            'Director',
            'Subdirector',
            'Técnico Agropecuario',
            'Veterinario',
            'Ingeniero Agrónomo',
            'Administrativo',
            'Asesor Técnico',
            'Coordinador de Proyectos',
            'Especialista en Ganadería',
            'Encargado de Extensión',
        ];

        $nombres = [
            'Carlos', 'María', 'José', 'Ana', 'Luis', 'Patricia', 'Roberto', 'Laura',
            'Miguel', 'Carmen', 'Jorge', 'Silvia', 'Daniel', 'Rosa', 'Fernando',
            'Mónica', 'Ricardo', 'Elena', 'Alberto', 'Isabel', 'Raúl', 'Beatriz',
            'Gabriel', 'Adriana', 'Pablo', 'Claudia', 'Martín', 'Graciela'
        ];

        $apellidos = [
            'González', 'Rodríguez', 'Fernández', 'López', 'Martínez', 'García',
            'Pérez', 'Sánchez', 'Romero', 'Torres', 'Flores', 'Benítez',
            'Acosta', 'Medina', 'Silva', 'Castro', 'Morales', 'Ortiz',
            'Ruiz', 'Díaz', 'Vargas', 'Herrera', 'Mendoza', 'Ramírez'
        ];

        foreach ($instituciones as $institucion) {
            // Cada institución tendrá entre 3 y 8 participantes adicionales
            $cantidadParticipantes = rand(3, 8);
            
            for ($i = 0; $i < $cantidadParticipantes; $i++) {
                $nombre = $nombres[array_rand($nombres)];
                $apellido = $apellidos[array_rand($apellidos)];
                $nombreCompleto = "$nombre $apellido";
                
                // Crear usuario
                $user = User::create([
                    'name' => $nombreCompleto,
                    'email' => strtolower(str_replace(' ', '.', $nombreCompleto)) . $i . '@' . 
                               str_replace(' ', '', strtolower($institucion->nombre)) . '.test',
                    'password' => Hash::make('password'),
                    'rol' => User::ROL_INSTITUCIONAL,
                    'verificado' => true,
                    'activo' => rand(0, 10) > 1, // 90% activos, 10% inactivos
                ]);

                // Roles permitidos: admin, tecnico, investigador, educativo
                $rolesPermitidos = ['tecnico', 'investigador', 'educativo'];
                if ($i == 0) {
                    $rolesPermitidos[] = 'admin'; // El primero puede ser admin
                }
                
                // Crear participante institucional
                InstitucionalParticipante::create([
                    'usuario_id' => $user->id,
                    'institucion_id' => $institucion->id,
                    'rol' => $rolesPermitidos[array_rand($rolesPermitidos)],
                    'cargo' => $cargos[array_rand($cargos)],
                    'activo' => $user->activo,
                    'fecha_ingreso' => now()->subMonths(rand(1, 36))->format('Y-m-d'),
                ]);
            }
            
            $this->command->info("✅ Creados $cantidadParticipantes participantes para {$institucion->nombre}");
        }

        $totalParticipantes = InstitucionalParticipante::count();
        $this->command->info("🎉 Total de participantes institucionales creados: $totalParticipantes");
    }
}

