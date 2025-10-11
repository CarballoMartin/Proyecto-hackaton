<div x-data="notificationsPanel()" class="relative">
    {{-- Icono de la campana --}}
    <button @click="togglePanel()"
            class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none"
            title="Notificaciones">
        <x-heroicon-o-bell class="h-6 w-6" />
        <template x-if="unreadCount > 0">
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                  x-text="unreadCount">
            </span>
        </template>
    </button>

    {{-- Panel deslizable de notificaciones --}}
    <div x-show="panelOpen" @keydown.escape.window="closePanel()" x-cloak>
        <div x-show="panelOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 transition-opacity"
             @click="closePanel()">
        </div>

        <div x-show="panelOpen"
             x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 z-40 flex flex-col w-full max-w-md bg-white shadow-xl">

            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Notificaciones</h2>
                <button @click="closePanel()" class="text-gray-500 hover:text-gray-800">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <template x-if="isLoading">
                    <div class="p-6 text-center text-gray-500">Cargando...</div>
                </template>

                <template x-if="!isLoading && notifications.length === 0">
                    <p class="p-6 text-sm text-center text-gray-500">No tienes notificaciones.</p>
                </template>

                <template x-for="notification in notifications" :key="notification.id">
                    <div class="p-4 border-b hover:bg-gray-50 group">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 pt-1">
                                <span class="block w-2.5 h-2.5 rounded-full"
                                      :class="{ 'bg-blue-500': !notification.read_at, 'bg-gray-300': notification.read_at }"
                                      :title="notification.read_at ? 'Leída' : 'No leída'"></span>
                            </div>

                            <div class="ml-3 flex-1">
                                <a href="#" @click.prevent="handleNotificationClick(notification)" class="block">
                                    <p class="text-sm font-bold text-gray-800" x-text="notification.data.titulo || 'Notificación'"></p>
                                    <p class="text-sm text-gray-600" x-text="notification.data.mensaje || ''"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="timeAgo(notification.created_at)"></p>
                                </a>
                            </div>

                            <div class="ml-2 flex-shrink-0">
                                <button @click="deleteNotification(notification.id)" class="text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <x-heroicon-s-x-circle class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="!isLoading && unreadCount > 0">
                <div class="p-4 border-t text-center bg-gray-50">
                    <button @click="markAllAsRead()" class="text-sm text-blue-500 hover:underline">
                        Marcar todas como leídas
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>


