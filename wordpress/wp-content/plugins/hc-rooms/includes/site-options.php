<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Site-wide content stored in an ACF Options page so the client can edit:
 *  - Gallery (lightbox + filter categories)
 *  - Testimonials
 *  - Awards / certificates
 *  - Footer / contact info
 *  - Hero carousel slides (home page)
 *  - Facility icons (home page "Why Choose Us")
 *
 * This lives in the rooms plugin for convenience — could be split out later.
 */

add_action( 'acf/init', function () {

    if ( ! function_exists( 'acf_add_options_page' ) ) return;

    acf_add_options_page( array(
        'page_title' => 'Hotel Cosmopolitan — Site Content',
        'menu_title' => 'Site Content',
        'menu_slug'  => 'hc-site-content',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 22,
        'capability' => 'manage_options',
        'autoload'   => true,
    ) );

    acf_add_local_field_group( array(
        'key'    => 'group_hc_site_content',
        'title'  => 'Site Content',
        'fields' => array(

            // ===== TAB: Contact =====
            array( 'key' => 'tab_contact', 'label' => 'Contact', 'type' => 'tab' ),
            array( 'key' => 'hc_address',         'name' => 'address',         'label' => 'Address',         'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'hc_phones',          'name' => 'phones',          'label' => 'Phone Numbers',   'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_phone_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text' ),
                    array( 'key' => 'hc_phone_value', 'name' => 'value', 'label' => 'Number', 'type' => 'text' ),
                ),
            ),
            array( 'key' => 'hc_emails',          'name' => 'emails',          'label' => 'Emails',          'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_email_value', 'name' => 'value', 'label' => 'Email', 'type' => 'email' ),
                ),
            ),
            array( 'key' => 'hc_map_url',         'name' => 'map_url',         'label' => 'Google Maps Embed URL', 'type' => 'url' ),
            array( 'key' => 'hc_facebook',        'name' => 'facebook_url',    'label' => 'Facebook URL',    'type' => 'url' ),
            array( 'key' => 'hc_instagram',       'name' => 'instagram_url',   'label' => 'Instagram URL',   'type' => 'url' ),

            // ===== TAB: Hero Carousel =====
            array( 'key' => 'tab_hero', 'label' => 'Home Hero', 'type' => 'tab' ),
            array( 'key' => 'hc_hero_slides', 'name' => 'hero_slides', 'label' => 'Hero Slides', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_hero_image',  'name' => 'image',  'label' => 'Background Image', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_hero_title',  'name' => 'title',  'label' => 'Heading', 'type' => 'text' ),
                    array( 'key' => 'hc_hero_rating_source', 'name' => 'rating_source', 'label' => 'Rating Source (e.g. Google)', 'type' => 'text' ),
                    array( 'key' => 'hc_hero_rating_icon',   'name' => 'rating_icon',   'label' => 'Rating Icon', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_hero_rating_value',  'name' => 'rating_value',  'label' => 'Rating Value', 'type' => 'text', 'placeholder' => '4.9' ),
                    array( 'key' => 'hc_hero_rating_count',  'name' => 'rating_count',  'label' => 'Review Count', 'type' => 'text', 'placeholder' => '1164' ),
                ),
            ),

            // ===== TAB: Facilities (Home page "Why Choose Us") =====
            array( 'key' => 'tab_facilities', 'label' => 'Facilities Icons', 'type' => 'tab' ),
            array( 'key' => 'hc_facilities', 'name' => 'facility_icons', 'label' => 'Facility Icons', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_fac_icon',  'name' => 'icon',  'label' => 'Icon', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_fac_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text' ),
                ),
            ),

            // ===== TAB: Gallery =====
            array( 'key' => 'tab_gallery', 'label' => 'Gallery', 'type' => 'tab' ),
            array( 'key' => 'hc_gallery', 'name' => 'gallery', 'label' => 'Gallery Items', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_gal_image',    'name' => 'image',    'label' => 'Image', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_gal_category', 'name' => 'category', 'label' => 'Category', 'type' => 'select',
                        'choices' => array(
                            'room'        => 'Room',
                            'reception'   => 'Reception',
                            'restaurent'  => 'Restaurant',
                            'hall'        => 'Banquet Hall',
                            'corridor'    => 'Corridor',
                        ),
                    ),
                ),
            ),

            // ===== TAB: Testimonials =====
            array( 'key' => 'tab_testimonials', 'label' => 'Testimonials', 'type' => 'tab' ),
            array( 'key' => 'hc_testimonials', 'name' => 'testimonials', 'label' => 'Reviews', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_ts_name',   'name' => 'name',   'label' => 'Name', 'type' => 'text' ),
                    array( 'key' => 'hc_ts_review', 'name' => 'review', 'label' => 'Review', 'type' => 'textarea', 'rows' => 4 ),
                    array( 'key' => 'hc_ts_stars',  'name' => 'stars',  'label' => 'Stars (1–5)', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5 ),
                ),
            ),

            // ===== TAB: Awards =====
            array( 'key' => 'tab_awards', 'label' => 'Awards', 'type' => 'tab' ),
            array( 'key' => 'hc_awards', 'name' => 'awards', 'label' => 'Awards / Certificates', 'type' => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ),

            // ===== TAB: Restaurant =====
            array( 'key' => 'tab_restaurant', 'label' => 'Restaurant', 'type' => 'tab' ),
            array( 'key' => 'hc_rest_hours', 'name' => 'restaurant_hours', 'label' => 'Restaurant Hours', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_rh_meal', 'name' => 'meal', 'label' => 'Meal', 'type' => 'text' ),
                    array( 'key' => 'hc_rh_time', 'name' => 'time', 'label' => 'Time', 'type' => 'text' ),
                ),
            ),
            array( 'key' => 'hc_rest_qr',      'name' => 'restaurant_qr',      'label' => 'Menu QR Code', 'type' => 'image', 'return_format' => 'array' ),
            array( 'key' => 'hc_rest_reserve', 'name' => 'restaurant_reserve_url', 'label' => 'Reservation URL', 'type' => 'url' ),

            // ===== TAB: Awards heading / banquet / conference categories =====
            array( 'key' => 'tab_facilities_cats', 'label' => 'Facility Categories', 'type' => 'tab' ),
            array( 'key' => 'hc_banquet_cats', 'name' => 'banquet_categories', 'label' => 'Banquet Hall — Event Categories', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_bcat_image', 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_bcat_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text' ),
                ),
            ),
            array( 'key' => 'hc_conf_cats', 'name' => 'conference_categories', 'label' => 'Conference / Board Room — Use Categories', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_ccat_image', 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_ccat_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text' ),
                ),
            ),
            array( 'key' => 'hc_rest_cuisines', 'name' => 'restaurant_cuisines', 'label' => 'Restaurant Cuisines', 'type' => 'repeater',
                'sub_fields' => array(
                    array( 'key' => 'hc_cu_image', 'name' => 'image', 'label' => 'Image', 'type' => 'image', 'return_format' => 'array' ),
                    array( 'key' => 'hc_cu_label', 'name' => 'label', 'label' => 'Label', 'type' => 'text' ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'hc-site-content',
                ),
            ),
        ),
    ) );
} );


