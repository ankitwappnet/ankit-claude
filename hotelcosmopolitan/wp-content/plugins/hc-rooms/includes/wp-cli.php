<?php
/**
 * WP-CLI commands for Hotel Cosmopolitan.
 *
 * Usage:
 *   wp hc install         — run all seeders + create pages + build menu (idempotent)
 *   wp hc reseed          — wipe seed flags and re-run all seeders
 *   wp hc status          — show seed state and counts
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;

class HC_CLI_Command {

    /**
     * Run all installers/seeders once.
     *
     * ## EXAMPLES
     *   wp hc install
     */
    public function install() {
        WP_CLI::log( '→ Triggering admin_init seeders...' );
        do_action( 'admin_init' );
        WP_CLI::log( '→ Flushing rewrite rules...' );
        flush_rewrite_rules();
        $this->status();
    }

    /**
     * Wipe seed flags and re-run all seeders. Does NOT delete existing rooms/pages.
     *
     * ## EXAMPLES
     *   wp hc reseed
     */
    public function reseed() {
        delete_option( 'hc_rooms_seeded' );
        delete_option( 'hc_site_content_seeded' );
        delete_option( 'hc_pages_seeded' );
        WP_CLI::log( '→ Flags cleared. Re-seeding...' );
        $this->install();
    }

    /**
     * Show current seed state and counts.
     *
     * ## EXAMPLES
     *   wp hc status
     */
    public function status() {
        $rooms_count = wp_count_posts( 'room' );
        $pages_count = wp_count_posts( 'page' );

        WP_CLI::log( '' );
        WP_CLI::log( '  Rooms seeded:        ' . ( get_option( 'hc_rooms_seeded' )        ? 'yes' : 'no' ) );
        WP_CLI::log( '  Site content seeded: ' . ( get_option( 'hc_site_content_seeded' ) ? 'yes' : 'no' ) );
        WP_CLI::log( '  Pages seeded:        ' . ( get_option( 'hc_pages_seeded' )        ? 'yes' : 'no' ) );
        WP_CLI::log( '' );
        WP_CLI::log( '  Rooms (published):   ' . intval( $rooms_count->publish ?? 0 ) );
        WP_CLI::log( '  Pages (published):   ' . intval( $pages_count->publish ?? 0 ) );
        WP_CLI::log( '' );
        WP_CLI::log( '  Front page:          ' . ( get_option( 'page_on_front' ) ? get_the_title( get_option( 'page_on_front' ) ) : '(none)' ) );
        WP_CLI::log( '  Permalink structure: ' . get_option( 'permalink_structure' ) );
    }
}

WP_CLI::add_command( 'hc', 'HC_CLI_Command' );
