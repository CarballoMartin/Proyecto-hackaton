<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ url('/') }}" class="flex items-center space-x-2 p-2 rounded-lg shadow-md">
                    <x-logo class="h-10 w-10" />
                    <span class="font-bold text-xl text-gray-700">Campo Verde</span>
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-base font-medium text-gray-500 hover:text-gray-900">Características</a>
                <a href="{{ route('cuenca-misiones') }}" class="text-base font-medium text-gray-500 hover:text-gray-900">Acerca de</a>
                                <button @click="contactModalOpen = !contactModalOpen" class="text-base font-medium text-gray-500 hover:text-gray-900">Contacto</button>
            </div>

            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                    Iniciar Sesión
                </a>

                <a href="{{ route('registro.institucional') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <x-heroicon-s-lock-closed class="h-5 w-5 mr-2" />
                    Solicitar Acceso Institucional
                </a>
            </div>

            <!-- Menú Hamburguesa para móviles -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                </button>

                <!-- Menú desplegable móvil -->
                <div x-show="mobileMenuOpen" x-cloak @click.away="mobileMenuOpen = false" class="absolute top-full right-4 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 p-4 space-y-2">
                    <a href="#features" class="block text-gray-700">Características</a>
                    <a href="{{ route('cuenca-misiones') }}" class="block text-gray-700">Acerca de</a>
                                        <button @click="contactModalOpen = !contactModalOpen; mobileMenuOpen = false" class="block text-gray-700 w-full text-left">Contacto</button>
                    <a href="{{ route('login') }}" class="block text-indigo-600 font-medium">Iniciar Sesión</a>
                    <a href="{{ route('registro.institucional') }}" class="block text-white bg-indigo-600 px-3 py-2 rounded-md">Solicitar Acceso</a>
                </div>
            </div>
        </div>
    </div>
</nav>
