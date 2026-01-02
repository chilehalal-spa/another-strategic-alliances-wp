<?php
class Another_SA_CPT {

    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);
        // NOTA: Ya no llamamos a add_meta_boxes aquí, lo hace la clase Admin_Page
    }

    public static function register_cpt() {
        register_post_type('another_widget', [
            'labels' => [
                'name'               => 'Widgets de Logos',
                'singular_name'      => 'Widget',
                'menu_name'          => 'Widgets de Logos',
                'add_new'            => 'Añadir Nuevo',
                'add_new_item'       => 'Añadir Nuevo Widget'
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-images-alt2',
            'supports'           => ['title'],
            'capability_type'    => 'post',
        ]);
    }
}
Another_SA_CPT::init();