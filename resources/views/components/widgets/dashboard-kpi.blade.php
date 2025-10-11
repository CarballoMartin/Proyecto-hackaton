@props(['icon', 'title', 'value', 'color' => 'gray', 'highlight' => false])

@php
$colorClasses = [
    'blue' => 'text-blue-500',
    'sky' => 'text-sky-500',
    'amber' => 'text-amber-500',
    'green' => 'text-green-500',
    'red' => 'text-red-500',
    'gray' => 'text-gray-500',
];
$highlightClasses = [
    'blue' => 'border-blue-500 ring-2 ring-blue-200',
    'sky' => 'border-sky-500 ring-2 ring-sky-200',
    'amber' => 'border-amber-500 ring-2 ring-amber-200',
    'green' => 'border-green-500 ring-2 ring-green-200',
    'red' => 'border-red-500 ring-2 ring-red-200',
    'gray' => 'border-gray-500 ring-2 ring-gray-200',
];

$colorClass = $colorClasses[$color] ?? $colorClasses['gray'];
$highlightClass = $highlight ? ($highlightClasses[$color] ?? $highlightClasses['gray']) : '';
@endphp

<div class="p-5 bg-white border rounded-lg shadow-sm {{ $highlightClass }}">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <x-dynamic-component :component="$icon" class="h-7 w-7 {{ $colorClass }}" />
        </div>
        <div class="ml-4 w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                <dd class="text-2xl font-bold text-gray-900">{{ $value }}</dd>
            </dl>
        </div>
    </div>
</div>