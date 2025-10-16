<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Productor;
use App\Models\Municipio;
use App\Models\Paraje;
use Illuminate\Support\Facades\Hash;

class ProductoresMasivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipios = Municipio::all();
        $parajes = Paraje::all();
        
        if ($municipios->isEmpty() || $parajes->isEmpty()) {
            $this->command->error('⚠️ Necesitas tener municipios y parajes en la BD primero');
            return;
        }

        $nombres = [
            'Juan', 'María', 'Carlos', 'Ana', 'José', 'Patricia', 'Luis', 'Carmen',
            'Roberto', 'Laura', 'Miguel', 'Silvia', 'Jorge', 'Rosa', 'Daniel',
            'Mónica', 'Fernando', 'Elena', 'Ricardo', 'Isabel', 'Raúl', 'Beatriz',
            'Gabriel', 'Adriana', 'Pablo', 'Claudia', 'Martín', 'Graciela', 'Diego',
            'Lucía', 'Andrés', 'Valeria', 'Héctor', 'Cristina', 'Sergio', 'Daniela'
        ];

        $apellidos = [
            'González', 'Rodríguez', 'Fernández', 'López', 'Martínez', 'García',
            'Pérez', 'Sánchez', 'Romero', 'Torres', 'Flores', 'Benítez',
            'Acosta', 'Medina', 'Silva', 'Castro', 'Morales', 'Ortiz',
            'Ruiz', 'Díaz', 'Vargas', 'Herrera', 'Mendoza', 'Ramírez',
            'Giménez', 'Cabrera', 'Ríos', 'Vera', 'Suárez', 'Molina'
        ];

        $tiposProductor = [
            'Productor', 'Productora', 'Ganadero', 'Ganadera', 
            'Criador', 'Criadora', 'Estanciero', 'Estanciera'
        ];

        // Crear 25 productores
        $cantidadProductores = 25;

        for ($i = 0; $i < $cantidadProductores; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)];
            $tipo = $tiposProductor[array_rand($tiposProductor)];
            $nombreCompleto = "$nombre $apellido";
            
            // Crear usuario
            $user = User::create([
                'name' => $nombreCompleto,
                'email' => strtolower(str_replace(' ', '.', $nombreCompleto)) . ($i + 100) . '@test.com',
                'password' => Hash::make('password'),
                'rol' => User::ROL_PRODUCTOR,
                'verificado' => rand(0, 10) > 0, // 90% verificados
                'activo' => rand(0, 10) > 1, // 90% activos
            ]);

            // Crear productor con datos realistas
            $municipio = $municipios->random();
            $parajesMunicipio = $parajes->where('municipio_id', $municipio->id);
            $paraje = $parajesMunicipio->isNotEmpty() ? $parajesMunicipio->random() : $parajes->random();
            
            Productor::create([
                'usuario_id' => $user->id,
                'nombre' => $nombreCompleto,
                'dni' => $this->generarDNI(),
                'municipio' => $municipio->nombre,
                'paraje' => $paraje->nombre,
                'direccion' => $this->generarDireccion(),
                'telefono' => $this->generarTelefono(),
                'activo' => $user->activo,
            ]);
        }

        $this->command->info("🎉 Creados $cantidadProductores productores con datos realistas");
    }

    private function generarDNI(): string
    {
        return str_pad(rand(10000000, 45000000), 8, '0', STR_PAD_LEFT);
    }

    private function generarTelefono(): string
    {
        $codigos = ['0376', '03755', '03757', '03751', '0297'];
        $codigo = $codigos[array_rand($codigos)];
        return $codigo . '-' . rand(100000, 999999);
    }

    private function generarDireccion(): string
    {
        $tiposRuta = ['Ruta Nacional', 'Ruta Provincial', 'Camino Rural', 'Avenida', 'Calle'];
        $tipo = $tiposRuta[array_rand($tiposRuta)];
        
        if (str_contains($tipo, 'Ruta')) {
            return "$tipo " . rand(1, 20) . " Km " . rand(1, 150);
        } else {
            $nombres = ['San Martín', 'Rivadavia', 'Belgrano', 'Sarmiento', 'Mitre', 'Colón'];
            return "$tipo " . $nombres[array_rand($nombres)] . " " . rand(100, 9999);
        }
    }
}

