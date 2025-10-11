<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">

            {{-- Icono de Éxito --}}
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            {{-- Título --}}
            <h1 class="text-2xl font-bold text-gray-900 mt-6">
                ¡Solicitud Enviada!
            </h1>

            {{-- Mensaje --}}
            <p class="text-sm text-gray-600 mt-4">
                Hemos recibido tu solicitud de incorporación. El equipo de administración la revisará a la brevedad y
                recibirás una notificación por correo electrónico cuando
                sea aprobada.
            </p>

            {{-- Botón para volver --}}
            <div class="mt-8">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest
      hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    Volver a la Página Principal
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>