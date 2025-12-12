<?php
/**
 * Alerts admin page template.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$unread_count = 0;
if ( ! empty( $alerts ) ) {
    foreach ( $alerts as $alert ) {
        if ( ! $alert->is_read ) {
            $unread_count++;
        }
    }
}
?>
<div class="cs-wrap">
    <!-- Page Header -->
    <div class="cs-header">
        <div class="cs-header-content">
            <div class="cs-header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
            </div>
            <div>
                <h1><?php esc_html_e( 'Alerts', 'contentshield-ai' ); ?></h1>
                <p class="cs-header-subtitle"><?php esc_html_e( 'Monitor potential plagiarism and content theft', 'contentshield-ai' ); ?></p>
            </div>
        </div>
        <?php if ( ! empty( $alerts ) && $unread_count > 0 ) : ?>
        <div class="cs-header-actions">
            <button type="button" class="cs-btn cs-btn-outline contentshield-mark-all-read">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                <?php esc_html_e( 'Mark All Read', 'contentshield-ai' ); ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Alert Stats -->
    <?php if ( ! empty( $alerts ) ) : ?>
    <div class="cs-stats-grid cs-stats-grid-compact" data-animate="fade-up" style="--delay: 0.1s">
        <div class="cs-stat-card cs-stat-card-sm">
            <div class="cs-stat-icon cs-stat-icon-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( count( $alerts ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Total Alerts', 'contentshield-ai' ); ?></span>
            </div>
        </div>
        <div class="cs-stat-card cs-stat-card-sm">
            <div class="cs-stat-icon cs-stat-icon-warning">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( $unread_count ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Unread', 'contentshield-ai' ); ?></span>
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
                <?php
                $resolved_count = 0;
                foreach ( $alerts as $alert ) {
                    if ( $alert->is_resolved ) {
                        $resolved_count++;
                    }
                }
                ?>
                <span class="cs-stat-number"><?php echo esc_html( $resolved_count ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Resolved', 'contentshield-ai' ); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alerts Table -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.2s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                <?php esc_html_e( 'All Alerts', 'contentshield-ai' ); ?>
            </h2>
            <?php if ( $unread_count > 0 ) : ?>
            <span class="cs-badge cs-badge-danger">
                <?php echo esc_html( $unread_count ); ?> <?php esc_html_e( 'unread', 'contentshield-ai' ); ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="cs-card-body cs-card-body-flush">
            <?php if ( ! empty( $alerts ) ) : ?>
                <div class="cs-table-responsive">
                    <table class="cs-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th><?php esc_html_e( 'Type', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Content', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Source', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Severity', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Date', 'contentshield-ai' ); ?></th>
                                <th class="cs-text-right"><?php esc_html_e( 'Actions', 'contentshield-ai' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $alerts as $alert ) : ?>
                                <tr class="cs-alert-row <?php echo ! $alert->is_read ? 'cs-alert-unread' : ''; ?> <?php echo $alert->is_resolved ? 'cs-alert-resolved' : ''; ?>" data-alert-id="<?php echo esc_attr( $alert->id ); ?>">
                                    <td class="cs-alert-indicator-cell">
                                        <?php if ( ! $alert->is_read ) : ?>
                                            <span class="cs-unread-indicator" title="<?php esc_attr_e( 'Unread', 'contentshield-ai' ); ?>"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $type_labels = array(
                                            'copy_detected'      => __( 'Copy Detected', 'contentshield-ai' ),
                                            'plagiarism_found'   => __( 'Plagiarism Found', 'contentshield-ai' ),
                                            'watermark_detected' => __( 'Watermark Found', 'contentshield-ai' ),
                                            'content_scraped'    => __( 'Content Scraped', 'contentshield-ai' ),
                                        );
                                        $type_icons = array(
                                            'copy_detected'      => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
                                            'plagiarism_found'   => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
                                            'watermark_detected' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                                            'content_scraped'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
                                        );
                                        ?>
                                        <span class="cs-alert-type">
                                            <?php echo isset( $type_icons[ $alert->alert_type ] ) ? $type_icons[ $alert->alert_type ] : ''; ?>
                                            <?php echo esc_html( $type_labels[ $alert->alert_type ] ?? ucfirst( str_replace( '_', ' ', $alert->alert_type ) ) ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ( $alert->post_id && $alert->post_title ) : ?>
                                            <a href="<?php echo esc_url( get_edit_post_link( $alert->post_id ) ); ?>" class="cs-link">
                                                <?php echo esc_html( wp_trim_words( $alert->post_title, 4, '...' ) ); ?>
                                            </a>
                                        <?php else : ?>
                                            <span class="cs-text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ( $alert->source_url ) : ?>
                                            <a href="<?php echo esc_url( $alert->source_url ); ?>" target="_blank" rel="noopener" class="cs-link-external">
                                                <?php echo esc_html( wp_trim_words( $alert->source_url, 4, '...' ) ); ?>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                    <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                                </svg>
                                            </a>
                                        <?php else : ?>
                                            <span class="cs-text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $severity_class = 'cs-badge-muted';
                                        if ( 'high' === $alert->severity ) {
                                            $severity_class = 'cs-badge-danger';
                                        } elseif ( 'medium' === $alert->severity ) {
                                            $severity_class = 'cs-badge-warning';
                                        } elseif ( 'low' === $alert->severity ) {
                                            $severity_class = 'cs-badge-success';
                                        }
                                        ?>
                                        <span class="cs-badge <?php echo esc_attr( $severity_class ); ?>">
                                            <?php echo esc_html( ucfirst( $alert->severity ) ); ?>
                                        </span>
                                    </td>
                                    <td class="cs-text-muted cs-text-nowrap">
                                        <?php echo esc_html( human_time_diff( strtotime( $alert->created_at ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'contentshield-ai' ); ?>
                                    </td>
                                    <td class="cs-text-right">
                                        <div class="cs-btn-group">
                                            <?php if ( ! $alert->is_read ) : ?>
                                                <button type="button" class="cs-btn cs-btn-sm cs-btn-ghost contentshield-mark-read" data-alert-id="<?php echo esc_attr( $alert->id ); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                    <?php esc_html_e( 'Read', 'contentshield-ai' ); ?>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ( ! $alert->is_resolved ) : ?>
                                                <button type="button" class="cs-btn cs-btn-sm cs-btn-outline contentshield-resolve-alert" data-alert-id="<?php echo esc_attr( $alert->id ); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                                    </svg>
                                                    <?php esc_html_e( 'Resolve', 'contentshield-ai' ); ?>
                                                </button>
                                            <?php else : ?>
                                                <span class="cs-resolved-label">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                    <?php esc_html_e( 'Resolved', 'contentshield-ai' ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="cs-empty-state cs-empty-state-lg">
                    <div class="cs-empty-icon cs-empty-icon-success">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e( 'No alerts yet', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Great news! No potential content theft has been detected. We\'ll notify you here when something requires your attention.', 'contentshield-ai' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-scanner' ) ); ?>" class="cs-btn cs-btn-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <?php esc_html_e( 'Run a Manual Scan', 'contentshield-ai' ); ?>
                    </a>
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
                    <h3><?php esc_html_e( 'Real-time Alerts', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Get instant notifications when your content is stolen, with detailed reports and one-click actions.', 'contentshield-ai' ); ?></p>
                    <ul class="cs-pro-features cs-pro-features-inline">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Email alerts', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Match reports', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'DMCA generator', 'contentshield-ai' ); ?>
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
