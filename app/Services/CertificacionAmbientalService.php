<?php

namespace App\Services;

use App\Models\Productor;
use App\Models\UnidadProductiva;
use Illuminate\Support\Facades\DB;

class CertificacionAmbientalService
{
    /**
     * Niveles de certificación
     */
    const NIVEL_BRONCE = 'bronce';
    const NIVEL_PLATA = 'plata';
    const NIVEL_ORO = 'oro';
    const NIVEL_PLATINO = 'platino';

    /**
     * Umbrales de puntos para cada nivel
     */
    const UMBRALES = [
        self::NIVEL_BRONCE => 50,
        self::NIVEL_PLATA => 100,
        self::NIVEL_ORO => 200,
        self::NIVEL_PLATINO => 300,
    ];

    /**
     * Calcula la certificación ambiental de un productor
     */
    public function calcularCertificacion(Productor $productor): array
    {
        $puntaje = 0;
        $badges = [];
        $metricas = [];

        // 1. Puntos por gestión del agua (max 80 puntos)
        $puntosAgua = $this->evaluarGestionAgua($productor);
        $puntaje += $puntosAgua;
        $metricas['agua'] = $puntosAgua;
        
        if ($puntosAgua >= 50) {
            $badges[] = [
                'id' => 'guardian_agua',
                'nombre' => 'Guardián del Agua',
                'descripcion' => 'Excelente gestión de recursos hídricos',
                'icono' => '💧',
                'color' => 'blue'
            ];
        }

        // 2. Puntos por biodiversidad (max 70 puntos)
        $puntosBiodiversidad = $this->evaluarBiodiversidad($productor);
        $puntaje += $puntosBiodiversidad;
        $metricas['biodiversidad'] = $puntosBiodiversidad;
        
        if ($puntosBiodiversidad >= 45) {
            $badges[] = [
                'id' => 'protector_biodiversidad',
                'nombre' => 'Protector de la Biodiversidad',
                'descripcion' => 'Preservación activa del ecosistema',
                'icono' => '🦋',
                'color' => 'green'
            ];
        }

        // 3. Puntos por eficiencia productiva (max 90 puntos)
        $puntosEficiencia = $this->evaluarEficienciaProductiva($productor);
        $puntaje += $puntosEficiencia;
        $metricas['eficiencia'] = $puntosEficiencia;
        
        if ($puntosEficiencia >= 60) {
            $badges[] = [
                'id' => 'productor_eficiente',
                'nombre' => 'Productor Eficiente',
                'descripcion' => 'Optimización de recursos productivos',
                'icono' => '⚡',
                'color' => 'yellow'
            ];
        }

        // 4. Puntos por manejo sostenible (max 60 puntos)
        $puntosSostenibilidad = $this->evaluarManejoSostenible($productor);
        $puntaje += $puntosSostenibilidad;
        $metricas['sostenibilidad'] = $puntosSostenibilidad;
        
        if ($puntosSostenibilidad >= 40) {
            $badges[] = [
                'id' => 'eco_ganadero',
                'nombre' => 'Eco-Ganadero',
                'descripcion' => 'Prácticas sustentables implementadas',
                'icono' => '🌱',
                'color' => 'green'
            ];
        }

        // Determinar nivel de certificación
        $nivel = $this->determinarNivel($puntaje);
        $siguienteNivel = $this->obtenerSiguienteNivel($nivel);

        return [
            'puntaje_total' => $puntaje,
            'puntaje_maximo' => 300,
            'nivel' => $nivel,
            'siguiente_nivel' => $siguienteNivel,
            'porcentaje' => round(($puntaje / 300) * 100, 1),
            'badges' => $badges,
            'metricas' => $metricas,
            'puntos_para_siguiente' => $siguienteNivel ? self::UMBRALES[$siguienteNivel] - $puntaje : 0,
        ];
    }

