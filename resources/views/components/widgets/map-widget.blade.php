@props([
    'title' => 'Mapa',
    'markers' => [],
    'centerLat' => -27.5,
    'centerLng' => -55.0,
    'zoom' => 8,
    'mapId' => 'map-' . uniqid()
])

<div class="border bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    <div id="{{ $mapId }}" class="h-96 rounded-b-lg z-10"></div>
</div>

@pushonce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpushonce

@pushonce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpushonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapElement = document.getElementById('{{ $mapId }}');
        
        // Evitar reinicialización del mapa
        if (mapElement && !mapElement._leaflet_id) {
            const map = L.map('{{ $mapId }}').setView([{{ $centerLat }}, {{ $centerLng }}], {{ $zoom }});

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const markers = @json($markers);

            markers.forEach(function(markerData) {
                if(markerData.lat && markerData.lng) {
                    L.marker([markerData.lat, markerData.lng]).addTo(map).bindPopup(markerData.popup);
                }
            });
        }
    });
</script>
@endpush
