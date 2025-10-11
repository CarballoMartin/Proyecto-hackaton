<x-guest-layout>
    <div class="bg-white text-gray-800">
        <!-- Barra de Navegación -->
        @include('layouts.partials.navigation.landing-nav')
        
        <main>
            <!-- Sección Hero -->
            @include('layouts.partials.landing.hero')

            <!-- Sección de Características (Features) -->
            @include('layouts.partials.landing.features-partners')

            <!-- Sección "Sobre Nosotros" (About) -->
            @include('layouts.partials.landing.about')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p>&copy; {{ date('Y') }} Proyecto Ovino-Caprinos. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

        <!-- Panel de Contacto -->
        @include('layouts.partials.landing.contact')
    </div>
</x-guest-layout>
