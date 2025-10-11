<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Productor;
use App\Models\Campo;
use Illuminate\Support\Facades\Hash;

class ProductorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario productor
        $user1 = User::create([
            'name' => 'Juan Productor',
            'email' => 'productor@test.com',
            'password' => Hash::make('password'),
            'rol' => User::ROL_PRODUCTOR,
            'verificado' => true,
            'activo' => true,
        ]);

        // Crear registro de productor asociado al usuario
        $productor = Productor::create([
            'usuario_id' => $user1->id,
            'nombre' => 'Juan Productor',
            'dni' => '12345678',
            'municipio' => 'Aristóbulo del Valle',
            'paraje' => 'Picada Paraguay',
            'direccion' => 'Ruta 12 Km 25',
            'telefono' => '0297-1234567',
            'activo' => true,
        ]);
    
        // Crear un segundo productor para pruebas
        $user2 = User::create([
            'name' => 'María Productora',
            'email' => 'productora@test.com',
            'password' => Hash::make('password'),
            'rol' => User::ROL_PRODUCTOR,
            'verificado' => true,
            'activo' => true,
        ]);

        $productor2 = Productor::create([
            'usuario_id' => $user2->id,
            'nombre' => 'María Productora',
            'dni' => '87654321',
            'municipio' => 'Candelaria',
            'paraje' => 'Paraje María',
            'direccion' => 'Ruta Provincial 5',
            'telefono' => '0297-7654321',
            'activo' => true,
        ]);
    }
}
