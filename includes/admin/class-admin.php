<?php
/**
 * Admin functionality.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Admin
 *
 * Handles admin menu, pages, and functionality.
 */
class ContentShield_Admin {

    /**
     * Main plugin instance.
     *
     * @var ContentShield_AI
     */
    private $plugin;

    /**
     * Settings instance.
     *
     * @var ContentShield_Settings
     */
    private $settings;

    /**
     * Constructor.
     *
     * @param ContentShield_AI $plugin Main plugin instance.
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->settings = new ContentShield_Settings();
    }

    /**
     * Add admin menu.
     *
     * @return void
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __( 'ContentShield AI', 'contentshield-ai' ),
            __( 'ContentShield', 'contentshield-ai' ),
            'manage_contentshield',
            'contentshield',
            array( $this, 'render_dashboard_page' ),
            'dashicons-shield-alt',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'contentshield',
            __( 'Dashboard', 'contentshield-ai' ),
            __( 'Dashboard', 'contentshield-ai' ),
            'manage_contentshield',
            'contentshield',
            array( $this, 'render_dashboard_page' )
        );

        // Protected Content
        add_submenu_page(
            'contentshield',
            __( 'Protected Content', 'contentshield-ai' ),
            __( 'Protected Content', 'contentshield-ai' ),
            'view_contentshield_reports',
            'contentshield-protected',
            array( $this, 'render_protected_content_page' )
        );

        // Scanner
        add_submenu_page(
            'contentshield',
            __( 'Plagiarism Scanner', 'contentshield-ai' ),
            __( 'Scanner', 'contentshield-ai' ),
            'manage_contentshield',
            'contentshield-scanner',
            array( $this, 'render_scanner_page' )
        );

        // Alerts
        add_submenu_page(
            'contentshield',
            __( 'Alerts', 'contentshield-ai' ),
            __( 'Alerts', 'contentshield-ai' ),
            'view_contentshield_reports',
            'contentshield-alerts',
            array( $this, 'render_alerts_page' )
        );

        // Settings
        add_submenu_page(
            'contentshield',
            __( 'Settings', 'contentshield-ai' ),
            __( 'Settings', 'contentshield-ai' ),
            'manage_contentshield_settings',
            'contentshield-settings',
            array( $this, 'render_settings_page' )
        );

        // Pro Features
        if ( ! $this->plugin->is_pro() ) {
            add_submenu_page(
                'contentshield',
                __( 'Upgrade to Pro', 'contentshield-ai' ),
                '<span style="color:#f39c12;">' . __( 'Upgrade to Pro', 'contentshield-ai' ) . '</span>',
                'manage_contentshield',
                'contentshield-pro',
                array( $this, 'render_pro_page' )
            );
        }
    }

    /**
     * Register settings.
     *
     * @return void
     */
    public function register_settings() {
        $this->settings->register();
    }

    /**
     * Enqueue admin styles.
     *
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function enqueue_styles( $hook ) {
        // Only load on plugin pages
        if ( strpos( $hook, 'contentshield' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'contentshield-admin',
            CONTENTSHIELD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CONTENTSHIELD_VERSION
        );
    }

    /**
     * Enqueue admin scripts.
     *
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function enqueue_scripts( $hook ) {
        // Only load on plugin pages
        if ( strpos( $hook, 'contentshield' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'contentshield-admin',
            CONTENTSHIELD_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            CONTENTSHIELD_VERSION,
            true
        );

        wp_localize_script( 'contentshield-admin', 'contentshieldAdmin', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'contentshield_admin_nonce' ),
            'strings'     => array(
                'scanning'    => __( 'Scanning...', 'contentshield-ai' ),
                'scanComplete' => __( 'Scan complete!', 'contentshield-ai' ),
                'scanError'   => __( 'Scan failed. Please try again.', 'contentshield-ai' ),
                'confirm'     => __( 'Are you sure?', 'contentshield-ai' ),
                'saving'      => __( 'Saving...', 'contentshield-ai' ),
                'saved'       => __( 'Saved!', 'contentshield-ai' ),
                'error'       => __( 'An error occurred.', 'contentshield-ai' ),
            ),
        ));
    }

    /**
     * Render dashboard page.
     *
     * @return void
     */
    public function render_dashboard_page() {
        $stats = $this->get_dashboard_stats();
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/dashboard.php';
    }

    /**
     * Render protected content page.
     *
     * @return void
     */
    public function render_protected_content_page() {
        $protected_content = $this->get_protected_content();
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/protected-content.php';
    }

    /**
     * Render scanner page.
     *
     * @return void
     */
    public function render_scanner_page() {
        $recent_scans = $this->get_recent_scans();
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/scans.php';
    }

