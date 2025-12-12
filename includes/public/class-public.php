<?php
/**
 * Public-facing functionality.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Public
 *
 * Handles public-facing features like RSS protection and copy detection.
 */
class ContentShield_Public {

    /**
     * Main plugin instance.
     *
     * @var ContentShield_AI
     */
    private $plugin;

    /**
     * Constructor.
     *
     * @param ContentShield_AI $plugin Main plugin instance.
     */
    public function __construct( $plugin ) {
        $this->plugin = $plugin;
    }

    /**
     * Enqueue public styles.
     *
     * @return void
     */
    public function enqueue_styles() {
        // Only load if copy protection is enabled
        if ( ! get_option( 'contentshield_copy_protection', false ) ) {
            return;
        }

        wp_enqueue_style(
            'contentshield-public',
            CONTENTSHIELD_PLUGIN_URL . 'assets/css/public.css',
            array(),
            CONTENTSHIELD_VERSION
        );
    }

    /**
     * Enqueue public scripts.
     *
     * @return void
     */
    public function enqueue_scripts() {
        // Only load if copy protection is enabled
        if ( ! get_option( 'contentshield_copy_protection', false ) ) {
            return;
        }

        // Don't load for logged-in editors/admins
        if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
            return;
        }

        wp_enqueue_script(
            'contentshield-public',
            CONTENTSHIELD_PLUGIN_URL . 'assets/js/public.js',
            array(),
            CONTENTSHIELD_VERSION,
            true
        );

