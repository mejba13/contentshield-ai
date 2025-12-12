/**
 * ContentShield AI Public JavaScript
 *
 * @package ContentShield_AI
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * ContentShield Public Module
     */
    var ContentShieldPublic = {

        /**
         * Configuration
         */
        config: {
            minCopyLength: 50,
            rightClickDisabled: false,
            postId: 0
        },

        /**
         * Initialize
         */
        init: function() {
            if (typeof contentshieldPublic !== 'undefined') {
                this.config.rightClickDisabled = contentshieldPublic.rightClickDisabled;
                this.config.postId = contentshieldPublic.postId;
            }

            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Copy detection
            document.addEventListener('copy', this.handleCopy.bind(this));

            // Right-click protection
            if (this.config.rightClickDisabled) {
                document.addEventListener('contextmenu', this.handleContextMenu.bind(this));
            }
        },

        /**
         * Handle copy event
         */
        handleCopy: function(e) {
            var selection = window.getSelection().toString();

            if (selection.length > this.config.minCopyLength) {
                this.logCopyAttempt(selection.length);
            }
        },

        /**
         * Handle context menu (right-click)
         */
        handleContextMenu: function(e) {
            var target = e.target;

            // Check if target is within protected content
            if (this.isProtectedContent(target)) {
                e.preventDefault();
                return false;
            }
        },

        /**
         * Check if element is within protected content
         */
        isProtectedContent: function(element) {
            var protectedSelectors = [
                '.entry-content',
                '.post-content',
                'article',
                '.the-content'
            ];

            for (var i = 0; i < protectedSelectors.length; i++) {
                if (element.closest(protectedSelectors[i])) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Log copy attempt
         */
        logCopyAttempt: function(length) {
            if (typeof contentshieldPublic === 'undefined') {
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', contentshieldPublic.ajaxUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var data = 'action=contentshield_log_copy' +
                '&nonce=' + encodeURIComponent(contentshieldPublic.nonce) +
                '&post_id=' + encodeURIComponent(this.config.postId) +
                '&length=' + encodeURIComponent(length);

            xhr.send(data);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ContentShieldPublic.init();
        });
    } else {
        ContentShieldPublic.init();
    }

})();
