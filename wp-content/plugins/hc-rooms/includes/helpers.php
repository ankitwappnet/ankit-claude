<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Import a file from the active theme's assets folder into the WP Media Library.
 *
 * @param string $relative_path  e.g. "images/rooms/executive/executive-1.webp" (relative to child theme root)
 * @param int    $parent_post_id Optional post to attach to.
 * @param string $title          Optional title for the attachment.
 *
 * @return int|WP_Error Attachment ID on success, WP_Error on failure.
 */
function hc_import_attachment( $relative_path, $parent_post_id = 0, $title = '' ) {
    // 1. Resolve source path inside the active child theme
    $source = trailingslashit( get_stylesheet_directory() ) . 'assets/' . ltrim( $relative_path, '/' );
    if ( ! file_exists( $source ) ) {
        return new WP_Error( 'hc_source_missing', 'Source file not found: ' . $relative_path );
    }

    // 2. If we've already imported this file, return the existing attachment
    $hash = md5( $source );
    $existing = get_posts( array(
        'post_type'      => 'attachment',
        'meta_key'       => '_hc_import_hash',
        'meta_value'     => $hash,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ) );
    if ( $existing ) {
        return (int) $existing[0];
    }

    // 3. Copy into uploads
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uploads  = wp_upload_dir();
    $filename = wp_unique_filename( $uploads['path'], basename( $source ) );
    $new_path = trailingslashit( $uploads['path'] ) . $filename;

    if ( ! copy( $source, $new_path ) ) {
        return new WP_Error( 'hc_copy_failed', 'Could not copy ' . $source . ' to ' . $new_path );
    }

    $filetype = wp_check_filetype( $filename );
    $attachment = array(
        'guid'           => trailingslashit( $uploads['url'] ) . $filename,
        'post_mime_type' => $filetype['type'],
        'post_title'     => $title ?: preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment( $attachment, $new_path, $parent_post_id );
    if ( is_wp_error( $attach_id ) || ! $attach_id ) {
        return new WP_Error( 'hc_attach_failed', 'Failed to register attachment' );
    }

    $meta = wp_generate_attachment_metadata( $attach_id, $new_path );
    wp_update_attachment_metadata( $attach_id, $meta );

    update_post_meta( $attach_id, '_hc_import_hash', $hash );

    return $attach_id;
}

/**
 * Bulk-import a directory of images from theme assets.
 *
 * @param string $relative_dir e.g. "images/rooms/executive"
 * @return int[] Array of attachment IDs in filename order.
 */
function hc_import_directory( $relative_dir ) {
    $dir = trailingslashit( get_stylesheet_directory() ) . 'assets/' . ltrim( $relative_dir, '/' );
    if ( ! is_dir( $dir ) ) return array();

    $files = glob( $dir . '/*.{webp,jpg,jpeg,png}', GLOB_BRACE );
    if ( ! $files ) return array();
    sort( $files );

    $ids = array();
    foreach ( $files as $f ) {
        $rel = $relative_dir . '/' . basename( $f );
        $id  = hc_import_attachment( $rel );
        if ( ! is_wp_error( $id ) ) $ids[] = $id;
    }
    return $ids;
}
