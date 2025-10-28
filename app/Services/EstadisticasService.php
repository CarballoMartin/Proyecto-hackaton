<?php

namespace App\Services;

use App\Models\Institucion;
use App\Models\Productor;
use App\Models\SolicitudVerificacion;
use App\Models\User;
use App\Models\InstitucionalParticipante;
use App\Models\UnidadProductiva;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EstadisticasService
{
    protected $stockHistoryService;

    public function __construct(StockHistoryService $stockHistoryService)
    {
        $this->stockHistoryService = $stockHistoryService;
    }

    /**
     * Obtiene los KPIs globales para el panel de administración.
     *
     * @return array
     */
    public function getKpisGlobales(): array
    {
        return Cache::remember('estadisticas.kpis_globales', 300, function () {
            return [
                'total_productores' => Productor::count(),
                'productores_activos' => User::where('rol', 'productor')
                    ->where('last_login_at', '>=', Carbon::now()->subDays(90))
                    ->count(),
                'total_instituciones' => Institucion::where('activa', true)->count(),
                'solicitudes_pendientes' => SolicitudVerificacion::where('estado', 'pendiente')->count(),
                'total_unidades_productivas' => UnidadProductiva::count(),
                'unidades_activas' => UnidadProductiva::where('activa', true)->count(),
            ];
        });
    }

    /**
     * Obtiene estadísticas específicas para una institución.
     *
     * @param int $institucionId
     * @return array
     */
    public function getEstadisticasInstitucionales(int $institucionId): array
    {
        return Cache::remember("estadisticas.institucion.{$institucionId}", 300, function () use ($institucionId) {
            return [
                'total_participantes' => InstitucionalParticipante::where('institucion_id', $institucionId)
                    ->where('activo', true)->count(),
                'total_solicitudes' => SolicitudVerificacion::where('institucion_id', $institucionId)->count(),
                'solicitudes_aprobadas' => SolicitudVerificacion::where('institucion_id', $institucionId)
                    ->where('estado', 'aprobada')->count(),
                'solicitudes_pendientes' => SolicitudVerificacion::where('institucion_id', $institucionId)
                    ->where('estado', 'pendiente')->count(),
                'solicitudes_rechazadas' => SolicitudVerificacion::where('institucion_id', $institucionId)
                    ->where('estado', 'rechazada')->count(),
                'productores_verificados' => Productor::whereHas('solicitudesVerificacion', function($query) use ($institucionId) {
                    $query->where('institucion_id', $institucionId)
                          ->where('estado', 'aprobada');
                })->count(),
                'participantes_nuevos_mes' => InstitucionalParticipante::where('institucion_id', $institucionId)
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            ];
        });
    }

    /**
     * Obtiene la composición por especie para gráficos.
     *
     * @param int|null $institucionId Si es null, obtiene datos globales
     * @return array
     */
    public function getComposicionPorEspecie(?int $institucionId = null): array
    {
        $cacheKey = $institucionId ? "composicion.especie.institucion.{$institucionId}" : 'composicion.especie.global';
        
        return Cache::remember($cacheKey, 600, function () use ($institucionId) {
            $query = DB::table('stock_histories as sh')
                ->join('unidades_productivas as up', 'sh.unidad_productiva_id', '=', 'up.id')
                ->join('productores as p', 'up.productor_id', '=', 'p.id')
                ->where('sh.fecha', '>=', Carbon::now()->subMonths(6))
                ->select('sh.especie', DB::raw('SUM(sh.cantidad) as total'))
                ->groupBy('sh.especie');

            if ($institucionId) {
                $query->join('institucional_participantes as ip', 'p.id', '=', 'ip.productor_id')
                      ->where('ip.institucion_id', $institucionId)
                      ->where('ip.activo', true);
            }

            return $query->get()->pluck('total', 'especie')->toArray();
        });
    }

    /**
     * Obtiene la composición por categoría (reproductores, crías, etc.).
     *
     * @param int|null $institucionId
     * @return array
     */
    public function getComposicionPorCategoria(?int $institucionId = null): array
    {
        $cacheKey = $institucionId ? "composicion.categoria.institucion.{$institucionId}" : 'composicion.categoria.global';
        
        return Cache::remember($cacheKey, 600, function () use ($institucionId) {
            $query = DB::table('stock_histories as sh')
                ->join('unidades_productivas as up', 'sh.unidad_productiva_id', '=', 'up.id')
                ->join('productores as p', 'up.productor_id', '=', 'p.id')
                ->where('sh.fecha', '>=', Carbon::now()->subMonths(6))
                ->select('sh.categoria', DB::raw('SUM(sh.cantidad) as total'))
                ->groupBy('sh.categoria');

            if ($institucionId) {
                $query->join('institucional_participantes as ip', 'p.id', '=', 'ip.productor_id')
                      ->where('ip.institucion_id', $institucionId)
                      ->where('ip.activo', true);
            }

            return $query->get()->pluck('total', 'categoria')->toArray();
        });
    }

    /**
     * Obtiene la evolución del stock mensual.
     *
     * @param int|null $institucionId
     * @param int $meses
     * @return array
     */
    public function getEvolucionStockMensual(?int $institucionId = null, int $meses = 12): array
    {
        $cacheKey = $institucionId ? "evolucion.stock.institucion.{$institucionId}.{$meses}" : "evolucion.stock.global.{$meses}";
        
        return Cache::remember($cacheKey, 300, function () use ($institucionId, $meses) {
            $fechaInicio = Carbon::now()->subMonths($meses)->startOfMonth();
            
            $query = DB::table('stock_histories as sh')
                ->join('unidades_productivas as up', 'sh.unidad_productiva_id', '=', 'up.id')
                ->join('productores as p', 'up.productor_id', '=', 'p.id')
                ->where('sh.fecha', '>=', $fechaInicio)
                ->select(
                    DB::raw('DATE_FORMAT(sh.fecha, "%Y-%m") as mes'),
                    DB::raw('SUM(sh.cantidad) as total')
                )
                ->groupBy('mes')
                ->orderBy('mes');

            if ($institucionId) {
                $query->join('institucional_participantes as ip', 'p.id', '=', 'ip.productor_id')
                      ->where('ip.institucion_id', $institucionId)
                      ->where('ip.activo', true);
            }

            return $query->get()->pluck('total', 'mes')->toArray();
        });
    }

    /**
     * Obtiene el resumen por unidad productiva.
     *
     * @param int|null $institucionId
     * @return array
     */
    public function getResumenPorUnidadProductiva(?int $institucionId = null): array
    {
        $cacheKey = $institucionId ? "resumen.up.institucion.{$institucionId}" : 'resumen.up.global';
        
        return Cache::remember($cacheKey, 600, function () use ($institucionId) {
            $query = DB::table('unidades_productivas as up')
                ->join('productores as p', 'up.productor_id', '=', 'p.id')
                ->leftJoin('stock_histories as sh', function($join) {
                    $join->on('up.id', '=', 'sh.unidad_productiva_id')
                         ->where('sh.fecha', '>=', Carbon::now()->subMonths(3));
                })
                ->select(
                    'up.nombre',
                    'p.nombre as productor',
                    DB::raw('COUNT(DISTINCT sh.especie) as especies'),
                    DB::raw('SUM(sh.cantidad) as total_stock')
                )
                ->groupBy('up.id', 'up.nombre', 'p.nombre');

            if ($institucionId) {
                $query->join('institucional_participantes as ip', 'p.id', '=', 'ip.productor_id')
                      ->where('ip.institucion_id', $institucionId)
                      ->where('ip.activo', true);
            }

            return $query->get()->toArray();
        });
    }

    /**
     * Obtiene estadísticas de participantes por mes para una institución.
     *
     * @param int $institucionId
     * @param int $meses
     * @return array
     */
    public function getParticipantesPorMes(int $institucionId, int $meses = 6): array
    {
        return Cache::remember("participantes.mes.institucion.{$institucionId}.{$meses}", 300, function () use ($institucionId, $meses) {
            return InstitucionalParticipante::where('institucion_id', $institucionId)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
                ->where('created_at', '>=', Carbon::now()->subMonths($meses))
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->pluck('total', 'mes')
                ->toArray();
        });
    }

    /**
     * Obtiene estadísticas de solicitudes por estado para una institución.
     *
     * @param int $institucionId
     * @return array
     */
    public function getSolicitudesPorEstado(int $institucionId): array
    {
        return Cache::remember("solicitudes.estado.institucion.{$institucionId}", 300, function () use ($institucionId) {
            return SolicitudVerificacion::where('institucion_id', $institucionId)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get()
                ->pluck('total', 'estado')
                ->toArray();
        });
    }

    /**
     * Obtiene actividad reciente de una institución.
     *
     * @param int $institucionId
     * @param int $limite
     * @return array
     */
    public function getActividadReciente(int $institucionId, int $limite = 10): array
    {
        return Cache::remember("actividad.reciente.institucion.{$institucionId}.{$limite}", 60, function () use ($institucionId, $limite) {
            $actividades = collect();

            // Participantes recientes
            $participantes = InstitucionalParticipante::where('institucion_id', $institucionId)
                ->with('productor')
                ->latest()
                ->limit($limite)
                ->get()
                ->map(function ($participante) {
                    return [
                        'tipo' => 'participante_nuevo',
                        'descripcion' => "Nuevo participante: {$participante->productor->nombre}",
                        'fecha' => $participante->created_at,
                        'icono' => 'user-plus',
                        'color' => 'blue'
                    ];
                });

            // Solicitudes recientes
            $solicitudes = SolicitudVerificacion::where('institucion_id', $institucionId)
                ->with('productor')
                ->latest()
                ->limit($limite)
                ->get()
                ->map(function ($solicitud) {
                    return [
                        'tipo' => 'solicitud_' . $solicitud->estado,
                        'descripcion' => "Solicitud {$solicitud->estado}: {$solicitud->productor->nombre}",
                        'fecha' => $solicitud->updated_at,
                        'icono' => $solicitud->estado === 'aprobada' ? 'check-circle' : 'clock',
                        'color' => $solicitud->estado === 'aprobada' ? 'green' : 'yellow'
                    ];
                });

            return $actividades->merge($participantes)->merge($solicitudes)
                ->sortByDesc('fecha')
                ->take($limite)
                ->values()
                ->toArray();
        });
    }

    /**
     * Limpia todas las cachés de estadísticas.
     *
     * @return void
     */
    public function limpiarCache(): void
    {
        $patterns = [
            'estadisticas.*',
            'composicion.*',
            'evolucion.*',
            'resumen.*',
            'participantes.*',
            'solicitudes.*',
            'actividad.*'
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Limpia caché específico de una institución.
     *
     * @param int $institucionId
     * @return void
     */
    public function limpiarCacheInstitucion(int $institucionId): void
    {
        $patterns = [
            "estadisticas.institucion.{$institucionId}",
            "composicion.*.institucion.{$institucionId}",
            "evolucion.*.institucion.{$institucionId}",
            "resumen.*.institucion.{$institucionId}",
            "participantes.*.institucion.{$institucionId}",
            "solicitudes.*.institucion.{$institucionId}",
            "actividad.*.institucion.{$institucionId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
