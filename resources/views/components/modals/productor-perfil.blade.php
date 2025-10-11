<div x-data="productorPerfilModal" @open-productor-perfil-modal.window="openModal()" x-show="show" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false" aria-hidden="true">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-6xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

            <div class="flex justify-between items-center bg-gray-50 px-6 py-4 border-b">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                    Mi Perfil de Productor
                </h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <div class="flex" style="height: 70vh;">
                <aside class="w-1/4 bg-gray-50 p-6 border-r overflow-y-auto">
                    <nav class="space-y-1">
                        <button @click="activeTab = 'personal'"
                            :class="{ 'bg-indigo-100 text-indigo-700': activeTab === 'personal', 'text-gray-600 hover:bg-gray-100 hover:text-gray-900': activeTab !== 'personal' }"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <x-heroicon-o-user-circle class="mr-3 h-6 w-6" />
                            <span>Datos Personales</span>
                        </button>
                        <button @click="activeTab = 'contacto'"
                            :class="{ 'bg-indigo-100 text-indigo-700': activeTab === 'contacto', 'text-gray-600 hover:bg-gray-100 hover:text-gray-900': activeTab !== 'contacto' }"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <x-heroicon-o-phone class="mr-3 h-6 w-6" />
                            <span>Contacto</span>
                        </button>
                        <button @click="activeTab = 'ubicacion'"
                            :class="{ 'bg-indigo-100 text-indigo-700': activeTab === 'ubicacion', 'text-gray-600 hover:bg-gray-100 hover:text-gray-900': activeTab !== 'ubicacion' }"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <x-heroicon-o-map-pin class="mr-3 h-6 w-6" />
                            <span>Ubicación</span>
                        </button>
                    </nav>
                </aside>

                <main class="w-3/4 p-6 overflow-y-auto">
                    <form :action="'{{ route('productor.perfil.update') }}'" method="POST" @submit.prevent="save($event)">
                        @csrf

                        <div x-show="loading" class="text-center p-8">
                            <p>Cargando datos del perfil...</p>
                        </div>

                        <div x-show="!loading" class="space-y-6">
                            <div x-show="activeTab === 'personal'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo *</label>
                                    <input id="nombre" type="text" x-model="formData.nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <template x-if="errors.nombre"><p class="text-red-500 text-sm mt-1" x-text="errors.nombre[0]"></p></template>
                                </div>
                                <div>
                                    <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                                    <input id="fecha_nacimiento" type="date" x-model="formData.fecha_nacimiento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <template x-if="errors.fecha_nacimiento"><p class="text-red-500 text-sm mt-1" x-text="errors.fecha_nacimiento[0]"></p></template>
                                </div>
                                <div>
                                    <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                                    <input id="dni" type="text" x-model="formData.dni" maxlength="12" placeholder="Ej: 20.123.456" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">7–8 dígitos (se normaliza al guardar).</p>
                                    <template x-if="errors.dni"><p class="text-red-500 text-sm mt-1" x-text="errors.dni[0]"></p></template>
                                </div>
                                <div>
                                    <label for="cuil" class="block text-sm font-medium text-gray-700">CUIL</label>
                                    <input id="cuil" type="text" x-model="formData.cuil" maxlength="14" placeholder="Ej: 20-12345678-3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">11 dígitos (se normaliza al guardar).</p>
                                    <template x-if="errors.cuil"><p class="text-red-500 text-sm mt-1" x-text="errors.cuil[0]"></p></template>
                                </div>
                            </div>

                            <div x-show="activeTab === 'contacto'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input id="email" type="email" x-model="formData.email" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">El email no se puede cambiar desde aquí.</p>
                                </div>
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input id="telefono" type="tel" x-model="formData.telefono" placeholder="+54 9 11 1234 5678" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Ingrese teléfono con área. Se normalizará.</p>
                                    <template x-if="errors.telefono"><p class="text-red-500 text-sm mt-1" x-text="errors.telefono[0]"></p></template>
                                </div>
                            </div>

                            <div x-show="activeTab === 'ubicacion'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="municipio" class="block text-sm font-medium text-gray-700">Municipio</label>
                                    <input id="municipio" type="text" x-model="formData.municipio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <template x-if="errors.municipio"><p class="text-red-500 text-sm mt-1" x-text="errors.municipio[0]"></p></template>
                                </div>
                                <div>
                                    <label for="paraje" class="block text-sm font-medium text-gray-700">Paraje</label>
                                    <input id="paraje" type="text" x-model="formData.paraje" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <template x-if="errors.paraje"><p class="text-red-500 text-sm mt-1" x-text="errors.paraje[0]"></p></template>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                                    <textarea id="direccion" rows="3" x-model="formData.direccion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    <template x-if="errors.direccion"><p class="text-red-500 text-sm mt-1" x-text="errors.direccion[0]"></p></template>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end items-center pt-8 mt-8 border-t">
                            <template x-if="formError"><p class="text-red-500 text-sm mr-4" x-text="formError"></p></template>
                            <button type="button" @click="show = false" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="isSaving" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                <span x-show="!isSaving">Guardar Cambios</span>
                                <span x-show="isSaving">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</div>
