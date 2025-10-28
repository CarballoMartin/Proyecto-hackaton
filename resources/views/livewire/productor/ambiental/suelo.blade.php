<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Análisis de Suelo</h1>
                        <p class="text-gray-600 mt-1">Características y recomendaciones de suelo desde FAO SoilGrids</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button 
                            wire:click="actualizarDatos"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 disabled:opacity-50">
                            <svg wire:loading.remove wire:target="actualizarDatos" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <svg wire:loading wire:target="actualizarDatos" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizar Suelo
                        </button>
                        <button 
                            wire:click="actualizarTodasLasUnidades"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50">
                            <svg wire:loading.remove wire:target="actualizarTodasLasUnidades" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <svg wire:loading wire:target="actualizarTodasLasUnidades" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizar Todas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if($unidadesProductivas->isEmpty())
        {{-- Sin Unidades con Coordenadas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Unidades Productivas con Coordenadas</h3>
            <p class="text-gray-500 mb-4">Para analizar el suelo necesitas unidades productivas con coordenadas GPS configuradas.</p>
            <a href="{{ route('productor.unidades-productivas.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Configurar Unidades Productivas
            </a>
        </div>
        @else
        {{-- Filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                        <select 
                            wire:model="unidadSeleccionada"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @foreach($unidadesProductivas as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Indicador de Carga --}}
        @if($cargando)
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600"></div>
            <span class="ml-3 text-gray-600">Cargando datos de suelo...</span>
        </div>
        @endif

        @if(!$cargando && $caracteristicasSuelo)
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
                        <p class="text-2xl font-bold text-gray-900">{{ $caracteristicasSuelo->estado_general }}</p>
                    </div>
                </div>
            </div>

            {{-- Índice de Calidad --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Índice Calidad</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $caracteristicasSuelo->calcularIndiceCalidad() }}%</p>
                    </div>
                </div>
            </div>

            {{-- pH --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">pH</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $caracteristicasSuelo->ph_valor }}</p>
                        <p class="text-xs text-gray-500">{{ $caracteristicasSuelo->clasificacion_ph }}</p>
                    </div>
                </div>
            </div>

            {{-- Textura --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Textura</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $caracteristicasSuelo->textura_clasificacion }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Características Detalladas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            {{-- Propiedades Químicas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Propiedades Químicas</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">pH</span>
                            <div class="flex items-center">
                                <span class="font-mono text-sm">{{ $caracteristicasSuelo->ph_valor }}</span>
                                <span class="ml-2 text-xs px-2 py-1 rounded-full 
                                    @if($caracteristicasSuelo->color_ph === 'red') bg-red-100 text-red-800
                                    @elseif($caracteristicasSuelo->color_ph === 'orange') bg-orange-100 text-orange-800
                                    @elseif($caracteristicasSuelo->color_ph === 'green') bg-green-100 text-green-800
                                    @elseif($caracteristicasSuelo->color_ph === 'blue') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ $caracteristicasSuelo->clasificacion_ph }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Materia Orgánica</span>
                            <div class="flex items-center">
                                <span class="font-mono text-sm">{{ $caracteristicasSuelo->materia_organica_porcentaje }}%</span>
                                <span class="ml-2 text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                                    {{ $caracteristicasSuelo->clasificacion_materia_organica }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Nitrógeno Total</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->nitrogeno_total }} g/kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Fósforo Disponible</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->fosforo_disponible }} mg/kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Potasio Intercambiable</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->potasio_intercambiable }} cmol/kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">CIC</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->capacidad_intercambio_cationico }} cmol/kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Saturación Bases</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->saturacion_bases }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Propiedades Físicas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Propiedades Físicas</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Arcilla</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->arcilla_porcentaje }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Limo</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->limo_porcentaje }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Arena</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->arena_porcentaje }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Capacidad Retención Agua</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->capacidad_retencion_agua }} mm/m</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Densidad Aparente</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->densidad_aparente }} g/cm³</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Profundidad Análisis</span>
                            <span class="font-mono text-sm">{{ $caracteristicasSuelo->profundidad_cm }} cm</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Fuente Datos</span>
                            <span class="text-sm font-medium text-gray-900">{{ strtoupper($caracteristicasSuelo->fuente_datos) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Fecha Consulta</span>
                            <span class="text-sm text-gray-900">{{ $caracteristicasSuelo->fecha_consulta->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recomendaciones de Mejoramiento --}}
        @if(!empty($recomendaciones))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">💡 Recomendaciones de Mejoramiento</h3>
                <p class="text-sm text-gray-600 mt-1">Acciones sugeridas para optimizar la calidad del suelo</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recomendaciones as $recomendacion)
                    <div class="flex items-start space-x-3 p-4 rounded-lg 
                        @if($recomendacion['prioridad'] === 'alta') bg-red-50 border border-red-200
                        @elseif($recomendacion['prioridad'] === 'media') bg-yellow-50 border border-yellow-200
                        @else bg-green-50 border border-green-200 @endif">
                        <div class="flex-shrink-0">
                            @if($recomendacion['prioridad'] === 'alta')
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            @elseif($recomendacion['prioridad'] === 'media')
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $recomendacion['problema'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $recomendacion['solucion'] }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2
                                @if($recomendacion['prioridad'] === 'alta') bg-red-100 text-red-800
                                @elseif($recomendacion['prioridad'] === 'media') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($recomendacion['prioridad']) }} prioridad
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Recomendaciones de Pasturas --}}
        @if(!empty($recomendacionesPasturas))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">🌱 Recomendaciones de Pasturas</h3>
                <p class="text-sm text-gray-600 mt-1">Especies recomendadas según las características del suelo</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($recomendacionesPasturas as $pastura)
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200">
                        <h4 class="text-sm font-medium text-gray-900">{{ $pastura['nombre'] }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $pastura['descripcion'] }}</p>
                        @if(isset($pastura['tolerancia_ph']))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2 bg-blue-100 text-blue-800">
                                Tolerante a pH {{ $pastura['tolerancia_ph'] }}
                            </span>
                        @endif
                        @if(isset($pastura['tolerancia_textura']))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2 bg-purple-100 text-purple-800">
                                Ideal para suelos {{ $pastura['tolerancia_textura'] }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @elseif(!$cargando)
        {{-- Sin Datos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Datos de Suelo Disponibles</h3>
            <p class="text-gray-500 mb-4">No hay datos de suelo para esta unidad productiva.</p>
            <button 
                wire:click="actualizarDatos"
                class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Obtener Datos de Suelo
            </button>
        </div>
        @endif
    </div>
</div>
