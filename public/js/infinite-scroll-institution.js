/**
 * Infinite Scroll Institution Pages
 * Similar to manga website functionality
 */

class InfiniteScrollInstitution {
    constructor() {
        this.currentPage = 'profile';
        this.isLoading = false;
        this.loadedPages = new Set(); // Don't assume profile is loaded
        this.pageOrder = ['profile', 'vision-mission', 'structure', 'facilities', 'gallery'];
        this.scrollThreshold = 0.7; // Load next page when 70% scrolled
        this.topScrollThreshold = 0.2; // Load previous page when scrolled to top 20%

        this.init();
    }

    init() {
        this.bindEvents();

        // Check if we have a hash target first
        if (window.location.hash) {
            const targetSection = window.location.hash.substring(1);
            if (this.pageOrder.includes(targetSection)) {
                // Load target page directly, don't load profile
                this.loadPageAndScrollImmediate(targetSection);
                return; // Skip the default profile setup
            }
        }

        // Default behavior - load profile
        this.loadedPages.add('profile');
        this.handleInitialHash();
        this.updateURL('profile');
        this.updatePageIndicator('profile');
        this.updateScrollProgress();
    }

    bindEvents() {
        // Scroll event for infinite loading
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 100));

        // Page indicator clicks
        document.querySelectorAll('.page-indicator .nav-item').forEach(navItem => {
            navItem.addEventListener('click', (e) => {
                const page = e.currentTarget.dataset.page;
                this.scrollToPage(page);
            });
        });

        // Browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.scrollToPage(e.state.page, false);
            }
        });

        // Update scroll progress on scroll
        window.addEventListener('scroll', this.throttle(this.updateScrollProgress.bind(this), 50));
    }

    handleInitialHash() {
        // Check if there's a hash in the URL when page loads
        if (window.location.hash) {
            const targetSection = window.location.hash.substring(1); // Remove #
            if (this.pageOrder.includes(targetSection)) {
                // Load target page immediately without delay
                this.loadPageAndScrollImmediate(targetSection);
            }
        }
    }

    async loadPageAndScrollImmediate(pageType) {
        if (this.loadedPages.has(pageType)) {
            // If already loaded, scroll immediately
            const section = document.getElementById(`section-${pageType}`);
            if (section) {
                section.scrollIntoView({ behavior: 'auto', block: 'start' });
                this.currentPage = pageType;
                this.updateURL(pageType);
                this.updatePageIndicator(pageType);
            }
            return;
        }

        // Show loading but don't block UI
        this.showLoadingIndicator();

        try {
            const response = await fetch(`/api/institution/${pageType}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Insert page in correct position
                this.insertPageInOrder(pageType, data.html, data.title);
                this.loadedPages.add(pageType);

                // Scroll immediately without animation delay
                const section = document.getElementById(`section-${pageType}`);
                if (section) {
                    section.scrollIntoView({ behavior: 'auto', block: 'start' });
                    this.currentPage = pageType;
                    this.updateURL(pageType);
                    this.updatePageIndicator(pageType);
                }
            }

        } catch (error) {
            console.error('Error loading page:', error);
            this.showErrorMessage('Gagal memuat halaman. Silakan coba lagi.');
        } finally {
            this.hideLoadingIndicator();
        }
    }

    handleScroll() {
        if (this.isLoading) return;

        const scrollTop = window.pageYOffset;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        // Calculate scroll percentage
        const scrollPercentage = (scrollTop + windowHeight) / documentHeight;
        const scrollFromTop = scrollTop / documentHeight;

        // Update current page based on scroll position
        this.updateCurrentPageFromScroll();

        // Load next page if scrolled near bottom
        if (scrollPercentage >= this.scrollThreshold) {
            this.loadNextPage();
        }

        // Load previous page if scrolled near top
        if (scrollFromTop <= this.topScrollThreshold && scrollTop < 300) {
            this.loadPreviousPage();
        }
    }

    updateCurrentPageFromScroll() {
        const sections = document.querySelectorAll('.page-section');
        let currentSection = null;

        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const sectionTop = rect.top;
            const sectionHeight = rect.height;

            // Check if section is in viewport (at least 50% visible)
            if (sectionTop <= window.innerHeight * 0.5 && sectionTop + sectionHeight > window.innerHeight * 0.5) {
                currentSection = section.dataset.page;
            }
        });

        if (currentSection && currentSection !== this.currentPage) {
            this.currentPage = currentSection;
            this.updateURL(currentSection);
            this.updatePageIndicator(currentSection);
        }
    }

    async loadNextPage() {
        const currentIndex = this.pageOrder.indexOf(this.currentPage);
        const nextPageIndex = currentIndex + 1;

        if (nextPageIndex >= this.pageOrder.length) {
            return; // No more pages to load
        }

        const nextPage = this.pageOrder[nextPageIndex];

        if (this.loadedPages.has(nextPage)) {
            return; // Page already loaded
        }

        this.isLoading = true;
        this.showLoadingIndicator();

        try {
            const response = await fetch(`/api/institution/${nextPage}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.appendPageContent(nextPage, data.html, data.title);
                this.loadedPages.add(nextPage);
            } else {
                console.error('Failed to load page:', data.error);
            }

        } catch (error) {
            console.error('Error loading next page:', error);
            this.showErrorMessage('Gagal memuat halaman berikutnya. Silakan coba lagi.');
        } finally {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }

    async loadPreviousPage() {
        // Find the first loaded page in DOM to determine what to load next
        const loadedSections = document.querySelectorAll('.page-section');
        if (loadedSections.length === 0) return;

        // Get the first (topmost) loaded page
        const firstSection = loadedSections[0];
        const firstPageType = firstSection.dataset.page;
        const firstPageIndex = this.pageOrder.indexOf(firstPageType);

        // Calculate what page should be loaded before the first loaded page
        const prevPageIndex = firstPageIndex - 1;

        if (prevPageIndex < 0) {
            return; // No previous pages to load
        }

        const prevPage = this.pageOrder[prevPageIndex];

        if (this.loadedPages.has(prevPage)) {
            return; // Page already loaded
        }

        this.isLoading = true;
        this.showLoadingIndicator();

        // Store current scroll position
        const currentScrollTop = window.pageYOffset;

        try {
            const response = await fetch(`/api/institution/${prevPage}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.prependPageContent(prevPage, data.html, data.title, currentScrollTop);
                this.loadedPages.add(prevPage);
            } else {
                console.error('Failed to load page:', data.error);
            }

        } catch (error) {
            console.error('Error loading previous page:', error);
            this.showErrorMessage('Gagal memuat halaman sebelumnya. Silakan coba lagi.');
        } finally {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }

    appendPageContent(pageType, html, title) {
        const container = document.getElementById('infiniteScrollContainer');

        // Create new section element
        const section = document.createElement('section');
        section.className = 'page-section';
        section.id = `section-${pageType}`;
        section.dataset.page = pageType;
        section.innerHTML = html;

        // Append to container
        container.appendChild(section);

        // Animate in
        setTimeout(() => {
            section.classList.add('visible');
        }, 100);

        console.log(`Loaded page: ${pageType} - ${title}`);
    }

    prependPageContent(pageType, html, title, previousScrollTop) {
        const container = document.getElementById('infiniteScrollContainer');

        // Create new section element
        const section = document.createElement('section');
        section.className = 'page-section';
        section.id = `section-${pageType}`;
        section.dataset.page = pageType;
        section.innerHTML = html;

        // Get current document height before adding content
        const oldHeight = document.documentElement.scrollHeight;

        // Prepend to container (add at the beginning)
        container.insertBefore(section, container.firstChild);

        // Calculate new scroll position to maintain user's view
        const newHeight = document.documentElement.scrollHeight;
        const heightDifference = newHeight - oldHeight;

        // Adjust scroll position to maintain user's current view
        window.scrollTo(0, previousScrollTop + heightDifference);

        // Animate in
        setTimeout(() => {
            section.classList.add('visible');
        }, 100);

        console.log(`Prepended page: ${pageType} - ${title}`);
    }

    scrollToPage(pageType, updateHistory = true) {
        const section = document.getElementById(`section-${pageType}`);

        if (section) {
            // Smooth scroll to section
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            this.currentPage = pageType;

            if (updateHistory) {
                this.updateURL(pageType);
            }

            this.updatePageIndicator(pageType);
        } else {
            // If page not loaded yet, load it first
            this.loadPageAndScroll(pageType);
        }
    }

    async loadPageAndScroll(pageType) {
        if (this.loadedPages.has(pageType)) {
            this.scrollToPage(pageType);
            return;
        }

        this.isLoading = true;
        this.showLoadingIndicator();

        try {
            // Only load the target page for fast navigation
            const response = await fetch(`/api/institution/${pageType}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Insert page in correct position
                this.insertPageInOrder(pageType, data.html, data.title);
                this.loadedPages.add(pageType);

                // Scroll to the newly loaded page
                setTimeout(() => {
                    this.scrollToPage(pageType);
                }, 200);
            } else {
                console.error('Failed to load page:', data.error);
            }

        } catch (error) {
            console.error('Error loading page:', error);
            this.showErrorMessage('Gagal memuat halaman. Silakan coba lagi.');
        } finally {
            this.isLoading = false;
            this.hideLoadingIndicator();
        }
    }

    insertPageInOrder(pageType, html, title) {
        const container = document.getElementById('infiniteScrollContainer');
        const targetIndex = this.pageOrder.indexOf(pageType);

        // Create new section element
        const section = document.createElement('section');
        section.className = 'page-section';
        section.id = `section-${pageType}`;
        section.dataset.page = pageType;
        section.innerHTML = html;

        // Find the correct position to insert
        const existingSections = container.querySelectorAll('.page-section');
        let insertPosition = null;

        for (let existingSection of existingSections) {
            const existingPageType = existingSection.dataset.page;
            const existingIndex = this.pageOrder.indexOf(existingPageType);

            if (existingIndex > targetIndex) {
                insertPosition = existingSection;
                break;
            }
        }

        // Insert in correct position
        if (insertPosition) {
            container.insertBefore(section, insertPosition);
        } else {
            container.appendChild(section);
        }

        // Animate in
        setTimeout(() => {
            section.classList.add('visible');
        }, 100);

        console.log(`Loaded page: ${pageType} - ${title}`);
    }

    updateURL(pageType) {
        const url = new URL(window.location);
        url.hash = pageType;

        // Update browser history
        history.pushState(
            { page: pageType },
            `${this.getPageTitle(pageType)} - ${document.title.split(' - ')[1]}`,
            url.toString()
        );

        // Update document title
        document.title = `${this.getPageTitle(pageType)} - ${document.title.split(' - ').slice(1).join(' - ')}`;
    }

    updatePageIndicator(pageType) {
        document.querySelectorAll('.page-indicator .nav-item').forEach(navItem => {
            navItem.classList.remove('active');
            if (navItem.dataset.page === pageType) {
                navItem.classList.add('active');
            }
        });
    }

    updateScrollProgress() {
        const scrollTop = window.pageYOffset;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercentage = (scrollTop / documentHeight) * 100;

        const progressBar = document.getElementById('scrollProgress');
        if (progressBar) {
            progressBar.style.width = `${Math.min(scrollPercentage, 100)}%`;
        }
    }

    showLoadingIndicator() {
        const indicator = document.getElementById('loadingIndicator');
        if (indicator) {
            indicator.classList.add('show');
        }
    }

    hideLoadingIndicator() {
        const indicator = document.getElementById('loadingIndicator');
        if (indicator) {
            indicator.classList.remove('show');
        }
    }

    showErrorMessage(message) {
        // Create temporary error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger position-fixed';
        errorDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
        errorDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(errorDiv);

        // Remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 5000);
    }

    getPageTitle(pageType) {
        const titles = {
            'profile': 'Profil Institusi',
            'vision-mission': 'Visi & Misi',
            'structure': 'Struktur Organisasi',
            'facilities': 'Fasilitas',
            'gallery': 'Galeri'
        };
        return titles[pageType] || 'Institusi';
    }

    // Utility function to throttle events
    throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on the infinite scroll page
    if (document.getElementById('infiniteScrollContainer')) {
        window.infiniteScrollInstitution = new InfiniteScrollInstitution();

        // Handle initial page parameter from URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialPage = urlParams.get('page');

        if (initialPage && initialPage !== 'profile') {
            // Load and scroll to the specified page
            setTimeout(() => {
                window.infiniteScrollInstitution.scrollToPage(initialPage);
            }, 500);
        }
    }
});

