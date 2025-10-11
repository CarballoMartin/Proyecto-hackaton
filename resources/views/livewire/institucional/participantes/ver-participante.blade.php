<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     x-data="{ show: @entangle('showViewModal') }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     wire:click.self="$dispatch('cerrar-modal-ver')">

    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90">

        {{-- Header del Modal --}}
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Detalles del Participante</h3>
            <button wire:click="cerrar" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Contenido del Modal --}}
        <div class="space-y-6">
            
            {{-- Información Principal --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center space-x-4">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ substr($participante->user->name, 0, 2) }}
                    </div>
                    
                    {{-- Información básica --}}
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900">{{ $participante->user->name }}</h4>
                        <p class="text-gray-600">{{ $participante->cargo ?? 'Sin cargo asignado' }}</p>
                        
                        {{-- Estado --}}
                        <div class="mt-2">
                            <button wire:click="toggleEstado" 
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-colors {{ $participante->activo ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $participante->activo ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $participante->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información Detallada --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Información de Contacto --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información de Contacto
                    </h5>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $participante->user->email }}</p>
                        </div>
                        
                        @if($participante->user->telefono)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Teléfono</label>
                            <p class="text-gray-900">{{ $participante->user->telefono }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Información Institucional --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Información Institucional
                    </h5>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Institución</label>
                            <p class="text-gray-900">{{ $participante->institucion->nombre }}</p>
                        </div>
                        
                        @if($participante->cargo)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cargo</label>
                            <p class="text-gray-900">{{ $participante->cargo }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Información Temporal --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información Temporal
                </h5>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <label class="text-sm font-medium text-gray-500">Fecha de Ingreso</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $participante->fecha_ingreso ? \Carbon\Carbon::parse($participante->fecha_ingreso)->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <label class="text-sm font-medium text-gray-500">Tiempo en la Institución</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->tiempoEnInstitucion }}</p>
                    </div>
                    
                    <div class="text-center">
                        <label class="text-sm font-medium text-gray-500">Días Activo</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->diasActivo ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Estadísticas Adicionales --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Estadísticas
                </h5>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $this->diasActivo ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Días Activo</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $participante->activo ? 'Sí' : 'No' }}
                        </div>
                        <div class="text-sm text-gray-500">Estado Actual</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $participante->user->email_verified_at ? 'Verificado' : 'Pendiente' }}
                        </div>
                        <div class="text-sm text-gray-500">Email Verificado</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ \Carbon\Carbon::parse($participante->created_at)->format('d/m') }}
                        </div>
                        <div class="text-sm text-gray-500">Registrado</div>
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <button wire:click="cerrar"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cerrar
                </button>
                <button wire:click="editar"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-lg font-medium text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </button>
                <button wire:click="eliminar"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 border border-transparent rounded-lg font-medium text-white hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Eliminar
                </button>
            </div>
        </div>

        {{-- Mensajes flash --}}
        @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Scripts para manejar eventos --}}
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('cerrar-modal-ver', () => {
        @this.set('showViewModal', false);
    });
    
    Livewire.on('editar-participante', (participanteId) => {
        @this.call('editar', participanteId);
    });
    
    Livewire.on('eliminar-participante', (participanteId) => {
        @this.call('eliminar', participanteId);
    });
});
</script>