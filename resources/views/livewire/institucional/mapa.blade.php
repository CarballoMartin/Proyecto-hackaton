<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Mapa Institucional</h1>
                        <p class="text-gray-600 mt-1">Visualización geográfica de participantes y actividades</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Exportar Mapa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Controles del Mapa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="show-participants" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show-participants" class="text-sm font-medium text-gray-700">Mostrar Participantes</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="show-activities" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="show-activities" class="text-sm font-medium text-gray-700">Mostrar Actividades</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="show-zones" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="show-zones" class="text-sm font-medium text-gray-700">Mostrar Zonas</label>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Todos los Municipios</option>
                        <option>Apóstoles</option>
                        <option>Concepción de la Sierra</option>
                        <option>San José</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Contenedor del Mapa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-96 flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
                <div class="text-center">
                    <svg class="w-24 h-24 text-blue-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mapa Interactivo</h3>
                    <p class="text-gray-600 mb-4">Integración con mapas en desarrollo</p>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span>Participantes</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span>Actividades</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                            <span>Zonas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadísticas del Mapa --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            
            {{-- Participantes por Zona --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Participantes por Zona</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Zona Norte</span>
                        <span class="text-sm font-medium text-gray-900">8 participantes</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Zona Centro</span>
                        <span class="text-sm font-medium text-gray-900">12 participantes</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Zona Sur</span>
                        <span class="text-sm font-medium text-gray-900">5 participantes</span>
                    </div>
                </div>
            </div>

            {{-- Actividades Recientes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Actividades Recientes</h4>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Capacitación en Apóstoles</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Reunión técnica en San José</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-600">Visita de campo en Concepción</span>
                    </div>
                </div>
            </div>

            {{-- Cobertura Geográfica --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Cobertura Geográfica</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Municipios Cubiertos</span>
                        <span class="text-sm font-medium text-gray-900">15/25</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Área Total</span>
                        <span class="text-sm font-medium text-gray-900">2,500 km²</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Densidad</span>
                        <span class="text-sm font-medium text-gray-900">0.6 part/km²</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>