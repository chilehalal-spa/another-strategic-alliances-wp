<?php
class Another_SA_CPT {

    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_meta_data']);
        add_filter('manage_another_widget_posts_columns', [__CLASS__, 'custom_columns']);
        add_action('manage_another_widget_posts_custom_column', [__CLASS__, 'custom_columns_content'], 10, 2);
    }

    public static function register_cpt() {
        register_post_type('another_widget', [
            'labels' => [
                'name'               => 'Widgets de Logos',
                'singular_name'      => 'Widget',
                'add_new'            => 'Añadir Nuevo Widget',
                'add_new_item'       => 'Añadir Nuevo Widget de Logos',
                'edit_item'          => 'Editar Widget',
                'new_item'           => 'Nuevo Widget',
                'all_items'          => 'Todos los Widgets',
                'menu_name'          => 'Widgets de Logos'
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-images-alt2',
            'supports'           => ['title'],
            'capability_type'    => 'post',
        ]);
    }

    public static function add_meta_boxes() {
        add_meta_box('another_sa_settings_box', 'Configuración del Widget', [__CLASS__, 'render_settings_box'], 'another_widget', 'side', 'high');
        add_meta_box('another_sa_items_box', 'Logos / Elementos', [__CLASS__, 'render_items_box'], 'another_widget', 'normal', 'high');
        add_meta_box('another_sa_shortcode_box', 'Shortcode', [__CLASS__, 'render_shortcode_box'], 'another_widget', 'side', 'low');
    }

    public static function render_settings_box($post) {
        $layout = get_post_meta($post->ID, '_another_layout', true) ?: 'slider';
        $color  = get_post_meta($post->ID, '_another_title_color', true) ?: '#1a428a';
        $show_names = get_post_meta($post->ID, '_another_show_names', true); 
        
        // --- NUEVO: Recuperar dimensiones ---
        $img_width  = get_post_meta($post->ID, '_another_img_width', true);
        $img_height = get_post_meta($post->ID, '_another_img_height', true);

        wp_nonce_field('another_sa_save_action', 'another_sa_nonce');
        ?>
        <p>
            <label><strong>Tipo de Visualización:</strong></label><br>
            <select name="another_layout" style="width:100%; margin-top:5px;">
                <option value="slider" <?php selected($layout, 'slider'); ?>>Carrusel (Slider)</option>
                <option value="grid" <?php selected($layout, 'grid'); ?>>Cuadrícula (Grid)</option>
            </select>
        </p>
        <hr>
        <p>
            <label>
                <input type="checkbox" name="another_show_names" value="1" <?php checked($show_names, '1'); ?>>
                <strong>Mostrar Nombres de Items</strong>
            </label>
        </p>
        <hr>
        <p><strong>Tamaño de Logos (Opcional)</strong></p>
        <div style="display:flex; gap:10px;">
            <div>
                <label style="font-size:11px;">Ancho Máx (px)</label>
                <input type="number" name="another_img_width" value="<?php echo esc_attr($img_width); ?>" style="width:100%;" placeholder="Ej: 150">
            </div>
            <div>
                <label style="font-size:11px;">Alto Máx (px)</label>
                <input type="number" name="another_img_height" value="<?php echo esc_attr($img_height); ?>" style="width:100%;" placeholder="Ej: 90">
            </div>
        </div>
        <p><small style="color:#666;">Deja vacío para usar los valores por defecto del CSS.</small></p>
        <hr>
        <p>
            <label><strong>Color del Título:</strong></label><br>
            <input type="text" name="another_title_color" value="<?php echo esc_attr($color); ?>" class="another-color-field" data-default-color="#947e1e" />
        </p>
        <?php
    }

    public static function render_items_box($post) {
        $json_value = get_post_meta($post->ID, '_another_items', true);
        $items = !empty($json_value) ? json_decode($json_value, true) : [];
        if (!is_array($items)) $items = [];
        ?>
        <div class="another-repeater-wrapper">
            <button type="button" class="button button-primary add-repeater-item">
                <span class="dashicons dashicons-plus"></span> Añadir Logo
            </button>
            <ul class="another-repeater-list">
                <?php foreach ($items as $item): ?>
                    <li class="another-repeater-item">
                        <div class="repeater-row">
                            <div class="repeater-col">
                                <label class="field-label">Nombre</label>
                                <input type="text" class="title-field widefat" value="<?php echo esc_attr($item['title'] ?? ''); ?>">
                            </div>
                            <div class="repeater-col">
                                <label class="field-label">Enlace</label>
                                <input type="text" class="url-field widefat" value="<?php echo esc_attr($item['url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="repeater-row media-row">
                            <label class="field-label">Logo</label>
                            <div class="image-upload-controls">
                                <?php 
                                    $img_val = $item['icon'] ?? ''; 
                                    $display = $img_val ? 'display:block;' : 'display:none;';
                                ?>
                                <img src="<?php echo esc_url($img_val); ?>" class="repeater-image-preview" style="<?php echo $display; ?>" />
                                <input type="hidden" class="icon-field" value="<?php echo esc_attr($img_val); ?>">
                                <button type="button" class="button upload-repeater-image">Elegir Imagen</button>
                                <?php if($img_val): ?>
                                    <button type="button" class="button remove-repeater-image" style="color: #a00;">Quitar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="repeater-actions">
                            <span class="drag-handle dashicons dashicons-move"></span>
                            <button type="button" class="button button-link-delete remove-item">Eliminar</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <textarea name="another_items" class="another-repeater-hidden" style="display:none;"><?php echo esc_textarea($json_value); ?></textarea>
        </div>
        <?php
    }

    public static function render_shortcode_box($post) {
        ?>
        <p>Copia y pega este shortcode:</p>
        <code style="display:block; padding:10px; background:#f0f0f1; border:1px solid #ccc;">
            [another_widget id="<?php echo $post->ID; ?>"]
        </code>
        <?php
    }

    public static function save_meta_data($post_id) {
        if (!isset($_POST['another_sa_nonce']) || !wp_verify_nonce($_POST['another_sa_nonce'], 'another_sa_save_action')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['another_layout'])) {
            update_post_meta($post_id, '_another_layout', sanitize_key($_POST['another_layout']));
        }
        
        if (isset($_POST['another_title_color'])) {
            update_post_meta($post_id, '_another_title_color', sanitize_hex_color($_POST['another_title_color']));
        }

        $show_names = isset($_POST['another_show_names']) ? '1' : '';
        update_post_meta($post_id, '_another_show_names', $show_names);

        // --- NUEVO: Guardar Dimensiones ---
        if (isset($_POST['another_img_width'])) {
            update_post_meta($post_id, '_another_img_width', sanitize_text_field($_POST['another_img_width']));
        }
        if (isset($_POST['another_img_height'])) {
            update_post_meta($post_id, '_another_img_height', sanitize_text_field($_POST['another_img_height']));
        }

        if (isset($_POST['another_items'])) {
            $json = wp_unslash($_POST['another_items']);
            update_post_meta($post_id, '_another_items', $json);
        }
    }

    public static function custom_columns($columns) {
        $columns['shortcode'] = 'Shortcode';
        $columns['layout'] = 'Tipo';
        return $columns;
    }

    public static function custom_columns_content($column, $post_id) {
        if ($column === 'shortcode') {
            echo '<code>[another_widget id="' . $post_id . '"]</code>';
        }
        if ($column === 'layout') {
            $layout = get_post_meta($post_id, '_another_layout', true);
            echo ($layout === 'slider') ? 'Carrusel' : 'Cuadrícula';
        }
    }
}
Another_SA_CPT::init();