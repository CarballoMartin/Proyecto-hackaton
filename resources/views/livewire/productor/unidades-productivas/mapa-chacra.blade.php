<div style="height: 100vh;" x-data="mapaChacra()" x-init="initMap()">
    <a href="{{ $backUrl }}" class="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-white text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Volver
    </a>
    <div id="map" style="height: 100%;"></div>

    @push('scripts')
    <script>
        function mapaChacra() {
            return {
                locations: @json($locations),
                initMap: function() {
                    if (this.locations.length === 0) {
                        document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full"><h1 class="text-2xl text-gray-500">No hay datos de ubicación para mostrar.</h1></div>';
                        return;
                    }

                    const map = L.map('map');

                    const currentUp = this.locations.find(loc => loc.is_current);
                    if (currentUp) {
                        map.setView([currentUp.lat, currentUp.lon], 13);
                    } else {
                        map.setView([this.locations[0].lat, this.locations[0].lon], 10);
                    }

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    const currentIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    const otherIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    const markers = [];
                    this.locations.forEach(loc => {
                        const icon = loc.is_current ? currentIcon : otherIcon;
                        const marker = L.marker([loc.lat, loc.lon], { icon: icon }).addTo(map);
                        marker.bindPopup(loc.name);
                        markers.push(marker);
                    });

                    if (markers.length > 0) {
                        const group = new L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.5));
                    }
                }
            }
        }
    </script>
    @endpush
</div>
