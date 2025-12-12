<?php
/**
 * Invisible watermarking using zero-width characters.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Watermark
 *
 * Implements invisible text watermarking using zero-width Unicode characters.
 */
class ContentShield_Watermark {

    /**
     * Zero-width space character.
     *
     * @var string
     */
    const ZWS = "\u{200B}";

    /**
     * Zero-width non-joiner character.
     *
     * @var string
     */
    const ZWNJ = "\u{200C}";

    /**
     * Zero-width joiner character.
     *
     * @var string
     */
    const ZWJ = "\u{200D}";

    /**
     * Word joiner character.
     *
     * @var string
     */
    const WJ = "\u{2060}";

    /**
     * Watermark pattern prefix.
     *
     * @var string
     */
    const WATERMARK_PREFIX = 'CS';

    /**
     * Apply watermark to content.
     *
     * @param string $content Post content.
     * @return string Content with watermark.
     */
    public function apply_watermark( $content ) {
        // Check if watermarking is enabled
        if ( ! get_option( 'contentshield_watermark_enabled', true ) ) {
            return $content;
        }

        // Don't watermark in admin or REST requests
        if ( is_admin() || defined( 'REST_REQUEST' ) ) {
            return $content;
        }

        // Don't watermark feeds (RSS protection is handled separately)
        if ( is_feed() ) {
            return $content;
        }

        // Get current post
        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return $content;
        }

        // Check if protection is disabled for this post
        if ( get_post_meta( $post_id, '_contentshield_disabled', true ) ) {
            return $content;
        }

