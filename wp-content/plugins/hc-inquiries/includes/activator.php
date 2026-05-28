<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function hc_inq_activate() {
    global $wpdb;
    $table   = $wpdb->prefix . HC_INQ_TABLE;
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        name          VARCHAR(120) NOT NULL,
        email         VARCHAR(190) NOT NULL,
        phone         VARCHAR(40) NOT NULL,
        check_in      DATE NULL,
        check_out     DATE NULL,
        adults        SMALLINT UNSIGNED DEFAULT 1,
        children      SMALLINT UNSIGNED DEFAULT 0,
        room_type     VARCHAR(80) NULL,
        message       TEXT NULL,
        source_page   VARCHAR(190) NULL,
        ip            VARCHAR(45) NULL,
        status        VARCHAR(20) DEFAULT 'new',
        PRIMARY KEY  (id),
        KEY created_at (created_at),
        KEY email      (email)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    update_option( 'hc_inq_db_version', HC_INQ_DB_VERSION );
}
