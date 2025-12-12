<?php
/**
 * License validation and management.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_License
 *
 * Handles license validation, activation, and feature checks.
 */
class ContentShield_License {

    /**
     * License key pattern.
     *
     * @var string
     */
    const KEY_PATTERN = '/^CSAI-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/i';

    /**
     * API client instance.
     *
     * @var ContentShield_API_Client
     */
    private $api_client;

    /**
     * Cached license data.
     *
     * @var array|null
     */
    private $license_data = null;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->api_client = new ContentShield_API_Client();
    }

    /**
     * Activate a license key.
     *
     * @param string $license_key License key to activate.
     * @return array Result with success status and message.
     */
    public function activate( $license_key ) {
        // Validate key format
        if ( ! $this->validate_format( $license_key ) ) {
            return array(
                'success' => false,
                'error'   => __( 'Invalid license key format. Expected: CSAI-XXXX-XXXX-XXXX-XXXX', 'contentshield-ai' ),
            );
        }

        // Call API to validate
        $response = $this->api_client->validate_license( $license_key );

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        if ( empty( $response['valid'] ) ) {
            return array(
                'success' => false,
                'error'   => $response['message'] ?? __( 'License validation failed.', 'contentshield-ai' ),
            );
        }

        // Store license data
        $license_data = array(
            'key_masked'  => $this->mask_key( $license_key ),
            'key_hash'    => hash( 'sha256', $license_key ),
            'plan'        => $response['license']['plan'] ?? 'pro',
            'status'      => 'active',
            'expires_at'  => $response['license']['expires_at'] ?? null,
            'features'    => $response['license']['features'] ?? array(),
            'activated_at' => current_time( 'mysql' ),
        );

        update_option( 'contentshield_license', $license_data );

        // Clear cached data
        $this->license_data = null;
        delete_transient( 'contentshield_license_check' );

        // Log activation
        $this->log_event( 'license_activated', $license_data['plan'] );

        return array(
            'success' => true,
            'message' => __( 'License activated successfully.', 'contentshield-ai' ),
            'license' => $license_data,
        );
    }

    /**
     * Deactivate the current license.
     *
     * @return array Result with success status and message.
     */
    public function deactivate() {
        $license = $this->get_license();

        if ( empty( $license['key_hash'] ) ) {
            return array(
                'success' => false,
                'error'   => __( 'No active license found.', 'contentshield-ai' ),
            );
        }

        // Call API to deactivate (non-blocking)
        $this->api_client->deactivate_license( $license['key_hash'] );

        // Clear local license data
        delete_option( 'contentshield_license' );
        delete_transient( 'contentshield_license_check' );
        $this->license_data = null;

        // Log deactivation
        $this->log_event( 'license_deactivated' );

        return array(
            'success' => true,
            'message' => __( 'License deactivated successfully.', 'contentshield-ai' ),
        );
    }

    /**
     * Check if license is valid.
     *
     * @return bool True if license is valid and active.
     */
    public function is_valid() {
        $license = $this->get_license();

        if ( empty( $license['status'] ) || $license['status'] !== 'active' ) {
            return false;
        }

        // Check expiration
        if ( ! empty( $license['expires_at'] ) ) {
            $expires = strtotime( $license['expires_at'] );
            if ( $expires && $expires < time() ) {
                return false;
            }
        }

        // Periodic validation with API (cached for 24 hours)
        return $this->periodic_validation();
    }

    /**
     * Get current plan.
     *
     * @return string Plan name or 'free'.
     */
    public function get_plan() {
        $license = $this->get_license();

        if ( $this->is_valid() && ! empty( $license['plan'] ) ) {
            return $license['plan'];
        }

        return 'free';
    }

    /**
     * Check if a specific feature is available.
     *
     * @param string $feature Feature name.
     * @return bool True if feature is available.
     */
    public function has_feature( $feature ) {
        // Free features available to all
        $free_features = array(
            'watermarking',
            'fingerprinting',
            'manual_scanning',
            'rss_protection',
            'copy_detection',
        );

        if ( in_array( $feature, $free_features, true ) ) {
            return true;
        }

        // Pro features require valid license
        if ( ! $this->is_valid() ) {
            return false;
        }

        $license = $this->get_license();
        $features = $license['features'] ?? array();

        // Check specific feature
        if ( isset( $features[ $feature ] ) ) {
            return (bool) $features[ $feature ];
        }

        // Plan-based feature check
        $plan = $this->get_plan();

        $plan_features = array(
            'starter' => array(
                'monitoring'       => true,
                'ai_matching'      => true,
                'dmca_templates'   => true,
                'auto_dmca'        => false,
                'api_access'       => false,
                'white_label'      => false,
            ),
            'pro' => array(
                'monitoring'       => true,
                'ai_matching'      => true,
                'dmca_templates'   => true,
                'auto_dmca'        => true,
                'api_access'       => true,
                'white_label'      => false,
            ),
            'agency' => array(
                'monitoring'       => true,
                'ai_matching'      => true,
                'dmca_templates'   => true,
                'auto_dmca'        => true,
                'api_access'       => true,
                'white_label'      => true,
            ),
        );

        if ( isset( $plan_features[ $plan ][ $feature ] ) ) {
            return $plan_features[ $plan ][ $feature ];
        }

        return false;
    }

    /**
     * Get license data.
     *
     * @return array License data array.
     */
    public function get_license() {
        if ( null === $this->license_data ) {
            $this->license_data = get_option( 'contentshield_license', array() );
        }

        return $this->license_data;
    }

    /**
     * Get license status for display.
     *
     * @return array Status information.
     */
    public function get_status() {
        $license = $this->get_license();
        $is_valid = $this->is_valid();

        $status = array(
            'is_active'   => $is_valid,
            'plan'        => $this->get_plan(),
            'key_masked'  => $license['key_masked'] ?? null,
            'expires_at'  => $license['expires_at'] ?? null,
            'days_left'   => null,
        );

        if ( ! empty( $license['expires_at'] ) ) {
            $expires = strtotime( $license['expires_at'] );
            if ( $expires ) {
                $status['days_left'] = max( 0, ceil( ( $expires - time() ) / DAY_IN_SECONDS ) );
            }
        }

        return $status;
    }

    /**
     * Validate license key format.
     *
     * @param string $license_key License key.
     * @return bool True if format is valid.
     */
    public function validate_format( $license_key ) {
        return (bool) preg_match( self::KEY_PATTERN, $license_key );
    }

    /**
     * Mask license key for display.
     *
     * @param string $license_key License key.
     * @return string Masked key.
     */
    public function mask_key( $license_key ) {
        if ( strlen( $license_key ) < 9 ) {
            return '****';
        }

        return substr( $license_key, 0, 9 ) . '-****-****-****';
    }

    /**
     * Periodic validation with API.
     *
     * @return bool True if valid.
     */
    private function periodic_validation() {
        // Check cache first
        $cached = get_transient( 'contentshield_license_check' );
        if ( $cached !== false ) {
            return $cached === 'valid';
        }

        // Attempt API validation
        $license = $this->get_license();

        if ( empty( $license['key_hash'] ) ) {
            set_transient( 'contentshield_license_check', 'invalid', DAY_IN_SECONDS );
            return false;
        }

        // For now, trust local data if API is unreachable
        // In production, this would make an API call
        $is_valid = ! empty( $license['status'] ) && $license['status'] === 'active';

        // Check expiration locally
        if ( $is_valid && ! empty( $license['expires_at'] ) ) {
            $expires = strtotime( $license['expires_at'] );
            if ( $expires && $expires < time() ) {
                $is_valid = false;
            }
        }

        // Cache result
        set_transient(
            'contentshield_license_check',
            $is_valid ? 'valid' : 'invalid',
            DAY_IN_SECONDS
        );

        return $is_valid;
    }

    /**
     * Log license event.
     *
     * @param string $event Event type.
     * @param string $plan  Plan name.
     * @return void
     */
    private function log_event( $event, $plan = null ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_logs';

        $wpdb->insert(
            $table,
            array(
                'event_type' => $event,
                'details'    => wp_json_encode( array(
                    'plan'      => $plan,
                    'timestamp' => current_time( 'mysql' ),
                ) ),
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s' )
        );
    }

    /**
     * AJAX handler for license activation.
     *
     * @return void
     */
    public function ajax_activate() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield_settings' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a license key.', 'contentshield-ai' ) ) );
        }

        $result = $this->activate( $license_key );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * AJAX handler for license deactivation.
     *
     * @return void
     */
    public function ajax_deactivate() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield_settings' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $result = $this->deactivate();

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
}
