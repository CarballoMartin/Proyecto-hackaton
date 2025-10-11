<div x-data="activityLogModal()" x-show="open" @keydown.escape.window="open = false" @open-activity-log-modal.window="openModal($event)" style="display: none;" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Actividad Reciente del Sistema
                        </h3>
                        <div class="mt-4">
                            <!-- Filters -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- User Filters -->
                                    <div class="border p-2 rounded">
                                        <h4 class="font-semibold mb-2">Actor</h4>
                                        <div class="space-y-2">
                                            <div>
                                                <label for="rol" class="block text-sm font-medium text-gray-700">Rol de Usuario</label>
                                                <select x-model="filters.rol" @change="fetchUsers" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    <option value="">Todos</option>
                                                    <option value="superadmin">Super Admin</option>
                                                    <option value="productor">Productor</option>
                                                    <option value="institucional">Institución</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                                                <select x-model="filters.user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    <option value="">Todos</option>
                                                    <template x-for="user in users" :key="user.id">
                                                        <option :value="user.id" x-text="user.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Entity Filters -->
                                    <div class="border p-2 rounded">
                                        <h4 class="font-semibold mb-2">Entidad Afectada</h4>
                                        <div class="space-y-2">
                                            <div>
                                                <label for="modelo" class="block text-sm font-medium text-gray-700">Tipo de Entidad</label>
                                                <select x-model="filters.modelo" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    <option value="">Todos</option>
                                                    <option value="Institucion">Institucion</option>
                                                    <option value="Productor">Productor</option>
                                                    <option value="User">User</option>
                                                    <option value="SolicitudVerificacion">Solicitud</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="modelo_id" class="block text-sm font-medium text-gray-700">ID de Entidad</label>
                                                <input type="text" x-model="filters.modelo_id" placeholder="Ej: 123" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Date Filters -->
                                    <div class="border p-2 rounded">
                                        <h4 class="font-semibold mb-2">Fecha</h4>
                                        <div class="space-y-2">
                                            <div>
                                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                                                <input type="date" x-model="filters.fecha_inicio" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <div>
                                                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                                                <input type="date" x-model="filters.fecha_fin" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <button title="Próximamente" disabled class="bg-red-300 cursor-not-allowed text-white font-bold py-2 px-4 rounded-md inline-flex items-center">
                                        <x-heroicon-o-arrow-down-tray class="h-5 w-5 mr-2"/>
                                        Exportar a PDF
                                    </button>
                                    <button @click="applyFilters" :disabled="isLoading" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md inline-flex items-center">
                                        <template x-if="isLoading">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="isLoading ? 'Filtrando...' : 'Filtrar'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Logs Table -->
                            <div class="mt-4">
                                <div class="overflow-x-auto" style="min-height: 330px;">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 font-mono">
                                            <template x-for="log in currentPageLogs" :key="log.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-2 whitespace-nowrap text-xs text-gray-600" x-text="log.user ? log.user.name : 'Sistema'"></td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-xs text-gray-600" x-text="log.accion"></td>
                                                    <td class="px-6 py-2 text-xs text-gray-600" x-text="log.descripcion"></td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-xs text-gray-600" x-text="new Date(log.created_at).toLocaleString()"></td>
                                                </tr>
                                            </template>
                                            <template x-if="allLogs.length === 0 && !isLoading">
                                                <tr>
                                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay logs para mostrar.</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 flex justify-between items-center">
                                <button @click="prevPage()" :disabled="clientCurrentPage === 1 && serverPage === 1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                                    Anterior
                                </button>
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Página
                                        <span class="font-medium" x-text="clientCurrentPage"></span>
                                    </p>
                                </div>
                                <button @click="nextPage()" :disabled="(clientCurrentPage >= clientTotalPages && !hasMoreServerData) || isLoading" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                                    Siguiente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function activityLogModal() {
        return {
            open: false,
            allLogs: [],
            currentPageLogs: [],
            users: [],
            isLoading: false,
            clientCurrentPage: 1,
            clientTotalPages: 1,
            serverPage: 1,
            hasMoreServerData: true,
            pageSize: 7,
            filters: {
                rol: '',
                user_id: '',
                modelo: '',
                modelo_id: '',
                fecha_inicio: '',
                fecha_fin: ''
            },
            init() {
                this.fetchUsers();
            },
            openModal(event) {
                // Reset filters to default
                this.filters.rol = '';
                this.filters.user_id = '';
                this.filters.modelo = '';
                this.filters.modelo_id = '';
                this.filters.fecha_inicio = '';
                this.filters.fecha_fin = '';

                // Apply pre-filters from event detail
                if (event.detail) {
                    for (const key in event.detail) {
                        this.filters[key] = event.detail[key];
                    }
                }

                this.open = true;
                this.applyFilters();
            },
            fetchLogsFromServer() {
                if (!this.hasMoreServerData) {
                    this.isLoading = false;
                    return;
                }
                this.isLoading = true;
                const params = new URLSearchParams(this.filters);
                params.append('page', this.serverPage);
                fetch(`/api/logs?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        this.allLogs = this.allLogs.concat(data.data);
                        this.clientTotalPages = Math.ceil(this.allLogs.length / this.pageSize);
                        this.hasMoreServerData = !!data.next_page_url;
                        this.updateClientPage();
                    }).finally(() => {
                        this.isLoading = false;
                    });
            },
            updateClientPage() {
                const start = (this.clientCurrentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                this.currentPageLogs = this.allLogs.slice(start, end);
            },
            prevPage() {
                if (this.clientCurrentPage > 1) {
                    this.clientCurrentPage--;
                    this.updateClientPage();
                }
            },
            nextPage() {
                if (this.clientCurrentPage < this.clientTotalPages) {
                    this.clientCurrentPage++;
                    this.updateClientPage();
                } else if (this.hasMoreServerData) {
                    this.serverPage++;
                    this.clientCurrentPage++;
                    this.fetchLogsFromServer();
                }
            },
            fetchUsers() {
                const params = new URLSearchParams({ rol: this.filters.rol });
                fetch(`/api/users?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        this.users = data;
                    });
            },
            applyFilters() {
                this.allLogs = [];
                this.serverPage = 1;
                this.clientCurrentPage = 1;
                this.hasMoreServerData = true;
                this.fetchLogsFromServer();
            }
        }
    }
</script>