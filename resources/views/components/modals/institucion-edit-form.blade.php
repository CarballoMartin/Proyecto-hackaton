<div
    x-data="editInstitucionModal()"
    @open-edit-institucion-modal.window="openModal($event.detail.id)"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
    style="display: none;"
    x-cloak
>
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.away="show = false">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Editar Institución</h2>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800 text-sm">
            <p class="font-bold">Aviso Importante</p>
            <p class="mt-1">Los cambios realizados afectarán la información visible de la institución.</p>
        </div>

        <form @submit.prevent="updateInstitucion">
            <div class="grid grid-cols-1 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="edit-inst-nombre" class="block text-sm font-medium text-gray-700">Nombre de la Institución</label>
                    <input type="text" x-model="formData.nombre" id="edit-inst-nombre" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <template x-if="errors.nombre"><span class="text-red-500 text-xs" x-text="errors.nombre[0]"></span></template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- CUIT -->
                    <div>
                        <label for="edit-inst-cuit" class="block text-sm font-medium text-gray-700">CUIT (Opcional)</label>
                        <input type="text" x-model="formData.cuit" id="edit-inst-cuit" 
                               placeholder="30-12345678-9"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <template x-if="errors.cuit"><span class="text-red-500 text-xs" x-text="errors.cuit[0]"></span></template>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="edit-inst-email" class="block text-sm font-medium text-gray-700">Email de Contacto</label>
                        <input type="email" x-model="formData.contacto_email" id="edit-inst-email" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <template x-if="errors.contacto_email"><span class="text-red-500 text-xs" x-text="errors.contacto_email[0]"></span></template>
                    </div>

                    <!-- Localidad -->
                    <div>
                        <label for="edit-inst-localidad" class="block text-sm font-medium text-gray-700">Localidad (Opcional)</label>
                        <input type="text" x-model="formData.localidad" id="edit-inst-localidad" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <template x-if="errors.localidad"><span class="text-red-500 text-xs" x-text="errors.localidad[0]"></span></template>
                    </div>

                    <!-- Provincia -->
                    <div>
                        <label for="edit-inst-provincia" class="block text-sm font-medium text-gray-700">Provincia (Opcional)</label>
                        <input type="text" x-model="formData.provincia" id="edit-inst-provincia" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <template x-if="errors.provincia"><span class="text-red-500 text-xs" x-text="errors.provincia[0]"></span></template>
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="edit-inst-descripcion" class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                    <textarea x-model="formData.descripcion" id="edit-inst-descripcion" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <template x-if="errors.descripcion"><span class="text-red-500 text-xs" x-text="errors.descripcion[0]"></span></template>
                </div>
            </div>

            <div x-show="formError" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <span x-text="formError"></span>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="show = false" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50" :disabled="loading">
                    <span x-show="!loading">Actualizar</span>
                    <span x-show="loading">Actualizando...</span>
                </button>
            </div>
        </form>
    </div>
</div>








