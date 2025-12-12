<?php
/**
 * Plugin activation handler.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Activator
 *
 * Handles plugin activation tasks including database table creation.
 */
class ContentShield_Activator {

    /**
     * Database version for migrations.
     *
     * @var string
     */
    const DB_VERSION = '1.0.0';

    /**
     * Run activation tasks.
     *
     * @return void
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        self::create_capabilities();
        self::schedule_events();

        // Store plugin version
        update_option( 'contentshield_version', CONTENTSHIELD_VERSION );
        update_option( 'contentshield_db_version', self::DB_VERSION );

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create custom database tables.
     *
     * @return void
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Fingerprints table
        $table_fingerprints = $wpdb->prefix . 'contentshield_fingerprints';
        $sql_fingerprints = "CREATE TABLE {$table_fingerprints} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            fingerprint VARCHAR(128) NOT NULL,
            content_hash VARCHAR(64) NOT NULL,
            word_count INT(11) DEFAULT 0,
            watermark_id VARCHAR(64) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY post_id (post_id),
            KEY fingerprint (fingerprint(32)),
            KEY content_hash (content_hash(32))
        ) {$charset_collate};";

        // Scans table
        $table_scans = $wpdb->prefix . 'contentshield_scans';
        $sql_scans = "CREATE TABLE {$table_scans} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            scanned_url VARCHAR(2048) NOT NULL,
            similarity_score DECIMAL(5,2) DEFAULT NULL,
            matched_content TEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            scan_type VARCHAR(20) DEFAULT 'manual',
            error_message TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY status (status),
            KEY scan_type (scan_type),
            KEY created_at (created_at)
        ) {$charset_collate};";

        // Alerts table
        $table_alerts = $wpdb->prefix . 'contentshield_alerts';
        $sql_alerts = "CREATE TABLE {$table_alerts} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            scan_id BIGINT(20) UNSIGNED DEFAULT NULL,
            alert_type VARCHAR(50) NOT NULL,
            severity VARCHAR(20) DEFAULT 'medium',
            source_url VARCHAR(2048) DEFAULT NULL,
            details LONGTEXT DEFAULT NULL,
            is_read TINYINT(1) DEFAULT 0,
            is_resolved TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            resolved_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY scan_id (scan_id),
            KEY alert_type (alert_type),
            KEY severity (severity),
            KEY is_read (is_read),
            KEY is_resolved (is_resolved),
            KEY created_at (created_at)
        ) {$charset_collate};";

        // Protection logs table
        $table_logs = $wpdb->prefix . 'contentshield_logs';
        $sql_logs = "CREATE TABLE {$table_logs} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            event_type VARCHAR(50) NOT NULL,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            details LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY post_id (post_id),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_fingerprints );
        dbDelta( $sql_scans );
        dbDelta( $sql_alerts );
        dbDelta( $sql_logs );
    }

    /**
     * Set default plugin options.
     *
     * @return void
     */
    private static function set_default_options() {
        $defaults = array(
            // General settings
            'contentshield_enabled'              => true,
            'contentshield_auto_protect'         => true,
            'contentshield_post_types'           => array( 'post', 'page' ),

            // Watermark settings
            'contentshield_watermark_enabled'    => true,
            'contentshield_watermark_position'   => 'distributed',

            // Fingerprint settings
            'contentshield_fingerprint_enabled'  => true,
            'contentshield_min_word_count'       => 100,

            // Copy protection settings
            'contentshield_copy_protection'      => false,
            'contentshield_right_click_disable'  => false,
            'contentshield_text_selection'       => true,

            // RSS protection settings
            'contentshield_rss_protection'       => true,
            'contentshield_rss_attribution'      => true,

            // Scanner settings
            'contentshield_scan_limit'           => 10,
            'contentshield_scan_timeout'         => 30,

            // Notification settings
            'contentshield_email_notifications'  => true,
            'contentshield_notification_email'   => get_option( 'admin_email' ),

            // License
            'contentshield_license'              => array(),
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }
    }

    /**
     * Create custom capabilities for the plugin.
     *
     * @return void
     */
    private static function create_capabilities() {
        $admin_role = get_role( 'administrator' );

        if ( $admin_role ) {
            $admin_role->add_cap( 'manage_contentshield' );
            $admin_role->add_cap( 'view_contentshield_reports' );
            $admin_role->add_cap( 'manage_contentshield_settings' );
        }

        $editor_role = get_role( 'editor' );

        if ( $editor_role ) {
            $editor_role->add_cap( 'view_contentshield_reports' );
        }
    }

    /**
     * Schedule cron events.
     *
     * @return void
     */
    private static function schedule_events() {
        // Schedule daily cleanup of old logs
        if ( ! wp_next_scheduled( 'contentshield_daily_cleanup' ) ) {
            wp_schedule_event( time(), 'daily', 'contentshield_daily_cleanup' );
        }

        // Schedule fingerprint check (free version - manual reminder)
        if ( ! wp_next_scheduled( 'contentshield_fingerprint_check' ) ) {
            wp_schedule_event( time(), 'daily', 'contentshield_fingerprint_check' );
        }
    }
}
