<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>La Cuenca - Gestión Ovino-Caprina</title>

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
                <div class="bg-white p-4 rounded-full shadow-lg flex-shrink-0">
                    <img src="{{ asset('logos/logoovinos.png') }}" alt="Logo Cuenca" class="h-32 w-32 object-contain">
                </div>
                <div class="text-left">
                    <h1 class="text-4xl md:text-5xl font-bold text-white">Mesa de Gestión de la Cuenca Ovino-Caprina</h1>
                    <p class="text-xl md:text-2xl text-gray-200 mt-4">Mesa de Gestión
                        Interinstitucional e Intersectorial. </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Navigation --}}
    <div class="sticky top-0 bg-[#8C2218] shadow-md z-50">
        <div class="container mx-auto px-6 py-3">
            <nav class="flex flex-col items-center space-y-2 md:flex-row md:space-y-0 md:space-x-8 text-gray-100 py-2">
                <a href="#territorio" class="hover:text-white hover:underline">El Territorio</a>
                <a href="#gestion" class="hover:text-white hover:underline">Gestión</a>
                <a href="#produccion" class="hover:text-white hover:underline">Producción</a>
                <a href="#ambiente" class="hover:text-white hover:underline">Medio Ambiente</a>
                <a href="#publicaciones" class="hover:text-white hover:underline">Más</a>
            </nav>
        </div>
    </div>

    {{-- Vision y Mision Section --}}
    <section id="vision-mision" class="pt-16 md:pt-24 pb-12 bg-white">
        <div class="container mx-auto px-6">

            <!-- Introducción Quienes Somos -->
            <!-- Encabezado aislado en franja bordo -->
            <div class="bg-[#8C2218] text-white py-12 px-6 rounded-lg shadow-lg mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-center">Quiénes Somos</h2>
                <p class="mt-4 text-lg max-w-3xl mx-auto text-center">
                    La Mesa de Gestión de la Cuenca Ovino Caprina de la Zona Sur de Misiones es una organización interinstitucional e intersectorial...
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Columna de Video (con padding-top trick para responsive) -->
                <div class="relative rounded-lg overflow-hidden shadow-2xl" style="padding-top: 56.25%;">
                    <iframe
                        src="https://www.youtube.com/embed/SFjmLh5l5yY"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="absolute top-0 left-0 w-full h-full">
                    </iframe>
                </div>

                <!-- Columna de Texto -->
                <div class="space-y-8">
                    <div>
                        <h3 class="text-3xl font-semibold text-[#8C2218] mb-3">Misión</h3>
                        <p class="text-gray-700 leading-relaxed border-l-4 border-[#8C2218] pl-6">
                            Ser un equipo interdisciplinario que, junto a los productores, impulse el desarrollo territorial sostenible. Buscamos crear un polo productivo, socio-cultural y turístico que mejore la calidad de vida, preserve la cultura local y proteja el medio ambiente a través de un modelo de Gobernanza Multinivel<span class="font-bold">*</span>.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-semibold text-[#8C2218] mb-3">Visión</h3>
                        <p class="text-gray-700 leading-relaxed border-l-4 border-[#8C2218] pl-6">
                            Consolidarnos como un equipo de referencia en la gestión de políticas públicas para el desarrollo de la producción primaria, el valor agregado y el turismo rural, con el fin de potenciar el desarrollo socioeconómico de los productores y comunidades de la Zona Sur de Misiones.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Nota Aclaratoria Interactiva (estilo para fondo claro) -->
            <div x-data="{ open: false }" class="mt-16 text-center">
                <button @click="open = !open" class="text-gray-600 hover:text-gray-900 hover:underline focus:outline-none">
                    <span x-show="!open">Leer más sobre Gobernanza Multinivel*</span>
                    <span x-show="open">Ocultar explicación</span>
                </button>

                <div x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4"
                    class="mt-4 text-sm text-gray-600 italic max-w-4xl mx-auto bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p>
                        <strong>*Gobernanza Multinivel:</strong> El enfoque de la llamada gobernanza multinivel, que responde a un modelo en el que los Estados (Nacional, Provincial y Local o municipal), las organizaciones, las autoridades regionales y locales, así como otros actores, tanto públicos como privados, busca la interacción de manera cooperativa en un escenario complejo que incluye diferentes niveles territoriales (compatibilizando la planificación provincial y la municipal, en una "Territorial". Se trata básicamente de un sistema referido a la existencia e interdependencia de múltiples gobiernos cuyas competencias se solapan entre los distintos niveles territoriales. Es un proceso político en el que predominan las prácticas de negociación y cooperación entre los distintos actores que actúan en los diversos niveles, aunque no todos disponen del mismo poder ni de la misma capacidad de influencia, ésta forma y método posibilita no caer en inequidades y en confluir esfuerzos en una prospectiva territorial unificada y pasible de una permanente revisión y modificación (Adaptado de "Gobernanza Multinivel en Europa. Una aproximación desde el caso Andaluz" April 2009, Publisher: Fundación Pública Andaluza Centro de Estudios Andaluces, ISBN: ISNN: 1699-8294)
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Divisor -->
    <div class="container mx-auto px-6">
        <hr class="border-t border-gray-200">
    </div>

    {{-- Accordion Sections --}}
    <div class="w-full">

        <!-- Section: Territorio -->
        <section id="territorio" x-data="{ open: false }" class="w-full bg-gray-50 border-b border-gray-200">
            <div @click="open = !open"
                class="cursor-pointer group py-12 relative overflow-hidden transition-all duration-300 hover:bg-gray-200">
                

                <!-- Contenido -->
                <div class="container mx-auto text-center relative z-10">
                    <h2 class="text-3xl font-bold text-gray-900 inline-block border-b-2 border-[#8C2218] pb-2">El Territorio</h2>
                    <p class="text-lg text-gray-700 mt-2">Descripción geográfica y social de la cuenca.</p>
                    <div class="flex justify-center mt-4">
                        <svg class="w-8 h-8 text-gray-700 transform transition-all duration-300 opacity-0 group-hover:opacity-100"
                            :class="{ 'rotate-180': open, 'opacity-100': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Contenido desplegable -->
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
                class="overflow-hidden">

                <div class="container mx-auto p-6 pt-0 space-y-10">

                    <!-- Intro -->
                    <div class="space-y-4 text-gray-800 leading-relaxed">
                        <p>
                            La <strong>Cuenca Ovina del Sur de la Provincia de Misiones</strong> comprende los municipios de
                            <em>Cerro Corá, Profundidad, Fachinal, San José, Garupá y Candelaria</em>.
                            Se caracteriza por una importante población rural compuesta por productores familiares,
                            quienes en la mayoría de los casos realizan la cría de ovinos y caprinos como actividad principal.
                        </p>
                        <p>
                            La producción ovino-caprina constituye el denominador común de los sistemas productivos locales,
                            generando numerosos puestos de trabajo —permanentes y transitorios— que favorecen la calidad de vida
                            y el arraigo en el territorio.
                        </p>
                        <p>
                            Este territorio concentra el <strong>65% del stock total provincial</strong> en una extensión de
                            <strong>116.859 has</strong>, con una tradición cultural de la cría de ovinos y caprinos transmitida
                            por generaciones como patrimonio propio de los agricultores familiares.
                        </p>
                    </div>

                    <!-- Datos destacados -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-center">
                        <div class="bg-[#8C2218] text-white p-6 rounded-lg shadow">
                            <span class="text-3xl font-bold">6</span>
                            <p class="mt-2">Municipios</p>
                        </div>
                        <div class="bg-gray-100 p-6 rounded-lg shadow">
                            <span class="text-3xl font-bold text-gray-900">333+</span>
                            <p class="mt-2 text-gray-700">Familias Rurales</p>
                        </div>
                        <div class="bg-gray-100 p-6 rounded-lg shadow">
                            <span class="text-3xl font-bold text-gray-900">170+</span>
                            <p class="mt-2 text-gray-700">Emprendimientos</p>
                        </div>
                    </div>

                    <!-- Desarrollo -->
                    <div class="space-y-4 text-gray-800 leading-relaxed">
                        <p>
                            El territorio de “La Cuenca” involucra directa e indirectamente un universo de
                            <strong>333 familias rurales</strong> y aproximadamente <strong>170 emprendimientos agropecuarios</strong>,
                            con procesos de mejora ya iniciados desde el año 2016.
                        </p>
                        <p>
                            Estas acciones se apoyan en políticas públicas y metodologías participativas que buscan
                            agregar valor al <strong>Proceso de Desarrollo Territorial</strong>, contemplando el
                            crecimiento del stock, la mejora sanitaria, la calidad genética y, sobre todo,
                            el compromiso del productor, su familia y las instituciones.
                        </p>
                    </div>

                    <!-- Mesa de gestión -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow">
                        <h3 class="font-bold text-2xl text-gray-900 mb-4">Mesa de Gestión</h3>
                        <p class="text-gray-700 leading-relaxed">
                            La Mesa de Gestión de la Cuenca Ovino Caprina de la Zona Sur de Misiones es una iniciativa
                            de instituciones nacionales, provinciales, locales, productores y organizaciones civiles.
                            Busca impulsar un <strong>Proceso de Desarrollo Territorial</strong> orientado al productor familiar
                            y capitalizado, promoviendo valores como compromiso, integración, organización, conocimiento
                            y trabajo, junto con la aplicación de tecnologías apropiadas para lograr la sustentabilidad.
                        </p>
                    </div>

                    <!-- Objetivo primario -->
                    <div class="bg-[#8C2218] text-white p-6 rounded-lg shadow">
                        <h3 class="font-bold text-2xl mb-4">Objetivo Primario</h3>
                        <p>
                            Generar un ámbito de participación, análisis, gestión y planificación,
                            que permita la complementación eficiente de los recursos de las instituciones participantes
                            para el logro eficaz de las políticas públicas.
                        </p>
                    </div>

                    <!-- Lista de objetivos -->
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-bold text-xl text-gray-900 mb-2">Líneas Estratégicas</h4>
                            <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                                <li>Generar y consolidar una estructura innovadora de gobernanza.</li>
                                <li>Consolidar los equipos de trabajo.</li>
                                <li>Desarrollar estructuras operativas funcionales.</li>
                                <li>Generar planificación operativa integrada.</li>
                                <li>Fortalecer compromisos entre productores y entes gubernamentales.</li>
                                <li>Mantener una base de datos dinámica de infraestructura y producción.</li>
                                <li>Fomentar la certificación de origen de los productos.</li>
                                <li>Definir sistemas de comunicación eficientes.</li>
                                <li>Consolidar grupos de productores ovinos y caprinos.</li>
                                <li>Promover la industrialización de productos.</li>
                                <li>Impulsar circuitos y canales comerciales.</li>
                            </ol>
                        </div>
                        <div>
                            <h4 class="font-bold text-xl text-gray-900 mb-2">Objetivos Específicos</h4>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700">
                                <li>Promover la participación de todos los actores vinculados a la producción ovina.</li>
                                <li>Diseñar estrategias para una red multisectorial e interinstitucional.</li>
                                <li>Articular acciones coordinadas entre los actores de la producción ovina.</li>
                                <li>Realizar un relevamiento permanente de las condiciones productivas de la micro-región.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Participantes -->
                    <div class="bg-gray-100 p-6 rounded-lg shadow">
                        <h4 class="font-bold text-xl text-gray-900 mb-2">Participantes</h4>
                        <p class="text-gray-700">Silvia Estigarribia, Javier Buscaglia, Susana Rodríguez</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section: Gestion -->
        <section id="gestion" x-data="{ open: false }" class="w-full bg-white border-b border-gray-200">
            <div @click="open = !open" class="cursor-pointer group py-12 relative overflow-hidden transition-all duration-300 hover:bg-gray-200">
                
                <!-- Contenido -->
                <div class="container mx-auto text-center relative z-10">
                    <h2 class="text-3xl font-bold text-gray-800 inline-block border-b-2 border-[#8C2218] pb-2">Comisión de Gestión</h2>
                    <p class="text-lg text-gray-600 mt-2">Actores y entidades que participan en la mesa.</p>
                    <div class="flex justify-center mt-4">
                        <svg class="w-8 h-8 text-gray-500 transform transition-all duration-300 opacity-0 group-hover:opacity-100" :class="{ 'rotate-180': open, 'opacity-100': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                <div class="container mx-auto p-6 pt-0">
                    <p class="text-gray-700 text-lg max-w-3xl mx-auto text-center">Aquí va el contenido detallado sobre la comisión de gestión y el consejo asesor.</p>
                </div>
            </div>
        </section>

        <!-- Section: Produccion -->
        <section id="produccion" x-data="{ open: false }" class="w-full bg-gray-50 border-b border-gray-200">
            <div @click="open = !open" class="cursor-pointer group py-12 relative overflow-hidden transition-all duration-300 hover:bg-gray-200">
                
                <div class="container mx-auto text-center relative z-10">
                    <h2 class="text-3xl font-bold text-gray-800 inline-block border-b-2 border-[#8C2218] pb-2">Producción y Comercialización</h2>
                    <p class="text-lg text-gray-600 mt-2">Detalles sobre la cadena de valor y procesos productivos.</p>
                    <div class="flex justify-center mt-4">
                        <svg class="w-8 h-8 text-gray-500 transform transition-all duration-300 opacity-0 group-hover:opacity-100" :class="{ 'rotate-180': open, 'opacity-100': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
                class="overflow-hidden bg-white">
                <div class="container mx-auto p-6">

                    <!-- Grid: 1 columna en mobile, 2 columnas en lg -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- Columna izquierda: Producción Primaria -->
                        <div class="rounded-lg shadow-2xl overflow-hidden border border-gray-200 flex flex-col">
                            <!-- Header -->
                            <div class="p-4 bg-[#8C2218]">
                                <h3 class="text-2xl font-bold text-white text-center">Producción Primaria</h3>
                            </div>
                            <!-- Content -->
                            <div class="relative p-6 bg-cover bg-center flex-grow" style="background-image: url('{{ asset('imagenes-campos/animales1.jpeg') }}');">
                                <div class="absolute inset-0 bg-black opacity-60 backdrop-blur-sm"></div>
                                <div class="relative z-10 text-gray-200 leading-relaxed">
                                    <p>
                                        El concepto de actividad primaria en actividades agrícolas, ganaderas y avícolas es importante porque según la Ley 1333 de 1986, Articulo 259, subsiste para los departamentos y municipios la prohibición de “imponer gravámenes de ninguna clase o denominación a la producción primaria, agrícola, ganadera y avícola, sin que se incluyan en esta prohibición las fábricas de productos alimenticios o toda industria donde haya un proceso de transformación por elemental que éste sea.”
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Valor Agregado y Comercialización -->
                        <div class="rounded-lg shadow-2xl overflow-hidden border border-gray-200 flex flex-col">
                            <!-- Header -->
                            <div class="p-4 bg-[#8C2218]">
                                <h3 class="text-2xl font-bold text-white text-center">Valor Agregado y Comercialización</h3>
                            </div>
                            <!-- Content -->
                            <div class="relative p-6 bg-cover bg-center flex-grow" style="background-image: url('{{ asset('imagenes-campos/trabajador.jpeg') }}');">
                                <div class="absolute inset-0 bg-black opacity-70 backdrop-blur-sm"></div>
                                <div class="relative z-10 space-y-4 h-full flex flex-col">
                                    <blockquote class="border-l-4 border-gray-300 pl-4 italic">
                                        <h4 class="font-semibold text-xl text-white not-italic mb-2">Comisión de Agregado de Valor</h4>
                                        <p class="text-gray-200">
                                            Compartir criterios de producto, mercado, oportunidad y logística necesaria y suficiente para el mejor logro de las metas económicas del productor. Generar intercambio de información económica que permita la construcción de canales eficientes buscando siempre integrar la producción primaria, el agregado de valor y su comercialización con las premisas de equidad y oportunidad.
                                        </p>
                                    </blockquote>

                                    <div class="space-y-2 text-gray-200">
                                        <h4 class="font-semibold text-xl text-white pt-4">El Cuero Curtido Caprino como Herramienta de Valor Agregado</h4>
                                        <p class="leading-relaxed">
                                            La actividad caprina además de producir cabrito lechal para comercializar, dispone del cuero como subproducto, el cual es comercializado generalmente sin procesamiento previo a curtiembres para realizar artículos industrializados. Existe la posibilidad de darle valor agregado al subproducto mediante diferentes técnicas de curtidos artesanales. Este proceso permite al productor ofrecer al mercado; cuero crudo, curtido de suela al tanino y curtido con pelo al alumbre. También se puede obtener artesanías de marroquinería usando como insumo el cuero curtido.
                                        </p>
                                    </div>

                                    <div class="pt-4 mt-auto">
                                        <a href="https://inta.gob.ar/sites/default/files/inta_-_el_valor_agregado_del_cuero_caprino.pdf"
                                            target="_blank" rel="noopener noreferrer"
                                            class="inline-block bg-white text-[#8C2218] font-bold py-2 px-4 rounded hover:bg-gray-200 transition">
                                            Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </section>

        <!-- Section: Medio Ambiente -->
        <section id="ambiente" x-data="{ open: false }" class="w-full bg-white border-b border-gray-200">
            <div @click="open = !open" class="cursor-pointer group py-12 relative overflow-hidden transition-all duration-300 hover:bg-gray-200">
                
                <div class="container mx-auto text-center relative z-10">
                    <h2 class="text-3xl font-bold text-gray-800 inline-block border-b-2 border-[#8C2218] pb-2">Gestión del Medio Ambiente</h2>
                    <p class="text-lg text-gray-600 mt-2">Prácticas sostenibles y cuidado de los recursos.</p>
                    <div class="flex justify-center mt-4">
                        <svg class="w-8 h-8 text-gray-500 transform transition-all duration-300 opacity-0 group-hover:opacity-100" :class="{ 'rotate-180': open, 'opacity-100': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden bg-white">
                <div class="container mx-auto p-6 pt-0">
                    <p class="text-gray-700 text-lg max-w-3xl mx-auto text-center">Aquí va el contenido detallado sobre la Gestión del Medio Ambiente.</p>
                </div>
            </div>
        </section>

        <!-- Section: Mas -->
        <section id="mas" class="w-full border-b py-16 bg-gray-100">
            <div class="container mx-auto px-6">
                
                <div x-data="{ logoModalOpen: false, proyectoModalOpen: false }">
                    <!-- Grid de Tarjetas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Card 1: Turismo Rural -->
                        <div class="bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col">
                            <div class="relative h-48 bg-cover bg-center" style="background-image: url('{{ asset('imagenes-campos/chacra3.jpeg') }}');">
                                <div class="absolute inset-0 bg-black opacity-40"></div>
                                <h3 class="absolute bottom-4 left-4 text-3xl font-bold text-white">Turismo Rural</h3>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <p class="text-[#8C2218] text-lg flex-grow">
                                    Visita "Camino de los Cerros" para conocer las propuestas turísticas de la cuenca.
                                </p>
                                <a href="https://caminodeloscerros.wixsite.com/cuenca" target="_blank" rel="noopener noreferrer"
                                   class="mt-4 inline-block bg-[#8C2218] text-white font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition text-center">
                                    Ir al Sitio
                                </a>
                            </div>
                        </div>

                        <!-- Card 2: Informacion Adicional -->
                        <div class="bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col">
                            <div class="p-6">
                                <h3 class="font-bold text-2xl mb-4 text-[#8C2218]">Información Adicional</h3>
                                <div class="space-y-4">
                                    <button @click="logoModalOpen = true" class="w-full bg-[#8C2218] text-white font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition">Entes Participantes</button>
                                    <a href="https://drive.google.com/file/d/1aazeYuqQICDmt0q9wT6GW-pMSO7nHdFm/view" target="_blank" rel="noopener noreferrer" class="block w-full bg-[#8C2218] text-white font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition text-center">Declaración de Interés Provincial</a>
                                    <button @click="proyectoModalOpen = true" class="w-full bg-[#8C2218] text-white font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition">Proyecto de Desarrollo Territorial</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Logos -->
                    <div x-show="logoModalOpen" x-cloak @keydown.escape.window="logoModalOpen = false" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                        <div @click.away="logoModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-3xl p-6 relative">
                            <button @click="logoModalOpen = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
                            <h3 class="text-2xl font-bold mb-4 text-center">Entes Participantes</h3>
                            <div x-data="{
                                logos: [
                                    '{{ asset('logos/efa-sancristobal.jpg') }}',
                                    '{{ asset('logos/inta1.png') }}',
                                    '{{ asset('logos/Logo SRM.jpg') }}',
                                    '{{ asset('logos/logoovinos.png') }}',
                                    '{{ asset('logos/todostenemos.jpg') }}',
                                    '{{ asset('logos/unam.jpg') }}',
                                    '{{ asset('logos/municipios/candelaria.png') }}',
                                    '{{ asset('logos/municipios/fachinal.jpg') }}',
                                    '{{ asset('logos/municipios/profundidad.jpg') }}'
                                ],
                                activeIndex: 0,
                                next() { this.activeIndex = (this.activeIndex + 1) % this.logos.length; },
                                prev() { this.activeIndex = (this.activeIndex - 1 + this.logos.length) % this.logos.length; }
                            }" class="relative flex items-center justify-center">
                                <button @click="prev()" class="absolute left-0 bg-white/50 rounded-full p-2 text-2xl z-10">&#10094;</button>
                                <div class="w-64 h-32 flex items-center justify-center">
                                    <template x-for="(logo, index) in logos" :key="index">
                                        <img :src="logo" x-show="activeIndex === index" class="max-h-full max-w-full object-contain" x-transition>
                                    </template>
                                </div>
                                <button @click="next()" class="absolute right-0 bg-white/50 rounded-full p-2 text-2xl z-10">&#10095;</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Proyecto -->
                    <div x-show="proyectoModalOpen" x-cloak @keydown.escape.window="proyectoModalOpen = false" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                        <div @click.away="proyectoModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-4xl h-[90vh] flex flex-col">
                            <div class="p-4 border-b flex justify-between items-center">
                                <h3 class="text-2xl font-bold">Proyecto de Desarrollo Territorial</h3>
                                <div>
                                    <button @click="window.print()" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition mr-2">Imprimir / Guardar PDF</button>
                                    <button @click="proyectoModalOpen = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
                                </div>
                            </div>
                            <div class="p-6 overflow-y-auto flex-grow prose max-w-none">
                                <pre class="whitespace-pre-wrap font-sans">{{ file_get_contents(base_path('desarrollo_territorial.txt')) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <footer class="bg-[#8C2218] text-gray-200 py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 Cuenca Ovino Caprina</p>
            <a href="mailto:cuencaovinocaprinasurmnes@gmail.com" class="hover:text-white hover:underline">cuencaovinocaprinasurmnes@gmail.com</a>
        </div>
    </footer>
    @livewireScripts
</body>

</html>