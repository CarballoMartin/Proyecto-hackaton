<x-app-layout>
    <x-panel-layout>
        {{-- Slot para el título del header --}}
        <x-slot name="header_title">
            @if (isset($header))
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $header }}
                </h2>
            @endif
        </x-slot>

        {{-- Slot para el menú lateral --}}
        <x-slot name="sidebar">
            @include('layouts.partials.sidebar.admin')
        </x-slot>

        {{-- Contenido principal --}}
        {{ $slot }}

        @push('modals')
            {{-- Cargar modales específicos del panel de Superadmin --}}
            @if (auth()->check() && auth()->user()->rol === 'superadmin')
                <x-modals.institucion-form />
                <x-modals.productor-form />
                {{-- <x-modals.productor-edit-form /> --}} {{-- Comentado por si aún no está migrado a componente Blade --}}
                @isset($configuracion)
                    <x-modals.configuracion :configuracion="$configuracion" />
                @endisset

                <x-modals.confirm-password-modal />

                {{-- Modal de Actividad Reciente --}}
                @if(isset($activityItems))
                    <x-modals.activity-feed :items="$activityItems" />
                @endif
                <x-modals.activity-log-modal />
            @endif
        @endpush

    </x-panel-layout>
</x-app-layout>
