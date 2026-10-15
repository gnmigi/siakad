/**
 * Homepage Optimized JavaScript
 * Performance-focused implementation with lazy loading and error handling
 */

(function() {
    'use strict';

    // Performance monitoring
    const perfStart = performance.now();

    // Utility functions
    const utils = {
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        isElementInViewport: function(el) {
            if (!el) return false;
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    };

    // Counter Animation with Performance Optimization
    const animateCounter = (counter) => {
        if (!counter || !counter.innerText) return;
        
        const target = parseInt(counter.innerText.replace(/\D/g, '')) || 0;
        if (target === 0) return;
        
        let count = 0;
        const duration = 2000;
        const increment = target / (duration / 16);
        
        const updateCount = () => {
            count += increment;
            if (count < target) {
                counter.innerText = Math.ceil(count) + '+';
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target + '+';
            }
        };
        
        requestAnimationFrame(updateCount);
    };

    // Intersection Observer for Animations
    const createIntersectionObserver = (elements, className, callback = null) => {
        if (!window.IntersectionObserver) {
            // Fallback for older browsers
            elements.forEach(el => {
                el.classList.add(className);
                if (callback) callback(el);
            });
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(className);
                    if (callback) callback(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(el => observer.observe(el));
    };

    // Slider Implementation with Touch Support
    class OptimizedSlider {
        constructor(trackId, options = {}) {
            this.track = document.getElementById(trackId);
            if (!this.track || this.track.children.length === 0) return;

            this.options = {
                itemsPerView: {
                    mobile: 1,
                    tablet: 2,
                    desktop: 3
                },
                autoSlide: options.autoSlide || false,
                autoSlideInterval: options.autoSlideInterval || 4000,
                ...options
            };

            this.currentSlide = 0;
            this.totalSlides = this.track.children.length;
            this.itemsPerView = this.getItemsPerView();
            this.maxSlide = Math.max(0, this.totalSlides - this.itemsPerView);

            this.init();
        }

        getItemsPerView() {
            const width = window.innerWidth;
            if (width >= 992) return this.options.itemsPerView.desktop;
            if (width >= 768) return this.options.itemsPerView.tablet;
            return this.options.itemsPerView.mobile;
        }

        init() {
            this.setupNavigation();
            this.setupDots();
            this.setupTouchEvents();
            this.setupKeyboardEvents();
            
            if (this.options.autoSlide && window.innerWidth < 768) {
                this.startAutoSlide();
            }

            // Handle window resize
            window.addEventListener('resize', utils.debounce(() => {
                const newItemsPerView = this.getItemsPerView();
                if (newItemsPerView !== this.itemsPerView) {
                    this.itemsPerView = newItemsPerView;
                    this.maxSlide = Math.max(0, this.totalSlides - this.itemsPerView);
                    this.currentSlide = Math.min(this.currentSlide, this.maxSlide);
                    this.updateSlider();
                }
            }, 250));
        }

        setupNavigation() {
            const prevBtn = document.getElementById(this.options.prevBtnId);
            const nextBtn = document.getElementById(this.options.nextBtnId);

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.prevSlide());
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.nextSlide());
            }
        }

        setupDots() {
            const dots = document.querySelectorAll(this.options.dotsSelector);
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    this.currentSlide = Math.min(index, this.maxSlide);
                    this.updateSlider();
                });
            });
        }

        setupTouchEvents() {
            let startX = 0;
            let startY = 0;
            let isDragging = false;
            let hasMovedHorizontally = false;

            this.track.addEventListener('touchstart', (e) => {
                if (e.touches && e.touches[0]) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                    isDragging = true;
                    hasMovedHorizontally = false;
                }
            }, { passive: true });

            this.track.addEventListener('touchmove', (e) => {
                if (!isDragging || !e.touches || !e.touches[0]) return;
                
                const currentX = e.touches[0].clientX;
                const currentY = e.touches[0].clientY;
                const diffX = Math.abs(currentX - startX);
                const diffY = Math.abs(currentY - startY);
                
                if (diffX > diffY && diffX > 10) {
                    hasMovedHorizontally = true;
                    e.preventDefault();
                } else if (diffY > diffX && diffY > 10) {
                    isDragging = false;
                    hasMovedHorizontally = false;
                }
            }, { passive: false });

            this.track.addEventListener('touchend', (e) => {
                if (!isDragging || !hasMovedHorizontally || !e.changedTouches || !e.changedTouches[0]) return;
                
                isDragging = false;
                const endX = e.changedTouches[0].clientX;
                const diffX = startX - endX;
                
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        this.nextSlide();
                    } else {
                        this.prevSlide();
                    }
                }
                
                hasMovedHorizontally = false;
            }, { passive: true });
        }

        setupKeyboardEvents() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    this.prevSlide();
                } else if (e.key === 'ArrowRight') {
                    this.nextSlide();
                }
            });
        }

        updateSlider() {
            const translateX = -(this.currentSlide * (100 / this.itemsPerView));
            this.track.style.transform = `translateX(${translateX}%)`;
            
            // Update dots
            const dots = document.querySelectorAll(this.options.dotsSelector);
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentSlide);
            });
        }

        nextSlide() {
            this.currentSlide = this.currentSlide >= this.maxSlide ? 0 : this.currentSlide + 1;
            this.updateSlider();
        }

        prevSlide() {
            this.currentSlide = this.currentSlide <= 0 ? this.maxSlide : this.currentSlide - 1;
            this.updateSlider();
        }

        startAutoSlide() {
            setInterval(() => {
                this.nextSlide();
            }, this.options.autoSlideInterval);
        }
    }

    // Lazy Loading Implementation
    const lazyLoadImages = () => {
        if (!window.IntersectionObserver) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    };

    // Smooth Scroll Implementation
    const setupSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };

    // Chat Widget Functionality
    const setupChatWidget = () => {
        window.toggleChatPopup = function() {
            const popup = document.getElementById('chatPopup');
            if (popup) {
                popup.classList.toggle('show');
            }
        };

        window.sendWhatsApp = function(event) {
            event.preventDefault();
            const name = document.getElementById('name')?.value || '';
            const whatsapp = document.getElementById('whatsapp')?.value || '';
            const message = document.getElementById('message')?.value || '';

            if (!name || !whatsapp || !message) {
                alert('Mohon lengkapi semua field');
                return;
            }

            const formattedMessage = `Halo, saya ${name}\n\n${message}`;
            const whatsappUrl = `https://wa.me/${whatsapp}?text=${encodeURIComponent(formattedMessage)}`;

            window.open(whatsappUrl, '_blank');
            event.target.reset();
            toggleChatPopup();
        };
    };

    // Main Initialization
    const init = () => {
        try {
            // Initialize animations
            const counters = document.querySelectorAll('.achievement-counter');
            const featureCards = document.querySelectorAll('.feature-card');
            const newsCards = document.querySelectorAll('.news-card');

            createIntersectionObserver(counters, 'visible', animateCounter);
            createIntersectionObserver(featureCards, 'visible');
            createIntersectionObserver(newsCards, 'visible');

            // Initialize sliders
            new OptimizedSlider('galleryTrack', {
                prevBtnId: 'galleryPrev',
                nextBtnId: 'galleryNext',
                dotsSelector: '.gallery-dot',
                autoSlide: true
            });

            new OptimizedSlider('programTrack', {
                prevBtnId: 'programPrev',
                nextBtnId: 'programNext',
                dotsSelector: '.program-dot',
                autoSlide: true,
                autoSlideInterval: 5000
            });

            // Initialize other features
            lazyLoadImages();
            setupSmoothScroll();
            setupChatWidget();

            // Performance logging
            const perfEnd = performance.now();
            console.log(`Homepage initialized in ${(perfEnd - perfStart).toFixed(2)}ms`);

        } catch (error) {
            console.error('Error initializing homepage:', error);
        }
    };

    // DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
