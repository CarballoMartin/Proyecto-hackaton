<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
        }
        .filters {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        thead {
            background-color: #eee;
        }
        h3, h4 {
            margin-bottom: 5px;
        }
        .alta {
            color: green;
        }
        .baja {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historial de Movimientos de Stock</h1>
        <h2>Productor: {{ $productor->nombre }}</h2>
    </div>

    <div class="filters">
        <strong>Filtros Aplicados:</strong><br>
        Rango de Fecha: {{ $filters['fecha_desde'] ?? 'Inicio' }} al {{ $filters['fecha_hasta'] ?? 'Fin' }} <br>
        @if(!empty($filters['up_id']))
            Unidad Productiva: {{ $filters['up_id'] ? optional($productor->unidadesProductivas->find($filters['up_id']))->nombre : 'Todas' }} <br> <br>
        @endif
        @if(!empty($filters['flujo']))
            Flujo: {{ strtoupper($filters['flujo']) }} <br>
        @endif
        @if(!empty($filters['motivo_id']))
            Motivo: {{ $resumen['motivo_filtrado'] ?? 'N/A' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>U. Productiva</th>
                <th>Tipo</th>
                <th>Motivo</th>
                <th>Especie</th>
                <th>Categoría</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movimientos as $movimiento)
                <tr>
                    <td>{{ $movimiento->fecha_registro->format('d/m/Y') }}</td>
                    <td>{{ $movimiento->unidadProductiva->nombre ?? 'N/A' }}</td>
                    @if (optional($movimiento->motivo)->tipo === 'alta')
                        <td class="alta">ENTRADA</td>
                    @else
                        <td class="baja">SALIDA</td>
                    @endif
                    <td>{{ optional($movimiento->motivo)->nombre ?? 'N/A' }}</td>
                    <td>{{ optional($movimiento->especie)->nombre ?? 'N/A' }}</td>
                    <td>{{ optional($movimiento->categoria)->nombre ?? 'N/A' }}</td>
                    @if (optional($movimiento->motivo)->tipo === 'alta')
                        <td class="alta" style="font-weight: bold;">+ {{ $movimiento->cantidad }}</td>
                    @else
                        <td class="baja" style="font-weight: bold;">- {{ $movimiento->cantidad }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No se encontraron movimientos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    @if ($resumen['altas']['total'] > 0 || $resumen['bajas']['total'] > 0)
        <div style="margin-top: 30px;">
            <h3>Resumen de Movimientos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($filters['motivo_id']))
                        <tr style="background-color: #e3f2fd;">
                            <td style="font-weight: bold;">Total para {{ $resumen['motivo_filtrado'] }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $resumen['specific_total'] }}</td>
                        </tr>
                    @else
                        @if ((empty($filters['flujo']) || $filters['flujo'] === 'alta') && $resumen['altas']['total'] > 0)
                            <tr style="background-color: #e8f5e9;">
                                <td colspan="2" style="font-weight: bold;">ALTAS</td>
                            </tr>
                            @foreach ($resumen['altas'] as $motivo => $total)
                                @if ($motivo !== 'total')
                                    <tr>
                                        <td style="padding-left: 20px;">{{ $motivo }}</td>
                                        <td style="text-align: right;">{{ $total }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="background-color: #e8f5e9; font-weight: bold;">
                                <td style="padding-left: 20px;">Total Altas</td>
                                <td style="text-align: right;">{{ $resumen['altas']['total'] }}</td>
                            </tr>
                        @endif
                        @if ((empty($filters['flujo']) || $filters['flujo'] === 'baja') && $resumen['bajas']['total'] > 0)
                            <tr style="background-color: #ffebee;">
                                <td colspan="2" style="font-weight: bold;">BAJAS</td>
                            </tr>
                            @foreach ($resumen['bajas'] as $motivo => $total)
                                @if ($motivo !== 'total')
                                    <tr>
                                        <td style="padding-left: 20px;">{{ $motivo }}</td>
                                        <td style="text-align: right;">{{ $total }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="background-color: #ffebee; font-weight: bold;">
                                <td style="padding-left: 20px;">Total Bajas</td>
                                <td style="text-align: right;">{{ $resumen['bajas']['total'] }}</td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Charts Section -->
        <div style="margin-top: 30px;">
            <h3>Gráficos de Resumen</h3>

            @php
                $altasSinTotal = array_diff_key($resumen['altas'], ['total' => 0]);
                $bajasSinTotal = array_diff_key($resumen['bajas'], ['total' => 0]);
                $max_alta = !empty($altasSinTotal) ? max($altasSinTotal) : 0;
                $max_baja = !empty($bajasSinTotal) ? max($bajasSinTotal) : 0;
                $max_value = max($max_alta, $max_baja, 1);
                $max_bar_width = 250;
            @endphp

            @if ((empty($filters['flujo']) || $filters['flujo'] === 'alta') && $resumen['altas']['total'] > 0)
                <div style="margin-top: 20px;">
                    <h4>Desglose de Altas</h4>
                    @foreach ($altasSinTotal as $motivo => $total)
                        <div style="margin-bottom: 5px; font-size: 11px;">
                            <div style="width: 120px; display: inline-block;">{{ $motivo }}:</div>
                            <div style="display: inline-block; height: 14px; background-color: #a5d6a7; width: {{ ($total / $max_value) * $max_bar_width }}px; vertical-align: middle;"></div>
                            <span style="padding-left: 5px; vertical-align: middle;">{{ $total }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ((empty($filters['flujo']) || $filters['flujo'] === 'baja') && $resumen['bajas']['total'] > 0)
                <div style="margin-top: 20px;">
                    <h4>Desglose de Bajas</h4>
                    @foreach ($bajasSinTotal as $motivo => $total)
                        <div style="margin-bottom: 5px; font-size: 11px;">
                            <div style="width: 120px; display: inline-block;">{{ $motivo }}:</div>
                            <div style="display: inline-block; height: 14px; background-color: #ef9a9a; width: {{ ($total / $max_value) * $max_bar_width }}px; vertical-align: middle;"></div>
                            <span style="padding-left: 5px; vertical-align: middle;">{{ $total }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</body>
</html>