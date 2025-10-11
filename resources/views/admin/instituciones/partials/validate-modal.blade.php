<!-- Modal de Confirmación de Validación -->
<div x-show="openValidateModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md" @click.away="openValidateModal = false">
        <h2 class="text-xl font-bold mb-4">Confirmar Validación</h2>
        <p class="text-gray-700 mb-6">Estás a punto de validar esta institución, otorgándole acceso completo al sistema. Esta acción es importante y debe hacerse con certeza. ¿Deseas continuar?</p>
        <form :action="validationUrl" method="POST">
            @csrf
            <div class="flex justify-end space-x-4">
                <button type="button" @click="openValidateModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </button>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Confirmar Validación
                </button>
            </div>
        </form>
    </div>
</div>
