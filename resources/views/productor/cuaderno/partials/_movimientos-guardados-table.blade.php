<fieldset class="border-2 border-gray-400 p-4">
    <legend @click="showMovimientosGuardados = !showMovimientosGuardados" class="px-2 font-bold text-gray-800 cursor-pointer flex items-center select-none">
        <span>Movimientos Guardados en este Periodo</span>
        <p x-show="!showMovimientosGuardados" class="ml-4 text-sm text-blue-600 font-semibold">(Clic para expandir/contraer)</p>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-600 transition-transform" :class="{ 'rotate-180': showMovimientosGuardados }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
    </legend>
    <div x-show="showMovimientosGuardados" x-collapse class="mt-4">
        <div class="mb-4">
            <div class="flex items-center">
                <label for="day-filter-saved" class="text-sm font-bold text-gray-700 mr-2">Filtrar por día:</label>
                <select x-model="dayFilter" @change="updateMovimientosGuardados()" id="day-filter-saved" class="bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="todos">Todos</option>
                    @foreach (['Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0] as $dayName => $dayValue)
                        <option value="{{ $dayValue }}">{{ $dayName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="overflow-x-auto border-2 border-gray-400 min-h-[250px]">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-300 border-b-2 border-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Tipo</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Fecha</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Motivo</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Especie</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Categoría</th>
                        <th class="px-4 py-2 text-right text-sm font-bold text-gray-700">Cantidad</th>
                    </tr>
                </thead>
                <tbody x-ref="movimientosGuardadosBody" class="divide-y divide-gray-400">
                    @forelse ($movimientosGuardadosData as $movimiento)
                        <tr class="bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium border-r-2 border-gray-400"><span class="font-semibold {{ optional($movimiento->motivo)->tipo === 'alta' ? 'text-green-600' : 'text-red-600' }}">{{ strtoupper(optional($movimiento->motivo)->tipo ?? 'N/A') }}</span></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ \Carbon\Carbon::parse($movimiento->fecha_registro)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->motivo)->nombre ?? 'Motivo no encontrado' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->especie)->nombre ?? 'Especie no encontrada' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400">{{ optional($movimiento->categoria)->nombre ?? 'Categoría no encontrada' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right font-bold">{{ $movimiento->cantidad }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500"><p>No hay movimientos guardados en el periodo actual.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</fieldset>
