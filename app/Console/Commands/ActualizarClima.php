<?php

namespace App\Console\Commands;

use App\Models\Clima;
use App\Models\Municipio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ActualizarClima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clima:actualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta la API de OpenWeather y actualiza los datos del clima para todos los municipios con coordenadas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('services.openweather.key');
        if (!$apiKey) {
            $this->error('La clave de API de OpenWeather no está configurada en config/services.php');
            return 1; // Termina con error
        }

        $municipios = Municipio::whereNotNull('latitud')->whereNotNull('longitud')->get();

        if ($municipios->isEmpty()) {
            $this->info('No se encontraron municipios con coordenadas para actualizar el clima.');
            return 0;
        }

        $this->info(sprintf('Actualizando el clima para %d municipios...', $municipios->count()));
        $this->getOutput()->progressStart($municipios->count());

        foreach ($municipios as $municipio) {
            $lat = $municipio->latitud;
            $lon = $municipio->longitud;

            try {
                $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $apiKey,
                    'units' => 'metric', // Unidades en sistema métrico
                    'lang' => 'es'      // Idioma español
                ]);

                if ($response->successful()) {
                    Clima::create([
                        'localidad_id' => $municipio->id,
                        'datos_json' => $response->body(),
                        'fecha_hora_consulta' => now(),
                    ]);
                } else {
                    $this->error(sprintf('Error al consultar el clima para %s: %s', $municipio->nombre, $response->body()));
                    Log::error('Error API Clima para ' . $municipio->nombre, ['response' => $response->body()]);
                }

            } catch (Exception $e) {
                $this->error(sprintf('Excepción al consultar el clima para %s: %s', $municipio->nombre, $e->getMessage()));
                Log::error('Excepción API Clima para ' . $municipio->nombre, ['exception' => $e->getMessage()]);
            }

            $this->getOutput()->progressAdvance();
            sleep(1); // Pequeña pausa para no saturar la API
        }

        $this->getOutput()->progressFinish();
        $this->info('\nActualización del clima completada.');
        return 0;
    }
}