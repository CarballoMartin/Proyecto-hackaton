<div
    x-data="institucionForm()"
    x-on:open-institucion-modal.window="openModal($event.detail)"
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
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full relative" @click.away="show = false">

        <h2 class="text-2xl font-bold mb-4">Registrar Nueva Institución</h2>

        <div class="mb-4 p-3 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <p class="text-sm"><b>Nota:</b> El sistema generará una <b>contraseña temporal</b> y la enviará al email de contacto principal.</p>
        </div>

        <form action="{{ route('admin.instituciones.store') }}" method="POST">
            @csrf
            <input type="hidden" name="solicitud_id" x-model="formData.solicitud_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre de la Institución -->
                <div>
                    <label for="inst-nombre" class="block text-sm font-medium text-gray-700">Nombre de la Institución</label>
                    <input type="text" name="nombre" id="inst-nombre" :value="formData.nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('nombre') border-red-500 @enderror">
                    @error('nombre')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
                <!-- CUIT -->
                <div>
                    <label for="inst-cuit" class="block text-sm font-medium text-gray-700">CUIT</label>
                    <input type="text" name="cuit" id="inst-cuit" :value="formData.cuit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('cuit') border-red-500 @enderror">
                    @error('cuit')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
                <!-- Email de Contacto -->
                <div>
                    <label for="inst-email" class="block text-sm font-medium text-gray-700">Email de Contacto</label>
                    <input type="email" name="contacto_email" id="inst-email" :value="formData.contacto_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('contacto_email') border-red-500 @enderror">
                    @error('contacto_email')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
                <!-- Email Secundario -->
                <div>
                    <label for="inst-email-sec" class="block text-sm font-medium text-gray-700">Email Secundario (Opcional)</label>
                    <input type="email" name="email_secundario" id="inst-email-sec" :value="formData.email_secundario" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <!-- Teléfono -->
                <div>
                    <label for="inst-telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" name="telefono" id="inst-telefono" :value="formData.telefono" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('telefono') border-red-500 @enderror">
                    @error('telefono')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
                <!-- Localidad -->
                <div>
                    <label for="inst-localidad" class="block text-sm font-medium text-gray-700">Localidad</label>
                    <input type="text" name="localidad" id="inst-localidad" :value="formData.localidad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('localidad') border-red-500 @enderror">
                    @error('localidad')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
                <!-- Provincia -->
                <div>
                    <label for="inst-provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                    <input type="text" name="provincia" id="inst-provincia" :value="formData.provincia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('provincia') border-red-500 @enderror">
                    @error('provincia')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Guardar Institución
                </button>
            </div>
        </form>
    </div>
</div>
