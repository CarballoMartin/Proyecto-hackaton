@props([
    // Datos iniciales para el primer renderizado, pasados desde el controlador.
    'initialData' => null,
    // El endpoint de la API de donde se obtendrán los datos completos del clima.
    'endpoint' => '/api/clima',
])

<div class="border bg-white rounded-lg shadow-sm p-4 h-48 flex flex-col"
    x-data="weatherWidget({ 
        initialData: @js($initialData), 
        endpoint: '{{ $endpoint }}' 
    })"
    x-init="initWidget()">

    <!-- Encabezado y Dropdown -->
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-semibold text-gray-900">Clima</h3>
        <div class="relative" x-show="allWeather.length > 1">
            <label for="weather-location-select" class="sr-only">Seleccionar ubicación</label>
            <select id="weather-location-select" x-model="selectedMunicipioId" 
                    class="block w-full pl-3 pr-10 py-1 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                    :disabled="isLoading">
                <template x-for="weather in allWeather" :key="weather.id">
                    <option :value="weather.id" x-text="weather.location"></option>
                </template>
            </select>
            <div x-show="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-8 pointer-events-none">
                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <!-- Subtítulo (si hay un solo clima) -->
        <div x-show="allWeather.length === 1 && !isLoading">
            <p class="text-sm text-gray-600" x-text="currentWeather ? currentWeather.location : ''"></p>
        </div>
    </div>

    <!-- Contenido del Clima (ocupa el espacio restante) -->
    <div class="flex-grow flex items-center justify-center">
        <template x-if="currentWeather">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10">
                        <template x-if="currentWeather.icon">
                            <div>
                                <x-heroicon-s-sun x-show="currentWeather.icon === 'heroicon-s-sun'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-s-moon x-show="currentWeather.icon === 'heroicon-s-moon'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-s-cloud x-show="currentWeather.icon === 'heroicon-s-cloud'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-s-cloud-arrow-down x-show="currentWeather.icon === 'heroicon-s-cloud-arrow-down'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-s-bolt x-show="currentWeather.icon === 'heroicon-s-bolt'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-s-bars-3 x-show="currentWeather.icon === 'heroicon-s-bars-3'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                                <x-heroicon-o-exclamation-circle x-show="currentWeather.icon === 'heroicon-o-exclamation-circle'" x-bind:class="`h-10 w-10 ${currentWeather.iconColor}`" />
                            </div>
                        </template>
                    </div>
                    <div class="text-3xl font-bold text-gray-800">
                        <span x-text="currentWeather.temperature"></span>&deg;C
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-600 capitalize" x-text="currentWeather.condition"></div>
                    <div class="text-xs text-gray-500">
                        Max: <span x-text="currentWeather.high"></span>&deg; / Min: <span x-text="currentWeather.low"></span>&deg;
                    </div>
                </div>
            </div>
        </template>

        <!-- Estado de No Hay Datos -->
        <template x-if="!currentWeather && !isLoading">
            <div class="text-center text-gray-500">
                <p class="text-sm">No hay datos de clima disponibles.</p>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
    function weatherWidget(config) {
        return {
            initialData: config.initialData,
            endpoint: config.endpoint,
            allWeather: config.initialData ? [config.initialData] : [],
            currentWeather: config.initialData,
            selectedMunicipioId: config.initialData ? config.initialData.id : null,
            isLoading: true,

            initWidget() {
                this.fetchData();
                this.$watch('selectedMunicipioId', (newId) => {
                    if (this.allWeather && this.allWeather.length > 0) {
                        const found = this.allWeather.find(w => w.id == newId);
                        if (found) {
                            this.currentWeather = found;
                        }
                    }
                });
            },

            fetchData() {
                this.isLoading = true;
                fetch(this.endpoint)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.length > 0) {
                            this.allWeather = data;
                            const stillExists = this.allWeather.find(w => w.id == this.selectedMunicipioId);
                            if (!stillExists) {
                                this.selectedMunicipioId = data[0].id;
                                this.currentWeather = data[0];
                            } else {
                                this.currentWeather = stillExists;
                            }
                        } else {
                            this.allWeather = [];
                            this.currentWeather = null;
                        }
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching weather data from ' + this.endpoint + ':', error);
                        this.allWeather = this.initialData ? [this.initialData] : [];
                        this.currentWeather = this.initialData;
                        this.isLoading = false;
                    });
            }
        }
    }
</script>
@endpush