<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MunicipioCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(base_path('municipios.geojson'));
        $data = json_decode($json);

        $this->command->getOutput()->progressStart(count($data->features));

        foreach ($data->features as $feature) {
            $nombre = $feature->properties->nombre;
            // A veces el nombre en el geojson tiene un prefijo, lo limpiamos
            $nombreLimpio = str_replace('Municipio ', '', $nombre);
            $nombreLimpio = str_replace('Comuna ', '', $nombreLimpio);

            $latitud = $feature->properties->centroide->lat;
            $longitud = $feature->properties->centroide->lon;

            // Buscamos por el nombre original y el limpio
            $municipio = Municipio::where('nombre', $nombre)
                                ->orWhere('nombre', $nombreLimpio)
                                ->first();

            if ($municipio) {
                $municipio->update([
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                ]);
            }
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('\nCoordenadas de municipios actualizadas correctamente desde el archivo geojson.');
    }
}