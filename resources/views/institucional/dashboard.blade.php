@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    {{-- Encabezado del Panel con Imagen de Fondo --}}
    <div class="relative overflow-hidden shadow-2xl" style="background-image: url('{{ asset('images/gradient-bg.jpg') }}'); background-size: cover; background-position: bottom; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full">
                <div class="absolute top-10 left-10 w-72 h-72 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>
            </div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-32">
                <div class="flex items-center space-x-8">
                    <div class="text-white">
                        <h1 class="text-4xl font-bold mb-2">Panel Institucional</h1>
                        <p class="text-blue-100 text-lg">
                            Bienvenido, <span class="font-semibold">{{ $user->name }}</span>
                        </p>
                        <p class="text-blue-200 text-sm">
                            {{ $institucion->nombre }}
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
            <div class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Participantes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $participantesCount }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-xl font-bold">P</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Solicitudes Pendientes --}}
            <div class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Solicitudes Pendientes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $solicitudesPendientes }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-xl font-bold">S</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estado de la Institución --}}
            <div class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Estado</p>
                            <p class="text-3xl font-bold {{ $institucion->validada ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $institucion->validada ? 'Verificada' : 'Pendiente' }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-xl font-bold">V</span>
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
            <a href="{{ route('institucional.participantes') }}" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-white text-2xl font-bold">GP</span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Gestionar Participantes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Ver, activar o desactivar miembros de la institución y gestionar sus permisos.</p>
            </a>

            {{-- Revisar Solicitudes --}}
            <a href="#" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-white text-2xl font-bold">RS</span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Revisar Solicitudes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Aprobar o rechazar solicitudes de nuevos miembros de tu institución.</p>
            </a>

            {{-- Ver Reportes --}}
            <a href="{{ route('institucional.reportes') }}" class="group relative block overflow-hidden rounded-3xl bg-white p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <span class="text-white text-2xl font-bold">VR</span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Ver Reportes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Acceder a informes detallados y estadísticas del sistema.</p>
            </a>

        </div>
    </div>
</div>
@endsection