// Gallery Load More Function for Institution Page
// Global variable to track loaded items
let galleryOffset = 6; // Start from 6 since we already loaded first 6 items

function loadMoreInstitutionGallery() {
    const button = document.getElementById('loadMoreGalleryBtn');
    const loadMoreText = document.getElementById('loadMoreText');
    const galleryContainer = document.getElementById('galleryContainer');

    if (!button || !galleryContainer) {
        console.error('Required elements not found');
        return;
    }

    // Show loading state
    const originalText = loadMoreText.innerHTML;
    button.disabled = true;
    loadMoreText.innerHTML = 'Memuat...';
    const svgElement = button.querySelector('svg');
    if (svgElement) {
        svgElement.style.display = 'none';
    }

    // Add spinner
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
    button.insertBefore(spinner, loadMoreText);

    // Make AJAX request
    fetch(`/ajax/gallery/institution/load-more?offset=${galleryOffset}&limit=6`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                // Append new gallery items
                galleryContainer.insertAdjacentHTML('beforeend', data.html);

                // Update offset
                galleryOffset = data.nextOffset;

                // Hide button if no more items
                if (!data.hasMore) {
                    button.style.display = 'none';
                }

                console.log(`Loaded ${data.loadedCount} more items. Total offset: ${galleryOffset}`);
            } else {
                console.error('Failed to load gallery items:', data.error);
                alert('Gagal memuat galeri. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error loading gallery:', error);
            alert('Terjadi kesalahan saat memuat galeri. Silakan coba lagi.');
        })
        .finally(() => {
            // Restore button state
            button.disabled = false;
            loadMoreText.innerHTML = originalText;
            if (svgElement) {
                svgElement.style.display = 'inline';
            }

            // Remove spinner
            const spinnerElement = button.querySelector('.spinner-border');
            if (spinnerElement) {
                spinnerElement.remove();
            }
        });
}

