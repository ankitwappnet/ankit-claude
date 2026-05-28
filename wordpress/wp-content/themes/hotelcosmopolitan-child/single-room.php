<?php
/**
 * Template for single Room CPT entries (e.g. /room/executive-room/).
 *
 * Renders a 2-column layout that mirrors the original site's deluxe-room.php:
 *   - Page title hero ("Executive Room" + breadcrumb)
 *   - Main column (2/3): image carousel + title + description + amenities
 *   - Sidebar (1/3):     Quick Inquiry form + per-option Book Now buttons
 *
 * The body is built via hc_divi_single_room_page() in the hc-rooms plugin
 * so every section is a native Divi module the client can edit in Builder.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) :
    the_post();

    if ( function_exists( 'hc_divi_single_room_page' ) ) {
        echo do_shortcode( hc_divi_single_room_page( get_the_ID() ) );
    } else {
        // Fallback if the hc-rooms plugin is missing
        echo '<div class="container" style="max-width:900px;margin:60px auto;padding:0 20px;">';
        the_title( '<h1>', '</h1>' );
        the_content();
        echo '</div>';
    }

endwhile;

get_footer();
