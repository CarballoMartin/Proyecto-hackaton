<div x-data="{
    formData: {
        name: '',
        email: '',
        subject: '',
        phone: '',
        message: '',
        _token: '{{ csrf_token() }}'
    },
    errors: {},
    loading: false,
    formSubmitted: false,
    generalError: '',
    submitForm() {
        this.loading = true;
        this.errors = {};
        this.generalError = '';

        fetch('{{ route('landing.contact.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(this.formData)
        })
        .then(response => {
            if (response.status === 422) {
                return response.json().then(data => {
                    this.errors = data.errors;
                    throw new Error('Error de validación');
                });
            }
            if (!response.ok) {
                return response.json().then(data => {
                    this.generalError = data.message || 'Ocurrió un error en el servidor.';
                    throw new Error(this.generalError);
                });
            }
            return response.json();
        })
        .then(data => {
            this.formSubmitted = true;
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            this.loading = false;
        });
    }
}">
    <template x-if="formSubmitted">
        {{-- Mensaje de Éxito --}}
        <div class="text-center py-12">
            <div class="flex justify-center mb-4">
                <x-heroicon-o-check-circle class="h-16 w-16 text-green-500" />
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900">¡Gracias por tu mensaje!</h2>
            <p class="mt-4 text-lg text-gray-600">Nos pondremos en contacto contigo a la brevedad.</p>
            <button @click="contactModalOpen = false" class="mt-8 w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Cerrar
            </button>
        </div>
    </template>

    <template x-if="!formSubmitted">
        {{-- Formulario --}}
        <div>
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-extrabold text-gray-900">Contáctanos</h2>
                <button @click="contactModalOpen = false" class="text-gray-500 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>
            <div class="mt-6">
                <div x-show="generalError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline" x-text="generalError"></span>
                </div>
                <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    @csrf
                    <div>
                        <label for="name" class="sr-only">Nombre</label>
                        <input x-model="formData.name" type="text" id="name" autocomplete="name" class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" :class="{'border-red-500': errors.name}" placeholder="Nombre completo">
                        <template x-if="errors.name"><span class="text-red-500 text-sm" x-text="errors.name[0]"></span></template>
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input x-model="formData.email" id="email" type="email" autocomplete="email" class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" :class="{'border-red-500': errors.email}" placeholder="Email">
                        <template x-if="errors.email"><span class="text-red-500 text-sm" x-text="errors.email[0]"></span></template>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="subject" class="sr-only">Asunto</label>
                        <input x-model="formData.subject" type="text" id="subject" class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" :class="{'border-red-500': errors.subject}" placeholder="Asunto">
                        <template x-if="errors.subject"><span class="text-red-500 text-sm" x-text="errors.subject[0]"></span></template>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone" class="sr-only">Teléfono</label>
                        <input x-model="formData.phone" type="text" id="phone" autocomplete="tel" class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" placeholder="Teléfono (Opcional)">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="sr-only">Mensaje</label>
                        <textarea x-model="formData.message" id="message" rows="4" class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" :class="{'border-red-500': errors.message}" placeholder="Escribe tu mensaje..."></textarea>
                        <template x-if="errors.message"><span class="text-red-500 text-sm" x-text="errors.message[0]"></span></template>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed" :disabled="loading">
                            <div x-show="!loading">
                                <span>Enviar Mensaje</span>
                            </div>
                            <div x-show="loading">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