    /**
     * Evalúa la gestión del agua
     */
    private function evaluarGestionAgua(Productor $productor): int
    {
        $puntos = 0;
        $unidades = $productor->unidadesProductivas;

        if ($unidades->isEmpty()) {
            return 0;
        }

        // Acceso a agua potable para humanos (20 puntos)
        $conAguaPotable = $unidades->where('agua_humano_en_casa', true)->count();
        $puntos += ($conAguaPotable / $unidades->count()) * 20;

        // Distancia a fuente de agua animal (30 puntos)
        $distanciaPromedio = $unidades->avg('agua_animal_distancia');
        if ($distanciaPromedio <= 500) {
            $puntos += 30;
        } elseif ($distanciaPromedio <= 1000) {
            $puntos += 20;
        } elseif ($distanciaPromedio <= 2000) {
            $puntos += 10;
        }

        // Fuentes de agua diversificadas (30 puntos)
        $fuentesDiferentes = $unidades->pluck('agua_animal_fuente_id')->unique()->count();
        if ($fuentesDiferentes >= 3) {
            $puntos += 30;
        } elseif ($fuentesDiferentes == 2) {
            $puntos += 20;
        } elseif ($fuentesDiferentes == 1) {
            $puntos += 10;
        }

        return min(80, round($puntos));
    }

    /**
     * Evalúa la biodiversidad
     */
    private function evaluarBiodiversidad(Productor $productor): int
    {
        $puntos = 0;
        $unidades = $productor->unidadesProductivas;

        if ($unidades->isEmpty()) {
            return 0;
        }

        // Diversidad de razas (30 puntos)
        $razasDiferentes = DB::table('stock_actual')
            ->whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->distinct('raza_id')
            ->count('raza_id');

        if ($razasDiferentes >= 5) {
            $puntos += 30;
        } elseif ($razasDiferentes >= 3) {
            $puntos += 20;
        } elseif ($razasDiferentes >= 2) {
            $puntos += 10;
        }

        // Diversidad de pastos (20 puntos)
        $tiposPasto = $unidades->pluck('tipo_pasto_predominante_id')->unique()->count();
        if ($tiposPasto >= 3) {
            $puntos += 20;
        } elseif ($tiposPasto == 2) {
            $puntos += 10;
        }

        // Presencia de forrajeras (20 puntos)
        $conForrajeras = $unidades->whereNotNull('forrajeras_predominante')->count();
        $puntos += ($conForrajeras / $unidades->count()) * 20;

        return min(70, round($puntos));
    }

    /**
     * Evalúa la eficiencia productiva
     */
    private function evaluarEficienciaProductiva(Productor $productor): int
    {
        $puntos = 0;
        $unidades = $productor->unidadesProductivas;

        if ($unidades->isEmpty()) {
            return 0;
        }

        // Carga animal óptima (40 puntos)
        $totalAnimales = DB::table('stock_actual')
            ->whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->sum('cantidad_actual');
        
        $superficieTotal = $unidades->sum('superficie');
        
        if ($superficieTotal > 0) {
            $cargaAnimal = $totalAnimales / $superficieTotal;
            
            // Carga óptima entre 0.5 y 2 animales/ha
            if ($cargaAnimal >= 0.5 && $cargaAnimal <= 2) {
                $puntos += 40;
            } elseif ($cargaAnimal > 0 && $cargaAnimal < 0.5) {
                $puntos += 20; // Sub-aprovechado
            } elseif ($cargaAnimal > 2 && $cargaAnimal <= 3) {
                $puntos += 20; // Ligeramente sobre-pastoreo
            }
        }

        // Diversidad de especies (30 puntos)
        $especiesDiferentes = DB::table('stock_actual')
            ->whereIn('unidad_productiva_id', $unidades->pluck('id'))
            ->distinct('especie_id')
            ->count('especie_id');

        if ($especiesDiferentes >= 3) {
            $puntos += 30;
        } elseif ($especiesDiferentes == 2) {
            $puntos += 20;
        } elseif ($especiesDiferentes == 1) {
            $puntos += 10;
        }

        // Completitud de datos (20 puntos)
        $unidadesCompletas = $unidades->where('completo', true)->count();
        $puntos += ($unidadesCompletas / $unidades->count()) * 20;

        return min(90, round($puntos));
    }

