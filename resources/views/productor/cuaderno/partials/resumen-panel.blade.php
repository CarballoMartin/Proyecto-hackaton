<div x-data="{}" class="bg-gray-200 p-4 border-2 border-gray-400 flex flex-col h-full">
    <h2 class="text-xl font-bold mb-4 text-center">Resumen de Movimientos</h2>
    <div x-show="!$store.cuaderno || $store.cuaderno.summary.items.length === 0" class="flex-grow flex items-center justify-center">
        <div class="text-center text-gray-600">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" /></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-800">Resumen vacío</h3>
            <p class="mt-1 text-sm text-gray-600">Los movimientos que añada aparecerán aquí.</p>
        </div>
    </div>
    <div x-show="$store.cuaderno && $store.cuaderno.summary.items.length > 0" class="flex-grow overflow-y-auto bg-white border-2 border-gray-400 p-1" style="display: none;">
        <template x-for="(item, index) in $store.cuaderno.summary.items" :key="index">
            <div class="flex items-center w-full text-sm py-1 border-b border-gray-300 last:border-b-0">
                <span class="block w-1 h-4 mr-2" :class="item.tipo === 'altas' ? 'bg-green-500' : 'bg-red-500'"></span>
                <span class="w-14 text-center font-semibold" :class="item.tipo === 'altas' ? 'text-green-700' : 'text-red-700'" x-text="item.tipo.toUpperCase()"></span>
                <span class="flex-grow px-2 truncate text-gray-700" :title="`${item.especie_nombre} - ${item.categoria_nombre} (${item.raza_nombre})`" x-text="`${item.especie_nombre} - ${item.categoria_nombre} (${item.raza_nombre})`"></span>
                <span class="w-12 text-right font-bold text-gray-800 pr-2" x-text="item.cantidad"></span>
                <button @click="$store.cuaderno.removeMovementFromSummary(index)" class="w-6 text-center text-gray-500 hover:text-red-700 font-bold text-lg">&times;</button>
            </div>
        </template>
    </div>
    <div x-show="$store.cuaderno && $store.cuaderno.summary.items.length > 0" class="mt-auto pt-2 border-t-2 border-gray-400" style="display: none;">
        <button @click="$store.cuaderno.clearSummary()" class="w-full text-sm text-center text-gray-600 hover:text-red-700 hover:font-semibold">Limpiar Resumen</button>
    </div>
</div>
