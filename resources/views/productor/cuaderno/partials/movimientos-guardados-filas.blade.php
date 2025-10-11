@forelse ($movimientos as $movimiento)
    <tr class="bg-gray-50">
        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium border-r-2 border-gray-400">
            <span class="font-semibold {{ optional($movimiento->motivo)->tipo === 'alta' ? 'text-green-600' : 'text-red-600' }}">
                {{ strtoupper(optional($movimiento->motivo)->tipo ?? 'N/A') }}
            </span>
        </td>
        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ \Carbon\Carbon::parse($movimiento->fecha_registro)->format('d/m/Y') }}</td>
        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->motivo)->nombre ?? 'Motivo no encontrado' }}</td>
        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->especie)->nombre ?? 'Especie no encontrada' }}</td>
        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->categoria)->nombre ?? 'Categoría no encontrada' }}</td>
        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right font-bold">{{ $movimiento->cantidad }}</td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
            <p>No se encontraron movimientos para el día seleccionado.</p>
        </td>
    </tr>
@endforelse
