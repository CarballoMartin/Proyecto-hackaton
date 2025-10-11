<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Productor;
use Illuminate\Support\Facades\Hash;

class ProductorSeederMejorado extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productores = [
            [
                'name' => 'Juan Carlos Productor',
                'email' => 'juan.productor@test.com',
                'nombre' => 'Juan Carlos Productor',
                'dni' => '12345678',
                'municipio' => 'Aristóbulo del Valle',
                'paraje' => 'Picada Paraguay',
                'direccion' => 'Ruta 12 Km 25',
                'telefono' => '0297-1234567',
            ],
            [
                'name' => 'María Elena Productora',
                'email' => 'maria.productora@test.com',
                'nombre' => 'María Elena Productora',
                'dni' => '87654321',
                'municipio' => 'Candelaria',
                'paraje' => 'Paraje María',
                'direccion' => 'Ruta Provincial 5',
                'telefono' => '0297-7654321',
            ],
            [
                'name' => 'Carlos Roberto Ganadero',
                'email' => 'carlos.ganadero@test.com',
                'nombre' => 'Carlos Roberto Ganadero',
                'dni' => '11223344',
                'municipio' => 'Oberá',
                'paraje' => 'Colonia Alberdi',
                'direccion' => 'Ruta Nacional 14',
                'telefono' => '03755-123456',
            ],
            [
                'name' => 'Ana María Criadora',
                'email' => 'ana.criadora@test.com',
                'nombre' => 'Ana María Criadora',
                'dni' => '55667788',
                'municipio' => 'Posadas',
                'paraje' => 'Villa Cabello',
                'direccion' => 'Av. Corrientes 1234',
                'telefono' => '0376-456789',
            ],
            [
                'name' => 'Roberto Estanciero',
                'email' => 'roberto.estanciero@test.com',
                'nombre' => 'Roberto Estanciero',
                'dni' => '99887766',
                'municipio' => 'San Javier',
                'paraje' => 'Paraje San Javier',
                'direccion' => 'Ruta Provincial 2',
                'telefono' => '03755-987654',
            ],
            [
                'name' => 'Elena Productora',
                'email' => 'elena.productora@test.com',
                'nombre' => 'Elena Productora',
                'dni' => '33445566',
                'municipio' => 'Campo Ramón',
                'paraje' => 'Colonia Campo Ramón',
                'direccion' => 'Ruta 14 Km 45',
                'telefono' => '03755-456789',
            ],
            [
                'name' => 'Miguel Ganadero',
                'email' => 'miguel.ganadero@test.com',
                'nombre' => 'Miguel Ganadero',
                'dni' => '77889900',
                'municipio' => 'Guaraní',
                'paraje' => 'Paraje Guaraní',
                'direccion' => 'Ruta Provincial 7',
                'telefono' => '03755-789012',
            ],
            [
                'name' => 'Sofía Criadora',
                'email' => 'sofia.criadora@test.com',
                'nombre' => 'Sofía Criadora',
                'dni' => '44556677',
                'municipio' => 'Los Helechos',
                'paraje' => 'Colonia Los Helechos',
                'direccion' => 'Ruta 14 Km 78',
                'telefono' => '03755-234567',
            ],
            [
                'name' => 'Diego Productor',
                'email' => 'diego.productor@test.com',
                'nombre' => 'Diego Productor',
                'dni' => '88990011',
                'municipio' => 'Panambí',
                'paraje' => 'Paraje Panambí',
                'direccion' => 'Ruta Nacional 12',
                'telefono' => '03755-345678',
            ],
            [
                'name' => 'Laura Ganadera',
                'email' => 'laura.ganadera@test.com',
                'nombre' => 'Laura Ganadera',
                'dni' => '22334455',
                'municipio' => 'San Martín',
                'paraje' => 'Colonia San Martín',
                'direccion' => 'Ruta Provincial 1',
                'telefono' => '03755-456789',
            ]
        ];

        foreach ($productores as $productorData) {
            // Crear usuario
            $user = User::create([
                'name' => $productorData['name'],
                'email' => $productorData['email'],
                'password' => Hash::make('password'),
                'rol' => User::ROL_PRODUCTOR,
                'verificado' => true,
                'activo' => true,
            ]);

            // Crear registro de productor
            Productor::create([
                'usuario_id' => $user->id,
                'nombre' => $productorData['nombre'],
                'dni' => $productorData['dni'],
                'municipio' => $productorData['municipio'],
                'paraje' => $productorData['paraje'],
                'direccion' => $productorData['direccion'],
                'telefono' => $productorData['telefono'],
                'activo' => true,
            ]);
        }
    }
}




















