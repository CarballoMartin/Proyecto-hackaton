<!-- resources/views/layouts/partials/sidebar/admin.blade.php -->
<nav class="mt-6">
    <div>
        <a href="{{ route('admin.panel') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.panel') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <x-heroicon-o-home class="w-6 h-6 mr-3" />
            Inicio
        </a>
    </div>
    <div class="mt-2">
        <a href="{{ route('admin.panel.maqueta') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.panel.maqueta') ? 'bg-yellow-200 text-yellow-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <x-heroicon-o-sparkles class="w-6 h-6 mr-3 text-yellow-600" />
            Ver Maqueta Dashboard
        </a>
    </div>

    <div class="mt-8">
        <h3 class="px-4 text-xs text-gray-500 uppercase tracking-wider font-semibold">Gestión</h3>
        <div class="mt-4 space-y-1">
            <!-- Productores Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.productores.*') || request()->routeIs('admin.productores.importar') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="flex items-center">
                        <x-heroicon-o-user-group class="w-6 h-6 mr-3" />
                        <span>Productores</span>
                    </div>
                    <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-cloak class="pl-10 mt-2 space-y-1">
                    <a href="{{ route('admin.productores.panel') }}" class="block px-4 py-2 text-sm rounded-md {{ request()->routeIs('admin.productores.panel') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Panel Productores</a>
                    <button type="button" @click="$dispatch('open-productor-form-modal')" class="block w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md">Registrar Productor</button>
                    <a href="{{ route('admin.productores.importar') }}" class="block px-4 py-2 text-sm rounded-md {{ request()->routeIs('admin.productores.importar') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Importar Productores</a>
                    <a href="{{ route('admin.productores.listado') }}" class="block px-4 py-2 text-sm rounded-md {{ request()->routeIs('admin.productores.listado') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Listar / Modificar</a>
                </div>
            </div>

            <!-- Instituciones Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.instituciones.*') || request()->routeIs('admin.solicitudes.gestionar') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="flex items-center">
                        <x-heroicon-o-building-office-2 class="w-6 h-6 mr-3" />
                        <span>Instituciones</span>
                    </div>
                    <svg class="h-5 w-5 transform transition-transform duration-150" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" x-cloak class="pl-10 mt-2 space-y-1">
                    <a href="{{ route('admin.instituciones.panel') }}" class="block px-4 py-2 text-sm rounded-md {{ request()->routeIs('admin.instituciones.panel') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Panel Instituciones</a>
                    <button type="button" @click="$dispatch('open-institucion-modal')" class="block w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md">Registrar Institución</button>
                    <a href="{{ route('admin.solicitudes.gestionar') }}" class="block px-4 py-2 text-sm rounded-md {{ request()->routeIs('admin.solicitudes.gestionar') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Gestionar Solicitudes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="px-4 text-xs text-gray-500 uppercase tracking-wider font-semibold">Herramientas</h3>
        <div class="mt-4 space-y-1">
            <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
                <x-heroicon-o-map class="w-6 h-6 mr-3" />
                Geolocalización
            </a>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="px-4 text-xs text-gray-500 uppercase tracking-wider font-semibold">Sistema</h3>
        <div class="mt-4 space-y-1">
            <button @click.prevent="$dispatch('open-configuracion-modal')" class="w-full flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                <x-heroicon-o-cog-6-tooth class="w-6 h-6 mr-3" />
                <span>Configuración</span>
            </button>
        </div>
    </div>
</nav>