@props(['route', 'icon', 'text'])

<a href="{{ $route }}" class="flex items-center w-full text-left p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
    <x-dynamic-component :component="$icon" class="h-6 w-6 mr-4 text-gray-600" />
    <span class="font-semibold text-gray-700">{{ $text }}</span>
    <x-heroicon-s-chevron-right class="h-5 w-5 ml-auto text-gray-400" />
</a>
