<div x-data="{ isLoading: false }" @loading-started.window="isLoading = true" @loading-finished.window="isLoading = false">
    @if ($step == 1)
        <form wire:submit.prevent="solicitarCodigo">
            <div>
                <x-label for="identificador" value="Email o Teléfono" />
                <p class="text-sm text-gray-600 mt-1">Se enviará un código de un solo uso a su dirección de correo electrónico o número de teléfono.</p>
                <x-input wire:model.defer="identificador" id="identificador" class="block mt-1 w-full" type="text" name="identificador" required autofocus />
                <x-input-error for="identificador" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit" x-bind:disabled="isLoading">
                    <span x-show="!isLoading">
                        Enviar Código de Acceso
                    </span>
                    <span x-show="isLoading">
                        Enviando...
                    </span>
                </x-button>
            </div>
        </form>
    @elseif ($step == 2)
        <div class="mb-4 text-sm text-green-600">
            {{ $feedbackMessage }}
        </div>

        <form wire:submit.prevent="iniciarSesion">
            <div>
                <x-label for="codigo" value="Código de Acceso" />
                <p class="text-sm text-gray-600 mt-1">Ingrese el código que recibió para iniciar sesión.</p>
                <x-input wire:model.defer="codigo" id="codigo" class="block mt-1 w-full" type="text" name="codigo" required />
                <x-input-error for="codigo" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="button" wire:click="$set('step', 1)" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Usar otro identificador
                </button>

                <x-button type="submit">
                    <span wire:loading.remove wire:target="iniciarSesion">
                        Iniciar Sesión
                    </span>
                    <span wire:loading wire:target="iniciarSesion">
                        Validando...
                    </span>
                </x-button>
            </div>
        </form>
    @endif
</div>
