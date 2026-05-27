<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Seed the 5 room types from the original static site, including images
 * imported into the WP Media Library from the child theme's /assets/images/rooms/ folder.
 *
 * Idempotent: controlled by the `hc_rooms_seeded_v2` option. To re-run, delete that option:
 *   wp option delete hc_rooms_seeded_v2   (WP-CLI)
 * or remove the row from wp_options manually, then reload WP-Admin.
 *
 * v2 — does NOT require ACF Pro. Stores all room metadata as native post meta
 * via hc_set() from storage.php.
 */
add_action( 'admin_init', 'hc_rooms_seed', 20 );

function hc_rooms_seed() {

    if ( get_option( 'hc_rooms_seeded_v2' ) ) return;

    // ---- Shared amenities (left + right columns from original deluxe-room.php) ----
    $base_amenities_left = array(
        array( 'icon' => 'fa-light fa-mug-tea',      'label' => 'Tea / coffee maker' ),
        array( 'icon' => 'fa-light fa-vault',        'label' => 'Safe deposit box' ),
        array( 'icon' => 'fa-light fa-door-open',    'label' => 'Wardrobe' ),
        array( 'icon' => 'fa-light fa-tv',           'label' => 'LED TV with fully loaded channel' ),
        array( 'icon' => 'fa-light fa-lamp-desk',    'label' => 'Writing table' ),
        array( 'icon' => 'fa-light fa-refrigerator', 'label' => 'Mini bar' ),
    );
    $base_amenities_right = array(
        array( 'icon' => 'fa-light fa-air-conditioner', 'label' => 'Central AC' ),
        array( 'icon' => 'fa-light fa-toothbrush',      'label' => 'Free grooming kit on request' ),
        array( 'icon' => 'fa-light fa-shower',          'label' => '24X7 running hot water' ),
        array( 'icon' => 'fa-light fa-raygun',          'label' => 'Hair dryer' ),
        array( 'icon' => 'fa-light fa-shirt',           'label' => 'Iron & Ironing board on request' ),
        array( 'icon' => 'fa-light fa-wifi',            'label' => 'Free Wi-Fi internet' ),
        array( 'icon' => 'fa-light fa-square-parking', 'label' => 'Parking as per availability' ),
    );

    $booking_options = array(
        array( 'label' => 'Room Only',         'url' => 'https://www.swiftbook.io/inst/#home?propertyId=2166' ),
        array( 'label' => 'Room + Breakfast', 'url' => 'https://www.swiftbook.io/inst/#home?propertyId=2166' ),
    );

    // ---- 5 rooms: original content from public_html/{room}-room.php ----
    $rooms = array(

        array(
            'slug'      => 'executive-room',
            'title'     => 'Executive Room',
            'image_dir' => 'images/rooms/executive',
            'short'     => 'Comfortable and well-appointed rooms for corporate and leisure travellers.',
            'content'   => 'From the moment you step into our Executive room, you will be greeted by thoughtfully curated interiors, displaying bespoke furnishings and artistic touches.',
            'bed'       => 'King bed / Twin beds',
            'size'      => '285 sq ft',
            'extra'     => array(
                array( 'icon' => 'fa-light fa-bed-front',  'label' => 'King bed / Twin beds' ),
                array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 285 sq ft' ),
                array( 'icon' => 'fa-light fa-person-booth', 'label' => 'Interconnected rooms (on request)' ),
            ),
        ),

        array(
            'slug'      => 'premium-room',
            'title'     => 'Premium Room',
            'image_dir' => 'images/rooms/premium',
            'short'     => 'Spacious rooms with a refined ambience and modern conveniences.',
            'content'   => 'Our Premium rooms feature a refined ambience with elegant interiors and modern conveniences for the discerning traveller.',
            'bed'       => 'King bed / Twin beds',
            'size'      => '290 sq ft',
            'extra'     => array(
                array( 'icon' => 'fa-light fa-bed-front',  'label' => 'King bed / Twin beds' ),
                array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 290 sq ft' ),
                array( 'icon' => 'fa-light fa-person-booth', 'label' => 'Interconnected rooms (on request)' ),
            ),
        ),

        array(
            'slug'      => 'presidential-room',
            'title'     => 'Presidential Room',
            // Original site didn't have a /images/rooms/presidential folder; fall back to luxury.
            'image_dir' => 'images/rooms/luxury',
            'short'     => 'A premium tier with elevated finishes for the discerning traveller.',
            'content'   => 'Our Presidential room redefines luxury with an elevated tier of finishes, generous space and meticulous attention to detail.',
            'bed'       => 'King bed',
            'size'      => '445 sq ft',
            'extra'     => array(
                array( 'icon' => 'fa-light fa-bed-front',  'label' => 'King bed' ),
                array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 445 sq ft' ),
            ),
        ),

        array(
            'slug'      => 'luxury-room',
            'title'     => 'Luxury Room',
            'image_dir' => 'images/rooms/luxury',
            'short'     => 'Our most expansive room category, with a bath tub and lounge area.',
            'content'   => 'The Luxury room is our most expansive offering — featuring a bath tub, lounge area and premium amenities for an indulgent stay.',
            'bed'       => 'King bed',
            'size'      => '430 sq ft',
            'extra'     => array(
                array( 'icon' => 'fa-light fa-bed-front',  'label' => 'King bed' ),
                array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 430 sq ft' ),
                array( 'icon' => 'fa-light fa-bath',       'label' => 'Bath Tub' ),
            ),
        ),

        array(
            'slug'      => 'deluxe-room',
            'title'     => 'Deluxe Room',
            'image_dir' => 'images/rooms/deluxe',
            'short'     => 'Thoughtfully curated interiors with bespoke furnishings and artistic touches.',
            'content'   => 'From the moment you step into our Deluxe room, you will be greeted by thoughtfully curated interiors, displaying bespoke furnishings and artistic touches.',
            'bed'       => 'King bed',
            'size'      => '440 sq ft',
            'extra'     => array(
                array( 'icon' => 'fa-light fa-bed-front',  'label' => 'King bed' ),
                array( 'icon' => 'fa-light fa-chart-area', 'label' => 'Room size: 440 sq ft' ),
            ),
        ),
    );

    $order = 10;
    foreach ( $rooms as $room ) {

        // Find existing by slug (might have been seeded before with old code)
        $existing = get_page_by_path( $room['slug'], OBJECT, 'room' );
        $post_id = $existing ? $existing->ID : 0;

        if ( ! $post_id ) {
            $post_id = wp_insert_post( array(
                'post_type'    => 'room',
                'post_status'  => 'publish',
                'post_title'   => $room['title'],
                'post_name'    => $room['slug'],
                'post_content' => $room['content'],
                'menu_order'   => $order,
            ) );
            if ( is_wp_error( $post_id ) || ! $post_id ) continue;
        }

        // Import all images from the room's folder
        $image_ids = hc_import_directory( $room['image_dir'] );

        if ( $image_ids ) {
            set_post_thumbnail( $post_id, $image_ids[0] );
            hc_set( 'gallery', $image_ids, $post_id );
        }

        hc_set( 'short_description', $room['short'], $post_id );
        hc_set( 'bed_type',          $room['bed'],   $post_id );
        hc_set( 'room_size',         $room['size'],  $post_id );
        hc_set( 'amenities',         array_merge( $room['extra'], $base_amenities_left, $base_amenities_right ), $post_id );
        hc_set( 'booking_options',   $booking_options, $post_id );

        $order += 10;
    }

    update_option( 'hc_rooms_seeded_v2', 1 );

    // Also clear the v1 flag so anyone re-running won't have leftover state
    delete_option( 'hc_rooms_seeded' );
}
