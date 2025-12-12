<?php
/**
 * Settings functionality.
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ContentShield_Settings
 *
 * Handles plugin settings using WordPress Settings API.
 */
class ContentShield_Settings {

    /**
     * Option group name.
     *
     * @var string
     */
    const OPTION_GROUP = 'contentshield_settings';

    /**
     * Register settings.
     *
     * @return void
     */
    public function register() {
        // General Settings Section
        add_settings_section(
            'contentshield_general',
            __( 'General Settings', 'contentshield-ai' ),
            array( $this, 'render_general_section' ),
            'contentshield-settings'
        );

        $this->register_general_settings();

        // Protection Settings Section
        add_settings_section(
            'contentshield_protection',
            __( 'Protection Settings', 'contentshield-ai' ),
            array( $this, 'render_protection_section' ),
            'contentshield-settings'
        );

        $this->register_protection_settings();

        // Scanner Settings Section
        add_settings_section(
            'contentshield_scanner',
            __( 'Scanner Settings', 'contentshield-ai' ),
            array( $this, 'render_scanner_section' ),
            'contentshield-settings'
        );

        $this->register_scanner_settings();

        // Notification Settings Section
        add_settings_section(
            'contentshield_notifications',
            __( 'Notification Settings', 'contentshield-ai' ),
            array( $this, 'render_notifications_section' ),
            'contentshield-settings'
        );

        $this->register_notification_settings();

        // License Settings Section
        add_settings_section(
            'contentshield_license',
            __( 'License', 'contentshield-ai' ),
            array( $this, 'render_license_section' ),
            'contentshield-settings'
        );

        $this->register_license_settings();
    }

