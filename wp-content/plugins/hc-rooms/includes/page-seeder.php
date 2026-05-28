<?php
/**
 * Auto-create pages with NATIVE Divi modules (et_pb_slider, et_pb_blurb,
 * et_pb_testimonial, et_pb_number_counter, et_pb_gallery, et_pb_image,
 * et_pb_text, et_pb_button) instead of custom [hc_*] shortcode wrappers,
 * so the client can edit each piece visually in Divi Builder.
 *
 * Only sections that have no native Divi equivalent stay as custom
 * shortcodes: [hc_rooms_grid] (queries the room CPT), [hc_inquiry_form]
 * (saves to DB + emails), [hc_footer_widgets] (richer than Divi's footer).
 *
 * Idempotent: bumped to v4 so existing pages from v2/v3 get rebuilt
 * automatically when the client pulls + visits admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'hc_pages_seed', 40 );

function hc_pages_seed() {

    if ( get_option( 'hc_pages_seeded_v5' ) ) return;

    $created_ids = array();

    // ----- 1. Build each main page from a layout builder function -----
    $pages = hc_pages_main_definitions();
    foreach ( $pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        $content  = call_user_func( $p['builder'] );

        if ( $existing ) {
            // Rebuild existing pages in place — needed so v4 layouts replace v2/v3
            wp_update_post( array(
                'ID'           => $existing->ID,
                'post_title'   => $p['title'],
                'post_content' => $content,
            ) );
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post( array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $p['title'],
                'post_name'    => $p['slug'],
                'post_content' => $content,
            ) );
            if ( is_wp_error( $post_id ) || ! $post_id ) continue;
        }

        update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
        update_post_meta( $post_id, '_et_pb_page_layout', 'et_no_sidebar' );
        update_post_meta( $post_id, '_et_pb_side_nav',    'off' );

        $created_ids[ $p['slug'] ] = $post_id;
    }

    // ----- 2. Static text pages (FAQ + policies + news/blogs index) -----
    foreach ( hc_pages_static_definitions() as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            wp_update_post( array(
                'ID'           => $existing->ID,
                'post_title'   => $p['title'],
                'post_content' => $p['content'],
            ) );
            $created_ids[ $p['slug'] ] = $existing->ID;
        } else {
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
    }

    // ----- 3. SEO landing pages -----
    hc_pages_seed_seo_pages();

    // ----- 4. Set front page to "Home" -----
    if ( isset( $created_ids['home'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $created_ids['home'] );
    }

    // ----- 5. Permalinks -----
    if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
        update_option( 'permalink_structure', '/%postname%/' );
    }

    // ----- 6. Primary menu -----
    hc_pages_build_menu( $pages, $created_ids );

    // ----- 7. Flush rewrites + mark done -----
    flush_rewrite_rules();
    update_option( 'hc_pages_seeded_v5', 1 );
    delete_option( 'hc_pages_seeded' );
    delete_option( 'hc_pages_seeded_v2' );
    delete_option( 'hc_pages_seeded_v3' );
    delete_option( 'hc_pages_seeded_v4' );
}

/**
 * Definitions for the 9 main Divi-builder pages — each has a `builder`
 * callable that returns its full post_content.
 */
function hc_pages_main_definitions() {
    return array(

        array(
            'slug'    => 'home',
            'title'   => 'Home',
            'builder' => 'hc_build_home_page',
            'menu'    => false,
        ),
        array(
            'slug'    => 'about-us',
            'title'   => 'About Us',
            'builder' => 'hc_build_about_page',
            'menu'    => true,
            'mobile'  => true,
        ),
        array(
            'slug'    => 'rooms',
            'title'   => 'Rooms',
            'builder' => 'hc_build_rooms_archive_page',
            'menu'    => true,
        ),
        array(
            'slug'    => 'restaurant',
            'title'   => 'Coriander Restaurant',
            'builder' => 'hc_build_restaurant_page',
            'menu'    => true,
        ),
        array(
            'slug'    => 'banquet-hall',
            'title'   => 'Banquet Hall',
            'builder' => 'hc_build_banquet_page',
            'menu'    => true,
        ),
        array(
            'slug'    => 'conference-room',
            'title'   => 'Board Room',
            'builder' => 'hc_build_conference_page',
            'menu'    => true,
        ),
        array(
            'slug'    => 'facilities',
            'title'   => 'Facilities',
            'builder' => 'hc_build_facilities_page',
            'menu'    => false,
            'mobile'  => true,
        ),
        array(
            'slug'    => 'gallery',
            'title'   => 'Gallery',
            'builder' => 'hc_build_gallery_page',
            'menu'    => true,
        ),
        array(
            'slug'    => 'contact-us',
            'title'   => 'Contact Us',
            'builder' => 'hc_build_contact_page',
            'menu'    => true,
        ),
    );
}

