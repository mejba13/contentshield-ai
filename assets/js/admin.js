/**
 * ContentShield AI Admin JavaScript
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * ContentShield Admin Module
     */
    var ContentShieldAdmin = {

        /**
         * Initialize the module.
         */
        init: function() {
            this.bindEvents();
            this.initScanForm();
            this.initAnimations();
            this.initToasts();
        },

        /**
         * Bind event handlers.
         */
        bindEvents: function() {
            // License activation
            $(document).on('click', '.contentshield-activate-license, .contentshield-activate-license-pro', this.activateLicense);
            $(document).on('click', '.contentshield-deactivate-license', this.deactivateLicense);

            // Fingerprint actions
            $(document).on('click', '.contentshield-regenerate-fingerprint, .contentshield-regenerate', this.regenerateFingerprint);
            $(document).on('click', '.contentshield-copy-fingerprint', this.copyFingerprint);

            // Alert actions
            $(document).on('click', '.contentshield-mark-read', this.markAlertRead);
            $(document).on('click', '.contentshield-mark-all-read', this.markAllAlertsRead);
            $(document).on('click', '.contentshield-resolve-alert', this.resolveAlert);

            // Notice dismiss
            $(document).on('click', '.contentshield-notice .notice-dismiss', this.dismissNotice);

            // Rescan button
            $(document).on('click', '.contentshield-rescan', this.rescanUrl);

            // Export fingerprints
            $(document).on('click', '.contentshield-export-fingerprints', this.exportFingerprints);

            // Scan for copies from protected content
            $(document).on('click', '.contentshield-scan-post', this.scanPost);
        },

        /**
         * Initialize animations on scroll.
         */
        initAnimations: function() {
            var $animatedElements = $('[data-animate]');

            if (!$animatedElements.length) {
                return;
            }

            // Initial check for elements in viewport
            this.checkAnimations($animatedElements);

            // Check on scroll
            var scrollTimer;
            $(window).on('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    ContentShieldAdmin.checkAnimations($animatedElements);
                }, 50);
            });
        },

        /**
         * Check if elements should animate.
         */
        checkAnimations: function($elements) {
            var windowHeight = $(window).height();
            var windowTop = $(window).scrollTop();

            $elements.each(function() {
                var $el = $(this);
                var elTop = $el.offset().top;

                if (elTop < windowTop + windowHeight - 50) {
                    $el.addClass('cs-animated');
                }
            });
        },

        /**
         * Initialize toast container.
         */
        initToasts: function() {
            if (!$('#cs-toast-container').length) {
                $('body').append('<div id="cs-toast-container" class="cs-toast-container"></div>');
            }
        },

        /**
         * Show toast notification.
         */
        showToast: function(message, type) {
            type = type || 'info';

            var icons = {
                success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
            };

            var $toast = $('<div class="cs-toast ' + type + '">' +
                '<span class="cs-toast-icon">' + icons[type] + '</span>' +
                '<div class="cs-toast-content">' +
                '<span class="cs-toast-message">' + this.escapeHtml(message) + '</span>' +
                '</div>' +
                '<button type="button" class="cs-toast-close">&times;</button>' +
                '</div>');

            $('#cs-toast-container').append($toast);

            // Animate in
            setTimeout(function() {
                $toast.addClass('cs-toast-visible');
            }, 10);

            // Auto close after 5 seconds
            var closeTimer = setTimeout(function() {
                ContentShieldAdmin.closeToast($toast);
            }, 5000);

            // Manual close
            $toast.find('.cs-toast-close').on('click', function() {
                clearTimeout(closeTimer);
                ContentShieldAdmin.closeToast($toast);
            });

            return $toast;
        },

        /**
         * Close toast.
         */
        closeToast: function($toast) {
            $toast.removeClass('cs-toast-visible');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        },

        /**
         * Initialize scan form.
         */
        initScanForm: function() {
            var $form = $('#contentshield-scan-form');

            if (!$form.length) {
                return;
            }

            $form.on('submit', function(e) {
                e.preventDefault();
                ContentShieldAdmin.scanUrl($(this));
            });
        },

        /**
         * Scan URL for plagiarism.
         */
        scanUrl: function($form) {
            var $button = $form.find('button[type="submit"]');
            var $status = $form.find('.cs-scan-status');
            var $results = $('#contentshield-scan-results');
            var url = $form.find('#scan_url').val();
            var postId = $form.find('#compare_post').val();

            if (!url) {
                this.showToast('Please enter a URL to scan', 'error');
                return;
            }

            // Show loading state
            var originalHtml = $button.html();
            $button.prop('disabled', true).html(
                '<span class="cs-spinner"></span> Scanning...'
            );
            $status.html('');
            $results.hide();

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_scan_url',
                    nonce: contentshieldAdmin.nonce,
                    url: url,
                    post_id: postId
                },
                success: function(response) {
                    $button.prop('disabled', false).html(originalHtml);

                    if (response.success) {
                        ContentShieldAdmin.showToast('Scan completed successfully!', 'success');
                        ContentShieldAdmin.displayScanResults(response.data, $results);
                    } else {
                        var errorMsg = response.data.error || response.data.message || contentshieldAdmin.strings.scanError;
                        ContentShieldAdmin.showToast(errorMsg, 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html(originalHtml);
                    ContentShieldAdmin.showToast(contentshieldAdmin.strings.scanError, 'error');
                }
            });
        },

        /**
         * Display scan results.
         */
        displayScanResults: function(data, $container) {
            var scoreClass = 'cs-score-low';
            var scoreLabel = 'Low Risk';

            if (data.similarity >= 70) {
                scoreClass = 'cs-score-high';
                scoreLabel = 'High Risk';
            } else if (data.similarity >= 40) {
                scoreClass = 'cs-score-medium';
                scoreLabel = 'Medium Risk';
            }

            var html = '<div class="cs-scan-result">';

            // Score card
            html += '<div class="cs-result-score ' + scoreClass + '">';
            html += '<div class="cs-result-score-value">' + data.similarity.toFixed(1) + '%</div>';
            html += '<div class="cs-result-score-label">' + scoreLabel + '</div>';
            html += '</div>';

            // Details
            html += '<div class="cs-result-details">';

            // Watermark warning
            if (data.watermark_found) {
                html += '<div class="cs-alert cs-alert-danger">';
                html += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
                html += '<strong>Watermark Detected!</strong> Your invisible watermark was found in this content.';
                html += '</div>';
            }

            // Matched content
            if (data.matched_content) {
                try {
                    var matches = JSON.parse(data.matched_content);
                    if (matches && matches.length > 0) {
                        html += '<div class="cs-result-matches">';
                        html += '<h4><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Matching Phrases</h4>';
                        html += '<ul>';
                        matches.forEach(function(match) {
                            html += '<li><code>' + ContentShieldAdmin.escapeHtml(match) + '</code></li>';
                        });
                        html += '</ul>';
                        html += '</div>';
                    }
                } catch (e) {
                    // Ignore JSON parse errors
                }
            }

            // Recommendations
            html += '<div class="cs-result-recommendations">';
            html += '<h4><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Recommendations</h4>';

            if (data.similarity >= 50 || data.watermark_found) {
                html += '<ul>';
                html += '<li>Document this finding for your records</li>';
                if (data.similarity >= 70) {
                    html += '<li><strong>Consider sending a DMCA takedown notice</strong></li>';
                }
                html += '<li>Contact the website owner if appropriate</li>';
                html += '</ul>';
            } else {
                html += '<p class="cs-text-success">No significant similarity detected. Your content appears to be unique on this page.</p>';
            }
            html += '</div>';

            html += '</div>'; // .cs-result-details
            html += '</div>'; // .cs-scan-result

            $container.find('.cs-results-content').html(html);
            $container.slideDown(300);

            // Scroll to results
            $('html, body').animate({
                scrollTop: $container.offset().top - 100
            }, 300);
        },

        /**
         * Scan post for copies.
         */
        scanPost: function(e) {
            e.preventDefault();

            var postId = $(this).data('post-id');

            if (postId) {
                // Navigate to scanner page with post pre-selected
                window.location.href = contentshieldAdmin.adminUrl + 'admin.php?page=contentshield-scanner&post_id=' + postId;
            }
        },

        /**
         * Activate license.
         */
        activateLicense: function(e) {
            e.preventDefault();

            var $button = $(this);
            var $container = $button.closest('.contentshield-license-field, .cs-license-form');
            var $input = $container.find('input[type="text"]');
            var $status = $container.find('.cs-license-status');
            var licenseKey = $input.val();

            if (!licenseKey) {
                ContentShieldAdmin.showToast('Please enter a license key', 'error');
                return;
            }

            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<span class="cs-spinner"></span> Activating...');

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_activate_license',
                    nonce: contentshieldAdmin.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $button.prop('disabled', false).html(originalHtml);

                    if (response.success) {
                        ContentShieldAdmin.showToast('License activated successfully!', 'success');
                        $status.html('<span class="cs-status cs-status-success">License activated!</span>');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        var errorMsg = response.data.error || response.data.message || 'Activation failed';
                        ContentShieldAdmin.showToast(errorMsg, 'error');
                        $status.html('<span class="cs-status cs-status-danger">' + ContentShieldAdmin.escapeHtml(errorMsg) + '</span>');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html(originalHtml);
                    ContentShieldAdmin.showToast('An error occurred', 'error');
                }
            });
        },

        /**
         * Deactivate license.
         */
        deactivateLicense: function(e) {
            e.preventDefault();

            if (!confirm(contentshieldAdmin.strings.confirm)) {
                return;
            }

            var $button = $(this);
            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<span class="cs-spinner"></span>');

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_deactivate_license',
                    nonce: contentshieldAdmin.nonce
                },
                success: function(response) {
                    $button.prop('disabled', false).html(originalHtml);

                    if (response.success) {
                        ContentShieldAdmin.showToast('License deactivated', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        ContentShieldAdmin.showToast('Deactivation failed', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html(originalHtml);
                    ContentShieldAdmin.showToast('An error occurred', 'error');
                }
            });
        },

        /**
         * Regenerate fingerprint.
         */
        regenerateFingerprint: function(e) {
            e.preventDefault();

            var $button = $(this);
            var postId = $button.data('post-id');

            if (!postId) {
                return;
            }

            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<span class="cs-spinner"></span> Regenerating...');

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_generate_fingerprint',
                    nonce: contentshieldAdmin.nonce,
                    post_id: postId
                },
                success: function(response) {
                    if (response.success) {
                        ContentShieldAdmin.showToast('Fingerprint regenerated!', 'success');
                        $button.html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Done!');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        $button.prop('disabled', false).html(originalHtml);
                        ContentShieldAdmin.showToast(response.data.message || 'Failed to regenerate', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html(originalHtml);
                    ContentShieldAdmin.showToast('An error occurred', 'error');
                }
            });
        },

        /**
         * Copy fingerprint to clipboard.
         */
        copyFingerprint: function(e) {
            e.preventDefault();

            var $button = $(this);
            var fingerprint = $button.data('fingerprint');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(fingerprint).then(function() {
                    ContentShieldAdmin.showToast('Fingerprint copied to clipboard!', 'success');
                    // Visual feedback
                    $button.addClass('cs-copied');
                    setTimeout(function() {
                        $button.removeClass('cs-copied');
                    }, 1500);
                });
            } else {
                // Fallback for older browsers
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(fingerprint).select();
                document.execCommand('copy');
                $temp.remove();
                ContentShieldAdmin.showToast('Fingerprint copied!', 'success');
            }
        },

        /**
         * Mark alert as read.
         */
        markAlertRead: function(e) {
            e.preventDefault();

            var $button = $(this);
            var alertId = $button.data('alert-id');
            var $row = $button.closest('tr');

            $button.prop('disabled', true);

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_mark_alert_read',
                    nonce: contentshieldAdmin.nonce,
                    alert_id: alertId
                },
                success: function(response) {
                    if (response.success) {
                        $row.removeClass('cs-alert-unread');
                        $row.find('.cs-unread-indicator').fadeOut(300, function() {
                            $(this).remove();
                        });
                        $button.fadeOut(300, function() {
                            $(this).remove();
                        });
                        ContentShieldAdmin.showToast('Alert marked as read', 'success');
                    } else {
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Mark all alerts as read.
         */
        markAllAlertsRead: function(e) {
            e.preventDefault();

            var $button = $(this);
            var originalHtml = $button.html();
            $button.prop('disabled', true).html('<span class="cs-spinner"></span> Processing...');

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_mark_all_alerts_read',
                    nonce: contentshieldAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        ContentShieldAdmin.showToast('All alerts marked as read', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        $button.prop('disabled', false).html(originalHtml);
                        ContentShieldAdmin.showToast('Failed to update alerts', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html(originalHtml);
                    ContentShieldAdmin.showToast('An error occurred', 'error');
                }
            });
        },

        /**
         * Resolve alert.
         */
        resolveAlert: function(e) {
            e.preventDefault();

            var $button = $(this);
            var alertId = $button.data('alert-id');
            var $row = $button.closest('tr');

            $button.prop('disabled', true).html('<span class="cs-spinner"></span>');

            $.ajax({
                url: contentshieldAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'contentshield_resolve_alert',
                    nonce: contentshieldAdmin.nonce,
                    alert_id: alertId
                },
                success: function(response) {
                    if (response.success) {
                        $row.addClass('cs-alert-resolved');
                        $button.replaceWith(
                            '<span class="cs-resolved-label">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>' +
                            ' Resolved</span>'
                        );
                        ContentShieldAdmin.showToast('Alert resolved', 'success');
                    } else {
                        $button.prop('disabled', false).html('Resolve');
                        ContentShieldAdmin.showToast('Failed to resolve alert', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html('Resolve');
                    ContentShieldAdmin.showToast('An error occurred', 'error');
                }
            });
        },

        /**
         * Dismiss notice.
         */
        dismissNotice: function() {
            var $notice = $(this).closest('.contentshield-notice');
            var noticeType = $notice.data('notice');

            if (noticeType) {
                $.ajax({
                    url: contentshieldAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'contentshield_dismiss_notice',
                        nonce: contentshieldAdmin.nonce,
                        notice: noticeType
                    }
                });
            }
        },

        /**
         * Rescan URL.
         */
        rescanUrl: function(e) {
            e.preventDefault();

            var url = $(this).data('url');

            if (url) {
                $('#scan_url').val(url);
                $('#contentshield-scan-form').submit();
                $('html, body').animate({
                    scrollTop: $('#contentshield-scan-form').offset().top - 100
                }, 500);
            }
        },

        /**
         * Export fingerprints.
         */
        exportFingerprints: function(e) {
            e.preventDefault();

            ContentShieldAdmin.showToast('Preparing export...', 'info');

            window.location.href = contentshieldAdmin.ajaxUrl +
                '?action=contentshield_export_fingerprints&nonce=' + contentshieldAdmin.nonce;
        },

        /**
         * Escape HTML.
         */
        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        ContentShieldAdmin.init();
    });

})(jQuery);
