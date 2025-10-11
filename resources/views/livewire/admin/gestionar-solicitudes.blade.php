<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
         <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestionar Solicitudes de Instituciones</h1>
    
         
   
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                               Institución y Ubicación
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto Solicitante
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mensaje
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Acciones</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($solicitudes as $solicitud)
                            <tr>
                                {{-- Columna Institución y Ubicación --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $solicitud->nombre_institucion }}</div>
                                    <div class="text-sm text-gray-500">{{ $solicitud->localidad }}, {{ $solicitud->provincia }}</div>
                                </td>
   
                                {{-- Columna Contacto Solicitante --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $solicitud->nombre_solicitante }}</div>
                                    {{-- CAMPO CORREGIDO: de email_solicitante a email_contacto --}}
                                    <div class="text-sm text-gray-500">{{ $solicitud->email_contacto }}</div>
                                   @if($solicitud->telefono_contacto)
                                        <div class="text-sm text-gray-500">Tel: {{ $solicitud->telefono_contacto }}</div>
                                    @endif
                                </td>
   
                                {{-- Columna Mensaje --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 max-w-xs truncate" title="{{ $solicitud->mensaje }}">
                                        {{ $solicitud->mensaje ?: 'N/A' }}
                                    </p>
                                </td>
   
                                {{-- Columna Fecha --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                                </td>
   
                                {{-- Columna Acciones --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button wire:click="aprobarSolicitud({{ $solicitud->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">Aprobar</button>
                                    <button wire:click="rechazarSolicitud({{ $solicitud->id }})"
                                        class="text-red-600 hover:text-red-900 font-semibold">Rechazar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    No hay solicitudes pendientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
   
        {{-- Paginación --}}
        <div class="mt-4">
            {{ $solicitudes->links() }}
        </div>
    </div>