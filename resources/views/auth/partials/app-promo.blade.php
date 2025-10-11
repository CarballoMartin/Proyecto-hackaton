<div class="relative w-full h-full bg-cover bg-center" style="background-image: url('{{ asset(
    'c-2.jpeg'
) }}');">
    <!-- Overlay con desenfoque y opacidad -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Contenido -->
    <div class="relative h-full flex flex-col items-center justify-center text-white p-12">
        <div class="text-center">
            <!-- Icono o Logo Opcional -->
            <div class="mb-6">
                <svg class="h-16 w-16 mx-auto text-indigo-400" xmlns="http://www.w3.org/2000/svg
      " fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25
      2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0
      00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3" />
                </svg>
            </div>

            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Lleva tu gestión al siguiente nivel
            </h2>
            <p class="mt-4 text-lg text-gray-300 max-w-lg mx-auto">
                Descarga nuestra aplicación móvil y gestiona tu producción directamente desde tu
                celular. Simple, rápido y siempre a tu alcance.
            </p>

            <!-- Botones de Descarga -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6">
                

                <!-- Botón Google Play -->
                <div class="text-center">
                    <div class="flex items-center justify-center w-48 h-14 bg-gray-800 bg-
      opacity-50 text-white font-semibold py-3 px-5 rounded-lg">
                        <svg class="h-8 w-8 mr-3" xmlns="http://www.w3.org/2000/svg" fill="
      currentColor" viewBox="0 0 24 24">
                            <path d="M21.8,11.1,6.4,1.5A1.34,1.34,0,0,0,4.5,2.7V21.3a1.34,1.34,0,0,0,1.9,1.2l15.4-9.6A1.34,1.34,0,0,
      0,21.8,11.1Z" />
                        </svg>
                        <span>Google Play</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Próximamente</p>
                </div>
            </div>
        </div>
    </div>
</div>