<?php
/**
 * REST API endpoints for the plugin.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_REST_Endpoints
 *
 * Registers and handles REST API endpoints.
 */
class ContentShield_REST_Endpoints {

    /**
     * API namespace.
     *
     * @var string
     */
    const NAMESPACE = 'contentshield/v1';

    /**
     * Register REST routes.
     *
     * @return void
     */
    public function register_routes() {
        // Fingerprint endpoints
        register_rest_route( self::NAMESPACE, '/fingerprint/(?P<post_id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_fingerprint' ),
                'permission_callback' => array( $this, 'check_read_permission' ),
                'args'                => array(
                    'post_id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'generate_fingerprint' ),
                'permission_callback' => array( $this, 'check_write_permission' ),
                'args'                => array(
                    'post_id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_fingerprint' ),
                'permission_callback' => array( $this, 'check_write_permission' ),
                'args'                => array(
                    'post_id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                ),
            ),
        ) );

        // Fingerprints list
        register_rest_route( self::NAMESPACE, '/fingerprints', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_fingerprints' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'page'     => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 20,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // Compare fingerprints
        register_rest_route( self::NAMESPACE, '/fingerprint/compare', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'compare_fingerprints' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'fingerprint1' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'fingerprint2' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ) );

        // Scan URL
        register_rest_route( self::NAMESPACE, '/scan', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'scan_url' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
            'args'                => array(
                'url' => array(
                    'required'          => true,
                    'validate_callback' => array( $this, 'validate_url' ),
                    'sanitize_callback' => 'esc_url_raw',
                ),
                'post_id' => array(
                    'required'          => false,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // Get scans
        register_rest_route( self::NAMESPACE, '/scans', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_scans' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'page'     => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 20,
                    'sanitize_callback' => 'absint',
                ),
                'status'   => array(
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_key',
                ),
            ),
        ) );

