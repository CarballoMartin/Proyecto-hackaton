@props(['configuracion'])

<div x-data="configuracionModal({{ Js::from($configuracion) }})"
     @open-configuracion-modal.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
             @click="open = false"
             aria-hidden="true">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block w-full max-w-5xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

            <div class="flex justify-between items-center bg-gray-50 px-6 py-4 border-b">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                    Configuración del Sistema
                </h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <div class="flex">
                <aside class="w-1/4 bg-gray-50 p-6 border-r">
                    <nav class="space-y-1">
                        <button @click="activeTab = 'stock'"
                                :class="{'bg-indigo-100 text-indigo-700': activeTab === 'stock', 'text-gray-600 hover:bg-gray-100 hover:text-gray-900': activeTab !== 'stock'}"
                                class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <x-heroicon-o-archive-box class="mr-3 h-6 w-6" />
                            <span>Actualización de Stock</span>
                        </button>
                    </nav>
                </aside>

                <main class="w-3/4 p-6">
                    <div x-show="activeTab === 'stock'" class="space-y-6">
                        <form @submit.prevent="save($el)" action="{{ route('admin.settings.store') }}">
                            @csrf
                            @method('POST')

                            <div>
                                <label for="frecuencia_dias" class="block text-sm font-medium text-gray-700">Frecuencia de Actualización (en días)</label>
                                <p class="mt-1 text-xs text-gray-500">
                                    Define la frecuencia con la que el sistema debe solicitar a los productores que actualicen su información de stock ganadero.
                                </p>
                                <input type="number" id="frecuencia_dias" x-model="formData.frecuencia_dias" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <div>
                                    <label for="activo" class="block text-sm font-medium text-gray-700">Estado del Sistema de Actualizaciones</label>
                                    <p class="text-xs text-gray-500">Si está activo, se enviarán recordatorios y se registrarán los ciclos.</p>
                                </div>
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input type="checkbox" id="activo" x-model="formData.activo" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                    <label for="activo" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <button type="button" @click="open = false" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="isSaving" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <span x-show="!isSaving">Guardar Cambios</span>
                                    <span x-show="isSaving">Guardando...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>

<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #4F46E5; /* indigo-600 */
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #4F46E5; /* indigo-600 */
    }
</style>
