@extends('core-themes.core-mainpage')

@section('custom-css')
<style>
    .infinite-scroll-container {
        position: relative;
    }

    .page-section {
        padding: 2rem 0;
        position: relative;
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.6s ease-in-out;
    }

    .page-section.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .page-section.loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .hero-section {
        background: linear-gradient(135deg, rgba(var(--tblr-primary-rgb), 0.25), rgba(var(--tblr-info-rgb), 0.9)), url('https://images.unsplash.com/photo-1576495199011-eb94736d05d6?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
        background-size: cover;
        background-attachment: scroll;
        min-height: 50vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 3rem 3rem;
        color: white;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .content-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .content-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .section-title {
        position: relative;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: var(--tblr-primary);
    }

    .text-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--tblr-body-color);
    }

    .text-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
        margin: 1rem 0;
    }

    /* Loading indicator */
    .loading-indicator {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(var(--tblr-primary-rgb), 0.9);
        color: white;
        padding: 1rem 2rem;
        border-radius: 2rem;
        display: none;
        z-index: 9999;
        backdrop-filter: blur(10px);
    }

    .loading-indicator.show {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Navigation indicator */
    .page-indicator {
        position: fixed;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .page-indicator .nav-item {
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        background: rgba(var(--tblr-bg-surface-rgb), 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(var(--tblr-border-color-rgb), 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        min-width: 120px;
    }

    .page-indicator .nav-item:hover {
        background: rgba(var(--tblr-primary-rgb), 0.1);
        border-color: var(--tblr-primary);
        transform: translateX(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .page-indicator .nav-item.active {
        background: var(--tblr-primary);
        color: white;
        transform: translateX(-10px);
        box-shadow: 0 8px 25px rgba(var(--tblr-primary-rgb), 0.3);
    }

    .page-indicator .nav-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 0.5rem;
        opacity: 0.7;
    }

    .page-indicator .nav-item.active .dot {
        opacity: 1;
    }

    .page-indicator .nav-item .label {
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    [data-bs-theme="dark"] .page-indicator .nav-item {
        background: rgba(var(--tblr-bg-surface-rgb), 0.95);
        border-color: rgba(var(--tblr-border-color-rgb), 0.5);
    }

    [data-bs-theme="dark"] .page-indicator .nav-item:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    /* Scroll progress */
    .scroll-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: var(--tblr-primary);
        z-index: 9999;
        transition: width 0.1s ease;
    }

    /* Vision Mission Cards */
    .vision-mission-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        overflow: hidden;
    }

    .vision-mission-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .vision-icon {
        width: 80px;
        height: 80px;
        background: var(--tblr-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 2rem;
    }

    .mission-icon {
        width: 80px;
        height: 80px;
        background: var(--tblr-success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 2rem;
    }

    /* Gallery styles */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .gallery-item {
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
    }

    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    /* Mobile responsive - Snap to edge with partial text visible */
    @media (max-width: 768px) {
        .page-indicator {
            right: -30px; /* Less hidden, more text visible */
            gap: 0.4rem;
            transition: right 0.3s ease;
        }
        
        .page-indicator:hover {
            right: 0.5rem; /* Slide out on hover */
        }
        
        .page-indicator .nav-item {
            min-width: 80px;
            padding: 0.4rem 0.8rem;
            border-radius: 25px 0 0 25px; /* Rounded only on left side */
            justify-content: flex-start;
            overflow: visible; /* Allow text to show */
            position: relative;
        }
        
        .page-indicator .nav-item .label {
            font-size: 0.75rem;
            white-space: nowrap;
            /* Remove text truncation to show more text */
        }
        
        .page-indicator .nav-item .dot {
            margin-right: 0.3rem;
            width: 8px;
            height: 8px;
            flex-shrink: 0;
        }
        
        .page-indicator .nav-item:hover,
        .page-indicator .nav-item.active {
            transform: translateX(-5px);
        }
        
        .hero-section {
            min-height: 40vh;
            padding: 3rem 0;
        }
        
        .page-section {
            padding: 1rem 0;
        }
    }

    @media (max-width: 480px) {
        .page-indicator {
            right: -35px; /* Less hidden on smaller screens */
        }
        
        .page-indicator:hover {
            right: 0.25rem;
        }
        
        .page-indicator .nav-item {
            min-width: 70px;
            padding: 0.3rem 0.6rem;
        }
        
        .page-indicator .nav-item .label {
            font-size: 0.7rem;
        }
        
        .page-indicator .nav-item .dot {
            width: 6px;
            height: 6px;
        }
    }
</style>
@endsection

@section('content')
<!-- Scroll Progress Bar -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- Loading Indicator -->
<div class="loading-indicator" id="loadingIndicator">
    <div class="spinner"></div>
    <span>Memuat konten...</span>
</div>

<!-- Page Navigation Indicator -->
<div class="page-indicator" id="pageIndicator">
    <div class="nav-item active" data-page="profile">
        <div class="dot"></div>
        <span class="label">Profil</span>
    </div>
    <div class="nav-item" data-page="vision-mission">
        <div class="dot"></div>
        <span class="label">Visi & Misi</span>
    </div>
    <div class="nav-item" data-page="structure">
        <div class="dot"></div>
        <span class="label">Struktur</span>
    </div>
    <div class="nav-item" data-page="facilities">
        <div class="dot"></div>
        <span class="label">Fasilitas</span>
    </div>
    <div class="nav-item" data-page="gallery">
        <div class="dot"></div>
        <span class="label">Galeri</span>
    </div>
</div>

<!-- Infinite Scroll Container -->
<div class="infinite-scroll-container" id="infiniteScrollContainer">
    <!-- All sections will be loaded dynamically via JavaScript -->
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalLabel">Detail Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="galleryModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="{{ asset('js/infinite-scroll-institution.js') }}"></script>
<script>
// Auto-snap navigation on scroll for mobile
document.addEventListener('DOMContentLoaded', function() {
    const pageIndicator = document.getElementById('pageIndicator');
    let scrollTimeout;
    let isHovered = false;
    
    // Track hover state
    pageIndicator.addEventListener('mouseenter', function() {
        isHovered = true;
    });
    
    pageIndicator.addEventListener('mouseleave', function() {
        isHovered = false;
    });
    
    // Auto-snap on scroll (only on mobile)
    function handleScroll() {
        if (window.innerWidth <= 768 && !isHovered) {
            // Clear existing timeout
            clearTimeout(scrollTimeout);
            
            // Force navigation to snap back to edge
            pageIndicator.style.right = window.innerWidth <= 480 ? '-35px' : '-30px';
            
            // Set timeout to allow navigation to be accessible again after scroll stops
            scrollTimeout = setTimeout(function() {
                // Navigation will be accessible via hover again
            }, 150);
        }
    }
    
    // Listen to scroll events
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Also handle touch events for mobile
    let touchTimeout;
    pageIndicator.addEventListener('touchstart', function() {
        clearTimeout(touchTimeout);
    });
    
    pageIndicator.addEventListener('touchend', function() {
        touchTimeout = setTimeout(function() {
            if (window.innerWidth <= 768) {
                pageIndicator.style.right = window.innerWidth <= 480 ? '-35px' : '-30px';
            }
        }, 2000); // Auto-snap after 2 seconds of no touch
    });
});
</script>
@endsection
