@props([
    'title' => 'Noticias del Sector',
    'items' => []
])

<div class="border bg-white rounded-lg shadow-sm p-6 h-full">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @if(count($items) > 0)
        <ul class="space-y-4">
            @foreach($items as $item)
                <li>
                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        {{ $item['title'] }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1">Fuente: {{ $item['source'] }}</p>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center text-gray-500 py-8">
            <p>No hay noticias disponibles.</p>
        </div>
    @endif
</div>
