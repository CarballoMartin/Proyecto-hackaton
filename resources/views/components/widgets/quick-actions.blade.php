@props([
    'title' => 'Acciones Rápidas',
    'actions' => []
])

<div class="border bg-white rounded-lg shadow-sm p-6 h-full">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @if(count($actions) > 0)
        <div class="space-y-3">
            @foreach($actions as $action)
                <x-widgets.quick-action-button 
                    :route="$action['route']"
                    :icon="$action['icon']"
                    :text="$action['text']"
                />
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 py-8">
            <p>No hay acciones disponibles.</p>
        </div>
    @endif
</div>
