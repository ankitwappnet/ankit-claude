<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Site-wide content stored as native WP options (no ACF dependency).
 *
 * On admin_init this seeder writes the original site's content into wp_options.
 * Idempotent: controlled by `hc_site_content_seeded_v2` option flag.
 *
 * v2 — replaces ACF Options Page from the original implementation. Everything
 * is just plain options now (auto-serialized by WP). A read-only viewer in
 * Settings → Site Content lets you confirm what was seeded.
 */

add_action( 'admin_init', 'hc_site_content_seed', 30 );

function hc_site_content_seed() {

    if ( get_option( 'hc_site_content_seeded_v2' ) ) return;

    // ===== Contact (from component/footer.php) =====
    hc_set( 'address', 'Darshan Society Road, Near Stadium Circle, Navrangpura, Ahmedabad - 380009' );
    hc_set( 'phones', array(
        array( 'label' => 'Reservations', 'value' => '+91-90-9991-4802' ),
        array( 'label' => 'Reservations', 'value' => '+91-90-9991-4811' ),
        array( 'label' => 'Front Desk',   'value' => '+91-79-6601-6601' ),
        array( 'label' => 'Front Desk',   'value' => '+91-79-2642-6001' ),
    ) );
    hc_set( 'emails', array(
        array( 'value' => 'reserve@hotelcosmopolitan.in' ),
        array( 'value' => 'ceo@hotelcosmopolitan.in' ),
    ) );
    hc_set( 'map_url',       'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d458.9406990288283!2d72.56015249452327!3d23.04118482332241!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e85d868252e09%3A0x2a7e419c53933ac6!2sHotel%20Cosmopolitan!5e0!3m2!1sen!2sin!4v1742808600129!5m2!1sen!2sin' );
    hc_set( 'facebook_url',  'https://www.facebook.com/hotelcosmopolitanabad' );
    hc_set( 'instagram_url', '' );

    // ===== Hero Carousel (from index.php) =====
    $hero_imgs   = hc_import_directory( 'images/home' );
    $google_icon = hc_import_attachment( 'images/icons/google.webp' );
    $trip_icon   = hc_import_attachment( 'images/icons/tripadvisor.webp' );
    $mmt_icon    = hc_import_attachment( 'images/icons/mmt.webp' );
    $goi_icon    = hc_import_attachment( 'images/icons/goibibo.webp' );
    $book_icon   = hc_import_attachment( 'images/icons/booking.webp' );

    $slides = array(
        array( 'title' => '3 Star Hotel Near Navrangpura in Ahmedabad',                                          'rating_source' => 'Google',        'rating_value' => '4.9', 'rating_count' => '1164', 'rating_icon' => is_wp_error( $google_icon ) ? 0 : intval( $google_icon ) ),
        array( 'title' => 'Your comfort is our priority and it shows in every corner of our rooms',              'rating_source' => 'TripAdvisor',   'rating_value' => '4.5', 'rating_count' => '282',  'rating_icon' => is_wp_error( $trip_icon ) ? 0 : intval( $trip_icon ) ),
        array( 'title' => 'Embark on a culinary adventure that will leave you craving for more',                 'rating_source' => 'Make My Trip',  'rating_value' => '4.2', 'rating_count' => '262',  'rating_icon' => is_wp_error( $mmt_icon ) ? 0 : intval( $mmt_icon ) ),
        array( 'title' => 'From weddings to corporate gatherings, our banquet hall sets the perfect stage',      'rating_source' => 'Goibibo',       'rating_value' => '4.4', 'rating_count' => '267',  'rating_icon' => is_wp_error( $goi_icon ) ? 0 : intval( $goi_icon ) ),
        array( 'title' => 'A space for innovation and collaboration — our Boardroom',                            'rating_source' => 'Booking.com',   'rating_value' => '4.4', 'rating_count' => '267',  'rating_icon' => is_wp_error( $book_icon ) ? 0 : intval( $book_icon ) ),
    );
    foreach ( $slides as $i => &$s ) {
        $s['image'] = isset( $hero_imgs[ $i ] ) ? intval( $hero_imgs[ $i ] ) : 0;
    }
    unset( $s );
    hc_set( 'hero_slides', $slides );

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
        $fac_rows[] = array( 'icon' => is_wp_error( $id ) ? 0 : intval( $id ), 'label' => $f['label'] );
    }
    hc_set( 'facility_icons', $fac_rows );

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
        $gallery_rows[] = array( 'image' => intval( $id ), 'category' => $g['category'] );
    }
    hc_set( 'gallery', $gallery_rows );

    // ===== Testimonials (from index.php) =====
    hc_set( 'testimonials', array(
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
    ) );

    // ===== Awards (from images/certificate/) =====
    $award_ids = hc_import_directory( 'images/certificate' );
    if ( $award_ids ) hc_set( 'awards', $award_ids );

    // ===== Restaurant (from restaurent.php) =====
    hc_set( 'restaurant_hours', array(
        array( 'meal' => 'Breakfast', 'time' => '7:30 am to 10:30 am' ),
        array( 'meal' => 'Lunch',     'time' => '12:30 pm to 3:00 pm' ),
        array( 'meal' => 'Dinner',    'time' => '7:00 pm to 11:00 pm' ),
    ) );
    $qr = hc_import_attachment( 'images/restaurent/qr.webp' );
    if ( ! is_wp_error( $qr ) ) hc_set( 'restaurant_qr', intval( $qr ) );
    hc_set( 'restaurant_reserve_url', 'https://wa.me/+919099914802?text=Hey%20Hotel%20Cosmopolitan,%20I%20want%20to%20reserve%20a%20table' );

    // ===== Restaurant cuisines =====
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
        $cu_rows[] = array( 'image' => is_wp_error( $id ) ? 0 : intval( $id ), 'label' => $c['label'] );
    }
    hc_set( 'restaurant_cuisines', $cu_rows );

    // ===== Banquet categories =====
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
        $b_rows[] = array( 'image' => is_wp_error( $id ) ? 0 : intval( $id ), 'label' => $b['label'] );
    }
    hc_set( 'banquet_categories', $b_rows );

    // ===== Conference categories =====
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
        $c_rows[] = array( 'image' => is_wp_error( $id ) ? 0 : intval( $id ), 'label' => $c['label'] );
    }
    hc_set( 'conference_categories', $c_rows );

    update_option( 'hc_site_content_seeded_v2', 1 );
    delete_option( 'hc_site_content_seeded' );
}

