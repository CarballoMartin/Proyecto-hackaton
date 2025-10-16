<tr>
    <td class="px-6 py-4 whitespace-nowrap">{{ $institucion->nombre }}</td>
    <td class="px-6 py-4 whitespace-nowrap">{{ $institucion->cuit ?? 'N/A' }}</td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if ($institucion->validada)
            <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-medium">Aprobada</span>
        @else
            <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-medium">No Aprobada</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        @if (!$institucion->validada)
            <button @click="validationUrl = '{{ route('admin.instituciones.validar', $institucion) }}'; openValidateModal = true" class="text-green-600 hover:text-green-900">Validar</button>
        @endif
        <button @click.prevent="$dispatch('open-edit-institucion-modal', { id: {{ $institucion->id }} })" 
                class="text-indigo-600 hover:text-indigo-900 ml-4">Editar</button>
        @if ($institucion->validada)
            <button @click="deactivationUrl = '{{ route('admin.instituciones.deactivate', $institucion) }}'; openDeactivateModal = true" class="text-yellow-600 hover:text-yellow-900 ml-4">Desactivar</button>
        @else
            <button @click="deletionUrl = '{{ route('admin.instituciones.destroy', $institucion) }}'; openDeleteModal = true" class="text-red-600 hover:text-red-900 ml-4">Eliminar</button>
        @endif
    </td>
</tr>
