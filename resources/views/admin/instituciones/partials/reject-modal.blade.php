<!-- Modal de Confirmación de Rechazo -->
<div x-show="openRejectModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md" @click.away="openRejectModal = false">
        <h2 class="text-xl font-bold mb-4 text-red-700">Confirmar Rechazo</h2>
        <p class="text-gray-700 mb-6">¿Estás seguro de que deseas rechazar esta solicitud? La solicitud se moverá al listado de 'No Aprobadas', desde donde podrás revisarla y aprobarla en el futuro.</p>
        <form :action="rejectionUrl" method="POST">
            @csrf
            <div class="flex justify-end space-x-4">
                <button type="button" @click="openRejectModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </button>
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Confirmar Rechazo
                </button>
            </div>
        </form>
    </div>
</div>
