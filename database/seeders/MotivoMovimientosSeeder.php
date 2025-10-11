<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotivoMovimientosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('motivo_movimientos')->insert([
            // Altas
            ['nombre' => 'Nacimiento', 'tipo' => 'alta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Compra', 'tipo' => 'alta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Traslado (Entrada)', 'tipo' => 'alta', 'created_at' => now(), 'updated_at' => now()],

            // Bajas
            ['nombre' => 'Muerte', 'tipo' => 'baja', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Venta', 'tipo' => 'baja', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Traslado (Salida)', 'tipo' => 'baja', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Consumo', 'tipo' => 'baja', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}