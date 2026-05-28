<?php
/**
 * Native WP storage layer — replaces the ACF Pro dependency.
 *
 * All "site content" lives in wp_options under keys prefixed with hc_*.
 * All "room fields" live in wp_postmeta under keys prefixed with _hc_*.
 *
 * Public API:
 *   hc_get( $key, $post_id = 0 )       — read a value
 *   hc_set( $key, $value, $post_id = 0 ) — write a value
 *   hc_image_url( $id, $size = 'full' ) — attachment ID → URL
 *   hc_image( $id, $size = 'large' )   — full attachment array (for back-compat with old ACF shape)
 *
 * When $post_id is 0, the key is treated as a site-wide option.
 * When $post_id is a positive int, the key is treated as post meta.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function hc_get( $key, $post_id = 0, $default = null ) {
    if ( $post_id ) {
        $val = get_post_meta( intval( $post_id ), '_hc_' . $key, true );
    } else {
        $val = get_option( 'hc_' . $key, null );
    }
    if ( null === $val || '' === $val ) return $default;
    return $val;
}

function hc_set( $key, $value, $post_id = 0 ) {
    if ( $post_id ) {
        return update_post_meta( intval( $post_id ), '_hc_' . $key, $value );
    }
    return update_option( 'hc_' . $key, $value, true );
}

function hc_delete( $key, $post_id = 0 ) {
    if ( $post_id ) {
        return delete_post_meta( intval( $post_id ), '_hc_' . $key );
    }
    return delete_option( 'hc_' . $key );
}

/**
 * Resolve an attachment ID to a URL. Accepts either an int ID
 * or a legacy ACF-style array (in which case we return $arr['url']).
 *
 * @param int|array|string $id Attachment ID, ACF image array, or URL string.
 */
function hc_image_url( $id, $size = 'full' ) {
    if ( empty( $id ) ) return '';
    if ( is_array( $id ) ) return $id['url'] ?? '';
    if ( is_string( $id ) && false !== strpos( $id, '://' ) ) return $id;
    return wp_get_attachment_image_url( intval( $id ), $size );
}

/**
 * Get an attachment's alt text.
 */
function hc_image_alt( $id, $fallback = '' ) {
    if ( empty( $id ) ) return $fallback;
    if ( is_array( $id ) ) return $id['alt'] ?? $fallback;
    $alt = get_post_meta( intval( $id ), '_wp_attachment_image_alt', true );
    return $alt ?: $fallback;
}
