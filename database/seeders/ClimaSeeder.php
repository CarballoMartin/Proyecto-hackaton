<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clima;
use App\Models\Municipio;
use Carbon\Carbon;

class ClimaSeeder extends Seeder
{
    public function run(): void
    {
        $municipios = Municipio::all();
        
        if ($municipios->isEmpty()) {
            echo "No hay municipios para crear datos de clima. Ejecute MunicipiosSeeder primero.\n";
            return;
        }

        foreach ($municipios as $municipio) {
            // Crear datos de clima para cada municipio
            // Temperaturas más realistas para Misiones
            $temperatura = rand(18, 32);
            $temperaturaMax = $temperatura + rand(3, 6);
            $temperaturaMin = $temperatura - rand(3, 8);
            
            // Condiciones más variadas y realistas
            $condiciones = [
                'clear sky', 'few clouds', 'scattered clouds', 'broken clouds', 
                'shower rain', 'rain', 'thunderstorm', 'mist', 'fog'
            ];
            $condicion = $condiciones[array_rand($condiciones)];
            
            $datosClima = [
                'main' => [
                    'temp' => $temperatura,
                    'temp_max' => $temperaturaMax,
                    'temp_min' => $temperaturaMin,
                    'humidity' => rand(40, 90)
                ],
                'weather' => [
                    [
                        'main' => ucfirst(str_replace(' ', '', $condicion)),
                        'description' => $condicion
                    ]
                ]
            ];

            Clima::create([
                'localidad_id' => $municipio->id,
                'fecha_hora_consulta' => Carbon::now()->subHours(rand(1, 6)),
                'datos_json' => json_encode($datosClima)
            ]);
        }
        
        echo "Datos de clima creados para " . $municipios->count() . " municipios.\n";
    }
}
