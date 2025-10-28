<?php

namespace App\Services;

use App\Models\UnidadProductiva;
use App\Models\IndiceVegetacion;
use App\Models\CaracteristicaSuelo;
use App\Models\AlertaAmbiental;
use App\Services\ClimaApi\NasaPowerService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AlertaAmbientalService
{
    private NasaPowerService $nasaPowerService;

    public function __construct(NasaPowerService $nasaPowerService)
    {
        $this->nasaPowerService = $nasaPowerService;
    }

    /**
     * Genera alertas ambientales para una unidad productiva
     */
    public function generarAlertasUnidad(UnidadProductiva $unidad): array
    {
        $alertasGeneradas = [];

        try {
            // Alertas de clima
            $alertasClima = $this->generarAlertasClima($unidad);
            $alertasGeneradas = array_merge($alertasGeneradas, $alertasClima);

            // Alertas de NDVI
            $alertasNdvi = $this->generarAlertasNdvi($unidad);
            $alertasGeneradas = array_merge($alertasGeneradas, $alertasNdvi);

            // Alertas de suelo
            $alertasSuelo = $this->generarAlertasSuelo($unidad);
            $alertasGeneradas = array_merge($alertasGeneradas, $alertasSuelo);

        } catch (\Exception $e) {
            Log::error('Error generando alertas para unidad ' . $unidad->id . ': ' . $e->getMessage());
        }

        return $alertasGeneradas;
    }

    /**
     * Genera alertas de clima
     */
    private function generarAlertasClima(UnidadProductiva $unidad): array
    {
        $alertas = [];
        $climaData = $this->nasaPowerService->obtenerDatosClima($unidad);

        if (!$climaData) {
            return $alertas;
        }

        // Alerta de sequía
        if ($climaData['precipitacion_7dias'] < config('ambiental.alertas.sequia_dias_sin_lluvia', 15)) {
            $severidad = $this->determinarSeveridadSequia($climaData['precipitacion_7dias']);
            
            $alertas[] = [
                'tipo_alerta' => 'sequia',
                'severidad' => $severidad,
                'titulo' => 'Sequía Prolongada Detectada',
                'descripcion' => "Sequía de {$climaData['precipitacion_7dias']} días sin lluvia significativa.",
                'datos_alerta' => [
                    'dias_sin_lluvia' => $climaData['precipitacion_7dias'],
                    'precipitacion_total' => $climaData['precipitacion_total'],
                    'humedad_suelo' => $climaData['humedad_suelo'] ?? 0,
                ],
                'recomendaciones' => $this->obtenerRecomendacionesSequia($severidad),
                'fecha_inicio' => Carbon::now()->subDays($climaData['precipitacion_7dias']),
            ];
        }

        // Alerta de tormenta
        if ($climaData['precipitacion_24h'] > config('ambiental.alertas.tormenta_mm_24h', 50)) {
            $severidad = $this->determinarSeveridadTormenta($climaData['precipitacion_24h']);
            
            $alertas[] = [
                'tipo_alerta' => 'tormenta',
                'severidad' => $severidad,
                'titulo' => 'Tormenta Intensa',
                'descripcion' => "Precipitación intensa de {$climaData['precipitacion_24h']}mm en 24 horas.",
                'datos_alerta' => [
                    'precipitacion_24h' => $climaData['precipitacion_24h'],
                    'viento_velocidad' => $climaData['viento_velocidad'],
                    'probabilidad_lluvia' => $climaData['probabilidad_lluvia'] ?? 0,
                ],
                'recomendaciones' => $this->obtenerRecomendacionesTormenta($severidad),
                'fecha_inicio' => Carbon::now(),
            ];
        }

        // Alerta de estrés térmico
        if ($climaData['temperatura_maxima'] > config('ambiental.alertas.estres_termico_celsius', 35)) {
            $severidad = $this->determinarSeveridadEstresTermico($climaData['temperatura_maxima']);
            
            $alertas[] = [
                'tipo_alerta' => 'estres_termico',
                'severidad' => $severidad,
                'titulo' => 'Estrés Térmico en Ganado',
                'descripcion' => "Temperatura máxima de {$climaData['temperatura_maxima']}°C detectada.",
                'datos_alerta' => [
                    'temperatura_maxima' => $climaData['temperatura_maxima'],
                    'temperatura_promedio' => $climaData['temperatura_promedio'],
                    'humedad_relativa' => $climaData['humedad_relativa'],
                ],
                'recomendaciones' => $this->obtenerRecomendacionesEstresTermico($severidad),
                'fecha_inicio' => Carbon::now(),
            ];
        }

        // Alerta de helada
        if ($climaData['temperatura_minima'] < config('ambiental.alertas.helada_celsius', 0)) {
            $severidad = $this->determinarSeveridadHelada($climaData['temperatura_minima']);
            
            $alertas[] = [
                'tipo_alerta' => 'helada',
                'severidad' => $severidad,
                'titulo' => 'Riesgo de Helada',
                'descripcion' => "Temperatura mínima de {$climaData['temperatura_minima']}°C detectada.",
                'datos_alerta' => [
                    'temperatura_minima' => $climaData['temperatura_minima'],
                    'temperatura_promedio' => $climaData['temperatura_promedio'],
                    'humedad_relativa' => $climaData['humedad_relativa'],
                ],
                'recomendaciones' => $this->obtenerRecomendacionesHelada($severidad),
                'fecha_inicio' => Carbon::now(),
            ];
        }

        // Alerta de viento fuerte
        if ($climaData['viento_velocidad'] > config('ambiental.alertas.viento_kmh', 40)) {
            $severidad = $this->determinarSeveridadViento($climaData['viento_velocidad']);
            
            $alertas[] = [
                'tipo_alerta' => 'viento',
                'severidad' => $severidad,
                'titulo' => 'Vientos Fuertes',
                'descripcion' => "Velocidad del viento de {$climaData['viento_velocidad']} km/h detectada.",
                'datos_alerta' => [
                    'viento_velocidad' => $climaData['viento_velocidad'],
                    'viento_direccion' => $climaData['viento_direccion'] ?? 'N/A',
                    'rafagas_maximas' => $climaData['rafagas_maximas'] ?? 0,
                ],
                'recomendaciones' => $this->obtenerRecomendacionesViento($severidad),
                'fecha_inicio' => Carbon::now(),
            ];
        }

        return $alertas;
    }

    /**
     * Genera alertas de NDVI
     */
    private function generarAlertasNdvi(UnidadProductiva $unidad): array
    {
        $alertas = [];
        
        $ndviReciente = IndiceVegetacion::where('unidad_productiva_id', $unidad->id)
            ->where('fecha_imagen', '>=', Carbon::now()->subDays(30))
            ->orderBy('fecha_imagen', 'desc')
            ->first();

        if (!$ndviReciente) {
            return $alertas;
        }

        // Alerta de NDVI bajo
        if ($ndviReciente->clasificacion === 'baja' && $ndviReciente->ndvi < 0.2) {
            $severidad = $this->determinarSeveridadNdvi($ndviReciente->ndvi);
            
            $alertas[] = [
                'tipo_alerta' => 'ndvi_bajo',
                'severidad' => $severidad,
                'titulo' => 'Vegetación Degradada',
                'descripcion' => "NDVI bajo detectado ({$ndviReciente->ndvi}) indicando vegetación degradada.",
                'datos_alerta' => [
                    'ndvi_valor' => $ndviReciente->ndvi,
                    'clasificacion' => $ndviReciente->clasificacion,
                    'fecha_imagen' => $ndviReciente->fecha_imagen,
                    'nubosidad' => $ndviReciente->nubosidad_porcentaje,
                ],
                'recomendaciones' => $this->obtenerRecomendacionesNdvi($severidad),
                'fecha_inicio' => $ndviReciente->fecha_imagen,
            ];
        }

        return $alertas;
    }

    /**
     * Genera alertas de suelo
     */
    private function generarAlertasSuelo(UnidadProductiva $unidad): array
    {
        $alertas = [];
        
        $sueloReciente = CaracteristicaSuelo::where('unidad_productiva_id', $unidad->id)
            ->orderBy('fecha_consulta', 'desc')
            ->first();

        if (!$sueloReciente) {
            return $alertas;
        }

        // Alerta de suelo degradado
        if ($sueloReciente->estado_general === 'Crítico' || $sueloReciente->estado_general === 'Deficiente') {
            $severidad = $sueloReciente->estado_general === 'Crítico' ? 'critica' : 'alta';
            
            $alertas[] = [
                'tipo_alerta' => 'suelo_degradado',
                'severidad' => $severidad,
                'titulo' => 'Degradación del Suelo',
                'descripcion' => "Estado del suelo: {$sueloReciente->estado_general}. Índice de calidad: {$sueloReciente->calcularIndiceCalidad()}%",
                'datos_alerta' => [
                    'estado_general' => $sueloReciente->estado_general,
                    'indice_calidad' => $sueloReciente->calcularIndiceCalidad(),
                    'ph_valor' => $sueloReciente->ph_valor,
                    'materia_organica' => $sueloReciente->materia_organica_porcentaje,
                    'textura' => $sueloReciente->textura_clasificacion,
                ],
                'recomendaciones' => $sueloReciente->recomendaciones,
                'fecha_inicio' => $sueloReciente->fecha_consulta,
            ];
        }

        return $alertas;
    }

    /**
     * Guarda alertas en la base de datos
     */
    public function guardarAlertas(UnidadProductiva $unidad, array $alertas): array
    {
        $alertasGuardadas = [];
        
        foreach ($alertas as $alertaData) {
            // Verificar si ya existe una alerta similar activa
            $alertaExistente = AlertaAmbiental::where('unidad_productiva_id', $unidad->id)
                ->where('tipo_alerta', $alertaData['tipo_alerta'])
                ->activas()
                ->first();

            if (!$alertaExistente) {
                $alerta = AlertaAmbiental::create([
                    'unidad_productiva_id' => $unidad->id,
                    'tipo_alerta' => $alertaData['tipo_alerta'],
                    'severidad' => $alertaData['severidad'],
                    'titulo' => $alertaData['titulo'],
                    'descripcion' => $alertaData['descripcion'],
                    'datos_alerta' => $alertaData['datos_alerta'],
                    'recomendaciones' => $alertaData['recomendaciones'],
                    'fecha_inicio' => $alertaData['fecha_inicio'],
                ]);
                
                $alertasGuardadas[] = $alerta;
            }
        }

        return $alertasGuardadas;
    }

    /**
     * Determina severidad de sequía
     */
    private function determinarSeveridadSequia(int $diasSinLluvia): string
    {
        if ($diasSinLluvia >= 30) return 'critica';
        if ($diasSinLluvia >= 20) return 'alta';
        if ($diasSinLluvia >= 15) return 'media';
        return 'baja';
    }

    /**
     * Determina severidad de tormenta
     */
    private function determinarSeveridadTormenta(float $precipitacion24h): string
    {
        if ($precipitacion24h >= 100) return 'critica';
        if ($precipitacion24h >= 75) return 'alta';
        if ($precipitacion24h >= 50) return 'media';
        return 'baja';
    }

    /**
     * Determina severidad de estrés térmico
     */
    private function determinarSeveridadEstresTermico(float $temperaturaMaxima): string
    {
        if ($temperaturaMaxima >= 40) return 'critica';
        if ($temperaturaMaxima >= 37) return 'alta';
        if ($temperaturaMaxima >= 35) return 'media';
        return 'baja';
    }

    /**
     * Determina severidad de helada
     */
    private function determinarSeveridadHelada(float $temperaturaMinima): string
    {
        if ($temperaturaMinima <= -5) return 'critica';
        if ($temperaturaMinima <= -2) return 'alta';
        if ($temperaturaMinima <= 0) return 'media';
        return 'baja';
    }

    /**
     * Determina severidad de viento
     */
    private function determinarSeveridadViento(float $velocidadViento): string
    {
        if ($velocidadViento >= 80) return 'critica';
        if ($velocidadViento >= 60) return 'alta';
        if ($velocidadViento >= 40) return 'media';
        return 'baja';
    }

    /**
     * Determina severidad de NDVI
     */
    private function determinarSeveridadNdvi(float $ndvi): string
    {
        if ($ndvi < 0.1) return 'critica';
        if ($ndvi < 0.15) return 'alta';
        if ($ndvi < 0.2) return 'media';
        return 'baja';
    }

    /**
     * Obtiene recomendaciones para sequía
     */
    private function obtenerRecomendacionesSequia(string $severidad): array
    {
        $recomendaciones = [
            'Implementar riego de emergencia si es posible',
            'Reducir carga animal en pasturas afectadas',
            'Proporcionar sombra y agua fresca al ganado',
            'Considerar alimentación suplementaria'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'EVACUAR ganado a áreas con mejor disponibilidad de agua';
            $recomendaciones[] = 'Contactar autoridades para declaración de emergencia';
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones para tormenta
     */
    private function obtenerRecomendacionesTormenta(string $severidad): array
    {
        $recomendaciones = [
            'Proteger ganado en refugios seguros',
            'Revisar estructuras y cercos',
            'Evitar áreas bajas propensas a inundación',
            'Monitorear niveles de agua'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'EVACUAR ganado de áreas de riesgo';
            $recomendaciones[] = 'Preparar planes de contingencia';
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones para estrés térmico
     */
    private function obtenerRecomendacionesEstresTermico(string $severidad): array
    {
        $recomendaciones = [
            'Proporcionar sombra adecuada',
            'Aumentar disponibilidad de agua fresca',
            'Evitar trabajo con ganado en horas pico',
            'Monitorear signos de estrés térmico'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'Implementar sistemas de enfriamiento';
            $recomendaciones[] = 'Considerar ventilación forzada';
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones para helada
     */
    private function obtenerRecomendacionesHelada(string $severidad): array
    {
        $recomendaciones = [
            'Proporcionar refugio adecuado',
            'Aumentar alimentación energética',
            'Proteger sistemas de agua',
            'Monitorear ganado vulnerable'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'EVACUAR ganado a refugios cerrados';
            $recomendaciones[] = 'Implementar calefacción de emergencia';
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones para viento
     */
    private function obtenerRecomendacionesViento(string $severidad): array
    {
        $recomendaciones = [
            'Proteger ganado en refugios',
            'Revisar estructuras y cercos',
            'Evitar áreas expuestas',
            'Monitorear objetos voladores'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'EVACUAR ganado a refugios seguros';
            $recomendaciones[] = 'Suspender actividades al aire libre';
        }

        return $recomendaciones;
    }

    /**
     * Obtiene recomendaciones para NDVI bajo
     */
    private function obtenerRecomendacionesNdvi(string $severidad): array
    {
        $recomendaciones = [
            'Implementar rotación de pasturas',
            'Aplicar fertilizantes orgánicos',
            'Sembrar especies resistentes',
            'Mejorar manejo del pastoreo'
        ];

        if ($severidad === 'critica') {
            $recomendaciones[] = 'Revisar sistema de riego';
            $recomendaciones[] = 'Consultar especialista en suelos';
        }

        return $recomendaciones;
    }
}
