@props(['class' => ''])

<svg {{ $attributes->merge(['class' => 'w-10 h-10 ' . $class]) }} viewBox="0 0 100 100"
    xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="50" r="50" fill="#ffffff" />
    <image href="{{ asset('logos/logoovinos.png') }}" x="0" y="0" width="100" height="100" />
</svg>