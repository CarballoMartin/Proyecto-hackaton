<!-- resources/views/partials/landing/about.blade.php -->
<section id="about" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Título -->
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Nuestra Visión: Impulsando el Futuro de la Cuenca
        </h2>

        <!-- Contenedor del Video -->
        <div x-data="{ showVideo: false }" class="mt-12 rounded-lg shadow-2xl overflow-hidden max-w-3xl mx-auto">
            <div class="relative aspect-video">
                <!-- Fachada de Video (Imagen y Botón de Play) -->
                <div x-show="!showVideo" @click="showVideo = true" class="cursor-pointer">
                    <!-- Usamos la miniatura de máxima resolución de YouTube -->
                    <img src="https://img.youtube.com/vi/1gFLM_XZ8x4/maxresdefault.jpg" alt="Vista previa del video sobre el
      proyecto" class="w-full h-full object-cover">
                    <div @click="showVideo = true" class="absolute inset-0 flex items-center justify-center bg-black
      bg-opacity-40 transition hover:bg-opacity-30">
                                                <x-heroicon-s-play-circle class="h-20 w-20 text-white" />
                    </div>
                </div>

                <!-- Iframe del Video (se muestra al hacer clic) -->
                <div x-show="showVideo" x-cloak>
                    <iframe class="absolute top-0 left-0 w-full h-full"
                        :src="showVideo ? 'https://www.youtube.com/embed/1gFLM_XZ8x4?autoplay=1' : ''" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;
     picture-in-picture" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Contenedor del Texto -->
        <div class="mt-12 prose prose-lg text-gray-600 mx-auto">

            <p>
                Somos una iniciativa interinstitucional dedicada a fortalecer el sector ovino y caprino de la región a
                través de
                la tecnología y la colaboración, balanceando la producción primaria con la conservación del suelo y la
                gestión eficiente de los
                recursos.
            </p>

        </div>

    </div>
</section>