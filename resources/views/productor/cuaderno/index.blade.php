<div x-data="{ activeView: 'inicio', summaryExpanded: false, summaryPeeking: false }" class="h-screen flex flex-col bg-gray-100">
    {{-- Top Header Banner --}}
    <header class="bg-gray-900 text-white shadow-lg z-20">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <img src="{{ asset('logos/logoovinos.png') }}" alt="Logo Ovinos" class="h-12 w-auto">
                </div>
                <div class="flex items-center">
                    <p class="text-sm">Cierre de ciclo: <span class="font-bold text-orange-400">{{ $tiempoRestante ?? 'N/A' }}</span></p>
                </div>
                <div class="flex items-center">
                    <h1 class="text-lg font-semibold">{{ auth()->user()->name }}</h1>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('productor.panel') }}" class="px-3 py-2 bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 relative overflow-x-hidden">
        {{-- Left Sidebar --}}
        <aside @mouseenter="summaryPeeking = false" class="w-64 bg-gray-800 text-white flex flex-col">
            <nav class="flex-1 px-4 py-4 space-y-6">
                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" @click.prevent="activeView = 'inicio'" :class="{ 'bg-gray-900 text-white': activeView === 'inicio', 'text-gray-300 hover:bg-gray-700 hover:text-white': activeView !== 'inicio' }" class="group flex items-center px-3 py-3 text-base font-medium rounded-md">
                            <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                            Inicio
                        </a>
                        <a href="#" @click.prevent="activeView = 'registro'" :class="{ 'bg-gray-900 text-white': activeView === 'registro', 'text-gray-300 hover:bg-gray-700 hover:text-white': activeView !== 'registro' }" class="group flex items-center px-3 py-3 text-base font-medium rounded-md">
                            <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Registrar Movimientos
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Consultas</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" @click.prevent="activeView = 'declaraciones'" :class="{ 'bg-gray-900 text-white': activeView === 'declaraciones', 'text-gray-300 hover:bg-gray-700 hover:text-white': activeView !== 'declaraciones' }" class="group flex items-center px-3 py-3 text-base font-medium rounded-md">
                            <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                            Declaraciones
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        {{-- Main Content (Dynamic) --}}
        <main @mouseenter="summaryPeeking = false" class="flex-1 flex flex-col overflow-y-auto">
            <div x-show="activeView === 'inicio'" class="h-full">
                @include('productor.cuaderno.partials.inicio-view')
            </div>
            <div x-show="activeView === 'registro'" class="h-full">
                @livewire('productor.cuaderno.registro-movimientos')
            </div>
            <div x-show="activeView === 'declaraciones'" class="p-6">
                <h2 class="text-2xl font-bold">Declaraciones Anteriores</h2>
                <p class="mt-2 text-gray-600">(Próximamente)</p>
            </div>
        </main>

        {{-- Hotspot for peeking --}}
        <div @mouseenter="summaryPeeking = true" class="absolute top-0 right-0 h-full w-4 z-20"></div>

        {{-- Right Sidebar Container --}}
        <div class="absolute top-0 right-0 h-full transition-transform duration-300 ease-in-out z-30" :class="{ 'translate-x-0': summaryExpanded, 'translate-x-full': !summaryExpanded && !summaryPeeking, 'translate-x-[420px]': !summaryExpanded && summaryPeeking }" style="width: 450px;">
            <button @click="summaryExpanded = !summaryExpanded" class="absolute top-1/2 -translate-y-1/2 left-0 -translate-x-full h-24 w-8 bg-gray-800 flex items-center justify-center rounded-l-lg border-2 border-r-0 border-gray-500 hover:bg-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6 text-gray-300 transition-transform duration-300" :class="{ 'transform rotate-180': summaryExpanded }"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            </button>
            <aside class="h-full w-full">
                <div x-show="activeView === 'registro'">
                     @include('productor.cuaderno.partials.resumen-panel')
                </div>
                 <div x-show="activeView !== 'registro'" class="bg-gray-200 p-4 border-2 border-gray-400 flex flex-col h-full items-center justify-center">
                    <p class="text-gray-600">Panel no disponible</p>
                </div>
            </aside>
        </div>
    </div>
</div>
