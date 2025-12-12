<?php
/**
 * Uninstall handler for ContentShield AI.
 *
 * This file runs when the plugin is deleted via WordPress admin.
 * It cleans up all plugin data including database tables and options.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Exit if not called by WordPress during uninstall
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Clean up plugin data on uninstall.
 */
function contentshield_uninstall() {
    global $wpdb;

    // Only clean up if the option is set (or always clean for complete removal)
    // Uncomment the following lines if you want to preserve data by default:
    // $preserve_data = get_option( 'contentshield_preserve_data', false );
    // if ( $preserve_data ) {
    //     return;
    // }

    // Drop custom database tables
    $tables = array(
        $wpdb->prefix . 'contentshield_fingerprints',
        $wpdb->prefix . 'contentshield_scans',
        $wpdb->prefix . 'contentshield_alerts',
        $wpdb->prefix . 'contentshield_logs',
    );

    foreach ( $tables as $table ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }

    // Delete plugin options
    $options = array(
        'contentshield_version',
        'contentshield_db_version',
        'contentshield_enabled',
        'contentshield_auto_protect',
        'contentshield_post_types',
        'contentshield_watermark_enabled',
        'contentshield_watermark_position',
        'contentshield_fingerprint_enabled',
        'contentshield_min_word_count',
        'contentshield_copy_protection',
        'contentshield_right_click_disable',
        'contentshield_text_selection',
        'contentshield_rss_protection',
        'contentshield_rss_attribution',
        'contentshield_scan_limit',
        'contentshield_scan_timeout',
        'contentshield_email_notifications',
        'contentshield_notification_email',
        'contentshield_license',
    );

    foreach ( $options as $option ) {
        delete_option( $option );
    }

    // Delete post meta
    $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_contentshield_%'" );

    // Delete user meta
    $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'contentshield_%'" );

    // Delete transients
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_contentshield_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_contentshield_%'" );

    // Remove capabilities
    $roles = array( 'administrator', 'editor' );

    foreach ( $roles as $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->remove_cap( 'manage_contentshield' );
            $role->remove_cap( 'view_contentshield_reports' );
            $role->remove_cap( 'manage_contentshield_settings' );
        }
    }

    // Clear scheduled events
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

    // Flush rewrite rules
    flush_rewrite_rules();
}

// Run uninstall
contentshield_uninstall();
