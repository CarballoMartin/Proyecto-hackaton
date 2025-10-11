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
            @include('layouts.partials.sidebar.institucional')
        </x-slot>

        {{-- Contenido principal --}}
        {{ $slot }}

    </x-panel-layout>
</x-app-layout>
