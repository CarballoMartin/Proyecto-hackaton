<fieldset class="border-2 border-gray-400 p-4">
    <legend class="px-2 font-bold">Movimientos Pendientes de Guardar</legend>
    <div class="flex justify-end items-center mb-4">
        <div class="flex space-x-2">
            <button @click="openModal('altas')" class="px-4 py-1 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold">Registrar Altas (+)</button>
            <button @click="openModal('bajas')" class="px-4 py-1 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold">Registrar Bajas (-)</button>
        </div>
    </div>
    <div class="overflow-x-auto border-2 border-gray-400">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-300 border-b-2 border-gray-400">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Tipo</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Día</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Motivo</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Especie</th>
                    <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Categoría</th>
                    <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 border-r-2 border-gray-400">Cantidad</th>
                    <th class="px-4 py-2 text-center text-sm font-bold text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-400">
                <template x-for="movement in filteredMovements" :key="movement.id">
                    <tr class="hover:bg-gray-200" :class="{'bg-green-50': movement.tipo === 'altas', 'bg-red-50': movement.tipo === 'bajas'}">
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium border-r-2 border-gray-400"><span x-text="movement.tipo.toUpperCase()" :class="movement.tipo === 'alta' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"></span></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="dayNames[new Date(movement.createdAt).getDay()]"></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="movement.motivo_nombre"></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="movement.especie_nombre"></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="movement.categoria_nombre"></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right font-bold border-r-2 border-gray-400" x-text="movement.cantidad"></td>
                        <td class="px-4 py-2 whitespace-nowrap text-center">
                            <button @click="$store.cuaderno.removeMovementFromSummary(movement.id)" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                            </button>
                        </td>
                    </tr>
                </template>
                <template x-if="!$store.cuaderno.summary.items || $store.cuaderno.summary.items.length === 0">
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><p>No hay movimientos pendientes de guardar.</p><p class="text-sm mt-1">Use los botones "Registrar Altas" o "Registrar Bajas" para comenzar.</p></td></tr>
                </template>
                <template x-if="$store.cuaderno.summary.items.length > 0 && filteredMovements.length === 0">
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><p>No se encontraron movimientos para el día seleccionado.</p></td></tr>
                </template>
            </tbody>
        </table>
    </div>
    <form method="POST" action="{{ route('cuaderno.store') }}" x-ref="saveForm" class="text-center flex justify-center space-x-4 mt-4">
        @csrf
        <input type="hidden" name="movimientos_json" x-model="JSON.stringify($store.cuaderno.summary.items)">
        <input type="hidden" name="upId" :value="selectedUpId">
        <button type="button" @click="cancelChanges()" class="px-6 py-2 bg-gray-200 text-gray-700 font-bold shadow-sm border-2 border-gray-500 hover:bg-gray-300">Limpiar Pendientes</button>
        <button type="submit" :disabled="!$store.cuaderno || $store.cuaderno.summary.items.length === 0" class="px-6 py-2 bg-blue-600 text-white font-bold shadow-sm border-2 border-gray-500 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">Guardar Cambios en Cuaderno</button>
    </form>
</fieldset>
