<?php
/**
 * URL scanner for plagiarism detection.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Scanner
 *
 * Scans external URLs for potential plagiarism of protected content.
 */
class ContentShield_Scanner {

    /**
     * Database table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Fingerprint instance.
     *
     * @var ContentShield_Fingerprint
     */
    private $fingerprint;

    /**
     * Watermark instance.
     *
     * @var ContentShield_Watermark
     */
    private $watermark;

    /**
     * Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name  = $wpdb->prefix . 'contentshield_scans';
        $this->fingerprint = new ContentShield_Fingerprint();
        $this->watermark   = new ContentShield_Watermark();
    }

    /**
     * Scan a URL for plagiarism.
     *
     * @param string   $url     URL to scan.
     * @param int|null $post_id Optional post ID to compare against.
     * @return array Scan results.
     */
    public function scan( $url, $post_id = null ) {
        global $wpdb;

        // Validate URL
        $url = esc_url_raw( $url );
        if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return array(
                'success' => false,
                'error'   => __( 'Invalid URL provided.', 'contentshield-ai' ),
            );
        }

        // Don't scan our own site
        if ( $this->is_same_domain( $url, home_url() ) ) {
            return array(
                'success' => false,
                'error'   => __( 'Cannot scan URLs from your own website.', 'contentshield-ai' ),
            );
        }

        // Create scan record
        $scan_id = $this->create_scan_record( $url, $post_id );

        // Fetch external content
        $fetch_result = $this->fetch_url( $url );

        if ( ! $fetch_result['success'] ) {
            $this->update_scan_status( $scan_id, 'failed', null, $fetch_result['error'] );
            return array(
                'success' => false,
                'scan_id' => $scan_id,
                'error'   => $fetch_result['error'],
            );
        }

        $external_content = $fetch_result['content'];

        // Check for watermark
        $watermark_found = $this->watermark->verify( $external_content );

        // Clean content for comparison
        $clean_external = $this->clean_content( $external_content );

        // Generate fingerprint for external content
        $external_fingerprint = $this->generate_temp_fingerprint( $clean_external );

        // Compare against content
        $comparison_result = $this->compare_content( $external_fingerprint, $clean_external, $post_id );

        // Update scan record
        $this->update_scan_status(
            $scan_id,
            'completed',
            $comparison_result['similarity'],
            null,
            $comparison_result['matched_content']
        );

        // Create alert if similarity is high
        if ( $comparison_result['similarity'] >= 50 || $watermark_found ) {
            $this->create_alert( $scan_id, $post_id ?: $comparison_result['matched_post_id'], $url, $comparison_result['similarity'], $watermark_found );
        }

