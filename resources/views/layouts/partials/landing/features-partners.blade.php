<!-- resources/views/partials/landing/features-partners.blade.php -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Una Plataforma Robusta con Respaldo Institucional
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Desarrollado en colaboración con instituciones y productores para garantizar una herramienta útil y
                eficaz.
            </p>
        </div>

        <!-- Carrusel de Logos -->
        <div x-data="{}" x-init="$nextTick(() => {
                    let ul = $refs.logos;
                    ul.insertAdjacentHTML('afterend', ul.outerHTML);
                    ul.nextSibling.setAttribute('aria-hidden', 'true');
                })"
            class="w-full inline-flex flex-nowrap overflow-hidden
      [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)] mt-12">
            <ul x-ref="logos"
                class="flex items-center justify-center md:justify-start [&_li]:mx-8 animate-infinite-scroll">

                <!-- Logo 1: Instituto Tecnológico -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-instituciones.svg') }}" alt="Instituto Tecnológico" class="max-h-full max-w-full
      object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

                <!-- Logo 2: Universidad -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-universidad.svg') }}" alt="Universidad" class="max-h-full
      max-w-full object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

                <!-- Logo 3: Cooperativa -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-cooperativa.svg') }}" alt="Cooperativa" class="max-h-full
      max-w-full object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

                <!-- Logo 4: Ministerio -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-ministerio.svg') }}" alt="Ministerio" class="max-h-full
      max-w-full object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

                <!-- Logo 5: Sistema -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-sistema.svg') }}" alt="Sistema" class="max-h-full
      max-w-full object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

                <!-- Logo 6: Placeholder -->
                <li class="flex justify-center items-center w-40 h-16">
                    <img src="{{ asset('logos/logo-placeholder.svg') }}" alt="Institución" class="max-h-full
      max-w-full object-contain grayscale hover:grayscale-0 transition duration-300" />
                </li>

            </ul>
        </div>

        <!-- Características Clave (versión compacta) -->
        <div class="mt-20 grid gap-10 md:grid-cols-3">
            <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                                        <x-heroicon-o-clock class="h-6 w-6" />
                </div>
                <h3 class="mt-6 text-xl font-bold text-gray-900">Stock en Tiempo Real</h3>
                <p class="mt-4 text-base text-gray-600">Actualiza y consulta tu inventario ganadero y de recursos de
                    forma
                    instantánea.</p>
            </div>
            <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                                        <x-heroicon-o-chart-bar class="h-6 w-6" />
                </div>
                <h3 class="mt-6 text-xl font-bold text-gray-900">Informes y Estadísticas</h3>
                <p class="mt-4 text-base text-gray-600">Genera reportes y visualiza datos clave para una toma de
                    decisiones
                    informada.</p>
            </div>
            <div class="text-center">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                                        <x-heroicon-o-map-pin class="h-6 w-6" />
                </div>
                <h3 class="mt-6 text-xl font-bold text-gray-900">Mapa Georreferenciado</h3>
                <p class="mt-4 text-base text-gray-600">Analiza la distribución de recursos y campos en un mapa
                    interactivo.</p>
            </div>
        </div>
    </div>
</section>