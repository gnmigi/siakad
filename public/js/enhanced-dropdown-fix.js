/**
 * Enhanced Dropdown Navigation Fix
 * Combines the best of both worlds:
 * - Maintains all enhancement features (auto-open, highlighting, animations)
 * - Fixes JavaScript conflicts that prevent dropdowns from working
 * - Safe initialization that doesn't break existing functionality
 */

(function () {
    'use strict';

    // Configuration
    const config = {
        storageKey: 'sidebar_dropdown_state',
        activeClass: 'active',
        dropdownActiveClass: 'dropdown-active',
        highlightClass: 'menu-highlight'
    };

    // Get current URL path for route matching
    const currentPath = window.location.pathname;
    const currentRoute = window.location.href;

    /**
     * Route patterns mapping to dropdown identifiers
     */
    const routePatterns = {
        'pengguna': [
            '/pengguna/users',
            '/pengguna/dosen',
            '/pengguna/mahasiswa'
        ],
        'publikasi': [
            '/publikasi/kategori',
            '/publikasi/berita',
            '/publikasi/pengumuman',
            '/publikasi/galeri',
            '/publikasi/program-studi',
            '/institusi/profile',
            '/institusi/vision-mission',
            '/institusi/structure',
            '/institusi/facilities'
        ],
        'akademik': [
            '/akademik/taka',
            '/akademik/prodi',
            '/akademik/kurikulum',
            '/akademik/mata-kuliah',
            '/akademik/jenis-kelas',
            '/akademik/kelas',
            '/akademik/waktu-kuliah',
            '/akademik/jadwal-kuliah',
            '/akademik/graduation-settings'
        ],
        'data-akademik': [
            '/akademik/data-akademik-mahasiswa',
            '/akademik/bobot-nilai',
            '/akademik/krs',
            '/akademik/dosen-penasehat'
        ],
        'pmb': [
            '/pmb/management-index'
        ],
        'keuangan': [
            '/keuangan/saldo',
            '/keuangan/tagihan-kuliah-group',
            '/keuangan/tagihan-individual',
            '/keuangan/payment-methods',
            '/keuangan/bank-accounts',
            '/keuangan/billing-templates',
            '/keuangan/pembayaran-manual'
        ],
        'infrastruktur': [
            '/infrastruktur/gedung',
            '/infrastruktur/ruang',
            '/infrastruktur/kategori-barang',
            '/infrastruktur/barang',
            '/infrastruktur/mutasi-barang',
            '/infrastruktur/pengadaan-barang',
            '/infrastruktur/inventaris-barang'
        ],
        'pengaturan': [
            '/pengaturan/log-aktivitas',
            '/pengaturan/web-settings',
            '/pengaturan/homepage'
        ]
    };

    /**
     * Find which dropdown group contains the current route
     */
    function findActiveDropdown() {
        for (const [dropdownKey, patterns] of Object.entries(routePatterns)) {
            for (const pattern of patterns) {
                if (currentPath.includes(pattern)) {
                    return dropdownKey;
                }
            }
        }
        return null;
    }

    /**
     * Add enhanced styling for active states and dropdown fixes
     */
    function addEnhancedStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* CRITICAL: Only target sidebar navigation dropdowns, not search dropdowns or modals */
            .navbar-vertical .nav-item.dropdown .dropdown-menu {
                z-index: 1051 !important;
                position: static !important;
                display: none !important;
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Do NOT interfere with other dropdown menus (like search results, user menu, notifications) */
            .dropdown-menu:not(.navbar-vertical .nav-item.dropdown .dropdown-menu) {
                /* Let other dropdowns work normally with proper positioning */
                position: absolute !important;
                z-index: 1055 !important;
            }
            
            /* Mobile-specific fixes to prevent content pushing - ULTRA AGGRESSIVE APPROACH */
            @media (max-width: 768px) {
                /* FORCE ALL dropdowns to be fixed positioned on mobile - NUCLEAR OPTION */
                .dropdown-menu:not(.navbar-vertical .dropdown-menu),
                .dropdown-menu.show:not(.navbar-vertical .dropdown-menu) {
                    position: fixed !important;
                    top: auto !important;
                    bottom: auto !important;
                    right: 1rem !important;
                    left: auto !important;
                    z-index: 9999 !important;
                    transform: none !important;
                    margin: 0 !important;
                    width: auto !important;
                    min-width: 200px !important;
                    max-width: calc(100vw - 2rem) !important;
                    background-color: var(--tblr-bg-surface) !important;
                    border: 1px solid var(--tblr-border-color) !important;
                    border-radius: var(--tblr-border-radius) !important;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.3) !important;
                    display: block !important;
                }
                
                /* Calculate position dynamically based on trigger */
                .nav-item.dropdown.show:not(.navbar-vertical .nav-item.dropdown) .dropdown-menu {
                    top: calc(var(--dropdown-top, 60px) + 0.5rem) !important;
                }
                
                /* Force ALL non-sidebar dropdown cards to be fixed positioned */
                .dropdown-menu-card:not(.navbar-vertical .dropdown-menu-card) {
                    position: fixed !important;
                    top: calc(var(--dropdown-top, 60px) + 0.5rem) !important;
                    right: 1rem !important;
                    left: auto !important;
                    z-index: 9999 !important;
                    margin: 0 !important;
                    max-width: calc(100vw - 2rem) !important;
                }
                
                /* Ensure ALL non-sidebar dropdown containers don't affect layout */
                .nav-item.dropdown:not(.navbar-vertical .nav-item.dropdown) {
                    position: relative !important;
                }
                
                /* Notification dropdown mobile fix */
                .dropdown-menu-card .card:not(.navbar-vertical .dropdown-menu-card .card) {
                    max-width: 100% !important;
                    margin: 0 !important;
                }
                
                /* ULTRA FORCE sidebar dropdown to stay static - NUCLEAR OVERRIDE */
                .navbar-vertical .nav-item.dropdown .dropdown-menu,
                .navbar-vertical .nav-item.dropdown .dropdown-menu.show {
                    position: static !important;
                    background: transparent !important;
                    border: none !important;
                    box-shadow: none !important;
                    z-index: auto !important;
                    transform: none !important;
                    margin: 0 !important;
                    width: auto !important;
                    max-width: none !important;
                    top: auto !important;
                    right: auto !important;
                    left: auto !important;
                    bottom: auto !important;
                    display: none !important;
                }
                
                .navbar-vertical .nav-item.dropdown .dropdown-menu.show {
                    display: block !important;
                    position: static !important;
                }
                
                .navbar-vertical .nav-item.dropdown {
                    position: static !important;
                }
                
                /* Add backdrop for mobile dropdowns */
                .nav-item.dropdown.show:not(.navbar-vertical .nav-item.dropdown)::before {
                    content: '';
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    background: rgba(0,0,0,0.1) !important;
                    z-index: 9998 !important;
                    pointer-events: none !important;
                }
            }
            
            /* Extra small mobile devices - ONLY for header navbar */
            @media (max-width: 480px) {
                .page > header.navbar .dropdown-menu,
                .page > header.navbar .dropdown-menu-card,
                .navbar.navbar-expand-md.d-none.d-lg-flex .dropdown-menu,
                .navbar.navbar-expand-md.d-none.d-lg-flex .dropdown-menu-card,
                .navbar-nav .dropdown-menu,
                .navbar-nav .dropdown-menu-card {
                    max-width: 95vw !important;
                    right: 0.5rem !important;
                }
                
                .page > header.navbar .nav-item.dropdown .dropdown-menu-card .card,
                .navbar.navbar-expand-md.d-none.d-lg-flex .nav-item.dropdown .dropdown-menu-card .card,
                .navbar-nav .nav-item.dropdown .dropdown-menu-card .card {
                    max-width: 90vw !important;
                }
            }
            
            /* Ensure modals work properly - DO NOT interfere with Bootstrap modals */
            .modal {
                z-index: 1055 !important;
                pointer-events: auto !important;
            }
            
            .modal-backdrop {
                z-index: 1050 !important;
                pointer-events: auto !important;
            }
            
            /* Ensure modal dialogs are visible and clickable */
            .modal-dialog {
                z-index: 1056 !important;
                pointer-events: auto !important;
            }
            
            /* Ensure modal close buttons are clickable */
            .modal .btn-close {
                pointer-events: auto !important;
                z-index: 1057 !important;
                position: relative !important;
            }
            
            /* Ensure modal content is interactive */
            .modal-content {
                pointer-events: auto !important;
                position: relative !important;
                z-index: 1056 !important;
            }
            
            /* Ensure modal header buttons work */
            .modal-header .btn-close {
                pointer-events: auto !important;
                cursor: pointer !important;
                opacity: 1 !important;
            }
            
            /* Show sidebar dropdown when active */
            .navbar-vertical .nav-item.dropdown .dropdown-menu.show {
                display: block !important;
            }
            
            /* Ensure dropdown items are properly styled */
            .navbar-vertical .dropdown-item {
                pointer-events: auto !important;
                display: block !important;
                padding: 0.5rem 1rem !important;
                color: rgba(255, 255, 255, 0.8) !important;
                background: transparent !important;
                border: none !important;
                text-decoration: none !important;
                font-size: 0.875rem !important;
                line-height: 1.5 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            /* Fix dropdown toggle cursor */
            .dropdown-toggle {
                cursor: pointer !important;
            }
            
            /* Prevent dropdown conflicts with page content */
            .dropdown-menu.show {
                display: block !important;
            }
            
            /* Fix for overlapping text in sidebar */
            .navbar-vertical .nav-item.dropdown .dropdown-menu {
                margin-left: 0 !important;
                padding-left: 1rem !important;
            }
            
            /* Ensure proper spacing for dropdown items */
            .navbar-vertical .dropdown-item {
                margin: 0 !important;
                width: 100% !important;
            }

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

            /* Pulse animation for active dropdown */
            .dropdown-active > .nav-link {
                animation: subtle-pulse 2s infinite;
            }

            @keyframes subtle-pulse {
                0%, 100% { 
                    box-shadow: 0 0 0 0 rgba(var(--tblr-primary-rgb), 0.4);
                }
                50% { 
                    box-shadow: 0 0 0 4px rgba(var(--tblr-primary-rgb), 0.1);
                }
            }

            /* Badge styling for dropdown items */
            .dropdown-item .badge {
                margin-left: auto;
            }
        `;

        // Remove existing style if it exists
        const existingStyle = document.getElementById('enhanced-dropdown-styles');
        if (existingStyle) {
            existingStyle.remove();
        }

        style.id = 'enhanced-dropdown-styles';
        document.head.appendChild(style);
    }

    /**
     * Safe Bootstrap dropdown initialization
     */
    function safeInitializeDropdowns() {
        // Only initialize if Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap not found, skipping dropdown initialization');
            return;
        }

        const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]:not([data-enhanced-initialized])');

        dropdownElements.forEach(function (element) {
            try {
                // Mark as initialized to prevent double initialization
                element.setAttribute('data-enhanced-initialized', 'true');

                // Only create new instance if none exists
                if (!bootstrap.Dropdown.getInstance(element)) {
                    new bootstrap.Dropdown(element, {
                        autoClose: 'outside',
                        boundary: 'viewport'
                    });
                }
            } catch (e) {
                // Silently ignore errors to prevent breaking existing functionality
                console.debug('Dropdown initialization skipped for element:', element);
            }
        });
    }

    /**
     * Open dropdown and highlight active items
     */
    function activateDropdown(dropdownKey) {
        // Find dropdown by data attribute
        const dropdown = document.querySelector(`[data-dropdown-group="${dropdownKey}"]`);

        if (!dropdown) {
            console.debug(`Dropdown group "${dropdownKey}" not found`);
            return;
        }

        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');

        if (!dropdownToggle || !dropdownMenu) return;

        // Check if this dropdown contains the active route
        const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
        let hasActiveItem = false;

        dropdownItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href.split('/').pop())) {
                hasActiveItem = true;
                item.classList.add('active');
            }
        });

        // If this dropdown has the active item, open it and mark as active
        if (hasActiveItem) {
            dropdown.classList.add(config.dropdownActiveClass);

            // Show the dropdown menu
            dropdownMenu.classList.add('show');
            dropdownToggle.setAttribute('aria-expanded', 'true');

            // Store state
            localStorage.setItem(config.storageKey, dropdownKey);

            console.log(`Activated dropdown: ${dropdownKey}`);
        }
    }

    /**
     * Enhanced active state for current page
     */
    function highlightCurrentPage() {
        // Find and highlight the exact current page link
        const allLinks = document.querySelectorAll('.nav-link, .dropdown-item');

        allLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && (currentRoute.includes(href) || currentPath === href)) {
                if (link.classList.contains('dropdown-item')) {
                    link.classList.add('active');
                } else if (!link.classList.contains('dropdown-toggle')) {
                    link.classList.add('active');
                }
            }
        });
    }

    /**
     * Setup safe event handlers for dropdown toggles
     */
    function setupSafeDropdownHandlers() {
        // Use event delegation to avoid conflicts
        document.addEventListener('click', function (e) {
            const dropdownToggle = e.target.closest('[data-bs-toggle="dropdown"]');

            if (dropdownToggle) {
                // Let Bootstrap handle the dropdown, just update our visual state
                setTimeout(() => {
                    const dropdown = dropdownToggle.closest('.nav-item.dropdown');
                    const dropdownMenu = dropdown ? dropdown.querySelector('.dropdown-menu') : null;

                    if (dropdown && dropdownMenu) {
                        if (dropdownMenu.classList.contains('show')) {
                            dropdown.classList.add(config.dropdownActiveClass);

                            // FORCE mobile dropdown positioning with JavaScript
                            if (window.innerWidth <= 768 && !dropdown.closest('.navbar-vertical')) {
                                forceMobileDropdownPositioning(dropdown, dropdownMenu, dropdownToggle);
                            }
                        } else {
                            dropdown.classList.remove(config.dropdownActiveClass);
                        }
                    }
                }, 10);
            }
        });
    }

    /**
     * Force mobile dropdown positioning with JavaScript - ULTRA SIMPLE
     */
    function forceMobileDropdownPositioning(dropdown, dropdownMenu, dropdownToggle) {
        console.log('Forcing mobile dropdown positioning...');

        // Get trigger button position
        const triggerRect = dropdownToggle.getBoundingClientRect();

        // BRUTE FORCE: Override ALL positioning with inline styles
        const cssText = `
            position: fixed !important;
            top: ${triggerRect.bottom + 5}px !important;
            right: 1rem !important;
            left: auto !important;
            z-index: 99999 !important;
            transform: none !important;
            margin: 0 !important;
            width: auto !important;
            min-width: 200px !important;
            max-width: calc(100vw - 2rem) !important;
            background: #1e293b !important;
            border: 1px solid #334155 !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3) !important;
            display: block !important;
        `;

        dropdownMenu.style.cssText = cssText;

        console.log('Mobile dropdown positioned at:', {
            top: triggerRect.bottom + 5,
            right: '1rem',
            zIndex: 99999
        });

        // Simple backdrop
        let backdrop = document.querySelector('.mobile-dropdown-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'mobile-dropdown-backdrop';
            backdrop.style.cssText = `
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: rgba(0,0,0,0.1) !important;
                z-index: 99998 !important;
                pointer-events: none !important;
            `;
            document.body.appendChild(backdrop);
        }

        // Remove backdrop when clicked outside
        setTimeout(() => {
            const removeBackdrop = () => {
                const existingBackdrop = document.querySelector('.mobile-dropdown-backdrop');
                if (existingBackdrop) {
                    existingBackdrop.remove();
                }
            };

            // Check if dropdown is still open every 100ms
            const checkInterval = setInterval(() => {
                if (!dropdownMenu.classList.contains('show')) {
                    removeBackdrop();
                    clearInterval(checkInterval);
                }
            }, 100);

            // Also remove after 10 seconds as failsafe
            setTimeout(() => {
                removeBackdrop();
                clearInterval(checkInterval);
            }, 10000);
        }, 100);
    }

    /**
     * Restore dropdown state from localStorage
     */
    function restoreDropdownState() {
        const savedState = localStorage.getItem(config.storageKey);
        if (savedState) {
            // Only restore if the current page is still in the same dropdown group
            const currentDropdown = findActiveDropdown();
            if (currentDropdown === savedState) {
                activateDropdown(savedState);
            } else if (currentDropdown) {
                // Update to new dropdown if route changed
                activateDropdown(currentDropdown);
            }
        }
    }

    /**
     * Initialize the enhanced dropdown system
     */
    function init() {
        console.log('Initializing Enhanced Dropdown Navigation Fix...');

        // Add enhanced styles (includes critical dropdown fixes)
        addEnhancedStyles();

        // Safe Bootstrap dropdown initialization
        safeInitializeDropdowns();

        // Find and activate the appropriate dropdown
        const activeDropdown = findActiveDropdown();
        if (activeDropdown) {
            activateDropdown(activeDropdown);
        } else {
            // Restore previous state if no active dropdown found
            restoreDropdownState();
        }

        // Highlight current page
        highlightCurrentPage();

        // Setup safe event handlers
        setupSafeDropdownHandlers();

        console.log('Enhanced Dropdown Navigation Fix initialized successfully');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-initialize on window load as backup
    window.addEventListener('load', function () {
        setTimeout(init, 100);
    });

    // Re-initialize on page navigation (for SPA-like behavior)
    window.addEventListener('popstate', init);

    // Debug info
    console.log('Current path:', currentPath);
    console.log('Active dropdown should be:', findActiveDropdown());

})();
