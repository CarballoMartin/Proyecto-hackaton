@props(['icon', 'title', 'value', 'color' => 'gray', 'highlight' => false])

@php
$colors = [
    'blue' => 'border-blue-500 bg-blue-100 text-blue-600',
    'sky' => 'border-sky-500 bg-sky-100 text-sky-600',
    'amber' => 'border-amber-500 bg-amber-100 text-amber-600',
    'green' => 'border-green-500 bg-green-100 text-green-600',
    'red' => 'border-red-500 bg-red-100 text-red-600',
    'gray' => 'border-gray-500 bg-gray-100 text-gray-600',
];
$colorClasses = $colors[$color] ?? $colors['gray'];
@endphp

<div class="bg-white overflow-hidden shadow-lg rounded-xl p-5 border-l-4 {{ $colorClasses }} @if($highlight) animate-pulse @endif">
    <div class="flex items-center">
        <div class="flex-shrink-0 rounded-md p-3 {{ $colorClasses }}">
            <x-dynamic-component :component="$icon" class="h-6 w-6 text-white" style="background-color: currentColor;" />
        </div>
        <div class="ml-5 w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                <dd class="text-2xl font-bold text-gray-900">{{ $value }}</dd>
            </dl>
        </div>
    </div>
</div>