    /**
     * Evalúa el manejo sostenible
     */
    private function evaluarManejoSostenible(Productor $productor): int
    {
        $puntos = 0;
        $unidades = $productor->unidadesProductivas;

        if ($unidades->isEmpty()) {
            return 0;
        }

        // Múltiples unidades productivas (rotación) (20 puntos)
        $cantidadUnidades = $unidades->count();
        if ($cantidadUnidades >= 5) {
            $puntos += 20;
        } elseif ($cantidadUnidades >= 3) {
            $puntos += 15;
        } elseif ($cantidadUnidades >= 2) {
            $puntos += 10;
        }

        // Ubicación geográfica definida (15 puntos)
        $conUbicacion = $unidades->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->count();
        $puntos += ($conUbicacion / $unidades->count()) * 15;

        // Identificación formal (15 puntos)
        $conIdentificacion = $unidades->whereNotNull('tipo_identificador_id')->count();
        $puntos += ($conIdentificacion / $unidades->count()) * 15;

        // Observaciones y seguimiento (10 puntos)
        $conObservaciones = $unidades->whereNotNull('observaciones')->count();
        $puntos += ($conObservaciones / $unidades->count()) * 10;

        return min(60, round($puntos));
    }

    /**
     * Determina el nivel según el puntaje
     */
    private function determinarNivel(int $puntaje): string
    {
        if ($puntaje >= self::UMBRALES[self::NIVEL_PLATINO]) {
            return self::NIVEL_PLATINO;
        } elseif ($puntaje >= self::UMBRALES[self::NIVEL_ORO]) {
            return self::NIVEL_ORO;
        } elseif ($puntaje >= self::UMBRALES[self::NIVEL_PLATA]) {
            return self::NIVEL_PLATA;
        } elseif ($puntaje >= self::UMBRALES[self::NIVEL_BRONCE]) {
            return self::NIVEL_BRONCE;
        }
        
        return 'sin_certificacion';
    }

    /**
     * Obtiene el siguiente nivel
     */
    private function obtenerSiguienteNivel(string $nivelActual): ?string
    {
        $niveles = [
            'sin_certificacion' => self::NIVEL_BRONCE,
            self::NIVEL_BRONCE => self::NIVEL_PLATA,
            self::NIVEL_PLATA => self::NIVEL_ORO,
            self::NIVEL_ORO => self::NIVEL_PLATINO,
            self::NIVEL_PLATINO => null,
        ];

        return $niveles[$nivelActual] ?? null;
    }

    /**
     * Obtiene información del nivel
     */
    public function obtenerInfoNivel(string $nivel): array
    {
        $niveles = [
            'sin_certificacion' => [
                'nombre' => 'Sin Certificación',
                'icono' => '⚪',
                'color' => 'gray',
                'descripcion' => 'Comienza tu viaje sustentable',
            ],
            self::NIVEL_BRONCE => [
                'nombre' => 'Bronce',
                'icono' => '🥉',
                'color' => 'orange',
                'descripcion' => 'Primeros pasos en sustentabilidad',
            ],
            self::NIVEL_PLATA => [
                'nombre' => 'Plata',
                'icono' => '🥈',
                'color' => 'gray',
                'descripcion' => 'Buenas prácticas implementadas',
            ],
            self::NIVEL_ORO => [
                'nombre' => 'Oro',
                'icono' => '🥇',
                'color' => 'yellow',
                'descripcion' => 'Excelencia ambiental',
            ],
            self::NIVEL_PLATINO => [
                'nombre' => 'Platino',
                'icono' => '💎',
                'color' => 'purple',
                'descripcion' => 'Líder en sustentabilidad',
            ],
        ];

        return $niveles[$nivel] ?? $niveles['sin_certificacion'];
    }
}




