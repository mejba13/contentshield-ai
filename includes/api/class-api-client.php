<?php
/**
 * API client for SaaS backend communication.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_API_Client
 *
 * Handles communication with the ContentShield SaaS API.
 */
class ContentShield_API_Client {

    /**
     * API base URL.
     *
     * @var string
     */
    private $api_url;

    /**
     * Request timeout in seconds.
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->api_url = CONTENTSHIELD_API_URL;
    }

    /**
     * Make a GET request to the API.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Query parameters.
     * @return array|WP_Error Response data or error.
     */
    public function get( $endpoint, $params = array() ) {
        $url = $this->build_url( $endpoint, $params );

        $response = wp_remote_get( $url, array(
            'timeout' => $this->timeout,
            'headers' => $this->get_headers(),
        ) );

        return $this->handle_response( $response );
    }

    /**
     * Make a POST request to the API.
     *
     * @param string $endpoint API endpoint.
     * @param array  $data     Request body data.
     * @return array|WP_Error Response data or error.
     */
    public function post( $endpoint, $data = array() ) {
        $url = $this->build_url( $endpoint );

        $response = wp_remote_post( $url, array(
            'timeout' => $this->timeout,
            'headers' => $this->get_headers(),
            'body'    => wp_json_encode( $data ),
        ) );

        return $this->handle_response( $response );
    }

    /**
     * Make a DELETE request to the API.
     *
     * @param string $endpoint API endpoint.
     * @return array|WP_Error Response data or error.
     */
    public function delete( $endpoint ) {
        $url = $this->build_url( $endpoint );

        $response = wp_remote_request( $url, array(
            'method'  => 'DELETE',
            'timeout' => $this->timeout,
            'headers' => $this->get_headers(),
        ) );

        return $this->handle_response( $response );
    }

    /**
     * Build full API URL.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Query parameters.
     * @return string Full URL.
     */
    private function build_url( $endpoint, $params = array() ) {
        $url = trailingslashit( $this->api_url ) . ltrim( $endpoint, '/' );

        if ( ! empty( $params ) ) {
            $url = add_query_arg( $params, $url );
        }

        return $url;
    }

    /**
     * Get request headers.
     *
     * @return array Headers array.
     */
    private function get_headers() {
        $headers = array(
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'X-Site-URL'   => home_url(),
            'X-Site-Hash'  => $this->get_site_hash(),
            'User-Agent'   => 'ContentShield-WP/' . CONTENTSHIELD_VERSION,
        );

        // Add authorization if license is active
        $license = get_option( 'contentshield_license', array() );
        if ( ! empty( $license['key_hash'] ) ) {
            $headers['Authorization'] = 'Bearer ' . $license['key_hash'];
        }

        return $headers;
    }

    /**
     * Handle API response.
     *
     * @param array|WP_Error $response Response from wp_remote_*.
     * @return array|WP_Error Parsed response or error.
     */
    private function handle_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $data        = json_decode( $body, true );

        if ( $status_code >= 200 && $status_code < 300 ) {
            return $data;
        }

        // Handle error responses
        $error_message = isset( $data['message'] )
            ? $data['message']
            : sprintf(
                /* translators: %d: HTTP status code */
                __( 'API error: %d', 'contentshield-ai' ),
                $status_code
            );

        return new WP_Error(
            'api_error',
            $error_message,
            array(
                'status_code' => $status_code,
                'response'    => $data,
            )
        );
    }

    /**
     * Get site hash for identification.
     *
     * @return string Site hash.
     */
    public function get_site_hash() {
        return hash( 'sha256', home_url() . '|' . DB_NAME );
    }

    /**
     * Validate license key with API.
     *
     * @param string $license_key License key.
     * @return array|WP_Error Validation response or error.
     */
    public function validate_license( $license_key ) {
        return $this->post( 'license/validate', array(
            'license_key'    => $license_key,
            'site_url'       => home_url(),
            'site_hash'      => $this->get_site_hash(),
            'plugin_version' => CONTENTSHIELD_VERSION,
        ) );
    }

    /**
     * Deactivate license from site.
     *
     * @param string $license_key License key.
     * @return array|WP_Error Response or error.
     */
    public function deactivate_license( $license_key ) {
        return $this->post( 'license/deactivate', array(
            'license_key' => $license_key,
            'site_url'    => home_url(),
            'site_hash'   => $this->get_site_hash(),
        ) );
    }

    /**
     * Register content for monitoring.
     *
     * @param int    $post_id     Post ID.
     * @param string $fingerprint Content fingerprint.
     * @param array  $metadata    Additional metadata.
     * @return array|WP_Error Response or error.
     */
    public function register_content( $post_id, $fingerprint, $metadata = array() ) {
        $post = get_post( $post_id );

        if ( ! $post ) {
            return new WP_Error( 'invalid_post', __( 'Invalid post ID.', 'contentshield-ai' ) );
        }

        return $this->post( 'content/register', array(
            'external_id' => $post_id,
            'title'       => $post->post_title,
            'url'         => get_permalink( $post_id ),
            'fingerprint' => $fingerprint,
            'word_count'  => str_word_count( wp_strip_all_tags( $post->post_content ) ),
            'published_at' => $post->post_date_gmt,
            'metadata'    => $metadata,
        ) );
    }

    /**
     * Get monitoring results.
     *
     * @param array $params Query parameters.
     * @return array|WP_Error Response or error.
     */
    public function get_monitoring_results( $params = array() ) {
        return $this->get( 'monitoring/results', $params );
    }

    /**
     * Trigger manual monitoring scan.
     *
     * @param int $post_id Optional specific post ID.
     * @return array|WP_Error Response or error.
     */
    public function trigger_scan( $post_id = null ) {
        $data = array();

        if ( $post_id ) {
            $data['external_id'] = $post_id;
        }

        return $this->post( 'monitoring/scan', $data );
    }

    /**
     * Generate DMCA notice.
     *
     * @param int    $post_id        Post ID.
     * @param string $infringing_url URL of infringing content.
     * @return array|WP_Error Response or error.
     */
    public function generate_dmca( $post_id, $infringing_url ) {
        return $this->post( 'dmca/generate', array(
            'external_id'    => $post_id,
            'infringing_url' => $infringing_url,
        ) );
    }

    /**
     * Get DMCA templates.
     *
     * @return array|WP_Error Response or error.
     */
    public function get_dmca_templates() {
        return $this->get( 'dmca/templates' );
    }

    /**
     * Get dashboard statistics.
     *
     * @return array|WP_Error Response or error.
     */
    public function get_dashboard_stats() {
        return $this->get( 'reports/dashboard' );
    }

    /**
     * Check API connectivity.
     *
     * @return bool True if API is reachable.
     */
    public function is_connected() {
        $response = wp_remote_head( $this->api_url, array(
            'timeout' => 10,
        ) );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code( $response );

        return $status_code >= 200 && $status_code < 500;
    }
}
