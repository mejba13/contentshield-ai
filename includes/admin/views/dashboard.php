<?php
/**
 * Dashboard admin page template.
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
                <h1><?php esc_html_e( 'Dashboard', 'contentshield-ai' ); ?></h1>
                <p class="cs-header-subtitle"><?php esc_html_e( 'Monitor and protect your content from plagiarism', 'contentshield-ai' ); ?></p>
            </div>
        </div>
        <div class="cs-header-actions">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-scanner' ) ); ?>" class="cs-btn cs-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <?php esc_html_e( 'Scan URL', 'contentshield-ai' ); ?>
            </a>
        </div>
    </div>

    <?php if ( ! get_option( 'contentshield_enabled', true ) ) : ?>
        <div class="cs-alert cs-alert-warning">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                <path d="M12 9v4"/><path d="M12 17h.01"/>
            </svg>
            <div>
                <strong><?php esc_html_e( 'ContentShield is currently disabled.', 'contentshield-ai' ); ?></strong>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-settings' ) ); ?>">
                    <?php esc_html_e( 'Enable it in settings', 'contentshield-ai' ); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="cs-stats-grid">
        <div class="cs-stat-card" data-animate="fade-up" style="--delay: 0.1s">
            <div class="cs-stat-icon cs-stat-icon-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( number_format( $stats['protected_posts'] ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Protected Posts', 'contentshield-ai' ); ?></span>
            </div>
            <div class="cs-stat-trend cs-stat-trend-up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19V5M5 12l7-7 7 7"/>
                </svg>
                <span><?php esc_html_e( 'Active', 'contentshield-ai' ); ?></span>
            </div>
        </div>

        <div class="cs-stat-card" data-animate="fade-up" style="--delay: 0.2s">
            <div class="cs-stat-icon cs-stat-icon-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( number_format( $stats['total_scans'] ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Total Scans', 'contentshield-ai' ); ?></span>
            </div>
            <div class="cs-stat-footer">
                <span class="cs-stat-meta"><?php esc_html_e( 'All time', 'contentshield-ai' ); ?></span>
            </div>
        </div>

        <div class="cs-stat-card" data-animate="fade-up" style="--delay: 0.3s">
            <div class="cs-stat-icon cs-stat-icon-warning">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( number_format( $stats['potential_matches'] ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Potential Matches', 'contentshield-ai' ); ?></span>
            </div>
            <?php if ( $stats['potential_matches'] > 0 ) : ?>
            <div class="cs-stat-trend cs-stat-trend-alert">
                <span><?php esc_html_e( 'Needs review', 'contentshield-ai' ); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="cs-stat-card" data-animate="fade-up" style="--delay: 0.4s">
            <div class="cs-stat-icon cs-stat-icon-danger">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
            </div>
            <div class="cs-stat-content">
                <span class="cs-stat-number"><?php echo esc_html( number_format( $stats['unread_alerts'] ) ); ?></span>
                <span class="cs-stat-label"><?php esc_html_e( 'Unread Alerts', 'contentshield-ai' ); ?></span>
            </div>
            <?php if ( $stats['unread_alerts'] > 0 ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-alerts' ) ); ?>" class="cs-stat-link">
                <?php esc_html_e( 'View all', 'contentshield-ai' ); ?>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="cs-dashboard-grid">
        <!-- Quick Actions Card (Full Width) -->
        <div class="cs-card cs-card-wide" data-animate="fade-up" style="--delay: 0.5s">
            <div class="cs-card-header">
                <h2 class="cs-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                    <?php esc_html_e( 'Quick Actions', 'contentshield-ai' ); ?>
                </h2>
            </div>
            <div class="cs-card-body">
                <div class="cs-quick-actions">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-scanner' ) ); ?>" class="cs-action-card">
                        <div class="cs-action-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <div class="cs-action-content">
                            <span class="cs-action-title"><?php esc_html_e( 'Scan URL', 'contentshield-ai' ); ?></span>
                            <span class="cs-action-desc"><?php esc_html_e( 'Check for plagiarism', 'contentshield-ai' ); ?></span>
                        </div>
                        <svg class="cs-action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-protected' ) ); ?>" class="cs-action-card">
                        <div class="cs-action-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div class="cs-action-content">
                            <span class="cs-action-title"><?php esc_html_e( 'Protected Content', 'contentshield-ai' ); ?></span>
                            <span class="cs-action-desc"><?php esc_html_e( 'View fingerprints', 'contentshield-ai' ); ?></span>
                        </div>
                        <svg class="cs-action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-alerts' ) ); ?>" class="cs-action-card">
                        <div class="cs-action-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                            </svg>
                        </div>
                        <div class="cs-action-content">
                            <span class="cs-action-title"><?php esc_html_e( 'View Alerts', 'contentshield-ai' ); ?></span>
                            <span class="cs-action-desc"><?php esc_html_e( 'Check notifications', 'contentshield-ai' ); ?></span>
                        </div>
                        <svg class="cs-action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-settings' ) ); ?>" class="cs-action-card">
                        <div class="cs-action-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </div>
                        <div class="cs-action-content">
                            <span class="cs-action-title"><?php esc_html_e( 'Settings', 'contentshield-ai' ); ?></span>
                            <span class="cs-action-desc"><?php esc_html_e( 'Configure plugin', 'contentshield-ai' ); ?></span>
                        </div>
                        <svg class="cs-action-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Scans Card -->
        <div class="cs-card cs-card-wide" data-animate="fade-up" style="--delay: 0.6s">
            <div class="cs-card-header">
                <h2 class="cs-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?php esc_html_e( 'Recent Scans', 'contentshield-ai' ); ?>
                </h2>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-scanner' ) ); ?>" class="cs-card-link">
                    <?php esc_html_e( 'View all', 'contentshield-ai' ); ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="cs-card-body cs-card-body-flush">
                <?php if ( ! empty( $stats['recent_scans'] ) ) : ?>
                    <table class="cs-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'URL', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Similarity', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'contentshield-ai' ); ?></th>
                                <th><?php esc_html_e( 'Date', 'contentshield-ai' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $stats['recent_scans'] as $scan ) : ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo esc_url( $scan->scanned_url ); ?>" target="_blank" rel="noopener" class="cs-link-external">
                                            <?php echo esc_html( wp_trim_words( $scan->scanned_url, 5, '...' ) ); ?>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                            </svg>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ( null !== $scan->similarity_score ) : ?>
                                            <?php
                                            $score_class = 'cs-badge-success';
                                            if ( $scan->similarity_score >= 70 ) {
                                                $score_class = 'cs-badge-danger';
                                            } elseif ( $scan->similarity_score >= 50 ) {
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
                                    <td class="cs-text-muted">
                                        <?php echo esc_html( human_time_diff( strtotime( $scan->created_at ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'contentshield-ai' ); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="cs-empty-state">
                        <div class="cs-empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3><?php esc_html_e( 'No scans yet', 'contentshield-ai' ); ?></h3>
                        <p><?php esc_html_e( 'Start by scanning a URL to check for potential plagiarism of your content.', 'contentshield-ai' ); ?></p>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-scanner' ) ); ?>" class="cs-btn cs-btn-primary">
                            <?php esc_html_e( 'Scan Your First URL', 'contentshield-ai' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Protection Status Card -->
        <div class="cs-card" data-animate="fade-up" style="--delay: 0.7s">
            <div class="cs-card-header">
                <h2 class="cs-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <?php esc_html_e( 'Protection Status', 'contentshield-ai' ); ?>
                </h2>
            </div>
            <div class="cs-card-body">
                <ul class="cs-status-list">
                    <li class="cs-status-item">
                        <span class="cs-status-indicator <?php echo get_option( 'contentshield_watermark_enabled', true ) ? 'cs-indicator-active' : 'cs-indicator-inactive'; ?>"></span>
                        <span class="cs-status-text"><?php esc_html_e( 'Invisible Watermarking', 'contentshield-ai' ); ?></span>
                        <?php if ( get_option( 'contentshield_watermark_enabled', true ) ) : ?>
                            <span class="cs-badge cs-badge-success"><?php esc_html_e( 'On', 'contentshield-ai' ); ?></span>
                        <?php else : ?>
                            <span class="cs-badge cs-badge-muted"><?php esc_html_e( 'Off', 'contentshield-ai' ); ?></span>
                        <?php endif; ?>
                    </li>
                    <li class="cs-status-item">
                        <span class="cs-status-indicator <?php echo get_option( 'contentshield_fingerprint_enabled', true ) ? 'cs-indicator-active' : 'cs-indicator-inactive'; ?>"></span>
                        <span class="cs-status-text"><?php esc_html_e( 'Content Fingerprinting', 'contentshield-ai' ); ?></span>
                        <?php if ( get_option( 'contentshield_fingerprint_enabled', true ) ) : ?>
                            <span class="cs-badge cs-badge-success"><?php esc_html_e( 'On', 'contentshield-ai' ); ?></span>
                        <?php else : ?>
                            <span class="cs-badge cs-badge-muted"><?php esc_html_e( 'Off', 'contentshield-ai' ); ?></span>
                        <?php endif; ?>
                    </li>
                    <li class="cs-status-item">
                        <span class="cs-status-indicator <?php echo get_option( 'contentshield_rss_protection', true ) ? 'cs-indicator-active' : 'cs-indicator-inactive'; ?>"></span>
                        <span class="cs-status-text"><?php esc_html_e( 'RSS Feed Protection', 'contentshield-ai' ); ?></span>
                        <?php if ( get_option( 'contentshield_rss_protection', true ) ) : ?>
                            <span class="cs-badge cs-badge-success"><?php esc_html_e( 'On', 'contentshield-ai' ); ?></span>
                        <?php else : ?>
                            <span class="cs-badge cs-badge-muted"><?php esc_html_e( 'Off', 'contentshield-ai' ); ?></span>
                        <?php endif; ?>
                    </li>
                    <li class="cs-status-item">
                        <span class="cs-status-indicator <?php echo get_option( 'contentshield_copy_protection', false ) ? 'cs-indicator-active' : 'cs-indicator-inactive'; ?>"></span>
                        <span class="cs-status-text"><?php esc_html_e( 'Copy-Paste Detection', 'contentshield-ai' ); ?></span>
                        <?php if ( get_option( 'contentshield_copy_protection', false ) ) : ?>
                            <span class="cs-badge cs-badge-success"><?php esc_html_e( 'On', 'contentshield-ai' ); ?></span>
                        <?php else : ?>
                            <span class="cs-badge cs-badge-muted"><?php esc_html_e( 'Off', 'contentshield-ai' ); ?></span>
                        <?php endif; ?>
                    </li>
                </ul>
                <div class="cs-card-footer">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-settings' ) ); ?>" class="cs-link">
                        <?php esc_html_e( 'Manage settings', 'contentshield-ai' ); ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <?php if ( ! contentshield()->is_pro() ) : ?>
        <!-- Pro Upgrade Card -->
        <div class="cs-card cs-card-gradient" data-animate="fade-up" style="--delay: 0.8s">
            <div class="cs-card-body">
                <div class="cs-pro-promo">
                    <div class="cs-pro-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php esc_html_e( 'PRO', 'contentshield-ai' ); ?>
                    </div>
                    <h3><?php esc_html_e( 'Upgrade to Pro', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Get advanced protection features and automated monitoring.', 'contentshield-ai' ); ?></p>
                    <ul class="cs-pro-features">
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Automated web monitoring', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'AI-powered plagiarism detection', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Automated DMCA takedowns', 'contentshield-ai' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php esc_html_e( 'Priority support', 'contentshield-ai' ); ?>
                        </li>
                    </ul>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-pro' ) ); ?>" class="cs-btn cs-btn-white">
                        <?php esc_html_e( 'View Plans', 'contentshield-ai' ); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
