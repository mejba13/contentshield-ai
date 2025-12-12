<?php
/**
 * Content fingerprinting using SimHash algorithm.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Fingerprint
 *
 * Generates and manages content fingerprints using SimHash algorithm.
 */
class ContentShield_Fingerprint {

    /**
     * Hash bit size.
     *
     * @var int
     */
    const HASH_BITS = 64;

    /**
     * Shingle size (n-gram).
     *
     * @var int
     */
    const SHINGLE_SIZE = 3;

    /**
     * Database table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'contentshield_fingerprints';
    }

    /**
     * Generate fingerprint for a post.
     *
     * @param int    $post_id Post ID.
     * @param string $content Optional content override.
     * @return array|false Fingerprint data or false on failure.
     */
    public function generate( $post_id, $content = null ) {
        global $wpdb;

        // Get post content if not provided
        if ( null === $content ) {
            $post = get_post( $post_id );
            if ( ! $post ) {
                return false;
            }
            $content = $post->post_content;
        }

        // Clean and normalize content
        $clean_content = $this->normalize_content( $content );

        // Check minimum word count
        $word_count = str_word_count( $clean_content );
        $min_words  = get_option( 'contentshield_min_word_count', 100 );

        if ( $word_count < $min_words ) {
            return false;
        }

        // Generate SimHash fingerprint
        $fingerprint = $this->simhash( $clean_content );

        // Generate content hash for quick exact matching
        $content_hash = hash( 'sha256', $clean_content );

        // Check if fingerprint already exists
        $existing = $this->get( $post_id );

        $data = array(
            'post_id'      => $post_id,
            'fingerprint'  => $fingerprint,
            'content_hash' => $content_hash,
            'word_count'   => $word_count,
            'updated_at'   => current_time( 'mysql' ),
        );

        if ( $existing ) {
            // Update existing fingerprint
            $wpdb->update(
                $this->table_name,
                $data,
                array( 'post_id' => $post_id ),
                array( '%d', '%s', '%s', '%d', '%s' ),
                array( '%d' )
            );
        } else {
            // Insert new fingerprint
            $data['created_at'] = current_time( 'mysql' );
            $wpdb->insert(
                $this->table_name,
                $data,
                array( '%d', '%s', '%s', '%d', '%s', '%s' )
            );
        }

        // Log the action
        $this->log_fingerprint_action( $post_id, $existing ? 'updated' : 'created' );

        return $data;
    }

