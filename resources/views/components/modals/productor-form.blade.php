<div
    x-data="productorForm()"
    x-on:open-productor-form-modal.window="openModal"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
    style="display: none;"
>
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full" @click.away="show = false">
        <h2 class="text-2xl font-bold mb-4">Registrar Nuevo Productor</h2>

        <div class="mb-4 p-3 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <p class="text-sm"><b>Nota:</b> El sistema generará una <b>contraseña temporal</b> que será enviada al email del productor.</p>
        </div>

        <form @submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <input type="text" x-model="formData.nombre" id="nombre" name="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.nombre"><span class="text-red-500 text-xs" x-text="errors.nombre[0]"></span></template>
                </div>
                <!-- DNI -->
                <div>
                    <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                    <input type="text" x-model="formData.dni" id="dni" name="dni" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.dni"><span class="text-red-500 text-xs" x-text="errors.dni[0]"></span></template>
                </div>
                <!-- CUIL -->
                <div>
                    <label for="cuil" class="block text-sm font-medium text-gray-700">CUIL</label>
                    <input type="text" x-model="formData.cuil" id="cuil" name="cuil" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.cuil"><span class="text-red-500 text-xs" x-text="errors.cuil[0]"></span></template>
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" x-model="formData.email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.email"><span class="text-red-500 text-xs" x-text="errors.email[0]"></span></template>
                </div>
                <!-- Telefono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" x-model="formData.telefono" id="telefono" name="telefono" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.telefono"><span class="text-red-500 text-xs" x-text="errors.telefono[0]"></span></template>
                </div>
                <!-- Municipio -->
                <div>
                    <label for="municipio" class="block text-sm font-medium text-gray-700">Municipio</label>
                    <input type="text" x-model="formData.municipio" id="municipio" name="municipio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.municipio"><span class="text-red-500 text-xs" x-text="errors.municipio[0]"></span></template>
                </div>
                <!-- Paraje -->
                <div>
                    <label for="paraje" class="block text-sm font-medium text-gray-700">Paraje (Opcional)</label>
                    <input type="text" x-model="formData.paraje" id="paraje" name="paraje" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.paraje"><span class="text-red-500 text-xs" x-text="errors.paraje[0]"></span></template>
                </div>
                <!-- Dirección -->
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección (Opcional)</label>
                    <input type="text" x-model="formData.direccion" id="direccion" name="direccion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <template x-if="errors.direccion"><span class="text-red-500 text-xs" x-text="errors.direccion[0]"></span></template>
                </div>
            </div>

            <div x-show="formError" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <span x-text="formError"></span>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" :disabled="loading" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <span x-show="!loading">Guardar Productor</span>
                    <span x-show="loading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>


