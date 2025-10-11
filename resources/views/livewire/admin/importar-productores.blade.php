<div>
    {{-- Contenido --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Instrucciones / Plantilla — versión dinámica y ordenada (correcciones: icono y feedback copia) --}}
        <div class="bg-white shadow rounded-xl p-4 border border-gray-100">
            <div class="md:grid md:grid-cols-3 md:gap-6">

                {{-- Columna 1: Requisitos (acordeón) --}}
                <div x-data="{ open: true }" class="mb-3 md:mb-0">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-3 rounded-lg transition focus:outline-none"
                        :class="open ? 'bg-indigo-50 border-l-4 border-indigo-200' : 'bg-white border'">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-md bg-indigo-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-6a2 2 0 00-2-2H5l7-7 7 7h-2a2 2 0 00-2 2v6" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium text-gray-800">Requisitos mínimos</div>
                                <div class="text-xs text-gray-500">Lo que debe tener cada fila para procesarse</div>
                            </div>
                        </div>
                        <svg x-show="!open" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="open" class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition
                        class="mt-3 p-3 bg-white border rounded-md text-sm text-gray-700 space-y-2">
                        <ul class="list-inside list-disc">
                            <li><strong>Apellido y nombre</strong> — obligatorio.</li>
                            <li><strong>DNI o CUIL</strong> — al menos uno (solo números, sin puntos ni espacios).</li>
                            <li><strong>Email o teléfono</strong> — al menos uno (email único por productor).</li>
                            <li><strong>RNSPA</strong> — obligatorio para la Unidad Productiva.</li>
                        </ul>

                        <div class="mt-2 text-xs text-gray-500">
                            Recomendado: validar duplicados y limpiar espacios antes de exportar a CSV (UTF-8).
                        </div>
                    </div>
                </div>

                {{-- Columna 2: Encabezados y plantilla (acordeón) --}}
                <div x-data="{ open: false, copied:false }" class="mb-3 md:mb-0">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-3 rounded-lg transition focus:outline-none"
                        :class="open ? 'bg-green-50 border-l-4 border-green-200' : 'bg-white border'">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-md bg-green-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M7 7V4h10v3" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium text-gray-800">Encabezados y Plantilla</div>
                                <div class="text-xs text-gray-500">Copia los encabezados exactos o descarga la plantilla
                                </div>
                            </div>
                        </div>

                        <svg x-show="!open" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="open" class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="mt-3 p-3 bg-white border rounded-md text-sm text-gray-700">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-xs text-gray-600">Encabezados oficiales:</div>
                            <div class="flex items-center gap-2">
                                {{-- botón copiar con feedback --}}
                                <button
                                    @click="navigator.clipboard.writeText('Apellido y nombre,dni,cuil,email,telefono,rnspa,latitud,longitud,superficie,municipio,paraje,direccion').then(()=>{copied=true; setTimeout(()=>copied=false,1500)})"
                                    class="text-xs px-2 py-1 border rounded hover:bg-gray-50 flex items-center gap-2">
                                    <span x-show="!copied">Copiar</span>
                                    <span x-show="copied" x-cloak class="text-green-600">Copiado ✓</span>
                                </button>

                                <a href="{{ asset('templates/plantilla_productores.csv') }}" download
                                    class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Descargar</a>
                            </div>
                        </div>

                        <div class="mt-3 overflow-x-auto bg-gray-50 rounded border">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left">
                                        <th class="px-2 py-2 text-xs text-gray-500">Apellido y nombre</th>
                                        <th class="px-2 py-2 text-xs text-gray-500">dni</th>
                                        <th class="px-2 py-2 text-xs text-gray-500">email</th>
                                        <th class="px-2 py-2 text-xs text-gray-500">rnspa</th>
                                        <th class="px-2 py-2 text-xs text-gray-500">latitud</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white">
                                        <td class="px-2 py-2">Pérez Juan</td>
                                        <td class="px-2 py-2">30111222</td>
                                        <td class="px-2 py-2 text-xs">juan.perez@mail.com</td>
                                        <td class="px-2 py-2">1234567</td>
                                        <td class="px-2 py-2">-27.34567</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            Tip: exportá la hoja como <strong>CSV UTF-8</strong> para evitar problemas con acentos y
                            caracteres.
                        </div>
                    </div>
                </div>

                {{-- Columna 3: Errores & soluciones (acordeón) --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-3 rounded-lg transition focus:outline-none"
                        :class="open ? 'bg-yellow-50 border-l-4 border-yellow-200' : 'bg-white border'">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-md bg-yellow-100 flex items-center justify-center">
                                {{-- icono de advertencia corregido --}}
                                <svg class="h-5 w-5 text-yellow-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium text-gray-800">Errores comunes & soluciones</div>
                                <div class="text-xs text-gray-500">Qué revisar rápido si falla la importación</div>
                            </div>
                        </div>

                        <svg x-show="!open" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <svg x-show="open" class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition
                        class="mt-3 p-3 bg-white border rounded-md text-sm text-gray-700 space-y-2">
                        <div class="text-xs text-red-700 font-medium">Problemas que causan rechazo</div>
                        <ul class="list-inside list-disc text-xs text-gray-600">
                            <li>Faltan columnas obligatorias (nombres mal escritos).</li>
                            <li>DNI/CUIL con caracteres no numéricos o duplicados.</li>
                            <li>Archivo no UTF-8 → caracteres corruptos.</li>
                        </ul>

                        <div class="text-xs text-gray-700 mt-2 font-medium">Soluciones rápidas</div>
                        <ul class="list-inside list-disc text-xs text-gray-600">
                            <li>Usar Buscar/Reemplazar en Excel para quitar puntos/espacios.</li>
                            <li>Convertir columnas numéricas a formato 'Número' antes de exportar.</li>
                            <li>Exportar con delimitador coma (o punto y coma según configuración) y verificar tamaño ≤
                                4MB.</li>
                        </ul>
                    </div>
                </div>

            </div>

            {{-- Footer compacto con acciones rápidas --}}
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-gray-500">¿Listo? Usá el panel de carga para procesar tu archivo. La vista
                    previa muestra las primeras 10 filas.</div>

                <div class="flex items-center gap-2">
                    <a href="{{ asset('templates/plantilla_productores.csv') }}" download
                        class="text-xs px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">Descargar
                        plantilla</a>

                    {{-- footer copy con feedback separado --}}
                    <div x-data="{ copied:false }">
                        <button
                            @click="navigator.clipboard.writeText('Apellido y nombre,dni,cuil,email,telefono,rnspa,latitud,longitud,superficie,municipio,paraje,direccion').then(()=>{copied=true; setTimeout(()=>copied=false,1500)})"
                            class="text-xs px-3 py-1 border rounded hover:bg-gray-50 flex items-center gap-2">
                            <span x-show="!copied">Copiar encabezados</span>
                            <span x-show="copied" x-cloak class="text-green-600">Copiado ✓</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Zona de carga (con loader genérico de textos rotativos) -->

        <div x-data="{
            isUploading: false,
            progress: 0,
            isDragging: false,
            fileName: '',
            loadingMessages: [
                'Validando archivo...',
                'Analizando columnas...',
                'Generando vista previa...',
                'Verificando formato de celdas...',
                'Comprobando encabezados...',
                'Preparando importación...'
            ],
            msgIndex: 0,
            intervalId: null
            }"
            x-on:livewire-upload-start="isUploading = true; if (!intervalId) { intervalId = setInterval(() => { if (isUploading) { msgIndex = (msgIndex + 1) % loadingMessages.length } }, 2500) }"
            x-on:livewire-upload-finish="isUploading = false; progress = 0; fileName = ''; clearInterval(intervalId); intervalId = null"
            x-on:livewire-upload-error="isUploading = false; progress = 0; fileName = ''; clearInterval(intervalId); intervalId = null"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            class="bg-white shadow rounded-xl p-6 border border-gray-100">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Archivo</label>
                    <div class="mt-2">
                        <div x-on:dragover.prevent="isDragging = true" x-on:dragleave.prevent="isDragging = false"
                            x-on:drop="isDragging = false"
                            class="relative rounded-md border-2 border-dashed p-6 text-center cursor-pointer transition bg-white hover:bg-gray-50"
                            :class="{'border-indigo-500 bg-indigo-50': isDragging}">
                            <input type="file" id="archivoCsv" wire:model="archivoCsv"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">

                            <div class="flex flex-col items-center">
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 48 48">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0
                                    01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" />
                                </svg>
                                <p class="mt-3 text-sm text-gray-600"><span class="font-semibold">Arrastra y
                                        suelta</span> o haz clic para seleccionar</p>
                                <p class="text-xs text-gray-500 mt-1">CSV, XLS, XLSX — Máx. 4MB</p>

                                <div class="mt-3 text-sm text-gray-700" x-show="fileName">
                                    <strong>Archivo:</strong> <span x-text="fileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-64 flex flex-col space-y-3">
                    <!-- Loader genérico (reemplaza barra de progreso con mensajes rotativos más lentos) -->
                    <div x-show="isUploading" class="flex items-center space-x-3">
                        <svg class="h-5 w-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <div class="text-sm text-gray-700" x-text="loadingMessages[msgIndex]"></div>
                    </div>

                    <div class="text-xs text-gray-500">
                        <div wire:loading wire:target="archivoCsv">⏳ Procesando archivo...</div>
                    </div>

                    @error('archivoCsv') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Si hay preview, mostramos botón para abrir modal (el modal se abre automáticamente al renderizarse) --}}
        @if ($preview && $preview->count())
            <div class="flex items-center justify-end">
                <button @click="$dispatch('abrir-preview')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A2 2 0 0122 9.618V14.38a2 2 0 01-2.447 1.894L15 14M4 6v12"></path>
                    </svg>
                    Ver vista previa
                </button>
            </div>

            <!-- Modal flotante para la vista previa + confirmación previa (no destructiva hasta confirmar) -->
            <div x-data="{
                                show: true,
                                filter: '',
                                showConfirm: false,
                                init() {
                                    window.addEventListener('abrir-preview', () => this.show = true);
                                    this.$watch('filter', value => {
                                        const rows = this.$refs.previewTbody?.querySelectorAll('tr') || [];
                                        rows.forEach(tr => {
                                            tr.style.display = value ? (tr.textContent.toLowerCase().includes(value.toLowerCase()) ? '' : 'none') : '';
                                        });
                                    });
                                }
                            }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-black opacity-40" @click="show = false"></div>

                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-5xl mx-auto overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Vista Previa de los Datos (primeras 10 filas)
                            </h3>
                            <p class="text-sm text-gray-500">Revisa la estructura del archivo antes de confirmar la
                                importación.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input x-model="filter" type="search" placeholder="Filtrar texto en filas..."
                                class="px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring focus:ring-indigo-200" />
                            <button @click="show = false"
                                class="px-3 py-2 bg-gray-100 rounded-md text-sm hover:bg-gray-200">Cerrar</button>
                        </div>
                    </div>

                    <!-- AVISO CLARO SOBRE EL PROPÓSITO DE LA VISTA PREVIA -->
                    <div class="p-4 border-b">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-900 p-3 rounded-md text-sm">
                            <strong>IMPORTANTE — Propósito de esta vista previa:</strong>
                            <div class="mt-1">
                                Esta vista previa muestra <strong>hasta las primeras 10 filas</strong> únicamente y su
                                objetivo es verificar que las <strong>columnas obligatorias</strong> estén presentes y con
                                el nombre correcto (p. ej. <code
                                    class="font-mono">Apellido y nombre, dni, cuil, email, telefono, rnspa</code>).
                            </div>
                            <div class="mt-1">
                                No valida ni corrige datos individuales (DNI, CUIL, emails). Si necesitás revisar o limpiar
                                registros específicos, hacelo <strong>antes</strong> de subir el archivo.
                            </div>
                        </div>
                    </div>

                    <div class="p-4 max-h-[60vh] overflow-auto">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        @foreach (array_keys($preview->first()) as $columna)
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $columna }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody x-ref="previewTbody" class="bg-white divide-y divide-gray-100">
                                    @foreach ($preview->take(10) as $fila)
                                        <tr class="hover:bg-gray-50">
                                            @foreach ($fila as $valor)
                                                <td class="px-4 py-2 align-top whitespace-normal break-words"
                                                    title="{{ is_scalar($valor) ? $valor : '' }}">
                                                    {{ Str::limit($valor, 120) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="p-4 border-t flex items-center justify-between">
                        <div class="text-xs text-gray-500">Se muestran hasta las primeras 10 filas. Revisa los encabezados y
                            su ortografía.</div>
                        <div class="flex items-center space-x-3">
                            <button @click="showConfirm = true"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:ring focus:ring-indigo-300 transition">
                                <svg class="-ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Confirmar e Iniciar Importación
                            </button>

                            <button @click="show = false"
                                class="px-3 py-2 bg-gray-100 rounded-md text-sm hover:bg-gray-200">Cancelar</button>
                        </div>
                    </div>

                    <!-- Modal interno de confirmación irreversible -->
                    <div x-show="showConfirm" x-cloak class="absolute inset-0 flex items-center justify-center">
                        <div class="absolute inset-0 bg-black opacity-30" @click="showConfirm = false"></div>

                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md z-50 p-5">
                            <h4 class="text-lg font-semibold text-gray-800">⚠️ Confirmar importación masiva</h4>
                            <p class="mt-2 text-sm text-gray-600">
                                Esta acción <strong>no se puede deshacer</strong>. Se importarán todos los registros del
                                archivo al sistema.
                                Revisa cuidadosamente los datos antes de continuar.
                            </p>

                            <ul class="mt-3 text-sm text-gray-700 list-disc list-inside bg-yellow-50 p-3 rounded">
                                <li>Verifica que <strong>DNI, CUIL y email</strong> sean correctos y únicos.</li>
                                <li>Comprueba que las columnas obligatorias estén presentes y escritas correctamente.</li>
                                <li>Se recomienda hacer un respaldo antes de ejecutar la importación en producción.</li>
                            </ul>

                            <div class="mt-4 flex justify-end space-x-3">
                                <button @click="showConfirm = false"
                                    class="px-3 py-2 bg-gray-100 rounded-md hover:bg-gray-200 text-sm">Volver</button>

                                <!-- Llama al método Livewire sólo cuando el usuario confirma -->
                                <button @click="showConfirm = false; show = false" wire:click="confirmarImportacion"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                    <span wire:loading.remove>Confirmar e Importar (irrevocable)</span>
                                    <span wire:loading>Procesando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /Modal interno de confirmación -->

                </div>
            </div>

        @endif

        <!-- Modal de Columnas Faltantes (mantengo tal cual su lógica) -->
        <div x-data="{ show: @entangle('showMissingColumnsModal') }" x-show="show" x-cloak
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
            <div @click.away="$wire.closeModal()" class="bg-white p-6 rounded-lg shadow-xl max-w-md mx-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Error de Validación de Archivo</h3>
                    <button @click="$wire.closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        El archivo <strong x-text="$wire.errorFileName"></strong> no pudo ser procesado porque le faltan
                        las siguientes columnas obligatorias:
                    </p>
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600 bg-red-50 p-3 rounded-md">
                        @if(is_array($missingColumns))
                            @foreach ($missingColumns as $column)
                                <li class="font-mono">{{ $column }}</li>
                            @endforeach
                        @endif
                    </ul>
                    <p class="mt-4 text-xs text-gray-500">
                        Por favor, corrige el archivo y vuelve a intentarlo. Asegúrate de que los encabezados coincidan
                        exactamente con los requeridos en las instrucciones.
                    </p>
                </div>
                <div class="mt-6 text-right">
                    <button @click="$wire.closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>