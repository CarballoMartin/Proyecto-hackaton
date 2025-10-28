<div>
    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="busqueda"
                    placeholder="Campo, tipo, mensaje..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
            </div>

            {{-- Filtro Tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select 
                    wire:model.live="filtroTipo"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="">Todos</option>
                    <option value="sequia">🔴 Sequía</option>
                    <option value="tormenta">⛈️ Tormenta</option>
                    <option value="estres_termico">🌡️ Estrés Térmico</option>
                    <option value="helada">❄️ Helada</option>
                </select>
            </div>

            {{-- Filtro Nivel --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                <select 
                    wire:model.live="filtroNivel"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="">Todos</option>
                    <option value="critico">Crítico</option>
                    <option value="alto">Alto</option>
                    <option value="medio">Medio</option>
                    <option value="bajo">Bajo</option>
                </select>
            </div>

            {{-- Filtro Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select 
                    wire:model.live="filtroEstado"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                >
                    <option value="activas">Solo Activas</option>
                    <option value="todas">Todas</option>
                    <option value="inactivas">Solo Inactivas</option>
                </select>
            </div>
        </div>

        @if($filtroTipo || $filtroNivel || $filtroEstado !== 'activas' || $busqueda)
            <div class="mt-3">
                <button 
                    wire:click="limpiarFiltros"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    ✕ Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Lista de Alertas --}}
    <div class="space-y-4">
        @forelse($alertas as $alerta)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        {{-- Contenido Principal --}}
                        <div class="flex items-start flex-1">
                            <span class="text-3xl mr-4 flex-shrink-0">{{ $alerta->obtenerEmoji() }}</span>
                            <div class="flex-1">
                                {{-- Título y Nivel --}}
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $alerta->titulo }}</h3>
                                    <div class="flex items-center gap-2 ml-4">
                                        <span class="px-3 py-1 text-xs font-medium text-white rounded-full bg-{{ $alerta->obtenerColor() }}-600">
                                            {{ ucfirst($alerta->nivel) }}
                                        </span>
                                        @if(!$alerta->activa)
                                            <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-200 rounded-full">
                                                Inactiva
                                            </span>
                                        @endif
                                        @if($alerta->leida)
                                            <span class="px-3 py-1 text-xs font-medium text-green-600 bg-green-100 rounded-full">
                                                ✓ Leída
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Mensaje --}}
                                <p class="text-gray-700 mb-3">{{ $alerta->mensaje }}</p>

                                {{-- Información del Campo --}}
                                <div class="flex items-center text-sm text-gray-600 mb-3">
                                    <span class="mr-4">
                                        <span class="font-medium">📍 Campo:</span> {{ $alerta->unidadProductiva->nombre }}
                                    </span>
                                    <span class="mr-4">
                                        <span class="font-medium">📅 Creada:</span> {{ $alerta->created_at->diffForHumans() }}
                                    </span>
                                    <span>
                                        <span class="font-medium">🕐 Inicio:</span> {{ $alerta->fecha_inicio->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                {{-- Datos de Contexto --}}
                                @if($alerta->datos_contexto)
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @if($alerta->tipo === 'sequia')
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                🌡️ {{ $alerta->datos_contexto['temperatura_promedio'] ?? 'N/A' }}°C promedio
                                            </span>
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                📅 {{ $alerta->datos_contexto['dias_sin_lluvia'] ?? 'N/A' }} días sin lluvia
                                            </span>
                                        @elseif($alerta->tipo === 'tormenta')
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                🌧️ {{ $alerta->datos_contexto['lluvia_esperada'] ?? 'N/A' }}mm esperados
                                            </span>
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                💨 {{ $alerta->datos_contexto['viento_esperado'] ?? 'N/A' }}km/h
                                            </span>
                                        @elseif($alerta->tipo === 'estres_termico')
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                🌡️ {{ $alerta->datos_contexto['temperatura_maxima'] ?? 'N/A' }}°C máxima
                                            </span>
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                📅 {{ $alerta->datos_contexto['dias_consecutivos'] ?? 'N/A' }} días consecutivos
                                            </span>
                                        @elseif($alerta->tipo === 'helada')
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700">
                                                ❄️ {{ $alerta->datos_contexto['temperatura_minima'] ?? 'N/A' }}°C mínima
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Recomendaciones --}}
                                <details class="mt-3">
                                    <summary class="text-sm text-blue-600 cursor-pointer hover:text-blue-800 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ver recomendaciones
                                    </summary>
                                    <ul class="mt-2 space-y-1 text-sm text-gray-600 bg-gray-50 p-4 rounded-md">
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
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-4 pt-4 border-t flex items-center justify-end gap-2">
                        @if($alerta->activa)
                            @if(!$alerta->leida)
                                <button 
                                    wire:click="marcarLeida({{ $alerta->id }})"
                                    class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors"
                                >
                                    ✓ Marcar como leída
                                </button>
                            @endif
                            <button 
                                wire:click="desactivarAlerta({{ $alerta->id }})"
                                wire:confirm="¿Desactivar esta alerta?"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
                            >
                                Desactivar
                            </button>
                        @else
                            <button 
                                wire:click="reactivarAlerta({{ $alerta->id }})"
                                class="px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition-colors"
                            >
                                🔄 Reactivar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <span class="text-6xl">🔍</span>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No se encontraron alertas</h3>
                <p class="mt-2 text-sm text-gray-600">
                    @if($filtroTipo || $filtroNivel || $filtroEstado !== 'activas' || $busqueda)
                        Intenta ajustar los filtros para ver más resultados
                    @else
                        ¡Excelente! No hay alertas ambientales en este momento
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($alertas->hasPages())
        <div class="mt-6">
            {{ $alertas->links() }}
        </div>
    @endif
</div>
