<fieldset class="border-2 border-gray-400 p-4">
    <legend @click="showTotalStock = !showTotalStock" class="px-2 font-bold text-gray-800 cursor-pointer flex items-center select-none">
        <span>Stock Actual en el Campo</span>
        <p x-show="!showTotalStock" class="ml-4 text-sm text-blue-600 font-semibold">(Clic para expandir/contraer)</p>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-600 transition-transform" :class="{ 'rotate-180': showTotalStock }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
    </legend>
    <div x-show="showTotalStock" x-collapse class="mt-4">
        <div class="overflow-x-auto border-2 border-gray-400">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-300 border-b-2 border-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Especie</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Categoría</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 border-r-2 border-gray-400">Raza</th>
                        <th class="px-4 py-2 text-right text-sm font-bold text-gray-700">Cantidad Actual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-400">
                    <template x-for="stockItem in selectedStock" :key="stockItem.id">
                        <tr class="hover:bg-green-100">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="stockItem.especie.nombre"></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-800 border-r-2 border-gray-400" x-text="stockItem.categoria.nombre"></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 border-r-2 border-gray-400" x-text="stockItem.raza.nombre"></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right font-bold" x-text="stockItem.cantidad_actual"></td>
                        </tr>
                    </template>
                    <template x-if="selectedStock.length === 0">
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500"><p>No hay stock registrado para este campo.</p></td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</fieldset>
