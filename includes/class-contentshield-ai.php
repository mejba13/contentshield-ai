<?php
/**
 * Main plugin class.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_AI
 *
 * The main plugin class that orchestrates all functionality.
 */
class ContentShield_AI {

    /**
     * Plugin version.
     *
     * @var string
     */
    protected $version;

    /**
     * Admin instance.
     *
     * @var ContentShield_Admin
     */
    protected $admin;

    /**
     * Public instance.
     *
     * @var ContentShield_Public
     */
    protected $public;

    /**
     * Fingerprint instance.
     *
     * @var ContentShield_Fingerprint
     */
    protected $fingerprint;

    /**
     * Watermark instance.
     *
     * @var ContentShield_Watermark
     */
    protected $watermark;

    /**
     * Scanner instance.
     *
     * @var ContentShield_Scanner
     */
    protected $scanner;

    /**
     * License instance.
     *
     * @var ContentShield_License
     */
    protected $license;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->version = CONTENTSHIELD_VERSION;
        $this->load_dependencies();
    }

    /**
     * Load required dependencies.
     *
     * @return void
     */
    private function load_dependencies() {
        // Core classes
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/core/class-fingerprint.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/core/class-watermark.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/core/class-scanner.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/core/class-protection.php';

        // API classes
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/api/class-api-client.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/api/class-license.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/api/class-rest-endpoints.php';

        // Admin classes
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/class-admin.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/class-settings.php';

        // Public classes
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/public/class-public.php';
        require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/public/class-copy-protection.php';

        // Initialize instances
        $this->fingerprint = new ContentShield_Fingerprint();
        $this->watermark   = new ContentShield_Watermark();
        $this->scanner     = new ContentShield_Scanner();
        $this->license     = new ContentShield_License();
        $this->admin       = new ContentShield_Admin( $this );
        $this->public      = new ContentShield_Public( $this );
    }

    /**
     * Run the plugin.
     *
     * @return void
     */
    public function run() {
        // Register hooks
        $this->register_admin_hooks();
        $this->register_public_hooks();
        $this->register_content_hooks();
        $this->register_cron_hooks();
        $this->register_rest_api();
    }

    /**
     * Register admin hooks.
     *
     * @return void
     */
    private function register_admin_hooks() {
        // Admin menu and pages
        add_action( 'admin_menu', array( $this->admin, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );

        // Admin AJAX handlers
        add_action( 'wp_ajax_contentshield_scan_url', array( $this->scanner, 'ajax_scan_url' ) );
        add_action( 'wp_ajax_contentshield_generate_fingerprint', array( $this->fingerprint, 'ajax_generate_fingerprint' ) );
        add_action( 'wp_ajax_contentshield_activate_license', array( $this->license, 'ajax_activate' ) );
        add_action( 'wp_ajax_contentshield_deactivate_license', array( $this->license, 'ajax_deactivate' ) );
        add_action( 'wp_ajax_contentshield_dismiss_notice', array( $this->admin, 'ajax_dismiss_notice' ) );
        add_action( 'wp_ajax_contentshield_get_stats', array( $this->admin, 'ajax_get_stats' ) );
        add_action( 'wp_ajax_contentshield_mark_alert_read', array( $this->admin, 'ajax_mark_alert_read' ) );
        add_action( 'wp_ajax_contentshield_mark_all_alerts_read', array( $this->admin, 'ajax_mark_all_alerts_read' ) );
        add_action( 'wp_ajax_contentshield_resolve_alert', array( $this->admin, 'ajax_resolve_alert' ) );
        add_action( 'wp_ajax_contentshield_export_fingerprints', array( $this->admin, 'ajax_export_fingerprints' ) );

        // Post meta box
        add_action( 'add_meta_boxes', array( $this->admin, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this->admin, 'save_post_meta' ), 10, 2 );

        // Admin notices
        add_action( 'admin_notices', array( $this->admin, 'display_notices' ) );

        // Plugin action links
        add_filter( 'plugin_action_links_' . CONTENTSHIELD_PLUGIN_BASENAME, array( $this->admin, 'add_action_links' ) );
    }

    /**
     * Register public hooks.
     *
     * @return void
     */
    private function register_public_hooks() {
        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) );

        // Copy protection
        if ( get_option( 'contentshield_copy_protection', false ) ) {
            add_action( 'wp_head', array( $this->public, 'add_copy_protection_styles' ) );
            add_action( 'wp_footer', array( $this->public, 'add_copy_protection_scripts' ) );
        }
    }

    /**
     * Register content-related hooks.
     *
     * @return void
     */
    private function register_content_hooks() {
        // Auto-protect new content
        if ( get_option( 'contentshield_auto_protect', true ) ) {
            add_action( 'publish_post', array( $this, 'auto_protect_content' ), 10, 2 );
            add_action( 'publish_page', array( $this, 'auto_protect_content' ), 10, 2 );
        }

        // Apply watermark to content display
        if ( get_option( 'contentshield_watermark_enabled', true ) ) {
            add_filter( 'the_content', array( $this->watermark, 'apply_watermark' ), 999 );
        }

        // RSS feed protection
        if ( get_option( 'contentshield_rss_protection', true ) ) {
            add_filter( 'the_content_feed', array( $this->public, 'protect_feed_content' ) );
            add_filter( 'the_excerpt_rss', array( $this->public, 'protect_feed_excerpt' ) );
        }
    }

    /**
     * Register cron hooks.
     *
     * @return void
     */
    private function register_cron_hooks() {
        add_action( 'contentshield_daily_cleanup', array( $this, 'daily_cleanup' ) );
        add_action( 'contentshield_fingerprint_check', array( $this, 'check_fingerprints' ) );
    }

    /**
     * Register REST API endpoints.
     *
     * @return void
     */
    private function register_rest_api() {
        add_action( 'rest_api_init', array( new ContentShield_REST_Endpoints(), 'register_routes' ) );
    }

    /**
     * Auto-protect content when published.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return void
     */
    public function auto_protect_content( $post_id, $post ) {
        // Skip autosaves and revisions
        if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Check if post type is protected
        $protected_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );
        if ( ! in_array( $post->post_type, $protected_types, true ) ) {
            return;
        }

        // Check minimum word count
        $min_words = get_option( 'contentshield_min_word_count', 100 );
        $word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );

        if ( $word_count < $min_words ) {
            return;
        }

        // Generate fingerprint
        if ( get_option( 'contentshield_fingerprint_enabled', true ) ) {
            $this->fingerprint->generate( $post_id );
        }
    }

    /**
     * Daily cleanup task.
     *
     * @return void
     */
    public function daily_cleanup() {
        global $wpdb;

        // Clean up old logs (older than 30 days)
        $table_logs = $wpdb->prefix . 'contentshield_logs';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_logs} WHERE created_at < %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) )
            )
        );

        // Clean up old completed scans (older than 90 days)
        $table_scans = $wpdb->prefix . 'contentshield_scans';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_scans} WHERE status = 'completed' AND created_at < %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) )
            )
        );

        // Clean up resolved alerts (older than 60 days)
        $table_alerts = $wpdb->prefix . 'contentshield_alerts';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table_alerts} WHERE is_resolved = 1 AND resolved_at < %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-60 days' ) )
            )
        );
    }

    /**
     * Check fingerprints for changes.
     *
     * @return void
     */
    public function check_fingerprints() {
        $this->fingerprint->check_for_changes();
    }

    /**
     * Get fingerprint instance.
     *
     * @return ContentShield_Fingerprint
     */
    public function get_fingerprint() {
        return $this->fingerprint;
    }

    /**
     * Get watermark instance.
     *
     * @return ContentShield_Watermark
     */
    public function get_watermark() {
        return $this->watermark;
    }

    /**
     * Get scanner instance.
     *
     * @return ContentShield_Scanner
     */
    public function get_scanner() {
        return $this->scanner;
    }

    /**
     * Get license instance.
     *
     * @return ContentShield_License
     */
    public function get_license() {
        return $this->license;
    }

    /**
     * Get plugin version.
     *
     * @return string
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Check if Pro features are available.
     *
     * @return bool
     */
    public function is_pro() {
        return $this->license->is_valid();
    }

    /**
     * Get current plan.
     *
     * @return string
     */
    public function get_plan() {
        return $this->license->get_plan();
    }
}
