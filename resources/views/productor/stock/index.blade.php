<x-productor-layout>
    <x-slot name="header_title">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mi Stock de Animales
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h1 class="text-2xl font-medium text-gray-900">
                            Inventario General
                        </h1>
                        
                        <form action="{{ route('productor.stock.index') }}" method="GET" class="flex items-center gap-2">
                            <select name="unidad_productiva_id" class="block w-full md:w-auto border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <option value="">Todas mis chacras</option>
                                @foreach ($unidadesProductivas as $up)
                                    <option value="{{ $up->id }}" {{ request('unidad_productiva_id') == $up->id ? 'selected' : '' }}>
                                        {{ $up->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Filtrar</button>
                        </form>
                    </div>

                    @if($stock->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No se encontró stock para la selección actual.</p>
                            @if(request('unidad_productiva_id'))
                                <a href="{{ route('productor.stock.index') }}" class="mt-2 text-sm text-indigo-600 hover:text-indigo-900">Limpiar filtro</a>
                            @endif
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chacra</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especie</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raza</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($stock as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->unidadProductiva->nombre ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->especie->nombre ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria->nombre ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->raza->nombre ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->cantidad }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $stock->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-productor-layout>
