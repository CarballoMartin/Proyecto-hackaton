<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-50">
    {{-- Encabezado del Dashboard --}}
    <div class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard del Productor</h1>
                    <p class="text-gray-600 mt-2">Bienvenido, {{ $productor->nombre ?? 'Productor' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Última actualización</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $ultimaActualizacion ? $ultimaActualizacion->format('d/m/Y H:i') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas Principales --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total de Campos --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Campos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCampos }}</p>
                    </div>
                </div>
            </div>

            {{-- Total de Ovinos --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Ovinos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalOvinos }}</p>
                    </div>
                </div>
            </div>

            {{-- Total de Caprinos --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Caprinos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCaprinos }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones Rápidas --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Acciones Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('productor.unidades-productivas.index') }}" 
                   class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Gestionar Campos</p>
                        <p class="text-sm text-gray-500">Ver y administrar tus campos</p>
                    </div>
                </a>

                <a href="{{ route('productor.stock.index') }}" 
                   class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Stock Animal</p>
                        <p class="text-sm text-gray-500">Gestionar inventario</p>
                    </div>
                </a>

                <a href="{{ route('cuaderno.index') }}" 
                   class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Cuaderno de Campo</p>
                        <p class="text-sm text-gray-500">Registrar movimientos</p>
                    </div>
                </a>

                <a href="{{ route('productor.reportes.index') }}" 
                   class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Reportes</p>
                        <p class="text-sm text-gray-500">Ver estadísticas</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Información del Productor --}}
        @if($productor)
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Información del Productor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Datos Personales</h3>
                    <div class="space-y-2">
                        <p class="text-sm"><span class="font-medium">Nombre:</span> {{ $productor->nombre }}</p>
                        <p class="text-sm"><span class="font-medium">DNI:</span> {{ $productor->dni }}</p>
                        <p class="text-sm"><span class="font-medium">Municipio:</span> {{ $productor->municipio }}</p>
                        <p class="text-sm"><span class="font-medium">Paraje:</span> {{ $productor->paraje }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Contacto</h3>
                    <div class="space-y-2">
                        <p class="text-sm"><span class="font-medium">Teléfono:</span> {{ $productor->telefono }}</p>
                        <p class="text-sm"><span class="font-medium">Dirección:</span> {{ $productor->direccion }}</p>
                        <p class="text-sm"><span class="font-medium">Estado:</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $productor->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $productor->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>




















