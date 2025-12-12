<?php
/**
 * Settings admin page template.
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
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <div>
                <h1><?php esc_html_e( 'Settings', 'contentshield-ai' ); ?></h1>
                <p class="cs-header-subtitle"><?php esc_html_e( 'Configure ContentShield AI protection settings', 'contentshield-ai' ); ?></p>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="cs-card cs-settings-card" data-animate="fade-up" style="--delay: 0.1s">
        <form method="post" action="options.php" class="cs-settings-form">
            <?php
            settings_fields( ContentShield_Settings::OPTION_GROUP );
            ?>

            <div class="cs-settings-sections">
                <?php do_settings_sections( 'contentshield-settings' ); ?>
            </div>

            <div class="cs-settings-footer">
                <?php submit_button( __( 'Save Settings', 'contentshield-ai' ), 'cs-btn cs-btn-primary cs-btn-lg', 'submit', false ); ?>
                <button type="reset" class="cs-btn cs-btn-outline">
                    <?php esc_html_e( 'Reset', 'contentshield-ai' ); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Help Info -->
    <div class="cs-card cs-card-info" data-animate="fade-up" style="--delay: 0.2s">
        <div class="cs-card-body">
            <div class="cs-info-content">
                <div class="cs-info-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <path d="M12 17h.01"/>
                    </svg>
                </div>
                <div class="cs-info-text">
                    <h4><?php esc_html_e( 'Need Help?', 'contentshield-ai' ); ?></h4>
                    <p><?php esc_html_e( 'Check out our documentation for detailed information about each setting and how to optimize your content protection.', 'contentshield-ai' ); ?></p>
                    <div class="cs-info-links">
                        <a href="https://contentshield.ai/docs" target="_blank" rel="noopener" class="cs-link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            <?php esc_html_e( 'Documentation', 'contentshield-ai' ); ?>
                        </a>
                        <a href="https://contentshield.ai/support" target="_blank" rel="noopener" class="cs-link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?php esc_html_e( 'Contact Support', 'contentshield-ai' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ( ! contentshield()->is_pro() ) : ?>
    <!-- Pro Promo -->
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
                    <h3><?php esc_html_e( 'Unlock Advanced Settings', 'contentshield-ai' ); ?></h3>
                    <p><?php esc_html_e( 'Get access to advanced protection features, automated monitoring, and priority support with ContentShield Pro.', 'contentshield-ai' ); ?></p>
                </div>
                <div class="cs-pro-action">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-pro' ) ); ?>" class="cs-btn cs-btn-white cs-btn-lg">
                        <?php esc_html_e( 'View Pro Features', 'contentshield-ai' ); ?>
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