// Legacy function - redirect to new function
function loadMoreGallery() {
    loadMoreInstitutionGallery();
}

// Gallery Modal Function
function viewGallery(code) {
    console.log('viewGallery called with code:', code);

    // Get modal elements
    const modalElement = document.getElementById('galleryModal');
    const modalBody = document.getElementById('galleryModalBody');
    const modalTitle = document.getElementById('galleryModalLabel');

    if (!modalElement || !modalBody) {
        console.error('Modal elements not found');
        alert('Modal gallery tidak ditemukan.');
        return;
    }

    // Show loading state
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat detail galeri...</p>
        </div>
    `;

    // Try to show modal with different methods
    try {
        // Method 1: Try Bootstrap 5
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
        // Method 2: Try jQuery Bootstrap (fallback)
        else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(modalElement).modal('show');
        }
        // Method 3: Manual show (last resort)
        else {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            modalElement.setAttribute('aria-hidden', 'false');

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'galleryModalBackdrop';
            document.body.appendChild(backdrop);

            // Add close functionality
            const closeBtn = modalElement.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.onclick = function () {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    modalElement.setAttribute('aria-hidden', 'true');
                    const backdrop = document.getElementById('galleryModalBackdrop');
                    if (backdrop) backdrop.remove();
                };
            }
        }

        // Load gallery details via AJAX
        fetch(`/ajax/gallery/detail/${code}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalTitle.textContent = data.title || 'Detail Galeri';
                    modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${data.image || '/storage/images/galeri/default.jpg'}" 
                                     class="img-fluid rounded mb-3" 
                                     alt="${data.title || 'Gallery Image'}"
                                     style="width: 100%; height: 300px; object-fit: cover;">
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">${data.title || 'Nama Galeri'}</h5>
                                <p class="text-muted mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                    ${data.date || 'Tanggal tidak tersedia'}
                                </p>
                                ${data.category ? `<span class="badge bg-primary mb-3">${data.category}</span>` : ''}
                                <div class="content">
                                    ${data.description || 'Deskripsi tidak tersedia.'}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    throw new Error(data.error || 'Failed to load gallery details');
                }
            })
            .catch(error => {
                console.error('Error loading gallery details:', error);
                modalTitle.textContent = 'Detail Galeri';
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="/storage/images/galeri/default.jpg" 
                                 class="img-fluid rounded mb-3" 
                                 alt="Gallery Image"
                                 style="width: 100%; height: 300px; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Nama Galeri</h5>
                            <p class="text-muted mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                </svg>
                                Tanggal tidak tersedia
                            </p>
                            <div class="content">
                                Deskripsi galeri dengan kode: ${code}
                            </div>
                        </div>
                    </div>
                `;
            });

    } catch (error) {
        console.error('Error showing modal:', error);
        alert('Terjadi kesalahan saat membuka modal. Error: ' + error.message);
    }
}

// Export for potential external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InfiniteScrollInstitution;
}
