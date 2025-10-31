<div>
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Análisis de Suelo</h1>
                    <p class="text-gray-600 mt-1">Características y calidad del suelo</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Fuente:</span> FAO SoilGrids
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Resolución:</span> 250m
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(empty($unidadesProductivas))
        {{-- Sin Unidades con Coordenadas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Unidades Productivas con Coordenadas</h3>
            <p class="text-gray-500 mb-4">Para analizar el suelo necesitas unidades productivas con coordenadas GPS configuradas.</p>
            <a href="{{ route('productor.unidades-productivas.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                        <select wire:model.live="unidadProductivaId" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Seleccionar unidad...</option>
                            @foreach($unidadesProductivas as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <!-- Botón de prueba simple -->
                        <button 
                            wire:click="testButton"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Test Livewire
                        </button>
                        
                        <!-- Botón principal -->
                        <button 
                            wire:click="actualizarDatos"
                            @if($actualizando || !$unidadProductivaId) disabled @endif
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg {{ $actualizando ? 'opacity-50 cursor-not-allowed' : '' }} {{ !$unidadProductivaId ? 'opacity-50 cursor-not-allowed' : '' }}">
                            @if($actualizando)
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Actualizando...
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Actualizar Datos de Suelo
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensajes de éxito/error --}}
        @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</p>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if($cargando)
        {{-- Loading --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Obteniendo datos de suelo...</p>
        </div>
        @endif

        @if(!$cargando && $caracteristicasSuelo)
        {{-- Resumen General --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Resumen General del Suelo</h3>
                <p class="text-sm text-gray-600 mt-1">Estado actual y calidad del suelo</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center
                            @if($caracteristicasSuelo->estado_general === 'excelente') bg-green-100 text-green-600
                            @elseif($caracteristicasSuelo->estado_general === 'bueno') bg-blue-100 text-blue-600
                            @elseif($caracteristicasSuelo->estado_general === 'regular') bg-yellow-100 text-yellow-600
                            @else bg-red-100 text-red-600 @endif">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">Estado General</h4>
                        <p class="text-sm text-gray-600 capitalize">{{ $caracteristicasSuelo->estado_general }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">Índice de Calidad</h4>
                        <p class="text-sm text-gray-600">{{ number_format($caracteristicasSuelo->calcularIndiceCalidad(), 1) }}/10</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">Textura</h4>
                        <p class="text-sm text-gray-600">{{ $caracteristicasSuelo->textura_clasificacion }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Características Principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            {{-- pH y Acidez --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">pH y Acidez</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-600">Valor de pH</span>
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($caracteristicasSuelo->ph_valor, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($caracteristicasSuelo->ph_valor / 14) * 100) }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600">{{ $caracteristicasSuelo->ph_clasificacion }}</p>
                </div>
            </div>

            {{-- Materia Orgánica --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Materia Orgánica</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-600">Contenido (%)</span>
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($caracteristicasSuelo->materia_organica_porcentaje, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($caracteristicasSuelo->materia_organica_porcentaje / 10) * 100) }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600">{{ $caracteristicasSuelo->materia_organica_clasificacion }}</p>
                </div>
            </div>

            {{-- Textura del Suelo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Composición del Suelo</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Arcilla</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->arcilla_porcentaje, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $caracteristicasSuelo->arcilla_porcentaje }}%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Limo</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->limo_porcentaje, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $caracteristicasSuelo->limo_porcentaje }}%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Arena</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->arena_porcentaje, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $caracteristicasSuelo->arena_porcentaje }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nutrientes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Nutrientes Principales</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Nitrógeno (N)</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->nitrogeno_mg_kg, 1) }} mg/kg</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Fósforo (P)</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->fosforo_mg_kg, 1) }} mg/kg</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Potasio (K)</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->potasio_mg_kg, 1) }} mg/kg</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">CEC</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($caracteristicasSuelo->cec_cmol_kg, 1) }} cmol/kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recomendaciones --}}
        @if(!empty($recomendaciones))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recomendaciones de Mejora</h3>
                <p class="text-sm text-gray-600 mt-1">Sugerencias para optimizar la calidad del suelo</p>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    @foreach($recomendaciones as $recomendacion)
                        <p>{{ $recomendacion }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Pasturas Recomendadas --}}
        @if(!empty($recomendacionesPasturas))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pasturas Recomendadas</h3>
                <p class="text-sm text-gray-600 mt-1">Especies de pastos adecuadas para este tipo de suelo</p>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    @foreach($recomendacionesPasturas as $pastura)
                        <p>{{ $pastura }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @elseif(!$cargando)
        {{-- Sin Datos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Datos de Suelo Disponibles</h3>
            <p class="text-gray-500 mb-4">No hay datos de suelo para esta unidad productiva.</p>
            <button 
                wire:click="actualizarDatos"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Obtener Datos de Suelo
            </button>
        </div>
        @endif
        @endif
    </div>
</div>