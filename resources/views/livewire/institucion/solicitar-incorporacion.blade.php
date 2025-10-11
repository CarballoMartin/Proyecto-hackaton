
<div class="p-6 mx-auto max-w-2xl bg-white rounded-lg shadow-md mt-10">

    {{-- Título del formulario --}}
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
        Solicitud de Incorporación Institucional
    </h2>

    {{-- 1. Mensaje de Éxito --}}
    @if (session()->has('status'))
        <div class="p-4 mb-4 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- 2. El Formulario y su Vínculo con Livewire --}}
    <form wire:submit.prevent="submit" class="space-y-6">

        {{-- 3. Campo: Nombre de la Institución --}}
        <div>
            <label for="nombre_institucion" class="block text-sm font-medium text-gray-700">Nombre de la
                Institución</label>
            <input type="text" id="nombre_institucion" wire:model="nombre_institucion"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            {{-- 4. Mensaje de Error para este campo --}}
            @error('nombre_institucion') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Campo: Email de Contacto --}}
        <div>
            <label for="email_contacto" class="block text-sm font-medium text-gray-700">Email de Contacto</label>
            <input type="email" id="email_contacto" wire:model="email_contacto"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('email_contacto') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Campo: CUIT (Opcional) --}}
        <div>
            <label for="cuit" class="block text-sm font-medium text-gray-700">CUIT (Opcional)</label>
            <input type="text" id="cuit" wire:model="cuit"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('cuit') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Campo: Nombre del Solicitante --}}
        <div>
            <label for="nombre_solicitante" class="block text-sm font-medium text-gray-700">Tu Nombre Completo</label>
            <input type="text" id="nombre_solicitante" wire:model="nombre_solicitante"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('nombre_solicitante') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Campo: Teléfono de Contacto (Opcional) --}}
        <div>
            <label for="telefono_contacto" class="block text-sm font-medium text-gray-700">Teléfono de Contacto
                (Opcional)</label>
            <input type="text" id="telefono_contacto" wire:model="telefono_contacto"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('telefono_contacto') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Campo: Mensaje (Opcional) --}}
        <div>
            <label for="mensaje" class="block text-sm font-medium text-gray-700">Mensaje (Opcional)</label>
            <textarea id="mensaje" wire:model="mensaje" rows="4"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            @error('mensaje') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- 5. El Botón de Envío --}}
        <div class="text-right">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700
      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>