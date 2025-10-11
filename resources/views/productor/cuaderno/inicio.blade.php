@extends('layouts.cuaderno')

@section('cuaderno_content')
    <!-- Marquee/Carousel -->
    <div x-data="{
            items: [
                { text: 'Nacimientos esta semana:', value: '12' },
                { text: 'Ventas realizadas:', value: '5' },
                { text: 'Animales en tratamiento:', value: '3' },
                { text: 'Próximo cierre de ciclo:', value: '15 días' },
                { text: 'Recordatorio:', value: 'Vacunar contra la aftosa' }
            ]
        }"
         class="w-full bg-white shadow-md flex flex-nowrap overflow-hidden">
        
        <div class="animate-scroll-and-repeat flex-shrink-0 flex items-center h-12">
            <template x-for="item in items" :key="item.text">
                <div class="flex items-center mx-6 whitespace-nowrap">
                    <span class="text-sm font-semibold text-gray-600" x-text="item.text"></span>
                    <span class="ml-2 text-sm font-bold text-indigo-600" x-text="item.value"></span>
                    <span class="mx-6 text-gray-300">|</span>
                </div>
            </template>
        </div>

    </div>

    <!-- Main content grid -->
    <div class="p-6 flex-grow">
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card 1: Help -->
            <div class="bg-gray-200 border-2 border-gray-400 p-1 hover:border-gray-500 hover:shadow-2xl transition-all duration-300">
                <fieldset class="border-2 border-gray-400 p-4 h-full flex flex-col">
                    <legend class="px-2 font-bold text-gray-800">Aprender a Usar el Cuaderno</legend>
                    <div class="flex-grow flex flex-col items-center justify-center text-center">
                        <x-heroicon-o-question-mark-circle class="h-12 w-12 text-gray-500 mb-2" />
                        <p class="text-gray-600 text-sm">Guías y tutoriales para dominar la herramienta.</p>
                        <span class="mt-4 text-xs font-semibold text-red-600">(Próximamente)</span>
                    </div>
                    <div class="mt-4 text-center">
                        <button disabled class="w-full px-4 py-1 bg-gray-300 border-2 border-gray-500 font-semibold text-gray-500 cursor-not-allowed">Empezar a aprender</button>
                    </div>
                </fieldset>
            </div>

            <!-- Card 2: Good Practices -->
            <div class="bg-gray-200 border-2 border-gray-400 p-1 hover:border-gray-500 hover:shadow-2xl transition-all duration-300">
                <fieldset class="border-2 border-gray-400 p-4 h-full flex flex-col">
                    <legend class="px-2 font-bold text-gray-800">Guías de Buenas Prácticas</legend>
                    <div class="flex-grow flex flex-col items-center justify-center text-center">
                        <x-heroicon-o-book-open class="h-12 w-12 text-gray-500 mb-2" />
                        <p class="text-gray-600 text-sm">Consejos sobre manejo, sanidad y normativas vigentes.</p>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="w-full px-4 py-1 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold">Explorar Guías</button>
                    </div>
                </fieldset>
            </div>

            <!-- Card 3: Additional Info -->
            <div class="bg-gray-200 border-2 border-gray-400 p-1 hover:border-gray-500 hover:shadow-2xl transition-all duration-300">
                <fieldset class="border-2 border-gray-400 p-4 h-full flex flex-col">
                    <legend class="px-2 font-bold text-gray-800">Información y Recursos</legend>
                    <div class="flex-grow flex flex-col items-center justify-center text-center">
                        <x-heroicon-o-information-circle class="h-12 w-12 text-gray-500 mb-2" />
                        <p class="text-gray-600 text-sm">Enlaces de interés, contactos y más información útil.</p>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="w-full px-4 py-1 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold">Ver Recursos</button>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
