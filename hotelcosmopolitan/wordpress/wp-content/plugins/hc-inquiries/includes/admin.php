<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin menu: list inquiries, view detail, mark status, delete.
 */
add_action( 'admin_menu', function () {
    add_menu_page(
        __( 'Inquiries', 'hc-inquiries' ),
        __( 'Inquiries', 'hc-inquiries' ),
        'manage_options',
        'hc-inquiries',
        'hc_inq_render_list_page',
        'dashicons-email-alt',
        25
    );

    add_submenu_page(
        'hc-inquiries',
        __( 'Settings', 'hc-inquiries' ),
        __( 'Settings', 'hc-inquiries' ),
        'manage_options',
        'hc-inquiries-settings',
        'hc_inq_render_settings_page'
    );
} );

function hc_inq_render_list_page() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die();

    global $wpdb;
    $table = $wpdb->prefix . HC_INQ_TABLE;

    // Handle actions
    if ( ! empty( $_GET['hc_action'] ) && ! empty( $_GET['id'] ) && check_admin_referer( 'hc_inq_action' ) ) {
        $id = intval( $_GET['id'] );
        if ( 'delete' === $_GET['hc_action'] ) {
            $wpdb->delete( $table, array( 'id' => $id ) );
        } elseif ( 'mark_done' === $_GET['hc_action'] ) {
            $wpdb->update( $table, array( 'status' => 'done' ), array( 'id' => $id ) );
        } elseif ( 'mark_new' === $_GET['hc_action'] ) {
            $wpdb->update( $table, array( 'status' => 'new' ), array( 'id' => $id ) );
        }
        echo '<div class="updated notice"><p>Done.</p></div>';
    }

    $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 200" );

    ?>
    <div class="wrap">
        <h1>Inquiries</h1>
        <p>Most recent submissions from contact / room-booking forms.</p>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! $rows ) : ?>
                    <tr><td colspan="8"><em>No inquiries yet.</em></td></tr>
                <?php else : foreach ( $rows as $r ) :
                    $nonce = wp_create_nonce( 'hc_inq_action' ); ?>
                    <tr>
                        <td><?php echo esc_html( $r->created_at ); ?></td>
                        <td><strong><?php echo esc_html( $r->name ); ?></strong>
                            <?php if ( $r->message ) : ?>
                                <br><small style="color:#666;"><?php echo esc_html( wp_trim_words( $r->message, 18 ) ); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><a href="mailto:<?php echo esc_attr( $r->email ); ?>"><?php echo esc_html( $r->email ); ?></a></td>
                        <td><?php echo esc_html( $r->phone ); ?></td>
                        <td><?php echo esc_html( $r->room_type ?: '—' ); ?></td>
                        <td>
                            <?php echo esc_html( $r->check_in  ?: '—' ); ?> →
                            <?php echo esc_html( $r->check_out ?: '—' ); ?>
                        </td>
                        <td><span class="hc-status hc-status-<?php echo esc_attr( $r->status ); ?>"><?php echo esc_html( $r->status ); ?></span></td>
                        <td>
                            <?php $base = admin_url( 'admin.php?page=hc-inquiries&id=' . $r->id . '&_wpnonce=' . $nonce ); ?>
                            <?php if ( 'new' === $r->status ) : ?>
                                <a href="<?php echo esc_url( $base . '&hc_action=mark_done' ); ?>">Mark done</a>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $base . '&hc_action=mark_new' ); ?>">Re-open</a>
                            <?php endif; ?>
                            &nbsp;|&nbsp;
                            <a href="<?php echo esc_url( $base . '&hc_action=delete' ); ?>" onclick="return confirm('Delete this inquiry?');" style="color:#a00;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <style>
        .hc-status { display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; text-transform:uppercase; }
        .hc-status-new { background:#fff3cd; color:#856404; }
        .hc-status-done { background:#d4edda; color:#155724; }
    </style>
    <?php
}

function hc_inq_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die();

    if ( isset( $_POST['hc_inq_recipient'] ) && check_admin_referer( 'hc_inq_settings' ) ) {
        update_option( 'hc_inq_recipient', sanitize_email( wp_unslash( $_POST['hc_inq_recipient'] ) ) );
        echo '<div class="updated notice"><p>Saved.</p></div>';
    }

    $recipient = get_option( 'hc_inq_recipient', 'reserve@hotelcosmopolitan.in' );
    ?>
    <div class="wrap">
        <h1>Inquiry Settings</h1>
        <form method="post">
            <?php wp_nonce_field( 'hc_inq_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="hc_inq_recipient">Notification Email</label></th>
                    <td>
                        <input type="email" id="hc_inq_recipient" name="hc_inq_recipient" value="<?php echo esc_attr( $recipient ); ?>" class="regular-text" required>
                        <p class="description">Form submissions will be emailed here.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Save' ); ?>
        </form>
    </div>
    <?php
}
