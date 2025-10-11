<div x-show="solicitudModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Fondo oscuro --}}
        <div x-show="solicitudModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="solicitudModalOpen = false" 
             aria-hidden="true">
        </div>

        {{-- Contenido del Modal --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="solicitudModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <form action="{{ route('solicitud.institucional.store') }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Solicitar Incorporación de Institución
                            </h3>
                            <div class="mt-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="col-span-2">
                                        <p class="text-sm text-gray-600">
                                            Complete el siguiente formulario para iniciar el proceso de incorporación de su institución a la plataforma. Un administrador revisará la solicitud.
                                        </p>
                                    </div>

                                    {{-- Form fields... --}}
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="nombre_institucion" value="Nombre de la Institución" />
                                        <x-input id="nombre_institucion" name="nombre_institucion" type="text" class="mt-1 block w-full" value="{{ old('nombre_institucion') }}" />
                                        @error('nombre_institucion')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="cuit" value="CUIT (Opcional)" />
                                        <x-input id="cuit" name="cuit" type="text" class="mt-1 block w-full" value="{{ old('cuit') }}" />
                                        @error('cuit')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="nombre_solicitante" value="Su Nombre Completo" />
                                        <x-input id="nombre_solicitante" name="nombre_solicitante" type="text" class="mt-1 block w-full" value="{{ old('nombre_solicitante') }}" />
                                        @error('nombre_solicitante')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="email_contacto" value="Email de Contacto" />
                                        <x-input id="email_contacto" name="email_contacto" type="email" class="mt-1 block w-full" value="{{ old('email_contacto') }}" />
                                        @error('email_contacto')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="telefono_contacto" value="Teléfono de Contacto" />
                                        <x-input id="telefono_contacto" name="telefono_contacto" type="text" class="mt-1 block w-full" value="{{ old('telefono_contacto') }}" />
                                        @error('telefono_contacto')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="provincia" value="Provincia" />
                                        <x-input id="provincia" name="provincia" type="text" class="mt-1 block w-full" value="{{ old('provincia') }}" />
                                        @error('provincia')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <x-label for="localidad" value="Localidad" />
                                        <x-input id="localidad" name="localidad" type="text" class="mt-1 block w-full" value="{{ old('localidad') }}" />
                                        @error('localidad')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-span-2">
                                        <x-label for="mensaje" value="Mensaje (Opcional)" />
                                        <textarea id="mensaje" name="mensaje" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">{{ old('mensaje') }}</textarea>
                                        @error('mensaje')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-button type="submit" x-bind:disabled="isSubmitting">
                        <div x-show="!isSubmitting">
                            <span>Enviar Solicitud</span>
                        </div>
                        <div x-show="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Enviando...</span>
                        </div>
                    </x-button>
                    <x-secondary-button type="button" @click="solicitudModalOpen = false" class="ms-3">
                        Cancelar
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>