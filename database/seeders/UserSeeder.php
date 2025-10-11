<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'rol' => 'superadmin',
            'verificado' => true,
            'activo' => true,
        ]);

        User::create([
            'name' => 'Admin Institucional',
            'email' => 'institucional@test.com',
            'password' => Hash::make('password'),
            'rol' => 'institucional',
            'verificado' => true,
            'activo' => true,
        ]);

        // Nota: Los productores ahora se crean en ProductorSeeder.php
        // para asegurar que tengan tanto usuario como registro de productor
    }
}
