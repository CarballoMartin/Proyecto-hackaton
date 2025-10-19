<div class="relative">
    {{-- Botón de campana --}}
    <button 
        wire:click="toggleLista"
        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 rounded-lg transition-colors"
        aria-label="Alertas ambientales"
        type="button"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        {{-- Badge con contador --}}
        @if($cantidadNoLeidas > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full animate-pulse">
                {{ $cantidadNoLeidas }}
            </span>
        @endif
    </button>

    {{-- Panel de alertas (dropdown) --}}
    @if($mostrarLista)
        <div 
            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
            wire:click.outside="mostrarLista = false"
            x-data
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    🚨 Alertas Ambientales
                </h3>
                @if($cantidadNoLeidas > 0)
                    <button 
                        wire:click="marcarTodasComoLeidas"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
                        type="button"
                    >
                        Marcar todas leídas
                    </button>
                @endif
            </div>

            {{-- Lista de alertas --}}
            <div class="max-h-96 overflow-y-auto">
                @forelse($alertasActivas as $alerta)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors @if(!$alerta->leida) bg-blue-50 @endif">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Emoji y título --}}
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-2xl">{{ $alerta->obtenerEmoji() }}</span>
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $alerta->titulo }}
                                    </h4>
                                    @if(!$alerta->leida)
                                        <span class="px-2 py-0.5 text-xs font-medium text-white bg-blue-600 rounded-full">
                                            Nuevo
                                        </span>
                                    @endif
                                </div>

                                {{-- Mensaje --}}
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ $alerta->mensaje }}
                                </p>

                                {{-- Unidad Productiva --}}
                                <p class="text-xs text-gray-500">
                                    📍 {{ $alerta->unidadProductiva->nombre }}
                                </p>

                                {{-- Contexto específico --}}
                                @if($alerta->datos_contexto)
                                    <div class="mt-2 text-xs text-gray-600">
                                        @if($alerta->tipo === 'sequia')
                                            <span>🌡️ Temp. promedio: {{ $alerta->datos_contexto['temperatura_promedio'] ?? 'N/A' }}°C</span>
                                            <span class="ml-2">📅 Días sin lluvia: {{ $alerta->datos_contexto['dias_sin_lluvia'] ?? 'N/A' }}</span>
                                        @elseif($alerta->tipo === 'tormenta')
                                            <span>🌧️ Lluvia esperada: {{ $alerta->datos_contexto['lluvia_esperada'] ?? 'N/A' }}mm</span>
                                            <span class="ml-2">💨 Viento: {{ $alerta->datos_contexto['viento_esperado'] ?? 'N/A' }}km/h</span>
                                        @elseif($alerta->tipo === 'estres_termico')
                                            <span>🌡️ Temp. máxima: {{ $alerta->datos_contexto['temperatura_maxima'] ?? 'N/A' }}°C</span>
                                            <span class="ml-2">📅 Días consecutivos: {{ $alerta->datos_contexto['dias_consecutivos'] ?? 'N/A' }}</span>
                                        @elseif($alerta->tipo === 'helada')
                                            <span>❄️ Temp. mínima: {{ $alerta->datos_contexto['temperatura_minima'] ?? 'N/A' }}°C</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Fecha --}}
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $alerta->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Botón marcar leída --}}
                            @if(!$alerta->leida)
                                <button 
                                    wire:click="marcarComoLeida({{ $alerta->id }})"
                                    class="ml-2 text-gray-400 hover:text-green-600 transition-colors"
                                    title="Marcar como leída"
                                    type="button"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Recomendaciones (expandibles) --}}
                        <details class="mt-2">
                            <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-800 font-medium">
                                💡 Ver recomendaciones
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs text-gray-600 bg-blue-50 p-3 rounded">
                                @foreach($alerta->obtenerRecomendaciones() as $recomendacion)
                                    <li class="flex items-start">
                                        <span class="mr-2 text-blue-600">•</span>
                                        <span>{{ $recomendacion }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-sm font-medium">No hay alertas activas</p>
                        <p class="text-xs mt-1 text-gray-400">Tus campos están en buenas condiciones</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            @if($alertasActivas->count() > 0)
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">
                        Total: {{ $alertasActivas->count() }} alerta{{ $alertasActivas->count() != 1 ? 's' : '' }}
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
