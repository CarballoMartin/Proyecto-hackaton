<x-admin-layout>

    <!-- Notificación Flotante (Mini-Modal) -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="fixed top-8 left-1/2 -translate-x-1/2 bg-green-500 text-white py-3 px-5 rounded-lg shadow-lg z-50 flex items-center">
            <p>{{ session('success') }}</p>
            <button @click="show = false" class="ml-4 text-xl font-semibold">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="fixed top-8 left-1/2 -translate-x-1/2 bg-red-500 text-white py-3 px-5 rounded-lg shadow-lg z-50 flex items-center">
            <p>{{ session('error') }}</p>
            <button @click="show = false" class="ml-4 text-xl font-semibold">&times;</button>
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Gestión de Instituciones
        </h2>
    </x-slot>

    <div x-data="{
        openValidateModal: false,
        openRejectModal: false,
        openDeactivateModal: false, // Nuevo
        openDeleteModal: false,     // Nuevo
        validationUrl: '',
        rejectionUrl: '',
        deactivationUrl: '', // Nuevo
        deletionUrl: ''       // Nuevo
    }">
        <div class="p-6 sm:p-8 space-y-8">

            

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros y Búsqueda</h3>
                <form method="GET" action="{{ route('admin.instituciones.panel') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, CUIT..." class="mt-1 p-2 border border-gray-300 rounded-md w-full shadow-sm">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="status" name="status" class="mt-1 p-2 border border-gray-300 rounded-md w-full shadow-sm">
                                <option value="aprobada" @if(request('status', 'aprobada') == 'aprobada') selected @endif>Aprobada</option>
                                <option value="no_aprobada" @if(request('status') == 'no_aprobada') selected @endif>No Aprobada</option>
                                <option value="pendiente" @if(request('status') == 'pendiente') selected @endif>Pendiente</option>
                                <option value="todos" @if(request('status') == 'todos') selected @endif>Todos</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md shadow-sm h-10">Aplicar Filtros</button>
                    </div>
                </form>
            </div>

            <!-- Tabla Principal -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 flex justify-between items-center border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Resultados</h3>
                    <button @click.prevent="$dispatch('open-institucion-modal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Nueva Institución</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CUIT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y divide-gray-200">
                            @if ($itemType === 'institucion')
                                @forelse ($items as $institucion)
                                    @include('admin.instituciones.partials.institucion-row', ['institucion' => $institucion])
                                @empty
                                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron instituciones.</td></tr>
                                @endforelse
                            @elseif ($itemType === 'solicitud')
                                @forelse ($items as $solicitud)
                                     @include('admin.instituciones.partials.solicitud-row', ['solicitud' => $solicitud])
                                @empty
                                     <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron solicitudes pendientes.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-white border-t">
                    {{ $items->appends($filters)->links() }}
                </div>
            </div>

            <!-- Tabla de Gestión de Solicitudes Pendientes -->
            <div id="gestion-solicitudes" class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Gestión de Solicitudes Pendientes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50"> 
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institución y Ubicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y divide-gray-200">
                            @forelse ($solicitudesPendientes as $solicitud)
                                @include('admin.instituciones.partials.gestion-solicitud-row', ['solicitud' => $solicitud])
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay solicitudes pendientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @include('admin.instituciones.partials.activity-log')

        </div>

        <!-- Modals -->
        <x-modals.institucion-form />
        @include('admin.instituciones.partials.validate-modal')
        @include('admin.instituciones.partials.reject-modal')
        @include('admin.instituciones.partials.deactivate-modal')
        @include('admin.instituciones.partials.delete-modal')

    </div>
</x-admin-layout>