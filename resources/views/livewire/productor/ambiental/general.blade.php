<div x-data="{ currentTab: 'general' }" class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🌱 Monitoreo Ambiental</h1>
            <p class="mt-2 text-sm text-gray-600">Seguimiento climático y alertas ambientales para tus campos</p>
        </div>

        {{-- Tabs Navigation --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button 
                        @click="currentTab = 'general'" 
                        :class="currentTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">🌍</span>
                            Vista General
                        </span>
                    </button>
                    <button 
                        @click="currentTab = 'alertas'" 
                        :class="currentTab === 'alertas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">🚨</span>
                            Alertas
                            @if($estadisticas['total_alertas'] > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-600 text-white rounded-full">{{ $estadisticas['total_alertas'] }}</span>
                            @endif
                        </span>
                    </button>
                    <button 
                        @click="currentTab = 'historico'" 
                        :class="currentTab === 'historico' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">📊</span>
                            Histórico
                        </span>
                    </button>
                    <button 
                        @click="currentTab = 'ndvi'" 
                        :class="currentTab === 'ndvi' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">🛰️</span>
                            NDVI Satelital
                        </span>
                    </button>
                    <button 
                        @click="currentTab = 'suelo'" 
                        :class="currentTab === 'suelo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">🌾</span>
                            Características de Suelo
                        </span>
                    </button>
                    <button 
                        @click="currentTab = 'config'" 
                        :class="currentTab === 'config' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                    >
                        <span class="flex items-center justify-center">
                            <span class="text-xl mr-2">⚙️</span>
                            Configuración
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        <div>
            {{-- TAB 1: GENERAL --}}
            <div x-show="currentTab === 'general'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    {{-- Estadísticas Resumen --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <span class="text-2xl">🚨</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Alertas Activas</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_alertas'] }}</p>
                                @if($estadisticas['alertas_criticas'] > 0)
                                    <p class="text-xs text-red-600">{{ $estadisticas['alertas_criticas'] }} críticas</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <span class="text-2xl">🗺️</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Campos Afectados</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['unidades_afectadas'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <span class="text-2xl">🌦️</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Clima Actual</p>
                                @if($datosClima)
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($datosClima->temperatura_actual, 1) }}°C</p>
                                    <p class="text-xs text-gray-500">{{ $datosClima->obtenerDescripcionClima() }}</p>
                                @else
                                    <p class="text-sm text-gray-500">Sin datos</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Widget Clima --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🌦️ Clima Actual</h3>
                        @livewire('productor.clima-widget')
                    </div>

                    {{-- Alertas Críticas --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Alertas Prioritarias</h3>
                        @if($alertasCriticas->count() > 0)
                            <div class="space-y-3">
                                @foreach($alertasCriticas->take(3) as $alerta)
                                    <div class="p-3 rounded-lg border-2 border-{{ $alerta->obtenerColor() }}-200 bg-{{ $alerta->obtenerColor() }}-50">
                                        <div class="flex items-start">
                                            <span class="text-2xl mr-2">{{ $alerta->obtenerEmoji() }}</span>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $alerta->titulo }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">{{ $alerta->mensaje }}</p>
                                                <p class="text-xs text-gray-500 mt-2">📍 {{ $alerta->unidadProductiva->nombre }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($alertasCriticas->count() > 3)
                                    <button @click="currentTab = 'alertas'" class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2">
                                        Ver todas las alertas ({{ $alertasCriticas->count() }}) →
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <span class="text-6xl">✅</span>
                                <p class="mt-2 text-gray-600">No hay alertas críticas</p>
                                <p class="text-sm text-gray-500">Tus campos están en buenas condiciones</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Campos con Alertas --}}
                @if($unidadesConAlertas->count() > 0)
                    <div class="mt-6 bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Estado por Campo</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($unidadesConAlertas as $unidad)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $unidad['nombre'] }}</h4>
                                            <p class="text-xs text-gray-500">{{ $unidad['localidad'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($unidad['nivel_maximo'] === 'critico') bg-red-100 text-red-800
                                            @elseif($unidad['nivel_maximo'] === 'alto') bg-orange-100 text-orange-800
                                            @elseif($unidad['nivel_maximo'] === 'medio') bg-yellow-100 text-yellow-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ $unidad['cantidad_alertas'] }} alerta{{ $unidad['cantidad_alertas'] > 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        @foreach($unidad['alertas'] as $alerta)
                                            <div class="text-sm flex items-center">
                                                <span class="mr-2">{{ $alerta->obtenerEmoji() }}</span>
                                                <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $alerta->tipo)) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAB 2: ALERTAS --}}
            <div x-show="currentTab === 'alertas'" x-transition x-cloak>
                @livewire('productor.ambiental.alertas-detalle')
            </div>

            {{-- TAB 3: HISTÓRICO --}}
            <div x-show="currentTab === 'historico'" x-transition x-cloak>
                <div class="p-6 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Histórico de Datos</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta funcionalidad estará disponible próximamente.</p>
                    </div>
                </div>
            </div>

            {{-- TAB 4: NDVI SATELITAL --}}
            <div x-show="currentTab === 'ndvi'" x-transition x-cloak>
                @livewire('productor.ambiental.ndvi')
            </div>

            {{-- TAB 5: CARACTERÍSTICAS DE SUELO --}}
            <div x-show="currentTab === 'suelo'" x-transition x-cloak>
                @livewire('productor.ambiental.suelo')
            </div>

            {{-- TAB 6: CONFIGURACIÓN --}}
            <div x-show="currentTab === 'config'" x-transition x-cloak>
                <div class="p-6 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Configuración del Módulo Ambiental</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta funcionalidad estará disponible próximamente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
