<div>
    {{-- Contenido principal: Búsqueda y Tabla + Modal de advertencia --}}
    <div x-data="{ confirmar: false, productorId: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="mb-4">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o DNI..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        DNI</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Localidad</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Campo</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($productores as $productor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $productor->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $productor->dni }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $productor->usuario->email ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $productor->municipio}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            @if($productor->unidadesProductivas && $productor->unidadesProductivas->count() > 0)
                                                <a href="{{ route('admin.mapa') }}?productor={{ $productor->id }}" 
                                                   class="inline-flex items-center text-blue-600 hover:text-blue-800"
                                                   title="Ver {{ $productor->unidadesProductivas->count() }} UP en el mapa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 22s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z" />
                                                    </svg>
                                                    <span class="ml-1 text-xs">{{ $productor->unidadesProductivas->count() }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center text-gray-400" title="Sin UPs registradas">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button @click="confirmar = true; productorId = {{ $productor->id }}"
                                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                                    {{ $productor->usuario && $productor->usuario->activo ? 'bg-green-500' : 'bg-red-500' }}">
                                                <span
                                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-lg transform ring-0 transition ease-in-out duration-200
                                                        {{ $productor->usuario && $productor->usuario->activo ? 'translate-x-5' : 'translate-x-0' }}">
                                                </span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click.prevent="$dispatch('open-edit-productor-modal', { id: {{ $productor->id }} })"
                                                class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No se encontraron productores.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6">
                        {{ $productores->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de advertencia con Alpine.js -->
        <template x-if="confirmar">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75">
                <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmar cambio de estado</h3>
                    <p class="mb-6 text-gray-700">¿Estás seguro que deseas cambiar el estado de este productor?</p>
                    <div class="flex justify-end space-x-4">
                        <button @click="confirmar = false"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button @click="$wire.toggleProductorStatus(productorId); confirmar = false"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Modal de edición refactorizado a componente de Blade/Alpine --}}
</div>