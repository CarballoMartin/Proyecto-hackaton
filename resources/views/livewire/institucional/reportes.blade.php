<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Reportes y Estadísticas</h1>
                        <p class="text-gray-600 mt-1">Análisis detallado de la actividad institucional - {{ $institucion->nombre }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button 
                            wire:click="mostrarFiltros = !mostrarFiltros"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Filtros
                        </button>
                        <button 
                            wire:click="refrescarDatos"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizar
                        </button>
                        <button 
                            wire:click="generarReporte"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    @if($mostrarFiltros)
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input 
                        type="date" 
                        wire:model="filtros.fecha_inicio"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input 
                        type="date" 
                        wire:model="filtros.fecha_fin"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                    <select 
                        wire:model="filtros.tipo_reporte"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="completo">Completo</option>
                        <option value="resumen">Resumen</option>
                        <option value="tendencias">Tendencias</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button 
                        wire:click="actualizarFiltros"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Aplicar
                    </button>
                    <button 
                        wire:click="limpiarFiltros"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Indicador de Carga --}}
        @if($cargando)
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
            <span class="ml-3 text-gray-600">Cargando datos...</span>
        </div>
        @endif

        {{-- Tarjetas de Resumen --}}
        @if(!$cargando)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            
            {{-- Total Participantes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Participantes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_participantes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Solicitudes Aprobadas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Solicitudes Aprobadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['solicitudes_aprobadas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Solicitudes Pendientes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Solicitudes Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['solicitudes_pendientes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Productores Verificados --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Productores Verificados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['productores_verificados'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Métricas de Rendimiento --}}
        @if(!empty($metricasRendimiento))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Métricas de Rendimiento</h3>
                <p class="text-sm text-gray-600 mt-1">Indicadores clave de desempeño institucional</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $metricasRendimiento['tasa_aprobacion_solicitudes'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600">Tasa de Aprobación</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $metricasRendimiento['tasa_crecimiento_participantes'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600">Crecimiento Participantes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $metricasRendimiento['productores_por_participante'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Productores/Participante</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-indigo-600">{{ $metricasRendimiento['eficiencia_gestion'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600">Eficiencia de Gestión</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Gráficos --}}
        @if(!empty($graficos))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            {{-- Composición por Especies --}}
            @if(isset($graficos['composicion_especies']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Composición por Especies</h3>
                <div class="h-64">
                    <canvas id="composicionEspeciesChart"></canvas>
                </div>
            </div>
            @endif

            {{-- Composición por Categorías --}}
            @if(isset($graficos['composicion_categorias']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Composición por Categorías</h3>
                <div class="h-64">
                    <canvas id="composicionCategoriasChart"></canvas>
                </div>
            </div>
            @endif

            {{-- Evolución del Stock --}}
            @if(isset($graficos['evolucion_stock']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolución del Stock</h3>
                <div class="h-64">
                    <canvas id="evolucionStockChart"></canvas>
                </div>
            </div>
            @endif

            {{-- Participantes por Mes --}}
            @if(isset($graficos['participantes_por_mes']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Participantes por Mes</h3>
                <div class="h-64">
                    <canvas id="participantesPorMesChart"></canvas>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Reporte Generado --}}
        @if(!empty($reporte))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Reporte Generado</h3>
                    <div class="flex space-x-2">
                        <button 
                            wire:click="exportarPDF"
                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </button>
                        <button 
                            wire:click="exportarExcel"
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-gray-600 mb-4">
                    <strong>Institución:</strong> {{ $reporte['institucion']['nombre'] ?? '' }}<br>
                    <strong>Período:</strong> {{ $reporte['institucion']['periodo']['inicio'] ?? '' }} - {{ $reporte['institucion']['periodo']['fin'] ?? '' }}<br>
                    <strong>Generado:</strong> {{ $reporte['institucion']['fecha_generacion'] ?? '' }}
                </div>
                
                @if(isset($reporte['resumen_ejecutivo']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Resumen Ejecutivo</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Participantes:</strong> {{ $reporte['resumen_ejecutivo']['participantes']['total'] ?? 0 }}</div>
                            <div><strong>Nuevos en período:</strong> {{ $reporte['resumen_ejecutivo']['participantes']['nuevos_periodo'] ?? 0 }}</div>
                            <div><strong>Crecimiento:</strong> {{ $reporte['resumen_ejecutivo']['participantes']['crecimiento_porcentual'] ?? 0 }}%</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Solicitudes</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Total:</strong> {{ $reporte['resumen_ejecutivo']['solicitudes']['total'] ?? 0 }}</div>
                            <div><strong>Aprobadas:</strong> {{ $reporte['resumen_ejecutivo']['solicitudes']['aprobadas'] ?? 0 }}</div>
                            <div><strong>Tasa de aprobación:</strong> {{ $reporte['resumen_ejecutivo']['solicitudes']['tasa_aprobacion'] ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Scripts para Chart.js --}}
@if(!empty($graficos))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($graficos['composicion_especies']))
    // Gráfico de Composición por Especies
    const composicionEspeciesCtx = document.getElementById('composicionEspeciesChart').getContext('2d');
    new Chart(composicionEspeciesCtx, @json($graficos['composicion_especies']));
    @endif

    @if(isset($graficos['composicion_categorias']))
    // Gráfico de Composición por Categorías
    const composicionCategoriasCtx = document.getElementById('composicionCategoriasChart').getContext('2d');
    new Chart(composicionCategoriasCtx, @json($graficos['composicion_categorias']));
    @endif

    @if(isset($graficos['evolucion_stock']))
    // Gráfico de Evolución del Stock
    const evolucionStockCtx = document.getElementById('evolucionStockChart').getContext('2d');
    new Chart(evolucionStockCtx, @json($graficos['evolucion_stock']));
    @endif

    @if(isset($graficos['participantes_por_mes']))
    // Gráfico de Participantes por Mes
    const participantesPorMesCtx = document.getElementById('participantesPorMesChart').getContext('2d');
    new Chart(participantesPorMesCtx, @json($graficos['participantes_por_mes']));
    @endif
});
</script>
@endif
