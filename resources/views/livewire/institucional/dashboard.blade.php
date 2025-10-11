<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    

    {{-- Encabezado del Panel con Imagen de Fondo --}}
        <div class="relative overflow-hidden shadow-2xl" style="background-image: url('{{ asset('imagenes-campos/header-institucional.png') }}'); background-size: cover; background-position: center top; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full">
                <div class="absolute top-10 left-10 w-72 h-72 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-green-400 opacity-20 rounded-full blur-3xl"></div>
            </div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-32">
                <div class="flex items-center space-x-8">
                    @if($institucion && $institucion->logo_path)
                    <div class="flex-shrink-0">
                        <img src="{{ asset($institucion->logo_path) }}" 
                             alt="Logo {{ $institucion->nombre }}" 
                             class="w-20 h-20 rounded-2xl shadow-2xl object-contain bg-white p-2">
                    </div>
                    @endif
                    <div class="text-white">
                        <h1 class="text-4xl font-bold mb-2">Panel Institucional</h1>
                        <p class="text-blue-100 text-lg">
                            Bienvenido, <span class="font-semibold">{{ Auth::user()->name }}</span>
                        </p>
                        <p class="text-blue-200 text-sm">
                            {{ optional($institucion)->nombre ?? 'Sin institución asociada aún' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12" style="position: relative; top: -60px;">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Total de Participantes --}}
            <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-blue-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-700">Total Participantes</p>
                            <p class="text-3xl font-bold text-blue-900">{{ $participantesCount ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Solicitudes Pendientes --}}
            <div class="group relative bg-gradient-to-br from-yellow-50 to-orange-100 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-yellow-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-700">Solicitudes Pendientes</p>
                            <p class="text-3xl font-bold text-yellow-900">{{ $solicitudesPendientes ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actividad Reciente --}}
            <div class="group relative bg-gradient-to-br from-purple-50 to-indigo-100 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-purple-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-700">Actividad Reciente</p>
                            <p class="text-3xl font-bold text-purple-900">{{ $estadisticas['nuevos_miembros_mes'] ?? 0 }}</p>
                            <p class="text-xs text-purple-600 mt-1">nuevos miembros este mes</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones Principales --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Acciones Rápidas</h2>
            <p class="text-gray-600">Selecciona una opción para gestionar tu institución</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            {{-- Gestionar Participantes --}}
            <a href="{{ route('institucional.participantes.index') }}" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Gestionar Participantes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Ver, activar o desactivar miembros de la institución y gestionar sus permisos.</p>
            </a>

            {{-- Revisar Solicitudes --}}
            <button wire:click="revisarSolicitudes" wire:loading.attr="disabled" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 disabled:opacity-50">
                <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Revisar Solicitudes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Aprobar o rechazar solicitudes de nuevos miembros de tu institución.</p>
            </button>

            {{-- Ver Reportes --}}
            <button wire:click="verReportes" wire:loading.attr="disabled" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 disabled:opacity-50">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Ver Reportes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Acceder a informes detallados y estadísticas del sistema.</p>
            </button>

        </div>
    </div>
</div>

