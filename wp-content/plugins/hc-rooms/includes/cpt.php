<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function hc_rooms_register_cpt() {
    register_post_type( 'room', array(
        'labels' => array(
            'name'               => __( 'Rooms', 'hc-rooms' ),
            'singular_name'      => __( 'Room', 'hc-rooms' ),
            'menu_name'          => __( 'Rooms', 'hc-rooms' ),
            'add_new'            => __( 'Add Room', 'hc-rooms' ),
            'add_new_item'       => __( 'Add New Room', 'hc-rooms' ),
            'edit_item'          => __( 'Edit Room', 'hc-rooms' ),
            'new_item'           => __( 'New Room', 'hc-rooms' ),
            'view_item'          => __( 'View Room', 'hc-rooms' ),
            'search_items'       => __( 'Search Rooms', 'hc-rooms' ),
            'not_found'          => __( 'No rooms found', 'hc-rooms' ),
            'not_found_in_trash' => __( 'No rooms in Trash', 'hc-rooms' ),
        ),
        'public'              => true,
        'show_in_rest'        => true,
        // Archive disabled so the /rooms/ URL resolves to the Page we generate
        // with the editable Divi-native rooms table layout. Without this the
        // CPT archive would win and show the default Divi blog template.
        'has_archive'         => false,
        'rewrite'             => array( 'slug' => 'room', 'with_front' => false ),
        'menu_icon'           => 'dashicons-admin-home',
        'menu_position'       => 20,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'capability_type'     => 'post',
    ) );
}
add_action( 'init', 'hc_rooms_register_cpt' );

/**
 * Make the Divi Builder available on the Room CPT so single rooms can use the Theme Builder template.
 */
add_filter( 'et_builder_post_types', function ( $types ) {
    $types[] = 'room';
    return $types;
} );

add_filter( 'et_fb_post_types', function ( $types ) {
    $types[] = 'room';
    return $types;
} );

/**
 * Pretty permalinks: /room/{slug}/ — already handled by the rewrite slug above.
 * Archive at /rooms/ (matches the original /rooms URL).
 */
