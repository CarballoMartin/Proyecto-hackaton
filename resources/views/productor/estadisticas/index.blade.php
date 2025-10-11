<x-productor-layout>
    <x-slot name="header_title">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Estadísticas y Gráficos
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 lg:p-8 space-y-6">
        
        {{-- Filtros --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Especie</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        <option value="ovino">Ovino</option>
                        <option value="caprino">Caprino</option>
                        <option value="bovino">Bovino</option>
                    </select>
                        </div>
                        <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="mes">Último Mes</option>
                        <option value="trimestre">Último Trimestre</option>
                        <option value="año">Último Año</option>
                        </select>
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        <option value="1">Estancia San Juan</option>
                        <option value="2">Campo Los Pinos</option>
                        </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico de Barras - Stock por Especie --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock por Especie</h3>
                <div class="h-64">
                    <canvas id="stockEspecieChart"></canvas>
            </div>
            </div>

            {{-- Gráfico de Dona - Distribución por Categoría --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Categoría</h3>
                <div class="h-64">
                    <canvas id="categoriaChart"></canvas>
            </div>
        </div>

            {{-- Gráfico de Líneas - Evolución Temporal --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Evolución del Stock</h3>
                <div class="h-64">
                    <canvas id="evolucionChart"></canvas>
                    </div>
            </div>

            {{-- Gráfico de Barras Horizontales - Stock por Raza --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock por Raza</h3>
                <div class="h-64">
                    <canvas id="razaChart"></canvas>
                    </div>
            </div>
        </div>

        {{-- Resumen Estadístico --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen Estadístico</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">45</div>
                    <div class="text-sm text-gray-600">Total de Animales</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">3</div>
                    <div class="text-sm text-gray-600">Especies</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">8</div>
                    <div class="text-sm text-gray-600">Razas</div>
        </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">12</div>
                    <div class="text-sm text-gray-600">Categorías</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
        // Gráfico de Barras - Stock por Especie
        const stockEspecieCtx = document.getElementById('stockEspecieChart').getContext('2d');
        new Chart(stockEspecieCtx, {
            type: 'bar',
            data: {
                labels: ['Ovino', 'Caprino', 'Bovino'],
                datasets: [{
                    label: 'Cantidad',
                    data: [25, 15, 5],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(168, 85, 247, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Dona - Distribución por Categoría
        const categoriaCtx = document.getElementById('categoriaChart').getContext('2d');
        new Chart(categoriaCtx, {
            type: 'doughnut',
            data: {
                labels: ['Ovejas', 'Corderos', 'Cabras', 'Chivos', 'Vacas'],
                datasets: [{
                    data: [15, 10, 8, 7, 5],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de Líneas - Evolución Temporal
        const evolucionCtx = document.getElementById('evolucionChart').getContext('2d');
        new Chart(evolucionCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Stock Total',
                    data: [35, 38, 42, 40, 45, 45],
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Barras Horizontales - Stock por Raza
        const razaCtx = document.getElementById('razaChart').getContext('2d');
        new Chart(razaCtx, {
            type: 'bar',
            data: {
                labels: ['Merino', 'Corriedale', 'Saanen', 'Angora', 'Criollo'],
                datasets: [{
                    label: 'Cantidad',
                    data: [12, 8, 6, 4, 15],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
        }
    });
</script>
</x-productor-layout>