<x-productor-layout>
    <x-slot name="header_title">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inicio
        </h2>
    </x-slot>

    {{-- Internal Data Carousel --}}
    <div class="relative bg-indigo-600 text-white rounded-lg shadow-lg p-6 mb-8 overflow-hidden">
        <div class="flex space-x-12 animate-scroll-and-repeat">
            @php
                // Datos reales calculados desde la base de datos
                $user = Auth::user();
                $productor = \App\Models\Productor::where('usuario_id', $user->id)->first();
                
                $totalCampos = $productor ? $productor->unidadesProductivas->count() : 0;
                $totalStock = $productor ? \App\Models\StockAnimal::whereIn('unidad_productiva_id', $productor->unidadesProductivas->pluck('id'))->sum('cantidad') : 0;
                
                // Últimos registros de stock (últimos 7 días)
                $registrosRecientes = $productor ? \App\Models\StockAnimal::whereIn('unidad_productiva_id', $productor->unidadesProductivas->pluck('id'))
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count() : 0;
                
                // Crear array de estadísticas dinámicas
                $stats = [
                    ['icon' => 'heroicon-s-home', 'label' => 'Unidades Productivas', 'value' => $totalCampos, 'color' => 'text-green-300'],
                    ['icon' => 'heroicon-s-cube', 'label' => 'Total de Animales', 'value' => $totalStock, 'color' => 'text-blue-300'],
                    ['icon' => 'heroicon-s-clipboard-document-list', 'label' => 'Registros Recientes (7d)', 'value' => $registrosRecientes, 'color' => 'text-indigo-300'],
                ];
                
                // Agregar estadísticas por especie dinámicamente
                if (isset($stockPorEspecie) && $stockPorEspecie->isNotEmpty()) {
                    foreach ($stockPorEspecie as $especie => $cantidad) {
                        $stats[] = [
                            'icon' => 'heroicon-s-chart-bar', 
                            'label' => $especie, 
                            'value' => $cantidad, 
                            'color' => 'text-green-300'
                        ];
                    }
                }
            @endphp
            @foreach (array_merge($stats, $stats) as $stat) {{-- Duplicate for seamless loop --}}
                <div class="flex-shrink-0 flex items-center space-x-4">
                    <x-dynamic-component :component="$stat['icon']" :class="'h-8 w-8 ' . $stat['color']" />
                    <div>
                        <div class="text-sm text-indigo-200">{{ $stat['label'] }}</div>
                        <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Evolution Graphs --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Evolución del Stock</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="evolucionCharts">
                    @if(isset($datosHistoricos['datosPorEspecie']))
                        @foreach($datosHistoricos['datosPorEspecie'] as $especie => $datos)
                            @php
                                $chartId = strtolower(str_replace(' ', '_', $especie)) . '_chart';
                            @endphp
                            <div class="border rounded-lg p-4">
                                <p class="text-sm text-gray-600">{{ $especie }}: Últimos 6 meses</p>
                                <div class="mt-4 h-48">
                                    <canvas id="{{ $chartId }}"></canvas>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-2 text-center text-gray-500">
                            No hay datos históricos disponibles
                        </div>
                    @endif
                </div>
            </div>

            {{-- Animal Stock Preview --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vista Previa de Stock</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($stockPreview) && $stockPreview->count() > 0)
                                @foreach($stockPreview as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->especie->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $item->cantidad }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No hay datos de stock disponibles
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Side Column --}}
        <div class="space-y-8">
            {{-- Widget de Clima --}}
            @livewire('productor.clima-widget')

            {{-- Panel de Alertas Ambientales (NUEVO) --}}
            @livewire('productor.alertas-panel')

            {{-- External News --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Noticias del Sector</h3>
                <ul class="space-y-4">
                    <li>
                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Nuevas líneas de crédito para productores ovinos.</a>
                        <p class="text-xs text-gray-500 mt-1">Fuente: Ministerio de Agricultura</p>
                    </li>
                    <li>
                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">El INTA presenta avances en pasturas resistentes a la sequía.</a>
                        <p class="text-xs text-gray-500 mt-1">Fuente: INTA Informa</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $datosEspecies = $datosHistoricos['datosPorEspecie'] ?? [];
                $mesesData = $datosHistoricos['meses'] ?? ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            @endphp
            const datosHistoricos = @json($datosEspecies);
            const meses = @json($mesesData);
            
            
            // Colores para cada especie
            const colores = [
                'rgba(34, 197, 94, 1)',   // green
                'rgba(59, 130, 246, 1)',  // blue
                'rgba(168, 85, 247, 1)',  // purple
                'rgba(236, 72, 153, 1)',  // pink
                'rgba(251, 146, 60, 1)',  // orange
            ];
            
            // Crear gráficos dinámicamente
            let colorIndex = 0;
            Object.keys(datosHistoricos).forEach(function(especie) {
                const canvasId = especie.toLowerCase().replace(/\s+/g, '_') + '_chart';
                const canvas = document.getElementById(canvasId);
                
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    const color = colores[colorIndex % colores.length];
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: meses,
                            datasets: [{
                                label: especie,
                                data: datosHistoricos[especie],
                                borderColor: color,
                                backgroundColor: color.replace('1)', '0.1)'),
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    
                    colorIndex++;
                }
            });
        });
    </script>
    @endpush
</x-productor-layout>