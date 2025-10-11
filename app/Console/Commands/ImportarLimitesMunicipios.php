<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;

class ImportarLimitesMunicipios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:importar-limites-municipios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los límites GeoJSON de los municipios de Misiones desde un archivo a la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando la importación de límites de municipios...');

        $filePath = base_path('municipios.geojson');

        if (!File::exists($filePath)) {
            $this->error('El archivo municipios.geojson no se encontró en la raíz del proyecto.');
            return 1;
        }

        $geojsonContent = File::get($filePath);
        $data = json_decode($geojsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Error al decodificar el archivo JSON. Asegúrese de que es un GeoJSON válido.');
            return 1;
        }

        $features = $data['features'] ?? [];
        $provinciaNombre = 'Misiones';
        $updatedCount = 0;
        $notFoundCount = 0;

        DB::beginTransaction();
        try {
            foreach ($features as $feature) {
                $properties = $feature['properties'] ?? [];
                $featureProvincia = $properties['provincia']['nombre'] ?? null;

                if ($featureProvincia === $provinciaNombre) {
                    $municipioNombre = $properties['nombre'] ?? null;

                    if (!$municipioNombre) {
                        $this->warn('Se encontró un municipio de Misiones sin nombre en el GeoJSON. Saltando...');
                        continue;
                    }

                    $municipio = Municipio::where('nombre', $municipioNombre)->first();

                    if ($municipio) {
                        $geometry = $feature['geometry'] ?? null;
                        if ($geometry) {
                            $municipio->geojson_boundary = json_encode($geometry);
                            $municipio->save();
                            $updatedCount++;
                            $this->line("Límites para '{$municipioNombre}' actualizados.");
                        } else {
                            $this->warn("No se encontró geometría para '{$municipioNombre}'.");
                        }
                    } else {
                        $this->warn("El municipio '{$municipioNombre}' del GeoJSON no fue encontrado en la base de datos.");
                        $notFoundCount++;
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Ocurrió un error durante la importación: ' . $e->getMessage());
            return 1;
        }

        $this->info('\nImportación completada.');
        $this->info("Municipios actualizados: {$updatedCount}");
        $this->warn("Municipios no encontrados en la BD: {$notFoundCount}");

        return 0;
    }
}
