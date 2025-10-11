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
                $stats = [
                    ['icon' => 'heroicon-s-arrow-trending-up', 'label' => 'Nacimientos (Últ. 7 días)', 'value' => '+32', 'color' => 'text-green-300'],
                    ['icon' => 'heroicon-s-currency-dollar', 'label' => 'Ventas (Últ. 7 días)', 'value' => '12', 'color' => 'text-green-300'],
                    ['icon' => 'heroicon-s-arrow-trending-down', 'label' => 'Bajas (Últ. 7 días)', 'value' => '-5', 'color' => 'text-red-300'],
                    ['icon' => 'heroicon-s-clipboard-document-list', 'label' => 'Registros Nuevos', 'value' => '47', 'color' => 'text-indigo-300'],
                ];
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Ovinos: Últimos 6 meses</p>
                        <div class="mt-4 h-48">
                            <canvas id="ovinosChart"></canvas>
                        </div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Caprinos: Últimos 6 meses</p>
                        <div class="mt-4 h-48">
                            <canvas id="caprinosChart"></canvas>
                        </div>
                    </div>
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
            {{-- Weather Widget --}}
            <x-widgets.weather-widget :initial-data="$weatherData[0] ?? null" endpoint="/api/productor/clima" />

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
            // Gráfico de Ovinos
            const ovinosCtx = document.getElementById('ovinosChart').getContext('2d');
            new Chart(ovinosCtx, {
                type: 'line',
                data: {
                    labels: @js($datosHistoricos['meses'] ?? ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']),
                    datasets: [{
                        label: 'Ovinos',
                        data: @js($datosHistoricos['ovinos'] ?? [0, 0, 0, 0, 0, 0]),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
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

            // Gráfico de Caprinos
            const caprinosCtx = document.getElementById('caprinosChart').getContext('2d');
            new Chart(caprinosCtx, {
                type: 'line',
                data: {
                    labels: @js($datosHistoricos['meses'] ?? ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']),
                    datasets: [{
                        label: 'Caprinos',
                        data: @js($datosHistoricos['caprinos'] ?? [0, 0, 0, 0, 0, 0]),
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
        });
    </script>
    @endpush
</x-productor-layout>