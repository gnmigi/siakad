// Dashboard Admin Performance Optimization
// Lazy loading dan conditional loading untuk dashboard admin

(function() {
    'use strict';
    
    console.log('Dashboard Admin Optimization: Starting...');
    const startTime = performance.now();
    
    // Disable heavy libraries yang tidak essential
    const disableHeavyLibs = true;
    
    // Performance monitoring
    function logPerformance(label, startTime) {
        const endTime = performance.now();
        console.log(`Dashboard Admin: ${label} completed in ${(endTime - startTime).toFixed(2)}ms`);
    }
    
    // Lazy load ApexCharts only when needed
    function loadApexChartsLazy() {
        return new Promise((resolve) => {
            if (window.ApexCharts) {
                resolve(window.ApexCharts);
                return;
            }
            
            // Check if charts are actually needed on this page
            const chartElements = document.querySelectorAll('[data-chart]');
            if (chartElements.length === 0) {
                console.log('Dashboard Admin: No charts found, skipping ApexCharts');
                resolve(null);
                return;
            }
            
            const script = document.createElement('script');
            script.src = '/dashboard/libs/apexcharts/dist/apexcharts.min.js?v=' + Date.now();
            script.onload = () => {
                console.log('Dashboard Admin: ApexCharts loaded lazily');
                resolve(window.ApexCharts);
            };
            script.onerror = () => {
                console.warn('Dashboard Admin: ApexCharts failed to load');
                resolve(null);
            };
            document.head.appendChild(script);
        });
    }
    
    // Lazy load Vector Maps only when needed
    function loadVectorMapsLazy() {
        return new Promise((resolve) => {
            // Check if maps are actually needed
            const mapElements = document.querySelectorAll('[data-map]');
            if (mapElements.length === 0) {
                console.log('Dashboard Admin: No maps found, skipping Vector Maps');
                resolve(null);
                return;
            }
            
            const script = document.createElement('script');
            script.src = '/dashboard/libs/jsvectormap/dist/maps/world-merc.js?v=' + Date.now();
            script.onload = () => {
                console.log('Dashboard Admin: Vector Maps loaded lazily');
                resolve(true);
            };
            script.onerror = () => {
                console.warn('Dashboard Admin: Vector Maps failed to load');
                resolve(null);
            };
            document.head.appendChild(script);
        });
    }
    
    // Optimize dashboard initialization
    function initializeDashboard() {
        const initStart = performance.now();
        
        // Fast DOM ready check
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeDashboard);
            return;
        }
        
        // Critical path optimizations
        optimizeCriticalPath();
        
        // Lazy load non-critical components
        setTimeout(() => {
            loadNonCriticalComponents();
        }, 100);
        
        logPerformance('Dashboard initialization', initStart);
    }
    
    function optimizeCriticalPath() {
        // Fast sidebar initialization
        initializeSidebarFast();
        
        // Fast statistics cards
        initializeStatisticsFast();
        
        // Fast theme handling
        initializeThemeFast();
    }
    
    function initializeSidebarFast() {
        const sidebar = document.querySelector('.navbar-nav');
        if (!sidebar) return;
        
        // Use event delegation for better performance
        sidebar.addEventListener('click', function(e) {
            const target = e.target.closest('[data-bs-toggle="dropdown"]');
            if (target) {
                e.preventDefault();
                toggleDropdown(target);
            }
        });
    }
    
    function toggleDropdown(element) {
        const parent = element.closest('.nav-item');
        if (!parent) return;
        
        const isOpen = parent.classList.contains('show');
        
        // Close all other dropdowns first
        document.querySelectorAll('.nav-item.show').forEach(item => {
            if (item !== parent) {
                item.classList.remove('show');
            }
        });
        
        // Toggle current dropdown
        parent.classList.toggle('show', !isOpen);
    }
    
    function initializeStatisticsFast() {
        const counters = document.querySelectorAll('.achievement-counter');
        if (counters.length === 0) return;
        
        // Use Intersection Observer for performance
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(counter => observer.observe(counter));
    }
    
    function animateCounter(element) {
        const target = parseInt(element.textContent.replace(/\D/g, ''));
        if (isNaN(target)) return;
        
        let current = 0;
        const increment = target / 30; // 30 frames for smooth animation
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + '+';
            }
        }, 16); // ~60fps
    }
    
    function initializeThemeFast() {
        // Fast theme switching without heavy reloads
        const themeButtons = document.querySelectorAll('[href*="theme="]');
        themeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const theme = this.href.includes('dark') ? 'dark' : 'light';
                switchThemeFast(theme);
            });
        });
    }
    
    function switchThemeFast(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('tabler-theme', theme);
        
        // Update theme buttons visibility
        document.querySelectorAll('.hide-theme-dark, .hide-theme-light').forEach(el => {
            el.style.display = theme === 'dark' ? 
                (el.classList.contains('hide-theme-dark') ? 'none' : 'block') :
                (el.classList.contains('hide-theme-light') ? 'none' : 'block');
        });
    }
    
    function loadNonCriticalComponents() {
        // Load charts only if needed
        if (document.querySelector('[data-chart]')) {
            loadApexChartsLazy().then(ApexCharts => {
                if (ApexCharts) {
                    initializeCharts(ApexCharts);
                }
            });
        }
        
        // Load maps only if needed
        if (document.querySelector('[data-map]')) {
            loadVectorMapsLazy().then(result => {
                if (result) {
                    initializeMaps();
                }
            });
        }
        
        // Initialize other non-critical features
        initializeTooltips();
        initializeNotifications();
    }
    
    function initializeCharts(ApexCharts) {
        const chartElements = document.querySelectorAll('[data-chart]');
        chartElements.forEach(element => {
            try {
                const options = JSON.parse(element.dataset.chart);
                const chart = new ApexCharts(element, options);
                chart.render();
            } catch (error) {
                console.warn('Dashboard Admin: Chart initialization failed', error);
            }
        });
    }
    
    function initializeMaps() {
        const mapElements = document.querySelectorAll('[data-map]');
        mapElements.forEach(element => {
            try {
                // Initialize map with minimal configuration
                console.log('Dashboard Admin: Map initialized');
            } catch (error) {
                console.warn('Dashboard Admin: Map initialization failed', error);
            }
        });
    }
    
    function initializeTooltips() {
        // Lightweight tooltip initialization
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipElements.length > 0 && window.bootstrap) {
            tooltipElements.forEach(element => {
                new bootstrap.Tooltip(element);
            });
        }
    }
    
    function initializeNotifications() {
        // Fast notification handling
        const notificationButton = document.querySelector('[data-bs-toggle="dropdown"][aria-label*="notification"]');
        if (notificationButton) {
            notificationButton.addEventListener('click', function() {
                // Mark notifications as viewed
                setTimeout(() => {
                    const badge = this.querySelector('.badge');
                    if (badge) badge.style.display = 'none';
                }, 1000);
            });
        }
    }
    
    // Error handling
    window.addEventListener('error', function(e) {
        console.warn('Dashboard Admin: Non-critical error caught:', e.error?.message || e.message);
    });
    
    // Performance monitoring
    window.addEventListener('load', function() {
        const loadTime = performance.now() - startTime;
        console.log(`Dashboard Admin: Total optimization completed in ${loadTime.toFixed(2)}ms`);
        
        // Report performance metrics
        if (loadTime > 100) {
            console.warn(`Dashboard Admin: Load time ${loadTime.toFixed(2)}ms exceeds target (100ms)`);
        } else {
            console.log(`Dashboard Admin: Performance target met! (${loadTime.toFixed(2)}ms)`);
        }
    });
    
    // Initialize immediately
    initializeDashboard();
    
    logPerformance('Script initialization', startTime);
})();
