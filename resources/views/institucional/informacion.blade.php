@extends('layouts.institucional')

@section('title', 'Información Institucional')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Información Institucional</h1>
            
            @if($institucion)
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-3">Datos de la Institución</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nombre</label>
                                <p class="text-gray-900">{{ $institucion->nombre }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Estado</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $institucion->validada ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $institucion->validada ? 'Verificada' : 'Pendiente' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-3">Descripción</h2>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $institucion->descripcion ?? 'No hay descripción disponible.' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Institución no encontrada</h3>
                    <p class="text-gray-600">No se pudo cargar la información de la institución.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

