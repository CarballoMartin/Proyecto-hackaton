@props([
    'title' => 'Actividad Reciente',
    'items' => []
])

<div class="border bg-white rounded-lg shadow-sm p-6 h-full">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @if(count($items) > 0)
        <ul class="space-y-4">
            @foreach($items as $item)
                <x-widgets.activity-item 
                    :icon="$item['icon'] ?? 'heroicon-s-information-circle'" 
                    :text="$item['text'] ?? ''" 
                    :time="$item['time'] ?? ''" 
                />
            @endforeach
        </ul>
    @else
        <div class="text-center text-gray-500 py-8">
            <div class="inline-flex items-center justify-center bg-gray-100 rounded-full p-4">
                <x-heroicon-o-no-symbol class="w-8 h-8 text-gray-400" />
            </div>
            <p class="mt-4">No hay actividad reciente.</p>
        </div>
    @endif
</div>
