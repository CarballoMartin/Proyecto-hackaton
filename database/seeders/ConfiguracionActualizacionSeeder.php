<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionActualizacion;
use App\Models\User;

class ConfiguracionActualizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the superadmin user to associate the configuration with
        $superadmin = User::where('rol', 'superadmin')->first();

        if ($superadmin) {
            // Create a default configuration period if none exists
            ConfiguracionActualizacion::firstOrCreate(
                ['superadmin_id' => $superadmin->id],
                [
                    'frecuencia_dias' => 365, // Default to one year
                    'ultima_actualizacion' => now()->subYear(),
                    'proxima_actualizacion' => now(),
                    'activo' => true,
                    'fecha_configuracion' => now(),
                ]
            );
        }
    }
}
