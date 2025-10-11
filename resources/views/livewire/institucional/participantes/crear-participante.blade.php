<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     x-data="{ show: @entangle('showCreateModal') }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     wire:click.self="$dispatch('cerrar-modal-crear')">

    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90">

        {{-- Header del Modal --}}
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Crear Nuevo Participante</h3>
            <button wire:click="cancelar" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Formulario --}}
        <form wire:submit.prevent="guardar" class="space-y-6">
            
            {{-- Nombre --}}
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       wire:model="nombre" 
                       id="nombre"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('nombre') border-red-500 @enderror"
                       placeholder="Ingrese el nombre completo">
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       wire:model="email" 
                       id="email"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
                       placeholder="ejemplo@institucion.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Grid de campos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input type="text" 
                           wire:model="telefono" 
                           id="telefono"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('telefono') border-red-500 @enderror"
                           placeholder="+54 9 11 1234-5678">
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cargo --}}
                <div>
                    <label for="cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo
                    </label>
                    <input type="text" 
                           wire:model="cargo" 
                           id="cargo"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('cargo') border-red-500 @enderror"
                           placeholder="Ej: Coordinador, Técnico, etc.">
                    @error('cargo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Fecha de Ingreso --}}
            <div>
                <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Ingreso <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       wire:model="fecha_ingreso" 
                       id="fecha_ingreso"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('fecha_ingreso') border-red-500 @enderror">
                @error('fecha_ingreso')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="flex items-center">
                <input type="checkbox" 
                       wire:model="activo" 
                       id="activo"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="activo" class="ml-2 block text-sm text-gray-700">
                    Participante activo
                </label>
            </div>

            {{-- Información adicional --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Información importante
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Se creará automáticamente una contraseña temporal</li>
                                <li>El participante recibirá un email con las credenciales</li>
                                <li>Podrá cambiar su contraseña en el primer login</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        wire:click="cancelar"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="guardar"
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                    
                    <svg wire:loading wire:target="guardar" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <span wire:loading.remove wire:target="guardar">Crear Participante</span>
                    <span wire:loading wire:target="guardar">Creando...</span>
                </button>
            </div>
        </form>

        {{-- Mensajes de error/success --}}
        @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Scripts para manejar eventos --}}
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('cerrar-modal-crear', () => {
        // Cerrar modal y resetear estado
        @this.set('showCreateModal', false);
    });
    
    Livewire.on('participante-creado', () => {
        // Actualizar la lista de participantes
        @this.call('$refresh');
    });
});
</script>