/**
 * One-time seed of site content (Gallery + Testimonials + Awards + Hero slides + Contact info + Restaurant hours).
 * Runs on admin_init after rooms are seeded.
 *
 * Idempotent: `hc_site_content_seeded` option flag.
 */
add_action( 'admin_init', 'hc_site_content_seed', 30 );

function hc_site_content_seed() {

    if ( get_option( 'hc_site_content_seeded' ) ) return;
    if ( ! function_exists( 'update_field' ) ) return;

    // ===== Contact (from component/footer.php) =====
    update_field( 'address', 'Darshan Society Road, Near Stadium Circle, Navrangpura, Ahmedabad - 380009', 'option' );
    update_field( 'phones', array(
        array( 'label' => 'Reservations', 'value' => '+91-90-9991-4802' ),
        array( 'label' => 'Reservations', 'value' => '+91-90-9991-4811' ),
        array( 'label' => 'Front Desk',   'value' => '+91-79-6601-6601' ),
        array( 'label' => 'Front Desk',   'value' => '+91-79-2642-6001' ),
    ), 'option' );
    update_field( 'emails', array(
        array( 'value' => 'reserve@hotelcosmopolitan.in' ),
        array( 'value' => 'ceo@hotelcosmopolitan.in' ),
    ), 'option' );
    update_field( 'map_url',       'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d458.9406990288283!2d72.56015249452327!3d23.04118482332241!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e85d868252e09%3A0x2a7e419c53933ac6!2sHotel%20Cosmopolitan!5e0!3m2!1sen!2sin!4v1742808600129!5m2!1sen!2sin', 'option' );
    update_field( 'facebook_url',  'https://www.facebook.com/hotelcosmopolitanabad', 'option' );
    update_field( 'instagram_url', '', 'option' );

    // ===== Hero Carousel (from index.php) =====
    // Try to import the home images if they exist; fall back to null if not.
    $hero_imgs = hc_import_directory( 'images/home' );
    $google_icon = hc_import_attachment( 'images/icons/google.webp' );
    $trip_icon   = hc_import_attachment( 'images/icons/tripadvisor.webp' );
    $mmt_icon    = hc_import_attachment( 'images/icons/mmt.webp' );
    $goi_icon    = hc_import_attachment( 'images/icons/goibibo.webp' );
    $book_icon   = hc_import_attachment( 'images/icons/booking.webp' );

    $slides = array(
        array( 'title' => '3 Star Hotel Near Navrangpura in Ahmedabad',                                          'rating_source' => 'Google',        'rating_value' => '4.9', 'rating_count' => '1164', 'rating_icon' => is_wp_error( $google_icon ) ? '' : $google_icon ),
        array( 'title' => 'Your comfort is our priority and it shows in every corner of our rooms',              'rating_source' => 'TripAdvisor',   'rating_value' => '4.5', 'rating_count' => '282',  'rating_icon' => is_wp_error( $trip_icon ) ? '' : $trip_icon ),
        array( 'title' => 'Embark on a culinary adventure that will leave you craving for more',                 'rating_source' => 'Make My Trip',  'rating_value' => '4.2', 'rating_count' => '262',  'rating_icon' => is_wp_error( $mmt_icon ) ? '' : $mmt_icon ),
        array( 'title' => 'From weddings to corporate gatherings, our banquet hall sets the perfect stage',      'rating_source' => 'Goibibo',       'rating_value' => '4.4', 'rating_count' => '267',  'rating_icon' => is_wp_error( $goi_icon ) ? '' : $goi_icon ),
        array( 'title' => 'A space for innovation and collaboration — our Boardroom',                            'rating_source' => 'Booking.com',   'rating_value' => '4.4', 'rating_count' => '267',  'rating_icon' => is_wp_error( $book_icon ) ? '' : $book_icon ),
    );
    // Attach hero images if present (one per slide, in order)
    foreach ( $slides as $i => &$s ) {
        if ( isset( $hero_imgs[ $i ] ) ) $s['image'] = $hero_imgs[ $i ];
    }
    update_field( 'hero_slides', $slides, 'option' );

    // ===== Facility icons (from index.php "Why Choose Us") =====
    $fac_icons = array(
        array( 'label' => 'Board Room',       'src' => 'images/icons/board-meeting.webp' ),
        array( 'label' => 'High Speed Wifi',  'src' => 'images/icons/wifi.webp' ),
        array( 'label' => 'Restaurant',       'src' => 'images/icons/restaurant.webp' ),
        array( 'label' => 'Banquet Hall',     'src' => 'images/icons/marriage-hall.webp' ),
        array( 'label' => 'Hygiene Plus',     'src' => 'images/icons/hygiene.webp' ),
        array( 'label' => 'Airport Transfer', 'src' => 'images/icons/airport.webp' ),
    );
    $fac_rows = array();
    foreach ( $fac_icons as $f ) {
        $id = hc_import_attachment( $f['src'] );
        $fac_rows[] = array( 'icon' => is_wp_error( $id ) ? '' : $id, 'label' => $f['label'] );
    }
    update_field( 'facility_icons', $fac_rows, 'option' );

    // ===== Gallery (from gallery.php — with categories) =====
    $gallery_items = array(
        array( 'src' => 'images/gallery/1.webp',  'category' => 'reception'  ),
        array( 'src' => 'images/gallery/2.webp',  'category' => 'restaurent' ),
        array( 'src' => 'images/gallery/5.webp',  'category' => 'room'       ),
        array( 'src' => 'images/gallery/6.webp',  'category' => 'room'       ),
        array( 'src' => 'images/gallery/7.webp',  'category' => 'room'       ),
        array( 'src' => 'images/gallery/8.webp',  'category' => 'restaurent' ),
        array( 'src' => 'images/gallery/9.webp',  'category' => 'reception'  ),
        array( 'src' => 'images/gallery/10.webp', 'category' => 'reception'  ),
        array( 'src' => 'images/gallery/14.webp', 'category' => 'hall'       ),
        array( 'src' => 'images/gallery/16.webp', 'category' => 'hall'       ),
        array( 'src' => 'images/gallery/18.webp', 'category' => 'room'       ),
        array( 'src' => 'images/gallery/20.webp', 'category' => 'room'       ),
        array( 'src' => 'images/gallery/21.webp', 'category' => 'reception'  ),
        array( 'src' => 'images/gallery/22.webp', 'category' => 'reception'  ),
        array( 'src' => 'images/gallery/24.webp', 'category' => 'restaurent' ),
        array( 'src' => 'images/gallery/25.webp', 'category' => 'restaurent' ),
        array( 'src' => 'images/gallery/banquet-hall-1.webp', 'category' => 'hall'     ),
        array( 'src' => 'images/gallery/banquet-hall-2.webp', 'category' => 'hall'     ),
        array( 'src' => 'images/gallery/corridor-1.webp',     'category' => 'corridor' ),
        array( 'src' => 'images/gallery/corridor-2.webp',     'category' => 'corridor' ),
        array( 'src' => 'images/gallery/corridor-3.webp',     'category' => 'corridor' ),
        array( 'src' => 'images/gallery/corridor-4.webp',     'category' => 'corridor' ),
    );
    $gallery_rows = array();
    foreach ( $gallery_items as $g ) {
        $id = hc_import_attachment( $g['src'] );
        if ( is_wp_error( $id ) ) continue;
        $gallery_rows[] = array( 'image' => $id, 'category' => $g['category'] );
    }
    update_field( 'gallery', $gallery_rows, 'option' );

    // ===== Testimonials (from index.php) =====
    update_field( 'testimonials', array(
        array(
            'name'   => 'Hege Nilsen',
            'stars'  => 5,
            'review' => 'We had such a good stay here! Incredible staff — always fixing and helping with everything. Lovely interior, new and fresh, fresh and white linen, goodie hot shower. AC all good. Above 4th floor amazing views, an urban troop of monkeys every sunset — such a delight! Highly recommended!',
        ),
        array(
            'name'   => 'Jain',
            'stars'  => 5,
            'review' => 'Location is very good. Rooms are reasonably spacious and well appointed. Staff is polite and helpful. Food is also very good.',
        ),
        array(
            'name'   => 'Omkar',
            'stars'  => 5,
            'review' => 'I stayed here for just one night but the experience was wonderful. The hotel is actually at the main road, near the Sardar Patel stadium metro station, also opposite to the Nidhi hospital. One can also find autos for local transfer or can have access to Uber or Ola.',
        ),
    ), 'option' );

    // ===== Awards (from images/certificate/) =====
    $award_ids = hc_import_directory( 'images/certificate' );
    if ( $award_ids ) update_field( 'awards', $award_ids, 'option' );

    // ===== Restaurant (from restaurent.php) =====
    update_field( 'restaurant_hours', array(
        array( 'meal' => 'Breakfast', 'time' => '7:30 am to 10:30 am' ),
        array( 'meal' => 'Lunch',     'time' => '12:30 pm to 3:00 pm' ),
        array( 'meal' => 'Dinner',    'time' => '7:00 pm to 11:00 pm' ),
    ), 'option' );
    $qr = hc_import_attachment( 'images/restaurent/qr.webp' );
    if ( ! is_wp_error( $qr ) ) update_field( 'restaurant_qr', $qr, 'option' );
    update_field( 'restaurant_reserve_url', 'https://wa.me/+919099914802?text=Hey%20Hotel%20Cosmopolitan,%20I%20want%20to%20reserve%20a%20table', 'option' );

    // ===== Restaurant cuisines (from images/restaurent/) =====
    $cuisines = array(
        array( 'label' => 'North Indian', 'src' => 'images/restaurent/northindian.webp' ),
        array( 'label' => 'South Indian', 'src' => 'images/restaurent/southindian.webp' ),
        array( 'label' => 'Gujarati',     'src' => 'images/restaurent/gujarati.webp' ),
        array( 'label' => 'Punjabi',      'src' => 'images/restaurent/punjabi.webp' ),
        array( 'label' => 'Chinese',      'src' => 'images/restaurent/chinese.webp' ),
        array( 'label' => 'Italian',      'src' => 'images/restaurent/italian.webp' ),
        array( 'label' => 'Spanish',      'src' => 'images/restaurent/spanish.webp' ),
        array( 'label' => 'Thai',         'src' => 'images/restaurent/thai.webp' ),
        array( 'label' => 'Mexican',      'src' => 'images/restaurent/mexican.webp' ),
        array( 'label' => 'Sea Food',     'src' => 'images/restaurent/sea.webp' ),
    );
    $cu_rows = array();
    foreach ( $cuisines as $c ) {
        $id = hc_import_attachment( $c['src'] );
        $cu_rows[] = array( 'image' => is_wp_error( $id ) ? '' : $id, 'label' => $c['label'] );
    }
    update_field( 'restaurant_cuisines', $cu_rows, 'option' );

    // ===== Banquet categories (from images/banquet-hall/) =====
    $banquet = array(
        array( 'label' => 'Wedding',          'src' => 'images/banquet-hall/wedding.webp' ),
        array( 'label' => 'Engagement',       'src' => 'images/banquet-hall/engagement.webp' ),
        array( 'label' => 'Baby Shower',      'src' => 'images/banquet-hall/baby-shower.webp' ),
        array( 'label' => 'Birthday',         'src' => 'images/banquet-hall/birthday.webp' ),
        array( 'label' => 'Corporate Party',  'src' => 'images/banquet-hall/corporate-party.webp' ),
        array( 'label' => 'Farewell',         'src' => 'images/banquet-hall/farewell.webp' ),
    );
    $b_rows = array();
    foreach ( $banquet as $b ) {
        $id = hc_import_attachment( $b['src'] );
        $b_rows[] = array( 'image' => is_wp_error( $id ) ? '' : $id, 'label' => $b['label'] );
    }
    update_field( 'banquet_categories', $b_rows, 'option' );

    // ===== Conference categories (from images/corona-hall/) =====
    $conf = array(
        array( 'label' => 'Business Meeting',  'src' => 'images/corona-hall/business-meeting.webp' ),
        array( 'label' => 'Community Meeting', 'src' => 'images/corona-hall/community-meeting.webp' ),
        array( 'label' => 'Product Launch',    'src' => 'images/corona-hall/product-launch.webp' ),
        array( 'label' => 'Seminar',           'src' => 'images/corona-hall/seminar.webp' ),
        array( 'label' => 'Training Session',  'src' => 'images/corona-hall/training-session.webp' ),
        array( 'label' => 'Workshop',          'src' => 'images/corona-hall/workshop.webp' ),
    );
    $c_rows = array();
    foreach ( $conf as $c ) {
        $id = hc_import_attachment( $c['src'] );
        $c_rows[] = array( 'image' => is_wp_error( $id ) ? '' : $id, 'label' => $c['label'] );
    }
    update_field( 'conference_categories', $c_rows, 'option' );

    update_option( 'hc_site_content_seeded', 1 );
}
