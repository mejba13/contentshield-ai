<?php
/**
 * Copy protection functionality.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Copy_Protection
 *
 * Provides copy-paste detection and protection features.
 */
class ContentShield_Copy_Protection {

    /**
     * Check if copy protection is enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return (bool) get_option( 'contentshield_copy_protection', false );
    }

    /**
     * Check if right-click is disabled.
     *
     * @return bool
     */
    public static function is_right_click_disabled() {
        return (bool) get_option( 'contentshield_right_click_disable', false );
    }

    /**
     * Check if text selection is allowed.
     *
     * @return bool
     */
    public static function is_text_selection_allowed() {
        return (bool) get_option( 'contentshield_text_selection', true );
    }

    /**
     * Get copy detection statistics.
     *
     * @param int $days Number of days to look back.
     * @return array Statistics array.
     */
    public static function get_statistics( $days = 30 ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'contentshield_logs';
        $since_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        // Total copy events
        $total_copies = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$logs_table}
             WHERE event_type = 'copy_detected'
             AND created_at >= %s",
            $since_date
        ) );

        // Unique IPs
        $unique_ips = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT ip_address) FROM {$logs_table}
             WHERE event_type = 'copy_detected'
             AND created_at >= %s",
            $since_date
        ) );

        // Most copied posts
        $most_copied = $wpdb->get_results( $wpdb->prepare(
            "SELECT post_id, COUNT(*) as copy_count
             FROM {$logs_table}
             WHERE event_type = 'copy_detected'
             AND created_at >= %s
             GROUP BY post_id
             ORDER BY copy_count DESC
             LIMIT 10",
            $since_date
        ) );

        // Daily breakdown
        $daily_breakdown = $wpdb->get_results( $wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count
             FROM {$logs_table}
             WHERE event_type = 'copy_detected'
             AND created_at >= %s
             GROUP BY DATE(created_at)
             ORDER BY date DESC",
            $since_date
        ) );

        return array(
            'total_copies'    => $total_copies,
            'unique_visitors' => $unique_ips,
            'most_copied'     => $most_copied,
            'daily_breakdown' => $daily_breakdown,
        );
    }

    /**
     * Get recent copy events.
     *
     * @param int $limit Number of events to retrieve.
     * @return array Array of copy events.
     */
    public static function get_recent_events( $limit = 20 ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'contentshield_logs';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT l.*, p.post_title
             FROM {$logs_table} l
             LEFT JOIN {$wpdb->posts} p ON l.post_id = p.ID
             WHERE l.event_type = 'copy_detected'
             ORDER BY l.created_at DESC
             LIMIT %d",
            $limit
        ) );
    }

    /**
     * Clear old copy logs.
     *
     * @param int $days Keep logs newer than this many days.
     * @return int Number of deleted rows.
     */
    public static function clear_old_logs( $days = 30 ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'contentshield_logs';
        $cutoff_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        return $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$logs_table}
             WHERE event_type = 'copy_detected'
             AND created_at < %s",
            $cutoff_date
        ) );
    }
}