    /**
     * Get fingerprint for a post.
     *
     * @param int $post_id Post ID.
     * @return object|null Fingerprint data or null.
     */
    public function get( $post_id ) {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE post_id = %d",
            $post_id
        ) );
    }

    /**
     * Delete fingerprint for a post.
     *
     * @param int $post_id Post ID.
     * @return bool
     */
    public function delete( $post_id ) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            array( 'post_id' => $post_id ),
            array( '%d' )
        );

        if ( $result ) {
            $this->log_fingerprint_action( $post_id, 'deleted' );
        }

        return (bool) $result;
    }

    /**
     * Compare two fingerprints and return similarity percentage.
     *
     * @param string $fingerprint1 First fingerprint.
     * @param string $fingerprint2 Second fingerprint.
     * @return float Similarity percentage (0-100).
     */
    public function compare( $fingerprint1, $fingerprint2 ) {
        // Convert hex fingerprints to binary strings
        $bin1 = $this->hex_to_binary( $fingerprint1 );
        $bin2 = $this->hex_to_binary( $fingerprint2 );

        // Calculate Hamming distance
        $hamming_distance = 0;
        $length = min( strlen( $bin1 ), strlen( $bin2 ) );

        for ( $i = 0; $i < $length; $i++ ) {
            if ( $bin1[ $i ] !== $bin2[ $i ] ) {
                $hamming_distance++;
            }
        }

        // Convert to similarity percentage
        $similarity = ( 1 - ( $hamming_distance / self::HASH_BITS ) ) * 100;

        return max( 0, min( 100, $similarity ) );
    }

    /**
     * Find similar content in database.
     *
     * @param string $fingerprint Fingerprint to compare.
     * @param int    $threshold   Minimum similarity threshold (default 70%).
     * @param int    $exclude_id  Post ID to exclude from results.
     * @return array Array of similar posts with similarity scores.
     */
    public function find_similar( $fingerprint, $threshold = 70, $exclude_id = 0 ) {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE post_id != %d",
            $exclude_id
        ) );

        $similar = array();

        foreach ( $results as $result ) {
            $similarity = $this->compare( $fingerprint, $result->fingerprint );

            if ( $similarity >= $threshold ) {
                $similar[] = array(
                    'post_id'    => $result->post_id,
                    'fingerprint' => $result->fingerprint,
                    'similarity'  => $similarity,
                );
            }
        }

        // Sort by similarity descending
        usort( $similar, function( $a, $b ) {
            return $b['similarity'] <=> $a['similarity'];
        } );

        return $similar;
    }

    /**
     * Generate SimHash fingerprint for text.
     *
     * @param string $text Text to fingerprint.
     * @return string Hexadecimal fingerprint.
     */
    private function simhash( $text ) {
        // Create shingles (n-grams)
        $shingles = $this->create_shingles( $text );

        if ( empty( $shingles ) ) {
            return str_repeat( '0', self::HASH_BITS / 4 );
        }

        // Initialize bit vector
        $vector = array_fill( 0, self::HASH_BITS, 0 );

        // Hash each shingle and update vector
        foreach ( $shingles as $shingle ) {
            $hash = $this->hash_shingle( $shingle );

            for ( $i = 0; $i < self::HASH_BITS; $i++ ) {
                $bit = ( $hash >> $i ) & 1;
                $vector[ $i ] += $bit ? 1 : -1;
            }
        }

        // Convert vector to fingerprint
        $fingerprint = 0;
        for ( $i = 0; $i < self::HASH_BITS; $i++ ) {
            if ( $vector[ $i ] > 0 ) {
                $fingerprint |= ( 1 << $i );
            }
        }

        // Convert to hex string
        return sprintf( '%016x', $fingerprint );
    }

    /**
     * Create shingles (n-grams) from text.
     *
     * @param string $text Text to shingle.
     * @return array Array of shingles.
     */
    private function create_shingles( $text ) {
        $words = preg_split( '/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY );

        if ( count( $words ) < self::SHINGLE_SIZE ) {
            return array( implode( ' ', $words ) );
        }

        $shingles = array();
        for ( $i = 0; $i <= count( $words ) - self::SHINGLE_SIZE; $i++ ) {
            $shingles[] = implode( ' ', array_slice( $words, $i, self::SHINGLE_SIZE ) );
        }

        return array_unique( $shingles );
    }

    /**
     * Hash a shingle to a 64-bit integer.
     *
     * @param string $shingle Shingle to hash.
     * @return int 64-bit hash value.
     */
    private function hash_shingle( $shingle ) {
        $hash = hash( 'md5', $shingle, true );
        return unpack( 'q', substr( $hash, 0, 8 ) )[1];
    }

    /**
     * Convert hexadecimal string to binary string.
     *
     * @param string $hex Hexadecimal string.
     * @return string Binary string.
     */
    private function hex_to_binary( $hex ) {
        $binary = '';
        for ( $i = 0; $i < strlen( $hex ); $i++ ) {
            $binary .= str_pad( base_convert( $hex[ $i ], 16, 2 ), 4, '0', STR_PAD_LEFT );
        }
        return $binary;
    }

    /**
     * Normalize content for fingerprinting.
     *
     * @param string $content Content to normalize.
     * @return string Normalized content.
     */
    private function normalize_content( $content ) {
        // Strip HTML tags
        $content = wp_strip_all_tags( $content );

        // Decode HTML entities
        $content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

        // Convert to lowercase
        $content = strtolower( $content );

        // Remove extra whitespace
        $content = preg_replace( '/\s+/', ' ', $content );

        // Remove punctuation (keep letters, numbers, spaces)
        $content = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $content );

        return trim( $content );
    }

    /**
     * Check for fingerprint changes in existing posts.
     *
     * @return void
     */
    public function check_for_changes() {
        global $wpdb;

        // Get all fingerprints
        $fingerprints = $wpdb->get_results(
            "SELECT post_id, content_hash FROM {$this->table_name}"
        );

        foreach ( $fingerprints as $fp ) {
            $post = get_post( $fp->post_id );

            if ( ! $post ) {
                // Post deleted, remove fingerprint
                $this->delete( $fp->post_id );
                continue;
            }

            // Check if content has changed
            $clean_content = $this->normalize_content( $post->post_content );
            $current_hash  = hash( 'sha256', $clean_content );

            if ( $current_hash !== $fp->content_hash ) {
                // Content changed, regenerate fingerprint
                $this->generate( $fp->post_id );
            }
        }
    }

    /**
     * Log fingerprint action.
     *
     * @param int    $post_id Post ID.
     * @param string $action  Action performed.
     * @return void
     */
    private function log_fingerprint_action( $post_id, $action ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_logs';

        $wpdb->insert(
            $table,
            array(
                'event_type' => 'fingerprint_' . $action,
                'post_id'    => $post_id,
                'details'    => wp_json_encode( array(
                    'action'    => $action,
                    'timestamp' => current_time( 'mysql' ),
                ) ),
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%d', '%s', '%s' )
        );
    }

    /**
     * AJAX handler for generating fingerprint.
     *
     * @return void
     */
    public function ajax_generate_fingerprint() {
        check_ajax_referer( 'contentshield_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_contentshield' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentshield-ai' ) ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid post ID.', 'contentshield-ai' ) ) );
        }

        $result = $this->generate( $post_id );

        if ( $result ) {
            wp_send_json_success( array(
                'message'     => __( 'Fingerprint generated successfully.', 'contentshield-ai' ),
                'fingerprint' => $result['fingerprint'],
                'word_count'  => $result['word_count'],
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to generate fingerprint. Content may be too short.', 'contentshield-ai' ),
            ) );
        }
    }

    /**
     * Export fingerprints to CSV.
     *
     * @return string CSV content.
     */
    public function export_csv() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT f.*, p.post_title
             FROM {$this->table_name} f
             LEFT JOIN {$wpdb->posts} p ON f.post_id = p.ID
             ORDER BY f.created_at DESC"
        );

        $csv = "Post ID,Title,Fingerprint,Word Count,Created,Updated\n";

        foreach ( $results as $row ) {
            $csv .= sprintf(
                '"%d","%s","%s","%d","%s","%s"' . "\n",
                $row->post_id,
                str_replace( '"', '""', $row->post_title ?? '' ),
                $row->fingerprint,
                $row->word_count,
                $row->created_at,
                $row->updated_at
            );
        }

        return $csv;
    }
}
