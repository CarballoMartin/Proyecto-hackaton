<div class="h-screen flex flex-col">
        <div x-data="{ sidebarOpen: true, notificationsOpen: false }" class="flex-grow relative flex md:flex-row overflow-hidden">
            
            <!-- Sidebar -->
            <aside :class="{ '-translate-x-full': !sidebarOpen }" class="z-30 bg-white w-64 min-h-full absolute inset-y-0 left-0 transform md:relative md:translate-x-0 transition-transform duration-200 ease-in-out shadow-md">
                <div class="flex justify-between items-center h-16 px-4 border-b">
                    <a href="#" class="flex items-center space-x-2">
                        <x-logo class="h-10 w-10" />
                        <span class="font-bold text-xl text-gray-700">Gestión</span>
                    </a>
                    <button @click="sidebarOpen = false" class="md:hidden text-gray-600 hover:text-gray-800">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>
                <div class="py-4 px-2">
                    {{ $sidebar ?? '' }}
                </div>
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden">
                
                <!-- Top-bar -->
                <header class="flex-shrink-0 bg-white shadow-sm z-10">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                        <div class="flex items-center">
                            <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                                <x-heroicon-o-bars-3 class="h-6 w-6" />
                            </button>
                            <h1 class="text-lg font-semibold text-gray-800 ml-2 md:ml-0">
                                {{ $header_title ?? '' }}
                            </h1>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Ecoganadería Button -->
                            <button @click="$dispatch('open-ecoganaderia-modal')" 
                                    class="relative p-2 rounded-full text-green-500 hover:text-white hover:bg-green-500 focus:outline-none transition-all duration-300 group"
                                    title="Ecoganadería - Prácticas Sustentables">
                                <x-heroicon-o-globe-americas class="h-6 w-6" />
                                <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-green-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-50">
                                    Ecoganadería
                                </span>
                            </button>
                            
                            <!-- Notifications Bell -->
                            <button @click="notificationsOpen = true" class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
                                <x-heroicon-o-bell class="h-6 w-6" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">3</span>
                            </button>

                            <!-- User Dropdown -->
                            <div class="ms-3 relative">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                            </button>
                                        @else
                                            <span class="inline-flex rounded-md">
                                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    {{ Auth::user()->name }}
                                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        @endif
                                    </x-slot>

                                    <x-slot name="content">
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Manage Account') }}
                                        </div>

                                        <x-dropdown-link href="{{ route('profile.show') }}">
                                            {{ __('Profile') }}
                                        </x-dropdown-link>

                                        <div class="border-t border-gray-200"></div>

                                        <!-- Authentication -->
                                        <livewire:institucional.logout-button />
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-100">
                    {{ $slot }}
                </main>
            </div>

            <!-- Notifications Panel -->
            <div x-show="notificationsOpen" class="fixed inset-0 z-40" x-cloak>
                <div @click="notificationsOpen = false" class="absolute inset-0 bg-gray-600 bg-opacity-50"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-50 ml-auto h-full transform transition-transform ease-in-out duration-300" x-show="notificationsOpen" x-transition:enter="transform translate-x-0" x-transition:enter-start="transform translate-x-full" x-transition:enter-end="transform translate-x-0" x-transition:leave="transform translate-x-full" x-transition:leave-start="transform translate-x-0" x-transition:leave-end="transform translate-x-full">
                    <div class="absolute top-0 left-0 -ml-12 pt-2">
                        <button @click="notificationsOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <x-heroicon-o-x-mark class="h-6 w-6 text-white" />
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="px-4">
                            <h3 class="text-lg font-semibold text-gray-800">Notificaciones</h3>
                        </div>
                        <div class="mt-4 px-4 space-y-4">
                            <div class="p-3 bg-white rounded-md shadow-sm border border-gray-200"><p class="text-sm font-medium text-gray-800">Nueva declaración jurada requerida.</p><p class="text-xs text-gray-500 mt-1">Hace 2 horas</p></div>
                            <div class="p-3 bg-white rounded-md shadow-sm border border-gray-200"><p class="text-sm font-medium text-gray-800">Se ha actualizado el precio de la lana.</p><p class="text-xs text-gray-500 mt-1">Hace 1 día</p></div>
                            <div class="p-3 bg-white rounded-md shadow-sm border border-gray-200"><p class="text-sm font-medium text-gray-800">Recordatorio: Vacunación anual la próxima semana.</p><p class="text-xs text-gray-500 mt-1">Hace 3 días</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex-shrink-0 bg-white border-t border-gray-200 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="text-sm text-gray-500">
                        <a href="mailto:soporte@sistema-ganadero.test" class="hover:text-indigo-600">soporte@sistema-ganadero.test</a>
                    </div>
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} Proyecto Ovino-Caprinos.
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <x-heroicon-o-phone class="h-4 w-4" />
                        <span>Soporte: 3755571080</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
