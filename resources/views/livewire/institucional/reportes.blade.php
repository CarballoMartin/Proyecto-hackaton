<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
<div>
                        <h1 class="text-3xl font-bold text-gray-900">Reportes y Estadísticas</h1>
                        <p class="text-gray-600 mt-1">Análisis detallado de la actividad institucional</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-lg font-medium text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Tarjetas de Resumen --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            
            {{-- Total Participantes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Participantes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_participantes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Activos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Solicitudes Aprobadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['solicitudes_aprobadas'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Nuevos este Mes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Solicitudes Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['solicitudes_pendientes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Tasa de Crecimiento --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Productores Verificados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['productores_verificados'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráficos y Análisis --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Gráfico de Participantes por Mes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Participantes por Mes</h3>
                <div class="h-64">
                    <canvas id="participantesChart"></canvas>
                </div>
            </div>

            {{-- Solicitudes por Estado --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Solicitudes por Estado</h3>
                <div class="h-64">
                    <canvas id="solicitudesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabla de Actividad Reciente --}}
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Reportes en Desarrollo</h4>
                    <p class="text-gray-500">Los reportes detallados se implementarán próximamente</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de participantes por mes
    const participantesData = @json($participantesPorMes);
    const meses = Object.keys(participantesData);
    const valores = Object.values(participantesData);
    
    // Gráfico de Participantes por Mes
    const participantesCtx = document.getElementById('participantesChart').getContext('2d');
    new Chart(participantesCtx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Participantes',
                data: valores,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
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
    
    // Datos de solicitudes por estado
    const solicitudesData = @json($solicitudesPorEstado);
    const estados = Object.keys(solicitudesData);
    const cantidades = Object.values(solicitudesData);
    
    // Gráfico de Solicitudes por Estado
    const solicitudesCtx = document.getElementById('solicitudesChart').getContext('2d');
    new Chart(solicitudesCtx, {
        type: 'doughnut',
        data: {
            labels: estados.map(estado => {
                const traducciones = {
                    'pendiente': 'Pendientes',
                    'aprobada': 'Aprobadas',
                    'rechazada': 'Rechazadas'
                };
                return traducciones[estado] || estado;
            }),
            datasets: [{
                data: cantidades,
                backgroundColor: [
                    'rgba(245, 158, 11, 0.8)', // Amarillo para pendientes
                    'rgba(34, 197, 94, 0.8)',  // Verde para aprobadas
                    'rgba(239, 68, 68, 0.8)'   // Rojo para rechazadas
                ],
                borderColor: [
                    'rgba(245, 158, 11, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>