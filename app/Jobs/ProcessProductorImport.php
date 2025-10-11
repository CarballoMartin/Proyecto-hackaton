<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ProductorImporter;
use App\Services\UnidadProductivaImporter;
use App\Services\CsvExcelProcessor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Notifications\ImportCompletedNotification;
use App\Notifications\ImportFailedNotification;
use App\Models\BackgroundTask;
use App\Services\LoggerService; // <-- AÑADIDO

class ProcessProductorImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $originalName;
    protected $user;
    protected $task;

    public function __construct(string $filePath, string $originalName, User $user)
    {
        $this->filePath = $filePath;
        $this->originalName = $originalName;
        $this->user = $user;
    }

    /**
     * Execute the job.
     * @param CsvExcelProcessor $csvProcessor
     * @param ProductorImporter $productorImporter
     * @param UnidadProductivaImporter $unidadProductivaImporter
     */
    public function handle(

        CsvExcelProcessor $csvProcessor,
        ProductorImporter $productorImporter,
        UnidadProductivaImporter $unidadProductivaImporter
    ): void {
        $this->task = BackgroundTask::create(['name' => 'Importación de productores']);
        Log::info("Iniciando job de importación para el archivo {$this->filePath}");
        $absolutePath = Storage::disk('local')->path($this->filePath);

        try {
            
            // 1. Procesar el archivo para obtener la colección de datos
            $datos = $csvProcessor->process($absolutePath, $this->originalName);
            $totalRows = count($datos);

            // --- LOG DE INICIO ---
            LoggerService::log(
                'inicio_importacion_productor',
                null,
                null,
                "Usuario {$this->user->name} inició una importación masiva de {$totalRows} productores desde el archivo '{$this->originalName}'.",
                $this->user->id
            );

            // --- INICIO DE LA ORQUESTACIÓN DE DOS ETAPAS ---

            // 2. Etapa 1: Importar Productores y Usuarios
            $productorImporter->importarDesdeColeccion($datos);

            // 3. Etapa 2: Importar Unidades Productivas y asociarlas
            $unidadProductivaImporter->importarDesdeColeccion($datos);

            // --- FIN DE LA ORQUESTACIÓN ---

            // 4. Consolidar resultados de ambos importadores
            $productoresImportados = $productorImporter->importados;
            $asociacionesFallidas = $unidadProductivaImporter->asociacionesFallidas;
            // Se considera "importado" solo al productor, la UP es un dato asociado.
            $erroresConsolidados = array_merge($productorImporter->errores, $unidadProductivaImporter->errores);
            $totalErrores = count($erroresConsolidados);

            // --- LOG DE FIN ---
            LoggerService::log(
                'fin_importacion_productor',
                null,
                null,
                "Finalizó la importación para '{$this->originalName}'. Procesados: {$productoresImportados}, Errores: {$totalErrores}.",
                $this->user->id
            );

            // 5. Notificar al usuario con el resumen completo y la lista de errores
            $this->user->notify(new ImportCompletedNotification(
                importados: $productoresImportados,
                errores: $totalErrores,
                asociacionesFallidas: $asociacionesFallidas, 
                listaErrores: $erroresConsolidados 
            ));
        } catch (\Exception $e) {
            Log::error("Error en el job de importación para el archivo {$this->filePath}: " . $e->getMessage());
            $this->user->notify(new ImportFailedNotification($e->getMessage()));
        } finally {
            // Eliminar el archivo temporal después de procesarlo
            Storage::disk('local')->delete($this->filePath);
            if ($this->task) {
                $this->task->delete();
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        if ($this->task) {
            $this->task->delete();
        }
        if (Storage::disk('local')->exists($this->filePath)) {
            Storage::disk('local')->delete($this->filePath);
        }
        Log::error("Job de importación falló catastróficamente para el archivo {$this->filePath}: {$exception->getMessage()}");
        $this->user->notify(new ImportFailedNotification("La importación falló por un error inesperado en el sistema."));
    }
}
