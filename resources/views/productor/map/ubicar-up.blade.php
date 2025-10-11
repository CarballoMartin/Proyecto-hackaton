<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Seleccionar Ubicación - {{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css'])

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
        #map { height: 100%; width: 100%; cursor: crosshair; }
        .leaflet-top.leaflet-left { left: 50%; transform: translateX(-50%); margin-left: 0 !important; margin-top: 10px; }
        .leaflet-control-custom-buttons { display: flex; gap: 10px; background-color: transparent; border: none; box-shadow: none; }
        .leaflet-control-custom-buttons a { background-color: white; color: #1f2937; border: 2px solid #e5e7eb; border-radius: 0.375rem; padding: 8px 16px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); display: flex; align-items: center; gap: 4px; }
        .leaflet-control-custom-buttons a:hover { background-color: #f9fafb; }
        .leaflet-control-custom-buttons a.save { background-color: #4f46e5; color: white; border-color: #4f46e5; }
        .leaflet-control-custom-buttons a.save:hover { background-color: #4338ca; }
        .leaflet-control-municipio-filter { background-color: white; padding: 8px; border-radius: 0.375rem; border: 2px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .leaflet-control-municipio-filter select { border: 1px solid #d1d5db; border-radius: 0.25rem; width: 240px; }
        .leaflet-bottom.leaflet-left { left: 50%; transform: translateX(-50%); margin-bottom: 20px; }
        .session-error-overlay { position: absolute; top: 80px; left: 50%; transform: translateX(-50%); z-index: 2000; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 0.375rem; padding: 1rem; max-width: 500px; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: opacity 0.5s ease-out; }
    </style>
</head>
<body class="antialiased">

    <!-- Tutorial Modal -->
    <div id="tutorialModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 z-[10000] hidden flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">¡Bienvenido al Mapa Interactivo!</h3>
                <button id="closeTutorialCross" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="text-gray-600 space-y-4">
                <p>Para registrar la ubicación de tu chacra, sigue estos simples pasos:</p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Haz clic</strong> en cualquier lugar del mapa para colocar un marcador.</li>
                    <li>Si no estás seguro, puedes <strong>arrastrar el marcador</strong> para ajustar la posición exacta.</li>
                    <li>Una vez que el marcador esté en el lugar correcto, haz clic en <strong>"Guardar Ubicación"</strong>.</li>
                </ul>
                <p>El sistema verificará que el punto esté dentro del municipio que seleccionaste en el paso anterior.</p>
            </div>
            <div class="mt-6 text-right">
                <button id="closeTutorialButton" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">¡Entendido!</button>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="session-error-overlay">
            {{ session('error') }}
        </div>
    @endif
    
    <div id="map"></div>

    <form id="locationForm" action="{{ route('productor.up.ubicar.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $unidadProductiva->latitud) }}">
        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $unidadProductiva->longitud) }}">
    </form>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tutorialModal = document.getElementById('tutorialModal');
            const closeTutorialButton = document.getElementById('closeTutorialButton');
            const closeTutorialCross = document.getElementById('closeTutorialCross');

            function openTutorialModal() {
                tutorialModal.classList.remove('hidden');
            }

            function closeTutorialModal() {
                tutorialModal.classList.add('hidden');
                if (!localStorage.getItem('mapTutorialSeen')) {
                    localStorage.setItem('mapTutorialSeen', 'true');
                }
            }

            if (!localStorage.getItem('mapTutorialSeen')) {
                openTutorialModal();
            }

            closeTutorialButton.addEventListener('click', closeTutorialModal);
            closeTutorialCross.addEventListener('click', closeTutorialModal);

            const errorOverlay = document.querySelector('.session-error-overlay');
            if (errorOverlay) {
                setTimeout(() => {
                    errorOverlay.style.opacity = '0';
                    setTimeout(() => errorOverlay.remove(), 500);
                }, 5000);
            }

            const latInput = document.getElementById('latitud');
            const lonInput = document.getElementById('longitud');
            const form = document.getElementById('locationForm');
            const initialLat = parseFloat(latInput.value) || -27.8;
            const initialLon = parseFloat(lonInput.value) || -55.9;
            let initialZoom = latInput.value ? 13 : 10;
            const selectedMunicipioBoundary = @json($unidadProductiva->municipio->geojson_boundary ?? null);
            const map = L.map('map', { zoomControl: false });

            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);

            if (selectedMunicipioBoundary) {
                try {
                    const boundaryData = JSON.parse(selectedMunicipioBoundary);
                    const geojsonLayer = L.geoJSON(boundaryData, { style: { color: "#3388ff", weight: 2, opacity: 0.8, fillOpacity: 0.1 } }).addTo(map);
                    map.fitBounds(geojsonLayer.getBounds());
                } catch (e) {
                    console.error('Error parsing GeoJSON boundary', e);
                    map.setView([initialLat, initialLon], initialZoom);
                }
            } else {
                map.setView([initialLat, initialLon], initialZoom);
            }

            let marker = null;
            function updateMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', e => updateInputs(e.target.getLatLng().lat, e.target.getLatLng().lng));
                }
                updateInputs(lat, lng);
            }
            function updateInputs(lat, lng) {
                latInput.value = lat.toFixed(6);
                lonInput.value = lng.toFixed(6);
            }
            map.on('click', e => updateMarker(e.latlng.lat, e.latlng.lng));
            if (latInput.value && lonInput.value) {
                updateMarker(initialLat, initialLon);
            }

            L.Control.CustomButtons = L.Control.extend({
                onAdd: function(map) {
                    const container = L.DomUtil.create('div', 'leaflet-control-custom-buttons');
                    L.DomEvent.disableClickPropagation(container);

                    const tutorialLink = L.DomUtil.create('a', 'tutorial', container);
                    tutorialLink.href = '#';
                    tutorialLink.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" /></svg> Ayuda';
                    L.DomEvent.on(tutorialLink, 'click', e => { e.preventDefault(); openTutorialModal(); });

                    const saveLink = L.DomUtil.create('a', 'save', container);
                    saveLink.href = '#';
                    saveLink.innerHTML = 'Guardar Ubicación';
                    L.DomEvent.on(saveLink, 'click', e => { e.preventDefault(); if (!latInput.value || !lonInput.value) { alert('Por favor, seleccione una ubicación en el mapa antes de guardar.'); return; } form.submit(); });

                    const cancelLink = L.DomUtil.create('a', 'cancel', container);
                    cancelLink.href = '#';
                    cancelLink.innerHTML = 'Cancelar';
                    L.DomEvent.on(cancelLink, 'click', e => { e.preventDefault(); if (confirm('¿Está seguro de que desea cancelar? La ubicación no se guardará.')) { window.history.back(); } });

                    return container;
                },
                onRemove: function(map) {}
            });
            new L.Control.CustomButtons({ position: 'topleft' }).addTo(map);

            const municipios = @json($municipios);
            L.Control.MunicipioFilter = L.Control.extend({
                onAdd: function(map) {
                    const container = L.DomUtil.create('div', 'leaflet-control-municipio-filter');
                    L.DomEvent.disableClickPropagation(container);
                    const select = L.DomUtil.create('select', '', container);
                    select.innerHTML = '<option value="">Centrar en municipio...</option>';
                    municipios.forEach(m => { const option = L.DomUtil.create('option', '', select); option.value = m.id; option.innerHTML = m.nombre; option.dataset.lat = m.latitud; option.dataset.lng = m.longitud; });
                    L.DomEvent.on(select, 'change', e => { const selectedOption = e.target.options[e.target.selectedIndex]; if (selectedOption.value) { map.flyTo([selectedOption.dataset.lat, selectedOption.dataset.lng], 13); } });
                    return container;
                },
                onRemove: function(map) {}
            });
            new L.Control.MunicipioFilter({ position: 'bottomleft' }).addTo(map);
        });
    </script>
</body>
</html>
