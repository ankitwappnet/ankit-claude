<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Seed the 5 room types from the original static site.
 * Runs once when WP-Admin loads — controlled by an option flag so it's idempotent.
 *
 * To re-seed: delete the option `hc_rooms_seeded` from wp_options and reload admin.
 */
add_action( 'admin_init', function () {

    if ( get_option( 'hc_rooms_seeded' ) ) return;
    if ( ! function_exists( 'update_field' ) ) return;

    $shared_amenities_left = array(
        array( 'icon' => 'fa-light fa-mug-tea',     'label' => 'Tea / coffee maker' ),
        array( 'icon' => 'fa-light fa-vault',       'label' => 'Safe deposit box' ),
        array( 'icon' => 'fa-light fa-door-open',   'label' => 'Wardrobe' ),
        array( 'icon' => 'fa-light fa-tv',          'label' => 'LED TV with channels' ),
        array( 'icon' => 'fa-light fa-lamp-desk',   'label' => 'Writing table' ),
        array( 'icon' => 'fa-light fa-refrigerator','label' => 'Mini bar' ),
    );
    $shared_amenities_right = array(
        array( 'icon' => 'fa-light fa-air-conditioner', 'label' => 'Central AC' ),
        array( 'icon' => 'fa-light fa-toothbrush',      'label' => 'Free grooming kit on request' ),
        array( 'icon' => 'fa-light fa-shower',          'label' => '24x7 running hot water' ),
        array( 'icon' => 'fa-light fa-raygun',          'label' => 'Hair dryer' ),
        array( 'icon' => 'fa-light fa-shirt',           'label' => 'Iron & Ironing board on request' ),
        array( 'icon' => 'fa-light fa-wifi',            'label' => 'Free Wi-Fi internet' ),
        array( 'icon' => 'fa-light fa-square-parking', 'label' => 'Parking as per availability' ),
    );

    $booking_options = array(
        array( 'label' => 'Room Only',         'url' => 'https://www.swiftbook.io/inst/#home?propertyId=2166' ),
        array( 'label' => 'Room + Breakfast', 'url' => 'https://www.swiftbook.io/inst/#home?propertyId=2166' ),
    );

    $rooms = array(
        array(
            'slug'  => 'executive-room',
            'title' => 'Executive Room',
            'desc'  => 'Comfortable and well-appointed rooms for corporate and leisure travellers.',
            'bed'   => 'King bed / Twin beds',
            'size'  => '285 sq ft',
            'extra' => array( array( 'icon' => 'fa-light fa-bed-front', 'label' => 'King bed / Twin beds' ),
                              array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 285 sq ft' ) ),
        ),
        array(
            'slug'  => 'premium-room',
            'title' => 'Premium Room',
            'desc'  => 'Spacious rooms with a refined ambience and modern conveniences.',
            'bed'   => 'King bed / Twin beds',
            'size'  => '290 sq ft',
            'extra' => array( array( 'icon' => 'fa-light fa-bed-front', 'label' => 'King bed / Twin beds' ),
                              array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 290 sq ft' ) ),
        ),
        array(
            'slug'  => 'presidential-room',
            'title' => 'Presidential Room',
            'desc'  => 'A premium tier with elevated finishes for the discerning traveller.',
            'bed'   => 'King bed',
            'size'  => '445 sq ft',
            'extra' => array( array( 'icon' => 'fa-light fa-bed-front', 'label' => 'King bed' ),
                              array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 445 sq ft' ) ),
        ),
        array(
            'slug'  => 'luxury-room',
            'title' => 'Luxury Room',
            'desc'  => 'Our most expansive room category, with a bath tub and lounge area.',
            'bed'   => 'King bed',
            'size'  => '430 sq ft',
            'extra' => array( array( 'icon' => 'fa-light fa-bed-front', 'label' => 'King bed' ),
                              array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 430 sq ft' ),
                              array( 'icon' => 'fa-light fa-bath',       'label' => 'Bath Tub' ) ),
        ),
        array(
            'slug'  => 'deluxe-room',
            'title' => 'Deluxe Room',
            'desc'  => 'Thoughtfully curated interiors with bespoke furnishings and artistic touches.',
            'bed'   => 'King bed',
            'size'  => '440 sq ft',
            'extra' => array( array( 'icon' => 'fa-light fa-bed-front', 'label' => 'King bed' ),
                              array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 440 sq ft' ) ),
        ),
    );

    $order = 10;
    foreach ( $rooms as $room ) {
        $existing = get_page_by_path( $room['slug'], OBJECT, 'room' );
        if ( $existing ) continue;

        $post_id = wp_insert_post( array(
            'post_type'    => 'room',
            'post_status'  => 'publish',
            'post_title'   => $room['title'],
            'post_name'    => $room['slug'],
            'post_content' => $room['desc'],
            'menu_order'   => $order,
        ) );

        if ( ! $post_id || is_wp_error( $post_id ) ) continue;

        update_field( 'short_description', $room['desc'], $post_id );
        update_field( 'bed_type', $room['bed'], $post_id );
        update_field( 'room_size', $room['size'], $post_id );
        update_field( 'amenities', array_merge( $room['extra'], $shared_amenities_left, $shared_amenities_right ), $post_id );
        update_field( 'booking_options', $booking_options, $post_id );

        $order += 10;
    }

    update_option( 'hc_rooms_seeded', 1 );
} );
