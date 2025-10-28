<div>
    <div @chacra-updated.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
        <div class="px-4 sm:px-6 lg:px-8 py-8">

            {{-- Notifications --}}
            <div class="space-y-4 mb-8">
                @if (session('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md"
                        role="alert">
                        <div class="flex">
                            <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg></div>
                            <div>
                                <p class="font-bold">Éxito</p>
                                <p class="text-sm">{{ session('message') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md"
                        role="alert">
                        <div class="flex">
                            <div class="py-1"><svg class="h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg></div>
                            <div>
                                <p class="font-bold">Error</p>
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-8">

                {{-- SECCIÓN 1: INFORMACIÓN Y MAPA --}}
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800">Resumen de la Unidad Productiva</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        {{-- Columna Izquierda: Datos Esenciales --}}
                        <div class="space-y-4">
                             <h4 class="text-lg font-semibold text-gray-700 border-b pb-2">Información Esencial</h4>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="font-medium text-gray-500">N° de RNSPA</dt>
                                    <dd class="mt-1 text-gray-900 font-semibold">{{ $up->identificador_local }}</dd>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Superficie</dt>
                                    <dd class="mt-1 text-gray-900">{{ $up->superficie }} ha</dd>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Municipio</dt>
                                    <dd class="mt-1 text-gray-900">{{ $up->municipio->nombre }}</dd>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Paraje</dt>
                                    <dd class="mt-1 text-gray-900">{{ $up->paraje ? $up->paraje->nombre : '-' }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="font-medium text-gray-500">Condición de Tenencia</dt>
                                    <dd class="mt-1 text-gray-900">
                                        {{ $up->condicionTenencia ? $up->condicionTenencia->nombre : '-' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        {{-- Columna Derecha: Mapa --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-lg font-semibold text-gray-700 border-b pb-2 flex-grow">Ubicación</h4>
                                <a href="{{ route('productor.unidades-productivas.mapa', ['id' => $up->id]) }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Pantalla completa</a>
                            </div>
                            <div class="p-2 bg-gray-50 rounded-lg mt-2">
                                <div id="map-preview" style="height: 250px; border-radius: 0.5rem;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: FORMULARIO --}}
                <form wire:submit.prevent="update">
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                        <div class="p-6 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Completar o Editar Información</h3>
                            <p class="text-gray-500 mt-1">Estos datos son opcionales pero ayudan a tener un registro más completo.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">

                                <div class="space-y-4 p-4 bg-gray-50 rounded-xl border">
                                    <h4 class="font-semibold text-gray-800">Agua para Consumo Humano</h4>
                                    <div>
                                        <label for="agua_humano_fuente_id" class="block text-sm font-medium text-gray-700">Fuente de Agua</label>
                                        <select wire:model="agua_humano_fuente_id" id="agua_humano_fuente_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Seleccionar fuente</option>
                                            @foreach($fuentes_agua as $fuente)
                                                <option value="{{ $fuente->id }}">{{ $fuente->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="agua_humano_en_casa" id="agua_humano_en_casa" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="agua_humano_en_casa" class="ml-2 block text-sm text-gray-900">Agua en la casa</label>
                                    </div>
                                    <div>
                                        <label for="agua_humano_distancia" class="block text-sm font-medium text-gray-700">Distancia (metros)</label>
                                        <input type="number" wire:model="agua_humano_distancia" id="agua_humano_distancia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div class="space-y-4 p-4 bg-gray-50 rounded-xl border">
                                    <h4 class="font-semibold text-gray-800">Agua para Consumo Animal</h4>
                                    <div>
                                        <label for="agua_animal_fuente_id" class="block text-sm font-medium text-gray-700">Fuente de Agua</label>
                                        <select wire:model="agua_animal_fuente_id" id="agua_animal_fuente_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Seleccionar fuente</option>
                                            @foreach($fuentes_agua as $fuente)
                                                <option value="{{ $fuente->id }}">{{ $fuente->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="agua_animal_distancia" class="block text-sm font-medium text-gray-700">Distancia (metros)</label>
                                        <input type="number" wire:model="agua_animal_distancia" id="agua_animal_distancia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div class="space-y-4 p-4 bg-gray-50 rounded-xl border">
                                    <h4 class="font-semibold text-gray-800">Pasto y Suelo</h4>
                                    <div>
                                        <label for="tipo_pasto_predominante_id" class="block text-sm font-medium text-gray-700">Tipo de Pasto Predominante</label>
                                        <select wire:model="tipo_pasto_predominante_id" id="tipo_pasto_predominante_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Seleccionar tipo</option>
                                            @foreach($tipos_pasto as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="tipo_suelo_predominante_id" class="block text-sm font-medium text-gray-700">Tipo de Suelo Predominante</label>
                                        <select wire:model="tipo_suelo_predominante_id" id="tipo_suelo_predominante_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Seleccionar tipo</option>
                                            @foreach($tipos_suelo as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="forrajeras_predominante" id="forrajeras_predominante" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="forrajeras_predominante" class="ml-2 block text-sm text-gray-900">Forrajeras predominantes</label>
                                    </div>
                                </div>

                                <div class="md:col-span-2 space-y-2 p-4 bg-gray-50 rounded-xl border">
                                    <h4 class="font-semibold text-gray-800">Observaciones</h4>
                                    <div>
                                        <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones Adicionales</label>
                                        <textarea wire:model="observaciones" id="observaciones" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Información adicional..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t">
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-150">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const locations = @json($locations);
                console.log('Map locations:', locations);

                if (!document.getElementById('map-preview') || locations.length === 0) {
                    if (document.getElementById('map-preview')) {
                        document.getElementById('map-preview').innerHTML = '<div class="text-center text-gray-500 py-8">No hay datos de ubicación para mostrar.</div>';
                    }
                    return;
                }

                const map = L.map('map-preview', {
                    scrollWheelZoom: true,
                    dragging: false,
                    zoomControl: true,
                    tap: false,
                    touchZoom: false,
                    doubleClickZoom: false,
                    boxZoom: false,
                    keyboard: false
                });

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
                locations.forEach(loc => {
                    const icon = loc.is_current ? currentIcon : otherIcon;
                    const marker = L.marker([loc.lat, loc.lon], { icon: icon }).addTo(map);
                    marker.bindTooltip(loc.name);
                    markers.push(marker);
                });

                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            });
        </script>
    @endpush
</div>
