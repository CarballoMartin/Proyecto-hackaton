<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Instituciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Notificación de éxito --}}
        @if (session()->has('message'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        {{-- Contenedor de las tarjetas --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- Tarjeta: Registrar Institución --}}
                <a href="{{ route('admin.instituciones.crear') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl cursor-pointer">
                    <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-green-50 transition-all duration-300 group-hover:scale-[10]"></span>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-lg bg-green-100 p-4 text-green-600">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-lg font-bold text-gray-900">Registrar Institución</h3>
                            <p class="mt-2 text-sm text-gray-600">Agregar una nueva institución al sistema.</p>
                        </div>
                    </div>
                </a>

                {{-- Tarjeta: Gestionar Solicitudes --}}
                <a href="{{ route('admin.solicitudes.gestionar') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                    <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-yellow-50 transition-all duration-300 group-hover:scale-[10]"></span>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-lg bg-yellow-100 p-4 text-yellow-600">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-lg font-bold text-gray-900">Gestionar Solicitudes</h3>
                            <p class="mt-2 text-sm text-gray-600">Revisar y aprobar nuevas solicitudes de registro.</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-admin-layout>