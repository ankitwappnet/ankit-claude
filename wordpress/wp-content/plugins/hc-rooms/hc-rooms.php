<?php
/**
 * Plugin Name: HC Rooms
 * Description: Custom post type "Room" for Hotel Cosmopolitan, with site-content seeding and shortcodes used by the Divi child theme. No ACF dependency.
 * Version: 2.0.0
 * Author: Wappnet Systems
 * Text Domain: hc-rooms
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HC_ROOMS_VERSION', '2.0.0' );
define( 'HC_ROOMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'HC_ROOMS_URL', plugin_dir_url( __FILE__ ) );

// Storage helpers FIRST — all other files depend on hc_get / hc_set / hc_image_url.
require_once HC_ROOMS_DIR . 'includes/storage.php';
require_once HC_ROOMS_DIR . 'includes/helpers.php';
require_once HC_ROOMS_DIR . 'includes/cpt.php';
require_once HC_ROOMS_DIR . 'includes/acf-fields.php'; // intentionally empty (no ACF needed)
require_once HC_ROOMS_DIR . 'includes/meta-box.php';
require_once HC_ROOMS_DIR . 'includes/site-options.php';
require_once HC_ROOMS_DIR . 'includes/shortcodes.php';
require_once HC_ROOMS_DIR . 'includes/site-shortcodes.php';
require_once HC_ROOMS_DIR . 'includes/divi-builders.php';
require_once HC_ROOMS_DIR . 'includes/seed-data.php';
require_once HC_ROOMS_DIR . 'includes/page-seeder.php';
require_once HC_ROOMS_DIR . 'includes/wp-cli.php';

register_activation_hook( __FILE__, function () {
    if ( function_exists( 'hc_rooms_register_cpt' ) ) {
        hc_rooms_register_cpt();
    }
    // Wipe seed flags so the new v2 seeders run on next admin load.
    delete_option( 'hc_rooms_seeded' );
    delete_option( 'hc_site_content_seeded' );
    delete_option( 'hc_pages_seeded' );
    delete_option( 'hc_pages_seeded_v2' );
    delete_option( 'hc_pages_seeded_v3' );
    delete_option( 'hc_seo_pages_seeded_v3' );
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
