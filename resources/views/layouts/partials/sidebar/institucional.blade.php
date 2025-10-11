<!-- resources/views/layouts/partials/sidebar/institucional.blade.php -->
<nav class="mt-6">
    <div>
        <a href="{{ route('institucional.dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('institucional.dashboard') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <x-heroicon-o-home class="w-6 h-6 mr-3" />
            Inicio
        </a>
    </div>

    <div class="mt-8">
        <h3 class="px-4 text-xs text-gray-500 uppercase tracking-wider font-semibold">Gestión</h3>
        <div class="mt-4 space-y-1">
            <a href="{{ route('institucional.participantes.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('institucional.participantes.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <x-heroicon-o-user-group class="w-6 h-6 mr-3" />
                <span>Participantes</span>
            </a>
            <a href="{{ route('institucional.reportes.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('institucional.reportes.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <x-heroicon-o-document-chart-bar class="w-6 h-6 mr-3" />
                <span>Reportes</span>
            </a>
            <a href="{{ route('institucional.mapa.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('institucional.mapa.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <x-heroicon-o-map class="w-6 h-6 mr-3" />
                <span>Mapa</span>
            </a>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="px-4 text-xs text-gray-500 uppercase tracking-wider font-semibold">Sistema</h3>
        <div class="mt-4 space-y-1">
            <a href="{{ route('institucional.configuracion.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('institucional.configuracion.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <x-heroicon-o-cog-6-tooth class="w-6 h-6 mr-3" />
                <span>Configuración</span>
            </a>
        </div>
    </div>
</nav>