        wp_localize_script( 'contentshield-public', 'contentshieldPublic', array(
            'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
            'nonce'              => wp_create_nonce( 'contentshield_public_nonce' ),
            'rightClickDisabled' => get_option( 'contentshield_right_click_disable', false ),
            'postId'             => get_the_ID(),
        ) );
    }

    /**
     * Add copy protection styles.
     *
     * @return void
     */
    public function add_copy_protection_styles() {
        if ( ! get_option( 'contentshield_copy_protection', false ) ) {
            return;
        }

        // Don't apply for logged-in editors/admins
        if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
            return;
        }

        $disable_selection = ! get_option( 'contentshield_text_selection', true );
        ?>
        <style id="contentshield-protection-styles">
            <?php if ( $disable_selection ) : ?>
            .entry-content,
            .post-content,
            article {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            <?php endif; ?>
        </style>
        <?php
    }

    /**
     * Add copy protection scripts.
     *
     * @return void
     */
    public function add_copy_protection_scripts() {
        if ( ! get_option( 'contentshield_copy_protection', false ) ) {
            return;
        }

        // Don't apply for logged-in editors/admins
        if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
            return;
        }

        $right_click_disabled = get_option( 'contentshield_right_click_disable', false );
        ?>
        <script id="contentshield-protection-script">
        (function() {
            'use strict';

            var postId = <?php echo wp_json_encode( get_the_ID() ); ?>;

            // Copy detection
            document.addEventListener('copy', function(e) {
                var selection = window.getSelection().toString();
                if (selection.length > 50) {
                    // Log copy attempt
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('action=contentshield_log_copy&post_id=' + postId + '&length=' + selection.length + '&nonce=<?php echo esc_js( wp_create_nonce( 'contentshield_public_nonce' ) ); ?>');
                }
            });

            <?php if ( $right_click_disabled ) : ?>
            // Disable right-click
            document.addEventListener('contextmenu', function(e) {
                if (e.target.closest('.entry-content, .post-content, article')) {
                    e.preventDefault();
                    return false;
                }
            });
            <?php endif; ?>
        })();
        </script>
        <?php
    }

    /**
     * Protect RSS feed content.
     *
     * @param string $content Feed content.
     * @return string Modified content.
     */
    public function protect_feed_content( $content ) {
        if ( ! get_option( 'contentshield_rss_protection', true ) ) {
            return $content;
        }

        $post_id = get_the_ID();

        // Check if protection is disabled for this post
        if ( get_post_meta( $post_id, '_contentshield_disabled', true ) ) {
            return $content;
        }

        // Add attribution
        if ( get_option( 'contentshield_rss_attribution', true ) ) {
            $attribution = $this->get_attribution_html( $post_id );
            $content = $content . $attribution;
        }

        return $content;
    }

    /**
     * Protect RSS feed excerpt.
     *
     * @param string $excerpt Feed excerpt.
     * @return string Modified excerpt.
     */
    public function protect_feed_excerpt( $excerpt ) {
        if ( ! get_option( 'contentshield_rss_protection', true ) ) {
            return $excerpt;
        }

        $post_id = get_the_ID();

        // Check if protection is disabled for this post
        if ( get_post_meta( $post_id, '_contentshield_disabled', true ) ) {
            return $excerpt;
        }

        // Add attribution link
        if ( get_option( 'contentshield_rss_attribution', true ) ) {
            $attribution = sprintf(
                ' <a href="%s">%s</a>',
                esc_url( get_permalink( $post_id ) ),
                esc_html__( 'Read more', 'contentshield-ai' )
            );
            $excerpt = $excerpt . $attribution;
        }

        return $excerpt;
    }

    /**
     * Get attribution HTML for RSS feeds.
     *
     * @param int $post_id Post ID.
     * @return string Attribution HTML.
     */
    private function get_attribution_html( $post_id ) {
        $post_url   = get_permalink( $post_id );
        $post_title = get_the_title( $post_id );
        $site_name  = get_bloginfo( 'name' );
        $site_url   = home_url();

        $html = '<p style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 12px; color: #666;">';
        $html .= sprintf(
            /* translators: 1: post title with link, 2: site name with link */
            esc_html__( 'Originally published as %1$s on %2$s.', 'contentshield-ai' ),
            '<a href="' . esc_url( $post_url ) . '">' . esc_html( $post_title ) . '</a>',
            '<a href="' . esc_url( $site_url ) . '">' . esc_html( $site_name ) . '</a>'
        );
        $html .= '</p>';

        return $html;
    }

    /**
     * AJAX handler for logging copy attempts.
     *
     * @return void
     */
    public function ajax_log_copy() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'contentshield_public_nonce' ) ) {
            wp_send_json_error();
        }

        global $wpdb;

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $length  = isset( $_POST['length'] ) ? absint( $_POST['length'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error();
        }

        // Get visitor info
        $ip_address = $this->get_visitor_ip();
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

        // Log the copy attempt
        $table = $wpdb->prefix . 'contentshield_logs';

        $wpdb->insert(
            $table,
            array(
                'event_type' => 'copy_detected',
                'post_id'    => $post_id,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'details'    => wp_json_encode( array(
                    'length'    => $length,
                    'timestamp' => current_time( 'mysql' ),
                ) ),
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%d', '%s', '%s', '%s', '%s' )
        );

        // Maybe create alert for suspicious activity
        $this->maybe_create_copy_alert( $post_id, $ip_address, $length );

        wp_send_json_success();
    }

    /**
     * Maybe create alert for suspicious copy activity.
     *
     * @param int    $post_id    Post ID.
     * @param string $ip_address Visitor IP.
     * @param int    $length     Copied text length.
     * @return void
     */
    private function maybe_create_copy_alert( $post_id, $ip_address, $length ) {
        global $wpdb;

        // Only alert for large copies (> 500 chars)
        if ( $length < 500 ) {
            return;
        }

        // Check if we already have a recent alert for this IP/post combo
        $alerts_table = $wpdb->prefix . 'contentshield_alerts';
        $recent_alert = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$alerts_table}
             WHERE post_id = %d
             AND alert_type = 'copy_detected'
             AND created_at >= %s
             AND details LIKE %s",
            $post_id,
            gmdate( 'Y-m-d H:i:s', strtotime( '-1 hour' ) ),
            '%' . $wpdb->esc_like( $ip_address ) . '%'
        ) );

        if ( $recent_alert ) {
            return;
        }

        // Create alert
        $wpdb->insert(
            $alerts_table,
            array(
                'post_id'     => $post_id,
                'alert_type'  => 'copy_detected',
                'severity'    => $length > 1000 ? 'high' : 'medium',
                'source_url'  => null,
                'details'     => wp_json_encode( array(
                    'ip_address' => $ip_address,
                    'length'     => $length,
                ) ),
                'is_read'     => 0,
                'is_resolved' => 0,
                'created_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
        );
    }

    /**
     * Get visitor IP address.
     *
     * @return string IP address.
     */
    private function get_visitor_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Can contain multiple IPs, get the first one
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
            $ip = explode( ',', $ip )[0];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return trim( $ip );
    }
}

// Register AJAX handler
add_action( 'wp_ajax_nopriv_contentshield_log_copy', array( 'ContentShield_Public', 'ajax_log_copy' ) );
add_action( 'wp_ajax_contentshield_log_copy', array( 'ContentShield_Public', 'ajax_log_copy' ) );
