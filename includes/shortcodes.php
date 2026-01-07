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

    // Recuperación de metadatos básicos
    $layout = get_post_meta($post_id, '_another_layout', true) ?: 'slider';
    $color  = get_post_meta($post_id, '_another_title_color', true) ?: '#947e1e';
    $show_names = get_post_meta($post_id, '_another_show_names', true);
    
    // Recuperar configuración de columnas (Default 4)
    $grid_cols = get_post_meta($post_id, '_another_grid_cols', true);
    if (!$grid_cols) $grid_cols = 4;

    // Dimensiones personalizadas (Ahora aceptan strings como '50%' o '100px')
    $custom_w = get_post_meta($post_id, '_another_img_width', true);
    $custom_h = get_post_meta($post_id, '_another_img_height', true);

    $img_inline_style = '';
    
    // Si el usuario introdujo un valor, lo usamos tal cual (sanitizado previamente)
    if (!empty($custom_w)) {
        // Añadimos 'px' solo si es numérico puro, para retrocompatibilidad/comodidad
        if (is_numeric($custom_w)) {
            $img_inline_style .= 'max-width: ' . intval($custom_w) . 'px; ';
        } else {
            $img_inline_style .= 'max-width: ' . esc_attr($custom_w) . '; ';
        }
    }
    
    if (!empty($custom_h)) {
        if (is_numeric($custom_h)) {
            $img_inline_style .= 'max-height: ' . intval($custom_h) . 'px; ';
        } else {
            $img_inline_style .= 'max-height: ' . esc_attr($custom_h) . '; ';
        }
    }
    
    $items_json = get_post_meta($post_id, '_another_items', true);
    $items  = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) return '';

    $display_title = !empty($atts['title']) ? $atts['title'] : get_the_title($post_id);
    $extra_class = $show_names ? 'has-names' : '';

    if ($layout === 'slider') {
        wp_enqueue_style('another-sa-slider');
    } else {
        wp_enqueue_style('another-sa-grid');
    }

    ob_start();
    
    if ($layout === 'slider') {
        include ANOTHER_SA_PATH . 'templates/slider-view.php';
    } else {
        include ANOTHER_SA_PATH . 'templates/grid-view.php';
    }

    return ob_get_clean();
}
add_shortcode('another_widget', 'another_sa_widget_render');