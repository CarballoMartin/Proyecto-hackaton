<div>
    {{-- ← Modal de confirmación para eliminar unidad productiva --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal-overlay">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
                {{-- ← Encabezado del modal --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Confirmar Eliminación</h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- ← Contenido del modal --}}
                <div class="text-center">
                    {{-- ← Icono de advertencia --}}
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>

                    {{-- ← Mensaje de confirmación --}}
                    <h4 class="text-lg font-medium text-gray-900 mb-2">¿Estás seguro?</h4>
                    <p class="text-sm text-gray-600 mb-6">
                        Estás a punto de eliminar la unidad productiva: <strong>{{ $nombre_unidad_productiva }}</strong>
                    </p>
                    <p class="text-sm text-red-600 mb-6">
                        <strong>Esta acción no se puede deshacer.</strong> Se eliminarán todos los datos asociados a esta unidad productiva.
                    </p>
                </div>

                {{-- ← Botones del modal --}}
                <div class="flex justify-end space-x-4">
                    <button wire:click="cerrarModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button wire:click="eliminarUnidadProductiva" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Eliminar Unidad Productiva
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
