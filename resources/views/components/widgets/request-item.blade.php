@props(['institution', 'date'])

<li class="py-4 flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-gray-900">{{ $institution }}</p>
        <p class="text-sm text-gray-500">Recibida el {{ $date }}</p>
    </div>
    <div class="flex space-x-2">
        <button class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded-full hover:bg-green-700">Aprobar</button>
        <button class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-full hover:bg-red-700">Rechazar</button>
    </div>
</li>
