<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Mapa de Unidades Productivas</h1>
                        @if($productorNombre)
                            <p class="text-gray-600 mt-1">
                                Mostrando {{ $totalUnidades }} UP de: <span class="font-semibold text-indigo-600">{{ $productorNombre }}</span>
                                <a href="{{ route('admin.mapa') }}" class="ml-2 text-sm text-blue-500 hover:text-blue-700 underline">Ver todos</a>
                            </p>
                        @else
                            <p class="text-gray-600 mt-1">Visualización geográfica de {{ $totalUnidades }} unidades productivas</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="map.setView([-27.4, -55.9], 9)" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Centrar Mapa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Controles del Mapa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">Unidades Productivas</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">Con Stock Registrado</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    <span class="text-sm text-gray-700">Sin Actividad Reciente</span>
                </div>
            </div>
        </div>

        {{-- Contenedor del Mapa --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>

        {{-- Estadísticas del Mapa --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            
            {{-- Total UPs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unidades Productivas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUnidades }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Productores --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Productores</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProductores }}</p>
                    </div>
                </div>
            </div>

            {{-- Superficie Total --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Superficie Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($superficieTotal, 0) }} ha</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar mapa
            const map = L.map('map').setView([-27.4, -55.9], 9);
            window.map = map;

            // Añadir capa de tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18,
            }).addTo(map);

            // Crear cluster de marcadores
            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });

            // Cargar datos desde API (con filtro de productor si existe)
            const productorId = '{{ $productorId }}';
            const url = productorId ? `/api/locations?productor=${productorId}` : '/api/locations';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(up => {
                        if (up.coordenadas && up.coordenadas.lat && up.coordenadas.lng) {
                            // Determinar color del marcador según tenga stock
                            const icon = L.divIcon({
                                className: 'custom-marker',
                                html: `<div style="background-color: #3B82F6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });

                            const marker = L.marker([up.coordenadas.lat, up.coordenadas.lng], { icon });
                            
                            // Popup con información
                            const productoresNombres = up.productores.map(p => p.nombre).join(', ') || 'Sin productor asignado';
                            marker.bindPopup(`
                                <div style="min-width: 200px;">
                                    <h3 style="font-weight: bold; margin-bottom: 8px; color: #1F2937;">Unidad Productiva</h3>
                                    <p style="margin: 4px 0;"><strong>ID:</strong> ${up.rnspa || 'N/A'}</p>
                                    <p style="margin: 4px 0;"><strong>Superficie:</strong> ${up.superficie || 'N/A'} ha</p>
                                    <p style="margin: 4px 0;"><strong>Productor(es):</strong> ${productoresNombres}</p>
                                    <hr style="margin: 8px 0;">
                                    <p style="margin: 4px 0; font-size: 0.85em; color: #6B7280;">
                                        <strong>Lat:</strong> ${up.coordenadas.lat.toFixed(5)}<br>
                                        <strong>Lng:</strong> ${up.coordenadas.lng.toFixed(5)}
                                    </p>
                                </div>
                            `);
                            
                            markers.addLayer(marker);
                        }
                    });

                    map.addLayer(markers);

                    // Si hay marcadores, ajustar zoom
                    if (data.length > 0) {
                        const bounds = markers.getBounds();
                        if (bounds.isValid()) {
                            map.fitBounds(bounds, { padding: [50, 50] });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al cargar ubicaciones:', error);
                    alert('Error al cargar las ubicaciones del mapa');
                });
        });
    </script>
    @endpush
</div>

