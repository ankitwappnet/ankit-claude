<?php
/* Enqueue Script and Style */
function dt_enqueue_styles()
{
    $parenthandle = 'divi-style';
    $theme = wp_get_theme();

    wp_enqueue_style(
        $parenthandle,
        get_template_directory_uri() . '/style.css',
        array(),
        $theme->parent()->get('Version')
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        array($parenthandle),
        $theme->get('Version')
    );
    
    wp_enqueue_script( 'div-child-script', get_stylesheet_directory_uri() . '/custom-script.js', array('jquery'), $theme->get('Version'), true );
    wp_localize_script('divi-child-script', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'dt_enqueue_styles');

/* Enqueue  SweetAlert Script and Style */
