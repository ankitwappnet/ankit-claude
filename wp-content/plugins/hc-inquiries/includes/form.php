<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [hc_inquiry_form] — front-end inquiry / room booking form.
 * Submits via admin-ajax; handler in includes/handler.php.
 *
 * Attributes:
 *   variant : "inquiry" (default) | "booking" — booking variant exposes check-in/out and room-type fields.
 *   title   : Optional heading shown above the form.
 */
add_shortcode( 'hc_inquiry_form', function ( $atts ) {
    $atts = shortcode_atts( array(
        'variant' => 'inquiry',
        'title'   => '',
    ), $atts );

    $is_booking = ( 'booking' === $atts['variant'] );

    $room_options = array();
    if ( $is_booking ) {
        $q = new WP_Query( array(
            'post_type'      => 'room',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ) );
        if ( $q->have_posts() ) {
            foreach ( $q->posts as $id ) {
                $room_options[ get_post_field( 'post_name', $id ) ] = get_the_title( $id );
            }
        }
    }

    ob_start(); ?>
    <form class="hc-form" novalidate>
        <?php if ( $atts['title'] ) : ?>
            <h3 style="margin-top:0;"><?php echo esc_html( $atts['title'] ); ?></h3>
        <?php endif; ?>

        <input type="hidden" name="variant" value="<?php echo esc_attr( $is_booking ? 'booking' : 'inquiry' ); ?>">
        <input type="hidden" name="source_page" value="<?php echo esc_url( ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>">

        <div class="hc-form-row">
            <input type="text"  name="name"  placeholder="Your name *" required>
            <input type="email" name="email" placeholder="Email *" required>
        </div>
        <div class="hc-form-row">
            <input type="tel"  name="phone" placeholder="Phone *" required>
            <?php if ( $is_booking && $room_options ) : ?>
                <select name="room_type">
                    <option value="">Select Room Type</option>
                    <?php foreach ( $room_options as $slug => $name ) : ?>
                        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <input type="text" name="room_type" placeholder="Room type (optional)">
            <?php endif; ?>
        </div>

        <?php if ( $is_booking ) : ?>
            <div class="hc-form-row">
                <input type="date" name="check_in"  placeholder="Check in">
                <input type="date" name="check_out" placeholder="Check out">
            </div>
            <div class="hc-form-row">
                <input type="number" name="adults"   min="1" max="20" placeholder="Adults"   value="1">
                <input type="number" name="children" min="0" max="20" placeholder="Children" value="0">
            </div>
        <?php endif; ?>

        <div class="hc-form-row hc-form-row--single">
            <textarea name="message" rows="4" placeholder="Message (optional)"></textarea>
        </div>

        <div class="hc-form-row hc-form-row--single">
            <button type="submit" class="hc-btn-primary"><?php echo $is_booking ? 'Submit Booking Request' : 'Send Inquiry'; ?></button>
        </div>

        <div class="hc-form-status" role="status" aria-live="polite"></div>
    </form>
    <?php
    return ob_get_clean();
} );
