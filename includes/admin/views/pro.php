<?php
/**
 * Pro features admin page template.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="cs-wrap cs-pro-page">
    <!-- Hero Section -->
    <div class="cs-pro-hero" data-animate="fade-up">
        <div class="cs-pro-hero-content">
            <div class="cs-pro-hero-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <?php esc_html_e( 'PRO', 'contentshield-ai' ); ?>
            </div>
            <h1><?php esc_html_e( 'Take Your Content Protection to the Next Level', 'contentshield-ai' ); ?></h1>
            <p class="cs-pro-hero-subtitle"><?php esc_html_e( 'Automated monitoring, AI-powered detection, and instant DMCA takedowns. Protect your content 24/7.', 'contentshield-ai' ); ?></p>
            <div class="cs-pro-hero-stats">
                <div class="cs-pro-hero-stat">
                    <span class="cs-pro-hero-stat-number">10K+</span>
                    <span class="cs-pro-hero-stat-label"><?php esc_html_e( 'Protected Sites', 'contentshield-ai' ); ?></span>
                </div>
                <div class="cs-pro-hero-stat">
                    <span class="cs-pro-hero-stat-number">1M+</span>
                    <span class="cs-pro-hero-stat-label"><?php esc_html_e( 'DMCA Sent', 'contentshield-ai' ); ?></span>
                </div>
                <div class="cs-pro-hero-stat">
                    <span class="cs-pro-hero-stat-number">99.9%</span>
                    <span class="cs-pro-hero-stat-label"><?php esc_html_e( 'Success Rate', 'contentshield-ai' ); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Plans -->
    <div class="cs-pricing-section" data-animate="fade-up" style="--delay: 0.1s">
        <div class="cs-section-header">
            <h2><?php esc_html_e( 'Choose Your Plan', 'contentshield-ai' ); ?></h2>
            <p><?php esc_html_e( 'Simple, transparent pricing. No hidden fees.', 'contentshield-ai' ); ?></p>
        </div>

        <div class="cs-pricing-grid">
            <!-- Starter Plan -->
            <div class="cs-pricing-card">
                <div class="cs-pricing-header">
                    <span class="cs-pricing-plan-name"><?php esc_html_e( 'Starter', 'contentshield-ai' ); ?></span>
                    <div class="cs-pricing-price">
                        <span class="cs-price-currency">$</span>
                        <span class="cs-price-amount">9</span>
                        <span class="cs-price-period">/<?php esc_html_e( 'mo', 'contentshield-ai' ); ?></span>
                    </div>
                    <p class="cs-pricing-description"><?php esc_html_e( 'Perfect for bloggers and small sites', 'contentshield-ai' ); ?></p>
                </div>
                <ul class="cs-pricing-features">
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Weekly monitoring', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( '50 protected posts', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'AI-powered matching', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'DMCA templates', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( '1 website', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-disabled">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        <?php esc_html_e( 'Auto DMCA submission', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-disabled">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        <?php esc_html_e( 'API access', 'contentshield-ai' ); ?>
                    </li>
                </ul>
                <a href="https://contentshield.ai/pricing?plan=starter" class="cs-btn cs-btn-outline cs-btn-lg cs-btn-block" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Get Started', 'contentshield-ai' ); ?>
                </a>
            </div>

            <!-- Pro Plan (Featured) -->
            <div class="cs-pricing-card cs-pricing-featured">
                <div class="cs-pricing-badge"><?php esc_html_e( 'Most Popular', 'contentshield-ai' ); ?></div>
                <div class="cs-pricing-header">
                    <span class="cs-pricing-plan-name"><?php esc_html_e( 'Pro', 'contentshield-ai' ); ?></span>
                    <div class="cs-pricing-price">
                        <span class="cs-price-currency">$</span>
                        <span class="cs-price-amount">19</span>
                        <span class="cs-price-period">/<?php esc_html_e( 'mo', 'contentshield-ai' ); ?></span>
                    </div>
                    <p class="cs-pricing-description"><?php esc_html_e( 'For serious content creators', 'contentshield-ai' ); ?></p>
                </div>
                <ul class="cs-pricing-features">
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Daily monitoring', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( '500 protected posts', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'AI-powered matching', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Auto DMCA to Google/Bing', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Full API access', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( '5 websites', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Priority support', 'contentshield-ai' ); ?>
                    </li>
                </ul>
                <a href="https://contentshield.ai/pricing?plan=pro" class="cs-btn cs-btn-primary cs-btn-lg cs-btn-block" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Get Pro', 'contentshield-ai' ); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Agency Plan -->
            <div class="cs-pricing-card">
                <div class="cs-pricing-header">
                    <span class="cs-pricing-plan-name"><?php esc_html_e( 'Agency', 'contentshield-ai' ); ?></span>
                    <div class="cs-pricing-price">
                        <span class="cs-price-currency">$</span>
                        <span class="cs-price-amount">49</span>
                        <span class="cs-price-period">/<?php esc_html_e( 'mo', 'contentshield-ai' ); ?></span>
                    </div>
                    <p class="cs-pricing-description"><?php esc_html_e( 'For agencies and publishers', 'contentshield-ai' ); ?></p>
                </div>
                <ul class="cs-pricing-features">
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Hourly monitoring', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Unlimited posts', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'AI-powered matching', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Auto DMCA to Google/Bing', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'White-label reports', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( 'Full API access', 'contentshield-ai' ); ?>
                    </li>
                    <li class="cs-feature-included">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e( '50 websites', 'contentshield-ai' ); ?>
                    </li>
                </ul>
                <a href="https://contentshield.ai/pricing?plan=agency" class="cs-btn cs-btn-outline cs-btn-lg cs-btn-block" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Get Agency', 'contentshield-ai' ); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Feature Comparison -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.2s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                <?php esc_html_e( 'Free vs Pro Features', 'contentshield-ai' ); ?>
            </h2>
        </div>
        <div class="cs-card-body cs-card-body-flush">
            <div class="cs-table-responsive">
                <table class="cs-table cs-comparison-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Feature', 'contentshield-ai' ); ?></th>
                            <th class="cs-text-center"><?php esc_html_e( 'Free', 'contentshield-ai' ); ?></th>
                            <th class="cs-text-center cs-highlight"><?php esc_html_e( 'Pro', 'contentshield-ai' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e( 'Invisible Watermarking', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Content Fingerprinting', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Manual URL Scanning', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'RSS Feed Protection', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Automated Monitoring', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'AI-Powered Detection', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'DMCA Templates', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Auto DMCA Submission', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Priority Support', 'contentshield-ai' ); ?></td>
                            <td class="cs-text-center"><span class="cs-check cs-check-no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></td>
                            <td class="cs-text-center cs-highlight"><span class="cs-check cs-check-yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- License Activation -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.3s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <?php esc_html_e( 'Activate Your License', 'contentshield-ai' ); ?>
            </h2>
        </div>
        <div class="cs-card-body">
            <p class="cs-text-muted cs-mb-4"><?php esc_html_e( 'Already have a license? Enter your license key below to activate Pro features:', 'contentshield-ai' ); ?></p>

            <div class="cs-license-form">
                <div class="cs-input-group cs-input-group-lg">
                    <span class="cs-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="contentshield_license_key_pro"
                        class="cs-input cs-input-lg"
                        placeholder="CSAI-XXXX-XXXX-XXXX-XXXX"
                    >
                </div>
                <button type="button" class="cs-btn cs-btn-primary cs-btn-lg contentshield-activate-license-pro">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <?php esc_html_e( 'Activate License', 'contentshield-ai' ); ?>
                </button>
            </div>
            <div class="cs-license-status"></div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="cs-card" data-animate="fade-up" style="--delay: 0.4s">
        <div class="cs-card-header">
            <h2 class="cs-card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <path d="M12 17h.01"/>
                </svg>
                <?php esc_html_e( 'Frequently Asked Questions', 'contentshield-ai' ); ?>
            </h2>
        </div>
        <div class="cs-card-body">
            <div class="cs-faq-grid">
                <div class="cs-faq-item">
                    <h4>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <?php esc_html_e( 'Can I cancel anytime?', 'contentshield-ai' ); ?>
                    </h4>
                    <p><?php esc_html_e( 'Yes, you can cancel your subscription at any time. Your Pro features will remain active until the end of your billing period.', 'contentshield-ai' ); ?></p>
                </div>

                <div class="cs-faq-item">
                    <h4>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <?php esc_html_e( 'Do you offer refunds?', 'contentshield-ai' ); ?>
                    </h4>
                    <p><?php esc_html_e( 'Yes, we offer a 14-day money-back guarantee. If you\'re not satisfied, contact us for a full refund.', 'contentshield-ai' ); ?></p>
                </div>

                <div class="cs-faq-item">
                    <h4>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <?php esc_html_e( 'What payment methods do you accept?', 'contentshield-ai' ); ?>
                    </h4>
                    <p><?php esc_html_e( 'We accept all major credit cards, PayPal, and other local payment methods through our secure payment processor.', 'contentshield-ai' ); ?></p>
                </div>

                <div class="cs-faq-item">
                    <h4>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <?php esc_html_e( 'Can I upgrade or downgrade my plan?', 'contentshield-ai' ); ?>
                    </h4>
                    <p><?php esc_html_e( 'Yes, you can change your plan at any time. Upgrades take effect immediately, and downgrades apply on your next billing date.', 'contentshield-ai' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Badges -->
    <div class="cs-trust-section" data-animate="fade-up" style="--delay: 0.5s">
        <div class="cs-trust-badges">
            <div class="cs-trust-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span><?php esc_html_e( 'Secure Payment', 'contentshield-ai' ); ?></span>
            </div>
            <div class="cs-trust-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span><?php esc_html_e( '14-Day Guarantee', 'contentshield-ai' ); ?></span>
            </div>
            <div class="cs-trust-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span><?php esc_html_e( 'Priority Support', 'contentshield-ai' ); ?></span>
            </div>
            <div class="cs-trust-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span><?php esc_html_e( 'Instant Activation', 'contentshield-ai' ); ?></span>
            </div>
        </div>
    </div>
</div>
