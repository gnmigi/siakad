/**
 * Enhanced Sidebar Navigation Fix
 * Automatically opens dropdown menus containing active pages
 * Supports Bootstrap dropdowns and Laravel route patterns
 * Maintains dropdown state across page refreshes
 * Enhanced support for data-dropdown-group attributes
 */

document.addEventListener('DOMContentLoaded', function () {

    // Configuration
    const config = {
        storageKey: 'sidebar_dropdown_state',
        activeClass: 'active',
        dropdownActiveClass: 'dropdown-active',
        highlightClass: 'menu-highlight',
        debugMode: true
    };

    // Get current URL and route information
    const currentPath = window.location.pathname;
    const currentRoute = window.location.href;
    const currentHash = window.location.hash;

    /**
     * Enhanced route patterns with Laravel route name support
     */
    const routePatterns = {
        'pengguna': {
            paths: ['/pengguna/users', '/pengguna/dosen', '/pengguna/mahasiswa'],
            routes: ['pengguna.users', 'pengguna.dosen', 'pengguna.mahasiswa']
        },
        'publikasi': {
            paths: ['/publikasi/', '/institusi/'],
            routes: ['publikasi.', 'institusi.']
        },
        'akademik': {
            paths: ['/akademik/'],
            routes: ['akademik.'],
            exclude: ['data-akademik']
        },
        'data-akademik': {
            paths: ['/akademik/data-akademik'],
            routes: ['akademik.data-akademik']
        },
        'pmb': {
            paths: ['/pmb/'],
            routes: ['pmb.']
        },
        'keuangan': {
            paths: ['/keuangan/', '/finance/'],
            routes: ['keuangan.', 'finance.'],
            keywords: ['saldo', 'tagihan', 'payment', 'bank', 'billing', 'pembayaran', 'laporan', 'analisis']
        },
        'infrastruktur': {
            paths: ['/infrastruktur/'],
            routes: ['infrastruktur.']
        },
        'pengaturan': {
            paths: ['/pengaturan/'],
            routes: ['pengaturan.']
        }
    };

    /**
     * Debug logging function
     */
    function debugLog(message, data = null) {
        if (config.debugMode) {
            console.log(`[Sidebar Fix] ${message}`, data || '');
        }
    }

    /**
     * Check if current route matches any pattern
     */
    function matchesPattern(pattern, currentPath, currentRoute) {
        // Check URL paths
        if (pattern.paths) {
            for (const path of pattern.paths) {
                if (currentPath.includes(path)) {
                    // Check exclusions
                    if (pattern.exclude) {
                        const isExcluded = pattern.exclude.some(exc => currentPath.includes(exc));
                        if (isExcluded) continue;
                    }
                    return true;
                }
            }
        }

        // Check route names (from Laravel)
        if (pattern.routes) {
            for (const route of pattern.routes) {
                if (currentRoute.includes(route) || currentPath.includes(route)) {
                    return true;
                }
            }
        }

        // Check keywords
        if (pattern.keywords) {
            for (const keyword of pattern.keywords) {
                if (currentPath.includes(keyword) || currentRoute.includes(keyword)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Find which dropdown group contains the current route
     */
    function findActiveDropdown() {
        debugLog('Finding active dropdown for path:', currentPath);

        // First, check for data-dropdown-group attributes
        const dropdowns = document.querySelectorAll('.nav-item.dropdown[data-dropdown-group]');
        for (const dropdown of dropdowns) {
            const groupName = dropdown.getAttribute('data-dropdown-group');
            const dropdownItems = dropdown.querySelectorAll('.dropdown-item');

            for (const item of dropdownItems) {
                const href = item.getAttribute('href');
                if (href && (currentPath.includes(href) || currentRoute.includes(href))) {
                    debugLog('Found active dropdown by data-group:', groupName);
                    return groupName;
                }
            }
        }

        // Fallback to pattern matching
        for (const [dropdownKey, pattern] of Object.entries(routePatterns)) {
            if (matchesPattern(pattern, currentPath, currentRoute)) {
                debugLog('Found active dropdown by pattern:', dropdownKey);
                return dropdownKey;
            }
        }

        debugLog('No active dropdown found');
        return null;
    }

    /**
     * Add enhanced styling for active states
     */
    function addCustomStyles() {
        // Remove existing styles to prevent duplicates
        const existingStyle = document.getElementById('sidebar-navigation-styles');
        if (existingStyle) {
            existingStyle.remove();
        }

        const style = document.createElement('style');
        style.id = 'sidebar-navigation-styles';
        style.textContent = `
            /* Enhanced active dropdown styling */
            .dropdown-active > .nav-link {
                background-color: rgba(var(--tblr-primary-rgb), 0.1) !important;
                color: rgb(var(--tblr-primary)) !important;
                border-left: 3px solid rgb(var(--tblr-primary)) !important;
                font-weight: 600 !important;
            }

            /* Active dropdown menu item */
            .dropdown-menu .dropdown-item.active {
                background-color: rgb(var(--tblr-primary)) !important;
                color: white !important;
                font-weight: 600 !important;
                border-radius: 4px !important;
                margin: 2px 8px !important;
                width: calc(100% - 16px) !important;
            }

            /* Hover effect for dropdown items */
            .dropdown-menu .dropdown-item:hover:not(.active) {
                background-color: rgba(var(--tblr-primary-rgb), 0.05) !important;
                color: rgb(var(--tblr-primary)) !important;
                border-radius: 4px !important;
                margin: 2px 8px !important;
                width: calc(100% - 16px) !important;
                transition: all 0.2s ease;
            }

            /* Dropdown arrow rotation when active */
            .dropdown-active .dropdown-toggle::after {
                transform: rotate(180deg);
                transition: transform 0.3s ease;
            }

            /* Smooth transition for dropdown toggles */
            .nav-link.dropdown-toggle::after {
                transition: transform 0.3s ease;
            }

            /* Enhanced active nav-link for non-dropdown items */
            .nav-link.active:not(.dropdown-toggle) {
                background-color: rgb(var(--tblr-primary)) !important;
                color: white !important;
                border-radius: 6px !important;
                margin: 2px 8px !important;
                width: calc(100% - 16px) !important;
                font-weight: 600 !important;
            }

            /* Subtle glow for active dropdown */
            .dropdown-active > .nav-link {
                box-shadow: 0 0 0 2px rgba(var(--tblr-primary-rgb), 0.2);
                transition: box-shadow 0.3s ease;
            }

            /* Badge styling for dropdown items */
            .dropdown-item .badge {
                margin-left: auto;
                opacity: 0.8;
            }

            /* Improved dropdown menu styling */
            .dropdown-menu {
                border: 1px solid rgba(var(--tblr-border-color-rgb), 0.15);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                border-radius: 0.375rem;
                padding: 0.5rem 0;
            }

            /* Smooth show/hide animation */
            .dropdown-menu {
                transition: opacity 0.15s linear, transform 0.15s ease-in-out;
            }

            .dropdown-menu.show {
                opacity: 1;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
        debugLog('Custom styles added');
    }

    /**
     * Activate dropdown and highlight active items
     */
    function activateDropdown(dropdownKey) {
        debugLog('Activating dropdown:', dropdownKey);

        // Find dropdown by data-dropdown-group first
        let targetDropdown = document.querySelector(`[data-dropdown-group="${dropdownKey}"]`);

        // Fallback: find by checking dropdown content
        if (!targetDropdown) {
            const dropdowns = document.querySelectorAll('.nav-item.dropdown');

            dropdowns.forEach(dropdown => {
                const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
                let hasActiveItem = false;

                dropdownItems.forEach(item => {
                    const href = item.getAttribute('href');
                    if (href) {
                        // More flexible matching
                        if (currentPath.includes(href) ||
                            currentRoute.includes(href) ||
                            href.includes(currentPath.split('/').pop())) {
                            hasActiveItem = true;
                            item.classList.add('active');
                            debugLog('Marked dropdown item as active:', href);
                        }
                    }
                });

                if (hasActiveItem && !targetDropdown) {
                    targetDropdown = dropdown;
                }
            });
        }

        if (targetDropdown) {
            const dropdownToggle = targetDropdown.querySelector('.dropdown-toggle');
            const dropdownMenu = targetDropdown.querySelector('.dropdown-menu');

            if (dropdownToggle && dropdownMenu) {
                // Mark dropdown as active
                targetDropdown.classList.add(config.dropdownActiveClass);

                // Show the dropdown menu using Bootstrap's method
                if (window.bootstrap && window.bootstrap.Dropdown) {
                    const bsDropdown = new window.bootstrap.Dropdown(dropdownToggle);
                    bsDropdown.show();
                } else {
                    // Fallback manual method
                    dropdownMenu.classList.add('show');
                    dropdownToggle.setAttribute('aria-expanded', 'true');
                }

                // Mark active items within the dropdown
                const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
                dropdownItems.forEach(item => {
                    const href = item.getAttribute('href');
                    if (href && (currentPath.includes(href) || currentRoute.includes(href))) {
                        item.classList.add('active');
                        debugLog('Marked dropdown item as active:', href);
                    }
                });

                // Store state
                localStorage.setItem(config.storageKey, dropdownKey);
                debugLog('Dropdown activated successfully:', dropdownKey);
            }
        } else {
            debugLog('Target dropdown not found:', dropdownKey);
        }
    }

    /**
     * Setup Bootstrap dropdown event handlers
     */
    function setupDropdownHandlers() {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            const dropdown = toggle.closest('.nav-item.dropdown');

            // Bootstrap dropdown events
            toggle.addEventListener('show.bs.dropdown', function () {
                dropdown.classList.add(config.dropdownActiveClass);
                debugLog('Dropdown shown via Bootstrap event');
            });

            toggle.addEventListener('hide.bs.dropdown', function () {
                dropdown.classList.remove(config.dropdownActiveClass);
                debugLog('Dropdown hidden via Bootstrap event');
            });

            // Fallback click handler
            toggle.addEventListener('click', function (e) {
                setTimeout(() => {
                    const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                    if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                        dropdown.classList.add(config.dropdownActiveClass);
                    } else {
                        dropdown.classList.remove(config.dropdownActiveClass);
                    }
                }, 10);
            });
        });

        debugLog('Dropdown handlers setup complete');
    }

    /**
     * Enhanced active state highlighting
     */
    function highlightCurrentPage() {
        debugLog('Highlighting current page');

        // Clear existing active states
        document.querySelectorAll('.nav-link.active, .dropdown-item.active').forEach(el => {
            if (!el.closest('.dropdown-active')) {
                el.classList.remove('active');
            }
        });

        // Find and highlight exact matches
        const allLinks = document.querySelectorAll('.nav-link, .dropdown-item');
        let foundExactMatch = false;

        allLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href) {
                // Exact match
                if (currentPath === href || currentRoute === href) {
                    link.classList.add('active');
                    foundExactMatch = true;
                    debugLog('Exact match found:', href);
                }
                // Partial match for dropdown items
                else if (link.classList.contains('dropdown-item') &&
                    (currentPath.includes(href) || href.includes(currentPath.split('/').pop()))) {
                    link.classList.add('active');
                    debugLog('Partial match found:', href);
                }
            }
        });

        debugLog('Page highlighting complete, exact match found:', foundExactMatch);
    }

    /**
     * Initialize the sidebar navigation fix
     */
    function init() {
        debugLog('Initializing Enhanced Sidebar Navigation Fix...');
        debugLog('Current path:', currentPath);
        debugLog('Current route:', currentRoute);

        // Add custom styles
        addCustomStyles();

        // Find and activate the appropriate dropdown
        const activeDropdown = findActiveDropdown();
        if (activeDropdown) {
            activateDropdown(activeDropdown);
        }

        // Highlight current page
        highlightCurrentPage();

        // Setup event handlers
        setupDropdownHandlers();

        debugLog('Sidebar Navigation Fix initialized successfully');
    }

    // Initialize when DOM is ready
    init();

    // Re-initialize on page navigation (for SPA-like behavior)
    window.addEventListener('popstate', function () {
        debugLog('Page navigation detected, re-initializing...');
        setTimeout(init, 100);
    });

    // Handle dynamic content loading
    const observer = new MutationObserver(function (mutations) {
        let shouldReinit = false;
        mutations.forEach(function (mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1 && (node.classList.contains('nav-item') || node.querySelector('.nav-item'))) {
                        shouldReinit = true;
                    }
                });
            }
        });

        if (shouldReinit) {
            debugLog('DOM changes detected, re-initializing...');
            setTimeout(init, 50);
        }
    });

    // Observe sidebar changes
    const sidebar = document.querySelector('.navbar-nav');
    if (sidebar) {
        observer.observe(sidebar, { childList: true, subtree: true });
    }

    // Expose debug function globally for testing
    window.sidebarDebug = {
        getCurrentPath: () => currentPath,
        getActiveDropdown: findActiveDropdown,
        reinit: init,
        config: config
    };

    debugLog('Enhanced Sidebar Navigation Fix loaded successfully');
});
