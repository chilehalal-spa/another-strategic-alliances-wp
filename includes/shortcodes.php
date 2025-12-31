<?php
/**
 * Renderiza el shortcode del widget delegando a plantillas y cargando assets bajo demanda.
 * @param array $atts Atributos del shortcode.
 * @return string HTML generado.
 */
function another_sa_widget_render($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'title' => '' 
    ], $atts);

    $post_id = intval($atts['id']);
    if (!$post_id) return '';

    // Recuperación de metadatos
    $layout = get_post_meta($post_id, '_another_layout', true) ?: 'slider';
    $color  = get_post_meta($post_id, '_another_title_color', true) ?: '#947e1e';
    $show_names = get_post_meta($post_id, '_another_show_names', true);
    
    // --- NUEVO: Recuperar dimensiones ---
    $custom_w = get_post_meta($post_id, '_another_img_width', true);
    $custom_h = get_post_meta($post_id, '_another_img_height', true);

    // Creamos una cadena de estilos inline si existen los valores
    $img_inline_style = '';
    if (!empty($custom_w)) {
        $img_inline_style .= 'max-width: ' . intval($custom_w) . 'px; ';
    }
    if (!empty($custom_h)) {
        $img_inline_style .= 'max-height: ' . intval($custom_h) . 'px; ';
    }
    
    $items_json = get_post_meta($post_id, '_another_items', true);
    $items  = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) return '';

    $display_title = !empty($atts['title']) ? $atts['title'] : get_the_title($post_id);
    $extra_class = $show_names ? 'has-names' : '';

    // --- CARGA CONDICIONAL DE CSS ---
    if ($layout === 'slider') {
        wp_enqueue_style('another-sa-slider');
    } else {
        wp_enqueue_style('another-sa-grid');
    }

    ob_start();
    
    // Incluimos la plantilla (que ahora recibirá $img_inline_style)
    if ($layout === 'slider') {
        include ANOTHER_SA_PATH . 'templates/slider-view.php';
    } else {
        include ANOTHER_SA_PATH . 'templates/grid-view.php';
    }

    return ob_get_clean();
}
add_shortcode('another_widget', 'another_sa_widget_render');