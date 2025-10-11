<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Cuaderno de Campo - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/cuaderno-de-campo.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased" x-data="loaderManager">
        <!-- Pantalla de Carga -->
        <div id="loader" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/30 backdrop-blur-sm">
            <div class="loader-dots">
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
            </div>
        </div>
        <div x-data="{ summaryExpanded: false, summaryPeeking: false }" class="h-screen flex flex-col bg-gray-100">
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
                {{-- Left Sidebar (Main Navigation) --}}
                <aside @mouseenter="summaryPeeking = false" class="w-64 bg-gray-800 text-white flex flex-col">
                    <nav class="flex-1 px-4 py-4 space-y-6">
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('cuaderno.inicio') }}" 
                                   class="group flex items-center px-3 py-3 text-base font-medium rounded-md {{ request()->routeIs('cuaderno.inicio') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                                    Inicio
                                </a>
                                <a href="{{ route('cuaderno.registro') }}" 
                                   class="group flex items-center px-3 py-3 text-base font-medium rounded-md {{ request()->routeIs('cuaderno.registro') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Registrar Movimientos
                                </a>
                            </div>
                        </div>
                        <div>
                            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Consultas</h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('cuaderno.historial') }}" 
                                   class="group flex items-center px-3 py-3 text-base font-medium rounded-md {{ request()->routeIs('cuaderno.historial') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    Historial
                                </a>
                            </div>
                        </div>
                    </nav>
                </aside>

                {{-- Main Content (Dynamic) --}}
                <main @mouseenter="summaryPeeking = false" class="flex-1 flex flex-col overflow-y-scroll">
                    @yield('cuaderno_content')
                </main>

                @if(false)
                {{-- Hotspot for peeking --}}
                <div @mouseenter="summaryPeeking = true" class="absolute top-0 right-0 h-full w-4 z-20"></div>

                {{-- Right Sidebar Container --}}
                <div class="absolute top-0 right-0 h-full transition-transform duration-300 ease-in-out z-30" :class="{ 'translate-x-0': summaryExpanded, 'translate-x-full': !summaryExpanded && !summaryPeeking, 'translate-x-[420px]': !summaryExpanded && summaryPeeking }" style="width: 450px;">
                    <button @click="summaryExpanded = !summaryExpanded" class="absolute top-1/2 -translate-y-1/2 left-0 -translate-x-full h-24 w-8 bg-gray-800 flex items-center justify-center rounded-l-lg border-2 border-r-0 border-gray-500 hover:bg-gray-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6 text-gray-300 transition-transform duration-300" :class="{ 'transform rotate-180': summaryExpanded }"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <aside class="h-full w-full">
                        @if(request()->routeIs('cuaderno.registro'))
                            @include('productor.cuaderno.partials.resumen-panel')
                        @else
                            <div class="bg-gray-200 p-4 border-2 border-gray-400 flex flex-col h-full items-center justify-center">
                                <p class="text-gray-600 text-center">El panel de resumen solo está disponible en la sección de registro de movimientos.</p>
                            </div>
                        @endif
                    </aside>
                </div>
                @endif
            </div>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
