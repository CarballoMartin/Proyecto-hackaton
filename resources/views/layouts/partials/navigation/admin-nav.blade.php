<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @php
        // Ajustá si tu ruta es distinta
        $isProfileRoute = request()->routeIs('profile.show');
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.panel') }}" class="p-2 rounded-lg shadow-md">
                        <x-application-mark class="block h-11 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ml-10 sm:flex">

                    <!-- Inicio Link -->
                    <x-nav-link href="{{ route('admin.panel') }}" :active="request()->routeIs('admin.panel')">
                        Inicio
                    </x-nav-link>

                    <!-- Productores Dropdown -->
                    <div class="relative flex items-center" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-full {{ request()->routeIs('admin.productores.*') || request()->routeIs('admin.productores.importar') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <span>Productores</span>
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             class="absolute top-full z-50 mt-2 w-56 rounded-md shadow-lg bg-white py-1" x-cloak>
                            <a href="{{ route('admin.productores.panel') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel Productores</a>
                            <div class="border-t border-gray-100"></div>
                            <button type="button" @click="$dispatch('open-productor-form-modal'); open = false;" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registrar Productor</button>
                            <a href="{{ route('admin.productores.importar') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importar Productores</a>
                            <a href="{{ route('admin.productores.listado') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Listar / Modificar</a>
                        </div>
                    </div>

                    <!-- Instituciones Dropdown -->
                    <div class="relative flex items-center" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-full {{ request()->routeIs('admin.instituciones.*') || request()->routeIs('solicitudes.gestionar') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <span>Instituciones</span>
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            class="absolute top-full z-50 mt-2 w-56 rounded-md shadow-lg bg-white py-1" x-cloak>
                            <a href="{{ route('admin.instituciones.panel') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel Instituciones</a>
                            <div class="border-t border-gray-100"></div>
                            <button type="button" @click="$dispatch('open-institucion-modal'); open = false;" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Registrar Institución</button>
                            <a href="{{ route('solicitudes.gestionar') }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gestionar Solicitudes</a>
                        </div>
                    </div>

                    <!-- Geolocalizacion Link -->
                    <x-nav-link href="{{ route('superadmin.mapa') }}" :active="request()->routeIs('superadmin.mapa')">
                        Geolocalizacion
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Link (icono engranaje) -->
                <div class="hidden sm:flex sm:items-center sm:ms-3">
                    <button type="button"
                            @click="$dispatch('open-configuracion-modal')"
                            class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-800 focus:outline-none"
                            title="Configuración">
                        <x-heroicon-o-cog-6-tooth class="h-6 w-6" />
                    </button>
                </div>

                <!-- Notifications -->
                <div class="ms-3 relative">
                    <x-notifications-panel />
                </div>

                @livewire('indicador-proceso')

                <!-- User Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <!-- Avatar -->
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 transition ease-in-out duration-150
                                           {{ $isProfileRoute ? 'bg-gray-100 text-gray-700' : 'bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-100 active:bg-gray-100' }}">
                                    <img class="size-8 rounded-full object-cover"
                                         src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            @else
                                <!-- Nombre -->
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 transition ease-in-out duration-150
                                               {{ $isProfileRoute ? 'bg-gray-100 text-gray-700' : 'bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-100 active:bg-gray-100' }}">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Administrar Cuenta
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                Perfil
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    API Tokens
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="#" @click.prevent="$root.submit();">
                                    Cerrar Sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('admin.panel') }}" :active="request()->routeIs('admin.panel')">
                Inicio
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    Perfil
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        API Tokens
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="#" @click.prevent="$root.submit();">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>