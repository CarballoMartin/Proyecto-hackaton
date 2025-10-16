<!-- resources/views/partials/landing/hero.blade.php -->
<section class="relative bg-gray-900 text-white">
    <!-- Imagen de Fondo con Overlay Oscuro -->
    <div class="absolute inset-0">
        <img src="{{ asset('imagenes-campos/pastos.jpeg') }}" alt="Paisaje ganadero" class="w-full h-full
      object-cover">
        <div class="absolute inset-0 bg-black opacity-50"></div>
    </div>

    <!-- Contenido del Hero -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            <span class="block">Gestión Inteligente para la</span>
            <span class="block text-indigo-400">Gestión Ganadera</span>
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-lg text-gray-300">
            La plataforma digital que conecta a productores, técnicos e instituciones para un futuro más productivo y
            sostenible.
        </p>

        <!-- Tríptico de Tarjetas Interactivas -->
        <div class="mt-12 grid gap-8 md:grid-cols-3">
            <!-- Tarjeta 1: Productores -->
            <div class="group relative p-8 border border-white/20 rounded-xl bg-white/10 backdrop-blur-sm transition duration-300
      ease-in-out hover:border-white/50 hover:-translate-y-2">
                <div class="flex flex-col items-center text-center">
                    <!-- Icono -->
                    <div class="mb-4">
                                                <x-heroicon-o-building-storefront class="h-12 w-12 text-indigo-300" />
                    </div>
                    <h3 class="text-xl font-bold text-white">Productores</h3>
                    <p class="mt-2 text-gray-300">Tu gestión, simplificada.</p>
                    <!-- Texto que aparece al hacer hover -->
                    <p class="mt-4 text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Actualiza tu stock, registra tus campos y accede a tu historial de datos de forma rápida y
                        segura.
                    </p>
                </div>
            </div>

            <!-- Tarjeta 2: Instituciones -->
            <div class="group relative p-8 border border-white/20 rounded-xl bg-white/10 backdrop-blur-sm transition duration-300
      ease-in-out hover:border-white/50 hover:-translate-y-2">
                <div class="flex flex-col items-center text-center">
                    <!-- Icono -->
                    <div class="mb-4">
                                                <x-heroicon-o-building-library class="h-12 w-12 text-indigo-300" />
                    </div>
                    <h3 class="text-xl font-bold text-white">Instituciones y Técnicos</h3>
                    <p class="mt-2 text-gray-300">Datos para decidir mejor.</p>
                    <p class="mt-4 text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Accede a información consolidada, genera reportes y utiliza el mapa interactivo para un análisis
                        territorial preciso.
                    </p>
                </div>
            </div>

            <!-- Tarjeta 3: Plataforma -->
            <div class="group relative p-8 border border-white/20 rounded-xl bg-white/10 backdrop-blur-sm transition duration-300
      ease-in-out hover:border-white/50 hover:-translate-y-2">
                <div class="flex flex-col items-center text-center">
                    <!-- Icono -->
                    <div class="mb-4">
                                                <x-heroicon-o-globe-alt class="h-12 w-12 text-indigo-300" />
                    </div>
                    <h3 class="text-xl font-bold text-white">Plataforma Colaborativa</h3>
                    <p class="mt-2 text-gray-300">Conectando productores.</p>
                    <p class="mt-4 text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Un ecosistema digital seguro que centraliza la información y fomenta la colaboración para el
                        desarrollo
                        sostenible.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>