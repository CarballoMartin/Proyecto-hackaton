<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gestionar Participantes</h1>
                        <p class="text-gray-600 mt-1">{{ $institucion->nombre ?? 'Sin institución' }}</p>
                    </div>
                    <button wire:click="crear" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nuevo Participante
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Búsqueda --}}
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar participantes</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               id="search"
                               placeholder="Buscar por nombre o email..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Filtro de Estado --}}
                <div>
                    <label for="filtroEstado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="filtroEstado" 
                            id="filtroEstado"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>
                </div>
            </div>

            {{-- Resultados de búsqueda --}}
            @if($search || $filtroEstado)
            <div class="mt-4 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Mostrando {{ $participantes->count() }} de {{ $participantes->total() }} participantes
                    @if($search)
                        para "{{ $search }}"
                    @endif
                </p>
                <button wire:click="$set('search', ''); $set('filtroEstado', '')" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Limpiar filtros
                </button>
            </div>
            @endif
        </div>

        {{-- Tabla de Participantes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            @if($participantes->count() > 0)
                {{-- Header de tabla --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700">
                        <div class="col-span-4">
                            <button wire:click="sortBy('nombre')" class="flex items-center hover:text-gray-900 transition-colors">
                                Participante
                                @if($sortBy === 'nombre')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </div>
                        <div class="col-span-3">
                            <button wire:click="sortBy('email')" class="flex items-center hover:text-gray-900 transition-colors">
                                Email
                                @if($sortBy === 'email')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </div>
                        <div class="col-span-2">
                            <button wire:click="sortBy('created_at')" class="flex items-center hover:text-gray-900 transition-colors">
                                Fecha Ingreso
                                @if($sortBy === 'created_at')
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </div>
                        <div class="col-span-2 text-center">Estado</div>
                        <div class="col-span-1 text-center">Acciones</div>
                    </div>
                </div>

                {{-- Filas de participantes --}}
                <div class="divide-y divide-gray-200">
                    @foreach($participantes as $participante)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            
                            {{-- Información del participante --}}
                            <div class="col-span-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-medium text-sm mr-3">
                                        {{ substr($participante->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $participante->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $participante->cargo ?? 'Sin cargo asignado' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-span-3">
                                <p class="text-sm text-gray-900">{{ $participante->user->email }}</p>
                                @if($participante->user->telefono)
                                <p class="text-xs text-gray-500">{{ $participante->user->telefono }}</p>
                                @endif
                            </div>

                            {{-- Fecha de ingreso --}}
                            <div class="col-span-2">
                                <p class="text-sm text-gray-900">{{ $participante->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $participante->created_at->diffForHumans() }}</p>
                            </div>

                            {{-- Estado --}}
                            <div class="col-span-2 text-center">
                                <button wire:click="toggleEstado({{ $participante->id }})" 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $participante->activo ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    <span class="w-2 h-2 rounded-full mr-2 {{ $participante->activo ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    {{ $participante->activo ? 'Activo' : 'Inactivo' }}
                                </button>
                            </div>

                            {{-- Acciones --}}
                            <div class="col-span-1 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button wire:click="ver({{ $participante->id }})" 
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded transition-colors" 
                                            title="Ver detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="editar({{ $participante->id }})" 
                                            class="text-green-600 hover:text-green-800 p-1 rounded transition-colors" 
                                            title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="eliminar({{ $participante->id }})" 
                                            class="text-red-600 hover:text-red-800 p-1 rounded transition-colors" 
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if($participantes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $participantes->links() }}
                </div>
                @endif

            @else
                {{-- Estado vacío --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay participantes</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search || $filtroEstado)
                            No se encontraron participantes con los filtros aplicados.
                        @else
                            Comienza agregando un nuevo participante a tu institución.
                        @endif
                    </p>
                    @if(!$search && !$filtroEstado)
                    <div class="mt-6">
                        <button wire:click="crear" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nuevo Participante
                        </button>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 z-50">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    </div>
    @endif

    {{-- Modales --}}
    @if($showCreateModal)
        @livewire('institucional.participantes.crear-participante')
    @endif

    @if($showEditModal && $participanteSeleccionado)
        @livewire('institucional.participantes.editar-participante', ['participante' => $participanteSeleccionado])
    @endif

    @if($showViewModal && $participanteSeleccionado)
        @livewire('institucional.participantes.ver-participante', ['participante' => $participanteSeleccionado])
    @endif

    {{-- Modal de confirmación de eliminación --}}
    @if($showDeleteModal && $participanteSeleccionado)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="$set('showDeleteModal', false)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que quieres eliminar a <strong>{{ $participanteSeleccionado->user->name }}</strong>? 
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button wire:click="confirmarEliminacion" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Eliminar
                    </button>
                    <button wire:click="$set('showDeleteModal', false)" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>