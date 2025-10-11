@php use Illuminate\Support\Str; @endphp
<tr>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">{{ $solicitud->nombre_institucion }}</div>
        <div class="text-sm text-gray-500">{{ $solicitud->localidad }}, {{ $solicitud->provincia }}</div>
        @if($solicitud->mensaje)
        <div class="text-xs text-gray-600 mt-1 italic">"{{ Str::limit($solicitud->mensaje, 80) }}"</div>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">{{ $solicitud->nombre_solicitante }}</div>
        <div class="text-sm text-gray-500">{{ $solicitud->email_contacto }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-gray-500">{{ $solicitud->created_at->format('d/m/Y') }}</span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
        <button @click.prevent="$dispatch('open-institucion-modal', { solicitud: {{ json_encode($solicitud) }} })" class="text-green-600 hover:text-green-900 font-semibold">Aprobar</button>
        <button @click="rejectionUrl = '{{ route('admin.solicitudes.reject', $solicitud) }}'; openRejectModal = true" class="text-red-600 hover:text-red-900 font-semibold">Rechazar</button>
    </td>
</tr>