    /**
     * Register general settings.
     *
     * @return void
     */
    private function register_general_settings() {
        // Enable plugin
        register_setting(
            self::OPTION_GROUP,
            'contentshield_enabled',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_enabled',
            __( 'Enable ContentShield', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_general',
            array(
                'label_for'   => 'contentshield_enabled',
                'description' => __( 'Enable or disable all ContentShield protection features.', 'contentshield-ai' ),
            )
        );

        // Auto-protect
        register_setting(
            self::OPTION_GROUP,
            'contentshield_auto_protect',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_auto_protect',
            __( 'Auto-Protect New Content', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_general',
            array(
                'label_for'   => 'contentshield_auto_protect',
                'description' => __( 'Automatically protect new posts and pages when published.', 'contentshield-ai' ),
            )
        );

        // Post types
        register_setting(
            self::OPTION_GROUP,
            'contentshield_post_types',
            array(
                'type'              => 'array',
                'default'           => array( 'post', 'page' ),
                'sanitize_callback' => array( $this, 'sanitize_post_types' ),
            )
        );

        add_settings_field(
            'contentshield_post_types',
            __( 'Protected Post Types', 'contentshield-ai' ),
            array( $this, 'render_post_types_field' ),
            'contentshield-settings',
            'contentshield_general',
            array(
                'label_for'   => 'contentshield_post_types',
                'description' => __( 'Select which post types to protect.', 'contentshield-ai' ),
            )
        );

        // Minimum word count
        register_setting(
            self::OPTION_GROUP,
            'contentshield_min_word_count',
            array(
                'type'              => 'integer',
                'default'           => 100,
                'sanitize_callback' => 'absint',
            )
        );

        add_settings_field(
            'contentshield_min_word_count',
            __( 'Minimum Word Count', 'contentshield-ai' ),
            array( $this, 'render_number_field' ),
            'contentshield-settings',
            'contentshield_general',
            array(
                'label_for'   => 'contentshield_min_word_count',
                'description' => __( 'Minimum number of words required to protect content.', 'contentshield-ai' ),
                'min'         => 0,
                'max'         => 1000,
            )
        );
    }

    /**
     * Register protection settings.
     *
     * @return void
     */
    private function register_protection_settings() {
        // Watermark enabled
        register_setting(
            self::OPTION_GROUP,
            'contentshield_watermark_enabled',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_watermark_enabled',
            __( 'Enable Watermarking', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_protection',
            array(
                'label_for'   => 'contentshield_watermark_enabled',
                'description' => __( 'Add invisible watermarks to your content.', 'contentshield-ai' ),
            )
        );

        // Fingerprint enabled
        register_setting(
            self::OPTION_GROUP,
            'contentshield_fingerprint_enabled',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_fingerprint_enabled',
            __( 'Enable Fingerprinting', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_protection',
            array(
                'label_for'   => 'contentshield_fingerprint_enabled',
                'description' => __( 'Generate content fingerprints for plagiarism detection.', 'contentshield-ai' ),
            )
        );

        // Copy protection
        register_setting(
            self::OPTION_GROUP,
            'contentshield_copy_protection',
            array(
                'type'              => 'boolean',
                'default'           => false,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_copy_protection',
            __( 'Copy Protection', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_protection',
            array(
                'label_for'   => 'contentshield_copy_protection',
                'description' => __( 'Detect and log copy-paste attempts (JavaScript-based).', 'contentshield-ai' ),
            )
        );

        // Right-click disable
        register_setting(
            self::OPTION_GROUP,
            'contentshield_right_click_disable',
            array(
                'type'              => 'boolean',
                'default'           => false,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_right_click_disable',
            __( 'Disable Right-Click', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_protection',
            array(
                'label_for'   => 'contentshield_right_click_disable',
                'description' => __( 'Disable right-click context menu on content (not recommended).', 'contentshield-ai' ),
            )
        );

        // RSS protection
        register_setting(
            self::OPTION_GROUP,
            'contentshield_rss_protection',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_rss_protection',
            __( 'RSS Feed Protection', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_protection',
            array(
                'label_for'   => 'contentshield_rss_protection',
                'description' => __( 'Add attribution links to RSS feed content.', 'contentshield-ai' ),
            )
        );
    }

    /**
     * Register scanner settings.
     *
     * @return void
     */
    private function register_scanner_settings() {
        // Scan limit
        register_setting(
            self::OPTION_GROUP,
            'contentshield_scan_limit',
            array(
                'type'              => 'integer',
                'default'           => 10,
                'sanitize_callback' => 'absint',
            )
        );

        add_settings_field(
            'contentshield_scan_limit',
            __( 'Scan History Limit', 'contentshield-ai' ),
            array( $this, 'render_number_field' ),
            'contentshield-settings',
            'contentshield_scanner',
            array(
                'label_for'   => 'contentshield_scan_limit',
                'description' => __( 'Number of recent scans to keep in history (free version).', 'contentshield-ai' ),
                'min'         => 5,
                'max'         => 100,
            )
        );

        // Scan timeout
        register_setting(
            self::OPTION_GROUP,
            'contentshield_scan_timeout',
            array(
                'type'              => 'integer',
                'default'           => 30,
                'sanitize_callback' => 'absint',
            )
        );

        add_settings_field(
            'contentshield_scan_timeout',
            __( 'Scan Timeout (seconds)', 'contentshield-ai' ),
            array( $this, 'render_number_field' ),
            'contentshield-settings',
            'contentshield_scanner',
            array(
                'label_for'   => 'contentshield_scan_timeout',
                'description' => __( 'Maximum time to wait for URL scan response.', 'contentshield-ai' ),
                'min'         => 10,
                'max'         => 120,
            )
        );
    }

    /**
     * Register notification settings.
     *
     * @return void
     */
    private function register_notification_settings() {
        // Email notifications
        register_setting(
            self::OPTION_GROUP,
            'contentshield_email_notifications',
            array(
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => 'rest_sanitize_boolean',
            )
        );

        add_settings_field(
            'contentshield_email_notifications',
            __( 'Email Notifications', 'contentshield-ai' ),
            array( $this, 'render_checkbox_field' ),
            'contentshield-settings',
            'contentshield_notifications',
            array(
                'label_for'   => 'contentshield_email_notifications',
                'description' => __( 'Receive email notifications for important alerts.', 'contentshield-ai' ),
            )
        );

        // Notification email
        register_setting(
            self::OPTION_GROUP,
            'contentshield_notification_email',
            array(
                'type'              => 'string',
                'default'           => get_option( 'admin_email' ),
                'sanitize_callback' => 'sanitize_email',
            )
        );

        add_settings_field(
            'contentshield_notification_email',
            __( 'Notification Email', 'contentshield-ai' ),
            array( $this, 'render_email_field' ),
            'contentshield-settings',
            'contentshield_notifications',
            array(
                'label_for'   => 'contentshield_notification_email',
                'description' => __( 'Email address for receiving notifications.', 'contentshield-ai' ),
            )
        );
    }

    /**
     * Register license settings.
     *
     * @return void
     */
    private function register_license_settings() {
        register_setting(
            self::OPTION_GROUP,
            'contentshield_license',
            array(
                'type'              => 'array',
                'default'           => array(),
                'sanitize_callback' => array( $this, 'sanitize_license' ),
            )
        );

        add_settings_field(
            'contentshield_license_key',
            __( 'License Key', 'contentshield-ai' ),
            array( $this, 'render_license_field' ),
            'contentshield-settings',
            'contentshield_license',
            array(
                'label_for'   => 'contentshield_license_key',
                'description' => __( 'Enter your Pro license key to unlock premium features.', 'contentshield-ai' ),
            )
        );
    }

    /**
     * Render general section description.
     *
     * @return void
     */
    public function render_general_section() {
        echo '<p>' . esc_html__( 'Configure general protection settings for your content.', 'contentshield-ai' ) . '</p>';
    }

    /**
     * Render protection section description.
     *
     * @return void
     */
    public function render_protection_section() {
        echo '<p>' . esc_html__( 'Configure how your content is protected against theft.', 'contentshield-ai' ) . '</p>';
    }

    /**
     * Render scanner section description.
     *
     * @return void
     */
    public function render_scanner_section() {
        echo '<p>' . esc_html__( 'Configure plagiarism scanner settings.', 'contentshield-ai' ) . '</p>';
    }

    /**
     * Render notifications section description.
     *
     * @return void
     */
    public function render_notifications_section() {
        echo '<p>' . esc_html__( 'Configure how you receive alerts and notifications.', 'contentshield-ai' ) . '</p>';
    }

    /**
     * Render license section description.
     *
     * @return void
     */
    public function render_license_section() {
        $license = get_option( 'contentshield_license', array() );

        if ( ! empty( $license['status'] ) && 'active' === $license['status'] ) {
            echo '<p class="contentshield-license-active">';
            echo '<span class="dashicons dashicons-yes-alt"></span> ';
            printf(
                /* translators: %s: plan name */
                esc_html__( 'Your %s license is active.', 'contentshield-ai' ),
                '<strong>' . esc_html( ucfirst( $license['plan'] ?? 'Pro' ) ) . '</strong>'
            );
            echo '</p>';
        } else {
            echo '<p>' . esc_html__( 'Enter your license key to unlock Pro features.', 'contentshield-ai' ) . '</p>';
        }
    }

    /**
     * Render checkbox field.
     *
     * @param array $args Field arguments.
     * @return void
     */
    public function render_checkbox_field( $args ) {
        $option_name = $args['label_for'];
        $value       = get_option( $option_name, false );
        $description = $args['description'] ?? '';
        ?>
        <label>
            <input
                type="checkbox"
                id="<?php echo esc_attr( $option_name ); ?>"
                name="<?php echo esc_attr( $option_name ); ?>"
                value="1"
                <?php checked( $value, true ); ?>
            >
            <?php echo esc_html( $description ); ?>
        </label>
        <?php
    }

    /**
     * Render number field.
     *
     * @param array $args Field arguments.
     * @return void
     */
    public function render_number_field( $args ) {
        $option_name = $args['label_for'];
        $value       = get_option( $option_name, 0 );
        $min         = $args['min'] ?? 0;
        $max         = $args['max'] ?? 999999;
        $description = $args['description'] ?? '';
        ?>
        <input
            type="number"
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $option_name ); ?>"
            value="<?php echo esc_attr( $value ); ?>"
            min="<?php echo esc_attr( $min ); ?>"
            max="<?php echo esc_attr( $max ); ?>"
            class="small-text"
        >
        <?php if ( $description ) : ?>
            <p class="description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render email field.
     *
     * @param array $args Field arguments.
     * @return void
     */
    public function render_email_field( $args ) {
        $option_name = $args['label_for'];
        $value       = get_option( $option_name, '' );
        $description = $args['description'] ?? '';
        ?>
        <input
            type="email"
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $option_name ); ?>"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
        >
        <?php if ( $description ) : ?>
            <p class="description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render post types field.
     *
     * @param array $args Field arguments.
     * @return void
     */
    public function render_post_types_field( $args ) {
        $option_name  = $args['label_for'];
        $selected     = get_option( $option_name, array( 'post', 'page' ) );
        $post_types   = get_post_types( array( 'public' => true ), 'objects' );
        $description  = $args['description'] ?? '';

        foreach ( $post_types as $post_type ) {
            if ( 'attachment' === $post_type->name ) {
                continue;
            }
            ?>
            <label style="display: block; margin-bottom: 5px;">
                <input
                    type="checkbox"
                    name="<?php echo esc_attr( $option_name ); ?>[]"
                    value="<?php echo esc_attr( $post_type->name ); ?>"
                    <?php checked( in_array( $post_type->name, $selected, true ) ); ?>
                >
                <?php echo esc_html( $post_type->labels->name ); ?>
            </label>
            <?php
        }

        if ( $description ) {
            echo '<p class="description">' . esc_html( $description ) . '</p>';
        }
    }

    /**
     * Render license field.
     *
     * @param array $args Field arguments.
     * @return void
     */
    public function render_license_field( $args ) {
        $license     = get_option( 'contentshield_license', array() );
        $is_active   = ! empty( $license['status'] ) && 'active' === $license['status'];
        $masked_key  = $license['key_masked'] ?? '';
        $description = $args['description'] ?? '';
        ?>
        <div class="contentshield-license-field">
            <?php if ( $is_active ) : ?>
                <input
                    type="text"
                    value="<?php echo esc_attr( $masked_key ); ?>"
                    class="regular-text"
                    readonly
                >
                <button type="button" class="button contentshield-deactivate-license">
                    <?php esc_html_e( 'Deactivate', 'contentshield-ai' ); ?>
                </button>
            <?php else : ?>
                <input
                    type="text"
                    id="contentshield_license_key_input"
                    placeholder="CSAI-XXXX-XXXX-XXXX-XXXX"
                    class="regular-text"
                >
                <button type="button" class="button button-primary contentshield-activate-license">
                    <?php esc_html_e( 'Activate', 'contentshield-ai' ); ?>
                </button>
            <?php endif; ?>
            <span class="contentshield-license-status"></span>
        </div>
        <?php if ( $description && ! $is_active ) : ?>
            <p class="description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>
        <p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=contentshield-pro' ) ); ?>">
                <?php esc_html_e( 'Get a Pro license', 'contentshield-ai' ); ?>
            </a>
        </p>
        <?php
    }

    /**
     * Sanitize post types array.
     *
     * @param array $value Input value.
     * @return array Sanitized value.
     */
    public function sanitize_post_types( $value ) {
        if ( ! is_array( $value ) ) {
            return array( 'post', 'page' );
        }

        return array_map( 'sanitize_key', $value );
    }

    /**
     * Sanitize license option.
     *
     * @param array $value Input value.
     * @return array Sanitized value.
     */
    public function sanitize_license( $value ) {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        if ( isset( $value['key_masked'] ) ) {
            $sanitized['key_masked'] = sanitize_text_field( $value['key_masked'] );
        }

        if ( isset( $value['key_hash'] ) ) {
            $sanitized['key_hash'] = sanitize_text_field( $value['key_hash'] );
        }

        if ( isset( $value['plan'] ) ) {
            $sanitized['plan'] = sanitize_key( $value['plan'] );
        }

        if ( isset( $value['status'] ) ) {
            $sanitized['status'] = sanitize_key( $value['status'] );
        }

        if ( isset( $value['expires_at'] ) ) {
            $sanitized['expires_at'] = sanitize_text_field( $value['expires_at'] );
        }

        if ( isset( $value['features'] ) && is_array( $value['features'] ) ) {
            $sanitized['features'] = array_map( 'sanitize_text_field', $value['features'] );
        }

        return $sanitized;
    }
}