        // Alerts
        register_rest_route( self::NAMESPACE, '/alerts', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_alerts' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'page'     => array(
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default'           => 20,
                    'sanitize_callback' => 'absint',
                ),
                'unread_only' => array(
                    'default'           => false,
                    'sanitize_callback' => 'rest_sanitize_boolean',
                ),
            ),
        ) );

        // Mark alert as read
        register_rest_route( self::NAMESPACE, '/alerts/(?P<alert_id>\d+)/read', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'mark_alert_read' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
            'args'                => array(
                'alert_id' => array(
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                ),
            ),
        ) );

        // Statistics
        register_rest_route( self::NAMESPACE, '/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_stats' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );

        // Protection status
        register_rest_route( self::NAMESPACE, '/protection/(?P<post_id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_protection_status' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'post_id' => array(
                    'required'          => true,
                    'validate_callback' => array( $this, 'validate_post_id' ),
                ),
            ),
        ) );

        // Watermark verify
        register_rest_route( self::NAMESPACE, '/watermark/verify', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( $this, 'verify_watermark' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
            'args'                => array(
                'content' => array(
                    'required'          => true,
                    'sanitize_callback' => 'wp_kses_post',
                ),
            ),
        ) );

        // Export fingerprints
        register_rest_route( self::NAMESPACE, '/export/fingerprints', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'export_fingerprints' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
            'args'                => array(
                'format' => array(
                    'default'           => 'json',
                    'sanitize_callback' => 'sanitize_key',
                ),
            ),
        ) );
    }

    /**
     * Check read permission.
     *
     * @return bool|WP_Error
     */
    public function check_read_permission() {
        if ( ! current_user_can( 'view_contentshield_reports' ) ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to access this resource.', 'contentshield-ai' ),
                array( 'status' => 403 )
            );
        }

        return true;
    }

    /**
     * Check write permission.
     *
     * @return bool|WP_Error
     */
    public function check_write_permission() {
        if ( ! current_user_can( 'manage_contentshield' ) ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to modify this resource.', 'contentshield-ai' ),
                array( 'status' => 403 )
            );
        }

        return true;
    }

    /**
     * Validate post ID.
     *
     * @param mixed $value Value to validate.
     * @return bool
     */
    public function validate_post_id( $value ) {
        $post_id = absint( $value );
        return $post_id > 0 && get_post( $post_id );
    }

    /**
     * Validate URL.
     *
     * @param mixed $value Value to validate.
     * @return bool
     */
    public function validate_url( $value ) {
        return filter_var( $value, FILTER_VALIDATE_URL ) !== false;
    }

    /**
     * Get fingerprint for a post.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_fingerprint( $request ) {
        $post_id = $request->get_param( 'post_id' );
        $fingerprint = new ContentShield_Fingerprint();
        $data = $fingerprint->get( $post_id );

        if ( ! $data ) {
            return new WP_Error(
                'not_found',
                __( 'Fingerprint not found for this post.', 'contentshield-ai' ),
                array( 'status' => 404 )
            );
        }

        return rest_ensure_response( array(
            'post_id'     => $data->post_id,
            'fingerprint' => $data->fingerprint,
            'word_count'  => $data->word_count,
            'created_at'  => $data->created_at,
            'updated_at'  => $data->updated_at,
        ) );
    }

    /**
     * Generate fingerprint for a post.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function generate_fingerprint( $request ) {
        $post_id = $request->get_param( 'post_id' );
        $fingerprint = new ContentShield_Fingerprint();
        $result = $fingerprint->generate( $post_id );

        if ( ! $result ) {
            return new WP_Error(
                'generation_failed',
                __( 'Failed to generate fingerprint. Content may be too short.', 'contentshield-ai' ),
                array( 'status' => 400 )
            );
        }

        return rest_ensure_response( array(
            'success'     => true,
            'fingerprint' => $result['fingerprint'],
            'word_count'  => $result['word_count'],
        ) );
    }

    /**
     * Delete fingerprint for a post.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function delete_fingerprint( $request ) {
        $post_id = $request->get_param( 'post_id' );
        $fingerprint = new ContentShield_Fingerprint();
        $result = $fingerprint->delete( $post_id );

        if ( ! $result ) {
            return new WP_Error(
                'delete_failed',
                __( 'Failed to delete fingerprint.', 'contentshield-ai' ),
                array( 'status' => 400 )
            );
        }

        return rest_ensure_response( array(
            'success' => true,
            'message' => __( 'Fingerprint deleted successfully.', 'contentshield-ai' ),
        ) );
    }

    /**
     * Get list of fingerprints.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_fingerprints( $request ) {
        global $wpdb;

        $page     = $request->get_param( 'page' );
        $per_page = min( $request->get_param( 'per_page' ), 100 );
        $offset   = ( $page - 1 ) * $per_page;

        $table = $wpdb->prefix . 'contentshield_fingerprints';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT f.*, p.post_title
             FROM {$table} f
             LEFT JOIN {$wpdb->posts} p ON f.post_id = p.ID
             ORDER BY f.created_at DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ) );

        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );

        return rest_ensure_response( array(
            'fingerprints' => $results,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $per_page,
            'total_pages'  => ceil( $total / $per_page ),
        ) );
    }

    /**
     * Compare two fingerprints.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function compare_fingerprints( $request ) {
        $fingerprint1 = $request->get_param( 'fingerprint1' );
        $fingerprint2 = $request->get_param( 'fingerprint2' );

        $fp = new ContentShield_Fingerprint();
        $similarity = $fp->compare( $fingerprint1, $fingerprint2 );

        return rest_ensure_response( array(
            'similarity' => $similarity,
            'is_match'   => $similarity >= 70,
        ) );
    }

    /**
     * Scan a URL.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function scan_url( $request ) {
        $url     = $request->get_param( 'url' );
        $post_id = $request->get_param( 'post_id' );

        $scanner = new ContentShield_Scanner();
        $result = $scanner->scan( $url, $post_id ?: null );

        if ( ! $result['success'] ) {
            return new WP_Error(
                'scan_failed',
                $result['error'],
                array( 'status' => 400 )
            );
        }

        return rest_ensure_response( $result );
    }

    /**
     * Get scans list.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_scans( $request ) {
        global $wpdb;

        $page     = $request->get_param( 'page' );
        $per_page = min( $request->get_param( 'per_page' ), 100 );
        $status   = $request->get_param( 'status' );
        $offset   = ( $page - 1 ) * $per_page;

        $table = $wpdb->prefix . 'contentshield_scans';
        $where = '1=1';

        if ( $status ) {
            $where .= $wpdb->prepare( ' AND status = %s', $status );
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT s.*, p.post_title
             FROM {$table} s
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             WHERE {$where}
             ORDER BY s.created_at DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ) );

        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE {$where}" );

        return rest_ensure_response( array(
            'scans'       => $results,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total / $per_page ),
        ) );
    }

    /**
     * Get alerts list.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_alerts( $request ) {
        global $wpdb;

        $page        = $request->get_param( 'page' );
        $per_page    = min( $request->get_param( 'per_page' ), 100 );
        $unread_only = $request->get_param( 'unread_only' );
        $offset      = ( $page - 1 ) * $per_page;

        $table = $wpdb->prefix . 'contentshield_alerts';
        $where = '1=1';

        if ( $unread_only ) {
            $where .= ' AND is_read = 0';
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT a.*, p.post_title
             FROM {$table} a
             LEFT JOIN {$wpdb->posts} p ON a.post_id = p.ID
             WHERE {$where}
             ORDER BY a.created_at DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ) );

        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE {$where}" );

        return rest_ensure_response( array(
            'alerts'      => $results,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total / $per_page ),
        ) );
    }

    /**
     * Mark alert as read.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function mark_alert_read( $request ) {
        global $wpdb;

        $alert_id = $request->get_param( 'alert_id' );
        $table = $wpdb->prefix . 'contentshield_alerts';

        $wpdb->update(
            $table,
            array( 'is_read' => 1 ),
            array( 'id' => $alert_id ),
            array( '%d' ),
            array( '%d' )
        );

        return rest_ensure_response( array(
            'success' => true,
        ) );
    }

    /**
     * Get statistics.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_stats( $request ) {
        $stats = ContentShield_Protection::get_statistics();
        return rest_ensure_response( $stats );
    }

    /**
     * Get protection status.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_protection_status( $request ) {
        $post_id = $request->get_param( 'post_id' );
        $status = ContentShield_Protection::get_status( $post_id );
        return rest_ensure_response( $status );
    }

    /**
     * Verify watermark in content.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function verify_watermark( $request ) {
        $content = $request->get_param( 'content' );
        $watermark = new ContentShield_Watermark();

        $extracted = $watermark->extract( $content );
        $is_ours = $watermark->verify( $content );

        return rest_ensure_response( array(
            'has_watermark' => $extracted !== false,
            'is_ours'       => $is_ours,
            'data'          => $extracted,
        ) );
    }

    /**
     * Export fingerprints.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function export_fingerprints( $request ) {
        $format = $request->get_param( 'format' );
        $fingerprint = new ContentShield_Fingerprint();

        if ( 'csv' === $format ) {
            $csv = $fingerprint->export_csv();
            return new WP_REST_Response(
                $csv,
                200,
                array(
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="contentshield-fingerprints.csv"',
                )
            );
        }

        // Default to JSON
        global $wpdb;
        $table = $wpdb->prefix . 'contentshield_fingerprints';

        $results = $wpdb->get_results(
            "SELECT f.*, p.post_title
             FROM {$table} f
             LEFT JOIN {$wpdb->posts} p ON f.post_id = p.ID
             ORDER BY f.created_at DESC"
        );

        return rest_ensure_response( array(
            'fingerprints' => $results,
            'exported_at'  => current_time( 'mysql' ),
        ) );
    }
}
