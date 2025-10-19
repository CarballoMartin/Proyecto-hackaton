@if($alertasActivas->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                🚨 Alertas Activas
            </h3>
            <span class="px-2 py-1 text-xs font-medium text-white bg-red-600 rounded-full">
                {{ $alertasActivas->count() }}
            </span>
        </div>

        {{-- Lista de alertas --}}
        <div class="space-y-3">
            @php
                $alertasMostrar = $mostrarTodas ? $alertasActivas : $alertasActivas->take(3);
            @endphp

            @foreach($alertasMostrar as $alerta)
                <div class="flex items-start p-4 rounded-lg border-2 border-{{ $alerta->obtenerColor() }}-200 bg-{{ $alerta->obtenerColor() }}-50 hover:shadow-md transition-shadow">
                    {{-- Emoji --}}
                    <span class="text-3xl mr-3 flex-shrink-0">{{ $alerta->obtenerEmoji() }}</span>
                    
                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        {{-- Título y nivel --}}
                        <div class="flex items-start justify-between mb-1">
                            <h4 class="font-semibold text-gray-900">{{ $alerta->titulo }}</h4>
                            <span class="ml-2 px-2 py-0.5 text-xs font-medium text-white bg-{{ $alerta->obtenerColor() }}-600 rounded-full whitespace-nowrap">
                                {{ ucfirst($alerta->nivel) }}
                            </span>
                        </div>
                        
                        {{-- Mensaje --}}
                        <p class="text-sm text-gray-600 mb-2">{{ $alerta->mensaje }}</p>
                        
                        {{-- Unidad Productiva y fecha --}}
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>📍 {{ $alerta->unidadProductiva->nombre }}</span>
                            <span>{{ $alerta->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- Datos de contexto --}}
                        @if($alerta->datos_contexto)
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                @if($alerta->tipo === 'sequia')
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        🌡️ {{ $alerta->datos_contexto['temperatura_promedio'] ?? 'N/A' }}°C promedio
                                    </span>
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        📅 {{ $alerta->datos_contexto['dias_sin_lluvia'] ?? 'N/A' }} días sin lluvia
                                    </span>
                                @elseif($alerta->tipo === 'tormenta')
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        🌧️ {{ $alerta->datos_contexto['lluvia_esperada'] ?? 'N/A' }}mm esperados
                                    </span>
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        💨 {{ $alerta->datos_contexto['viento_esperado'] ?? 'N/A' }}km/h
                                    </span>
                                @elseif($alerta->tipo === 'estres_termico')
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        🌡️ {{ $alerta->datos_contexto['temperatura_maxima'] ?? 'N/A' }}°C máxima
                                    </span>
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        📅 {{ $alerta->datos_contexto['dias_consecutivos'] ?? 'N/A' }} días consecutivos
                                    </span>
                                @elseif($alerta->tipo === 'helada')
                                    <span class="px-2 py-1 bg-white rounded border border-{{ $alerta->obtenerColor() }}-300">
                                        ❄️ {{ $alerta->datos_contexto['temperatura_minima'] ?? 'N/A' }}°C mínima
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Recomendaciones --}}
                        <details class="mt-3">
                            <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-800 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ver recomendaciones
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs text-gray-600 bg-white p-3 rounded border border-{{ $alerta->obtenerColor() }}-200">
                                @foreach($alerta->obtenerRecomendaciones() as $recomendacion)
                                    <li class="flex items-start">
                                        <span class="mr-2 text-{{ $alerta->obtenerColor() }}-600">•</span>
                                        <span>{{ $recomendacion }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Botón ver todas --}}
        @if($alertasActivas->count() > 3)
            <div class="mt-4 text-center">
                <button 
                    wire:click="toggleMostrarTodas"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    @if($mostrarTodas)
                        Mostrar menos
                    @else
                        Ver todas las alertas ({{ $alertasActivas->count() }})
                    @endif
                </button>
            </div>
        @endif
    </div>
@endif
