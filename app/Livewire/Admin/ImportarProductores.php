<?php

namespace App\Livewire\Admin;

use App\Exceptions\MissingHeadersException;
use App\Jobs\ProcessProductorImport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Interfaces\FileProcessorInterface;

class ImportarProductores extends Component
{
    use WithFileUploads;

    public $archivoCsv;
    public $preview; // Variable para almacenar la vista previa del archivo
    public $showMissingColumnsModal = false;
    public $missingColumns = [];
    public $errorFileName = '';

    /**
     * Método que se ejecuta al montar el componente.
     * Inicializa la colección de vista previa.
     */
    public function mount()
    {
        $this->preview = collect(); // Inicializar preview como una colección vacía
    }

    public function rules()
    {
        return ['archivoCsv' => 'required|file|mimes:csv,txt,xls,xlsx,ods|max:4096'];
    }

    public function updatedArchivoCsv()
    {
        $this->validate();
        $fileProcessor = app(FileProcessorInterface::class);
        try {
            $this->preview = $fileProcessor->process($this->archivoCsv->getRealPath(), $this->archivoCsv->getClientOriginalName());
        } catch (MissingHeadersException $e) {
            $this->missingColumns = $e->missingHeaders;
            $this->errorFileName = $this->archivoCsv->getClientOriginalName();
            $this->showMissingColumnsModal = true;
            $this->reset('archivoCsv', 'preview');
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('banner-message', style: 'danger', message: $e->getMessage());
            $this->reset('archivoCsv', 'preview');
        }
    }

    public function closeModal()
    {
        $this->showMissingColumnsModal = false;
        $this->missingColumns = [];
        $this->errorFileName = '';
    }

    /**
     * Método para confirmar la importación de los productores.
     * Utiliza el servicio ProductorImporter para procesar la colección de vista previa.
     */
    public function confirmarImportacion()
    {
        // Validar que haya un archivo y una vista previa para importar.
        if (!$this->archivoCsv || $this->preview->isEmpty())
        {
            $this->dispatch('banner-message', style: 'danger', message: 'Debe seleccionar un archivo y tener una vista previa para importar.');
            return;
        }

        $user = Auth::user();

        // 1. Guardar el archivo en una ubicación temporal.
        $path = $this->archivoCsv->store('imports', 'local');


        // 2. Despachar el Job a la cola.
        ProcessProductorImport::dispatch($path, $this->archivoCsv->getClientOriginalName(), $user);

        // 3. Mostrar un mensaje de éxito inmediato al usuario.
        $this->dispatch('banner-message', style: 'success', message: '¡Tu archivo ha sido recibido!');

        $this->reset('archivoCsv', 'preview');
        $this->mount(); // Reiniciar el componente para limpiar el estado   
    }


    public function render()
    {
        return view('livewire.admin.importar-productores');
    }
}