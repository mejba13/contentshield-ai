<?php
/**
 * Scanner admin page template.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="cs-wrap">
    <!-- Page Header -->
    <div class="cs-header">
        <div class="cs-header-content">
            <div class="cs-header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <div>
                <h1><?php esc_html_e( 'Plagiarism Scanner', 'contentshield-ai' ); ?></h1>
                <p class="cs-header-subtitle"><?php esc_html_e( 'Check any URL for potential content plagiarism', 'contentshield-ai' ); ?></p>
            </div>
        </div>
    </div>

    <!-- Scanner Form Card -->
    <div class="cs-card cs-scanner-card" data-animate="fade-up" style="--delay: 0.1s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                <?php esc_html_e( 'Scan URL for Plagiarism', 'contentshield-ai' ); ?>
            </h2>
        </div>
        <div class="cs-card-body">
            <p class="cs-text-muted cs-mb-4">
                <?php esc_html_e( 'Enter a URL to check if it contains content copied from your site. We\'ll compare it against your protected content fingerprints.', 'contentshield-ai' ); ?>
            </p>

            <form id="contentshield-scan-form" method="post" class="cs-form">
                <?php wp_nonce_field( 'contentshield_scan_url', 'contentshield_scan_nonce' ); ?>

                <div class="cs-form-group">
                    <label for="scan_url" class="cs-label">
                        <?php esc_html_e( 'URL to Scan', 'contentshield-ai' ); ?>
                        <span class="cs-required">*</span>
                    </label>
                    <div class="cs-input-group">
                        <span class="cs-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                        </span>
                        <input
                            type="url"
                            id="scan_url"
                            name="scan_url"
                            class="cs-input cs-input-lg"
                            placeholder="https://example.com/suspicious-page"
                            required
                        >
                    </div>
                    <p class="cs-input-hint"><?php esc_html_e( 'Enter the full URL including https://', 'contentshield-ai' ); ?></p>
                </div>

                <div class="cs-form-group">
                    <label for="compare_post" class="cs-label"><?php esc_html_e( 'Compare Against', 'contentshield-ai' ); ?></label>
                    <div class="cs-select-wrapper">
                        <select id="compare_post" name="compare_post" class="cs-select">
                            <option value=""><?php esc_html_e( 'All protected content', 'contentshield-ai' ); ?></option>
                            <?php
                            $protected_posts = get_posts( array(
                                'post_type'      => get_option( 'contentshield_post_types', array( 'post', 'page' ) ),
                                'posts_per_page' => 100,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                                'meta_query'     => array(
                                    array(
                                        'key'     => '_contentshield_disabled',
                                        'compare' => 'NOT EXISTS',
                                    ),
                                ),
                            ) );

                            foreach ( $protected_posts as $post ) :
                                ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>">
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="cs-select-arrow">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </span>
                    </div>
                    <p class="cs-input-hint"><?php esc_html_e( 'Optionally select a specific post to compare against', 'contentshield-ai' ); ?></p>
                </div>

                <div class="cs-form-actions">
                    <button type="submit" class="cs-btn cs-btn-primary cs-btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <?php esc_html_e( 'Start Scan', 'contentshield-ai' ); ?>
                    </button>
                    <span class="cs-scan-status"></span>
                </div>
            </form>

            <!-- Scan Results -->
            <div id="contentshield-scan-results" class="cs-scan-results" style="display: none;">
                <div class="cs-results-header">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                        <?php esc_html_e( 'Scan Results', 'contentshield-ai' ); ?>
                    </h3>
                </div>
                <div class="cs-results-content"></div>
            </div>
        </div>
    </div>

    <!-- Recent Scans Card -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.2s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <?php esc_html_e( 'Recent Scans', 'contentshield-ai' ); ?>
            </h2>
            <span class="cs-badge cs-badge-info"><?php echo esc_html( count( $recent_scans ) ); ?> <?php esc_html_e( 'scans', 'contentshield-ai' ); ?></span>
        </div>
        <div class="cs-card-body cs-card-body-flush">
            <?php if ( ! empty( $recent_scans ) ) : ?>
                <div class="cs-table-responsive">
                    <table class="cs-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Scanned URL', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Compared To', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Similarity', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Type', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Date', 'contentshield-ai' ); ?></th>
                                <th class="cs-text-right"><?php esc_html_e( 'Actions', 'contentshield-ai' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $recent_scans as $scan ) : ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo esc_url( $scan->scanned_url ); ?>" target="_blank" rel="noopener" class="cs-link-external">
                                            <?php echo esc_html( wp_trim_words( $scan->scanned_url, 6, '...' ) ); ?>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                            </svg>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ( $scan->post_id && $scan->post_title ) : ?>
                                            <a href="<?php echo esc_url( get_permalink( $scan->post_id ) ); ?>" class="cs-link">
                                                <?php echo esc_html( wp_trim_words( $scan->post_title, 3, '...' ) ); ?>
                                            </a>
                                        <?php else : ?>
                                            <span class="cs-text-muted"><?php esc_html_e( 'All content', 'contentshield-ai' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ( null !== $scan->similarity_score ) : ?>
                                            <?php
                                            $score_class = 'cs-badge-success';
                                            if ( $scan->similarity_score >= 70 ) {
                                                $score_class = 'cs-badge-danger';
                                            } elseif ( $scan->similarity_score >= 40 ) {
                                                $score_class = 'cs-badge-warning';
                                            }
                                            ?>
                                            <span class="cs-badge <?php echo esc_attr( $score_class ); ?>">
                                                <?php echo esc_html( number_format( $scan->similarity_score, 1 ) ); ?>%
                                            </span>
                                        <?php else : ?>
                                            <span class="cs-text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = 'cs-status-default';
                                        if ( 'completed' === $scan->status ) {
                                            $status_class = 'cs-status-success';
                                        } elseif ( 'pending' === $scan->status ) {
                                            $status_class = 'cs-status-warning';
                                        } elseif ( 'failed' === $scan->status ) {
                                            $status_class = 'cs-status-danger';
                                        }
                                        ?>
                                        <span class="cs-status <?php echo esc_attr( $status_class ); ?>">
                                            <?php echo esc_html( ucfirst( $scan->status ) ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cs-badge cs-badge-muted"><?php echo esc_html( ucfirst( $scan->scan_type ) ); ?></span>
                                    </td>
                                    <td class="cs-text-muted cs-text-nowrap">
                                        <?php echo esc_html( human_time_diff( strtotime( $scan->created_at ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'contentshield-ai' ); ?>
                                    </td>
                                    <td class="cs-text-right">
                                        <div class="cs-btn-group">
                                            <?php if ( 'completed' === $scan->status && $scan->similarity_score >= 50 ) : ?>
                                                <button type="button" class="cs-btn cs-btn-sm cs-btn-outline contentshield-view-details" data-scan-id="<?php echo esc_attr( $scan->id ); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    <?php esc_html_e( 'Details', 'contentshield-ai' ); ?>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="cs-btn cs-btn-sm cs-btn-ghost contentshield-rescan" data-url="<?php echo esc_attr( $scan->scanned_url ); ?>">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                                </svg>
                                                <?php esc_html_e( 'Rescan', 'contentshield-ai' ); ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="cs-empty-state">
                    <div class="cs-empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e( 'No scans yet', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Use the form above to check a URL for potential plagiarism of your content.', 'contentshield-ai' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( ! contentshield()->is_pro() ) : ?>
    <!-- Pro Feature Promo -->
    <div class="cs-card cs-card-gradient" data-animate="fade-up" style="--delay: 0.3s">
        <div class="cs-card-body">
            <div class="cs-pro-promo cs-pro-promo-horizontal">
                <div class="cs-pro-content">
                    <div class="cs-pro-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php esc_html_e( 'PRO', 'contentshield-ai' ); ?>
                    </div>
                    <h3><?php esc_html_e( 'Automated Monitoring', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Let us automatically scan the web for your content. No more manual checking!', 'contentshield-ai' ); ?></p>
                    <ul class="cs-pro-features cs-pro-features-inline">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Scheduled scans', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Google integration', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'AI detection', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Auto DMCA', 'contentshield-ai' ); ?>
                        </li>
                    </ul>
                </div>
                <div class="cs-pro-action">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-pro' ) ); ?>" class="cs-btn cs-btn-white cs-btn-lg">
                        <?php esc_html_e( 'Upgrade to Pro', 'contentshield-ai' ); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
