<?php
/**
 * Divi shortcode builders.
 *
 * These return strings of native et_pb_* shortcodes that the page-seeder
 * embeds into post_content. The result: every page opens in Divi Builder
 * with editable native modules — sliders, blurbs, testimonials, number
 * counters, galleries, images, text — instead of opaque [hc_*] custom
 * shortcode wrappers.
 *
 * Only the truly-dynamic sections still use custom shortcodes:
 *   - [hc_rooms_grid]     (queries the room CPT)
 *   - [hc_inquiry_form]   (saves to DB + emails)
 *   - [hc_footer_widgets] (richer than Divi's default footer)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------- internal helpers ---------- */

function hc_divi_section_open( $bg = '', $padding = '80px||80px||true|false' ) {
    $atts = ' fb_built="1" _builder_version="4.20.0" custom_padding="' . esc_attr( $padding ) . '"';
    if ( $bg ) $atts .= ' background_color="' . esc_attr( $bg ) . '"';
    return '[et_pb_section' . $atts . ']';
}

function hc_divi_heading( $eyebrow, $title, $align = 'center', $color = '#14141e' ) {
    $align_attr = 'header_2_text_align="' . esc_attr( $align ) . '" text_text_align="' . esc_attr( $align ) . '"';
    $eyebrow_html = $eyebrow
        ? '<p style="color:#D81418;letter-spacing:2px;font-size:13px;text-transform:uppercase;margin:0 0 6px;font-weight:600;text-align:' . esc_attr( $align ) . ';">' . esc_html( $eyebrow ) . '</p>'
        : '';
    return '[et_pb_text _builder_version="4.20.0" header_2_font="Poppins|600|||||||" header_2_text_color="' . esc_attr( $color ) . '" header_2_font_size="32px" ' . $align_attr . ' custom_margin="||30px||false|false"]'
         . $eyebrow_html
         . '<h2>' . wp_kses_post( $title ) . '</h2>'
         . '[/et_pb_text]';
}

/* ---------- big page-section builders ---------- */

/**
 * Hero — native et_pb_slider with 5 slides.
 * Each slide has a background image, heading and a rating-source overlay.
 */
