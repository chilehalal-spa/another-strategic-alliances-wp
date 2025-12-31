<?php
/**
 * Renderiza el shortcode del widget delegando a plantillas.
 * * @param array $atts Atributos del shortcode.
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
    
    $items_json = get_post_meta($post_id, '_another_items', true);
    $items  = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) return '';

    $display_title = !empty($atts['title']) ? $atts['title'] : get_the_title($post_id);
    
    // Clase auxiliar para estilos CSS
    $extra_class = $show_names ? 'has-names' : '';

    ob_start();
    
    // Carga dinámica de la plantilla basada en el layout
    // Se asume que ANOTHER_SA_PATH está definido en el archivo principal del plugin
    if ($layout === 'slider') {
        include ANOTHER_SA_PATH . 'templates/slider-view.php';
    } else {
        include ANOTHER_SA_PATH . 'templates/grid-view.php';
    }

    return ob_get_clean();
}
add_shortcode('another_widget', 'another_sa_widget_render');