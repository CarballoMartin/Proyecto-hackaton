document.addEventListener('alpine:init', () => {
    if (!Alpine.store('cuaderno')) {
        Alpine.store('cuaderno', {
            summary: {
                items: [],
                get total() {
                    return this.items.reduce((acc, item) => acc + parseInt(item.cantidad), 0);
                }
            },
            addMovementToSummary(movement) {
                const existing = this.summary.items.find(item =>
                    item.especie_id == movement.especie_id &&
                    item.categoria_id == movement.categoria_id &&
                    item.raza_id == movement.raza_id &&
                    item.motivo_movimiento_id == movement.motivo_movimiento_id &&
                    item.destino_traslado == movement.destino_traslado
                );

                if (existing) {
                    existing.cantidad = parseInt(existing.cantidad) + parseInt(movement.cantidad);
                } else {
                    this.summary.items.push(movement);
                }
            },
            removeMovementFromSummary(movementId) {
                const index = this.summary.items.findIndex(item => item.id === movementId);
                if (index > -1) {
                    this.summary.items.splice(index, 1);
                }
            },
            clearSummary() {
                this.summary.items = [];
            }
        });
    }

    Alpine.data('cuadernoDeCampo', (initialData) => ({
        // --- DATA FROM CONTROLLER ---
        unidadesProductivas: initialData.unidadesProductivasData,
        stockActualPorUP: initialData.stockActualPorUPData,
        especies: initialData.especiesData,
        categorias: initialData.categoriasData,
        razas: initialData.razasData,
        motivos: initialData.motivosData,

        // --- UI STATE ---
        selectedUpId: initialData.selectedUpId || null,
        showModal: false,
        registrationType: null, // 'altas' or 'bajas'
        showTotalStock: false, // Show by default now
        showMovimientosGuardados: false, // Show by default now
        dayFilter: 'todos',
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        
        init() {
            // Convert array to object for easier access
            if (Array.isArray(this.stockActualPorUP)) {
                const stockByUp = {};
                this.stockActualPorUP.forEach(stock => {
                    if (!stockByUp[stock.unidad_productiva_id]) {
                        stockByUp[stock.unidad_productiva_id] = [];
                    }
                    stockByUp[stock.unidad_productiva_id].push(stock);
                });
                this.stockActualPorUP = stockByUp;
            }
        },
        
        // --- MODAL FORM STATE ---
        form: {
            especie_id: '',
            categoria_id: '',
            raza_id: '',
            cantidad: 1,
            motivo_movimiento_id: '',
            destino_traslado: ''
        },

        // --- COMPUTED-LIKE PROPERTIES ---
        get selectedUp() {
            if (!this.selectedUpId) return null;
            return this.unidadesProductivas.find(up => up.id == this.selectedUpId);
        },
        get filteredMovements() {
            return this.$store.cuaderno.summary.items;
        },
        get selectedStock() {
            return this.stockActualPorUP[this.selectedUpId] || [];
        },
        get filteredCategorias() {
            if (!this.form.especie_id) return [];
            return this.categorias.filter(c => c.especie_id == this.form.especie_id);
        },
        get filteredRazas() {
            if (!this.form.especie_id) return [];
            return this.razas.filter(r => r.especie_id == this.form.especie_id);
        },

        // --- METHODS ---
        openModal(type) {
            this.registrationType = type;
            this.showModal = true;
        },
        addMovement() {
            // Basic validation
            if (!this.form.especie_id || !this.form.categoria_id || !this.form.raza_id || !this.form.motivo_movimiento_id || this.form.cantidad < 1) {
                Alpine.store('notification').notify('Por favor, complete todos los campos del formulario.', true);
                return;
            }

            const selectedMotivo = this.motivos[this.registrationType.slice(0, -1)]?.find(m => m.id == this.form.motivo_movimiento_id);
            if (selectedMotivo && selectedMotivo.nombre.toLowerCase().includes('traslado') && !this.form.destino_traslado) {
                Alpine.store('notification').notify('El destino del traslado es obligatorio para este motivo.', true);
                return;
            }

            const especie = this.especies.find(e => e.id == this.form.especie_id);
            const categoria = this.categorias.find(c => c.id == this.form.categoria_id);
            const raza = this.razas.find(r => r.id == this.form.raza_id);

            const movement = {
                id: Date.now() + Math.random(), // Unique ID for the session
                ...this.form,
                especie_nombre: especie.nombre,
                categoria_nombre: categoria.nombre,
                raza_nombre: raza.nombre,
                motivo_nombre: selectedMotivo ? selectedMotivo.nombre : 'N/A',
                tipo: this.registrationType,
                createdAt: new Date()
            };

            Alpine.store('cuaderno').addMovementToSummary(movement);

            this.showModal = false;
            this.form = { especie_id: '', categoria_id: '', raza_id: '', cantidad: 1, motivo_movimiento_id: '', destino_traslado: '' };
        },
        cancelChanges() {
            Alpine.store('cuaderno').clearSummary();
        },
        updateMovimientosGuardados() {
            if (!this.selectedUpId) return;

            const url = new URL(initialData.filtrarUrl);
            url.searchParams.append('day_filter', this.dayFilter);
            // Asegurarnos de que el ID de la UP también se envíe si es necesario en el futuro
            // url.searchParams.append('up_id', this.selectedUpId);

            // Opcional: mostrar un loader
            this.$refs.movimientosGuardadosBody.innerHTML = '<tr><td colspan="6" class="text-center p-4">Cargando...</td></tr>';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.text())
            .then(html => {
                this.$refs.movimientosGuardadosBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al filtrar movimientos:', error);
                this.$refs.movimientosGuardadosBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-red-500">Error al cargar los movimientos.</td></tr>';
            });
        }
    }));
});
