import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('notification', {
        show: false,
        isError: false,
        message: '',

        notify(message, isError = false) {
            this.message = message;
            this.isError = isError;
            this.show = true;
        }
    });
});
import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('alpine:init', () => {
    Alpine.data('loaderManager', () => ({
        init() {
            // Mostrar el loader justo antes de que la página se descargue para navegar a otra
            window.addEventListener('beforeunload', () => this.show());

            // Ocultar en la carga inicial de la página nueva
            window.addEventListener('load', () => this.hide());
            
            // Ocultar en la navegación de Livewire (se mantiene por compatibilidad)
            document.addEventListener('livewire:navigated', () => this.hide());

            // Maneja el botón de retroceso/adelante del navegador (cache de navegador)
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    this.hide();
                }
            });
        },
        show() {
            const loader = document.getElementById('loader');
            if (loader) loader.classList.remove('hidden');
        },
        hide() {
            const loader = document.getElementById('loader');
            if (loader) loader.classList.add('hidden');
        }
    }));

    Alpine.data('configuracionModal', (initialConfig) => ({
        open: false,
        activeTab: 'stock',
        isSaving: false,
        formData: {
            frecuencia_dias: 30,
            activo: false
        },
        init() {
            if (initialConfig) {
                this.formData.frecuencia_dias = initialConfig.frecuencia_dias;
                // Asegurarse de que 'activo' sea un booleano
                this.formData.activo = !!parseInt(initialConfig.activo);
            }
        },
        save(formElement) {
            const url = formElement.action;
            this.$dispatch('open-confirm-password-modal', {
                callback: (password) => {
                    return this.executeSave(password, url);
                }
            });
        },

        executeSave(password, url) {
            this.isSaving = true;
            const payload = { ...this.formData, password: password };

            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw errorData;
                    });
                }
                return response.json();
            })
            .then(data => {
                window.dispatchEvent(new CustomEvent('banner-message', {
                    detail: { style: 'success', message: data.message }
                }));
                this.open = false;
            })
            .finally(() => {
                this.isSaving = false;
            });
        }
    }));

    Alpine.data('notificationsPanel', () => ({
        panelOpen: false,
        notifications: [],
        unreadCount: 0,
        isLoading: true,
        init() {
            this.fetchNotifications();
        },
        fetchNotifications() {
            this.isLoading = true;
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.isLoading = false;
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    this.isLoading = false;
                });
        },
        togglePanel() {
            this.panelOpen = !this.panelOpen;
            if (this.panelOpen) {
                this.fetchNotifications();
            }
        },
        closePanel() {
            this.panelOpen = false;
        },
        timeAgo(dateString) {
            const date = new Date(dateString);
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " años";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " meses";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " días";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " horas";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutos";
            return Math.floor(seconds) + " segundos";
        },
        handleNotificationClick(notification) {
            fetch(`/notifications/${notification.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    this.fetchNotifications();
                }
            });
        },
        deleteNotification(notificationId) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta notificación?')) return;

            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(() => {
                this.fetchNotifications();
            });
        },
        markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(() => {
                this.fetchNotifications();
            });
        }
    }));

    Alpine.data('productorForm', () => ({
        show: false,
        loading: false,
        formError: '',
        errors: {},
        formData: {
            nombre: '',
            dni: '',
            cuil: '',
            email: '',
            telefono: '',
            municipio: '',
            paraje: '',
            direccion: ''
        },
        openModal() {
            this.resetForm();
            this.show = true;
        },
        resetForm() {
            this.formData = { nombre: '', dni: '', cuil: '', email: '', telefono: '', municipio: '', paraje: '', direccion: '' };
            this.errors = {};
            this.formError = '';
            this.loading = false;
        },
        async save() {
            this.loading = true;
            this.errors = {};
            this.formError = '';

            try {
                const response = await fetch('/superadmin/productores', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.status === 422) {
                    const data = await response.json();
                    this.errors = data.errors;
                    throw new Error('Error de validación');
                }

                if (!response.ok) {
                    const data = await response.json();
                    this.formError = data.message || 'Ocurrió un error en el servidor.';
                    throw new Error('Error en el servidor');
                }

                const data = await response.json();
                
                window.dispatchEvent(new CustomEvent('banner-message', { detail: { style: 'success', message: data.message } }));
                window.dispatchEvent(new CustomEvent('productorSaved'));

                this.show = false;

            } catch (error) {
                console.error('Error al guardar productor:', error);
                if (!this.formError && Object.keys(this.errors).length === 0) {
                    this.formError = 'No se pudo completar la solicitud. Revisa tu conexión o contacta a soporte.';
                }
            } finally {
                this.loading = false;
            }
        }
    }));

    Alpine.data('institucionForm', () => ({
        show: false,
        formData: {
            solicitud_id: null,
            nombre: '',
            cuit: '',
            contacto_email: '',
            email_secundario: '',
            telefono: '',
            localidad: '',
            provincia: ''
        },
        openModal(detail) {
            this.resetForm();
            if (detail && detail.solicitud) {
                const s = detail.solicitud;
                this.formData.solicitud_id = s.id;
                this.formData.nombre = s.nombre_institucion;
                this.formData.cuit = s.cuit;
                this.formData.contacto_email = s.email_contacto;
                this.formData.telefono = s.telefono_contacto;
                this.formData.localidad = s.localidad;
                this.formData.provincia = s.provincia || 'Neuquén';
            } else {
                this.formData.provincia = 'Neuquén';
            }
            this.show = true;
        },
        resetForm() {
            this.formData = { solicitud_id: null, nombre: '', cuit: '', contacto_email: '', email_secundario: '', telefono: '', localidad: '', provincia: '' };
        }
    }));

    Alpine.data('editProductorModal', () => ({
        show: false,
        loading: false,
        formError: '',
        errors: {},
        productorId: null,
        formData: {
            nombre: '',
            dni: '',
            cuil: '',
            email: '',
            telefono: '',
            municipio: '',
            paraje: '',
            direccion: ''
        },

        openModal(id) {
            this.resetForm();
            this.productorId = id;
            this.show = true;
            this.loading = true;
            fetch(`/superadmin/productores/${id}`)
                .then(response => response.json())
                .then(data => {
                    this.formData = data;
                })
                .catch(error => {
                    console.error('Error fetching productor data:', error);
                    this.formError = 'Error al cargar los datos del productor.';
                })
                .finally(() => this.loading = false);
        },

        resetForm() {
            this.formData = { nombre: '', dni: '', cuil: '', email: '', telefono: '', municipio: '', paraje: '', direccion: '' };
            this.errors = {};
            this.formError = '';
            this.loading = false;
            this.productorId = null;
        },

        async updateProductor() {
            this.loading = true;
            this.errors = {};
            this.formError = '';

            try {
                const response = await fetch(`/superadmin/productores/${this.productorId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.status === 422) {
                    const data = await response.json();
                    this.errors = data.errors;
                    throw new Error('Error de validación');
                }

                if (!response.ok) {
                    const data = await response.json();
                    this.formError = data.message || 'Ocurrió un error en el servidor.';
                    throw new Error('Error en el servidor');
                }

                const data = await response.json();
                
                window.dispatchEvent(new CustomEvent('banner-message', { detail: { style: 'success', message: data.message } }));
                
                if (data.dispatch_event) {
                    window.Livewire.dispatch(data.dispatch_event);
                }

                this.show = false;

            } catch (error) {
                console.error('Error al actualizar productor:', error);
                if (!this.formError && Object.keys(this.errors).length === 0) {
                    this.formError = 'No se pudo completar la solicitud. Revisa tu conexión o contacta a soporte.';
                }
            } finally {
                this.loading = false;
            }
        }
    }));

    Alpine.data('editInstitucionModal', () => ({
        show: false,
        loading: false,
        formError: '',
        errors: {},
        institucionId: null,
        formData: {
            nombre: '',
            cuit: '',
            contacto_email: '',
            localidad: '',
            provincia: '',
            descripcion: ''
        },

        openModal(id) {
            this.resetForm();
            this.institucionId = id;
            this.show = true;
            this.loading = true;
            fetch(`/superadmin/instituciones/${id}`)
                .then(response => response.json())
                .then(data => {
                    this.formData = data;
                })
                .catch(error => {
                    console.error('Error fetching institucion data:', error);
                    this.formError = 'Error al cargar los datos de la institución.';
                })
                .finally(() => this.loading = false);
        },

        resetForm() {
            this.formData = { nombre: '', cuit: '', contacto_email: '', localidad: '', provincia: '', descripcion: '' };
            this.errors = {};
            this.formError = '';
            this.loading = false;
            this.institucionId = null;
        },

        async updateInstitucion() {
            this.loading = true;
            this.errors = {};
            this.formError = '';

            try {
                const response = await fetch(`/superadmin/instituciones/${this.institucionId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.status === 422) {
                    const data = await response.json();
                    this.errors = data.errors;
                    throw new Error('Error de validación');
                }

                if (!response.ok) {
                    const data = await response.json();
                    this.formError = data.message || 'Ocurrió un error en el servidor.';
                    throw new Error('Error en el servidor');
                }

                const data = await response.json();
                
                window.dispatchEvent(new CustomEvent('banner-message', { detail: { style: 'success', message: data.message } }));
                
                this.show = false;
                window.location.reload(); // Recargar para ver cambios

            } catch (error) {
                console.error('Error al actualizar institución:', error);
                if (!this.formError && Object.keys(this.errors).length === 0) {
                    this.formError = 'No se pudo completar la solicitud. Revisa tu conexión o contacta a soporte.';
                }
            } finally {
                this.loading = false;
            }
        }
    }));

    Alpine.data('productorPerfilModal', () => ({
        show: false,
        loading: true,
        isSaving: false,
        formError: '',
        errors: {},
        activeTab: 'personal',
        formData: {
            nombre: '',
            fecha_nacimiento: '',
            dni: '',
            cuil: '',
            municipio: '',
            paraje: '',
            direccion: '',
            telefono: '',
            email: '',
        },

        openModal() {
            this.resetForm();
            this.show = true;
            this.loadProfileData();
        },

        loadProfileData() {
            this.loading = true;
            fetch('/productor/perfil')
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar el perfil.');
                    return response.json();
                })
                .then(data => {
                    this.formData = data;
                })
                .catch(error => {
                    this.formError = error.message;
                    console.error('Error:', error);
                })
                .finally(() => this.loading = false);
        },

        resetForm() {
            this.formData = { nombre: '', fecha_nacimiento: '', dni: '', cuil: '', municipio: '', paraje: '', direccion: '', telefono: '', email: '' };
            this.errors = {};
            this.formError = '';
            this.isSaving = false;
            this.loading = true;
            this.activeTab = 'personal';
        },

        async save(event) {
            this.isSaving = true;
            this.errors = {};
            this.formError = '';

            try {
                const response = await fetch(event.target.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                        throw new Error('Error de validación');
                    }
                    throw new Error(data.message || 'Error del servidor');
                }

                window.dispatchEvent(new CustomEvent('banner-message', { detail: { style: 'success', message: data.message } }));
                this.show = false;

            } catch (error) {
                console.error('Error al guardar perfil:', error);
                if (error.message !== 'Error de validación') {
                    this.formError = error.message;
                }
            } finally {
                this.isSaving = false;
            }
        }
    }));

    Alpine.data('confirmPasswordModal', () => ({
        open: false,
        password: '',
        error: '',
        isSaving: false,
        callback: null,
        init() {
            document.addEventListener('open-confirm-password-modal', (event) => {
                this.password = '';
                this.error = '';
                this.isSaving = false;
                this.callback = event.detail.callback;
                this.open = true;
                this.$nextTick(() => this.$refs.passwordInput.focus());
            });
        },
        submit() {
            if (!this.password) {
                this.error = 'La contraseña no puede estar vacía.';
                return;
            }
            this.isSaving = true;
            this.error = '';

            if (typeof this.callback === 'function') {
                this.callback(this.password)
                    .then(() => {
                        this.open = false;
                        this.password = '';
                    })
                    .catch(error => {
                        if (error.errors && error.errors.password) {
                            this.error = error.errors.password[0];
                        } else {
                            this.error = 'La contraseña o la petición fallaron.';
                        }
                    })
                    .finally(() => {
                        this.isSaving = false;
                    });
            } else {
                console.error('Callback no definido para el modal de confirmación.');
                this.isSaving = false;
            }
        }
    }));
});
// Iniciar Alpine después de registrar los listeners anteriores
Alpine.start();
