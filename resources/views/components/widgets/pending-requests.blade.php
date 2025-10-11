@props([
    'title' => 'Solicitudes Pendientes',
    'requests' => [],
    'viewAllRoute' => '#'
])

<div class="border bg-white rounded-lg shadow-sm p-6 h-full flex flex-col">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    
    @if(count($requests) > 0)
        <div class="flex-grow">
            <ul class="divide-y divide-gray-200">
                @foreach($requests as $request)
                    <li class="py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $request['institution'] }}</p>
                        <p class="text-sm text-gray-500">Recibida el {{ $request['date'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="mt-4 pt-4 border-t">
            <a href="{{ $viewAllRoute }}" class="w-full text-center block px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                Gestionar todas las solicitudes &rarr;
            </a>
        </div>
    @else
        <div class="flex-grow flex flex-col items-center justify-center text-center text-gray-500">
            <div class="inline-flex items-center justify-center bg-green-100 rounded-full p-4">
                <x-heroicon-o-check-circle class="w-8 h-8 text-green-600" />
            </div>
            <p class="mt-4">No hay solicitudes pendientes.</p>
        </div>
    @endif
</div>