/**
 * Read-only admin viewer to confirm what was seeded.
 * Settings → Site Content.
 */
add_action( 'admin_menu', function () {
    add_options_page(
        'Site Content',
        'Site Content',
        'manage_options',
        'hc-site-content',
        'hc_render_site_content_viewer'
    );
} );

function hc_render_site_content_viewer() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    ?>
    <div class="wrap">
        <h1>Site Content (read-only)</h1>
        <p>Auto-seeded from the original public_html/ site. To edit a value, edit it directly in the database (<code>wp_options</code>, keys prefixed <code>hc_</code>) or via WP-CLI: <code>wp option update hc_address "..."</code></p>

        <table class="widefat striped">
            <thead><tr><th style="width:200px;">Key</th><th>Value</th></tr></thead>
            <tbody>
                <?php
                $keys = array(
                    'address', 'phones', 'emails', 'facebook_url', 'instagram_url', 'map_url',
                    'hero_slides', 'facility_icons', 'gallery', 'testimonials', 'awards',
                    'restaurant_hours', 'restaurant_cuisines', 'restaurant_qr', 'restaurant_reserve_url',
                    'banquet_categories', 'conference_categories',
                );
                foreach ( $keys as $k ) :
                    $v = hc_get( $k );
                    $display = '';
                    if ( is_array( $v ) ) {
                        $display = sprintf( '%d items', count( $v ) );
                        if ( $v ) {
                            $display .= ' <details><summary style="cursor:pointer;color:#0073aa;">view</summary><pre style="background:#f0f0f1;padding:10px;overflow:auto;max-height:400px;">'
                                . esc_html( print_r( $v, true ) ) . '</pre></details>';
                        }
                    } else {
                        $display = esc_html( (string) $v );
                    }
                    ?>
                    <tr>
                        <td><code>hc_<?php echo esc_html( $k ); ?></code></td>
                        <td><?php echo $display; // already escaped above ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Seed flags</h2>
        <table class="widefat striped">
            <tbody>
                <tr><td>hc_rooms_seeded_v2</td>        <td><?php echo get_option( 'hc_rooms_seeded_v2' )        ? 'yes' : 'no'; ?></td></tr>
                <tr><td>hc_site_content_seeded_v2</td> <td><?php echo get_option( 'hc_site_content_seeded_v2' ) ? 'yes' : 'no'; ?></td></tr>
                <tr><td>hc_pages_seeded_v2</td>        <td><?php echo get_option( 'hc_pages_seeded_v2' )        ? 'yes' : 'no'; ?></td></tr>
            </tbody>
        </table>
    </div>
    <?php
}
