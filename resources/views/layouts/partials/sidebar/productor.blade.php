<!-- resources/views/layouts/partials/sidebar/productor.blade.php -->
<nav class="py-4 space-y-6">
    <!-- Sección Principal -->
    <div>
        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Principal</h3>
        <div class="mt-2 space-y-1">
            <a href="{{ route('productor.panel') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-home class="h-6 w-6 mr-3" />
                <span>Inicio</span>
            </a>

            <button @click.prevent="$dispatch('open-productor-perfil-modal')" 
                    class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-user-circle class="h-6 w-6 mr-3" />
                <span>Mi Perfil</span>
            </button>
        </div>
    </div>

    <!-- Sección Gestión -->
    <div>
        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestión Productiva</h3>
        <div class="mt-2 space-y-1">
            <a href="{{ route('cuaderno.index') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-book-open class="h-6 w-6 mr-3" />
                <span>Cuaderno de Campo</span>
            </a>
            <a href="{{ route('productor.stock.index') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-table-cells class="h-6 w-6 mr-3" />
                <span>Mi Stock</span>
            </a>
            <a href="{{ route('productor.unidades-productivas.index') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-map-pin class="h-6 w-6 mr-3" />
                <span>Mis Campos</span>
            </a>
        </div>
    </div>

    <!-- Sección Monitoreo Ambiental -->
    <div>
        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Monitoreo Ambiental</h3>
        <div class="mt-2 space-y-1">
            <a href="{{ route('productor.ambiental') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ request()->routeIs('productor.ambiental') ? 'bg-gray-100 text-gray-900' : '' }}">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="flex-1">Vista General</span>
                @php
                    $cantidadAlertas = \App\Models\AlertaAmbiental::activas()->whereHas('unidadProductiva', function($query) {
                        $productor = \App\Models\Productor::where('usuario_id', Auth::id())->first();
                        if ($productor) {
                            $query->where('productor_id', $productor->id);
                        }
                    })->count();
                @endphp
                @if($cantidadAlertas > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-600 text-white rounded-full">{{ $cantidadAlertas }}</span>
                @endif
            </a>
            <a href="{{ route('productor.ambiental.ndvi') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ request()->routeIs('productor.ambiental.ndvi') ? 'bg-gray-100 text-gray-900' : '' }}">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                <span>NDVI Satelital</span>
            </a>
            <a href="{{ route('productor.huella-carbono') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ request()->routeIs('productor.huella-carbono') ? 'bg-gray-100 text-gray-900' : '' }}">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Huella de Carbono</span>
            </a>
        </div>
    </div>

    <!-- Sección Análisis y Datos -->
    <div>
        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Análisis y Datos</h3>
        <div class="mt-2 space-y-1">
            <a href="{{ route('productor.estadisticas.index') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-chart-pie class="h-6 w-6 mr-3" />
                <span>Estadísticas</span>
            </a>
            <a href="{{ route('productor.reportes.index') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-document-arrow-down class="h-6 w-6 mr-3" />
                <span>Reportes</span>
            </a>
        </div>
    </div>
</nav>
