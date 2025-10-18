<?php

namespace App\Services;

use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use App\Models\DatoClimaticoCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para detección y gestión de alertas ambientales
 * 
 * Analiza datos climáticos y detecta condiciones de riesgo:
 * - Sequía prolongada
 * - Tormentas intensas
 * - Estrés térmico animal
 * - Riesgo de heladas
 */
class AlertasAmbientalesService
{
    // ✅ CORRECCIÓN #3: Constantes de umbrales configurables
    private const UMBRAL_SEQUIA_DIAS = 15;
    private const UMBRAL_SEQUIA_TEMP = 32;
    private const UMBRAL_TORMENTA_LLUVIA = 50; // mm
    private const UMBRAL_TORMENTA_VIENTO = 60; // km/h
    private const UMBRAL_ESTRES_TERMICO = 35; // °C
    private const UMBRAL_ESTRES_DIAS = 3;
    private const UMBRAL_HELADA = 5; // °C
    private const HORAS_MAX_DATOS_ANTIGUOS = 25; // horas

    /**
     * Detecta y crea alertas para todas las unidades productivas
     */
    public function detectarAlertasParaTodasLasUnidades(): array
    {
        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->with(['datosClimaticos' => function($query) {
                $query->latest('fecha_consulta')->take(30);
            }])
            ->get();

        $estadisticas = [
            'total_unidades' => $unidades->count(),
            'alertas_creadas' => 0,
            'alertas_desactivadas' => 0,
            'errores' => 0,
        ];

        foreach ($unidades as $unidad) {
            try {
                $resultado = $this->detectarAlertasParaUnidad($unidad);
                $estadisticas['alertas_creadas'] += $resultado['creadas'];
                $estadisticas['alertas_desactivadas'] += $resultado['desactivadas'];
            } catch (\Exception $e) {
                $estadisticas['errores']++;
                Log::error('Error detectando alertas', [
                    'unidad_productiva_id' => $unidad->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $estadisticas;
    }

    /**
     * Detecta alertas para una unidad productiva específica
     * ✅ CORRECCIÓN #4: Validación de datos recientes agregada
     */
    public function detectarAlertasParaUnidad(UnidadProductiva $unidad): array
    {
        $estadisticas = [
            'creadas' => 0,
            'desactivadas' => 0,
        ];

        // Obtener datos climáticos recientes
        $datosClimaticos = $unidad->datosClimaticos()
            ->where('fecha_consulta', '>=', now()->subDays(30))
            ->where('fecha_consulta', '>=', now()->subHours(self::HORAS_MAX_DATOS_ANTIGUOS))
            ->orderBy('fecha_consulta', 'desc')
            ->get();

        if ($datosClimaticos->isEmpty()) {
            Log::warning('No hay datos climáticos recientes para la unidad', [
                'unidad_productiva_id' => $unidad->id,
            ]);
            return $estadisticas;
        }

        // ✅ CORRECCIÓN #4: Validar que el dato más reciente no sea muy viejo
        $datoMasReciente = $datosClimaticos->first();
        if ($datoMasReciente->fecha_consulta < now()->subHours(self::HORAS_MAX_DATOS_ANTIGUOS)) {
            Log::warning('Datos climáticos obsoletos, no se crean alertas', [
                'unidad_productiva_id' => $unidad->id,
                'ultima_actualizacion' => $datoMasReciente->fecha_consulta,
                'horas_antiguo' => $datoMasReciente->fecha_consulta->diffInHours(now()),
            ]);
            return $estadisticas;
        }

        // 1. Detectar Sequía
        if ($this->detectarSequia($datosClimaticos)) {
            if ($this->crearOActualizarAlerta($unidad, 'sequia', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'sequia')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 2. Detectar Tormenta
        if ($this->detectarTormenta($datoMasReciente)) {
            if ($this->crearOActualizarAlerta($unidad, 'tormenta', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'tormenta')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 3. Detectar Estrés Térmico
        if ($this->detectarEstreTermico($datosClimaticos)) {
            if ($this->crearOActualizarAlerta($unidad, 'estres_termico', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'estres_termico')) {
                $estadisticas['desactivadas']++;
            }
        }

        // 4. Detectar Helada
        if ($this->detectarHelada($datoMasReciente)) {
            if ($this->crearOActualizarAlerta($unidad, 'helada', $datosClimaticos)) {
                $estadisticas['creadas']++;
            }
        } else {
            if ($this->desactivarAlerta($unidad, 'helada')) {
                $estadisticas['desactivadas']++;
            }
        }

        return $estadisticas;
    }

    /**
     * Detecta condiciones de sequía
     * ✅ USA constantes configurables
     */
    private function detectarSequia($datosClimaticos): bool
    {
        $ultimos15Dias = $datosClimaticos->take(15);
        
        if ($ultimos15Dias->count() < 15) {
            return false;
        }

        $diasSinLluvia = 0;
        $temperaturaPromedio = 0;

        foreach ($ultimos15Dias as $dato) {
            $lluviaHoy = $dato->precipitacion[0] ?? 0;
            
            if ($lluviaHoy < 1) {
                $diasSinLluvia++;
            }
            
            $temperaturaPromedio += $dato->temperatura_actual ?? 0;
        }

        $temperaturaPromedio = $temperaturaPromedio / $ultimos15Dias->count();

        return $diasSinLluvia >= self::UMBRAL_SEQUIA_DIAS || 
               $temperaturaPromedio > self::UMBRAL_SEQUIA_TEMP;
    }

    /**
     * Detecta tormenta inminente
     * ✅ USA constantes configurables
     */
    private function detectarTormenta($datoClimatico): bool
    {
        if (!$datoClimatico) {
            return false;
        }

        $lluviaEsperada = $datoClimatico->precipitacion ?? [];
        $vientoEsperado = $datoClimatico->viento_max ?? [];

        for ($i = 0; $i < 3 && $i < count($lluviaEsperada); $i++) {
            $lluvia = $lluviaEsperada[$i] ?? 0;
            $viento = $vientoEsperado[$i] ?? 0;

            if ($lluvia > self::UMBRAL_TORMENTA_LLUVIA || 
                $viento > self::UMBRAL_TORMENTA_VIENTO) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta estrés térmico
     * ✅ USA constantes configurables
     */
    private function detectarEstreTermico($datosClimaticos): bool
    {
        $ultimos3Dias = $datosClimaticos->take(3);
        
        if ($ultimos3Dias->count() < 3) {
            return false;
        }

        $diasCalurosos = 0;

        foreach ($ultimos3Dias as $dato) {
            $tempMax = $dato->temperaturas_max[0] ?? 0;
            
            if ($tempMax > self::UMBRAL_ESTRES_TERMICO) {
                $diasCalurosos++;
            }
        }

        return $diasCalurosos >= self::UMBRAL_ESTRES_DIAS;
    }

    /**
     * Detecta riesgo de helada
     * ✅ USA constantes configurables
     */
    private function detectarHelada($datoClimatico): bool
    {
        if (!$datoClimatico) {
            return false;
        }

        $temperaturasMin = $datoClimatico->temperaturas_min ?? [];

        for ($i = 0; $i < 2 && $i < count($temperaturasMin); $i++) {
            $tempMin = $temperaturasMin[$i] ?? 999;
            
            if ($tempMin < self::UMBRAL_HELADA) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea o actualiza una alerta
     * ✅ CORRECCIÓN #7: Logging agregado
     */
    private function crearOActualizarAlerta(UnidadProductiva $unidad, string $tipo, $datosClimaticos): bool
    {
        $alertaExistente = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
            ->where('tipo', $tipo)
            ->activas()
            ->first();

        if ($alertaExistente) {
            return false;
        }

        $datosAlerta = $this->obtenerDatosAlerta($tipo, $datosClimaticos->first());

        $alerta = AlertaAmbiental::create([
            'unidad_productiva_id' => $unidad->id,
            'tipo' => $tipo,
            'nivel' => $datosAlerta['nivel'],
            'titulo' => $datosAlerta['titulo'],
            'mensaje' => $datosAlerta['mensaje'],
            'datos_contexto' => $datosAlerta['contexto'],
            'fecha_inicio' => now(),
            'activa' => true,
            'leida' => false,
        ]);

        // ✅ CORRECCIÓN #7: Logging
        Log::info('Alerta ambiental creada', [
            'alerta_id' => $alerta->id,
            'tipo' => $tipo,
            'nivel' => $datosAlerta['nivel'],
            'unidad_productiva_id' => $unidad->id,
            'unidad_nombre' => $unidad->nombre,
            'datos_contexto' => $datosAlerta['contexto'],
        ]);

        return true;
    }

    /**
     * Desactiva alertas del tipo especificado
     * ✅ CORRECCIÓN #7: Logging agregado
     */
    private function desactivarAlerta(UnidadProductiva $unidad, string $tipo): bool
    {
        $alertas = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
            ->where('tipo', $tipo)
            ->activas()
            ->get();

        if ($alertas->isEmpty()) {
            return false;
        }

        foreach ($alertas as $alerta) {
            $alerta->desactivar();
            
            // ✅ CORRECCIÓN #7: Logging
            Log::info('Alerta ambiental desactivada', [
                'alerta_id' => $alerta->id,
                'tipo' => $alerta->tipo,
                'nivel' => $alerta->nivel,
                'unidad_productiva_id' => $alerta->unidad_productiva_id,
                'duracion_horas' => $alerta->created_at->diffInHours(now()),
                'fue_leida' => $alerta->leida,
            ]);
        }

        return true;
    }

    /**
     * Obtiene los datos específicos de cada tipo de alerta
     */
    private function obtenerDatosAlerta(string $tipo, $datoClimatico): array
    {
        return match($tipo) {
            'sequia' => [
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales.',
                'contexto' => [
                    'temperatura_promedio' => $datoClimatico->temperatura_actual ?? null,
                    'dias_sin_lluvia' => 15,
                ],
            ],
            'tormenta' => [
                'nivel' => 'alto',
                'titulo' => 'Tormenta Intensa',
                'mensaje' => 'Se espera tormenta intensa. Asegura instalaciones y protege animales vulnerables.',
                'contexto' => [
                    'lluvia_esperada' => $datoClimatico->precipitacion[0] ?? null,
                    'viento_esperado' => $datoClimatico->viento_max[0] ?? null,
                    'fecha_esperada' => $datoClimatico->fechas[0] ?? null,
                ],
            ],
            'estres_termico' => [
                'nivel' => 'medio',
                'titulo' => 'Estrés Térmico',
                'mensaje' => 'Temperaturas extremas pueden afectar el bienestar animal. Aumenta disponibilidad de sombra y agua.',
                'contexto' => [
                    'temperatura_maxima' => $datoClimatico->temperaturas_max[0] ?? null,
                    'dias_consecutivos' => 3,
                ],
            ],
            'helada' => [
                'nivel' => 'bajo',
                'titulo' => 'Riesgo de Helada',
                'mensaje' => 'Se esperan temperaturas bajas. Protege crías recién nacidas.',
                'contexto' => [
                    'temperatura_minima' => $datoClimatico->temperaturas_min[0] ?? null,
                    'fecha_esperada' => $datoClimatico->fechas[0] ?? null,
                ],
            ],
            default => [
                'nivel' => 'bajo',
                'titulo' => 'Alerta Ambiental',
                'mensaje' => 'Condición detectada que requiere atención.',
                'contexto' => [],
            ],
        };
    }

    /**
     * Obtiene alertas activas para un productor
     */
    public function obtenerAlertasActivasParaProductor($productorId): \Illuminate\Database\Eloquent\Collection
    {
        return AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productorId) {
            $query->where('productors.id', $productorId);
        })
        ->activas()
        ->with('unidadProductiva')
        ->distinct()
        ->orderBy('nivel', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Cuenta alertas no leídas para un productor
     */
    public function contarAlertasNoLeidasParaProductor($productorId): int
    {
        return AlertaAmbiental::whereHas('unidadProductiva.productores', function($query) use ($productorId) {
            $query->where('productors.id', $productorId);
        })
        ->activas()
        ->noLeidas()
        ->count();
    }
}

