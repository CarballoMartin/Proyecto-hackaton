<div class="bg-white rounded-lg shadow p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            🌦️ Clima Actual
        </h3>
        @if($datosClima)
            <span class="text-xs text-gray-500">
                Actualizado: {{ $datosClima->fecha_consulta->diffForHumans() }}
            </span>
        @endif
    </div>

    @if($datosClima && $datosClima->esVigente())
        {{-- Clima actual --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-5xl font-bold text-gray-900">
                        {{ number_format($datosClima->temperatura_actual, 1) }}°C
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        @if($unidadSeleccionada)
                            {{ $unidadSeleccionada->nombre }}
                        @endif
                    </div>
                </div>
                <div class="text-6xl">
                    {{ $datosClima->obtenerIconoClima() }}
                </div>
            </div>

            {{-- Viento --}}
            @if($datosClima->velocidad_viento)
                <div class="mt-3 text-sm text-gray-600">
                    💨 Viento: {{ number_format($datosClima->velocidad_viento, 1) }} km/h
                </div>
            @endif
        </div>

        {{-- Pronóstico 7 días --}}
        @if($datosClima->fechas && count($datosClima->fechas) > 0)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-3">
                    Pronóstico 7 días
                </h4>
                <div class="space-y-2">
                    @foreach($datosClima->fechas as $index => $fecha)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m') }} - 
                                {{ \Carbon\Carbon::parse($fecha)->locale('es')->dayName }}
                            </span>
                            <div class="flex items-center space-x-3">
                                @if(isset($datosClima->precipitacion[$index]) && $datosClima->precipitacion[$index] > 0)
                                    <span class="text-blue-600 text-xs">
                                        🌧️ {{ number_format($datosClima->precipitacion[$index], 1) }}mm
                                    </span>
                                @endif
                                <span class="text-gray-900 font-medium">
                                    {{ number_format($datosClima->temperaturas_max[$index] ?? 0, 0) }}° / 
                                    {{ number_format($datosClima->temperaturas_min[$index] ?? 0, 0) }}°
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        {{-- Sin datos --}}
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-2">🌡️</div>
            <p class="text-sm">
                No hay datos climáticos disponibles.
            </p>
            <p class="text-xs mt-1">
                Los datos se actualizan automáticamente cada 24 horas.
            </p>
        </div>
    @endif
</div>
