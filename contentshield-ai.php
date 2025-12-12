<?php
/**
 * Plugin Name:       ContentShield AI
 * Plugin URI:        https://contentshield.ai
 * Description:       AI-powered content protection and plagiarism defense. Protect your content with invisible watermarks, fingerprinting, and plagiarism detection.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Engr Mejba Ahmed
 * Author URI:        https://www.mejba.me/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contentshield-ai
 * Domain Path:       /languages
 *
 * @package ContentShield_AI
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'CONTENTSHIELD_VERSION', '1.0.0' );
define( 'CONTENTSHIELD_PLUGIN_FILE', __FILE__ );
define( 'CONTENTSHIELD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTENTSHIELD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONTENTSHIELD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CONTENTSHIELD_API_URL', 'https://api.contentshield.ai/v1' );
define( 'CONTENTSHIELD_MIN_PHP_VERSION', '7.4' );
define( 'CONTENTSHIELD_MIN_WP_VERSION', '5.8' );

/**
 * Check PHP version and WordPress version requirements.
 *
 * @return bool
 */
function contentshield_requirements_met() {
    if ( version_compare( PHP_VERSION, CONTENTSHIELD_MIN_PHP_VERSION, '<' ) ) {
        return false;
    }

    if ( version_compare( get_bloginfo( 'version' ), CONTENTSHIELD_MIN_WP_VERSION, '<' ) ) {
        return false;
    }

    return true;
}

/**
 * Display admin notice if requirements are not met.
 */
function contentshield_requirements_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            printf(
                /* translators: 1: PHP version, 2: WordPress version */
                esc_html__( 'ContentShield AI requires PHP %1$s+ and WordPress %2$s+. Please upgrade your server to meet these requirements.', 'contentshield-ai' ),
                esc_html( CONTENTSHIELD_MIN_PHP_VERSION ),
                esc_html( CONTENTSHIELD_MIN_WP_VERSION )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Plugin activation hook.
 */
function contentshield_activate() {
    if ( ! contentshield_requirements_met() ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            sprintf(
                /* translators: 1: PHP version, 2: WordPress version */
                esc_html__( 'ContentShield AI requires PHP %1$s+ and WordPress %2$s+.', 'contentshield-ai' ),
                esc_html( CONTENTSHIELD_MIN_PHP_VERSION ),
                esc_html( CONTENTSHIELD_MIN_WP_VERSION )
            )
        );
    }

    require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/class-activator.php';
    ContentShield_Activator::activate();
}
register_activation_hook( __FILE__, 'contentshield_activate' );

/**
 * Plugin deactivation hook.
 */
function contentshield_deactivate() {
    require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/class-deactivator.php';
    ContentShield_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'contentshield_deactivate' );

/**
 * Initialize the plugin.
 */
function contentshield_init() {
    if ( ! contentshield_requirements_met() ) {
        add_action( 'admin_notices', 'contentshield_requirements_notice' );
        return;
    }

    // Load text domain for translations
    load_plugin_textdomain(
        'contentshield-ai',
        false,
        dirname( CONTENTSHIELD_PLUGIN_BASENAME ) . '/languages/'
    );

    // Load the main plugin class
    require_once CONTENTSHIELD_PLUGIN_DIR . 'includes/class-contentshield-ai.php';

    // Initialize the plugin
    $plugin = new ContentShield_AI();
    $plugin->run();
}
add_action( 'plugins_loaded', 'contentshield_init' );

/**
 * Get the main plugin instance.
 *
 * @return ContentShield_AI|null
 */
function contentshield() {
    static $instance = null;

    if ( null === $instance ) {
        if ( class_exists( 'ContentShield_AI' ) ) {
            $instance = new ContentShield_AI();
        }
    }

    return $instance;
}
