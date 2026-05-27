<?php
/**
 * Auto-create all pages with their Divi layout content on plugin activation,
 * plus build the primary nav menu, set the homepage, and flush permalinks.
 *
 * Idempotent: skips pages that already exist (matched by slug).
 *
 * Tracked via the `hc_pages_seeded` option flag.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'hc_pages_seed', 40 );

function hc_pages_seed() {

    if ( get_option( 'hc_pages_seeded_v2' ) ) return;

    // The 9 pages we ship layouts for, in menu order
    $pages = array(
        array(
            'slug'      => 'home',
            'title'     => 'Home',
            'layout'    => 'home-layout.json',
            'menu'      => false,                  // not in primary menu (it's the home logo)
        ),
        array(
            'slug'      => 'about-us',
            'title'     => 'About Us',
            'layout'    => 'about-us-layout.json',
            'menu'      => true,
            'mobile'    => true,                  // only mobile menu
        ),
        array(
            'slug'      => 'rooms',
            'title'     => 'Rooms',
            'layout'    => 'rooms-archive-layout.json',
            'menu'      => true,
        ),
        array(
            'slug'      => 'restaurant',
            'title'     => 'Coriander Restaurant',
            'layout'    => 'restaurant-layout.json',
            'menu'      => true,
        ),
        array(
            'slug'      => 'banquet-hall',
            'title'     => 'Banquet Hall',
            'layout'    => 'banquet-hall-layout.json',
            'menu'      => true,
        ),
        array(
            'slug'      => 'conference-room',
            'title'     => 'Board Room',
            'layout'    => 'conference-room-layout.json',
            'menu'      => true,
        ),
        array(
            'slug'      => 'facilities',
            'title'     => 'Facilities',
            'layout'    => 'facilities-layout.json',
            'menu'      => false,                  // not in primary; lives in mobile
            'mobile'    => true,
        ),
        array(
            'slug'      => 'gallery',
            'title'     => 'Gallery',
            'layout'    => 'gallery-layout.json',
            'menu'      => true,
        ),
        array(
            'slug'      => 'contact-us',
            'title'     => 'Contact Us',
            'layout'    => 'contact-us-layout.json',
            'menu'      => true,
        ),
    );

    // Static (non-Divi-builder) pages from public_html/ — FAQ + policies + 404 + blogs index
    $static_pages = hc_pages_static_definitions();

    $created_ids = array();

    // ----- 1. Divi-builder pages -----
    foreach ( $pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            $created_ids[ $p['slug'] ] = $existing->ID;
            continue;
        }

        $content = hc_load_layout_content( $p['layout'] );

        $post_id = wp_insert_post( array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_content' => $content,
        ) );

        if ( ! is_wp_error( $post_id ) && $post_id ) {
            // Tell Divi this page uses the builder
            update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
            update_post_meta( $post_id, '_et_pb_page_layout', 'et_no_sidebar' );
            update_post_meta( $post_id, '_et_pb_side_nav',    'off' );
            $created_ids[ $p['slug'] ] = $post_id;
        }
    }

    // ----- 2. Static text pages -----
    foreach ( $static_pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            $created_ids[ $p['slug'] ] = $existing->ID;
            continue;
        }
        $post_id = wp_insert_post( array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_content' => $p['content'],
        ) );
        if ( ! is_wp_error( $post_id ) && $post_id ) {
            $created_ids[ $p['slug'] ] = $post_id;
        }
    }

    // ----- 2b. SEO landing pages (extracted from public_html/) -----
    hc_pages_seed_seo_pages();

    // ----- 3. Set front page to "Home" -----
    if ( isset( $created_ids['home'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $created_ids['home'] );
    }

    // ----- 4. Set permalink structure to /%postname%/ -----
    if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
        update_option( 'permalink_structure', '/%postname%/' );
    }

    // ----- 5. Build the Primary Menu -----
    hc_pages_build_menu( $pages, $created_ids );

    // ----- 6. Flush rewrites -----
    flush_rewrite_rules();

    update_option( 'hc_pages_seeded_v2', 1 );
    delete_option( 'hc_pages_seeded' );
}

/**
 * Load Divi layout JSON shipped in /layouts/ and return the embedded shortcode content.
 */
function hc_load_layout_content( $filename ) {
    $path = HC_ROOMS_DIR . 'layouts/' . $filename;
    if ( ! file_exists( $path ) ) return '';

    $raw = file_get_contents( $path );
    $json = json_decode( $raw, true );
    if ( ! is_array( $json ) || empty( $json['data'] ) ) return '';

    // data is an object keyed by post id ("1") with the shortcode string as value
    $content = is_array( $json['data'] ) ? reset( $json['data'] ) : '';

    // Replace placeholders
    $content = str_replace(
        array( '@@HC_THEME_URI@@' ),
        array( get_stylesheet_directory_uri() ),
        $content
    );

    return $content;
}

