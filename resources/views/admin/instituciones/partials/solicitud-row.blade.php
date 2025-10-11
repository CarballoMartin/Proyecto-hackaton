<tr>
    <td class="px-6 py-4 whitespace-nowrap">{{ $solicitud->nombre_institucion }}</td>
    <td class="px-6 py-4 whitespace-nowrap">{{ $solicitud->cuit ?? 'N/A' }}</td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-medium">Pendiente</span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <a href="#gestion-solicitudes" class="text-indigo-600 hover:text-indigo-900">Ver en Gestión</a>
    </td>
</tr>
