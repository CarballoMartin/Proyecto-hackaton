<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard Ambiental Integrado</h1>
                        <p class="text-gray-600 mt-1">Monitoreo completo de clima, vegetación y suelo</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button 
                            wire:click="actualizarDatos"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                            <svg wire:loading.remove wire:target="actualizarDatos" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <svg wire:loading wire:target="actualizarDatos" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizar Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if($cargando)
        {{-- Indicador de Carga --}}
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Cargando dashboard ambiental...</span>
        </div>
        @elseif(!empty($dashboard))

        {{-- Filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                        <select 
                            wire:model="filtroUnidad"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="todas">Todas las unidades</option>
                            @foreach($this->obtenerUnidadesProductivas() as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período de Análisis</label>
                        <select 
                            wire:model="filtroPeriodo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen General --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            
            {{-- Estado General --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Estado General</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dashboard['resumen_general']['estado_general'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Cobertura de Datos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cobertura Datos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dashboard['resumen_general']['cobertura_datos'] }}%</p>
                    </div>
                </div>
            </div>

            {{-- Alertas Críticas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Alertas Críticas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dashboard['resumen_general']['alertas_criticas'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Índice Calidad Promedio --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Calidad Promedio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dashboard['resumen_general']['indice_calidad_promedio'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Métricas por Categoría --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            {{-- Métricas de Clima --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                        Condiciones Climáticas
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Temperatura Actual</span>
                            <span class="font-mono text-sm font-bold">{{ $dashboard['metricas_clima']['temperatura_actual'] }}°C</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Precipitación 7 días</span>
                            <span class="font-mono text-sm">{{ $dashboard['metricas_clima']['precipitacion_7dias'] }}mm</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Humedad Relativa</span>
                            <span class="font-mono text-sm">{{ $dashboard['metricas_clima']['humedad_relativa'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Velocidad Viento</span>
                            <span class="font-mono text-sm">{{ $dashboard['metricas_clima']['viento_velocidad'] }} km/h</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Índice Sequía</span>
                            <span class="font-mono text-sm 
                                @if($dashboard['metricas_clima']['indice_sequia'] >= 3) text-red-600
                                @elseif($dashboard['metricas_clima']['indice_sequia'] >= 2) text-orange-600
                                @elseif($dashboard['metricas_clima']['indice_sequia'] >= 1) text-yellow-600
                                @else text-green-600 @endif">
                                {{ $dashboard['metricas_clima']['indice_sequia'] }}/4
                            </span>
                        </div>
                        @if($dashboard['metricas_clima']['estres_termico'])
                        <div class="flex items-center text-red-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-sm font-medium">Estrés Térmico Detectado</span>
                        </div>
                        @endif
                        @if($dashboard['metricas_clima']['helada_riesgo'])
                        <div class="flex items-center text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-sm font-medium">Riesgo de Helada</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Métricas de NDVI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Índices de Vegetación
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">NDVI Promedio</span>
                            <span class="font-mono text-sm font-bold">{{ $dashboard['metricas_ndvi']['ndvi_promedio'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Vegetación Saludable</span>
                            <span class="font-mono text-sm text-green-600">{{ $dashboard['metricas_ndvi']['vegetacion_saludable'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Vegetación Degradada</span>
                            <span class="font-mono text-sm text-red-600">{{ $dashboard['metricas_ndvi']['vegetacion_degradada'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tendencia Mejorando</span>
                            <span class="font-mono text-sm text-green-600">{{ $dashboard['metricas_ndvi']['tendencia_mejorando'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tendencia Empeorando</span>
                            <span class="font-mono text-sm text-red-600">{{ $dashboard['metricas_ndvi']['tendencia_empeorando'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Datos Recientes</span>
                            <span class="font-mono text-sm">{{ $dashboard['metricas_ndvi']['datos_recientes'] }} unidades</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Métricas de Suelo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Características del Suelo
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">pH Promedio</span>
                            <span class="font-mono text-sm font-bold">{{ $dashboard['metricas_suelo']['ph_promedio'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Materia Orgánica</span>
                            <span class="font-mono text-sm">{{ $dashboard['metricas_suelo']['materia_organica_promedio'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Suelos Ácidos</span>
                            <span class="font-mono text-sm text-red-600">{{ $dashboard['metricas_suelo']['suelos_acidicos'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Suelos Alcalinos</span>
                            <span class="font-mono text-sm text-blue-600">{{ $dashboard['metricas_suelo']['suelos_alcalinos'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Textura Óptima</span>
                            <span class="font-mono text-sm text-green-600">{{ $dashboard['metricas_suelo']['textura_optima'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Fertilidad Alta</span>
                            <span class="font-mono text-sm text-green-600">{{ $dashboard['metricas_suelo']['fertilidad_alta'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Necesita Mejoras</span>
                            <span class="font-mono text-sm text-orange-600">{{ $dashboard['metricas_suelo']['necesita_mejoras'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Certificación Ambiental --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Certificación Ambiental Campo Verde
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ $dashboard['certificacion']['categoria'] }}</h4>
                            <span class="text-2xl font-bold text-gray-900">{{ $dashboard['certificacion']['porcentaje'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ $dashboard['certificacion']['porcentaje'] }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ $dashboard['certificacion']['puntos_totales'] }} de {{ $dashboard['certificacion']['puntos_maximos'] }} puntos
                        </p>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900 mb-2">Desglose de Puntos:</p>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Base</span>
                                    <span class="font-mono">{{ $dashboard['certificacion']['desglose']['base'] }} pts</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NDVI</span>
                                    <span class="font-mono">{{ $dashboard['certificacion']['desglose']['ndvi'] }} pts</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Suelo</span>
                                    <span class="font-mono">{{ $dashboard['certificacion']['desglose']['suelo'] }} pts</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Clima</span>
                                    <span class="font-mono">{{ $dashboard['certificacion']['desglose']['clima'] }} pts</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Alertas</span>
                                    <span class="font-mono">{{ $dashboard['certificacion']['desglose']['alertas'] }} pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-24 h-24 mx-auto mb-4 rounded-full flex items-center justify-center
                                @if($dashboard['certificacion']['nivel'] === 'avanzado') bg-gradient-to-r from-green-500 to-emerald-600
                                @elseif($dashboard['certificacion']['nivel'] === 'intermedio') bg-gradient-to-r from-yellow-500 to-orange-600
                                @else bg-gradient-to-r from-gray-500 to-gray-600 @endif">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <p class="text-lg font-medium text-gray-900 capitalize">{{ $dashboard['certificacion']['nivel'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas Activas --}}
        @if($alertasActivas->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Alertas Ambientales Activas ({{ $alertasActivas->count() }})
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($alertasActivas as $alerta)
                    <div class="flex items-start space-x-4 p-4 rounded-lg border
                        @if($alerta->severidad === 'critica') bg-red-50 border-red-200
                        @elseif($alerta->severidad === 'alta') bg-orange-50 border-orange-200
                        @elseif($alerta->severidad === 'media') bg-yellow-50 border-yellow-200
                        @else bg-green-50 border-green-200 @endif">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">{{ $alerta->icono_tipo }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900">{{ $alerta->titulo }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($alerta->severidad === 'critica') bg-red-100 text-red-800
                                    @elseif($alerta->severidad === 'alta') bg-orange-100 text-orange-800
                                    @elseif($alerta->severidad === 'media') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($alerta->severidad) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $alerta->descripcion }}</p>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $alerta->unidadProductiva->nombre }} • {{ $alerta->fecha_inicio->format('d/m/Y') }}
                                @if($alerta->duracion > 0)
                                    • {{ $alerta->duracion }} días
                                @endif
                            </p>
                            @if(!empty($alerta->recomendaciones))
                            <div class="mt-3">
                                <p class="text-xs font-medium text-gray-700 mb-1">Recomendaciones:</p>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    @foreach(array_slice($alerta->recomendaciones, 0, 3) as $recomendacion)
                                    <li>• {{ $recomendacion }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0 flex space-x-2">
                            @if(!$alerta->notificada)
                            <button 
                                wire:click="marcarAlertaComoLeida({{ $alerta->id }})"
                                class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                                Marcar Leída
                            </button>
                            @endif
                            <button 
                                wire:click="desactivarAlerta({{ $alerta->id }})"
                                class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                                Desactivar
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Recomendaciones Generales --}}
        @if(!empty($dashboard['recomendaciones']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Recomendaciones Inteligentes
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($dashboard['recomendaciones'] as $recomendacion)
                    <div class="flex items-start space-x-3 p-4 rounded-lg
                        @if($recomendacion['tipo'] === 'urgente') bg-red-50 border border-red-200
                        @elseif($recomendacion['tipo'] === 'importante') bg-orange-50 border border-orange-200
                        @else bg-blue-50 border border-blue-200 @endif">
                        <div class="flex-shrink-0">
                            @if($recomendacion['tipo'] === 'urgente')
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            @elseif($recomendacion['tipo'] === 'importante')
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $recomendacion['titulo'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $recomendacion['descripcion'] }}</p>
                            <p class="text-sm font-medium text-gray-900 mt-2">{{ $recomendacion['accion'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @else
        {{-- Sin Datos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Datos del Dashboard Disponibles</h3>
            <p class="text-gray-500 mb-4">No hay datos suficientes para generar el dashboard ambiental integrado.</p>
            <button 
                wire:click="actualizarDatos"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Generar Dashboard
            </button>
        </div>
        @endif
    </div>
</div>
