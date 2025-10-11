<x-productor-layout>
    <x-slot name="header_title">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reportes y Descargas
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 lg:p-8 space-y-6">

        <!-- Sección de Reportes de Datos -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-3">Reportes de Datos</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Reporte de Inventario -->
                <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Inventario Completo</h4>
                        <p class="text-sm text-gray-500">Exporta un listado detallado de todo tu stock actual en formato Excel.</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <x-heroicon-o-arrow-down-tray class="h-4 w-4"/>
                    </a>
                </div>
                <!-- Reporte de Movimientos -->
                <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Historial de Movimientos</h4>
                        <p class="text-sm text-gray-500">Exporta un registro de todas las altas y bajas. (Requiere filtro de fecha).</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <x-heroicon-o-arrow-down-tray class="h-4 w-4"/>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sección de Análisis -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-3">Análisis y Documentación</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Análisis de Suelo -->
                <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Análisis de Suelo (Ejemplo)</h4>
                        <p class="text-sm text-gray-500">Resultados del último análisis de suelo para la Chacra 1. (PDF)</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <x-heroicon-o-document-text class="h-4 w-4"/>
                    </a>
                </div>
                 <!-- Guía de Buenas Prácticas -->
                 <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Guía de Buenas Prácticas</h4>
                        <p class="text-sm text-gray-500">Manual de buenas prácticas para la cría ovina y caprina. (PDF)</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <x-heroicon-o-document-text class="h-4 w-4"/>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sección de Plantillas -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-3">Plantillas y Formularios</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Plantilla Sanitaria -->
                <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Plantilla de Registro Sanitario</h4>
                        <p class="text-sm text-gray-500">Documento para llevar un control manual de la sanidad. (Word)</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        <x-heroicon-o-document-duplicate class="h-4 w-4"/>
                    </a>
                </div>
                <!-- Formulario de Carga -->
                <div class="border p-4 rounded-lg flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">Formulario de Carga Masiva</h4>
                        <p class="text-sm text-gray-500">Plantilla Excel para la importación masiva de animales.</p>
                    </div>
                    <a href="#" class="ml-4 inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        <x-heroicon-o-document-duplicate class="h-4 w-4"/>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-productor-layout>
