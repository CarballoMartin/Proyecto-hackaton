@extends('layouts.cuaderno')

@section('cuaderno_content')
<div x-data="cuadernoDeCampo({
    unidadesProductivasData: {{ Js::from($unidadesProductivasData) }},
    stockActualPorUPData: {{ Js::from($stockActualPorUPData) }},
    especiesData: {{ Js::from($especiesData) }},
    categoriasData: {{ Js::from($categoriasData) }},
    razasData: {{ Js::from($razasData) }},
    motivosData: {{ Js::from($motivosData) }},
    storeUrl: '{{ route('cuaderno.store') }}',
    filtrarUrl: '{{ route('api.cuaderno.movimientos.filtrar') }}',
    selectedUpId: {{ request()->get('selected_up_id') ?? 'null' }}
})">
    <div class="bg-gray-200 p-4 border-2 border-gray-400 h-full" :class="{ 'flex items-center justify-center': !selectedUpId }">
        <div :class="{ 'w-full': selectedUpId }">
            <style>
                .led-green {
                    width: 14px;
                    height: 14px;
                    background-color: #32CD32;
                    border-radius: 50%;
                    box-shadow: 0 0 6px #32CD32, 0 0 12px #32CD32;
                    display: inline-block;
                }
            </style>
            {{-- Chacra Selector --}}
            <fieldset class="border-2 border-gray-400 p-4 w-full">
                <legend class="px-2 font-bold">Seleccione un campo</legend>
                <div class="space-y-4">
                    <form method="GET" action="{{ route('cuaderno.registro') }}">
                        <select name="selected_up_id" onchange="this.form.submit()" class="block w-64 bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Elija un campo --</option>
                            @foreach ($unidadesProductivasData as $up)
                                <option value="{{ $up->id }}" {{ request()->get('selected_up_id') == $up->id ? 'selected' : '' }}>
                                    {{ $up->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <div x-show="selectedUp" x-transition class="flex justify-between items-center w-full bg-white border-2 border-gray-300 px-4 py-3">
                        <div>
                            <p class="text-sm text-gray-600"><strong>RNSPA:</strong> <span x-text="selectedUp?.identificador_local || 'N/A'"></span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600"><strong>Municipio:</strong> <span x-text="selectedUp?.municipio?.nombre || 'N/A'"></span></p>
                        </div>
                        <div class="led-green" title="Chacra activa"></div>
                    </div>
                </div>
            </fieldset>

            <div x-show="selectedUpId" class="mt-4 space-y-6">

                @include('productor.cuaderno.partials._movimientos-pendientes-table')

                @include('productor.cuaderno.partials._movimientos-guardados-table', ['movimientosGuardadosData' => $movimientosGuardadosData])

                @include('productor.cuaderno.partials._stock-actual-table')

            </div>
        </div>
    </div>

    {{-- Modal de Altas/Bajas --}}
    <div x-show="showModal" @keydown.escape.window="showModal = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-gray-200 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-2 border-gray-500">
                <fieldset class="border-2 border-gray-400 p-4 m-4">
                    <legend class="px-2 font-bold" x-text="registrationType === 'altas' ? 'Registrar Alta' : 'Registrar Baja'"></legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Especie</label>
                            <select x-model="form.especie_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione</option>
                                <template x-for="especie in especies" :key="especie.id"><option :value="especie.id" x-text="especie.nombre"></option></template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoría</label>
                            <select x-model="form.categoria_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione</option>
                                <template x-for="categoria in filteredCategorias" :key="categoria.id"><option :value="categoria.id" x-text="categoria.nombre"></option></template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Raza</label>
                            <select x-model="form.raza_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione</option>
                                <template x-for="raza in filteredRazas" :key="raza.id"><option :value="raza.id" x-text="raza.nombre"></option></template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                            <input type="number" x-model="form.cantidad" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500" min="1">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Motivo</label>
                            <select x-model="form.motivo_movimiento_id" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione un motivo</option>
                                <template x-for="motivo in (registrationType ? motivos[registrationType.slice(0, -1)] : [])" :key="motivo.id"><option :value="motivo.id" x-text="motivo.nombre"></option></template>
                            </select>
                        </div>
                        <div class="md:col-span-2" x-show="form.motivo_movimiento_id && motivos[registrationType.slice(0, -1)]?.find(m => m.id == form.motivo_movimiento_id)?.nombre.toLowerCase().includes('traslado')">
                            <label class="block text-sm font-medium text-gray-700">Destino del Traslado</label>
                            <input type="text" x-model="form.destino_traslado" class="mt-1 block w-full bg-white border-gray-400 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: Chacra 'La Perseverancia', RNSPA XX.XXX.X...">
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button @click="addMovement()" type="button" class="px-4 py-1 bg-gray-200 border-2 border-gray-500 hover:bg-gray-300 font-semibold">Añadir al Resumen &rarr;</button>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    {{-- Notification Modal --}}
    <div x-show="$store.notification.show" @keydown.escape.window="$store.notification.show = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
            <div x-show="$store.notification.show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$store.notification.show = false" aria-hidden="true"></div>
            <div x-show="$store.notification.show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block bg-gray-200 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border-2 border-t-0 border-gray-500">
                <div class="px-4 py-1 flex justify-between items-center border-b-2 border-gray-500" :class="$store.notification.isError ? 'bg-red-700' : 'bg-blue-800'">
                    <h3 class="text-lg font-semibold text-white" id="modal-title" x-text="$store.notification.isError ? 'Error' : 'Notificación'"></h3>
                </div>
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full border-2" :class="$store.notification.isError ? 'bg-red-100 border-red-400' : 'bg-green-100 border-green-400'">
                        <svg x-show="$store.notification.isError" class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <svg x-show="!$store.notification.isError" class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <p class="text-base text-gray-700" x-text="$store.notification.message"></p>
                    </div>
                </div>
                <div class="bg-gray-300 px-4 py-3 text-center border-t-2 border-gray-400">
                    <button @click="$store.notification.show = false" type="button" class="w-1/2 inline-flex justify-center rounded-md border-2 border-gray-500 shadow-sm px-4 py-2 bg-gray-200 text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @if (session('success') || session('error') || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let message = '';
            let isError = false;
            @if (session('success'))
                message = "{{ session('success') }}";
                isError = false;
            @elseif (session('error'))
                message = "{{ session('error') }}";
                isError = true;
            @elseif ($errors->any())
                message = "{{ $errors->first() }}";
                isError = true;
            @endif
            
            if (message) {
                setTimeout(() => {
                    if (window.Alpine && Alpine.store('notification')) {
                        Alpine.store('notification').notify(message, isError);
                    }
                }, 100);
            }
        });
    </script>
    @endif
@endpush
@endsection