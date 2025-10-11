<x-guest-layout>
    {{-- Header Section --}}
    <div class="bg-[#8C2218] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-16">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Registro para Instituciones</h1>
            <p class="mt-4 text-xl text-gray-200 max-w-3xl mx-auto">
                Un espacio para la colaboración y el fortalecimiento del sector ovino-caprino.
            </p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Potenciando Decisiones con Información Estratégica</h2>
                <p class="mt-3 text-lg text-gray-600 max-w-3xl mx-auto">Acceda a un ecosistema de datos unificado. Visualice, analice y colabore para un futuro más productivo.</p>
            </div>

            {{-- Benefits Cards Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Card 1: Data Access --}}
                <div class="group bg-white rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border-t-4 border-transparent hover:border-[#8C2218]">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-6">
                            <x-heroicon-o-chart-bar class="h-16 w-16 text-[#8C2218]" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Acceso a Datos Centralizados</h3>
                        <p class="mt-2 text-gray-600 text-lg">
                            Visualice información productiva y ambiental de la región de forma consolidada y amigable.
                        </p>
                    </div>
                </div>

                {{-- Card 2: Reports --}}
                <div class="group bg-white rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border-t-4 border-transparent hover:border-[#8C2218]">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-6">
                            <x-heroicon-o-document-text class="h-16 w-16 text-[#8C2218]" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Generación de Reportes</h3>
                        <p class="mt-2 text-gray-600 text-lg">
                            Cree informes y estadísticas automatizadas para investigación, asesoramiento y toma de decisiones.
                        </p>
                    </div>
                </div>

                {{-- Card 3: Interactive Map --}}
                <div class="group bg-white rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border-t-4 border-transparent hover:border-[#8C2218]">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-6">
                            <x-heroicon-o-map class="h-16 w-16 text-[#8C2218]" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Mapa Interactivo</h3>
                        <p class="mt-2 text-gray-600 text-lg">
                            Explore un mapa georreferenciado con datos de chacras, uso del suelo y otros indicadores relevantes.
                        </p>
                    </div>
                </div>

                {{-- Card 4: Collaboration --}}
                <div class="group bg-white rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 border-t-4 border-transparent hover:border-[#8C2218]">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-6">
                            <x-heroicon-o-users class="h-16 w-16 text-[#8C2218]" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Colaboración Estratégica</h3>
                        <p class="mt-2 text-gray-600 text-lg">
                            Participe en una red de trabajo colaborativa para impulsar el desarrollo sostenible del sector.
                        </p>
                    </div>
                </div>
            </div>
            {{-- END OF BENEFITS SECTION --}}

            {{-- Call to Action Section --}}
            <div class="mt-24 text-center bg-white rounded-lg p-10 shadow-xl border-t-4 border-[#8C2218]">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Inicie el Proceso de Registro</h2>
                <p class="text-gray-600 text-xl max-w-3xl mx-auto mb-8">
                    Para formar parte de nuestra red, su institución debe completar un sencillo proceso de solicitud. Nuestro equipo la revisará y creará un perfil de <strong>Administrador Institucional</strong> para su organización.
                </p>
                                                                <button @click="solicitudModalOpen = true" class="inline-block bg-green-600 text-white font-bold py-4 px-8 rounded-lg shadow-lg text-lg hover:bg-green-700 transition-transform transform hover:scale-105 duration-300">
                    Iniciar Solicitud de Acceso
                </button>
            </div>

        </div>
    </div>
</x-guest-layout>
