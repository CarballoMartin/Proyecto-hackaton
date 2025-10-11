<x-productor-wizard-layout>
    <div wire:ignore>
        <div class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

        <!-- Contenedor Principal -->
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200" x-data="{
            step: 1,
            init() {
                setTimeout(() => {
                    this.$refs.slider.classList.remove('transition-none');
                }, 150);
            },
            nextStep() { if (this.step < 3) this.step++ },
            previousStep() { if (this.step > 1) this.step-- }
         }" x-cloak>

            <!-- Formas Abstractas de Fondo -->
            <div class="absolute top-0 left-0 w-full h-full opacity-50">
                <div
                    class="absolute top-0 left-0 -translate-x-1/3 -translate-y-1/3 w-80 h-80 bg-indigo-50 rounded-full">
                </div>
                <div
                    class="absolute bottom-0 right-0 translate-x-1/3 translate-y-1/3 w-96 h-96 bg-green-50 rounded-full">
                </div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gray-50 rounded-full blur-2xl">
                </div>
            </div>

            <div class="relative p-8 sm:p-12 z-10">
                <!-- Barra de Progreso -->
                <div class="mb-12">
                    <div class="relative h-2 bg-gray-200 rounded-full">
                        <div class="absolute top-0 left-0 h-2 bg-green-500 rounded-full transition-all duration-500 ease-out"
                            :style="`width: ${step === 1 ? '33%' : (step === 2 ? '66%' : '100%')}`"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm font-medium text-gray-500">
                        <span :class="{ 'text-green-600 font-semibold': step >= 1 }">Bienvenida</span>
                        <span :class="{ 'text-green-600 font-semibold': step >= 2 }">Resumen</span>
                        <span :class="{ 'text-green-600 font-semibold': step >= 3 }">Primeros Pasos</span>
                    </div>
                </div>

                <!-- Contenedor de Pasos con Overflow Hidden -->
                <div class="overflow-hidden">
                    <!-- Wrapper Deslizable -->
                    <div class="flex transition-transform duration-500 ease-in-out will-change-transform transition-none"
                         x-ref="slider"
                         :style="`transform: translateX(-${step - 1}00%)`">

                        <!-- Fase 1: Bienvenida -->
                        <div class="w-full flex-shrink-0 min-h-[350px] flex flex-col text-center px-4">
                            <h1 class="text-5xl font-bold text-gray-800">¡Bienvenido, <span
                                    class="text-green-600">{{ $productorNombre }}</span>!</h1>
                            <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
                                Estamos encantados de tenerte en nuestra plataforma, diseñada para simplificar la
                                gestión de tu producción ovina y caprina.
                            </p>
                            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                                Este breve asistente te guiará para configurar tu cuenta en menos de un minuto.
                            </p>
                        </div>

                        <!-- Fase 2: Resumen -->
                        <div class="w-full flex-shrink-0 min-h-[350px] flex flex-col text-center px-4">
                            <h2 class="text-4xl font-bold text-gray-800">Todo lo que necesitas, en un solo lugar</h2>
                            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                                    <strong class="text-green-600">Gestiona tu Stock</strong>
                                    <p class="text-sm text-gray-600 mt-1">Lleva un registro digital y actualizado de
                                        todos tus animales.</p>
                                </div>
                                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                                    <strong class="text-green-600">Cuaderno de Campo</strong>
                                    <p class="text-sm text-gray-600 mt-1">Registra altas, bajas y movimientos de forma
                                        sencilla e intuitiva.</p>
                                </div>
                                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                                    <strong class="text-green-600">Genera Reportes</strong>
                                    <p class="text-sm text-gray-600 mt-1">Crea informes y declaraciones juradas con solo
                                        unos clics.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fase 3: Primeros Pasos -->
                        <div class="w-full flex-shrink-0 min-h-[550px] flex flex-col text-center px-4">
                            <h2 class="text-4xl font-bold text-gray-800">Tu Primer Paso: Registrar tu chacra</h2>
                            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                                El formulario tiene 3 secciones. Aquí tienes una guía rápida de lo que necesitarás:
                            </p>

                            <div class="mt-8 w-full max-w-2xl mx-auto flex-grow relative">
                                <!-- Paso 1 -->
                                <div class="absolute top-[-65px] left-0 w-[30rem]">
                                    <img src="{{ asset('img/onboarding/paso1.png') }}" alt="Paso 1: Datos Básicos" class="w-full h-auto">
                                </div>

                                <!-- Paso 2 -->
                                <div class="absolute top-1/2 right-0 w-[30rem] transform -translate-y-1/2">
                                    <img src="{{ asset('img/onboarding/paso2.png') }}" alt="Paso 2: Ubicación" class="w-full h-auto">
                                </div>

                                <!-- Paso 3 -->
                                <div class="absolute bottom-[-65px] left-0 w-[30rem]">
                                    <img src="{{ asset('img/onboarding/paso3.png') }}" alt="Paso 3: Información Adicional" class="w-full h-auto">
                                </div>
                            </div>

                            <div class="mt-auto pt-8">
                                <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg max-w-2xl mx-auto">
                                    <p class="font-semibold text-indigo-800">
                                        Al pulsar "Comenzar", serás dirigido al formulario para que cargues los datos.
                                        Una vez guardada, tendrás acceso completo a la plataforma.
                                    </p>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Navegación -->
                <div class="mt-12 flex justify-between items-center">
                    <div>
                        <button @click="previousStep()"
                            class="px-8 py-3 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50"
                            :disabled="step === 1">
                            Anterior
                        </button>
                    </div>
                    <div>
                        <button @click="nextStep()" x-show="step < 3"
                            class="px-8 py-3 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            Siguiente
                        </button>
                        <a href="{{ route('productor.unidades-productivas.create') }}" x-show="step === 3"
                            class="inline-block px-8 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Comenzar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-productor-wizard-layout>