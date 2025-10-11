
{{-- 
Componente de modal controlado por Alpine.js para crear un nuevo paraje.
Este componente no guarda datos en el backend. En su lugar, emite un evento
de navegador `parajeAgregado` con los datos del nuevo paraje para que el 
componente padre (Livewire en este caso) pueda manejarlo.

Escucha el evento `open-crear-paraje-modal` para abrirse.
--}}
@props(['municipioId' => null, 'municipioNombre' => ''])

<div 
    x-data="{
        show: false,
        municipioId: @js($municipioId),
        municipioNombre: @js($municipioNombre),
        nombreParaje: ''
    }"
    x-on:open-crear-paraje-modal.window="
        show = true;
        municipioId = $event.detail.municipioId;
        municipioNombre = $event.detail.municipioNombre;
        $nextTick(() => $refs.nombreParaje.focus());
    "
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed z-50 inset-0 overflow-y-auto" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Fondo oscuro --}}
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>

        {{-- Contenido del modal --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <form @submit.prevent="$dispatch('parajeAgregado', { nombre: nombreParaje }); show = false; nombreParaje = '';">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Crear Nuevo Paraje
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Estás agregando un paraje para el municipio de <strong x-text="municipioNombre"></strong>.
                                </p>
                                <div class="mt-4">
                                    <label for="nombre_paraje_modal" class="block text-sm font-medium text-gray-700">Nombre del Paraje</label>
                                    <input type="text" x-model="nombreParaje" x-ref="nombreParaje" id="nombre_paraje_modal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Ej: La Cieneguita" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Agregar Paraje
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
