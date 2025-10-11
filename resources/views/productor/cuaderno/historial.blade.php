@extends('layouts.cuaderno')

@section('cuaderno_content')
<div class="h-full bg-gray-200 p-4 space-y-6" 
     x-data="{
        periodos: {{ json_encode($periodos->keyBy('id')) }},
        fecha_desde: '{{ $filters['fecha_desde'] ?? '' }}',
        fecha_hasta: '{{ $filters['fecha_hasta'] ?? '' }}',
        summaryModalOpen: false,
        updateFechas(periodoId) {
            if (!periodoId || !this.periodos[periodoId]) {
                this.fecha_desde = '';
                this.fecha_hasta = '';
                return;
            }
            let periodo = this.periodos[periodoId];
            this.fecha_desde = periodo.ultima_actualizacion.split('T')[0];
            this.fecha_hasta = periodo.proxima_actualizacion.split('T')[0];
        }
     }">

    <!-- Filtros -->
    <form method="GET" action="{{ route('cuaderno.historial') }}">
        <fieldset class="border-2 border-gray-400 p-4">
            <legend class="px-2 font-bold text-gray-800">Historial de Movimientos</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                
                <!-- Row 1 -->
                <!-- Filtro Rápido por Período -->
                <div>
                    <label for="periodo_preset" class="block text-sm font-medium text-gray-700 mb-1">Filtro Rápido por Período</label>
                    <select id="periodo_preset" @change="updateFechas($event.target.value)" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                        <option value="">Seleccione un período...</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">
                                {{ $periodo->ultima_actualizacion->format('d/m/Y') }} - {{ $periodo->proxima_actualizacion->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtros de Fecha -->
                <div>
                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" x-model="fecha_desde" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                </div>
                <div>
                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" x-model="fecha_hasta" :min="fecha_desde" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                </div>
                
                <!-- Filtro UP -->
                <div>
                    <label for="up_id" class="block text-sm font-medium text-gray-700 mb-1">Unidad Productiva</label>
                    <select id="up_id" name="up_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                        <option value="">Todas</option>
                        @foreach ($unidadesProductivas as $up)
                            <option value="{{ $up->id }}" {{ ($filters['up_id'] ?? '') == $up->id ? 'selected' : '' }}>
                                {{ $up->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2 -->
                <!-- Filtro Flujo -->
                <div>
                    <label for="flujo" class="block text-sm font-medium text-gray-700 mb-1">Flujo</label>
                    <select id="flujo" name="flujo" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                        <option value="">Todos</option>
                        <option value="alta" {{ ($filters['flujo'] ?? '') == 'alta' ? 'selected' : '' }}>Altas</option>
                        <option value="baja" {{ ($filters['flujo'] ?? '') == 'baja' ? 'selected' : '' }}>Bajas</option>
                    </select>
                </div>

                <!-- Filtro Motivo -->
                <div>
                    <label for="motivo_id" class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                    <select id="motivo_id" name="motivo_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-base shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($motivos as $motivo)
                            <option value="{{ $motivo->id }}" {{ ($filters['motivo_id'] ?? '') == $motivo->id ? 'selected' : '' }}>
                                {{ $motivo->nombre }} ({{ $motivo->tipo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón y Limpiar -->
                <div class="col-span-1 md:col-span-2 lg:col-span-2 flex items-end space-x-4">
                    <button type="submit" class="h-11 w-full px-4 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('cuaderno.historial') }}" class="h-11 w-full px-4 bg-red-500 border-2 border-gray-500 hover:bg-red-600 font-semibold flex items-center justify-center shadow-sm text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        <span>Limpiar</span>
                    </a>
                </div>
            </div>
        </fieldset>
    </form>

    <!-- Resumen y Exportación -->
    <div class="bg-blue-100 border-t-4 border-b-4 border-blue-500 p-4 flex justify-between items-center shadow-md">
        <div>
            <p class="text-sm text-blue-800">Mostrando resultados para los filtros aplicados</p>
            <p class="font-bold text-blue-900 text-lg">
                @if (!empty($filters['fecha_desde']) || !empty($filters['fecha_hasta']))
                    Rango: {{ $filters['fecha_desde'] ?? 'Inicio' }} a {{ $filters['fecha_hasta'] ?? 'Hoy' }}
                @else
                    Sin filtro de fecha
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-4">
            <button @click="summaryModalOpen = true" type="button" class="px-4 py-2 bg-blue-800 border-2 border-gray-500 hover:bg-blue-900 text-white font-bold flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z" /><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.414l2.26-2.26A4 4 0 1011 5z" clip-rule="evenodd" /></svg>
                Ver Resumen
            </button>
            @if($hasFilters)
            <a href="{{ route('cuaderno.historial.exportar-pdf', request()->query()) }}" target="_blank" class="px-4 py-2 bg-green-600 border-2 border-gray-500 hover:bg-green-700 text-white font-bold flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" /></svg>
                Exportar a PDF
            </a>
            @endif
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="overflow-x-auto border-2 border-gray-400">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-300 border-b-2 border-gray-400">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Fecha</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">U. Productiva</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Tipo</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Motivo</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Especie</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Categoría</th>
                    <th class="px-4 py-2 text-right text-sm font-bold text-gray-700">Cantidad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-400">
                @forelse ($movimientos as $movimiento)
                    <tr class="hover:bg-gray-200 {{ $loop->odd ? 'bg-gray-50' : '' }}">
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ $movimiento->fecha_registro->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ $movimiento->unidadProductiva->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold border-r-2 border-gray-400 {{ optional($movimiento->motivo)->tipo === 'alta' ? 'text-green-600' : 'text-red-600' }}">
                            {{ strtoupper(optional($movimiento->motivo)->tipo ?? 'N/A') }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->motivo)->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->especie)->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->categoria)->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 font-bold text-right">
                            {{ optional($movimiento->motivo)->tipo === 'alta' ? '+' : '-' }} {{ $movimiento->cantidad }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            @if($hasFilters)
                                <p>No se encontraron movimientos para los filtros seleccionados.</p>
                            @else
                                <p>Por favor, aplique un filtro para ver los resultados.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if ($movimientos->hasPages())
        <div class="mt-4">
            {{ $movimientos->links() }}
        </div>
    @endif

    <!-- Summary Modal -->
    <div x-show="summaryModalOpen" @keydown.escape.window="summaryModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="summaryModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="summaryModalOpen = false" aria-hidden="true"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="summaryModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-gray-50 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Resumen de Movimientos Filtrados
                            </h3>
                            <div class="mt-4">
                                @if ($resumen['altas']['total'] > 0 || $resumen['bajas']['total'] > 0)
                                    <div class="overflow-x-auto border-2 border-gray-300">
                                        <table class="min-w-full bg-white">
                                            <thead class="bg-gray-300 border-b-2 border-gray-400">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Concepto</th>
                                                    <th class="px-4 py-2 text-right text-sm font-bold text-gray-700">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-400">
                                                @if (!empty($filters['motivo_id']))
                                                    <tr class="bg-blue-50 hover:bg-blue-100">
                                                        <td class="px-4 py-2 font-bold text-blue-800 border-r-2 border-gray-400">Total para {{ $resumen['motivo_filtrado'] }}</td>
                                                        <td class="px-4 py-2 font-bold text-blue-900 text-right">{{ $resumen['specific_total'] }}</td>
                                                    </tr>
                                                @else
                                                    @if (empty($filters['flujo']) || $filters['flujo'] === 'alta')
                                                        @if ($resumen['altas']['total'] > 0)
                                                            <tr class="bg-green-200">
                                                                <td class="px-4 py-2 text-left text-sm font-bold text-green-800 border-r-2 border-gray-400" colspan="2">ALTAS</td>
                                                            </tr>
                                                            @foreach ($resumen['altas'] as $motivo => $total)
                                                                @if ($motivo !== 'total')
                                                                    <tr class="hover:bg-gray-200">
                                                                        <td class="px-4 py-2 pl-8 text-gray-700 border-r-2 border-gray-400">{{ $motivo }}</td>
                                                                        <td class="px-4 py-2 text-gray-900 text-right">{{ $total }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                            <tr class="bg-green-100 font-bold">
                                                                <td class="px-4 py-2 pl-8 text-green-800 border-r-2 border-gray-400">Total Altas</td>
                                                                <td class="px-4 py-2 text-green-900 text-right">{{ $resumen['altas']['total'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endif

                                                    @if (empty($filters['flujo']) || $filters['flujo'] === 'baja')
                                                         @if ($resumen['bajas']['total'] > 0)
                                                            <tr class="bg-red-200">
                                                                <td class="px-4 py-2 text-left text-sm font-bold text-red-800 border-r-2 border-gray-400" colspan="2">BAJAS</td>
                                                            </tr>
                                                            @foreach ($resumen['bajas'] as $motivo => $total)
                                                                @if ($motivo !== 'total')
                                                                    <tr class="hover:bg-gray-200">
                                                                        <td class="px-4 py-2 pl-8 text-gray-700 border-r-2 border-gray-400">{{ $motivo }}</td>
                                                                        <td class="px-4 py-2 text-gray-900 text-right">{{ $total }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                            <tr class="bg-red-100 font-bold">
                                                                <td class="px-4 py-2 pl-8 text-red-800 border-r-2 border-gray-400">Total Bajas</td>
                                                                <td class="px-4 py-2 text-red-900 text-right">{{ $resumen['bajas']['total'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500">No hay datos de resumen para los filtros seleccionados.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 px-4 py-3 sm:px-6">
                    <div class="text-sm text-gray-600">
                        <p><span class="font-bold">Nota:</span> Al exportar a PDF se incluirá esta tabla de resumen y los gráficos correspondientes.</p>
                    </div>
                </div>
                <div class="bg-gray-200 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="summaryModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection