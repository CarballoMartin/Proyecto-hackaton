<div x-data="crearParajeModal()" @open-crear-paraje-modal.window="openModal($event.detail)" style="display: none;" x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="agregarParaje()">
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
                                    <label for="paraje_nombre" class="block text-sm font-medium text-gray-700">Nombre del Paraje</label>
                                    <input type="text" x-model="nombre" id="paraje_nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Ej: La Cieneguita">
                                    <template x-if="errors.nombre">
                                        <span class="text-red-500 text-sm" x-text="errors.nombre[0]"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" :disabled="loading" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm" :class="{ 'opacity-50': loading }">
                        <span x-show="!loading">Agregar Paraje</span>
                        <span x-show="loading">Agregando...</span>
                    </button>
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function crearParajeModal() {
        return {
            showModal: false,
            loading: false,
            nombre: '',
            municipioId: null,
            municipioNombre: '',
            errors: {},
            openModal(detail) {
                this.reset();
                this.municipioId = detail.municipioId;
                this.municipioNombre = detail.municipioNombre;
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            reset() {
                this.nombre = '';
                this.municipioId = null;
                this.municipioNombre = '';
                this.errors = {};
                this.loading = false;
            },
            async agregarParaje() {
                this.loading = true;
                this.errors = {};

                try {
                    const response = await fetch('{{ route("productor.parajes.validar-temporal") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            nombre: this.nombre,
                            municipio_id: this.municipioId
                        })
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors;
                    } else if (response.ok) {
                        this.$dispatch('paraje-temporal-agregado', { nombre: this.nombre });
                        this.closeModal();
                    } else {
                        // Generic error handling
                        console.error('Error inesperado del servidor');
                    }
                } catch (error) {
                    console.error('Error de red o de fetch:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
