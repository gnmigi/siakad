/**
 * Enhanced Dropdown Navigation Fix
 * Fixes dropdown conflicts in complex pages like payment forms
 * Ensures Bootstrap dropdowns work properly across all pages
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading Enhanced Dropdown Navigation Fix...');

    // Wait for all other scripts to load
    setTimeout(function() {
        initializeDropdownFix();
    }, 100);
});

function initializeDropdownFix() {
    // Force re-initialize Bootstrap dropdowns
    reinitializeBootstrapDropdowns();
    
    // Fix z-index issues
    fixDropdownZIndex();
    
    // Add event delegation for dropdown toggles
    setupDropdownEventDelegation();
    
    // Monitor for dynamic content changes
    setupMutationObserver();
    
    console.log('Enhanced Dropdown Navigation Fix initialized');
}

function reinitializeBootstrapDropdowns() {
    // Dispose existing dropdown instances to prevent conflicts
    const existingDropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    existingDropdowns.forEach(element => {
        try {
            const existingInstance = bootstrap.Dropdown.getInstance(element);
            if (existingInstance) {
                existingInstance.dispose();
            }
        } catch (e) {
            // Ignore errors for elements without instances
        }
    });

    // Re-initialize all dropdowns
    setTimeout(() => {
        const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownElements.forEach(element => {
            try {
                new bootstrap.Dropdown(element, {
                    autoClose: 'outside',
                    boundary: 'viewport'
                });
            } catch (e) {
                console.warn('Could not initialize dropdown:', e);
            }
        });
    }, 50);
}

function fixDropdownZIndex() {
    // Add CSS to ensure dropdowns appear above other elements
    const style = document.createElement('style');
    style.textContent = `
        /* Force dropdown menus to appear above all other content */
        .dropdown-menu {
            z-index: 9999 !important;
            position: absolute !important;
        }
        
        /* Ensure navbar dropdowns have higher z-index */
        .navbar .dropdown-menu {
            z-index: 10000 !important;
        }
        
        /* Sidebar dropdown menus */
        .navbar-vertical .dropdown-menu {
            z-index: 10001 !important;
        }
        
        /* Fix for overlapping modals */
        .modal {
            z-index: 9998 !important;
        }
        
        /* Ensure dropdown backdrop doesn't interfere */
        .dropdown-backdrop {
            z-index: 9997 !important;
        }
        
        /* Fix dropdown positioning issues */
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
        }
        
        /* Prevent dropdown menu from being hidden by overflow */
        .navbar-nav .dropdown {
            position: static !important;
        }
        
        .navbar-vertical .nav-item.dropdown {
            position: relative !important;
        }
        
        /* Fix for dropdown items being unclickable */
        .dropdown-item {
            pointer-events: auto !important;
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Ensure dropdown toggle works */
        .dropdown-toggle {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Fix for dropdown menu positioning in sidebar */
        .navbar-vertical .dropdown-menu {
            position: absolute !important;
            top: 0 !important;
            left: 100% !important;
            margin-top: 0 !important;
            margin-left: 0.125rem !important;
        }
        
        /* Mobile dropdown fixes */
        @media (max-width: 991.98px) {
            .navbar-vertical .dropdown-menu {
                position: static !important;
                left: auto !important;
                top: auto !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
            }
        }
    `;
    
    // Remove existing style if it exists
    const existingStyle = document.getElementById('dropdown-fix-styles');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    style.id = 'dropdown-fix-styles';
    document.head.appendChild(style);
}

function setupDropdownEventDelegation() {
    // Use event delegation to handle dropdown clicks
    document.addEventListener('click', function(e) {
        const dropdownToggle = e.target.closest('[data-bs-toggle="dropdown"]');
        
        if (dropdownToggle) {
            e.preventDefault();
            e.stopPropagation();
            
            // Force close other dropdowns first
            closeAllDropdowns();
            
            // Toggle this dropdown
            setTimeout(() => {
                try {
                    const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownToggle);
                    dropdown.toggle();
                } catch (error) {
                    console.warn('Dropdown toggle failed:', error);
                    // Fallback: manual toggle
                    manualDropdownToggle(dropdownToggle);
                }
            }, 10);
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
}

function closeAllDropdowns() {
    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
    openDropdowns.forEach(menu => {
        const toggle = menu.previousElementSibling;
        if (toggle && toggle.hasAttribute('data-bs-toggle')) {
            try {
                const dropdown = bootstrap.Dropdown.getInstance(toggle);
                if (dropdown) {
                    dropdown.hide();
                }
            } catch (e) {
                // Manual close
                menu.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
}

function manualDropdownToggle(toggle) {
    const menu = toggle.nextElementSibling;
    if (menu && menu.classList.contains('dropdown-menu')) {
        const isOpen = menu.classList.contains('show');
        
        if (isOpen) {
            menu.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
        } else {
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
        }
    }
}

function setupMutationObserver() {
    // Monitor for dynamic content changes that might affect dropdowns
    const observer = new MutationObserver(function(mutations) {
        let shouldReinitialize = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const addedNodes = Array.from(mutation.addedNodes);
                const hasDropdownElements = addedNodes.some(node => 
                    node.nodeType === 1 && (
                        node.querySelector && node.querySelector('[data-bs-toggle="dropdown"]') ||
                        node.hasAttribute && node.hasAttribute('data-bs-toggle')
                    )
                );
                
                if (hasDropdownElements) {
                    shouldReinitialize = true;
                }
            }
        });
        
        if (shouldReinitialize) {
            setTimeout(reinitializeBootstrapDropdowns, 100);
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// Force fix on window load as well
window.addEventListener('load', function() {
    setTimeout(initializeDropdownFix, 200);
});

// Re-initialize on page visibility change (for browser tab switching)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        setTimeout(initializeDropdownFix, 100);
    }
});