        // Check if post type is protected
        $protected_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );
        if ( ! in_array( get_post_type( $post_id ), $protected_types, true ) ) {
            return $content;
        }

        // Generate watermark payload
        $watermark_data = $this->generate_watermark_data( $post_id );

        // Encode watermark using zero-width characters
        $encoded_watermark = $this->encode( $watermark_data );

        // Apply watermark based on position setting
        $position = get_option( 'contentshield_watermark_position', 'distributed' );

        switch ( $position ) {
            case 'start':
                $content = $encoded_watermark . $content;
                break;
            case 'end':
                $content = $content . $encoded_watermark;
                break;
            case 'distributed':
            default:
                $content = $this->distribute_watermark( $content, $encoded_watermark );
                break;
        }

        return $content;
    }

    /**
     * Generate watermark data payload.
     *
     * @param int $post_id Post ID.
     * @return string Watermark data string.
     */
    private function generate_watermark_data( $post_id ) {
        // Create unique identifier
        $site_hash = substr( hash( 'sha256', home_url() ), 0, 8 );
        $post_hash = substr( hash( 'sha256', $post_id . wp_salt() ), 0, 8 );
        $timestamp = base_convert( time(), 10, 36 );

        // Format: CS|site_hash|post_hash|timestamp
        return self::WATERMARK_PREFIX . '|' . $site_hash . '|' . $post_hash . '|' . $timestamp;
    }

    /**
     * Encode string to zero-width characters.
     *
     * @param string $data Data to encode.
     * @return string Encoded zero-width string.
     */
    public function encode( $data ) {
        // Convert to binary representation
        $binary = '';
        for ( $i = 0; $i < strlen( $data ); $i++ ) {
            $binary .= str_pad( decbin( ord( $data[ $i ] ) ), 8, '0', STR_PAD_LEFT );
        }

        // Map bits to zero-width characters
        $encoded = '';
        for ( $i = 0; $i < strlen( $binary ); $i += 2 ) {
            $bits = substr( $binary, $i, 2 );

            switch ( $bits ) {
                case '00':
                    $encoded .= self::ZWS;
                    break;
                case '01':
                    $encoded .= self::ZWNJ;
                    break;
                case '10':
                    $encoded .= self::ZWJ;
                    break;
                case '11':
                    $encoded .= self::WJ;
                    break;
            }
        }

        return $encoded;
    }

    /**
     * Decode zero-width characters to string.
     *
     * @param string $encoded Encoded zero-width string.
     * @return string|false Decoded data or false on failure.
     */
    public function decode( $encoded ) {
        // Extract zero-width characters
        $zwc_pattern = '/[' . preg_quote( self::ZWS . self::ZWNJ . self::ZWJ . self::WJ, '/' ) . ']+/u';

        if ( ! preg_match( $zwc_pattern, $encoded, $matches ) ) {
            return false;
        }

        $zwc_string = $matches[0];

        // Map zero-width characters back to bits
        $binary = '';
        $chars = preg_split( '//u', $zwc_string, -1, PREG_SPLIT_NO_EMPTY );

        foreach ( $chars as $char ) {
            switch ( $char ) {
                case self::ZWS:
                    $binary .= '00';
                    break;
                case self::ZWNJ:
                    $binary .= '01';
                    break;
                case self::ZWJ:
                    $binary .= '10';
                    break;
                case self::WJ:
                    $binary .= '11';
                    break;
            }
        }

        // Convert binary back to string
        $decoded = '';
        for ( $i = 0; $i < strlen( $binary ); $i += 8 ) {
            $byte = substr( $binary, $i, 8 );
            if ( strlen( $byte ) === 8 ) {
                $decoded .= chr( bindec( $byte ) );
            }
        }

        // Verify watermark prefix
        if ( strpos( $decoded, self::WATERMARK_PREFIX ) !== 0 ) {
            return false;
        }

        return $decoded;
    }

    /**
     * Distribute watermark throughout content.
     *
     * @param string $content          Original content.
     * @param string $encoded_watermark Encoded watermark.
     * @return string Content with distributed watermark.
     */
    private function distribute_watermark( $content, $encoded_watermark ) {
        // Split watermark into segments
        $watermark_chars = preg_split( '//u', $encoded_watermark, -1, PREG_SPLIT_NO_EMPTY );
        $segment_count   = count( $watermark_chars );

        if ( $segment_count === 0 ) {
            return $content;
        }

        // Find suitable insertion points (after words, before spaces)
        $pattern = '/(\s+)/u';
        $parts   = preg_split( $pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

        if ( empty( $parts ) ) {
            return $encoded_watermark . $content;
        }

        // Calculate insertion interval
        $insertable_count = 0;
        foreach ( $parts as $part ) {
            if ( ! ctype_space( $part ) ) {
                $insertable_count++;
            }
        }

        if ( $insertable_count === 0 ) {
            return $encoded_watermark . $content;
        }

        $interval = max( 1, intval( $insertable_count / $segment_count ) );

        // Insert watermark segments
        $result       = '';
        $word_count   = 0;
        $segment_idx  = 0;

        foreach ( $parts as $part ) {
            $result .= $part;

            if ( ! ctype_space( $part ) ) {
                $word_count++;

                if ( $segment_idx < $segment_count && $word_count % $interval === 0 ) {
                    $result .= $watermark_chars[ $segment_idx ];
                    $segment_idx++;
                }
            }
        }

        // Append any remaining segments at the end
        while ( $segment_idx < $segment_count ) {
            $result .= $watermark_chars[ $segment_idx ];
            $segment_idx++;
        }

        return $result;
    }

    /**
     * Extract watermark from text.
     *
     * @param string $text Text potentially containing watermark.
     * @return array|false Watermark data array or false if not found.
     */
    public function extract( $text ) {
        $decoded = $this->decode( $text );

        if ( ! $decoded ) {
            return false;
        }

        // Parse watermark data
        $parts = explode( '|', $decoded );

        if ( count( $parts ) !== 4 ) {
            return false;
        }

        return array(
            'prefix'     => $parts[0],
            'site_hash'  => $parts[1],
            'post_hash'  => $parts[2],
            'timestamp'  => base_convert( $parts[3], 36, 10 ),
            'raw'        => $decoded,
        );
    }

    /**
     * Verify if text contains our watermark.
     *
     * @param string $text Text to verify.
     * @return bool True if watermark found and valid.
     */
    public function verify( $text ) {
        $watermark = $this->extract( $text );

        if ( ! $watermark ) {
            return false;
        }

        // Verify site hash matches
        $expected_site_hash = substr( hash( 'sha256', home_url() ), 0, 8 );

        return $watermark['site_hash'] === $expected_site_hash;
    }

    /**
     * Check if text contains any zero-width characters.
     *
     * @param string $text Text to check.
     * @return bool True if zero-width characters found.
     */
    public function has_zero_width_chars( $text ) {
        $pattern = '/[' . preg_quote( self::ZWS . self::ZWNJ . self::ZWJ . self::WJ, '/' ) . ']/u';
        return (bool) preg_match( $pattern, $text );
    }

    /**
     * Remove all zero-width characters from text.
     *
     * @param string $text Text to clean.
     * @return string Cleaned text.
     */
    public function remove_watermark( $text ) {
        $pattern = '/[' . preg_quote( self::ZWS . self::ZWNJ . self::ZWJ . self::WJ, '/' ) . ']/u';
        return preg_replace( $pattern, '', $text );
    }

    /**
     * Generate watermark ID for a post.
     *
     * @param int $post_id Post ID.
     * @return string Watermark ID.
     */
    public function generate_watermark_id( $post_id ) {
        return hash( 'sha256', $post_id . home_url() . wp_salt() );
    }

    /**
     * Get watermark statistics.
     *
     * @return array Statistics array.
     */
    public function get_stats() {
        $protected_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );

        $total_posts = 0;
        foreach ( $protected_types as $post_type ) {
            $total_posts += wp_count_posts( $post_type )->publish;
        }

        $disabled_posts = 0;
        $posts_with_meta = get_posts( array(
            'post_type'      => $protected_types,
            'posts_per_page' => -1,
            'meta_key'       => '_contentshield_disabled',
            'meta_value'     => '1',
            'fields'         => 'ids',
        ) );
        $disabled_posts = count( $posts_with_meta );

        return array(
            'total_eligible'  => $total_posts,
            'protected'       => $total_posts - $disabled_posts,
            'disabled'        => $disabled_posts,
            'watermark_enabled' => get_option( 'contentshield_watermark_enabled', true ),
        );
    }
}
