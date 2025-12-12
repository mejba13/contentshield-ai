<?php
/**
 * Plugin deactivation handler.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Deactivator
 *
 * Handles plugin deactivation tasks.
 */
class ContentShield_Deactivator {

    /**
     * Run deactivation tasks.
     *
     * @return void
     */
    public static function deactivate() {
        self::clear_scheduled_events();
        self::clear_transients();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Clear all scheduled cron events.
     *
     * @return void
     */
    private static function clear_scheduled_events() {
        $events = array(
            'contentshield_daily_cleanup',
            'contentshield_fingerprint_check',
            'contentshield_monitoring_scan',
        );

        foreach ( $events as $event ) {
            $timestamp = wp_next_scheduled( $event );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, $event );
            }
        }
    }

    /**
     * Clear plugin transients.
     *
     * @return void
     */
    private static function clear_transients() {
        global $wpdb;

        // Delete all plugin transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_contentshield_%',
                '_transient_timeout_contentshield_%'
            )
        );
    }
}
