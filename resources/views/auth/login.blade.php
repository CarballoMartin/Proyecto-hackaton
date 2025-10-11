<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Columna Izquierda: Formulario de Login (60%) -->
        <div class="w-full lg:w-3/5 flex items-center justify-center bg-gray-100 p-8">
            @include('auth.partials.login-form')
        </div>

        <!-- Columna Derecha: Promoción de la App (40%) -->
        <div class="hidden lg:block lg:w-2/5">
            @include('auth.partials.app-promo')
        </div>
    </div>
</x-guest-layout>