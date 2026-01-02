/**
 * Admin Gallery App
 * Maneja la lógica de la interfaz Grid Master-Detail.
 */
;(function($) {
    'use strict';

    class AnotherGalleryApp {
        constructor() {
            // Referencias DOM
            this.$container  = $('#another-gallery-wrapper');
            this.$grid       = $('#another-gallery-grid');
            this.$inspector  = $('#another-gallery-inspector');
            this.$hiddenInput = $('#another_items_json');
            
            // Botones Globales
            this.$btnAdd     = $('#another-btn-add-images');
            
            // Estado
            this.items = [];
            this.selectedIndex = null;

            // Inicialización
            this.init();
        }

        init() {
            // 1. Cargar datos iniciales
            this.loadInitialData();

            // 2. Renderizar inicial
            this.renderGrid();
            this.renderInspector();

            // 3. Bindear eventos
            this.bindEvents();
            this.initSortable();
        }

        /**
         * Lee el JSON guardado en el input hidden al cargar la página
         */
        loadInitialData() {
            try {
                const raw = this.$hiddenInput.val();
                this.items = raw ? JSON.parse(raw) : [];
            } catch (e) {
                console.error('Error parseando JSON inicial', e);
                this.items = [];
            }
        }

        /**
         * Guarda el estado actual en el input hidden para que WP lo procese
         */
        saveState() {
            this.$hiddenInput.val(JSON.stringify(this.items));
        }

        /**
         * Configura jQuery UI Sortable para arrastrar y soltar
         */
        initSortable() {
            const self = this;
            this.$grid.sortable({
                placeholder: 'ui-sortable-placeholder',
                tolerance: 'pointer',
                update: function(event, ui) {
                    // Reordenar el array 'items' basado en el DOM
                    const newOrder = [];
                    self.$grid.find('.gallery-item').each(function() {
                        const index = $(this).data('index');
                        // Importante: usamos el objeto original, no clonamos
                        newOrder.push(self.items[index]);
                    });
                    
                    self.items = newOrder;
                    self.selectedIndex = null; // Deseleccionar al mover para evitar conflictos
                    self.saveState();
                    self.renderGrid(); // Re-render para actualizar índices data-index
                    self.renderInspector();
                }
            });
        }

        bindEvents() {
            const self = this;

            // Selección de item en la Grid
            this.$grid.on('click', '.gallery-item', function() {
                const index = $(this).data('index');
                self.selectItem(index);
            });

            // Botón Añadir (Bulk Upload)
            this.$btnAdd.on('click', (e) => {
                e.preventDefault();
                self.openMediaFrame();
            });

            // Inspector: Cambios en inputs (Live Update)
            this.$inspector.on('input', '.inspector-input', function() {
                if (self.selectedIndex === null) return;
                
                const field = $(this).data('field'); // 'title' o 'url'
                self.items[self.selectedIndex][field] = $(this).val();
                self.saveState();
            });

            // Inspector: Eliminar Item
            this.$inspector.on('click', '#inspector-btn-remove', function() {
                if (confirm('¿Eliminar este logo?')) {
                    self.items.splice(self.selectedIndex, 1);
                    self.selectedIndex = null;
                    self.saveState();
                    self.renderGrid();
                    self.renderInspector();
                }
            });

            // Inspector: Cambiar Imagen individual
            this.$inspector.on('click', '#inspector-btn-change-img', function(e) {
                e.preventDefault();
                self.openSingleImageFrame();
            });
        }

        selectItem(index) {
            this.selectedIndex = index;
            // Actualizar UI visual de selección
            this.$grid.find('.gallery-item').removeClass('selected');
            this.$grid.find(`.gallery-item[data-index="${index}"]`).addClass('selected');
            
            this.renderInspector();
        }

        /**
         * Renderiza la cuadrícula izquierda (Solo imágenes)
         */
        renderGrid() {
            this.$grid.empty();

            if (this.items.length === 0) {
                this.$grid.append('<p style="grid-column: 1/-1; text-align:center; color:#ccc;">No hay logos. Añade algunos.</p>');
                return;
            }

            this.items.forEach((item, index) => {
                const isSelected = (index === this.selectedIndex) ? 'selected' : '';
                const html = `
                    <div class="gallery-item ${isSelected}" data-index="${index}">
                        <img src="${item.icon}" alt="Logo">
                    </div>
                `;
                this.$grid.append(html);
            });
        }

        /**
         * Renderiza el panel lateral derecho
         */
        renderInspector() {
            const $content = this.$inspector.find('.inspector-content');
            $content.empty();

            if (this.selectedIndex === null) {
                $content.html(`
                    <div class="inspector-empty-state">
                        <span class="dashicons dashicons-format-image" style="font-size:40px; height:40px; width:40px; margin-bottom:10px;"></span><br>
                        Selecciona un logo para editar sus detalles.
                    </div>
                `);
                return;
            }

            const item = this.items[this.selectedIndex];

            const html = `
                <div class="inspector-form">
                    <div class="inspector-preview-area">
                        <img src="${item.icon}" class="inspector-preview-img" id="inspector-img-preview">
                    </div>
                    <div style="text-align:center; margin-bottom:15px;">
                        <button type="button" class="button button-small" id="inspector-btn-change-img">Cambiar Imagen</button>
                    </div>

                    <div class="form-group">
                        <label>Nombre / Título</label>
                        <input type="text" class="inspector-input" data-field="title" value="${this.escapeHtml(item.title || '')}">
                    </div>

                    <div class="form-group">
                        <label>Enlace (URL)</label>
                        <input type="text" class="inspector-input" data-field="url" value="${this.escapeHtml(item.url || '')}" placeholder="https://...">
                    </div>

                    <div class="inspector-actions">
                        <button type="button" class="button button-link-delete" id="inspector-btn-remove" style="color:#b32d2e;">Eliminar Logo</button>
                    </div>
                </div>
            `;
            $content.html(html);
        }

        /**
         * Abre el selector de medios para Carga Masiva (Multiple)
         */
        openMediaFrame() {
            const self = this;
            const frame = wp.media({
                title: 'Seleccionar Logos',
                button: { text: 'Añadir a la galería' },
                multiple: true // Permite seleccionar varios
            });

            frame.on('select', function() {
                const selection = frame.state().get('selection');
                selection.map(attachment => {
                    const json = attachment.toJSON();
                    // Añadimos al array
                    self.items.push({
                        title: json.title || '',
                        url: '',
                        icon: json.url
                    });
                });
                self.saveState();
                self.renderGrid();
            });

            frame.open();
        }

        /**
         * Abre el selector para cambiar UNA sola imagen
         */
        openSingleImageFrame() {
            const self = this;
            const frame = wp.media({
                title: 'Cambiar Logo',
                button: { text: 'Usar esta imagen' },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                // Actualizamos el item seleccionado
                if (self.selectedIndex !== null) {
                    self.items[self.selectedIndex].icon = attachment.url;
                    self.saveState();
                    self.renderGrid();
                    self.renderInspector(); // Refrescar preview del inspector
                }
            });

            frame.open();
        }

        escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    }

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        if ($('#another-gallery-wrapper').length) {
            new AnotherGalleryApp();
        }
    });

})(jQuery);