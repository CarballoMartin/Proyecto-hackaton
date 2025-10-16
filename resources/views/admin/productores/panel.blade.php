<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Productores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- Tarjeta: Registrar Productor --}}
                <button type="button" @click="$dispatch('open-productor-form-modal')" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl cursor-pointer text-left w-full">
                    <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-indigo-50 transition-all duration-300 group-hover:scale-[10]"></span>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-lg bg-indigo-100 p-4 text-indigo-600">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-lg font-bold text-gray-900">Registrar Productor</h3>
                            <p class="mt-2 text-sm text-gray-600">Agregar un nuevo productor de forma manual.</p>
                        </div>
                    </div>
                </button>

                {{-- Tarjeta: Importar Productores --}}
                <a href="{{ route('admin.productores.importar') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                    <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-green-50 transition-all duration-300 group-hover:scale-[10]"></span>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-lg bg-green-100 p-4 text-green-600">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-lg font-bold text-gray-900">Importar Productores</h3>
                            <p class="mt-2 text-sm text-gray-600">Carga masiva desde un archivo CSV o Excel.</p>
                        </div>
                    </div>
                </a>

                {{-- Tarjeta: Listar y Modificar --}}
                <a href="{{ route('admin.productores.listado') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                    <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-purple-50 transition-all duration-300 group-hover:scale-[10]"></span>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-lg bg-purple-100 p-4 text-purple-600">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-lg font-bold text-gray-900">Listar y Modificar</h3>
                            <p class="mt-2 text-sm text-gray-600">Ver, buscar y editar productores registrados.</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-admin-layout>