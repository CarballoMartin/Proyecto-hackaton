<?php

namespace App\Livewire\Productor\Ambiental;

use Livewire\Component;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\CaracteristicaSuelo;
use App\Services\SueloApi\SoilGridsApiService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Suelo extends Component
{
    public $productor;
    public $unidadesProductivas = [];
    public $unidadSeleccionada = null;
    public $caracteristicasSuelo = null;
    public $recomendaciones = [];
    public $recomendacionesPasturas = [];
    public $cargando = false;
    public $actualizando = false;

    protected $soilGridsService;

    public function boot(SoilGridsApiService $soilGridsService)
    {
        $this->soilGridsService = $soilGridsService;
    }

    public function mount()
    {
        $this->productor = Productor::where('usuario_id', Auth::id())->first();
        
        if ($this->productor) {
            $this->unidadesProductivas = $this->productor->unidadesProductivas()
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->get();
            
            if ($this->unidadesProductivas->isNotEmpty()) {
                $this->unidadSeleccionada = $this->unidadesProductivas->first()->id;
                $this->cargarDatos();
            }
        }
    }

    public function updatedUnidadSeleccionada()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        if (!$this->unidadSeleccionada) {
            return;
        }

        $this->cargando = true;

        try {
            // Cargar características de suelo más recientes
            $this->caracteristicasSuelo = CaracteristicaSuelo::where('unidad_productiva_id', $this->unidadSeleccionada)
                ->orderBy('fecha_consulta', 'desc')
                ->first();

            if ($this->caracteristicasSuelo) {
                $this->recomendaciones = $this->caracteristicasSuelo->recomendaciones;
                $this->recomendacionesPasturas = $this->caracteristicasSuelo->recomendaciones_pasturas;
            } else {
                $this->recomendaciones = [];
                $this->recomendacionesPasturas = [];
            }

        } catch (\Exception $e) {
            $this->addError('error', 'Error cargando datos: ' . $e->getMessage());
        } finally {
            $this->cargando = false;
        }
    }

    public function actualizarDatos()
    {
        if (!$this->unidadSeleccionada) {
            $this->addError('error', 'Selecciona una unidad productiva');
            return;
        }

        $this->actualizando = true;

        try {
            $unidad = UnidadProductiva::find($this->unidadSeleccionada);
            
            if (!$unidad) {
                $this->addError('error', 'Unidad productiva no encontrada');
                return;
            }

            // Obtener datos de suelo más recientes
            $sueloData = $this->soilGridsService->obtenerDatosSuelo($unidad);
            
            if ($sueloData) {
                // Guardar en base de datos
                $this->soilGridsService->guardarDatosSuelo($unidad, $sueloData);
                
                // Recargar datos
                $this->cargarDatos();
                
                session()->flash('success', 'Datos de suelo actualizados correctamente');
            } else {
                $this->addError('error', 'No se pudieron obtener datos de suelo actualizados');
            }

        } catch (\Exception $e) {
            $this->addError('error', 'Error actualizando datos: ' . $e->getMessage());
        } finally {
            $this->actualizando = false;
        }
    }

    public function actualizarTodasLasUnidades()
    {
        $this->actualizando = true;

        try {
            $resultados = $this->soilGridsService->actualizarDatosSuelo();
            
            // Recargar datos de la unidad actual
            $this->cargarDatos();
            
            $mensaje = "Actualización completada: {$resultados['exitosos']} exitosas, {$resultados['fallidos']} fallidas";
            session()->flash('success', $mensaje);

        } catch (\Exception $e) {
            $this->addError('error', 'Error actualizando todas las unidades: ' . $e->getMessage());
        } finally {
            $this->actualizando = false;
        }
    }

    public function render()
    {
        return view('livewire.productor.ambiental.suelo');
    }
}