    /**
     * Render alerts page.
     *
     * @return void
     */
    public function render_alerts_page() {
        $alerts = $this->get_alerts();
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/alerts.php';
    }

    /**
     * Render settings page.
     *
     * @return void
     */
    public function render_settings_page() {
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/settings.php';
    }

    /**
     * Render Pro features page.
     *
     * @return void
     */
    public function render_pro_page() {
        include CONTENTSHIELD_PLUGIN_DIR . 'includes/admin/views/pro.php';
    }

    /**
     * Add meta boxes to post editor.
     *
     * @return void
     */
    public function add_meta_boxes() {
        $post_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'contentshield_protection',
                __( 'ContentShield Protection', 'contentshield-ai' ),
                array( $this, 'render_meta_box' ),
                $post_type,
                'side',
                'default'
            );
        }
    }

    /**
     * Render meta box content.
     *
     * @param WP_Post $post Current post object.
     * @return void
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'contentshield_meta_box', 'contentshield_meta_box_nonce' );

        $fingerprint = $this->plugin->get_fingerprint()->get( $post->ID );
        $is_protected = (bool) $fingerprint;
        $protection_disabled = get_post_meta( $post->ID, '_contentshield_disabled', true );
        ?>
        <div class="contentshield-meta-box">
            <p>
                <label>
                    <input type="checkbox" name="contentshield_enabled" value="1" <?php checked( ! $protection_disabled ); ?>>
                    <?php esc_html_e( 'Enable protection for this content', 'contentshield-ai' ); ?>
                </label>
            </p>

            <?php if ( $is_protected ) : ?>
                <p class="contentshield-status protected">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e( 'Content is protected', 'contentshield-ai' ); ?>
                </p>
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: date */
                        esc_html__( 'Fingerprint created: %s', 'contentshield-ai' ),
                        esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fingerprint->created_at ) ) )
                    );
                    ?>
                </p>
            <?php else : ?>
                <p class="contentshield-status not-protected">
                    <span class="dashicons dashicons-warning"></span>
                    <?php esc_html_e( 'Not yet protected', 'contentshield-ai' ); ?>
                </p>
                <p class="description">
                    <?php esc_html_e( 'Content will be protected automatically on publish.', 'contentshield-ai' ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $is_protected ) : ?>
                <p>
                    <button type="button" class="button contentshield-regenerate" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
                        <?php esc_html_e( 'Regenerate Fingerprint', 'contentshield-ai' ); ?>
                    </button>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save post meta.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return void
     */
    public function save_post_meta( $post_id, $post ) {
        // Verify nonce
        if ( ! isset( $_POST['contentshield_meta_box_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['contentshield_meta_box_nonce'] ) ), 'contentshield_meta_box' ) ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Skip autosaves
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Save protection status
        $enabled = isset( $_POST['contentshield_enabled'] ) ? '0' : '1';
        update_post_meta( $post_id, '_contentshield_disabled', $enabled );
    }

    /**
     * Display admin notices.
     *
     * @return void
     */
    public function display_notices() {
        // Check for unresolved alerts
        $unread_alerts = $this->get_unread_alert_count();

        if ( $unread_alerts > 0 && current_user_can( 'view_contentshield_reports' ) ) {
            $dismissed = get_user_meta( get_current_user_id(), 'contentshield_alerts_dismissed', true );

            if ( ! $dismissed ) {
                ?>
                <div class="notice notice-warning is-dismissible contentshield-notice" data-notice="alerts">
                    <p>
                        <?php
                        printf(
                            /* translators: %d: number of alerts */
                            esc_html( _n(
                                'ContentShield AI: You have %d unread alert.',
                                'ContentShield AI: You have %d unread alerts.',
                                $unread_alerts,
                                'contentshield-ai'
                            ) ),
                            esc_html( $unread_alerts )
                        );
                        ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-alerts' ) ); ?>">
                            <?php esc_html_e( 'View Alerts', 'contentshield-ai' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
        }
    }

    /**
     * AJAX handler for dismissing notices.
     *
     * @return void
     */
    public function ajax_dismiss_notice() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $notice = isset( $_POST['notice'] ) ? sanitize_text_field( wp_unslash( $_POST['notice'] ) ) : '';

        if ( 'alerts' === $notice ) {
            update_user_meta( get_current_user_id(), 'contentshield_alerts_dismissed', time() );
        }

        wp_send_json_success();
    }

    /**
     * AJAX handler for getting dashboard stats.
     *
     * @return void
     */
    public function ajax_get_stats() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'view_contentshield_reports' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $stats = $this->get_dashboard_stats();
        wp_send_json_success( $stats );
    }

    /**
     * Add plugin action links.
     *
     * @param array $links Existing links.
     * @return array Modified links.
     */
    public function add_action_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=contentshield-settings' ) . '">' . __( 'Settings', 'contentshield-ai' ) . '</a>',
        );

        if ( ! $this->plugin->is_pro() ) {
            $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=contentshield-pro' ) . '" style="color:#f39c12;font-weight:bold;">' . __( 'Go Pro', 'contentshield-ai' ) . '</a>';
        }

        return array_merge( $plugin_links, $links );
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    private function get_dashboard_stats() {
        global $wpdb;

        $fingerprints_table = $wpdb->prefix . 'contentshield_fingerprints';
        $scans_table        = $wpdb->prefix . 'contentshield_scans';
        $alerts_table       = $wpdb->prefix . 'contentshield_alerts';

        return array(
            'protected_posts'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$fingerprints_table}" ),
            'total_scans'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$scans_table}" ),
            'potential_matches' => (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$scans_table} WHERE similarity_score >= %f",
                50.0
            ) ),
            'unread_alerts'     => (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$alerts_table} WHERE is_read = %d",
                0
            ) ),
            'recent_scans'      => $this->get_recent_scans( 5 ),
        );
    }

    /**
     * Get protected content list.
     *
     * @param int $limit Number of items to retrieve.
     * @return array
     */
    private function get_protected_content( $limit = 50 ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_fingerprints';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT f.*, p.post_title, p.post_type, p.post_status
             FROM {$table} f
             LEFT JOIN {$wpdb->posts} p ON f.post_id = p.ID
             ORDER BY f.created_at DESC
             LIMIT %d",
            $limit
        ) );
    }

    /**
     * Get recent scans.
     *
     * @param int $limit Number of scans to retrieve.
     * @return array
     */
    private function get_recent_scans( $limit = 10 ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_scans';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT s.*, p.post_title
             FROM {$table} s
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             ORDER BY s.created_at DESC
             LIMIT %d",
            $limit
        ) );
    }

    /**
     * Get alerts.
     *
     * @param int $limit Number of alerts to retrieve.
     * @return array
     */
    private function get_alerts( $limit = 50 ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_alerts';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT a.*, p.post_title
             FROM {$table} a
             LEFT JOIN {$wpdb->posts} p ON a.post_id = p.ID
             ORDER BY a.created_at DESC
             LIMIT %d",
            $limit
        ) );
    }

    /**
     * Get unread alert count.
     *
     * @return int
     */
    private function get_unread_alert_count() {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_alerts';

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE is_read = %d",
            0
        ) );
    }

    /**
     * AJAX handler for marking alert as read.
     *
     * @return void
     */
    public function ajax_mark_alert_read() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        global $wpdb;
        $alert_id = isset( $_POST['alert_id'] ) ? absint( $_POST['alert_id'] ) : 0;

        if ( ! $alert_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid alert ID.', 'contentshield-ai' ) ) );
        }

        $table = $wpdb->prefix . 'contentshield_alerts';
        $wpdb->update(
            $table,
            array( 'is_read' => 1 ),
            array( 'id' => $alert_id ),
            array( '%d' ),
            array( '%d' )
        );

        wp_send_json_success();
    }

    /**
     * AJAX handler for marking all alerts as read.
     *
     * @return void
     */
    public function ajax_mark_all_alerts_read() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'contentshield_alerts';

        $wpdb->update(
            $table,
            array( 'is_read' => 1 ),
            array( 'is_read' => 0 ),
            array( '%d' ),
            array( '%d' )
        );

        wp_send_json_success();
    }

    /**
     * AJAX handler for resolving alert.
     *
     * @return void
     */
    public function ajax_resolve_alert() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        global $wpdb;
        $alert_id = isset( $_POST['alert_id'] ) ? absint( $_POST['alert_id'] ) : 0;

        if ( ! $alert_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid alert ID.', 'contentshield-ai' ) ) );
        }

        $table = $wpdb->prefix . 'contentshield_alerts';
        $wpdb->update(
            $table,
            array(
                'is_resolved' => 1,
                'resolved_at' => current_time( 'mysql' ),
            ),
            array( 'id' => $alert_id ),
            array( '%d', '%s' ),
            array( '%d' )
        );

        wp_send_json_success();
    }

    /**
     * AJAX handler for exporting fingerprints.
     *
     * @return void
     */
    public function ajax_export_fingerprints() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_die( esc_html__( 'Permission denied.', 'contentshield-ai' ) );
        }

        $fingerprint = new ContentShield_Fingerprint();
        $csv = $fingerprint->export_csv();

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=contentshield-fingerprints-' . gmdate( 'Y-m-d' ) . '.csv' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
}
