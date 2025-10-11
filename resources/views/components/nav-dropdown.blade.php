@props(['route' => '#', 'text' => '', 'active' => false])

@php
    // Base idéntica al nav-link pero con altura para centrar
    $base = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out';


    $activeClass = $active
        ? 'border-indigo-500 text-gray-900'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300';
@endphp

<div x-data="{ open: false }" class="relative">
    <a {{ $attributes->merge(['href' => $route, 'class' => trim($base . ' ' . $activeClass)]) }}
        @click.prevent="open = !open" @keydown.escape="open = false" aria-haspopup="true" :aria-expanded="open">
        <span class="me-2">{{ $text }}</span>
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
            <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
        </svg>
    </a>

    <div x-show="open" x-cloak @click.away="open = false" x-transition
        class="absolute z-50 mt-2 w-56 rounded-md shadow-lg bg-white">
        <div class="py-1">
            {{ $slot }}
        </div>
    </div>
</div>