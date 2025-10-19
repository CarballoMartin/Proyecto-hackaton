<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acerca del Sistema - Gestión Ganadera</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    {{-- Hero Section --}}
    <div class="relative h-96 flex items-center justify-center bg-cover bg-center" style="background-image: url('{{ asset('imagenes-campos/chacra1.jpeg') }}');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative container mx-auto px-6 z-10">
            <div class="flex items-center justify-center space-x-8 md:space-x-12">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-white">Sistema de Gestión Ganadera</h1>
                    <p class="text-xl md:text-2xl text-gray-200 mt-4">Plataforma Integral para Productores e Instituciones</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Navigation --}}
    <div class="sticky top-0 bg-indigo-700 shadow-md z-50">
        <div class="container mx-auto px-6 py-3">
            <nav class="flex flex-col items-center space-y-2 md:flex-row md:space-y-0 md:space-x-8 text-gray-100 py-2">
                <a href="#vision-mision" class="hover:text-white hover:underline">Visión y Misión</a>
                <a href="#caracteristicas" class="hover:text-white hover:underline">Características</a>
                <a href="#tecnologias" class="hover:text-white hover:underline">Tecnologías</a>
                <a href="{{ url('/') }}" class="hover:text-white hover:underline">Volver al Inicio</a>
            </nav>
        </div>
    </div>

    {{-- Vision y Mision Section --}}
    <section id="vision-mision" class="pt-16 md:pt-24 pb-12 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Misión y Visión</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <div class="bg-indigo-50 p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-indigo-700 mb-4">Nuestra Misión</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Trabajar en relación directa con productores ganaderos y comunidades para el desarrollo 
                        territorial a través de herramientas digitales que faciliten la gestión, el registro y la 
                        toma de decisiones informadas en la producción ovina y caprina.
                    </p>
                </div>
                
                <div class="bg-green-50 p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-green-700 mb-4">Nuestra Visión</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Constituir una plataforma interinstitucional e interdisciplinaria para la implementación 
                        coordinada de políticas públicas, gestionando y ejecutando acciones para el desarrollo de 
                        la producción primaria, el agregado de valor y el desarrollo rural sustentable.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Características Section --}}
    <section id="caracteristicas" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Características del Sistema</h2>
                <p class="text-lg text-gray-600 mt-4">Una plataforma completa para la gestión integral</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-indigo-600 text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Cuaderno de Campo Digital</h3>
                    <p class="text-gray-600">
                        Registra todos los movimientos de tu ganado de forma digital, con historial completo 
                        y exportación a PDF.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-green-600 text-4xl mb-4">📈</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Estadísticas en Tiempo Real</h3>
                    <p class="text-gray-600">
                        Visualiza el estado de tu producción con gráficos interactivos y reportes automáticos.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-blue-600 text-4xl mb-4">🌍</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Gestión Geolocalizada</h3>
                    <p class="text-gray-600">
                        Administra múltiples unidades productivas con ubicación en mapas interactivos.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-purple-600 text-4xl mb-4">👥</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Multi-Rol</h3>
                    <p class="text-gray-600">
                        Acceso diferenciado para productores, instituciones y administradores.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-orange-600 text-4xl mb-4">🌤️</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Clima Integrado</h3>
                    <p class="text-gray-600">
                        Información meteorológica actualizada para la planificación de actividades.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-red-600 text-4xl mb-4">📱</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Acceso Móvil</h3>
                    <p class="text-gray-600">
                        API lista para aplicación móvil con acceso desde cualquier lugar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tecnologías Section --}}
    <section id="tecnologias" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Tecnologías Utilizadas</h2>
                <p class="text-lg text-gray-600 mt-4">Construido con las mejores herramientas del mercado</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">Laravel 12</div>
                    <p class="text-sm text-gray-600">Framework PHP</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">Livewire 3</div>
                    <p class="text-sm text-gray-600">Componentes Reactivos</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">Tailwind CSS</div>
                    <p class="text-sm text-gray-600">Framework CSS</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">Chart.js</div>
                    <p class="text-sm text-gray-600">Gráficos Interactivos</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-gray-400 py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} Sistema de Gestión Ganadera</p>
            <a href="mailto:soporte@sistema-ganadero.test" class="hover:text-white hover:underline">soporte@sistema-ganadero.test</a>
        </div>
    </footer>

</body>

</html>











