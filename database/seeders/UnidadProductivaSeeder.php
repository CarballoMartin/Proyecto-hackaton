<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Productor;
use App\Models\Campo;
use App\Models\UnidadProductiva;
use App\Models\TipoIdentificador;
use App\Models\CondicionTenencia;

class UnidadProductivaSeeder extends Seeder
{
    public function run(): void
    {
        $tipornspa = TipoIdentificador::where('nombre', 'RNSPA')->firstOrFail();
        $productores = Productor::all();
        $condicion = CondicionTenencia::firstOrFail();

        if ($productores->isEmpty()) {
            $this->command->error('No hay productores. Ejecuta ProductorSeeder
      primero.');
            return;
        }

        $campo1 = Campo::create([
            'nombre' => 'Campo de Juan',
            'localidad' => 'Zona Sur',
            'latitud' => -27.5911598205566,
            'longitud' => -55.6348381042481,
        ]);
        $campo2 = Campo::create([
            'nombre' => 'Campo de Maria',
            'localidad' => 'Zona Centro',
            'latitud' => -27.5502891540527,
            'longitud' => -55.6715888977051,
        ]);

        $campo3 = Campo::create([
            'nombre' => 'Campo los pollos',
            'localidad' => 'Zona Centro',
            'latitud' => -27.4518795013428,
            'longitud' => -55.7689895629883,
        ]);

        $up1 = UnidadProductiva::create([
            'campo_id' => $campo1->id,
            'tipo_identificador_id' => $tipornspa->id,
            'identificador_local' => '04.025.1.00001',
            'superficie' => 50.5,
            'habita' => true,
        ]);

        $up2 = UnidadProductiva::create([
            'campo_id' => $campo2->id,
            'tipo_identificador_id' => $tipornspa->id,
            'identificador_local' => '04.026.1.00003',
            'superficie' => 120.0,
            'habita' => false,
        ]);

        // Asociar UPs a Productores con datos en la tabla pivote
        if ($productores->has(0)) {
            $productor1 = $productores[0];
            $productor1->unidadesProductivas()->attach($up1->id, [
                'condicion_tenencia_id' => $condicion->id,
                'fecha_inicio' => now()->subYears(5),
            ]);
        }

        if ($productores->has(1)) {
            $productor2 = $productores[1];
            $productor2->unidadesProductivas()->attach($up2->id, [
                'condicion_tenencia_id' => $condicion->id,
                'fecha_inicio' => now()->subYears(2),
            ]);
            // El productor 2 también tiene acceso a la UP1
            $productor2->unidadesProductivas()->attach($up1->id, [
                'condicion_tenencia_id' => $condicion->id,
                'fecha_inicio' => now()->subYears(1),
            ]);
        }
    }
}