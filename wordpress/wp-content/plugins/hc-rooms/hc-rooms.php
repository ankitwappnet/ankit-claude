<?php
/**
 * Plugin Name: HC Rooms
 * Description: Custom post type "Room" for Hotel Cosmopolitan, with ACF Pro field definitions and shortcodes used by the Divi child theme.
 * Version: 1.0.0
 * Author: Wappnet Systems
 * Text Domain: hc-rooms
 * Requires Plugins: advanced-custom-fields-pro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HC_ROOMS_VERSION', '1.0.0' );
define( 'HC_ROOMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'HC_ROOMS_URL', plugin_dir_url( __FILE__ ) );

require_once HC_ROOMS_DIR . 'includes/cpt.php';
require_once HC_ROOMS_DIR . 'includes/acf-fields.php';
require_once HC_ROOMS_DIR . 'includes/shortcodes.php';
require_once HC_ROOMS_DIR . 'includes/seed-data.php';

register_activation_hook( __FILE__, function () {
    hc_rooms_register_cpt();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
