@php
use App\Services\CertificacionAmbientalService;
use App\Services\HuellaCarbonService;
use Illuminate\Support\Facades\Auth;
use App\Models\Productor;

$certificacionService = app(CertificacionAmbientalService::class);
$huellaCarbonService = app(HuellaCarbonService::class);
$productor = Productor::where('usuario_id', Auth::id())->first();
$certificacion = $productor ? $certificacionService->calcularCertificacion($productor) : null;
$infoNivel = $certificacion ? $certificacionService->obtenerInfoNivel($certificacion['nivel']) : null;
$huellaCarbono = $productor ? $huellaCarbonService->calcularHuellaTotal($productor) : null;
@endphp

<div x-data="{ open: false }" 
     @open-ecoganaderia-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-show="open" 
     style="display: none;" 
     class="fixed z-50 inset-0 overflow-y-auto">
    
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background overlay - Click para cerrar -->
        <div x-show="open" 
             @click="open = false"
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 transition-opacity cursor-pointer" 
             aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div x-show="open" 
             @click.stop
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full relative max-h-[90vh] overflow-hidden">
            
            <!-- Header del Dashboard con botón de cierre -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center">
                            <x-heroicon-o-globe-americas class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">🌍 Centro de Control Ambiental</h3>
                            <p class="text-green-100 text-sm">Dashboard de Impacto en Tiempo Real</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <div class="text-white text-sm">Última actualización</div>
                            <div class="text-green-200 text-xs" x-text="new Date().toLocaleTimeString()"></div>
                        </div>
                        <!-- Botón de cierre -->
                        <button @click="open = false" 
                                class="text-white hover:text-green-100 transition-colors p-2 hover:bg-white/10 rounded-full"
                                title="Cerrar">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content con scroll -->
            <div class="p-6 space-y-6 overflow-y-auto max-h-[calc(90vh-80px)] scrollbar-thin scrollbar-thumb-green-500 scrollbar-track-green-100">
                
                <!-- Métricas Principales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Huella de Carbono -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Huella de Carbono</h4>
                                    <p class="text-sm text-gray-600">kg CO₂ equivalente</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            @if($certificacion)
                            <div class="text-3xl font-bold text-red-600 mb-2">{{ $certificacion['metricas']['agua'] ?? 0 }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-red-400 to-red-600 h-2 rounded-full transition-all duration-1000" style="width: {{ ($certificacion['metricas']['agua'] ?? 0) / 80 * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ round(($certificacion['metricas']['agua'] ?? 0) / 80 * 100) }}% de gestión óptima</p>
                            @else
                            <div class="text-3xl font-bold text-gray-400 mb-2">--</div>
                            <p class="text-xs text-gray-500 mt-2">Sin datos disponibles</p>
                            @endif
                        </div>
                            </div>

                    <!-- Eficiencia Hídrica -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Eficiencia Hídrica</h4>
                                    <p class="text-sm text-gray-600">L/kg producción</p>
                                </div>
                                            </div>
                                        </div>
                        <div class="text-center">
                            @if($certificacion)
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $certificacion['metricas']['eficiencia'] ?? 0 }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-1200" style="width: {{ ($certificacion['metricas']['eficiencia'] ?? 0) / 90 * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ round(($certificacion['metricas']['eficiencia'] ?? 0) / 90 * 100) }}% de eficiencia</p>
                            @else
                            <div class="text-3xl font-bold text-gray-400 mb-2">--</div>
                            <p class="text-xs text-gray-500 mt-2">Sin datos disponibles</p>
                            @endif
                                        </div>
                                    </div>
                                    
                    <!-- Índice de Biodiversidad -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Biodiversidad</h4>
                                    <p class="text-sm text-gray-600">Índice de riqueza</p>
                                </div>
                                            </div>
                                        </div>
                        <div class="text-center">
                            @if($certificacion)
                            <div class="text-3xl font-bold text-green-600 mb-2">{{ $certificacion['metricas']['biodiversidad'] ?? 0 }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-1500" style="width: {{ ($certificacion['metricas']['biodiversidad'] ?? 0) / 70 * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ round(($certificacion['metricas']['biodiversidad'] ?? 0) / 70 * 100) }}% de biodiversidad</p>
                            @else
                            <div class="text-3xl font-bold text-gray-400 mb-2">--</div>
                            <p class="text-xs text-gray-500 mt-2">Sin datos disponibles</p>
                            @endif
                        </div>
                                        </div>
                                    </div>
                                    
                <!-- Calculadora de Huella de Carbono -->
                @if($huellaCarbono && $huellaCarbono['total_animales'] > 0)
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">📊 Calculadora de Huella de Carbono</h4>
                                <p class="text-sm text-orange-600">Análisis automático de emisiones</p>
                            </div>
                        </div>
                        <div class="text-center bg-white rounded-lg px-4 py-2 shadow">
                            <div class="text-xs text-gray-500">Clasificación</div>
                            <div class="text-lg font-bold text-{{ $huellaCarbono['clasificacion']['color'] }}-600">
                                {{ $huellaCarbono['clasificacion']['icono'] }} {{ ucfirst($huellaCarbono['clasificacion']['nivel']) }}
                            </div>
                                            </div>
                                        </div>

                    <!-- Resumen de Emisiones -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">Emisiones Anuales</div>
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($huellaCarbono['emisiones_totales_co2eq_kg'], 0) }}</div>
                            <div class="text-xs text-gray-500">kg CO₂eq</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">Por Animal</div>
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($huellaCarbono['emisiones_por_animal'], 1) }}</div>
                            <div class="text-xs text-gray-500">kg CO₂eq/animal</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">Por Hectárea</div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($huellaCarbono['emisiones_por_hectarea'], 1) }}</div>
                            <div class="text-xs text-gray-500">kg CO₂eq/ha</div>
                                        </div>
                                    </div>
                                    
                    <!-- Desglose por Especie -->
                    @if(!empty($huellaCarbono['detalle_especies']))
                    <div class="bg-white rounded-lg p-4 shadow-sm mb-4">
                        <h5 class="font-semibold text-gray-900 mb-3 text-sm">Emisiones por Especie</h5>
                        <div class="space-y-2">
                            @foreach($huellaCarbono['detalle_especies'] as $especie)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-700">{{ $especie['especie'] }} ({{ $especie['cantidad'] }} animales)</span>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($especie['co2_equivalente_kg'], 0) }} kg</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $especie['porcentaje'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Equivalencias -->
                    @if(!empty($huellaCarbono['equivalencias']))
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4 mb-4">
                        <h5 class="font-semibold text-gray-900 mb-3 text-sm">🌍 Para dimensionar el impacto</h5>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl mb-1">🌳</div>
                                <div class="text-lg font-bold text-green-600">{{ $huellaCarbono['equivalencias']['arboles_necesarios'] }}</div>
                                <div class="text-xs text-gray-600">árboles necesarios para compensar</div>
                            </div>
                            <div>
                                <div class="text-2xl mb-1">🚗</div>
                                <div class="text-lg font-bold text-blue-600">{{ number_format($huellaCarbono['equivalencias']['km_auto'], 0) }}</div>
                                <div class="text-xs text-gray-600">km en auto</div>
                            </div>
                            <div>
                                <div class="text-2xl mb-1">🏠</div>
                                <div class="text-lg font-bold text-purple-600">{{ $huellaCarbono['equivalencias']['hogares_anual'] }}</div>
                                <div class="text-xs text-gray-600">hogares/año</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Potencial de Reducción -->
                    @if(!empty($huellaCarbono['potencial_reduccion']))
                    <div class="bg-green-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-900 mb-3 text-sm flex items-center">
                            📉 Potencial de Reducción
                        </h5>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white rounded p-3">
                                <div class="text-xs text-gray-600">Escenario Conservador</div>
                                <div class="text-xl font-bold text-green-600">-{{ $huellaCarbono['potencial_reduccion']['reduccion_conservadora']['porcentaje'] }}%</div>
                                <div class="text-xs text-gray-500">{{ number_format($huellaCarbono['potencial_reduccion']['reduccion_conservadora']['kg_co2_anual'], 0) }} kg CO₂/año</div>
                                <div class="text-xs text-gray-400 mt-1">Plazo: {{ $huellaCarbono['potencial_reduccion']['reduccion_conservadora']['plazo'] }}</div>
                            </div>
                            <div class="bg-white rounded p-3">
                                <div class="text-xs text-gray-600">Escenario Optimista</div>
                                <div class="text-xl font-bold text-green-600">-{{ $huellaCarbono['potencial_reduccion']['reduccion_optimista']['porcentaje'] }}%</div>
                                <div class="text-xs text-gray-500">{{ number_format($huellaCarbono['potencial_reduccion']['reduccion_optimista']['kg_co2_anual'], 0) }} kg CO₂/año</div>
                                <div class="text-xs text-gray-400 mt-1">Plazo: {{ $huellaCarbono['potencial_reduccion']['reduccion_optimista']['plazo'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Sección de Progreso -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Objetivos Mensuales -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <x-heroicon-o-chart-bar class="h-5 w-5 text-green-500 mr-2" />
                            Progreso por Categoría
                        </h4>
                        @if($certificacion)
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600">💧 Gestión del Agua</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $certificacion['metricas']['agua'] }}/80</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ ($certificacion['metricas']['agua'] / 80) * 100 }}%"></div>
                                            </div>
                                        </div>
                            
                                        <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600">🦋 Biodiversidad</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $certificacion['metricas']['biodiversidad'] }}/70</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ ($certificacion['metricas']['biodiversidad'] / 70) * 100 }}%"></div>
                                        </div>
                                    </div>
                            
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600">⚡ Eficiencia Productiva</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $certificacion['metricas']['eficiencia'] }}/90</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full transition-all" style="width: {{ ($certificacion['metricas']['eficiencia'] / 90) * 100 }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-gray-600">🌱 Manejo Sostenible</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $certificacion['metricas']['sostenibilidad'] }}/60</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ ($certificacion['metricas']['sostenibilidad'] / 60) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-500 text-center py-4">No hay datos disponibles</p>
                        @endif
                    </div>

                    <!-- Certificación Ambiental -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-lg p-6 border border-yellow-200">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <x-heroicon-o-star class="h-5 w-5 text-yellow-500 mr-2" />
                            Certificación Ambiental
                                </h4>
                        @if($certificacion && $infoNivel)
                        <div class="text-center">
                            <div class="text-4xl mb-2">{{ $infoNivel['icono'] }}</div>
                            <h5 class="text-xl font-bold text-gray-900 mb-1">Nivel {{ $infoNivel['nombre'] }}</h5>
                            <p class="text-sm text-gray-600 mb-4">{{ $infoNivel['descripcion'] }}</p>
                            
                            <!-- Barra de progreso general -->
                            <div class="w-full bg-gray-300 rounded-full h-3 mb-4">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-3 rounded-full transition-all duration-1000" 
                                     style="width: {{ $certificacion['porcentaje'] }}%"></div>
                            </div>
                            
                            <div class="text-sm text-gray-600 mb-4">
                                <span class="font-medium">{{ $certificacion['puntaje_total'] }} puntos</span> de {{ $certificacion['puntaje_maximo'] }} totales
                            </div>

                            @if($certificacion['siguiente_nivel'])
                            @php
                            $siguienteInfo = $certificacionService->obtenerInfoNivel($certificacion['siguiente_nivel']);
                            @endphp
                            <div class="bg-white rounded-lg p-3 mt-3">
                                <p class="text-xs text-gray-500 mb-1">Próximo nivel:</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $siguienteInfo['icono'] }} {{ $siguienteInfo['nombre'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">Faltan <span class="font-bold text-green-600">{{ $certificacion['puntos_para_siguiente'] }}</span> puntos</p>
                            </div>
                            @else
                            <div class="bg-gradient-to-r from-purple-100 to-purple-200 rounded-lg p-3 mt-3">
                                <p class="text-sm font-bold text-purple-900">🎉 ¡Nivel máximo alcanzado!</p>
                                <p class="text-xs text-purple-700">Eres un líder en sustentabilidad</p>
                            </div>
                            @endif
                        </div>
                        @else
                                    <div class="text-center">
                            <div class="text-4xl mb-2">⚪</div>
                            <h5 class="text-xl font-bold text-gray-900 mb-1">Sin Certificación</h5>
                            <p class="text-sm text-gray-600">Completa tus datos para obtener tu certificación</p>
                                    </div>
                        @endif
                                    </div>
                                    </div>

                <!-- Badges Ganados -->
                @if($certificacion && !empty($certificacion['badges']))
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <x-heroicon-o-trophy class="h-5 w-5 text-yellow-500 mr-2" />
                        Insignias Ganadas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($certificacion['badges'] as $badge)
                        <div class="bg-gradient-to-br from-{{ $badge['color'] }}-50 to-{{ $badge['color'] }}-100 rounded-lg p-4 border border-{{ $badge['color'] }}-200">
                            <div class="flex items-start space-x-3">
                                <div class="text-3xl">{{ $badge['icono'] }}</div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-gray-900 mb-1">{{ $badge['nombre'] }}</h5>
                                    <p class="text-xs text-gray-600">{{ $badge['descripcion'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recomendaciones -->
                <div class="bg-gradient-to-r from-green-100 to-emerald-100 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <x-heroicon-o-light-bulb class="h-5 w-5 text-green-600 mr-2" />
                        Recomendaciones Personalizadas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($huellaCarbono && !empty($huellaCarbono['recomendaciones']))
                            @foreach($huellaCarbono['recomendaciones'] as $recomendacion)
                            <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-{{ $recomendacion['impacto'] == 'alto' ? 'red' : ($recomendacion['impacto'] == 'medio' ? 'yellow' : 'green') }}-400">
                                <div class="flex items-start justify-between mb-2">
                                    <h5 class="font-semibold text-gray-900 text-sm">{{ $recomendacion['titulo'] }}</h5>
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                        {{ $recomendacion['reduccion_estimada'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600">{{ $recomendacion['descripcion'] }}</p>
                                <div class="mt-2 text-xs text-gray-500">
                                    Impacto: <span class="font-medium capitalize">{{ $recomendacion['impacto'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h5 class="font-semibold text-gray-900 mb-2">🌱 Sistema Silvopastoril</h5>
                                <p class="text-sm text-gray-600">Plantar árboles nativos puede mejorar tu puntuación de biodiversidad</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h5 class="font-semibold text-gray-900 mb-2">💧 Gestión del Agua</h5>
                                <p class="text-sm text-gray-600">Optimizar las fuentes de agua mejorará tu certificación</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h5 class="font-semibold text-gray-900 mb-2">📊 Completa tus Datos</h5>
                                <p class="text-sm text-gray-600">Registra todas tus unidades productivas para aumentar tu puntaje</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h5 class="font-semibold text-gray-900 mb-2">🐑 Diversifica</h5>
                                <p class="text-sm text-gray-600">Mantén diversidad de razas y especies para mejor puntuación</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


