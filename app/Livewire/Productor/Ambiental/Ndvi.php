<?php

namespace App\Livewire\Productor\Ambiental;

use Livewire\Component;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\IndiceVegetacion;
use App\Services\SatelitalApi\CopernicusApiService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Ndvi extends Component
{
    public $productor;
    public $unidadesProductivas = [];
    public $unidadSeleccionada = null;
    public $periodo = 90; // Días
    public $datosNdvi = [];
    public $estadisticas = [];
    public $cargando = false;
    public $actualizando = false;

    protected $copernicusService;

    public function boot(CopernicusApiService $copernicusService)
    {
        $this->copernicusService = $copernicusService;
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

    public function updatedPeriodo()
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
            $unidad = UnidadProductiva::find($this->unidadSeleccionada);
            
            if (!$unidad) {
                $this->addError('error', 'Unidad productiva no encontrada');
                return;
            }

            // Cargar datos históricos de NDVI
            $this->datosNdvi = IndiceVegetacion::where('unidad_productiva_id', $this->unidadSeleccionada)
                ->where('fecha_imagen', '>=', Carbon::now()->subDays($this->periodo))
                ->orderBy('fecha_imagen')
                ->get();

            // Calcular estadísticas
            $this->calcularEstadisticas();

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

            // Obtener datos NDVI más recientes
            $ndviData = $this->copernicusService->obtenerNDVI($unidad);
            
            if ($ndviData) {
                // Guardar en base de datos
                $this->copernicusService->guardarDatosNDVI($unidad, $ndviData);
                
                // Recargar datos
                $this->cargarDatos();
                
                session()->flash('success', 'Datos NDVI actualizados correctamente');
            } else {
                $this->addError('error', 'No se pudieron obtener datos NDVI actualizados');
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
            $resultados = $this->copernicusService->actualizarDatosNDVI();
            
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

    private function calcularEstadisticas()
    {
        if ($this->datosNdvi->isEmpty()) {
            $this->estadisticas = [
                'promedio' => 0,
                'maximo' => 0,
                'minimo' => 0,
                'tendencia' => 'sin_datos',
                'clasificacion_actual' => 'sin_datos',
                'estado_salud' => 'Sin datos',
                'datos_confiables' => 0,
                'total_datos' => 0,
            ];
            return;
        }

        $valoresNdvi = $this->datosNdvi->pluck('ndvi')->toArray();
        $datosConfiables = $this->datosNdvi->where('nubosidad_porcentaje', '<=', 20)->count();

        // Calcular tendencia
        $tendencia = IndiceVegetacion::tendenciaNdviUnidad($this->unidadSeleccionada, $this->periodo);

        // Obtener datos más recientes
        $datoReciente = $this->datosNdvi->sortByDesc('fecha_imagen')->first();

        $this->estadisticas = [
            'promedio' => round(array_sum($valoresNdvi) / count($valoresNdvi), 3),
            'maximo' => round(max($valoresNdvi), 3),
            'minimo' => round(min($valoresNdvi), 3),
            'tendencia' => $tendencia['tendencia'],
            'cambio_tendencia' => $tendencia['cambio'],
            'clasificacion_actual' => $datoReciente ? $datoReciente->clasificacion : 'sin_datos',
            'estado_salud' => $datoReciente ? $datoReciente->estado_salud : 'Sin datos',
            'datos_confiables' => $datosConfiables,
            'total_datos' => $this->datosNdvi->count(),
            'porcentaje_confiables' => $this->datosNdvi->count() > 0 
                ? round(($datosConfiables / $this->datosNdvi->count()) * 100, 1) 
                : 0,
        ];
    }

    public function render()
    {
        return view('livewire.productor.ambiental.ndvi')->layout('layouts.productor');
    }
}
