<?php
/**
 * Plugin Name: HC Inquiries
 * Description: Inquiry / room-booking form handler for Hotel Cosmopolitan. Stores submissions in a custom DB table and emails them to reserve@hotelcosmopolitan.in.
 * Version: 1.0.0
 * Author: Wappnet Systems
 * Text Domain: hc-inquiries
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HC_INQ_VERSION', '1.0.0' );
define( 'HC_INQ_DIR', plugin_dir_path( __FILE__ ) );
define( 'HC_INQ_URL', plugin_dir_url( __FILE__ ) );
define( 'HC_INQ_DB_VERSION', '1' );
define( 'HC_INQ_TABLE',  'hc_inquiries' );

require_once HC_INQ_DIR . 'includes/activator.php';
require_once HC_INQ_DIR . 'includes/form.php';
require_once HC_INQ_DIR . 'includes/handler.php';
require_once HC_INQ_DIR . 'includes/admin.php';

register_activation_hook( __FILE__, 'hc_inq_activate' );
