<?php
/**
 * Protected content admin page template.
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
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <h1><?php esc_html_e( 'Protected Content', 'contentshield-ai' ); ?></h1>
                <p class="cs-header-subtitle"><?php esc_html_e( 'Content that has been fingerprinted and protected', 'contentshield-ai' ); ?></p>
            </div>
        </div>
        <?php if ( ! empty( $protected_content ) ) : ?>
        <div class="cs-header-actions">
            <button type="button" class="cs-btn cs-btn-outline contentshield-export-fingerprints">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <?php esc_html_e( 'Export CSV', 'contentshield-ai' ); ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Stats Overview -->
    <?php if ( ! empty( $protected_content ) ) : ?>
    <div class="cs-stats-grid cs-stats-grid-compact" data-animate="fade-up" style="--delay: 0.1s">
        <div class="cs-stat-card cs-stat-card-sm">
            <div class="cs-stat-icon cs-stat-icon-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( count( $protected_content ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Protected Items', 'contentshield-ai' ); ?></span>
            </div>
        </div>
        <div class="cs-stat-card cs-stat-card-sm">
            <div class="cs-stat-icon cs-stat-icon-info">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <?php
                $total_words = 0;
                foreach ( $protected_content as $item ) {
                    $total_words += $item->word_count;
                }
                ?>
                <span class="cs-stat-number"><?php echo esc_html( number_format( $total_words ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Total Words', 'contentshield-ai' ); ?></span>
            </div>
        </div>
        <div class="cs-stat-card cs-stat-card-sm">
            <div class="cs-stat-icon cs-stat-icon-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number">100%</span>
                <span class="cs-stat-label"><?php esc_html_e( 'Protected', 'contentshield-ai' ); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Protected Content Table -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.2s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <?php esc_html_e( 'Fingerprinted Content', 'contentshield-ai' ); ?>
            </h2>
            <?php if ( ! empty( $protected_content ) ) : ?>
            <span class="cs-badge cs-badge-success">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?php echo esc_html( count( $protected_content ) ); ?> <?php esc_html_e( 'items', 'contentshield-ai' ); ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="cs-card-body cs-card-body-flush">
            <?php if ( ! empty( $protected_content ) ) : ?>
                <div class="cs-table-responsive">
                    <table class="cs-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Title', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Type', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Words', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Fingerprint', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Protected', 'contentshield-ai' ); ?></th>
                                <th class="cs-text-right"><?php esc_html_e( 'Actions', 'contentshield-ai' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $protected_content as $item ) : ?>
                                <tr data-post-id="<?php echo esc_attr( $item->post_id ); ?>">
                                    <td>
                                        <div class="cs-content-title">
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->post_id ) ); ?>" class="cs-link-title">
                                                <?php echo esc_html( $item->post_title ?: __( '(No title)', 'contentshield-ai' ) ); ?>
                                            </a>
                                            <div class="cs-row-actions">
                                                <a href="<?php echo esc_url( get_permalink( $item->post_id ) ); ?>" target="_blank" class="cs-action-link">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    <?php esc_html_e( 'View', 'contentshield-ai' ); ?>
                                                </a>
                                                <span class="cs-action-divider">|</span>
                                                <a href="#" class="cs-action-link contentshield-scan-post" data-post-id="<?php echo esc_attr( $item->post_id ); ?>">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                                    </svg>
                                                    <?php esc_html_e( 'Scan', 'contentshield-ai' ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $post_type_obj = get_post_type_object( $item->post_type );
                                        $type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $item->post_type;
                                        ?>
                                        <span class="cs-badge cs-badge-muted"><?php echo esc_html( $type_label ); ?></span>
                                    </td>
                                    <td>
                                        <span class="cs-text-number"><?php echo esc_html( number_format( $item->word_count ) ); ?></span>
                                    </td>
                                    <td>
                                        <div class="cs-fingerprint-cell">
                                            <code class="cs-code" title="<?php echo esc_attr( $item->fingerprint ); ?>">
                                                <?php echo esc_html( substr( $item->fingerprint, 0, 12 ) . '...' ); ?>
                                            </code>
                                            <button type="button" class="cs-btn-icon contentshield-copy-fingerprint" data-fingerprint="<?php echo esc_attr( $item->fingerprint ); ?>" title="<?php esc_attr_e( 'Copy fingerprint', 'contentshield-ai' ); ?>">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="cs-text-muted cs-text-nowrap">
                                        <?php echo esc_html( human_time_diff( strtotime( $item->created_at ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'contentshield-ai' ); ?>
                                    </td>
                                    <td class="cs-text-right">
                                        <button type="button" class="cs-btn cs-btn-sm cs-btn-outline contentshield-regenerate-fingerprint" data-post-id="<?php echo esc_attr( $item->post_id ); ?>">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                            </svg>
                                            <?php esc_html_e( 'Regenerate', 'contentshield-ai' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="cs-empty-state cs-empty-state-lg">
                    <div class="cs-empty-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e( 'No protected content yet', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Content will be automatically protected when you publish posts or pages. Each piece of content gets a unique fingerprint that helps detect plagiarism.', 'contentshield-ai' ); ?></p>
                    <div class="cs-empty-actions">
                        <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="cs-btn cs-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <?php esc_html_e( 'Create New Post', 'contentshield-ai' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-settings' ) ); ?>" class="cs-btn cs-btn-outline">
                            <?php esc_html_e( 'Configure Settings', 'contentshield-ai' ); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Card -->
    <div class="cs-card cs-card-info" data-animate="fade-up" style="--delay: 0.3s">
        <div class="cs-card-body">
            <div class="cs-info-content">
                <div class="cs-info-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                    </svg>
                </div>
                <div class="cs-info-text">
                    <h4><?php esc_html_e( 'How Fingerprinting Works', 'contentshield-ai' ); ?></h4>
                    <p><?php esc_html_e( 'ContentShield uses SimHash algorithm to create unique fingerprints for your content. These fingerprints remain similar even when text is slightly modified, allowing detection of plagiarism even when content thieves make minor changes.', 'contentshield-ai' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