/* ============================================================
 * Per-page builders (all use native Divi modules via includes/divi-builders.php)
 * ============================================================ */

function hc_build_home_page() {
    return hc_divi_hero()
         . hc_divi_home_about()
         . hc_divi_category_cards()
         . hc_divi_facilities()
         . hc_divi_rooms_section()
         . hc_divi_gallery_slider()
         . hc_divi_testimonials()
         . hc_divi_awards();
}

function hc_build_about_page() {
    return hc_divi_page_title( 'About Us' )
         . hc_divi_about_intro()
         . hc_divi_counters()
         . hc_divi_facilities( 'Why Choose', 'Our Hotel' )
         . hc_divi_gallery_slider();
}

function hc_build_rooms_archive_page() {
    // Use the same per-room native Divi blurb stack as the home page so
    // every room card on /rooms/ is also clickable / editable in Divi Builder.
    return hc_divi_page_title( 'Rooms' )
         . hc_divi_rooms_section( '', 'Choose Your Room' );
}

function hc_build_restaurant_page() {
    return hc_divi_page_title( 'Coriander Restaurant', array( array( 'label' => 'Facilities', 'url' => home_url( '/facilities/' ) ) ) )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="60px||60px||true|false"]'
            . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
            . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
            . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em" header_2_font="Poppins|600|||||||" header_2_text_color="#14141e" header_2_font_size="32px"]'
            . '<h2>Coriander Restaurant</h2>'
            . '<p>Coriander — our restaurant serves a blend of popular cuisines including North Indian, South Indian, Gujarati, Oriental as well as Continental. Coriander offers a delectable choice of starters and main courses. Guests can relish an array of Indian and global dishes with the choice of vegetarian and Jain preparations. The restaurant is open to hotel guests as well as outside patrons. We serve breakfast, lunch and dinner.</p>'
            . '[/et_pb_text]'
            . '[/et_pb_column]'
            . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
            . hc_divi_restaurant_hours()
            . '[/et_pb_column][/et_pb_row][/et_pb_section]'
         . hc_divi_category_grid( 'restaurant_cuisines', 'Menu', 'Our Cuisines', 3 );
}

function hc_build_banquet_page() {
    $theme_uri = get_stylesheet_directory_uri();
    return hc_divi_page_title( 'Banquet Hall', array( array( 'label' => 'Facilities', 'url' => home_url( '/facilities/' ) ) ) )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="80px||60px||true|false"]'
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em" header_2_font="Poppins|600|||||||" header_2_text_color="#14141e" header_2_font_size="32px" header_2_text_align="center" text_text_align="center"]'
            . '<h2>Grand Banquet Hall in Ahmedabad</h2>'
            . '<p>From weddings to corporate gatherings, our banquet hall at Hotel Cosmopolitan sets the perfect stage. With elegant decor, modern AV, ample capacity and a curated catering menu by Coriander, it\'s a versatile venue for any occasion.</p>'
            . '[/et_pb_text]'
            . '[et_pb_image src="' . esc_url( $theme_uri . '/assets/images/gallery/banquet-hall-1.webp' ) . '" alt="Banquet Hall" _builder_version="4.20.0" custom_margin="30px||30px||true|false"][/et_pb_image]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]'
         . hc_divi_category_grid( 'banquet_categories', 'Occasions', 'Events We Host', 3 )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="60px||60px||true|false"]'
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . '[et_pb_button button_text="Enquire About Banquet Hall" button_url="' . esc_url( home_url( '/contact-us/' ) ) . '" button_alignment="center" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||"][/et_pb_button]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

