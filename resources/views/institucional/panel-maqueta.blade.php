<x-institucional-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Institucional (Maqueta)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Estadísticas Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm font-medium text-gray-500">Total Participantes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['participantes'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm font-medium text-gray-500">Solicitudes Pendientes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['solicitudes'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm font-medium text-gray-500">Estado</p>
                    <p class="text-2xl font-bold text-green-600">Verificada</p>
                </div>
            </div>

            {{-- Layout de 2 Columnas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Columna Principal (Tabs) --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6" x-data="{ activeTab: 'participantes' }">
                    
                    {{-- Navegación de Tabs --}}
                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-6">
                            <a href="#" @click.prevent="activeTab = 'participantes'" 
                               :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'participantes', 'text-gray-500 hover:text-gray-700': activeTab !== 'participantes' }"
                               class="whitespace-nowrap py-3 px-1 font-medium text-sm">
                               Participantes
                            </a>
                            <a href="#" @click.prevent="activeTab = 'solicitudes'" 
                               :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'solicitudes', 'text-gray-500 hover:text-gray-700': activeTab !== 'solicitudes' }"
                               class="whitespace-nowrap py-3 px-1 font-medium text-sm">
                               Solicitudes Pendientes
                            </a>
                            <a href="#" @click.prevent="activeTab = 'actividad'" 
                               :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'actividad', 'text-gray-500 hover:text-gray-700': activeTab !== 'actividad' }"
                               class="whitespace-nowrap py-3 px-1 font-medium text-sm">
                               Actividad Reciente
                            </a>
                        </nav>
                    </div>

                    {{-- Contenido de los Tabs --}}
                    <div>
                        {{-- Tab de Participantes --}}
                        <div x-show="activeTab === 'participantes'" x-cloak>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Últimos Participantes Agregados</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Juan Perez</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Técnico</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="#" class="text-indigo-600 hover:text-indigo-900">Editar</a></td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Maria Garcia</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Investigadora</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="#" class="text-indigo-600 hover:text-indigo-900">Editar</a></td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Carlos Lopez</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Administrativo</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Inactivo</span></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="#" class="text-indigo-600 hover:text-indigo-900">Editar</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="#" class="inline-block mt-4 text-sm text-blue-600 hover:text-blue-800">Ver todos los participantes &rarr;</a>
                        </div>

                        {{-- Tab de Solicitudes --}}
                        <div x-show="activeTab === 'solicitudes'" x-cloak>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Solicitudes para Unirse</h3>
                            <p class="text-sm text-gray-600">No hay solicitudes pendientes en este momento.</p>
                        </div>

                        {{-- Tab de Actividad --}}
                        <div x-show="activeTab === 'actividad'" x-cloak>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Actividad Reciente en la Institución</h3>
                            <ul class="divide-y divide-gray-200">
                                <li class="py-3 flex justify-between items-center">
                                    <p class="text-sm text-gray-800"><span class="font-semibold">Juan Perez</span> actualizó los datos de un productor.</span></p>
                                    <span class="text-xs text-gray-500">hace 2 horas</span>
                                </li>
                                <li class="py-3 flex justify-between items-center">
                                    <p class="text-sm text-gray-800"><span class="font-semibold">Maria Garcia</span> generó un nuevo reporte de stock.</span></p>
                                    <span class="text-xs text-gray-500">hace 5 horas</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                {{-- Columna Lateral (Acciones) --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="flex items-center p-3 rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors">- Invitar nuevo participante</a></li>
                        <li><a href="#" class="flex items-center p-3 rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors">- Generar Reporte General</a></li>
                        <li><a href="#" class="flex items-center p-3 rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors">- Ver mapa de productores</a></li>
                        <li><a href="#" class="flex items-center p-3 rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors">- Editar datos de la institución</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-institucional-layout>