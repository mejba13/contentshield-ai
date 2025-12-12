<?php
/**
 * Content protection utilities.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Protection
 *
 * Provides various content protection utilities.
 */
class ContentShield_Protection {

    /**
     * Check if a post is protected.
     *
     * @param int $post_id Post ID.
     * @return bool
     */
    public static function is_protected( $post_id ) {
        global $wpdb;

        // Check if protection is disabled for this post
        if ( get_post_meta( $post_id, '_contentshield_disabled', true ) ) {
            return false;
        }

        // Check if post type is protected
        $post_type = get_post_type( $post_id );
        $protected_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );

        if ( ! in_array( $post_type, $protected_types, true ) ) {
            return false;
        }

        // Check if fingerprint exists
        $table = $wpdb->prefix . 'contentshield_fingerprints';
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE post_id = %d",
            $post_id
        ) );

        return (bool) $exists;
    }

    /**
     * Get protection status for a post.
     *
     * @param int $post_id Post ID.
     * @return array Protection status details.
     */
    public static function get_status( $post_id ) {
        global $wpdb;

        $table = $wpdb->prefix . 'contentshield_fingerprints';
        $fingerprint = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE post_id = %d",
            $post_id
        ) );

        $disabled = (bool) get_post_meta( $post_id, '_contentshield_disabled', true );

        return array(
            'post_id'          => $post_id,
            'is_protected'     => ! $disabled && $fingerprint,
            'is_disabled'      => $disabled,
            'has_fingerprint'  => (bool) $fingerprint,
            'fingerprint'      => $fingerprint ? $fingerprint->fingerprint : null,
            'word_count'       => $fingerprint ? $fingerprint->word_count : 0,
            'protected_at'     => $fingerprint ? $fingerprint->created_at : null,
            'updated_at'       => $fingerprint ? $fingerprint->updated_at : null,
        );
    }

    /**
     * Enable protection for a post.
     *
     * @param int $post_id Post ID.
     * @return bool
     */
    public static function enable( $post_id ) {
        delete_post_meta( $post_id, '_contentshield_disabled' );

        // Generate fingerprint if it doesn't exist
        $fingerprint = new ContentShield_Fingerprint();
        if ( ! $fingerprint->get( $post_id ) ) {
            $fingerprint->generate( $post_id );
        }

        return true;
    }

    /**
     * Disable protection for a post.
     *
     * @param int $post_id Post ID.
     * @return bool
     */
    public static function disable( $post_id ) {
        return (bool) update_post_meta( $post_id, '_contentshield_disabled', '1' );
    }

    /**
     * Bulk enable protection for posts.
     *
     * @param array $post_ids Array of post IDs.
     * @return int Number of posts protected.
     */
    public static function bulk_enable( $post_ids ) {
        $count = 0;

        foreach ( $post_ids as $post_id ) {
            if ( self::enable( absint( $post_id ) ) ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Bulk disable protection for posts.
     *
     * @param array $post_ids Array of post IDs.
     * @return int Number of posts unprotected.
     */
    public static function bulk_disable( $post_ids ) {
        $count = 0;

        foreach ( $post_ids as $post_id ) {
            if ( self::disable( absint( $post_id ) ) ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get all protected posts.
     *
     * @param array $args Query arguments.
     * @return array Array of protected posts.
     */
    public static function get_protected_posts( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'limit'    => 50,
            'offset'   => 0,
            'orderby'  => 'created_at',
            'order'    => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $table = $wpdb->prefix . 'contentshield_fingerprints';

        $orderby = in_array( $args['orderby'], array( 'created_at', 'updated_at', 'word_count' ), true )
            ? $args['orderby']
            : 'created_at';

        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT f.*, p.post_title, p.post_type, p.post_status
             FROM {$table} f
             LEFT JOIN {$wpdb->posts} p ON f.post_id = p.ID
             ORDER BY f.{$orderby} {$order}
             LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        ) );
    }

    /**
     * Get protection statistics.
     *
     * @return array Statistics array.
     */
    public static function get_statistics() {
        global $wpdb;

        $fingerprints_table = $wpdb->prefix . 'contentshield_fingerprints';
        $scans_table        = $wpdb->prefix . 'contentshield_scans';
        $alerts_table       = $wpdb->prefix . 'contentshield_alerts';

        // Protected posts count
        $protected_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$fingerprints_table}"
        );

        // Total word count of protected content
        $total_words = (int) $wpdb->get_var(
            "SELECT SUM(word_count) FROM {$fingerprints_table}"
        );

        // Scans this month
        $scans_this_month = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$scans_table}
             WHERE created_at >= %s",
            gmdate( 'Y-m-01 00:00:00' )
        ) );

        // Potential matches found
        $matches_found = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$scans_table}
             WHERE similarity_score >= %f",
            50.0
        ) );

        // Unresolved alerts
        $unresolved_alerts = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$alerts_table}
             WHERE is_resolved = %d",
            0
        ) );

        // High severity alerts
        $high_severity_alerts = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$alerts_table}
             WHERE severity = %s AND is_resolved = %d",
            'high',
            0
        ) );

        return array(
            'protected_posts'      => $protected_count,
            'total_words'          => $total_words,
            'scans_this_month'     => $scans_this_month,
            'matches_found'        => $matches_found,
            'unresolved_alerts'    => $unresolved_alerts,
            'high_severity_alerts' => $high_severity_alerts,
        );
    }

    /**
     * Get unprotected posts that meet criteria.
     *
     * @param int $limit Number of posts to return.
     * @return array Array of unprotected posts.
     */
    public static function get_unprotected_posts( $limit = 20 ) {
        global $wpdb;

        $fingerprints_table = $wpdb->prefix . 'contentshield_fingerprints';
        $protected_types = get_option( 'contentshield_post_types', array( 'post', 'page' ) );
        $min_words = get_option( 'contentshield_min_word_count', 100 );

        if ( empty( $protected_types ) ) {
            return array();
        }

        $type_placeholders = implode( ',', array_fill( 0, count( $protected_types ), '%s' ) );

        $query = $wpdb->prepare(
            "SELECT p.ID, p.post_title, p.post_type, p.post_date
             FROM {$wpdb->posts} p
             LEFT JOIN {$fingerprints_table} f ON p.ID = f.post_id
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_contentshield_disabled'
             WHERE p.post_type IN ({$type_placeholders})
             AND p.post_status = 'publish'
             AND f.post_id IS NULL
             AND (pm.meta_value IS NULL OR pm.meta_value != '1')
             ORDER BY p.post_date DESC
             LIMIT %d",
            array_merge( $protected_types, array( $limit ) )
        );

        return $wpdb->get_results( $query );
    }

    /**
     * Generate DMCA notice template.
     *
     * @param int    $post_id    Post ID.
     * @param string $infringing_url URL of infringing content.
     * @return string DMCA notice text.
     */
    public static function generate_dmca_notice( $post_id, $infringing_url ) {
        $post = get_post( $post_id );

        if ( ! $post ) {
            return '';
        }

        $site_name = get_bloginfo( 'name' );
        $site_url  = home_url();
        $post_url  = get_permalink( $post_id );
        $admin_email = get_option( 'admin_email' );

        $template = __(
            "DMCA TAKEDOWN NOTICE

To Whom It May Concern:

I am writing to report copyright infringement of content owned by me and published on my website.

ORIGINAL CONTENT:
Title: %1\$s
URL: %2\$s
Published on: %3\$s
Website: %4\$s

INFRINGING CONTENT:
URL: %5\$s

I have a good faith belief that use of the copyrighted materials described above on the allegedly infringing web page is not authorized by the copyright owner, its agent, or the law.

I swear, under penalty of perjury, that the information in this notification is accurate and that I am the copyright owner or am authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.

I request that you immediately remove or disable access to the infringing material.

Signature: [YOUR SIGNATURE]
Name: [YOUR NAME]
Title/Position: [YOUR TITLE]
Company: %4\$s
Email: %6\$s
Date: %7\$s

This notice is sent pursuant to the Digital Millennium Copyright Act (17 U.S.C. § 512).",
            'contentshield-ai'
        );

        return sprintf(
            $template,
            $post->post_title,
            $post_url,
            get_the_date( get_option( 'date_format' ), $post ),
            $site_name,
            $infringing_url,
            $admin_email,
            current_time( get_option( 'date_format' ) )
        );
    }
}