function hc_build_conference_page() {
    $theme_uri = get_stylesheet_directory_uri();
    return hc_divi_page_title( 'Board Room', array( array( 'label' => 'Facilities', 'url' => home_url( '/facilities/' ) ) ) )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="80px||60px||true|false"]'
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em" header_2_font="Poppins|600|||||||" header_2_text_color="#14141e" header_2_font_size="32px" header_2_text_align="center" text_text_align="center"]'
            . '<h2>A Space for Innovation and Collaboration</h2>'
            . '<p>Our fully-equipped board room is built for productive conversations. Conference-grade AV, fast Wi-Fi, comfortable seating and on-call Coriander catering — everything you need for meetings, training and presentations.</p>'
            . '[/et_pb_text]'
            . '[et_pb_image src="' . esc_url( $theme_uri . '/assets/images/corona-hall/corona-hall-1.webp' ) . '" alt="Board Room" _builder_version="4.20.0" custom_margin="30px||30px||true|false"][/et_pb_image]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]'
         . hc_divi_category_grid( 'conference_categories', 'Use Cases', 'Perfect For', 3 )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="60px||60px||true|false"]'
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . '[et_pb_button button_text="Book the Board Room" button_url="' . esc_url( home_url( '/contact-us/' ) ) . '" button_alignment="center" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||"][/et_pb_button]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

function hc_build_facilities_page() {
    $theme_uri = get_stylesheet_directory_uri();
    return hc_divi_page_title( 'Facilities' )
         . hc_divi_facilities( 'Facilities', 'Why Choose Us' )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="60px||80px||true|false"]'
            . '[et_pb_row column_structure="1_3,1_3,1_3" _builder_version="4.20.0"]'
            . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
            . '[et_pb_blurb title="Coriander Restaurant" url="' . esc_url( home_url( '/restaurant/' ) ) . '" image="' . esc_url( $theme_uri . '/assets/images/home/restaurent.webp' ) . '" _builder_version="4.20.0" header_level="h3" header_font="Poppins|600|||||||" header_text_align="center" body_text_align="center" text_orientation="center"]Multi-cuisine fine dining — North Indian, South Indian, Gujarati, Oriental and Continental.[/et_pb_blurb]'
            . '[/et_pb_column]'
            . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
            . '[et_pb_blurb title="Banquet Hall" url="' . esc_url( home_url( '/banquet-hall/' ) ) . '" image="' . esc_url( $theme_uri . '/assets/images/home/banquet-hall.webp' ) . '" _builder_version="4.20.0" header_level="h3" header_font="Poppins|600|||||||" header_text_align="center" body_text_align="center" text_orientation="center"]Grand banquet hall for weddings, conferences and celebrations.[/et_pb_blurb]'
            . '[/et_pb_column]'
            . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
            . '[et_pb_blurb title="Board Room" url="' . esc_url( home_url( '/conference-room/' ) ) . '" image="' . esc_url( $theme_uri . '/assets/images/home/conference-room.webp' ) . '" _builder_version="4.20.0" header_level="h3" header_font="Poppins|600|||||||" header_text_align="center" body_text_align="center" text_orientation="center"]Fully-equipped conference and board-room facilities for corporate gatherings.[/et_pb_blurb]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

function hc_build_gallery_page() {
    return hc_divi_page_title( 'Gallery' )
         . hc_divi_gallery_grid();
}

function hc_build_contact_page() {
    return hc_divi_page_title( 'Contact' )
         . '[et_pb_section fb_built="1" _builder_version="4.20.0" custom_padding="80px||60px||true|false"]'
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . hc_divi_heading( 'Contact Us', 'Send a Message' )
            . '[/et_pb_column][/et_pb_row]'
            . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
            . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
            . '[et_pb_code _builder_version="4.20.0"][hc_inquiry_form variant="inquiry"][/et_pb_code]'
            . '[/et_pb_column]'
            . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
            . hc_divi_contact_info_block()
            . '[/et_pb_column][/et_pb_row][/et_pb_section]'
         . hc_divi_map_block();
}

/**
 * Build/update the primary nav menu.
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

    if ( wp_get_nav_menu_items( $menu_id ) ) {
        // Don't overwrite an existing populated menu (might be hand-edited)
    } else {
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
    }

    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary-menu'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * SEO landing pages — same Divi-native section stack as the home page,
 * but the about block carries the page's prose content from the original
 * public_html/*.php files (pre-extracted into data/seo-landing-pages.json).
 */
