<x-app-layout>
    <x-panel-layout>
        <x-slot name="header_title">
            @if (isset($header))
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $header }}
                </h2>
            @else
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Panel del Productor
                </h2>
            @endif
        </x-slot>

        <x-slot name="sidebar">
            @include('layouts.partials.sidebar.productor')
        </x-slot>

            {{ $slot }}

    <x-modals.productor-perfil />
    <x-modals.activity-log-modal />

</x-panel-layout>
</x-app-layout>
