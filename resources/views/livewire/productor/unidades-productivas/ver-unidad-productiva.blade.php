<div>
    {{-- ← Modal para ver detalles de la unidad productiva --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal-overlay">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                {{-- ← Encabezado del modal --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Detalles de la Unidad Productiva</h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- ← Contenido del modal --}}
                <div class="space-y-6">
                    {{-- ← Información Básica --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información Básica</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campo</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $campo ?: 'No especificado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Identificador Local (RENSPA)</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $identificador_local ?: 'No especificado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Identificador</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $tipo_identificador ?: 'No especificado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Superficie (ha)</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $superficie ?: 'No especificada' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Habita en el lugar</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($habita)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sí
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            No
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Latitud</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $latitud ? number_format($latitud, 6) : 'No especificada' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Longitud</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $longitud ? number_format($longitud, 6) : 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ← Agua para Humanos --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Agua para Consumo Humano</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fuente de Agua</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agua_humano_fuente }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Agua en la Casa</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($agua_humano_en_casa)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sí
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            No
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Distancia (metros)</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agua_humano_distancia ?: 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ← Agua para Animales --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Agua para Consumo Animal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fuente de Agua</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agua_animal_fuente }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Distancia (metros)</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agua_animal_distancia ?: 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ← Pasto y Suelo --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Pasto y Suelo</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Pasto</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $tipo_pasto }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Suelo</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $tipo_suelo }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Forrajeras Predominantes</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($forrajeras_predominante)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sí
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            No
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ← Observaciones --}}
                    @if($observaciones)
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Observaciones</h4>
                            <p class="text-sm text-gray-900">{{ $observaciones }}</p>
                        </div>
                    @endif

                    {{-- ← Información del Sistema --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información del Sistema</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($activo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $created_at ? $created_at->format('d/m/Y H:i') : 'No disponible' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $updated_at ? $updated_at->format('d/m/Y H:i') : 'No disponible' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ← Botones del modal --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <button wire:click="cerrarModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cerrar
                    </button>
                    <a href="{{ route('productor.unidades-productivas.editar', $unidad_productiva_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar Unidad Productiva
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
