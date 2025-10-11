<div class="bg-white rounded-lg shadow-sm mt-8">
    <div class="p-6 flex justify-between items-center border-b">
        <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
        <button @click="$dispatch('open-activity-log-modal', { modelo: 'Institucion' })" class="text-sm text-blue-500 hover:text-blue-700 font-semibold">
            Ver todo
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                @forelse ($recentActivities as $activity)
                    <tr class="bg-gray-50">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800 font-semibold">{{ $activity->accion }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $activity->descripcion }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay actividad reciente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
