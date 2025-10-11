<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Maqueta del Nuevo Dashboard
        </h2>
    </x-slot>

    <div class="p-6 sm:p-8 space-y-8">

        {{-- Admin Data Carousel --}}
        <div class="relative bg-[#8C2218] text-white rounded-lg shadow-lg p-6 mb-8 overflow-hidden">
            <div class="flex space-x-12 animate-scroll-and-repeat">
                @php
                    $adminStats = [
                        ['icon' => 'heroicon-s-inbox-arrow-down', 'label' => 'Nuevas Solicitudes', 'value' => '3', 'color' => 'text-amber-300'],
                        ['icon' => 'heroicon-s-user-minus', 'label' => 'Productores Inactivos', 'value' => '8', 'color' => 'text-red-300'],
                        ['icon' => 'heroicon-s-exclamation-triangle', 'label' => 'Stock sin Registrar (24h)', 'value' => '15', 'color' => 'text-yellow-300'],
                        ['icon' => 'heroicon-s-cloud', 'label' => 'Próx. Sync Clima', 'value' => 'en 2h', 'color' => 'text-sky-300'],
                    ];
                @endphp
                @foreach (array_merge($adminStats, $adminStats) as $stat) {{-- Duplicate for seamless loop --}}
                    <div class="flex-shrink-0 flex items-center space-x-4">
                        <x-dynamic-component :component="$stat['icon']" :class="'h-8 w-8 ' . $stat['color']" />
                        <div>
                            <div class="text-sm text-red-100">{{ $stat['label'] }}</div>
                            <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bloque 1: KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-widgets.dashboard-kpi icon="heroicon-o-user-group" title="Productores" value="{{ $stats['totalProductores'] }}" color="blue" />
            <x-widgets.dashboard-kpi icon="heroicon-o-building-office-2" title="Instituciones" value="{{ $stats['totalInstituciones'] }}" color="sky" />
            <x-widgets.dashboard-kpi icon="heroicon-o-inbox-stack" title="Solicitudes Pend." value="{{ $stats['solicitudesPendientes'] }}" color="amber" :highlight="$stats['solicitudesPendientes'] > 0" />
        </div>

        <!-- Bloque 2: Mapa y Actividad Reciente -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <x-widgets.map-widget title="Mapa de Productores" :markers="$mapMarkers" />
            </div>
            <div class="border bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h3>
                <ul class="space-y-4">
                    <x-widgets.activity-item icon="heroicon-s-user-plus" text="Nuevo productor registrado: Juan Perez" time="hace 5 minutos" />
                    <x-widgets.activity-item icon="heroicon-s-check-badge" text="Institución aprobada: INTA" time="hace 2 horas" />
                    <x-widgets.activity-item icon="heroicon-s-arrow-up-on-square" text="Se importaron 50 productores" time="hace 1 día" />
                    <x-widgets.activity-item icon="heroicon-s-user-plus" text="Nuevo productor registrado: Maria Gomez" time="hace 2 días" />
                    <x-widgets.activity-item icon="heroicon-s-x-circle" text="Solicitud rechazada: Coop. Agrícola" time="hace 3 días" />
                </ul>
            </div>
        </div>

        <!-- Bloque 3: Acciones y Solicitudes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <x-widgets.quick-actions :actions="$quickActions" />
            </div>
            <div class="lg:col-span-2">
                <x-widgets.pending-requests 
                    :requests="$pendingRequests" 
                    view-all-route="{{ route('admin.solicitudes.gestionar') }}" 
                />
            </div>
        </div>

        <!-- Bloque 4: Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="border bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Composición Ganadera</h3>
                <div class="relative h-64">
                    <canvas id="compositionChart"></canvas>
                </div>
            </div>
            <div class="border bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Productores por Municipio</h3>
                <div class="relative h-64">
                    <canvas id="producersByCityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bloque 5: Widgets Adicionales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <x-widgets.weather-widget :weatherData="$weatherData" />
            </div>
            <div>
                <x-widgets.news-widget :items="$newsItems" />
            </div>
        </div>

    </div>

    {{-- Los scripts de los componentes se "empujan" aquí automáticamente --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Los scripts de los componentes (como el mapa) se manejan en sus propios archivos.
                // Este bloque es para scripts que sean específicos de esta página.

                // Gráfico de Composición
                new Chart(document.getElementById('compositionChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Ovinos', 'Caprinos'],
                        datasets: [{
                            label: 'Total Animales',
                            data: [6250, 2180],
                            backgroundColor: ['#34D399', '#60A5FA'],
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // Gráfico de Productores por Municipio
                new Chart(document.getElementById('producersByCityChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Apóstoles', 'Concepción de la Sierra', 'San José', 'Azara', 'Tres Capones'],
                        datasets: [{
                            label: 'Nº de Productores',
                            data: [45, 32, 28, 21, 15],
                            backgroundColor: '#A5B4FC',
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
                });
            });
        </script>
    @endpush

</x-admin-layout>