function hc_divi_hero() {
    $slides = hc_get( 'hero_slides' );
    if ( ! is_array( $slides ) || ! $slides ) return '';

    $slide_shortcodes = '';
    foreach ( $slides as $i => $s ) {
        $bg = ! empty( $s['image'] ) ? hc_image_url( $s['image'], 'full' ) : '';
        $title = isset( $s['title'] ) ? wp_strip_all_tags( $s['title'] ) : '';

        // Rating overlay as inline HTML in slide body
        $rating_html = '';
        if ( ! empty( $s['rating_source'] ) ) {
            $icon_url = ! empty( $s['rating_icon'] ) ? hc_image_url( $s['rating_icon'] ) : '';
            $rating_html = '<div style="display:inline-block;background:rgba(255,255,255,.12);padding:10px 20px;margin-top:20px;backdrop-filter:blur(6px);">'
                . ( $icon_url ? '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $s['rating_source'] ) . '" style="width:24px;height:24px;vertical-align:middle;margin-right:8px;">' : '' )
                . '<strong>' . esc_html( $s['rating_value'] ?? '' ) . '</strong> ★ | '
                . esc_html( $s['rating_count'] ?? '' ) . ' Reviews on '
                . esc_html( $s['rating_source'] )
                . '</div>';
        }

        $slide_shortcodes .= '[et_pb_slide'
            . ' heading="' . esc_attr( $title ) . '"'
            . ( $bg ? ' background_image="' . esc_url( $bg ) . '"' : '' )
            . ' background_color="rgba(20,20,30,0.55)"'
            . ' background_layout="dark"'
            . ' _builder_version="4.20.0"'
            . ' header_font="Poppins|700|||||||"'
            . ' header_font_size="48px"'
            . ' header_text_color="#ffffff"'
            . ' background_blend="multiply"'
            . ' use_bg_overlay="on"'
            . ' bg_overlay_color="rgba(20,20,30,0.55)"'
            . ' button_text=""'
            . ' button_link=""'
            . ']' . $rating_html . '[/et_pb_slide]';
    }

    return '[et_pb_section fb_built="1" fullwidth="on" _builder_version="4.20.0" custom_padding="0|0|0|0|false|false"]'
        . '[et_pb_fullwidth_slider _builder_version="4.20.0" auto="on" auto_speed="6000" show_arrows="on" show_pagination="on"]'
        . $slide_shortcodes
        . '[/et_pb_fullwidth_slider][/et_pb_section]';
}

/**
 * About + side image + Quick Inquiry — used on SEO landings.
 */
function hc_divi_about_with_inquiry( $title, $eyebrow, $body_html ) {
    $theme_uri = get_stylesheet_directory_uri();
    return hc_divi_section_open()
        . '[et_pb_row column_structure="1_2,1_4,1_4" _builder_version="4.20.0"]'

        . '[et_pb_column type="1_2" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em" header_3_font="Poppins|600|||||||" header_3_text_color="#14141e"]'
        . '<p style="color:#D81418;letter-spacing:2px;font-size:13px;text-transform:uppercase;margin:0 0 8px;font-weight:600;">' . esc_html( $eyebrow ) . '</p>'
        . '<h3 style="font-size:28px;margin:0 0 20px;">' . esc_html( $title ) . '</h3>'
        . wp_kses_post( $body_html )
        . '[/et_pb_text][/et_pb_column]'

        . '[et_pb_column type="1_4" _builder_version="4.20.0"]'
        . '[et_pb_image src="' . esc_url( $theme_uri . '/assets/images/building.webp' ) . '" alt="Hotel Cosmopolitan" _builder_version="4.20.0"][/et_pb_image]'
        . '[/et_pb_column]'

        . '[et_pb_column type="1_4" _builder_version="4.20.0"]'
        . '[et_pb_code _builder_version="4.20.0"][hc_inquiry_form variant="booking" title="Quick Inquiry"][/et_pb_code]'
        . '[/et_pb_column]'

        . '[/et_pb_row][/et_pb_section]';
}

/**
 * 4 category cards (Rooms / Restaurant / Banquet Hall / Board Room).
 * Native Divi blurbs in a 4-column row.
 */
function hc_divi_category_cards() {
    $theme_uri = get_stylesheet_directory_uri();
    $cards = array(
        array( 'label' => 'Rooms',        'url' => home_url( '/rooms/' ),           'src' => $theme_uri . '/assets/images/home/room.webp' ),
        array( 'label' => 'Restaurant',   'url' => home_url( '/restaurant/' ),      'src' => $theme_uri . '/assets/images/home/restaurent.webp' ),
        array( 'label' => 'Banquet Hall', 'url' => home_url( '/banquet-hall/' ),    'src' => $theme_uri . '/assets/images/home/banquet-hall.webp' ),
        array( 'label' => 'Board Room',   'url' => home_url( '/conference-room/' ), 'src' => $theme_uri . '/assets/images/home/conference-room.webp' ),
    );

    $cols = '';
    foreach ( $cards as $c ) {
        $cols .= '[et_pb_column type="1_4" _builder_version="4.20.0"]'
              . '[et_pb_blurb'
              . ' title="' . esc_attr( $c['label'] ) . '"'
              . ' url="' . esc_url( $c['url'] ) . '"'
              . ' image="' . esc_url( $c['src'] ) . '"'
              . ' _builder_version="4.20.0"'
              . ' header_level="h4"'
              . ' header_font="Poppins|600|on||||||"'
              . ' header_text_align="center"'
              . ' header_font_size="20px"'
              . ' text_orientation="center"'
              . ' image_max_width="100%"'
              . ' module_alignment="center"'
              . ']Discover our ' . esc_html( strtolower( $c['label'] ) ) . '.[/et_pb_blurb]'
              . '[/et_pb_column]';
    }

    return hc_divi_section_open( '', '60px||60px||true|false' )
        . '[et_pb_row column_structure="1_4,1_4,1_4,1_4" _builder_version="4.20.0"]'
        . $cols
        . '[/et_pb_row][/et_pb_section]';
}

/**
 * 6 facility icons ("Why Choose Us").
 * Native Divi blurbs in a 6-column row.
 */
function hc_divi_facilities( $eyebrow = 'Facilities', $title = 'Why Choose Us' ) {
    $rows = hc_get( 'facility_icons' );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    $cols = '';
    foreach ( $rows as $r ) {
        $icon_url = ! empty( $r['icon'] ) ? hc_image_url( $r['icon'] ) : '';
        $cols .= '[et_pb_column type="1_6" _builder_version="4.20.0"]'
              . '[et_pb_blurb'
              . ' title="' . esc_attr( $r['label'] ) . '"'
              . ( $icon_url ? ' image="' . esc_url( $icon_url ) . '"' : '' )
              . ' _builder_version="4.20.0"'
              . ' header_level="h5"'
              . ' header_font="Poppins|600|||||||"'
              . ' header_text_align="center"'
              . ' header_font_size="14px"'
              . ' header_letter_spacing="1px"'
              . ' text_orientation="center"'
              . ' image_max_width="60px"'
              . ' module_alignment="center"'
              . ' custom_margin="||20px||false|false"'
              . '][/et_pb_blurb]'
              . '[/et_pb_column]';
    }

    return hc_divi_section_open( '#f7f7f7' )
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( $eyebrow, $title )
        . '[/et_pb_column][/et_pb_row]'
        . '[et_pb_row column_structure="1_6,1_6,1_6,1_6,1_6,1_6" _builder_version="4.20.0"]'
        . $cols
        . '[/et_pb_row][/et_pb_section]';
}

/**
 * Rooms section — queries the room CPT at seed time and emits ONE
 * stack of native Divi modules per room (image + text + booking buttons)
 * so the client can click each room card and edit it individually in
 * Divi Builder.
 *
 * Layout: 2 rooms per row. Each cell is an et_pb_image + et_pb_text
 * (title + meta + amenities) + et_pb_button(s) + a Know More text link.
 *
 * NOTE: this generates STATIC Divi content. If the client adds a new
 * room type via Rooms admin, they need to also add a corresponding
 * card to the home/landing pages in Divi Builder (clone an existing
 * room column and re-point it).
 */
function hc_divi_rooms_section( $eyebrow = 'Explore', $title = 'Our Rooms', $limit = -1 ) {

    $q = new WP_Query( array(
        'post_type'      => 'room',
        'posts_per_page' => intval( $limit ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    if ( ! $q->have_posts() ) {
        // No rooms exist yet — fall back to the dynamic shortcode so
        // the page isn't blank if the seeder runs before rooms.
        return hc_divi_section_open()
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . hc_divi_heading( $eyebrow, $title )
            . '[et_pb_code _builder_version="4.20.0"][hc_rooms_grid columns="2"][/et_pb_code]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';
    }

    $rooms = $q->posts;
    wp_reset_postdata();

    // Build a card markup per room
    $room_cards = array();
    foreach ( $rooms as $room ) {
        $room_cards[] = hc_divi_single_room_card( $room->ID );
    }

    // Chunk into rows of 2
    $rows_markup = '';
    foreach ( array_chunk( $room_cards, 2 ) as $pair ) {
        $rows_markup .= '[et_pb_row column_structure="1_2,1_2" _builder_version="4.20.0" custom_padding="0||30px||false|false"]';
        $rows_markup .= '[et_pb_column type="1_2" _builder_version="4.20.0"]' . $pair[0] . '[/et_pb_column]';
        if ( isset( $pair[1] ) ) {
            $rows_markup .= '[et_pb_column type="1_2" _builder_version="4.20.0"]' . $pair[1] . '[/et_pb_column]';
        } else {
            $rows_markup .= '[et_pb_column type="1_2" _builder_version="4.20.0"][/et_pb_column]';
        }
        $rows_markup .= '[/et_pb_row]';
    }

    return hc_divi_section_open()
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( $eyebrow, $title )
        . '[/et_pb_column][/et_pb_row]'
        . $rows_markup
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . '[et_pb_button button_text="View All Rooms" button_url="' . esc_url( home_url( '/rooms/' ) ) . '" button_alignment="center" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||" custom_margin="30px||||false|false"][/et_pb_button]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Render a single room as a stack of editable native Divi modules:
 *   - et_pb_image      (featured image, linked to the room page)
 *   - et_pb_text       (title + bed/size meta + amenities as a 2-col HTML list)
 *   - et_pb_button × N (one per booking option, e.g. Room Only / Room + Breakfast)
 *   - et_pb_text       ("Know More" link)
 *
 * Returns the concatenated shortcode string (no outer column wrapper).
 */
function hc_divi_single_room_card( $room_id ) {

    $room_url = get_permalink( $room_id );
    $title    = get_the_title( $room_id );
    $thumb_id = get_post_thumbnail_id( $room_id );
    $img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

    $bed       = hc_get( 'bed_type',        $room_id );
    $size      = hc_get( 'room_size',       $room_id );
    $amenities = hc_get( 'amenities',       $room_id );
    $options   = hc_get( 'booking_options', $room_id );

    // --- Image module ---
    $image_module = $img_url
        ? '[et_pb_image src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $title ) . '" url="' . esc_url( $room_url ) . '" _builder_version="4.20.0" custom_margin="0||10px||false|false"][/et_pb_image]'
        : '';

    // --- Title + meta + amenities text module ---
    $meta_line = '';
    if ( $bed || $size ) {
        $meta_line = '<p style="color:#888;font-size:14px;margin:0 0 14px;">'
                   . esc_html( trim( $bed . ( $bed && $size ? ' · ' : '' ) . $size ) )
                   . '</p>';
    }

    $amenities_html = '';
    if ( is_array( $amenities ) && $amenities ) {
        $shown = array_slice( $amenities, 0, 8 );
        $amenities_html = '<ul style="list-style:none;padding:0;margin:0 0 18px;display:grid;grid-template-columns:1fr 1fr;gap:6px 16px;font-size:14px;">';
        foreach ( $shown as $a ) {
            $amenities_html .= '<li style="color:#666;padding-left:22px;position:relative;">'
                            . '<span style="color:#D81418;font-weight:700;position:absolute;left:0;">&#10003;</span>'
                            . esc_html( $a['label'] ?? '' )
                            . '</li>';
        }
        $amenities_html .= '</ul>';
    }

    $text_module = '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" header_3_font="Poppins|600|||||||" header_3_text_color="#D81418" header_3_font_size="22px"]'
                 . '<h3>' . esc_html( $title ) . '</h3>'
                 . $meta_line
                 . $amenities_html
                 . '[/et_pb_text]';

    // --- Booking option buttons ---
    $buttons = '';
    if ( is_array( $options ) && $options ) {
        foreach ( $options as $opt ) {
            $label = $opt['label'] ?? 'Book Now';
            $url   = $opt['url']   ?? '#';
            $buttons .= '[et_pb_button'
                . ' button_text="' . esc_attr( $label . ' — Book Now' ) . '"'
                . ' button_url="' . esc_url( $url ) . '"'
                . ' url_new_window="on"'
                . ' button_alignment="left"'
                . ' _builder_version="4.20.0"'
                . ' custom_button="on"'
                . ' button_text_size="13px"'
                . ' button_text_color="#ffffff"'
                . ' button_bg_color="#D81418"'
                . ' button_border_width="2px"'
                . ' button_border_color="#D81418"'
                . ' button_letter_spacing="1px"'
                . ' button_font="Poppins|600|on||||||"'
                . ' custom_margin="0||10px||false|false"'
                . '][/et_pb_button]';
        }
    }

    // --- "Know More" text link ---
    $know_more = '[et_pb_text _builder_version="4.20.0"]'
               . '<p style="margin:14px 0 0;"><a href="' . esc_url( $room_url ) . '" style="color:#D81418;font-weight:600;text-decoration:none;">Know More &rarr;</a></p>'
               . '[/et_pb_text]';

    return $image_module . $text_module . $buttons . $know_more;
}

/**
 * Testimonials — native Divi et_pb_testimonial × N in columns.
 */
function hc_divi_testimonials() {
    $rows = hc_get( 'testimonials' );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    $col_type = count( $rows ) >= 3 ? '1_3' : ( count( $rows ) === 2 ? '1_2' : '4_4' );
    $structure = count( $rows ) >= 3 ? '1_3,1_3,1_3' : ( count( $rows ) === 2 ? '1_2,1_2' : '4_4' );

    $cols = '';
    foreach ( $rows as $t ) {
        $cols .= '[et_pb_column type="' . esc_attr( $col_type ) . '" _builder_version="4.20.0"]'
              . '[et_pb_testimonial'
              . ' author="' . esc_attr( $t['name'] ?? '' ) . '"'
              . ' use_background_color="off"'
              . ' background_layout="dark"'
              . ' _builder_version="4.20.0"'
              . ' body_font="Poppins||on||||||"'
              . ' body_text_color="#ffffff"'
              . ' author_font="Poppins|600|||||||"'
              . ' author_text_color="#ffffff"'
              . ' quote_icon_color="#D81418"'
              . ']' . wp_kses_post( $t['review'] ?? '' ) . '[/et_pb_testimonial]'
              . '[/et_pb_column]';
    }

    return hc_divi_section_open( '#14141e' )
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( 'People Reviews', '<span style="color:#fff;">What Our Guests Say</span>', 'center', '#ffffff' )
        . '[/et_pb_column][/et_pb_row]'
        . '[et_pb_row column_structure="' . esc_attr( $structure ) . '" _builder_version="4.20.0"]'
        . $cols
        . '[/et_pb_row][/et_pb_section]';
}

/**
 * Counters — native Divi number counters in a 3-column row.
 */
function hc_divi_counters() {
    $items = array(
        array( 'number' => 50,  'label' => 'Rooms' ),
        array( 'number' => 70,  'label' => 'Staff' ),
        array( 'number' => 100, 'label' => 'Dishes' ),
    );

    $cols = '';
    foreach ( $items as $c ) {
        $cols .= '[et_pb_column type="1_3" _builder_version="4.20.0"]'
              . '[et_pb_number_counter'
              . ' title="' . esc_attr( $c['label'] ) . '"'
              . ' number="' . intval( $c['number'] ) . '"'
              . ' percent_sign="off"'
              . ' counter_color="#D81418"'
              . ' _builder_version="4.20.0"'
              . ' title_font="Poppins|600|on||||||"'
              . ' title_text_color="#ffffff"'
              . ' title_letter_spacing="2px"'
              . ' number_font="Poppins|700|||||||"'
              . ' number_text_color="#D81418"'
              . ' number_font_size="56px"'
              . ' text_orientation="center"'
              . ' background_layout="dark"'
              . '][/et_pb_number_counter]'
              . '[/et_pb_column]';
    }

    return hc_divi_section_open( '#14141e', '60px||60px||true|false' )
        . '[et_pb_row column_structure="1_3,1_3,1_3" _builder_version="4.20.0"]'
        . $cols
        . '[/et_pb_row][/et_pb_section]';
}

/**
 * Gallery slider for home page (4 thumbs slider). Native Divi gallery module.
 */
function hc_divi_gallery_slider( $limit = 12 ) {
    $items = hc_get( 'gallery' );
    if ( ! is_array( $items ) || ! $items ) return '';

    $ids = array();
    foreach ( $items as $g ) {
        if ( ! empty( $g['image'] ) ) $ids[] = intval( $g['image'] );
        if ( count( $ids ) >= $limit ) break;
    }
    if ( ! $ids ) return '';

    return hc_divi_section_open()
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( 'Gallery', 'Take a Tour of Our Hotel' )
        . '[et_pb_gallery gallery_ids="' . esc_attr( implode( ',', $ids ) ) . '" fullwidth="on" show_title_and_caption="off" show_pagination="off" _builder_version="4.20.0" auto="on" auto_speed="5000"][/et_pb_gallery]'
        . '[et_pb_button button_text="View All" button_url="' . esc_url( home_url( '/gallery/' ) ) . '" button_alignment="center" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||" custom_margin="30px||||false|false"][/et_pb_button]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Full gallery page (grid layout, lots of items). Uses Divi gallery in grid mode.
 */
function hc_divi_gallery_grid() {
    $items = hc_get( 'gallery' );
    if ( ! is_array( $items ) || ! $items ) return '';

    $ids = array();
    foreach ( $items as $g ) {
        if ( ! empty( $g['image'] ) ) $ids[] = intval( $g['image'] );
    }
    if ( ! $ids ) return '';

    return hc_divi_section_open()
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . '[et_pb_gallery gallery_ids="' . esc_attr( implode( ',', $ids ) ) . '" posts_number="50" show_title_and_caption="off" show_pagination="on" _builder_version="4.20.0"][/et_pb_gallery]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Awards — native Divi gallery in slider mode.
 */
function hc_divi_awards() {
    $ids = hc_get( 'awards' );
    if ( ! is_array( $ids ) || ! $ids ) return '';
    $ids = array_map( 'intval', $ids );

    return hc_divi_section_open( '#f7f7f7' )
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( 'Recognition', 'Awards and Accolades' )
        . '[et_pb_gallery gallery_ids="' . esc_attr( implode( ',', $ids ) ) . '" fullwidth="on" show_title_and_caption="off" show_pagination="off" _builder_version="4.20.0" auto="on" auto_speed="6000"][/et_pb_gallery]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Generic image+label tile grid — used for banquet categories, conference
 * categories, restaurant cuisines.
 */
function hc_divi_category_grid( $field_key, $eyebrow = '', $title = '', $columns = 3 ) {
    $rows = hc_get( $field_key );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    $col_type = '1_' . $columns;
    $structure = implode( ',', array_fill( 0, $columns, $col_type ) );

    $out = hc_divi_section_open( '#f7f7f7' )
         . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
         . hc_divi_heading( $eyebrow, $title )
         . '[/et_pb_column][/et_pb_row]';

    $chunks = array_chunk( $rows, $columns );
    foreach ( $chunks as $chunk ) {
        $cols = '';
        foreach ( $chunk as $r ) {
            $img_url = ! empty( $r['image'] ) ? hc_image_url( $r['image'], 'large' ) : '';
            $cols .= '[et_pb_column type="' . esc_attr( $col_type ) . '" _builder_version="4.20.0"]'
                  . '[et_pb_blurb'
                  . ' title="' . esc_attr( $r['label'] ?? '' ) . '"'
                  . ( $img_url ? ' image="' . esc_url( $img_url ) . '"' : '' )
                  . ' _builder_version="4.20.0"'
                  . ' header_level="h5"'
                  . ' header_font="Poppins|600|on||||||"'
                  . ' header_text_align="center"'
                  . ' header_font_size="18px"'
                  . ' header_letter_spacing="1px"'
                  . ' text_orientation="center"'
                  . ' custom_margin="||20px||false|false"'
                  . '][/et_pb_blurb]'
                  . '[/et_pb_column]';
        }
        // Pad incomplete row with empty columns so layout stays balanced
        while ( count( $chunk ) < $columns ) {
            $cols .= '[et_pb_column type="' . esc_attr( $col_type ) . '" _builder_version="4.20.0"][/et_pb_column]';
            $chunk[] = null;
        }
        $out .= '[et_pb_row column_structure="' . esc_attr( $structure ) . '" _builder_version="4.20.0"]' . $cols . '[/et_pb_row]';
    }

    return $out . '[/et_pb_section]';
}

/**
 * Restaurant hours sidebar — native Divi text module (editable).
 */
function hc_divi_restaurant_hours() {
    $rows    = hc_get( 'restaurant_hours' );
    $qr_id   = hc_get( 'restaurant_qr' );
    $reserve = hc_get( 'restaurant_reserve_url' );

    if ( ! is_array( $rows ) ) $rows = array();

    $rows_html = '<ul style="list-style:none;padding:0;margin:0;">';
    foreach ( $rows as $r ) {
        $rows_html .= '<li style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eee;">'
            . '<strong>' . esc_html( $r['meal'] ?? '' ) . '</strong>'
            . '<span>' . esc_html( $r['time'] ?? '' ) . '</span>'
            . '</li>';
    }
    $rows_html .= '</ul>';

    $reserve_btn = $reserve
        ? '[et_pb_button button_text="Reserve a Table" button_url="' . esc_url( $reserve ) . '" url_new_window="on" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||" custom_margin="20px||||false|false"][/et_pb_button]'
        : '';

    $qr_url = $qr_id ? hc_image_url( $qr_id ) : '';
    $qr_block = $qr_url
        ? '[et_pb_text _builder_version="4.20.0" custom_margin="30px||0px||false|false"]<h5>Scan QR to View Menu</h5>[/et_pb_text]'
          . '[et_pb_image src="' . esc_url( $qr_url ) . '" alt="Menu QR" _builder_version="4.20.0" max_width="180px" module_alignment="left"][/et_pb_image]'
        : '';

    return '[et_pb_text _builder_version="4.20.0" background_color="#f8f8f8" custom_padding="30px|30px|30px|30px|false|false"]'
        . '<h3 style="margin-top:0;">Restaurant Hours</h3>'
        . $rows_html
        . '[/et_pb_text]'
        . $reserve_btn
        . $qr_block;
}

/**
 * Contact info block (native text module — editable).
 */
function hc_divi_contact_info_block() {
    $phones  = hc_get( 'phones' );
    $emails  = hc_get( 'emails' );
    $address = hc_get( 'address' );

    $body = '<h3 style="margin-top:0;color:#14141e;">Get in touch</h3>';

    if ( is_array( $phones ) && $phones ) {
        $body .= '<h6 style="color:#14141e;font-size:13px;letter-spacing:1px;margin:18px 0 6px;">Phone</h6>';
        foreach ( $phones as $p ) {
            $tel = preg_replace( '/[^0-9+]/', '', $p['value'] ?? '' );
            $body .= '<p><a href="tel:' . esc_attr( $tel ) . '" style="color:#666;">' . esc_html( $p['value'] ?? '' ) . '</a></p>';
        }
    }
    if ( is_array( $emails ) && $emails ) {
        $body .= '<h6 style="color:#14141e;font-size:13px;letter-spacing:1px;margin:18px 0 6px;">Email</h6>';
        foreach ( $emails as $e ) {
            $body .= '<p><a href="mailto:' . esc_attr( $e['value'] ?? '' ) . '" style="color:#666;">' . esc_html( $e['value'] ?? '' ) . '</a></p>';
        }
    }
    if ( $address ) {
        $body .= '<h6 style="color:#14141e;font-size:13px;letter-spacing:1px;margin:18px 0 6px;">Address</h6>';
        $body .= '<p style="color:#666;">' . esc_html( $address ) . '</p>';
    }

    return '[et_pb_text _builder_version="4.20.0" background_color="#f7f7f7" custom_padding="30px|30px|30px|30px|false|false"]' . $body . '[/et_pb_text]';
}

/**
 * Google Map — iframe inside a code module (Divi's native map module requires
 * an API key which we can't bundle).
 */
function hc_divi_map_block() {
    $url = hc_get( 'map_url' );
    if ( ! $url ) return '';
    return '[et_pb_section fb_built="1" fullwidth="on" _builder_version="4.20.0" custom_padding="0|0|0|0|false|false"]'
        . '[et_pb_fullwidth_code _builder_version="4.20.0"]'
        . '<iframe src="' . esc_url( $url ) . '" width="100%" height="450" style="border:0;display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
        . '[/et_pb_fullwidth_code][/et_pb_section]';
}

/**
 * Page-title hero band (used at the top of non-home pages).
 */
function hc_divi_page_title( $title, $breadcrumbs = array() ) {
    $crumb_html = '<ul style="list-style:none;padding:0;margin:10px 0 0;display:flex;justify-content:center;gap:8px;font-size:13px;color:rgba(255,255,255,0.85);">';
    $crumb_html .= '<li><a href="' . esc_url( home_url( '/' ) ) . '" style="color:rgba(255,255,255,0.85);">Home</a></li>';
    foreach ( $breadcrumbs as $crumb ) {
        $crumb_html .= '<li>/</li>';
        if ( ! empty( $crumb['url'] ) ) {
            $crumb_html .= '<li><a href="' . esc_url( $crumb['url'] ) . '" style="color:rgba(255,255,255,0.85);">' . esc_html( $crumb['label'] ) . '</a></li>';
        } else {
            $crumb_html .= '<li style="color:#D81418;">' . esc_html( $crumb['label'] ) . '</li>';
        }
    }
    $crumb_html .= '<li>/</li><li style="color:#D81418;">' . esc_html( $title ) . '</li>';
    $crumb_html .= '</ul>';

    return '[et_pb_section fb_built="1" _builder_version="4.20.0" background_color="#14141e" custom_padding="180px||80px||false|false"]'
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0" header_font="Poppins|700|on||||||" header_text_color="#ffffff" header_font_size="42px" text_text_align="center" header_text_align="center" header_letter_spacing="2px"]'
        . '<h1>' . esc_html( $title ) . '</h1>'
        . $crumb_html
        . '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Inquiry form section (custom shortcode wrapped in editable Divi heading).
 */
function hc_divi_inquiry_section( $eyebrow = 'Contact Us', $title = 'Send a Message', $variant = 'inquiry' ) {
    return hc_divi_section_open( '#f7f7f7' )
        . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
        . hc_divi_heading( $eyebrow, $title )
        . '[/et_pb_column][/et_pb_row]'
        . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
        . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
        . '[et_pb_code _builder_version="4.20.0"][hc_inquiry_form variant="' . esc_attr( $variant ) . '"][/et_pb_code]'
        . '[/et_pb_column]'
        . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
        . hc_divi_contact_info_block()
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Home page about block (different layout from SEO landing about — has the
 * SwiftBook quick-book widget on the right and no image).
 */
function hc_divi_home_about() {
    $body = '<p style="color:#D81418;letter-spacing:2px;font-size:13px;text-transform:uppercase;margin:0 0 8px;font-weight:600;">Know About Luxury Hotel</p>'
          . '<h3 style="font-size:32px;margin:0 0 20px;">3-Star Hotel Near C.G Road, Ahmedabad</h3>'
          . '<p>Discover a seamless blend of convenience and comfort at Hotel Cosmopolitan, a <strong>3 Star Boutique Hotel in Navrangpura, Ahmedabad</strong>, located in the bustling heart of the city center. Nestled in Navrangpura, Ahmedabad, Hotel Cosmopolitan is a great choice for travelers looking for premium facilities and exceptional service.</p>';

    return hc_divi_section_open()
        . '[et_pb_row column_structure="1_2,1_2" _builder_version="4.20.0"]'
        . '[et_pb_column type="1_2" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em"]'
        . $body
        . '[/et_pb_text]'
        . '[et_pb_button button_text="Read More" button_url="' . esc_url( home_url( '/about-us/' ) ) . '" _builder_version="4.20.0" custom_button="on" button_text_size="14px" button_text_color="#ffffff" button_bg_color="#D81418" button_border_width="2px" button_border_color="#D81418" button_letter_spacing="2px" button_font="Poppins|600|on||||||"][/et_pb_button]'
        . '[/et_pb_column]'
        . '[et_pb_column type="1_2" _builder_version="4.20.0"]'
        . '[et_pb_code _builder_version="4.20.0"]<div id="quickbook-widget"></div><script src="https://www.swiftbook.io/plugin/js/booking-service.min.js" id="propInfo" propertyid="921MM8J0Tix3XchxUnohbkipepNdr7jyROMRjP9SkuUky56wI3jE2Ng==" cal-rendererId="quickbook-widget" JDRN="Y" redirect="off"></script>[/et_pb_code]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * About page intro — text + building image side by side.
 */
function hc_divi_about_intro() {
    $theme_uri = get_stylesheet_directory_uri();
    $body = '<p style="color:#D81418;letter-spacing:2px;font-size:13px;text-transform:uppercase;margin:0 0 8px;font-weight:600;">Welcome To</p>'
          . '<h3 style="font-size:32px;margin:0 0 20px;">Our Hotel</h3>'
          . '<p>Discover a seamless blend of convenience and comfort at Hotel Cosmopolitan located in the bustling heart of the city center. Nestled in Navrangpura, Ahmedabad, Hotel Cosmopolitan is a great choice for travelers looking for a 4 star hotel.</p>'
          . '<p>From all the 4 Star hotels in Ahmedabad, Hotel Cosmopolitan is very much popular among the tourists. Our prime location ensures that you are at the center of all the action, with business areas, cultural attractions, and vibrant entertainment options just steps away.</p>'
          . '<p>After a day of business endeavors or exploring city, unwind in our comfortable accommodations and take advantage of our in-house restaurant - Coriander, ensuring that your stay is both productive and pleasurable.</p>'
          . '<p>Welcome to Hotel Cosmopolitan that understands the rhythm of your business travels as well as family vacations while placing you at the heart of the city\'s vibrant energy.</p>';

    return hc_divi_section_open()
        . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
        . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em"]'
        . $body
        . '[/et_pb_text]'
        . '[/et_pb_column]'
        . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
        . '[et_pb_image src="' . esc_url( $theme_uri . '/assets/images/building.webp' ) . '" alt="Hotel Cosmopolitan building" _builder_version="4.20.0"][/et_pb_image]'
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}

/**
 * Rooms archive table layout — matches the original site's /rooms/ page.
 *
 * Each row of the table = one room, with 3 Divi columns:
 *   1. (1/4)  Image + room title + "Know More" link
 *   2. (2/4)  Amenities — 2-column grid with red checkmarks
 *   3. (1/4)  Booking-option buttons (Room Only, Room + Breakfast)
 *
 * Every module is a native Divi module so the client can click any image,
 * title, amenity list or button in Divi Builder and edit it.
 */
function hc_divi_rooms_archive_layout() {

    $q = new WP_Query( array(
        'post_type'      => 'room',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    if ( ! $q->have_posts() ) {
        return hc_divi_section_open()
            . '[et_pb_row _builder_version="4.20.0"][et_pb_column type="4_4" _builder_version="4.20.0"]'
            . '[et_pb_text _builder_version="4.20.0"]<p>No rooms yet.</p>[/et_pb_text]'
            . '[/et_pb_column][/et_pb_row][/et_pb_section]';
    }

    $rooms = $q->posts;
    wp_reset_postdata();

    // -- Header row: "Room Type" label + dropdown + "Amenities" + "Options" labels --
    $header_row = '[et_pb_row column_structure="1_4,2_4,1_4" _builder_version="4.20.0" custom_padding="20px||20px||true|false" background_color="#f7f7f7"]'
        . '[et_pb_column type="1_4" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0"]'
        . '<div style="display:flex;align-items:center;gap:8px;font-weight:600;">'
        . '<label for="hc-room-type-select" style="margin:0;">Room Type:</label>'
        . '<select id="hc-room-type-select" style="padding:6px 10px;border:1px solid #ddd;flex:1;">'
        . '<option value="" disabled selected>' . count( $rooms ) . ' Room Types</option>';
    foreach ( $rooms as $room ) {
        $header_row .= '<option value="room-' . esc_attr( $room->post_name ) . '">' . esc_html( $room->post_title ) . '</option>';
    }
    $header_row .= '</select></div>'
        . '<script>document.addEventListener("change",function(e){if(e.target&&e.target.id==="hc-room-type-select"){var el=document.getElementById(e.target.value);if(el)el.scrollIntoView({behavior:"smooth",block:"start"});}});</script>'
        . '[/et_pb_text][/et_pb_column]'
        . '[et_pb_column type="2_4" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0"]<p style="font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#14141e;margin:0;">Amenities</p>[/et_pb_text]'
        . '[/et_pb_column]'
        . '[et_pb_column type="1_4" _builder_version="4.20.0"]'
        . '[et_pb_text _builder_version="4.20.0"]<p style="font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#14141e;margin:0;">Options</p>[/et_pb_text]'
        . '[/et_pb_column][/et_pb_row]';

    // -- Per-room rows --
    $room_rows = '';
    foreach ( $rooms as $room ) {

        $room_url  = get_permalink( $room->ID );
        $title     = get_the_title( $room->ID );
        $thumb_id  = get_post_thumbnail_id( $room->ID );
        $img_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
        $amenities = hc_get( 'amenities',       $room->ID );
        $options   = hc_get( 'booking_options', $room->ID );

        // Column 1: image + title + Know More
        $col_image = $img_url
            ? '[et_pb_image src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $title ) . '" url="' . esc_url( $room_url ) . '" _builder_version="4.20.0" custom_margin="0||10px||false|false"][/et_pb_image]'
            : '';
        $col1 = '[et_pb_column type="1_4" _builder_version="4.20.0"]'
              . $col_image
              . '[et_pb_text _builder_version="4.20.0" text_text_align="center"]'
              . '<h4 style="color:#D81418;font-size:18px;margin:0 0 6px;">' . esc_html( $title ) . '</h4>'
              . '<p style="margin:0;"><a href="' . esc_url( $room_url ) . '" style="color:#D81418;font-weight:600;font-size:13px;text-decoration:none;">KNOW MORE &rarr;</a></p>'
              . '[/et_pb_text][/et_pb_column]';

        // Column 2: amenities (2-column grid)
        $amenities_html = '';
        if ( is_array( $amenities ) && $amenities ) {
            $amenities_html = '<ul style="list-style:none;padding:0;margin:0;display:grid;grid-template-columns:1fr 1fr;gap:4px 16px;font-size:14px;">';
            foreach ( $amenities as $a ) {
                $amenities_html .= '<li style="color:#555;padding-left:20px;position:relative;line-height:1.7;">'
                                . '<span style="color:#D81418;font-weight:700;position:absolute;left:0;">&#10003;</span>'
                                . esc_html( $a['label'] ?? '' )
                                . '</li>';
            }
            $amenities_html .= '</ul>';
        }
        $col2 = '[et_pb_column type="2_4" _builder_version="4.20.0"]'
              . '[et_pb_text _builder_version="4.20.0"]'
              . $amenities_html
              . '[/et_pb_text][/et_pb_column]';

        // Column 3: booking options as native buttons
        $buttons = '';
        if ( is_array( $options ) && $options ) {
            foreach ( $options as $opt ) {
                $buttons .= '[et_pb_text _builder_version="4.20.0" custom_margin="0||4px||false|false"]'
                    . '<p style="margin:0;font-size:14px;color:#555;">' . esc_html( $opt['label'] ?? '' ) . '</p>'
                    . '[/et_pb_text]'
                    . '[et_pb_button'
                    . ' button_text="Book Now"'
                    . ' button_url="' . esc_url( $opt['url'] ?? '#' ) . '"'
                    . ' url_new_window="on"'
                    . ' button_alignment="left"'
                    . ' _builder_version="4.20.0"'
                    . ' custom_button="on"'
                    . ' button_text_size="13px"'
                    . ' button_text_color="#ffffff"'
                    . ' button_bg_color="#D81418"'
                    . ' button_border_width="2px"'
                    . ' button_border_color="#D81418"'
                    . ' button_letter_spacing="1px"'
                    . ' button_font="Poppins|600|on||||||"'
                    . ' custom_margin="0||14px||false|false"'
                    . '][/et_pb_button]';
            }
        }
        $col3 = '[et_pb_column type="1_4" _builder_version="4.20.0"]'
              . $buttons
              . '[/et_pb_column]';

        $room_rows .= '[et_pb_row column_structure="1_4,2_4,1_4" _builder_version="4.20.0" custom_padding="30px||30px||true|false" custom_css_main_element="border-top:1px solid #eee;||scroll-margin-top:100px;" module_id="room-' . esc_attr( $room->post_name ) . '"]'
            . $col1 . $col2 . $col3
            . '[/et_pb_row]';
    }

    return hc_divi_section_open( '', '40px||80px||true|false' )
        . $header_row
        . $room_rows
        . '[/et_pb_section]';
}

/**
 * Single-room layout — used by the child theme's single-room.php template
 * to render the inner room page (e.g. /room/executive-room/).
 *
 * Layout: page title hero + 2 columns:
 *   - 8/12  image gallery (carousel) + title + description + amenities table
 *   - 4/12  sidebar with Quick Inquiry form + Book Now buttons
 *
 * Returns shortcode string that the template runs through do_shortcode.
 */
function hc_divi_single_room_page( $room_id ) {

    $title     = get_the_title( $room_id );
    $content   = apply_filters( 'the_content', get_post_field( 'post_content', $room_id ) );
    $bed       = hc_get( 'bed_type',        $room_id );
    $size      = hc_get( 'room_size',       $room_id );
    $amenities = hc_get( 'amenities',       $room_id );
    $gallery   = hc_get( 'gallery',         $room_id );
    $options   = hc_get( 'booking_options', $room_id );
    $thumb_id  = get_post_thumbnail_id( $room_id );

    // Gallery — build a Divi gallery module from the room's attachment IDs
    $gallery_ids = array();
    if ( is_array( $gallery ) ) $gallery_ids = array_map( 'intval', $gallery );
    if ( ! $gallery_ids && $thumb_id ) $gallery_ids = array( intval( $thumb_id ) );

    $gallery_module = $gallery_ids
        ? '[et_pb_gallery gallery_ids="' . esc_attr( implode( ',', $gallery_ids ) ) . '" fullwidth="on" show_title_and_caption="off" show_pagination="off" _builder_version="4.20.0" auto="on" auto_speed="5000"][/et_pb_gallery]'
        : '';

    // Title + meta
    $meta_line = '';
    if ( $bed || $size ) {
        $meta_line = '<p style="color:#888;font-size:14px;margin:8px 0 0;">'
                   . esc_html( trim( $bed . ( $bed && $size ? ' · ' : '' ) . $size ) )
                   . '</p>';
    }
    $title_text = '[et_pb_text _builder_version="4.20.0" header_2_font="Poppins|600|||||||" header_2_text_color="#14141e" header_2_font_size="32px" custom_margin="20px||10px||false|false"]'
        . '<h2>' . esc_html( $title ) . '</h2>'
        . $meta_line
        . '[/et_pb_text]';

    // Description (from post_content)
    $desc_text = $content
        ? '[et_pb_text _builder_version="4.20.0" text_font="Poppins||||||||" text_text_color="#555555" text_font_size="16px" text_line_height="1.8em" custom_margin="20px||30px||false|false"]' . $content . '[/et_pb_text]'
        : '';

    // Amenities — full list with original 2-column "AMENITIES AND SERVICES" heading
    $amenities_block = '';
    if ( is_array( $amenities ) && $amenities ) {
        $list_html = '<ul style="list-style:none;padding:0;margin:0;display:grid;grid-template-columns:1fr 1fr;gap:8px 24px;font-size:15px;">';
        foreach ( $amenities as $a ) {
            $icon = ! empty( $a['icon'] ) ? '<i class="' . esc_attr( $a['icon'] ) . '" style="margin-right:8px;color:#D81418;"></i>' : '<span style="color:#D81418;font-weight:700;margin-right:8px;">&#10003;</span>';
            $list_html .= '<li style="color:#555;padding:6px 0;line-height:1.6;">' . $icon . esc_html( $a['label'] ?? '' ) . '</li>';
        }
        $list_html .= '</ul>';

        $amenities_block = '[et_pb_text _builder_version="4.20.0" header_3_font="Poppins|600|||||||" header_3_text_color="#14141e" header_3_font_size="22px"]'
            . '<h3 style="text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid #D81418;display:inline-block;padding-bottom:6px;margin-bottom:20px;">Amenities and Services</h3>'
            . $list_html
            . '[/et_pb_text]';
    }

    // Sidebar: booking buttons + Quick Inquiry form
    $booking_buttons = '';
    if ( is_array( $options ) && $options ) {
        $booking_buttons = '[et_pb_text _builder_version="4.20.0" header_3_font="Poppins|600|||||||" header_3_text_color="#14141e" header_3_font_size="20px"]'
            . '<h3>Book This Room</h3>'
            . '[/et_pb_text]';
        foreach ( $options as $opt ) {
            $booking_buttons .= '[et_pb_text _builder_version="4.20.0" custom_margin="14px||4px||false|false"]'
                . '<p style="margin:0;font-size:14px;color:#555;">' . esc_html( $opt['label'] ?? '' ) . '</p>'
                . '[/et_pb_text]'
                . '[et_pb_button'
                . ' button_text="Book Now"'
                . ' button_url="' . esc_url( $opt['url'] ?? '#' ) . '"'
                . ' url_new_window="on"'
                . ' button_alignment="left"'
                . ' _builder_version="4.20.0"'
                . ' custom_button="on"'
                . ' button_text_size="13px"'
                . ' button_text_color="#ffffff"'
                . ' button_bg_color="#D81418"'
                . ' button_border_width="2px"'
                . ' button_border_color="#D81418"'
                . ' button_letter_spacing="1px"'
                . ' button_font="Poppins|600|on||||||"'
                . ' custom_margin="0||10px||false|false"'
                . '][/et_pb_button]';
        }
    }

    $sidebar = '[et_pb_text _builder_version="4.20.0" background_color="#f8f8f8" custom_padding="24px|24px|24px|24px|false|false"]'
        . '<div style="margin-bottom:10px;">' . do_shortcode( '[hc_inquiry_form variant="booking" title="Quick Inquiry"]' ) . '</div>'
        . '[/et_pb_text]'
        . $booking_buttons;

    // Page title hero
    $hero = hc_divi_page_title( $title, array( array( 'label' => 'Rooms', 'url' => home_url( '/rooms/' ) ) ) );

    return $hero
        . hc_divi_section_open()
        . '[et_pb_row column_structure="2_3,1_3" _builder_version="4.20.0"]'
        . '[et_pb_column type="2_3" _builder_version="4.20.0"]'
        . $gallery_module
        . $title_text
        . $desc_text
        . $amenities_block
        . '[/et_pb_column]'
        . '[et_pb_column type="1_3" _builder_version="4.20.0"]'
        . $sidebar
        . '[/et_pb_column][/et_pb_row][/et_pb_section]';
}
