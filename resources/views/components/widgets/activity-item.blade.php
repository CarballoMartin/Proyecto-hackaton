@props(['icon', 'text', 'time'])

<li class="flex items-start space-x-3">
    <div class="flex-shrink-0 pt-0.5">
        <x-dynamic-component :component="$icon" class="h-5 w-5 text-gray-400" />
    </div>
    <div class="flex-1">
        <p class="text-sm text-gray-700">{{ $text }}</p>
        <p class="text-xs text-gray-500">{{ $time }}</p>
    </div>
</li>
