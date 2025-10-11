{{-- resources/views/mapa.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Mapa de Productores - Ovino-Caprinos</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Leaflet Sidebar v2 CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-sidebar-v2@3.2.3/css/leaflet-sidebar.min.css" />

    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <!-- Font Awesome 4 (compatible con las clases fa fa-*) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }

        #map {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .sidebar-content {
            padding: 12px;
        }

        .filter-row {
            margin-bottom: 10px;
        }

        .suggestions {
            max-height: 200px;
            overflow: auto;
            border: 1px solid #ddd;
            background: #fff;
        }

        .suggestion-item {
            padding: 6px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
        }

        .suggestion-item:hover {
            background: #f5f5f5;
        }

        .small-muted {
            font-size: 0.85rem;
            color: #666;
        }

        /* Spinner overlay */
        .map-loader {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.85);
            z-index: 9999;
            transition: opacity 0.25s ease;
        }

        .map-loader.hidden {
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        .spinner {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            position: relative;
            display: inline-block;
        }

        .spinner::before,
        .spinner::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 6px solid transparent;
            border-top-color: #2b7de9;
            animation: spin 1s linear infinite;
        }

        .spinner::after {
            border: 6px solid transparent;
            border-bottom-color: #2b7de9;
            transform: rotate(180deg);
            animation-duration: 1.4s;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loader-text {
            margin-top: 10px;
            text-align: center;
            font-size: 0.95rem;
            color: #333;
        }

        /* Ajustes móviles */
        @media (max-width: 640px) {
            .leaflet-sidebar {
                width: 260px;
            }
        }
    </style>
</head>

<body>
    {{-- Sidebar container --}}
    <div id="sidebar" class="leaflet-sidebar collapsed">
        <div class="leaflet-sidebar-tabs">
            <ul role="tablist">
                <li><a href="#capas" role="tab"><i class="fa fa-bars"></i></a></li>
                <li><a href="#filtros" role="tab"><i class="fa fa-filter"></i></a></li>
                <li><a href="#busqueda" role="tab"><i class="fa fa-search"></i></a></li>
            </ul>
        </div>

        <div class="leaflet-sidebar-content">
            {{-- Capas --}}
            <div class="leaflet-sidebar-pane" id="capas">
                <h1 class="leaflet-sidebar-header">Capas
                    <span class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></span>
                </h1>
                <div class="sidebar-content">
                    <h2>Mapas base</h2>
                    <div id="baselayers-controls" class="small-muted"></div>

                    <hr>
                    <h2>Capas temáticas</h2>
                    <p class="small-muted">Aquí se listarán capas temáticas (WMS / GeoJSON) para activar/desactivar en
                        el futuro.</p>
                    <div id="thematic-layers"></div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="leaflet-sidebar-pane" id="filtros">
                <h1 class="leaflet-sidebar-header">Filtros
                    <span class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></span>
                </h1>
                <div class="sidebar-content">
                    <div class="filter-row">
                        <label for="filter-municipio"><strong>Municipio</strong></label>
                        <select id="filter-municipio" style="width:100%; padding:6px;">
                            <option value="">— Todos —</option>
                        </select>
                        <div class="small-muted">Si la API no provee municipio, este control permanecerá vacío.</div>
                    </div>

                    <div class="filter-row">
                        <label for="filter-superficie"><strong>Superficie (ha)</strong></label>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input id="filter-superficie" type="range" min="0" max="1000" step="1" value="100"
                                style="flex:1;">
                            <span id="filter-superficie-val" style="min-width:60px; text-align:right;">≥ 100 ha</span>
                        </div>
                        <div class="small-muted">Arrastra para mostrar unidades productivas con superficie mayor o
                            igual.</div>
                    </div>

                    <div class="filter-row">
                        <label for="filter-productor"><strong>Productor</strong></label>
                        <select id="filter-productor" style="width:100%; padding:6px;">
                            <option value="">— Todos —</option>
                        </select>
                    </div>

                    <div style="margin-top:8px;">
                        <button id="btn-reset-filters" style="padding:8px 10px;">Resetear filtros</button>
                    </div>
                </div>
            </div>

            {{-- Búsqueda --}}
            <div class="leaflet-sidebar-pane" id="busqueda">
                <h1 class="leaflet-sidebar-header">Búsqueda
                    <span class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></span>
                </h1>
                <div class="sidebar-content">
                    <label for="search-input"><strong>Buscar por RNSPA, ID o nombre</strong></label>
                    <input id="search-input" type="text" placeholder="Escribe RNSPA, ID o productor..."
                        style="width:100%; padding:8px;">
                    <div id="search-suggestions" class="suggestions" style="display:none; margin-top:8px;"></div>

                    <div style="margin-top:12px;">
                        <button id="btn-zoom-all" style="padding:8px 10px;">Zoom a todos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor mapa --}}
    <div id="map">
        {{-- Loader --}}
        <div id="map-loader" class="map-loader" role="status" aria-live="polite" aria-label="Cargando mapa">
            <div style="text-align:center;">
                <div class="spinner" aria-hidden="true"></div>
                <div class="loader-text">Cargando mapa y ubicaciones…</div>
            </div>
        </div>
    </div>

    <!-- Dependencies -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-sidebar-v2@3.2.3/js/leaflet-sidebar.min.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        (function () {
            // Estado compartido
            let map = null;
            let markersCluster = null;
            let markersIndex = {};
            let locations = [];
            let searchIndex = [];
            let currentBaseKey = "OpenStreetMap";

            // Base layers (definidos fuera de la creación del mapa)
            const baseLayers = {
                "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }),
                "Satélite (ESRI)": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri'
                }),
                "Topográfico (OpenTopoMap)": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    maxZoom: 17,
                    attribution: 'Map data: &copy; OpenTopoMap'
                })
            };

            // Datos de ejemplo en caso de error
            const SAMPLE_LOCATIONS = [
                { id: 1, rnspa: "R-123", superficie: 50, coordenadas: { lat: -27.82, lng: -55.95 }, productores: [{ id: 1, nombre: "Juan Perez" }], municipio: "San Vicente" },
                { id: 2, rnspa: "R-456", superficie: 150, coordenadas: { lat: -27.9, lng: -55.7 }, productores: [{ id: 2, nombre: "Maria Gomez" }], municipio: "Eldorado" },
            ];

            // Helper: ocultar/mostrar loader
            function showLoader() {
                const el = document.getElementById('map-loader');
                if (el) el.classList.remove('hidden');
            }
            function hideLoader() {
                const el = document.getElementById('map-loader');
                if (el) el.classList.add('hidden');
            }

            // Popup content
            function createPopupContent(loc) {
                const productoresNombres = (loc.productores || []).map(p => p.nombre).join(', ');
                const productor = (loc.productores && loc.productores[0]) || {};
                return `
          <div>
            <b>ID UP:</b> ${loc.id}<br>
            <b>RNSPA:</b> ${loc.rnspa || 'No especificado'}<br>
            <b>Superficie:</b> ${loc.superficie || 'N/A'} ha<br>
            <b>Productores:</b> ${productoresNombres || 'N/D'}<br>
            ${productor.id ? `<a href="/reportes/productor/${productor.id}" target="_blank">Ver más</a>` : ''}
          </div>
        `;
            }

            function addMarkerForLocation(loc) {
                if (!loc.coordenadas || typeof loc.coordenadas.lat !== 'number' || typeof loc.coordenadas.lng !== 'number') {
                    console.warn('Ubicación sin coordenadas válidas:', loc);
                    return null;
                }
                const marker = L.marker([loc.coordenadas.lat, loc.coordenadas.lng], { title: loc.rnspa || String(loc.id) });
                marker.bindPopup(createPopupContent(loc));
                markersIndex[loc.id] = marker;
                markersCluster.addLayer(marker);
                return marker;
            }

            function clearAllMarkers() {
                if (markersCluster) markersCluster.clearLayers();
                markersIndex = {};
            }

            function renderMarkers(list) {
                clearAllMarkers();
                list.forEach(addMarkerForLocation);
            }

            // Construir controles base layer en sidebar (solo tras crear el mapa)
            function buildBaseLayerControls() {
                const container = document.getElementById('baselayers-controls');
                container.innerHTML = '';
                Object.keys(baseLayers).forEach(key => {
                    const id = 'base-' + key.replace(/\s+/g, '-');
                    const wrapper = document.createElement('div');
                    wrapper.style.marginBottom = '6px';
                    wrapper.innerHTML = `
            <label style="cursor:pointer">
              <input type="radio" name="baselayer" id="${id}" value="${key}" ${key === currentBaseKey ? 'checked' : ''}>
              &nbsp; ${key}
            </label>
          `;
                    container.appendChild(wrapper);

                    const input = wrapper.querySelector('input');
                    if (input) {
                        input.addEventListener('change', (e) => {
                            if (e.target.checked && map) {
                                // Remover base anterior y añadir la nueva
                                if (baseLayers[currentBaseKey]) map.removeLayer(baseLayers[currentBaseKey]);
                                baseLayers[key].addTo(map);
                                currentBaseKey = key;
                            }
                        });
                    }
                });
            }

            // Filtros: construir selects y rangos
            function buildFiltersFromData(data) {
                const municipioSelect = document.getElementById('filter-municipio');
                municipioSelect.innerHTML = '<option value="">— Todos —</option>';
                const municipios = new Set();
                data.forEach(d => { if (d.municipio) municipios.add(d.municipio); });
                if (municipios.size === 0) {
                    municipioSelect.disabled = true;
                    municipioSelect.innerHTML = '<option value="">(Sin datos de municipio)</option>';
                } else {
                    municipioSelect.disabled = false;
                    Array.from(municipios).sort().forEach(m => {
                        const opt = document.createElement('option'); opt.value = m; opt.textContent = m;
                        municipioSelect.appendChild(opt);
                    });
                }

                const productorSelect = document.getElementById('filter-productor');
                productorSelect.innerHTML = '<option value="">— Todos —</option>';
                const productoresSet = new Map();
                data.forEach(d => {
                    (d.productores || []).forEach(p => {
                        if (p && p.id) productoresSet.set(String(p.id), p.nombre || `#${p.id}`);
                    });
                });
                Array.from(productoresSet.entries()).sort((a, b) => a[1].localeCompare(b[1])).forEach(([id, nombre]) => {
                    const opt = document.createElement('option'); opt.value = id; opt.textContent = nombre;
                    productorSelect.appendChild(opt);
                });

                const superficies = data.map(d => Number(d.superficie || 0)).filter(n => !isNaN(n));
                const range = document.getElementById('filter-superficie');
                const label = document.getElementById('filter-superficie-val');
                if (superficies.length > 0) {
                    const min = Math.min(...superficies);
                    const max = Math.max(...superficies);
                    range.min = Math.floor(min);
                    range.max = Math.ceil(max);
                    range.value = Math.ceil(Math.min(100, max));
                    label.textContent = `≥ ${range.value} ha`;
                } else {
                    range.min = 0; range.max = 1000; range.value = 100;
                    label.textContent = `≥ ${range.value} ha`;
                }
            }

            function applyFiltersAndRender() {
                const municipioVal = document.getElementById('filter-municipio').value;
                const productorVal = document.getElementById('filter-productor').value;
                const superficieMin = Number(document.getElementById('filter-superficie').value);

                const filtered = locations.filter(loc => {
                    const okMunicipio = !municipioVal || (loc.municipio === municipioVal);
                    const okProductor = !productorVal || ((loc.productores || []).some(p => String(p.id) === String(productorVal)));
                    const sup = Number(loc.superficie || 0);
                    const okSup = isNaN(sup) ? true : (sup >= superficieMin);
                    return okMunicipio && okProductor && okSup;
                });

                renderMarkers(filtered);
            }

            // Busqueda
            function buildSearchIndex(data) {
                return data.map(loc => {
                    const productores = (loc.productores || []).map(p => p.nombre).join(', ');
                    return {
                        id: loc.id,
                        rnspa: String(loc.rnspa || ''),
                        productores,
                        label: `${loc.rnspa || ''} ${productores} ${loc.id}`.trim(),
                        loc
                    };
                });
            }

            function showSuggestions(matches) {
                const box = document.getElementById('search-suggestions');
                box.innerHTML = '';
                if (!matches || matches.length === 0) { box.style.display = 'none'; return; }
                matches.slice(0, 10).forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.textContent = `${m.rnspa} — ${m.productores || ('UP #' + m.id)}`;
                    div.addEventListener('click', () => {
                        const latlng = [m.loc.coordenadas.lat, m.loc.coordenadas.lng];
                        map.setView(latlng, 14, { animate: true });
                        const mk = markersIndex[m.loc.id];
                        if (mk) mk.openPopup();
                        else {
                            const tempMk = L.marker(latlng).addTo(map);
                            tempMk.bindPopup(createPopupContent(m.loc)).openPopup();
                            setTimeout(() => map.removeLayer(tempMk), 8000);
                        }
                        box.style.display = 'none';
                        document.getElementById('search-input').value = '';
                    });
                    box.appendChild(div);
                });
                box.style.display = 'block';
            }

            // Listeners UI
            document.getElementById('filter-municipio').addEventListener('change', applyFiltersAndRender);
            document.getElementById('filter-productor').addEventListener('change', applyFiltersAndRender);
            document.getElementById('filter-superficie').addEventListener('input', (e) => {
                document.getElementById('filter-superficie-val').textContent = `≥ ${e.target.value} ha`;
                applyFiltersAndRender();
            });
            document.getElementById('btn-reset-filters').addEventListener('click', () => {
                document.getElementById('filter-municipio').value = '';
                document.getElementById('filter-productor').value = '';
                const r = document.getElementById('filter-superficie');
                r.value = r.min || 0;
                document.getElementById('filter-superficie-val').textContent = `≥ ${r.value} ha`;
                applyFiltersAndRender();
            });

            let searchTimeout = null;
            document.getElementById('search-input').addEventListener('input', (e) => {
                const q = e.target.value.trim().toLowerCase();
                if (!q) { showSuggestions([]); return; }
                if (searchTimeout) clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const matches = searchIndex.filter(item => {
                        return item.label.toLowerCase().includes(q) || String(item.id) === q;
                    });
                    showSuggestions(matches);
                }, 150);
            });

            document.getElementById('btn-zoom-all').addEventListener('click', () => {
                if (!markersCluster) return;
                const allBounds = markersCluster.getBounds();
                if (allBounds.isValid()) map.fitBounds(allBounds.pad(0.15));
            });

            // Crear mapa con bounds inicial (evita salto)
            function initializeMap(initialBounds) {
                const center = initialBounds && initialBounds.getCenter ? initialBounds.getCenter() : [-27.8, -55.9];
                map = L.map('map', { preferCanvas: true, center: center, zoom: 10 });

                // añadir base por defecto
                baseLayers["OpenStreetMap"].addTo(map);

                // inicializar sidebar y forzar invalidation cuando se abre/cierra
                L.control.sidebar({ container: 'sidebar', position: 'left' }).addTo(map);
                document.querySelectorAll('.leaflet-sidebar-tabs a, .leaflet-sidebar-close').forEach(el => {
                    el.addEventListener('click', () => setTimeout(() => map.invalidateSize(), 300));
                });

                // crear cluster y añadir
                markersCluster = L.markerClusterGroup();
                markersCluster.addTo(map);

                // si hay bounds, ajustar vista
                if (initialBounds && initialBounds.isValid()) {
                    map.fitBounds(initialBounds.pad(0.15));
                } else {
                    map.setView(center, 10);
                }

                // construir controles base layer ya que el mapa existe
                buildBaseLayerControls();
            }

            // Cargar ubicaciones y bootstrap
            async function loadLocations() {
                showLoader();
                const API_URL = '/api/locations';
                try {
                    const res = await fetch(API_URL, { cache: 'no-store' });
                    if (!res.ok) {
                        console.warn(`La API respondió ${res.status}. Usando datos de ejemplo.`);
                        locations = SAMPLE_LOCATIONS;
                    } else {
                        const data = await res.json();
                        if (!Array.isArray(data)) {
                            console.warn('Formato inesperado de respuesta API; se esperaba array. Usando ejemplo.');
                            locations = SAMPLE_LOCATIONS;
                        } else {
                            locations = data;
                        }
                    }
                } catch (err) {
                    console.error('Error al obtener /api/locations:', err);
                    locations = SAMPLE_LOCATIONS;
                }

                // calcular bounds válidos
                const latLngs = locations
                    .filter(l => l.coordenadas && typeof l.coordenadas.lat === 'number' && typeof l.coordenadas.lng === 'number')
                    .map(l => [l.coordenadas.lat, l.coordenadas.lng]);

                let bounds = null;
                if (latLngs.length > 0) bounds = L.latLngBounds(latLngs);

                // crear mapa ahora (evita salto)
                initializeMap(bounds);

                // llenar UI y renderizar marcadores
                buildFiltersFromData(locations);
                renderMarkers(locations);
                searchIndex = buildSearchIndex(locations);

                // ajusta vista y oculta loader
                setTimeout(() => {
                    if (bounds && bounds.isValid()) map.fitBounds(bounds.pad(0.15));
                    map.invalidateSize();
                    hideLoader();
                }, 250);
            }

            // inicia proceso
            loadLocations();

            // Hook de debug
            window.__OVINO_MAP = { getState: () => ({ map, markersCluster, locations, markersIndex }) };
        })();
    </script>
</body>

</html>