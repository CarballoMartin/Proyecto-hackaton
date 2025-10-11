{{-- resources/views/profile/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    {{-- Wrapper Alpine: escucha change-admin-tab pero NO inicializa tab ni forza hash --}}
    <div x-data="{
            adminRoute: '{{ route('admin.panel') }}',
            // handler simple y robusto para el evento: redirige fuera del admin, o actualiza hash si ya estamos en admin
            handleChangeTab(tab) {
                try {
                    const currentPath = window.location.pathname;
                    const adminPath = (new URL(this.adminRoute, window.location.origin)).pathname;

                    if (currentPath === adminPath) {
                        // ya estamos en admin.panel -> solo actualizamos el hash
                        window.location.hash = tab;
                    } else {
                        // fuera del admin.panel -> redirigimos con hash
                        window.location.href = this.adminRoute + '#' + tab;
                    }
                } catch (e) {
                    // fallback seguro
                    window.location.href = this.adminRoute + '#' + tab;
                }
            }
        }"
        @change-admin-tab.window="handleChangeTab($event.detail.tab)"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div> {{-- cierra wrapper Alpine --}}
</x-app-layout>