/**
 * Build (or update) a "Primary Menu" matching the original site navigation.
 */
function hc_pages_build_menu( $pages, $created_ids ) {
    $menu_name = 'Primary Menu';

    $menu = wp_get_nav_menu_object( $menu_name );
    if ( ! $menu ) {
        $menu_id = wp_create_nav_menu( $menu_name );
        $menu = wp_get_nav_menu_object( $menu_id );
    }

    if ( ! $menu ) return;
    $menu_id = $menu->term_id;

    // Skip if menu already has items (don't overwrite manual edits)
    $existing_items = wp_get_nav_menu_items( $menu_id );
    if ( $existing_items ) return;

    foreach ( $pages as $p ) {
        if ( empty( $p['menu'] ) ) continue;
        if ( empty( $created_ids[ $p['slug'] ] ) ) continue;

        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'     => $p['title'],
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $created_ids[ $p['slug'] ],
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ) );
    }

    // Assign to the "primary-menu" theme location
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary-menu'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Seed all 30+ SEO landing pages from data/seo-landing-pages.json (pre-extracted
 * from the original public_html/ files at build time).
 *
 * Each gets the SEO landing Divi layout wrapping its original prose content,
 * with the original meta title + meta description preserved as post meta so an
 * SEO plugin (Yoast / RankMath) can pick them up.
 */
function hc_pages_seed_seo_pages() {
    $path = HC_ROOMS_DIR . 'data/seo-landing-pages.json';
    if ( ! file_exists( $path ) ) return;

    $json = json_decode( file_get_contents( $path ), true );
    if ( ! is_array( $json ) ) return;

    $layout_wrapper = hc_load_layout_content( 'seo-landing-template.json' );

    foreach ( $json as $entry ) {
        $slug = isset( $entry['slug'] ) ? sanitize_title( $entry['slug'] ) : '';
        if ( ! $slug ) continue;
        if ( get_page_by_path( $slug ) ) continue; // already exists

        // Inject the original prose content where the et_pb_post_content module sits.
        // We do this by appending an et_pb_text block before the rooms grid so the
        // Divi layout still works AND the content shows.
        $title   = isset( $entry['title'] ) ? wp_strip_all_tags( $entry['title'] ) : ucfirst( str_replace( '-', ' ', $slug ) );
        $title   = ucwords( strtolower( $title ) );  // normalize ALLCAPS originals
        $content = ! empty( $entry['content'] ) ? $entry['content'] : '<p>Content coming soon.</p>';

        // Build a self-contained Divi page that embeds the prose as a text module
        $body = '[et_pb_section fb_built="1" _builder_version="4.20.0" background_color="#14141e" custom_padding="180px||80px||false|false"]'
              . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
              . '[et_pb_text _builder_version="4.20.0" header_font="Poppins|700|on||||||" text_text_color="#ffffff" header_text_color="#ffffff" header_font_size="42px" text_text_align="center" header_text_align="center"]'
              . '<h1>' . esc_html( $title ) . '</h1>'
              . '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]'
              . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="60px||40px||true|false"]'
              . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
              . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
              . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em"]'
              . wp_kses_post( $content )
              . '[/et_pb_text][/et_pb_column]'
              . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
              . '[et_pb_code _builder_version="4.20.0"]<div id="quickbook-widget"></div>[/et_pb_code]'
              . '[et_pb_code _builder_version="4.20.0" custom_margin="20px||||false|false"][hc_inquiry_form variant="booking" title="Quick Inquiry"][/et_pb_code]'
              . '[/et_pb_column][/et_pb_row][/et_pb_section]'
              . '[et_pb_section fb_built="1" _builder_version="4.20.0" background_color="#f7f7f7" custom_padding="60px||60px||true|false"]'
              . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
              . '[et_pb_text _builder_version="4.20.0" header_2_font="Poppins|600|||||||" header_2_text_align="center" custom_margin="||30px||false|false"]<h2>Our Rooms</h2>[/et_pb_text]'
              . '[et_pb_code _builder_version="4.20.0"][hc_rooms_grid columns="2"][/et_pb_code]'
              . '[/et_pb_column][/et_pb_row][/et_pb_section]';

        $post_id = wp_insert_post( array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $body,
        ) );

        if ( is_wp_error( $post_id ) || ! $post_id ) continue;

        update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
        update_post_meta( $post_id, '_et_pb_page_layout', 'et_no_sidebar' );

        // Stash original SEO meta for Yoast / RankMath / manual use
        if ( ! empty( $entry['meta_title'] ) ) {
            update_post_meta( $post_id, '_yoast_wpseo_title',         $entry['meta_title'] );
            update_post_meta( $post_id, 'rank_math_title',             $entry['meta_title'] );
            update_post_meta( $post_id, '_hc_original_meta_title',     $entry['meta_title'] );
        }
        if ( ! empty( $entry['meta_description'] ) ) {
            update_post_meta( $post_id, '_yoast_wpseo_metadesc',       $entry['meta_description'] );
            update_post_meta( $post_id, 'rank_math_description',       $entry['meta_description'] );
            update_post_meta( $post_id, '_hc_original_meta_desc',      $entry['meta_description'] );
        }
    }
}

