<?php
/**
 * Meta box for the `room` CPT — replaces the ACF Pro UI.
 *
 * Edits the same keys the seeder writes:
 *   - short_description (textarea)
 *   - bed_type          (text)
 *   - room_size         (text)
 *   - amenities         (newline-separated "icon|label" pairs)
 *   - gallery           (comma-separated attachment IDs)
 *   - booking_options   (newline-separated "label|url" pairs)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'hc_room_details',
        'Room Details',
        'hc_render_room_meta_box',
        'room',
        'normal',
        'high'
    );
} );

function hc_render_room_meta_box( $post ) {
    wp_nonce_field( 'hc_room_meta', 'hc_room_meta_nonce' );

    $short    = hc_get( 'short_description', $post->ID, '' );
    $bed      = hc_get( 'bed_type',          $post->ID, '' );
    $size     = hc_get( 'room_size',         $post->ID, '' );
    $amen     = hc_get( 'amenities',         $post->ID, array() );
    $gallery  = hc_get( 'gallery',           $post->ID, array() );
    $booking  = hc_get( 'booking_options',   $post->ID, array() );

    $amen_text = '';
    if ( is_array( $amen ) ) {
        foreach ( $amen as $a ) {
            $amen_text .= ( $a['icon'] ?? '' ) . '|' . ( $a['label'] ?? '' ) . "\n";
        }
    }

    $booking_text = '';
    if ( is_array( $booking ) ) {
        foreach ( $booking as $b ) {
            $booking_text .= ( $b['label'] ?? '' ) . '|' . ( $b['url'] ?? '' ) . "\n";
        }
    }

    $gallery_csv = is_array( $gallery ) ? implode( ',', array_map( 'intval', $gallery ) ) : '';

    ?>
    <style>
        .hc-mb-field { margin-bottom: 18px; }
        .hc-mb-field label { display:block; font-weight:600; margin-bottom:4px; }
        .hc-mb-field input[type=text], .hc-mb-field textarea { width:100%; }
        .hc-mb-help { font-size:12px; color:#888; margin-top:4px; }
        .hc-mb-gallery-preview { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
        .hc-mb-gallery-preview img { width:80px; height:60px; object-fit:cover; border:1px solid #ddd; }
    </style>

    <div class="hc-mb-field">
        <label for="hc_short_description">Short Description</label>
        <textarea id="hc_short_description" name="hc_short_description" rows="2"><?php echo esc_textarea( $short ); ?></textarea>
    </div>

    <div class="hc-mb-field" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <div>
            <label for="hc_bed_type">Bed Type</label>
            <input type="text" id="hc_bed_type" name="hc_bed_type" value="<?php echo esc_attr( $bed ); ?>" placeholder="King bed / Twin beds">
        </div>
        <div>
            <label for="hc_room_size">Room Size</label>
            <input type="text" id="hc_room_size" name="hc_room_size" value="<?php echo esc_attr( $size ); ?>" placeholder="285 sq ft">
        </div>
    </div>

    <div class="hc-mb-field">
        <label for="hc_amenities">Amenities</label>
        <textarea id="hc_amenities" name="hc_amenities" rows="10" placeholder="fa-light fa-wifi|Free Wi-Fi internet"><?php echo esc_textarea( $amen_text ); ?></textarea>
        <p class="hc-mb-help">One per line. Format: <code>icon-class|label</code>. Icon class is optional (leave the | and put just the label, e.g. <code>|King bed</code>).</p>
    </div>

    <div class="hc-mb-field">
        <label for="hc_gallery">Gallery (attachment IDs)</label>
        <input type="text" id="hc_gallery" name="hc_gallery" value="<?php echo esc_attr( $gallery_csv ); ?>" placeholder="123,124,125">
        <p class="hc-mb-help">Comma-separated WP media attachment IDs. Upload images to the Media Library, copy each "Attachment ID" (visible in URL when editing a media item: <code>?item=123</code>), paste them here.</p>
        <?php if ( is_array( $gallery ) && $gallery ) : ?>
            <div class="hc-mb-gallery-preview">
                <?php foreach ( $gallery as $id ) :
                    $url = hc_image_url( $id, 'thumbnail' );
                    if ( $url ) : ?>
                        <img src="<?php echo esc_url( $url ); ?>" title="ID: <?php echo intval( $id ); ?>">
                    <?php endif;
                endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="hc-mb-field">
        <label for="hc_booking_options">Booking Options</label>
        <textarea id="hc_booking_options" name="hc_booking_options" rows="4" placeholder="Room Only|https://..."><?php echo esc_textarea( $booking_text ); ?></textarea>
        <p class="hc-mb-help">One per line. Format: <code>label|booking-url</code>.</p>
    </div>
    <?php
}

add_action( 'save_post_room', function ( $post_id ) {
    if ( ! isset( $_POST['hc_room_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['hc_room_meta_nonce'], 'hc_room_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['hc_short_description'] ) ) {
        hc_set( 'short_description', sanitize_textarea_field( wp_unslash( $_POST['hc_short_description'] ) ), $post_id );
    }
    if ( isset( $_POST['hc_bed_type'] ) ) {
        hc_set( 'bed_type', sanitize_text_field( wp_unslash( $_POST['hc_bed_type'] ) ), $post_id );
    }
    if ( isset( $_POST['hc_room_size'] ) ) {
        hc_set( 'room_size', sanitize_text_field( wp_unslash( $_POST['hc_room_size'] ) ), $post_id );
    }

    if ( isset( $_POST['hc_amenities'] ) ) {
        $rows = array();
        foreach ( preg_split( '/\r?\n/', wp_unslash( $_POST['hc_amenities'] ) ) as $line ) {
            $line = trim( $line );
            if ( '' === $line ) continue;
            $parts = explode( '|', $line, 2 );
            $rows[] = array(
                'icon'  => trim( $parts[0] ?? '' ),
                'label' => sanitize_text_field( trim( $parts[1] ?? $parts[0] ?? '' ) ),
            );
        }
        hc_set( 'amenities', $rows, $post_id );
    }

    if ( isset( $_POST['hc_gallery'] ) ) {
        $ids = array_filter( array_map( 'intval', explode( ',', $_POST['hc_gallery'] ) ) );
        hc_set( 'gallery', $ids, $post_id );
    }

    if ( isset( $_POST['hc_booking_options'] ) ) {
        $rows = array();
        foreach ( preg_split( '/\r?\n/', wp_unslash( $_POST['hc_booking_options'] ) ) as $line ) {
            $line = trim( $line );
            if ( '' === $line ) continue;
            $parts = explode( '|', $line, 2 );
            $rows[] = array(
                'label' => sanitize_text_field( trim( $parts[0] ?? '' ) ),
                'url'   => esc_url_raw( trim( $parts[1] ?? '' ) ),
            );
        }
        hc_set( 'booking_options', $rows, $post_id );
    }
} );
