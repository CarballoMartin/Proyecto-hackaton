<x-admin-layout>
    {{-- Encabezado del Panel --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Administración
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-12">
                {{-- Tarjetas de estadísticas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Productores</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $totalProductores }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Instituciones</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $totalInstituciones }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Solicitudes Pendientes</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $solicitudesPendientes }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 border-l-4 border-red-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Unidades Productivas</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $totalUnidadesProductivas }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones rápidas --}}
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Acciones Rápidas</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        {{-- Acción: Gestionar Productores --}}
                        <a href="{{ route('admin.productores.panel') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                            <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-indigo-50 transition-all duration-300 group-hover:scale-[10] pointer-events-none"></span>
                            <div class="relative z-10">
                                <div class="inline-flex rounded-lg bg-indigo-100 p-4 text-indigo-600"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></div>
                                <div class="mt-6">
                                    <h3 class="text-lg font-bold text-gray-900">Gestionar Productores</h3>
                                    <p class="mt-2 text-sm text-gray-600">Registrar, importar o listar productores.</p>
                                </div>
                            </div>
                        </a>

                        {{-- Acción: Gestionar Instituciones --}}
                        <a href="{{ route('admin.instituciones.panel') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                            <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-green-50 transition-all duration-300 group-hover:scale-[10] pointer-events-none"></span>
                            <div class="relative z-10">
                                <div class="inline-flex rounded-lg bg-green-100 p-4 text-green-600"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                                <div class="mt-6">
                                    <h3 class="text-lg font-bold text-gray-900">Gestionar Instituciones</h3>
                                    <p class="mt-2 text-sm text-gray-600">Administrar instituciones participantes.</p>
                                </div>
                            </div>
                        </a>

                        {{-- Acción: Gestionar Solicitudes --}}
                        <a href="{{ route('admin.solicitudes.gestionar') }}" class="group relative block overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
                            <span class="absolute -right-4 -top-4 z-0 h-20 w-20 rounded-full bg-yellow-50 transition-all duration-300 group-hover:scale-[10] pointer-events-none"></span>
                            <div class="relative z-10">
                                <div class="inline-flex rounded-lg bg-yellow-100 p-4 text-yellow-600"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                <div class="mt-6">
                                    <h3 class="text-lg font-bold text-gray-900">Gestionar Solicitudes</h3>
                                    <p class="mt-2 text-sm text-gray-600">Revisar y aprobar nuevas solicitudes.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>