function hc_pages_seed_seo_pages() {
    $path = HC_ROOMS_DIR . 'data/seo-landing-pages.json';
    if ( ! file_exists( $path ) ) return;

    $json = json_decode( file_get_contents( $path ), true );
    if ( ! is_array( $json ) ) return;

    foreach ( $json as $entry ) {
        $slug = isset( $entry['slug'] ) ? sanitize_title( $entry['slug'] ) : '';
        if ( ! $slug ) continue;

        $title = isset( $entry['title'] ) ? wp_strip_all_tags( $entry['title'] ) : ucfirst( str_replace( '-', ' ', $slug ) );
        $title = ucwords( strtolower( $title ) );
        $prose = ! empty( $entry['content'] ) ? $entry['content'] : '<p>Content coming soon.</p>';

        $body = hc_divi_page_title( $title )
              . hc_divi_about_with_inquiry( $title, 'Welcome To', $prose )
              . hc_divi_awards()
              . hc_divi_rooms_section()
              . hc_divi_facilities()
              . hc_divi_testimonials()
              . hc_divi_counters()
              . hc_divi_gallery_slider()
              . hc_divi_inquiry_section();

        $existing = get_page_by_path( $slug );
        if ( $existing ) {
            wp_update_post( array(
                'ID'           => $existing->ID,
                'post_title'   => $title,
                'post_content' => $body,
            ) );
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post( array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $body,
            ) );
            if ( is_wp_error( $post_id ) || ! $post_id ) continue;
        }

        update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
        update_post_meta( $post_id, '_et_pb_page_layout', 'et_no_sidebar' );

        if ( ! empty( $entry['meta_title'] ) ) {
            update_post_meta( $post_id, '_yoast_wpseo_title',     $entry['meta_title'] );
            update_post_meta( $post_id, 'rank_math_title',         $entry['meta_title'] );
            update_post_meta( $post_id, '_hc_original_meta_title', $entry['meta_title'] );
        }
        if ( ! empty( $entry['meta_description'] ) ) {
            update_post_meta( $post_id, '_yoast_wpseo_metadesc',   $entry['meta_description'] );
            update_post_meta( $post_id, 'rank_math_description',   $entry['meta_description'] );
            update_post_meta( $post_id, '_hc_original_meta_desc',  $entry['meta_description'] );
        }
    }

    update_option( 'hc_seo_pages_seeded_v3', 1 );
}

/**
 * Static text pages — FAQ, policies, news-blogs index.
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
        . '<p>Hotel Cosmopolitan respects your privacy and is committed to protecting the personal data you share with us.</p>'
        . '<h4>Information We Collect</h4>'
        . '<ul><li>Contact details (name, email, phone) provided through booking forms and inquiries.</li>'
        . '<li>Reservation details (dates, room preferences, special requests).</li>'
        . '<li>Technical data (IP address, browser type) for security and analytics.</li></ul>'
        . '<h4>How We Use It</h4>'
        . '<ul><li>To process bookings and respond to inquiries.</li>'
        . '<li>To send confirmations, updates, and (with consent) promotional offers.</li>'
        . '<li>To improve our website and services.</li></ul>'
        . '<h4>Sharing</h4>'
        . '<p>We do not sell your personal data. We may share it with trusted service providers (payment processors, booking platforms) strictly as needed to provide services to you.</p>'
        . '<h4>Contact</h4>'
        . '<p>Questions? Email <a href="mailto:reserve@hotelcosmopolitan.in">reserve@hotelcosmopolitan.in</a>.</p>';

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
        . '<p>Hotel Cosmopolitan is not responsible for loss or damage to personal belongings beyond what is required by applicable Indian law.</p>';

    return array(
        array( 'slug' => 'faq',                  'title' => "FAQ's",               'content' => $faq_html ),
        array( 'slug' => 'reservation-policy',   'title' => 'Reservation Policy',  'content' => $reservation_html ),
        array( 'slug' => 'cancellation-policy',  'title' => 'Cancellation Policy', 'content' => $cancellation_html ),
        array( 'slug' => 'privacy-policy',       'title' => 'Privacy Policy',      'content' => $privacy_html ),
        array( 'slug' => 'terms-condition',      'title' => 'Terms & Conditions',  'content' => $terms_html ),
        array( 'slug' => 'news-blogs',           'title' => 'News & Blogs',        'content' => '[hc_blogs_grid limit="9" columns="3"]' ),
    );
}
