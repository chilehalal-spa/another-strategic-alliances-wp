<?php
/**
 * Plugin Name: Another Display Logos
 * Description: Crea widgets Grids y Sliders mediante Custom Post Types.
 * Version: 1.4.1
 * Author: Zumito
 * Text Domain: another-sa
 */

if (!defined('ABSPATH')) exit; 

define('ANOTHER_SA_PATH', plugin_dir_path(__FILE__));
define('ANOTHER_SA_URL', plugin_dir_url(__FILE__));

// Cargamos el gestor de CPT y Shortcodes
require_once ANOTHER_SA_PATH . 'includes/class-cpt.php';
require_once ANOTHER_SA_PATH . 'includes/shortcodes.php';

// Frontend Assets
function another_sa_enqueue_scripts() {
    // 1. Estilos Comunes (Base) - Se cargan siempre o se pueden condicionar también
    wp_enqueue_style('another-sa-common', ANOTHER_SA_URL . 'assets/css/style.css', [], '2.1.0');

    // 2. Swiper (Librería externa) - Se mantiene igual
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.0.0');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.0.0', true);

    // 3. Estilos Específicos (Solo REGISTRO, se encolan en el shortcode)
    wp_register_style('another-sa-slider', ANOTHER_SA_URL . 'assets/css/slider.css', ['another-sa-common', 'swiper-css'], '2.1.0');
    wp_register_style('another-sa-grid', ANOTHER_SA_URL . 'assets/css/grid.css', ['another-sa-common'], '2.1.0');

    // 4. JS Frontend
    wp_enqueue_script('another-sa-frontend', ANOTHER_SA_URL . 'assets/js/frontend.js', ['swiper-js'], '2.0.0', true);
}
add_action('wp_enqueue_scripts', 'another_sa_enqueue_scripts');

// Admin Assets (Solo en la edición de nuestro CPT)
function another_sa_admin_assets($hook) {
    global $post;

    // Verificar si estamos en la edición del post type 'another_widget'
    if (($hook === 'post-new.php' || $hook === 'post.php')) {
        if (isset($post) && $post->post_type === 'another_widget') {
            
            wp_enqueue_media();
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('jquery-ui-sortable');

            wp_enqueue_script(
                'another-sa-repeater-js',
                ANOTHER_SA_URL . 'assets/js/admin-repeater.js',
                ['jquery', 'jquery-ui-sortable', 'wp-color-picker'], 
                '2.0.0',
                true
            );

            wp_enqueue_style('another-sa-repeater-css', ANOTHER_SA_URL . 'assets/css/admin-repeater.css', [], '2.0.0');
        }
    }
}
add_action('admin_enqueue_scripts', 'another_sa_admin_assets');