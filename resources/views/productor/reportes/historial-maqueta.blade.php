<x-productor-layout>
    <x-slot name="header_title">
        <h1>
            {{ __('Historial de Movimientos por Período') }}
        </h1>
    </x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            <!-- Filtros -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-3">Filtros de Búsqueda</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">Período de Declaración</label>
                        <select id="periodo" name="periodo" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                            <option value="1">Período Actual (01/07/2025 - 31/12/2025)</option>
                            <option value="2">Período Anterior (01/01/2025 - 30/06/2025)</option>
                            <option value="3">Período 2024 - Semestre 2</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="button" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumen y Exportación -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6 flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-800">Mostrando resultados para:</p>
                    <p class="font-bold text-blue-900">Período Actual (01/07/2025 - 31/12/2025)</p>
                </div>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                    </svg>
                    Exportar a PDF
                </a>
            </div>

            <!-- Tabla de Movimientos -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Motivo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Especie</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Categoría</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Fila de ejemplo 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">15/09/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Entrada
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Nacimiento</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Ovino</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Cordero/a</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">+ 25</td>
                            </tr>
                            <!-- Fila de ejemplo 2 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">12/09/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Salida
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Venta</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Caprino</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Chivito/a</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">- 10</td>
                            </tr>
                            <!-- Fila de ejemplo 3 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">05/09/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Entrada
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Compra</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Ovino</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Oveja</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">+ 5</td>
                            </tr>
                            <!-- Fila de ejemplo 4 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">28/08/2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Salida
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Muerte</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Ovino</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Carnero</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">- 1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginación -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Anterior </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Siguiente </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">1</span>
                                a
                                <span class="font-medium">10</span>
                                de
                                <span class="font-medium">97</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><span>&laquo;</span></a>
                                <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 1 </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 2 </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium"> 3 </a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"> ... </span>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium"> 8 </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 9 </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 10 </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"><span>&raquo;</span></a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-productor-layout>
