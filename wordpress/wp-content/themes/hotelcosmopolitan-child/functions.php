<?php
/**
 * Hotel Cosmopolitan — Divi Child Theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HC_CHILD_VERSION', '1.0.0' );
define( 'HC_CHILD_DIR',  get_stylesheet_directory() );
define( 'HC_CHILD_URL',  get_stylesheet_directory_uri() );

/**
 * Enqueue parent + child stylesheets and custom JS.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'divi-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'Divi' )->get( 'Version' )
    );

    wp_enqueue_style(
        'hc-child-style',
        HC_CHILD_URL . '/style.css',
        array( 'divi-parent-style' ),
        HC_CHILD_VERSION
    );

    wp_enqueue_style(
        'hc-poppins',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_script(
        'hc-child-js',
        HC_CHILD_URL . '/assets/js/custom.js',
        array( 'jquery' ),
        HC_CHILD_VERSION,
        true
    );

    wp_localize_script( 'hc-child-js', 'hcSettings', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'hc_form_nonce' ),
    ) );
}, 20 );

/**
 * Register primary + footer menus (Divi exposes these in Customizer once registered).
 */
add_action( 'after_setup_theme', function () {
    register_nav_menus( array(
        'primary-menu' => __( 'Primary Menu', 'hotelcosmopolitan-child' ),
        'footer-menu'  => __( 'Footer Menu', 'hotelcosmopolitan-child' ),
        'mobile-menu'  => __( 'Mobile Menu', 'hotelcosmopolitan-child' ),
    ) );

    add_theme_support( 'post-thumbnails' );
    add_image_size( 'hc-room-card',   600, 400, true );
    add_image_size( 'hc-room-banner', 1600, 700, true );
    add_image_size( 'hc-blog-card',   600, 400, true );
} );

/**
 * Inject schema.org Hotel JSON-LD into <head>. Mirrors component/header.php from original.
 * Editable later via Customizer / ACF options page if you want; for now hard-coded to match
 * existing SEO footprint exactly.
 */
add_action( 'wp_head', function () {
    if ( ! is_front_page() ) return;
    ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Hotel",
    "name": "3 Star Hotels in Ahmedabad - Hotel Cosmopolitan",
    "image": "<?php echo esc_url( home_url( '/wp-content/uploads/logo.png' ) ); ?>",
    "url": "<?php echo esc_url( home_url( '/' ) ); ?>",
    "telephone": "[+91-9099914802,+91-9099914811]",
    "email": "reserve@hotelcosmopolitan.in",
    "tourBookingPage": "<?php echo esc_url( home_url( '/rooms/' ) ); ?>",
    "priceRange": "2999",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "Darshan Society Road, Near Stadium Circle, Navrangpura",
        "addressLocality": "Ahmedabad",
        "postalCode": "380009",
        "addressCountry": "IN"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 23.04021927003561,
        "longitude": 72.56129639150095
    },
    "sameAs": [
        "https://www.facebook.com/hotelcosmopolitanabad"
    ]
}
</script>
    <?php
}, 5 );

/**
 * Add "Book Now" CTA to primary menu, mirroring the original .btn-book in header.
 * Filterable so you can disable via child theme child if ever needed.
 */
add_filter( 'wp_nav_menu_items', function ( $items, $args ) {
    if ( ( isset( $args->theme_location ) && 'primary-menu' === $args->theme_location )
      || ( isset( $args->menu_class ) && false !== strpos( $args->menu_class, 'et-disable-mobile' ) ) ) {

        $url = apply_filters( 'hc_book_now_url', 'https://www.swiftbook.io/inst/#home?propertyId=2166' );
        $items .= sprintf(
            '<li class="menu-item hc-menu-book"><a class="hc-book-now-btn" href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
            esc_url( $url ),
            esc_html__( 'Book Now', 'hotelcosmopolitan-child' )
        );
    }
    return $items;
}, 10, 2 );

/**
 * Helper: render the standard page-title block. Use from Divi via shortcode [hc_page_title].
 */
/**
 * Inject the custom footer widget area into the Divi footer.
 * Mirrors component/footer.php from the original site (logo, rooms menu, quick links, contact).
 *
 * Disable via Customizer or by setting the `hc_render_custom_footer` filter to false.
 */
add_action( 'et_after_main_content', function () {
    if ( ! apply_filters( 'hc_render_custom_footer', true ) ) return;
    if ( ! shortcode_exists( 'hc_footer_widgets' ) ) return;
    echo '<div class="hc-custom-footer-widgets" style="background:#14141e;color:#ccc;"><div style="max-width:1200px;margin:0 auto;padding:0 20px;">'
        . do_shortcode( '[hc_footer_widgets]' )
        . '</div></div>';
}, 5 );

/**
 * Append legal links to the Divi copyright row.
 */
add_filter( 'et_get_safe_localization', function ( $text ) {
    if ( false !== strpos( $text, 'designed by' ) ) {
        $extras = ' | <a href="' . esc_url( home_url( '/terms-condition/' ) ) . '">Terms &amp; Conditions</a>'
                . ' | <a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">Privacy Policy</a>'
                . ' | <a href="' . esc_url( home_url( '/reservation-policy/' ) ) . '">Booking Policy</a>'
                . ' | <a href="' . esc_url( home_url( '/cancellation-policy/' ) ) . '">Cancellation Policy</a>';
        $text .= $extras;
    }
    return $text;
} );

add_shortcode( 'hc_page_title', function ( $atts ) {
    $atts = shortcode_atts( array(
        'title'      => get_the_title(),
        'background' => '',
    ), $atts );

    $style = '';
    if ( ! empty( $atts['background'] ) ) {
        $style = sprintf(
            'style="background: linear-gradient(rgba(20,20,30,.55), rgba(20,20,30,.55)), url(%s) center/cover no-repeat;"',
            esc_url( $atts['background'] )
        );
    }

    ob_start(); ?>
    <div class="hc-page-title" <?php echo $style; // phpcs:ignore ?>>
        <div class="et_pb_row">
            <h1><?php echo esc_html( $atts['title'] ); ?></h1>
            <ul class="hc-breadcrumbs">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li class="separator"></li>
                <li class="active"><?php echo esc_html( $atts['title'] ); ?></li>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
} );
