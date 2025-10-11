<!-- resources/views/layouts/partials/sidebar/productor.blade.php -->
<nav class="px-2 py-4 space-y-6">
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
