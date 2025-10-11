<div class="w-full max-w-md" x-data="{ loginMethod: 'password' }">
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        <!-- Logo Centrado -->
        <div class="flex justify-center pt-4">
            <a href="/">
                <x-logo class="h-40 w-40 text-gray-500" />
            </a>
        </div>

        <!-- Pestañas de selección de método -->
        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
                <button @click="loginMethod = 'password'"
                    :class="{
                        'border-indigo-500 text-indigo-600': loginMethod === 'password',
                        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': loginMethod !== 'password'
                    }"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium focus:outline-none">
                    Con Contraseña
                </button>
                <button @click="loginMethod = 'otp'"
                    :class="{
                        'border-indigo-500 text-indigo-600': loginMethod === 'otp',
                        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': loginMethod !== 'otp'
                    }"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium focus:outline-none">
                    Con Código de Acceso
                </button>
            </nav>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <div class="relative min-h-[280px] overflow-hidden">
            <!-- Formulario de Contraseña -->
            <div x-show="loginMethod === 'password'"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="-translate-x-full blur-sm"
                 x-transition:enter-end="translate-x-0 blur-none"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="translate-x-0 blur-none"
                 x-transition:leave-end="-translate-x-full blur-sm"
                 class="absolute w-full">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                    <div class="mt-4">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    </div>
                    <div class="block mt-4">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                        <x-button class="ms-4 me-4">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Componente de Login con OTP -->
            <div x-show="loginMethod === 'otp'"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="translate-x-full blur-sm"
                 x-transition:enter-end="translate-x-0 blur-none"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="translate-x-0 blur-none"
                 x-transition:leave-end="translate-x-full blur-sm"
                 class="absolute w-full">
                @livewire('auth.login-otp')
            </div>
        </div>

    </x-authentication-card>
</div>
