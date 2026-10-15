/**
 * Simple Dropdown Fix
 * Minimal intervention approach to fix dropdown issues
 * Focus on CSS and basic event handling without conflicts
 */

(function () {
    'use strict';

    // Only run if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap not found, skipping dropdown fix');
        return;
    }

    // Simple CSS injection for dropdown fixes
    function injectDropdownCSS() {
        const css = `
            /* Ensure dropdown menus are visible and clickable */
            .dropdown-menu {
                z-index: 1050 !important;
                position: absolute !important;
            }
            
            /* Sidebar dropdown positioning */
            .navbar-vertical .dropdown-menu {
                z-index: 1051 !important;
            }
            
            /* Ensure dropdown items are clickable */
            .dropdown-item {
                pointer-events: auto !important;
                display: block !important;
            }
            
            /* Fix dropdown toggle cursor */
            .dropdown-toggle {
                cursor: pointer !important;
            }
            
            /* Prevent dropdown conflicts with page content */
            .dropdown-menu.show {
                display: block !important;
            }
        `;

        const styleElement = document.createElement('style');
        styleElement.textContent = css;
        styleElement.id = 'simple-dropdown-fix-css';

        // Remove existing if present
        const existing = document.getElementById('simple-dropdown-fix-css');
        if (existing) {
            existing.remove();
        }

        document.head.appendChild(styleElement);
    }

    // Very simple dropdown initialization
    function initializeDropdowns() {
        // Don't interfere with existing dropdowns, just ensure they work
        const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]:not([data-dropdown-initialized])');

        dropdownElements.forEach(function (element) {
            try {
                // Mark as initialized to prevent double initialization
                element.setAttribute('data-dropdown-initialized', 'true');

                // Only create new instance if none exists
                if (!bootstrap.Dropdown.getInstance(element)) {
                    new bootstrap.Dropdown(element);
                }
            } catch (e) {
                // Silently ignore errors to prevent breaking existing functionality
                console.debug('Dropdown initialization skipped for element:', element);
            }
        });
    }

    // Simple initialization function
    function init() {
        injectDropdownCSS();
        initializeDropdowns();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-run on window load as backup
    window.addEventListener('load', function () {
        setTimeout(init, 100);
    });

})();