/**
 * Static-text pages (FAQ + policies + blogs index) — content extracted from public_html/
 */
function hc_pages_static_definitions() {

    $faq_html = '<div class="hc-faq">'
        . '<details open><summary>How do I request an early check-in or late check-out with the hotel?</summary><p>Since hotel policies regarding early check-in (generally before 2:00 pm) or late checkout (generally after 12:00 pm) vary by location and by hotel, please call the hotel directly prior to your arrival to make any necessary arrangements. Direct hotel phone numbers can be found on your confirmation email or on the hotel information page.</p></details>'
        . '<details><summary>What is your policy regarding cancellations?</summary><p>If your travel plans change, you can cancel or modify your reservation in accordance with the hotel\'s cancellation policy as stated during the reservation process.</p></details>'
        . '<details><summary>Will I be charged for extra guests occupying my room?</summary><p>Hotel room rates vary by date and by the number of adults occupying a single room. To accommodate more guests, you need to change your reservation. You will be notified of any additional charges prior to confirming your updated reservation.</p></details>'
        . '<details><summary>Is there a minimum age requirement to reserve a hotel room?</summary><p>Although individual hotel policies may vary, most hotels have a minimum age requirement of 21 years old. Please call the hotel directly prior to your arrival to make any necessary arrangements. Direct hotel phone numbers can be found in your confirmation email or on the respective hotel information page.</p></details>'
        . '<details><summary>Where can I find maps and directions to my hotel?</summary><p>When on a hotel\'s overview page, click on to the "Contact" tab. Here, you\'ll find contact details for the hotel. Scroll down further to the section on how to get to the hotel. This will include directions from major transport hubs and an interactive map helping you find the hotel from your chosen location, via car, public transport or on foot.</p></details>'
        . '<details><summary>What\'s the best way to find the information I need?</summary><p>Find all the guidance you need regarding a hotel on the respective hotel\'s web page. Navigate through the tabs (Overview, Rooms, Meetings &amp; Events, Nearby Attractions, Contact, Reviews) to find the relevant information.</p></details>'
        . '</div>'
        . '<style>.hc-faq details{border:1px solid #eee;padding:18px 22px;margin-bottom:8px;}'
        . '.hc-faq summary{cursor:pointer;font-weight:600;color:#14141e;font-size:16px;}'
        . '.hc-faq details[open] summary{color:#D81418;}'
        . '.hc-faq p{margin:14px 0 0;color:#666;line-height:1.7;}</style>';

    $reservation_html = '<h2>Reservation Policy</h2>'
        . '<p><strong>Updated on: 19-10-2023</strong></p>'
        . '<p><strong>Check-In Time:</strong> 1200hrs &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Check-Out Time:</strong> 1100hrs</p>'
        . '<ul>'
        . '<li>Early check-in or late check-out may be allowed subject to room availability, with additional charges.</li>'
        . '<li>40% of applicable room rate chargeable for early check-in from 0600 to 1200 hours and late check-out from 1100 to 1700 hours.</li>'
        . '<li>A full room rate chargeable for additional hours for check-in before 0600 hours or check-out after 1700 hours.</li>'
        . '<li>All guests must produce a valid ID proof (Aadhar / Passport) upon check-in.</li>'
        . '<li>Requests for room with double / twin bed will be accommodated subject to availability.</li>'
        . '<li>Minimum 1 night rate per room will be blocked / charged at the time of reservation.</li>'
        . '</ul>';

    $cancellation_html = '<h2>Cancellation Policy</h2>'
        . '<p><strong>Updated on: 19-10-2023</strong></p>'
        . '<h4>For individual bookings &amp; short stay (up to 4 nights)</h4>'
        . '<ul>'
        . '<li>No charges for cancellation prior to 48 hours from the date of arrival considering standard check-in time.</li>'
        . '<li>Minimum 1 room night will be charged for cancellation done less than 48 hours from the date of arrival considering standard check-in time.</li>'
        . '</ul>'
        . '<h4>For group bookings &amp; long stay (more than 5 nights)</h4>'
        . '<ul>'
        . '<li>No charges for cancellation prior to 7 days from the date of arrival considering standard check-in time.</li>'
        . '<li>Minimum 1 room night will be charged for cancellation done less than 7 days from the date of arrival considering standard check-in time.</li>'
        . '</ul>'
        . '<h4>No-show charges</h4>'
        . '<ul>'
        . '<li>In case of no-show, minimum 1 room night will be charged.</li>'
        . '<li>Room not occupied by 2300 hours will be presumed as no-show, unless prior written intimation has been made.</li>'
        . '</ul>'
        . '<p><strong>For peak-season or big-event days, cancellation policies may not be applicable and the full amount for the booking days will be charged.</strong></p>';

    $privacy_html = '<h2>Privacy Policy</h2>'
        . '<p>Hotel Cosmopolitan respects your privacy and is committed to protecting the personal data you share with us. This privacy policy explains how we collect, use, and safeguard the information you provide when using our website or services.</p>'
        . '<h4>Information We Collect</h4>'
        . '<ul><li>Contact details (name, email, phone) provided through booking forms and inquiries.</li>'
        . '<li>Reservation details (dates, room preferences, special requests).</li>'
        . '<li>Technical data (IP address, browser type) for security and analytics.</li></ul>'
        . '<h4>How We Use It</h4>'
        . '<ul><li>To process bookings and respond to inquiries.</li>'
        . '<li>To send confirmations, updates, and (with consent) promotional offers.</li>'
        . '<li>To improve our website and services.</li></ul>'
        . '<h4>Sharing</h4>'
        . '<p>We do not sell your personal data. We may share it with trusted service providers (payment processors, booking platforms) strictly as needed to provide services to you. We comply with applicable Indian data-protection laws.</p>'
        . '<h4>Contact</h4>'
        . '<p>Questions about this policy? Email <a href="mailto:reserve@hotelcosmopolitan.in">reserve@hotelcosmopolitan.in</a>.</p>';

    $terms_html = '<h2>Terms &amp; Conditions</h2>'
        . '<p>By using the Hotel Cosmopolitan website and making a reservation, you agree to the following terms.</p>'
        . '<h4>Reservations</h4>'
        . '<ul><li>All reservations are subject to availability and confirmation.</li>'
        . '<li>Rates are quoted per room, per night, and include applicable taxes unless stated otherwise.</li>'
        . '<li>Valid government-issued ID is required at check-in.</li></ul>'
        . '<h4>Payments</h4>'
        . '<ul><li>A minimum 1-night charge may be blocked at the time of booking.</li>'
        . '<li>Any incidental charges (mini-bar, room service, damages) will be billed at check-out.</li></ul>'
        . '<h4>Cancellations &amp; No-shows</h4>'
        . '<p>Cancellations are governed by our <a href="/cancellation-policy/">Cancellation Policy</a>. No-shows will be billed for one room night.</p>'
        . '<h4>Conduct</h4>'
        . '<p>The hotel reserves the right to refuse service to any guest whose behaviour is disruptive, dangerous or in breach of house rules.</p>'
        . '<h4>Liability</h4>'
        . '<p>Hotel Cosmopolitan is not responsible for loss or damage to personal belongings beyond what is required by applicable Indian law. Use of in-room safes is strongly recommended.</p>';

    $news_blogs_html = '[hc_blogs_grid limit="9" columns="3"]';

    return array(
        array( 'slug' => 'faq',                  'title' => "FAQ's",               'content' => $faq_html ),
        array( 'slug' => 'reservation-policy',   'title' => 'Reservation Policy',  'content' => $reservation_html ),
        array( 'slug' => 'cancellation-policy',  'title' => 'Cancellation Policy', 'content' => $cancellation_html ),
        array( 'slug' => 'privacy-policy',       'title' => 'Privacy Policy',      'content' => $privacy_html ),
        array( 'slug' => 'terms-condition',      'title' => 'Terms & Conditions',  'content' => $terms_html ),
        array( 'slug' => 'news-blogs',           'title' => 'News & Blogs',        'content' => $news_blogs_html ),
    );
}
