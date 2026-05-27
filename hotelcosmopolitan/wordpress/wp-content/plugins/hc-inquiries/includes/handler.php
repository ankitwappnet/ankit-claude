<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Where inquiry emails go. Filterable so the admin / agency can change this without code edits.
 */
function hc_inq_get_recipient() {
    $default = get_option( 'hc_inq_recipient', 'reserve@hotelcosmopolitan.in' );
    return apply_filters( 'hc_inquiry_recipient', $default );
}

add_action( 'wp_ajax_nopriv_hc_submit_inquiry', 'hc_inq_handle_submit' );
add_action( 'wp_ajax_hc_submit_inquiry',        'hc_inq_handle_submit' );

function hc_inq_handle_submit() {

    // --- 1. Validate nonce -------------------------------------------------
    if ( empty( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'hc_form_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page.' ), 403 );
    }

    // --- 2. Lightweight rate-limit: 1 submission per IP per 30s ------------
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
    $key = 'hc_inq_throttle_' . md5( $ip );
    if ( $ip && get_transient( $key ) ) {
        wp_send_json_error( array( 'message' => 'Please wait a moment before submitting again.' ), 429 );
    }

    // --- 3. Sanitize inputs ------------------------------------------------
    $data = array(
        'name'        => isset( $_POST['name'] )      ? sanitize_text_field( wp_unslash( $_POST['name'] ) )      : '',
        'email'       => isset( $_POST['email'] )     ? sanitize_email( wp_unslash( $_POST['email'] ) )           : '',
        'phone'       => isset( $_POST['phone'] )     ? sanitize_text_field( wp_unslash( $_POST['phone'] ) )      : '',
        'check_in'    => isset( $_POST['check_in'] )  ? sanitize_text_field( wp_unslash( $_POST['check_in'] ) )   : null,
        'check_out'   => isset( $_POST['check_out'] ) ? sanitize_text_field( wp_unslash( $_POST['check_out'] ) )  : null,
        'adults'      => isset( $_POST['adults'] )    ? max( 0, intval( $_POST['adults'] ) )                       : 1,
        'children'    => isset( $_POST['children'] )  ? max( 0, intval( $_POST['children'] ) )                     : 0,
        'room_type'   => isset( $_POST['room_type'] ) ? sanitize_text_field( wp_unslash( $_POST['room_type'] ) )  : '',
        'message'     => isset( $_POST['message'] )   ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ): '',
        'source_page' => isset( $_POST['source_page'] ) ? esc_url_raw( wp_unslash( $_POST['source_page'] ) )       : '',
        'ip'          => $ip,
        'status'      => 'new',
        'created_at'  => current_time( 'mysql' ),
    );

    if ( empty( $data['name'] ) || empty( $data['email'] ) || empty( $data['phone'] ) ) {
        wp_send_json_error( array( 'message' => 'Please fill in your name, email and phone.' ), 400 );
    }
    if ( ! is_email( $data['email'] ) ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ), 400 );
    }

    // Normalize blank dates to NULL so MySQL doesn't choke
    if ( empty( $data['check_in'] ) )  $data['check_in']  = null;
    if ( empty( $data['check_out'] ) ) $data['check_out'] = null;

    // --- 4. Insert into DB -------------------------------------------------
    global $wpdb;
    $table = $wpdb->prefix . HC_INQ_TABLE;
    $inserted = $wpdb->insert( $table, $data );

    if ( false === $inserted ) {
        wp_send_json_error( array( 'message' => 'Could not save your submission. Please try again or call us directly.' ), 500 );
    }

    set_transient( $key, 1, 30 );

    // --- 5. Email the hotel ------------------------------------------------
    $to      = hc_inq_get_recipient();
    $subject = sprintf( '[%s] New %s from %s',
        wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
        ( ! empty( $_POST['variant'] ) && 'booking' === $_POST['variant'] ) ? 'booking request' : 'inquiry',
        $data['name']
    );

    $lines = array(
        'Name:       ' . $data['name'],
        'Email:      ' . $data['email'],
        'Phone:      ' . $data['phone'],
        'Room type:  ' . ( $data['room_type'] ?: '—' ),
        'Check in:   ' . ( $data['check_in']  ?: '—' ),
        'Check out:  ' . ( $data['check_out'] ?: '—' ),
        'Adults:     ' . $data['adults'],
        'Children:   ' . $data['children'],
        '',
        'Message:',
        $data['message'] ?: '—',
        '',
        '-- Submitted from: ' . ( $data['source_page'] ?: '(unknown)' ),
        'IP: ' . ( $data['ip'] ?: '—' ),
    );
    $body = implode( "\n", $lines );

    $headers = array(
        'From: ' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' <' . get_option( 'admin_email' ) . '>',
        'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>',
    );

    wp_mail( $to, $subject, $body, $headers );

    do_action( 'hc_inquiry_received', $data, $wpdb->insert_id );

    wp_send_json_success( array(
        'message' => 'Thank you — we have received your request and will be in touch shortly.',
        'id'      => $wpdb->insert_id,
    ) );
}