        return array(
            'success'         => true,
            'scan_id'         => $scan_id,
            'url'             => $url,
            'similarity'      => $comparison_result['similarity'],
            'watermark_found' => $watermark_found,
            'matched_post_id' => $comparison_result['matched_post_id'],
            'matched_content' => $comparison_result['matched_content'],
        );
    }

    /**
     * Fetch content from external URL.
     *
     * @param string $url URL to fetch.
     * @return array Result with success status and content or error.
     */
    private function fetch_url( $url ) {
        $timeout = get_option( 'contentshield_scan_timeout', 30 );

        $response = wp_remote_get( $url, array(
            'timeout'     => $timeout,
            'sslverify'   => true,
            'user-agent'  => 'ContentShield Bot/1.0 (+https://contentshield.ai/bot)',
            'headers'     => array(
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        $status_code = wp_remote_retrieve_response_code( $response );

        if ( $status_code !== 200 ) {
            return array(
                'success' => false,
                'error'   => sprintf(
                    /* translators: %d: HTTP status code */
                    __( 'HTTP error: %d', 'contentshield-ai' ),
                    $status_code
                ),
            );
        }

        $body = wp_remote_retrieve_body( $response );

        if ( empty( $body ) ) {
            return array(
                'success' => false,
                'error'   => __( 'Empty response from URL.', 'contentshield-ai' ),
            );
        }

        // Extract text content from HTML
        $content = $this->extract_text_from_html( $body );

        return array(
            'success' => true,
            'content' => $content,
        );
    }

    /**
     * Extract text content from HTML.
     *
     * @param string $html HTML content.
     * @return string Extracted text.
     */
    private function extract_text_from_html( $html ) {
        // Remove script and style tags
        $html = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $html );
        $html = preg_replace( '/<style\b[^>]*>(.*?)<\/style>/is', '', $html );

        // Remove HTML comments
        $html = preg_replace( '/<!--.*?-->/s', '', $html );

        // Try to extract main content areas
        $content_patterns = array(
            '/<article[^>]*>(.*?)<\/article>/is',
            '/<main[^>]*>(.*?)<\/main>/is',
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<div[^>]*class="[^"]*post[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<div[^>]*class="[^"]*entry[^"]*"[^>]*>(.*?)<\/div>/is',
        );

        foreach ( $content_patterns as $pattern ) {
            if ( preg_match( $pattern, $html, $matches ) ) {
                $html = $matches[1];
                break;
            }
        }

        // Strip remaining HTML tags
        $text = wp_strip_all_tags( $html );

        // Decode HTML entities
        $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

        // Normalize whitespace
        $text = preg_replace( '/\s+/', ' ', $text );

        return trim( $text );
    }

    /**
     * Clean content for comparison.
     *
     * @param string $content Content to clean.
     * @return string Cleaned content.
     */
    private function clean_content( $content ) {
        // Remove zero-width characters
        $content = $this->watermark->remove_watermark( $content );

        // Convert to lowercase
        $content = strtolower( $content );

        // Remove punctuation
        $content = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $content );

        // Normalize whitespace
        $content = preg_replace( '/\s+/', ' ', $content );

        return trim( $content );
    }

    /**
     * Generate temporary fingerprint for external content.
     *
     * @param string $content Content to fingerprint.
     * @return string Fingerprint.
     */
    private function generate_temp_fingerprint( $content ) {
        // Use the fingerprint class's SimHash algorithm
        // We need to access the private method, so we'll recreate it here

        $shingle_size = 3;
        $hash_bits    = 64;

        // Create shingles
        $words = preg_split( '/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY );

        if ( count( $words ) < $shingle_size ) {
            $shingles = array( implode( ' ', $words ) );
        } else {
            $shingles = array();
            for ( $i = 0; $i <= count( $words ) - $shingle_size; $i++ ) {
                $shingles[] = implode( ' ', array_slice( $words, $i, $shingle_size ) );
            }
            $shingles = array_unique( $shingles );
        }

        if ( empty( $shingles ) ) {
            return str_repeat( '0', $hash_bits / 4 );
        }

        // Initialize bit vector
        $vector = array_fill( 0, $hash_bits, 0 );

        // Hash each shingle
        foreach ( $shingles as $shingle ) {
            $hash_raw = hash( 'md5', $shingle, true );
            $hash     = unpack( 'q', substr( $hash_raw, 0, 8 ) )[1];

            for ( $i = 0; $i < $hash_bits; $i++ ) {
                $bit = ( $hash >> $i ) & 1;
                $vector[ $i ] += $bit ? 1 : -1;
            }
        }

        // Convert to fingerprint
        $fingerprint = 0;
        for ( $i = 0; $i < $hash_bits; $i++ ) {
            if ( $vector[ $i ] > 0 ) {
                $fingerprint |= ( 1 << $i );
            }
        }

        return sprintf( '%016x', $fingerprint );
    }

    /**
     * Compare external content against protected content.
     *
     * @param string   $external_fingerprint External content fingerprint.
     * @param string   $external_content     External content text.
     * @param int|null $post_id              Specific post ID to compare against.
     * @return array Comparison result.
     */
    private function compare_content( $external_fingerprint, $external_content, $post_id = null ) {
        global $wpdb;

        $fingerprints_table = $wpdb->prefix . 'contentshield_fingerprints';

        if ( $post_id ) {
            // Compare against specific post
            $fingerprint_data = $this->fingerprint->get( $post_id );

            if ( ! $fingerprint_data ) {
                return array(
                    'similarity'       => 0,
                    'matched_post_id'  => null,
                    'matched_content'  => null,
                );
            }

            $similarity = $this->fingerprint->compare( $external_fingerprint, $fingerprint_data->fingerprint );

            return array(
                'similarity'       => $similarity,
                'matched_post_id'  => $post_id,
                'matched_content'  => $this->extract_matched_excerpts( $external_content, $post_id ),
            );
        } else {
            // Compare against all protected content
            $all_fingerprints = $wpdb->get_results(
                "SELECT post_id, fingerprint FROM {$fingerprints_table}"
            );

            $best_match = array(
                'similarity'       => 0,
                'matched_post_id'  => null,
                'matched_content'  => null,
            );

            foreach ( $all_fingerprints as $fp ) {
                $similarity = $this->fingerprint->compare( $external_fingerprint, $fp->fingerprint );

                if ( $similarity > $best_match['similarity'] ) {
                    $best_match = array(
                        'similarity'       => $similarity,
                        'matched_post_id'  => $fp->post_id,
                        'matched_content'  => null, // Will be populated below
                    );
                }
            }

            // Get matched excerpts for best match
            if ( $best_match['matched_post_id'] && $best_match['similarity'] >= 30 ) {
                $best_match['matched_content'] = $this->extract_matched_excerpts(
                    $external_content,
                    $best_match['matched_post_id']
                );
            }

            return $best_match;
        }
    }

    /**
     * Extract matching excerpts between external content and post.
     *
     * @param string $external_content External content.
     * @param int    $post_id          Post ID.
     * @return string|null Matched excerpts or null.
     */
    private function extract_matched_excerpts( $external_content, $post_id ) {
        $post = get_post( $post_id );

        if ( ! $post ) {
            return null;
        }

        $post_content = $this->clean_content( wp_strip_all_tags( $post->post_content ) );

        // Find common phrases (5+ words)
        $external_words = explode( ' ', $external_content );
        $post_words     = explode( ' ', $post_content );

        $matches = array();
        $phrase_length = 5;

        for ( $i = 0; $i <= count( $external_words ) - $phrase_length; $i++ ) {
            $phrase = array_slice( $external_words, $i, $phrase_length );
            $phrase_str = implode( ' ', $phrase );

            if ( strpos( $post_content, $phrase_str ) !== false ) {
                // Try to extend the match
                $extended = $phrase;
                $j = $i + $phrase_length;

                while ( $j < count( $external_words ) ) {
                    $test_phrase = implode( ' ', array_merge( $extended, array( $external_words[ $j ] ) ) );
                    if ( strpos( $post_content, $test_phrase ) !== false ) {
                        $extended[] = $external_words[ $j ];
                        $j++;
                    } else {
                        break;
                    }
                }

                if ( count( $extended ) >= $phrase_length ) {
                    $matches[] = implode( ' ', $extended );
                }

                $i = $j - 1; // Skip matched portion
            }
        }

        // Remove duplicates and get unique matches
        $matches = array_unique( $matches );

        // Sort by length (longest first)
        usort( $matches, function( $a, $b ) {
            return strlen( $b ) - strlen( $a );
        } );

        // Return top 3 matches
        $top_matches = array_slice( $matches, 0, 3 );

        if ( empty( $top_matches ) ) {
            return null;
        }

        return wp_json_encode( $top_matches );
    }

    /**
     * Create scan record in database.
     *
     * @param string   $url     URL being scanned.
     * @param int|null $post_id Post ID being compared.
     * @return int Scan ID.
     */
    private function create_scan_record( $url, $post_id = null ) {
        global $wpdb;

        $wpdb->insert(
            $this->table_name,
            array(
                'post_id'     => $post_id,
                'scanned_url' => $url,
                'status'      => 'pending',
                'scan_type'   => 'manual',
                'created_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%s' )
        );

        return $wpdb->insert_id;
    }

    /**
     * Update scan record status.
     *
     * @param int         $scan_id         Scan ID.
     * @param string      $status          New status.
     * @param float|null  $similarity      Similarity score.
     * @param string|null $error           Error message.
     * @param string|null $matched_content Matched content excerpts.
     * @return void
     */
    private function update_scan_status( $scan_id, $status, $similarity = null, $error = null, $matched_content = null ) {
        global $wpdb;

        $data = array(
            'status'       => $status,
            'completed_at' => current_time( 'mysql' ),
        );

        $formats = array( '%s', '%s' );

        if ( null !== $similarity ) {
            $data['similarity_score'] = $similarity;
            $formats[] = '%f';
        }

        if ( null !== $error ) {
            $data['error_message'] = $error;
            $formats[] = '%s';
        }

        if ( null !== $matched_content ) {
            $data['matched_content'] = $matched_content;
            $formats[] = '%s';
        }

        $wpdb->update(
            $this->table_name,
            $data,
            array( 'id' => $scan_id ),
            $formats,
            array( '%d' )
        );
    }

    /**
     * Create alert for potential plagiarism.
     *
     * @param int        $scan_id         Scan ID.
     * @param int|null   $post_id         Post ID.
     * @param string     $source_url      Source URL.
     * @param float      $similarity      Similarity score.
     * @param bool       $watermark_found Whether watermark was found.
     * @return void
     */
    private function create_alert( $scan_id, $post_id, $source_url, $similarity, $watermark_found ) {
        global $wpdb;

        $alerts_table = $wpdb->prefix . 'contentshield_alerts';

        $severity = 'low';
        if ( $watermark_found || $similarity >= 80 ) {
            $severity = 'high';
        } elseif ( $similarity >= 60 ) {
            $severity = 'medium';
        }

        $alert_type = $watermark_found ? 'watermark_detected' : 'plagiarism_found';

        $wpdb->insert(
            $alerts_table,
            array(
                'post_id'    => $post_id ?: 0,
                'scan_id'    => $scan_id,
                'alert_type' => $alert_type,
                'severity'   => $severity,
                'source_url' => $source_url,
                'details'    => wp_json_encode( array(
                    'similarity'      => $similarity,
                    'watermark_found' => $watermark_found,
                ) ),
                'is_read'    => 0,
                'is_resolved' => 0,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
        );

        // Send email notification if enabled
        $this->maybe_send_notification( $post_id, $source_url, $similarity, $watermark_found );
    }

    /**
     * Maybe send email notification for alert.
     *
     * @param int|null $post_id         Post ID.
     * @param string   $source_url      Source URL.
     * @param float    $similarity      Similarity score.
     * @param bool     $watermark_found Whether watermark was found.
     * @return void
     */
    private function maybe_send_notification( $post_id, $source_url, $similarity, $watermark_found ) {
        if ( ! get_option( 'contentshield_email_notifications', true ) ) {
            return;
        }

        $email = get_option( 'contentshield_notification_email', get_option( 'admin_email' ) );

        if ( ! is_email( $email ) ) {
            return;
        }

        $post_title = $post_id ? get_the_title( $post_id ) : __( 'Unknown', 'contentshield-ai' );

        $subject = sprintf(
            /* translators: %s: site name */
            __( '[ContentShield] Potential plagiarism detected on %s', 'contentshield-ai' ),
            get_bloginfo( 'name' )
        );

        $message = sprintf(
            /* translators: 1: post title, 2: URL, 3: similarity percentage */
            __( "ContentShield AI has detected potential plagiarism:\n\nYour Content: %1\$s\nSuspicious URL: %2\$s\nSimilarity Score: %3\$.1f%%\nWatermark Found: %4\$s\n\nPlease review this match in your WordPress dashboard.\n\n%5\$s", 'contentshield-ai' ),
            $post_title,
            $source_url,
            $similarity,
            $watermark_found ? __( 'Yes', 'contentshield-ai' ) : __( 'No', 'contentshield-ai' ),
            admin_url( 'admin.php?page=contentshield-alerts' )
        );

        wp_mail( $email, $subject, $message );
    }

    /**
     * Check if two URLs are from the same domain.
     *
     * @param string $url1 First URL.
     * @param string $url2 Second URL.
     * @return bool
     */
    private function is_same_domain( $url1, $url2 ) {
        $host1 = wp_parse_url( $url1, PHP_URL_HOST );
        $host2 = wp_parse_url( $url2, PHP_URL_HOST );

        // Remove www prefix for comparison
        $host1 = preg_replace( '/^www\./', '', $host1 );
        $host2 = preg_replace( '/^www\./', '', $host2 );

        return strtolower( $host1 ) === strtolower( $host2 );
    }

    /**
     * AJAX handler for scanning URL.
     *
     * @return void
     */
    public function ajax_scan_url() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : null;

        if ( empty( $url ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a URL to scan.', 'contentshield-ai' ) ) );
        }

        // Check scan limit for free version
        if ( ! contentshield()->is_pro() ) {
            $limit = get_option( 'contentshield_scan_limit', 10 );
            $recent_scans = $this->get_recent_scan_count();

            if ( $recent_scans >= $limit ) {
                wp_send_json_error( array(
                    'message' => sprintf(
                        /* translators: %d: scan limit */
                        __( 'You have reached the scan limit (%d scans). Upgrade to Pro for unlimited scans.', 'contentshield-ai' ),
                        $limit
                    ),
                ) );
            }
        }

        $result = $this->scan( $url, $post_id ?: null );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Get recent scan count.
     *
     * @return int
     */
    private function get_recent_scan_count() {
        global $wpdb;

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
             WHERE scan_type = 'manual'
             AND created_at >= %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-24 hours' ) )
        ) );
    }

    /**
     * Get scan by ID.
     *
     * @param int $scan_id Scan ID.
     * @return object|null
     */
    public function get( $scan_id ) {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $scan_id
        ) );
    }
}
