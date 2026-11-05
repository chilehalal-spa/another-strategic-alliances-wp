<?php
class Another_SA_Admin_Page {

    /**
     * Inicializa los hooks de administración.
     */
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'register_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_meta_data']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    /**
     * Encola los scripts y estilos necesarios para el panel de administración.
     * @param string $hook La página actual del admin.
     */
    public static function enqueue_assets($hook) {
        global $post;
        if (($hook === 'post-new.php' || $hook === 'post.php')) {
            if (isset($post) && $post->post_type === 'another_widget') {
                wp_enqueue_media();
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
                wp_enqueue_script('jquery-ui-sortable');

                wp_enqueue_script(
                    'another-sa-admin-app',
                    ANOTHER_SA_URL . 'assets/js/admin-gallery-app.js',
                    ['jquery', 'jquery-ui-sortable'],
                    '3.1.0',
                    true
                );

                wp_enqueue_style(
                    'another-sa-admin-css', 
                    ANOTHER_SA_URL . 'assets/css/admin-gallery.css', 
                    [], 
                    '3.1.0'
                );
            }
        }
    }

    /**
     * Registra las cajas (meta boxes) en la pantalla de edición.
     */
    public static function register_meta_boxes() {
        add_meta_box(
            'another_sa_gallery_box', 
            'Galería de Logos', 
            [__CLASS__, 'render_gallery_box'], 
            'another_widget', 
            'normal', 
            'high'
        );

        add_meta_box(
            'another_sa_inspector_box', 
            'Detalles del Logo Seleccionado', 
            [__CLASS__, 'render_inspector_box'], 
            'another_widget', 
            'side', 
            'high'
        );

        add_meta_box(
            'another_sa_settings_box', 
            'Configuración General', 
            [__CLASS__, 'render_settings_box'], 
            'another_widget', 
            'side', 
            'default'
        );
    }

    /**
     * Renderiza la Grid principal.
     */
    public static function render_gallery_box($post) {
        $json_value = get_post_meta($post->ID, '_another_items', true);
        if (empty($json_value)) $json_value = '[]';
        
        wp_nonce_field('another_sa_save_action', 'another_sa_nonce');
        ?>
        <div id="another-gallery-wrapper">
            <div class="another-gallery-toolbar" style="margin-bottom: 15px; text-align: right;">
                <button type="button" class="button button-primary" id="another-btn-add-images">
                    <span class="dashicons dashicons-plus-alt2" style="position:relative; top:3px;"></span> 
                    Añadir Imágenes (Bulk)
                </button>
            </div>
            
            <div id="another-gallery-grid" class="another-gallery-grid">
                </div>

            <textarea name="another_items" id="another_items_json" style="display:none;"><?php echo esc_textarea($json_value); ?></textarea>
        </div>
        <p class="description">Arrastra para reordenar. Haz clic en una imagen para editar sus detalles en la barra lateral.</p>
        <?php
    }

    /**
     * Renderiza el Inspector lateral.
     */
    public static function render_inspector_box($post) {
        ?>
        <div id="another-gallery-inspector" class="another-inspector-standalone">
            <div class="inspector-content">
                </div>
        </div>
        <?php
    }

    /**
     * Renderiza la configuración general (Layout, Colores, Dimensiones).
     */
    public static function render_settings_box($post) {
        $layout = get_post_meta($post->ID, '_another_layout', true) ?: 'slider';
        $grid_cols = get_post_meta($post->ID, '_another_grid_cols', true) ?: 4;
        $color  = get_post_meta($post->ID, '_another_title_color', true) ?: '#947e1e';
        $show_names = get_post_meta($post->ID, '_another_show_names', true);
        $mobile_scroll = get_post_meta($post->ID, '_another_mobile_scroll', true);

        // Nuevos campos de dimensiones
        $img_width = get_post_meta($post->ID, '_another_img_width', true);
        $img_height = get_post_meta($post->ID, '_another_img_height', true);
        ?>
        <p><strong>Layout:</strong></p>
        <select name="another_layout" style="width:100%; margin-bottom:10px;">
            <option value="slider" <?php selected($layout, 'slider'); ?>>Carrusel (Slider)</option>
            <option value="grid" <?php selected($layout, 'grid'); ?>>Cuadrícula (Grid)</option>
        </select>

        <label>Columnas (Solo Grid):</label>
        <input type="number" name="another_grid_cols" value="<?php echo esc_attr($grid_cols); ?>" min="1" max="12" style="width:60px;">

        <label style="display:block; margin-top:8px;">
            <input type="checkbox" name="another_mobile_scroll" value="1" <?php checked($mobile_scroll, '1'); ?>>
            En teléfono: franja deslizable (Solo Grid)
        </label>
        <p class="description" style="font-size:11px;">Para listas largas: dos filas que se deslizan de lado en vez de una columna muy alta.</p>

        <hr>
        <p><strong>Dimensiones Máximas (Armonía):</strong></p>
        <p class="description" style="font-size:11px; margin-bottom:5px;">Deja vacío para usar defecto. Acepta px, %, rem.</p>
        
        <label style="display:block; margin-bottom:3px;">Ancho Máx (Max-Width):</label>
        <input type="text" name="another_img_width" value="<?php echo esc_attr($img_width); ?>" placeholder="ej. 150px o 100%" style="width:100%; margin-bottom:8px;">

        <label style="display:block; margin-bottom:3px;">Alto Máx (Max-Height):</label>
        <input type="text" name="another_img_height" value="<?php echo esc_attr($img_height); ?>" placeholder="ej. 80px" style="width:100%;">

        <hr>
        <label>
            <input type="checkbox" name="another_show_names" value="1" <?php checked($show_names, '1'); ?>>
            Mostrar Títulos
        </label>
        
        <hr>
        <p><strong>Color Título:</strong></p>
        <input type="text" name="another_title_color" value="<?php echo esc_attr($color); ?>" class="another-color-field" />
        <script>jQuery(document).ready(function($){ $('.another-color-field').wpColorPicker(); });</script>
        
        <hr>
        <code style="display:block; padding:10px; background:#f0f0f1; font-size:11px;">[another_widget id="<?php echo $post->ID; ?>"]</code>
        <?php
    }

    /**
     * Guarda los metadatos del post.
     */
    public static function save_meta_data($post_id) {
        if (!isset($_POST['another_sa_nonce']) || !wp_verify_nonce($_POST['another_sa_nonce'], 'another_sa_save_action')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['another_items'])) update_post_meta($post_id, '_another_items', wp_unslash($_POST['another_items']));
        if (isset($_POST['another_layout'])) update_post_meta($post_id, '_another_layout', sanitize_key($_POST['another_layout']));
        if (isset($_POST['another_grid_cols'])) update_post_meta($post_id, '_another_grid_cols', intval($_POST['another_grid_cols']));
        if (isset($_POST['another_title_color'])) update_post_meta($post_id, '_another_title_color', sanitize_hex_color($_POST['another_title_color']));
        
        // Guardar dimensiones permitiendo texto (px, %, etc)
        if (isset($_POST['another_img_width'])) update_post_meta($post_id, '_another_img_width', sanitize_text_field($_POST['another_img_width']));
        if (isset($_POST['another_img_height'])) update_post_meta($post_id, '_another_img_height', sanitize_text_field($_POST['another_img_height']));

        $show_names = isset($_POST['another_show_names']) ? '1' : '';
        update_post_meta($post_id, '_another_show_names', $show_names);

        $mobile_scroll = isset($_POST['another_mobile_scroll']) ? '1' : '';
        update_post_meta($post_id, '_another_mobile_scroll', $mobile_scroll);
    }
}