// Dashboard Optimized JavaScript - Error-Free Version
document.addEventListener('DOMContentLoaded', function() {
    const startTime = performance.now();
    
    try {
        // Enhanced error handling for all functions
        console.log('Dashboard initialization starting...');

        // Safe element selector with null checks
        function safeQuerySelector(selector) {
            try {
                return document.querySelector(selector);
            } catch (error) {
                console.warn(`Selector error for ${selector}:`, error);
                return null;
            }
        }

        function safeQuerySelectorAll(selector) {
            try {
                return document.querySelectorAll(selector);
            } catch (error) {
                console.warn(`Selector error for ${selector}:`, error);
                return [];
            }
        }

        // Counter animation with comprehensive error handling
        const animateCounter = (counter) => {
            if (!counter || !counter.innerText) return;
            
            try {
                const text = counter.innerText.replace(/[^\d]/g, '');
                const target = parseInt(text);
                
                if (isNaN(target) || target === 0) return;
                
                let count = 0;
                const duration = 1500; // Reduced for better performance
                const increment = target / (duration / 16);
                
                const updateCount = () => {
                    try {
                        count += increment;
                        if (count < target) {
                            counter.innerText = Math.ceil(count) + (text !== target.toString() ? '+' : '');
                            requestAnimationFrame(updateCount);
                        } else {
                            counter.innerText = target + (text !== target.toString() ? '+' : '');
                        }
                    } catch (error) {
                        console.warn('Counter update error:', error);
                    }
                };
                updateCount();
            } catch (error) {
                console.warn('Counter animation error:', error);
            }
        };

        // Intersection Observer with error handling
        const createIntersectionObserver = (callback, options = {}) => {
            try {
                const defaultOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px',
                    ...options
                };

                return new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        try {
                            if (entry.isIntersecting && callback) {
                                callback(entry.target);
                            }
                        } catch (error) {
                            console.warn('Intersection callback error:', error);
                        }
                    });
                }, defaultOptions);
            } catch (error) {
                console.warn('IntersectionObserver creation error:', error);
                return null;
            }
        };

        // Initialize counters with error handling
        const initializeCounters = () => {
            try {
                const counters = safeQuerySelectorAll('.achievement-counter, [class*="counter"]');
                if (counters.length === 0) return;

                const observer = createIntersectionObserver((target) => {
                    animateCounter(target);
                    if (observer) observer.unobserve(target);
                });

                if (observer) {
                    counters.forEach(counter => {
                        if (counter) observer.observe(counter);
                    });
                }
            } catch (error) {
                console.warn('Counter initialization error:', error);
            }
        };

        // Card animations with error handling
        const initializeCardAnimations = () => {
            try {
                const cards = safeQuerySelectorAll('.card, .feature-card, .news-card');
                if (cards.length === 0) return;

                const observer = createIntersectionObserver((target) => {
                    target.classList.add('animate-in');
                    if (observer) observer.unobserve(target);
                });

                if (observer) {
                    cards.forEach(card => {
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                            observer.observe(card);
                        }
                    });
                }

                // Add CSS for animation
                const style = document.createElement('style');
                style.textContent = `
                    .animate-in {
                        opacity: 1 !important;
                        transform: translateY(0) !important;
                    }
                `;
                document.head.appendChild(style);
            } catch (error) {
                console.warn('Card animation initialization error:', error);
            }
        };

        // Smooth scroll with error handling
        const initializeSmoothScroll = () => {
            try {
                const anchorLinks = safeQuerySelectorAll('a[href^="#"]');
                
                anchorLinks.forEach(anchor => {
                    if (!anchor) return;
                    
                    anchor.addEventListener('click', function(e) {
                        try {
                            const href = this.getAttribute('href');
                            if (!href || href === '#') return;

                            const target = safeQuerySelector(href);
                            if (target) {
                                e.preventDefault();
                                target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        } catch (error) {
                            console.warn('Smooth scroll error:', error);
                        }
                    });
                });
            } catch (error) {
                console.warn('Smooth scroll initialization error:', error);
            }
        };

        // Theme toggle with error handling
        const initializeThemeToggle = () => {
            try {
                const themeToggle = safeQuerySelector('[data-bs-theme-toggle]');
                if (!themeToggle) return;

                themeToggle.addEventListener('click', function() {
                    try {
                        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        
                        document.documentElement.setAttribute('data-bs-theme', newTheme);
                        localStorage.setItem('theme', newTheme);
                    } catch (error) {
                        console.warn('Theme toggle error:', error);
                    }
                });
            } catch (error) {
                console.warn('Theme toggle initialization error:', error);
            }
        };

        // Payment filter with error handling
        const initializePaymentFilter = () => {
            try {
                const filterButtons = safeQuerySelectorAll('[data-filter]');
                const tableRows = safeQuerySelectorAll('tbody tr');
                
                if (filterButtons.length === 0 || tableRows.length === 0) return;

                console.log('Payment History Filter - Initializing...');

                function filterTable(status) {
                    let visibleRows = 0;
                    
                    tableRows.forEach(row => {
                        if (!row) return;
                        
                        try {
                            const statusCell = row.querySelector('[data-status]');
                            const rowStatus = statusCell ? statusCell.getAttribute('data-status') : '';
                            
                            if (status === '' || rowStatus === status) {
                                row.style.display = '';
                                visibleRows++;
                            } else {
                                row.style.display = 'none';
                            }
                        } catch (error) {
                            console.warn('Row filter error:', error);
                        }
                    });
                    
                    console.log(`Filtering by status: ${status}`);
                    console.log(`Visible rows: ${visibleRows}`);
                }

                // Initialize filter
                filterTable('');

                // Add event listeners
                filterButtons.forEach(button => {
                    if (!button) return;
                    
                    button.addEventListener('click', function() {
                        try {
                            const status = this.getAttribute('data-filter') || '';
                            filterTable(status);
                            
                            // Update active button
                            filterButtons.forEach(btn => {
                                if (btn) btn.classList.remove('active');
                            });
                            this.classList.add('active');
                        } catch (error) {
                            console.warn('Filter button error:', error);
                        }
                    });
                });

                console.log('Payment History Filter - Ready!');
            } catch (error) {
                console.warn('Payment filter initialization error:', error);
            }
        };

        // Form validation with error handling
        const initializeFormValidation = () => {
            try {
                const forms = safeQuerySelectorAll('form[data-validate]');
                
                forms.forEach(form => {
                    if (!form) return;
                    
                    form.addEventListener('submit', function(e) {
                        try {
                            const requiredFields = form.querySelectorAll('[required]');
                            let isValid = true;
                            
                            requiredFields.forEach(field => {
                                if (!field || !field.value.trim()) {
                                    isValid = false;
                                    field.classList.add('is-invalid');
                                } else {
                                    field.classList.remove('is-invalid');
                                }
                            });
                            
                            if (!isValid) {
                                e.preventDefault();
                            }
                        } catch (error) {
                            console.warn('Form validation error:', error);
                        }
                    });
                });
            } catch (error) {
                console.warn('Form validation initialization error:', error);
            }
        };

        // Initialize all components
        initializeCounters();
        initializeCardAnimations();
        initializeSmoothScroll();
        initializeThemeToggle();
        initializePaymentFilter();
        initializeFormValidation();

        // Performance logging
        const endTime = performance.now();
        const loadTime = (endTime - startTime).toFixed(2);
        console.log(`Dashboard initialized successfully in ${loadTime}ms`);

        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('dashboardReady', {
            detail: { loadTime: loadTime }
        }));

    } catch (error) {
        console.error('Dashboard initialization failed:', error);
        
        // Fallback initialization
        setTimeout(() => {
            try {
                console.log('Attempting fallback initialization...');
                const counters = document.querySelectorAll('.achievement-counter');
                counters.forEach(counter => {
                    if (counter && counter.innerText) {
                        const target = parseInt(counter.innerText.replace(/\D/g, ''));
                        if (!isNaN(target)) {
                            counter.innerText = target + '+';
                        }
                    }
                });
            } catch (fallbackError) {
                console.error('Fallback initialization failed:', fallbackError);
            }
        }, 100);
    }
});

// Global error handler
window.addEventListener('error', function(e) {
    console.warn('Global JavaScript error caught:', e.error);
    return true; // Prevent default error handling
});

// Unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(e) {
    console.warn('Unhandled promise rejection:', e.reason);
    e.preventDefault(); // Prevent default error handling
});
