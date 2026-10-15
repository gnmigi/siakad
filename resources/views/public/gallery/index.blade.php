@extends('core-themes.core-mainpage')

@section('custom-css')
    <style>
        .page-header {
            background: var(--tblr-bg-surface);
            border-bottom: 1px solid var(--tblr-border-color);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        [data-bs-theme="dark"] .page-header {
            background: var(--tblr-dark);
        }

        .gallery-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            background: var(--tblr-card-bg);
            cursor: pointer;
        }

        .gallery-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .gallery-card img {
            transition: transform 0.3s ease;
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .gallery-card:hover img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 1rem;
        }

        .gallery-card:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay-content {
            color: white;
            width: 100%;
        }

        .gallery-overlay-content h5 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .gallery-overlay-content p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            border: 1px solid var(--tblr-border-color);
            color: var(--tblr-body-color);
            background: var(--tblr-card-bg);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--tblr-primary);
            border-color: var(--tblr-primary);
            color: white;
        }

        .pagination .page-link:hover {
            background-color: var(--tblr-primary);
            border-color: var(--tblr-primary);
            color: white;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--tblr-body-color);
            text-decoration: none;
            opacity: 0.8;
        }

        .breadcrumb-item a:hover {
            color: var(--tblr-body-color);
            opacity: 1;
        }

        .breadcrumb-item.active {
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
        }

        [data-bs-theme="dark"] .breadcrumb-item a:hover {
            color: white;
        }

        [data-bs-theme="dark"] .breadcrumb-item.active {
            color: white;
        }

        .badge.bg-primary {
            background-color: var(--tblr-primary) !important;
        }

        .btn-primary {
            background-color: var(--tblr-primary);
            border-color: var(--tblr-primary);
        }

        .btn-primary:hover {
            background-color: var(--tblr-primary);
            border-color: var(--tblr-primary);
        }

        /* Lightbox styles */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }

        .lightbox-content {
            position: relative;
            margin: auto;
            padding: 0;
            width: 90%;
            max-width: 1200px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }

        .lightbox-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10000;
        }

        .lightbox-close:hover {
            color: #bbb;
        }

        .lightbox-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            color: white;
            text-align: center;
            background: rgba(0,0,0,0.7);
            padding: 1rem;
            border-radius: 0.5rem;
        }

        @media (max-width: 768px) {
            .gallery-card {
                margin-bottom: 1.5rem;
            }
            
            .gallery-card img {
                height: 200px;
            }
            
            .page-header {
                padding: 1.5rem 0;
            }

            .lightbox-close {
                top: 10px;
                right: 20px;
                font-size: 30px;
            }

            .lightbox-info {
                bottom: 10px;
                left: 10px;
                right: 10px;
                padding: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('root.home-index') }}">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Galeri</li>
                        </ol>
                    </nav>
                    <h1 class="h2 mb-3">Galeri</h1>
                    <p class="mb-0">Dokumentasi kegiatan dan momen berharga institusi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-4">
        <div class="container">
            @if($galeri->count() > 0)
                <div class="row g-4">
                    @foreach($galeri as $gallery)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card gallery-card h-100" data-image="{{ asset('storage/images/galeri/' . $gallery->photo) }}" data-title="{{ $gallery->name }}" data-description="{{ strip_tags($gallery->content) }}" data-category="{{ $gallery->kategori ? $gallery->kategori->name : 'Galeri' }}" data-date="{{ $gallery->created_at->format('d M Y') }}">
                                <div class="position-relative">
                                    @if($gallery->photo)
                                        <img src="{{ asset('storage/images/galeri/' . $gallery->photo) }}" class="card-img-top" alt="{{ $gallery->name }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=1200&h=500" class="card-img-top" alt="{{ $gallery->name }}">
                                    @endif
                                    <div class="gallery-overlay">
                                        <div class="gallery-overlay-content">
                                            <h5>{{ $gallery->name }}</h5>
                                            <p>{{ Str::limit(strip_tags($gallery->content), 80) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3 flex-wrap">
                                        <span class="badge bg-primary me-2 mb-1">{{ $gallery->kategori ? $gallery->kategori->name : 'Galeri' }}</span>
                                        <small class="text-muted">{{ $gallery->created_at->format('d M Y') }}</small>
                                    </div>
                                    <h3 class="card-title h5">{{ $gallery->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($gallery->content), 100) }}</p>
                                    <div class="d-flex align-items-center text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                        <small>Klik untuk melihat</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $galeri->links() }}
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M15 8h.01" />
                                <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                            </svg>
                            <h3 class="text-muted">Belum ada galeri</h3>
                            <p class="text-muted">Galeri akan muncul di sini ketika tersedia</p>
                            <a href="{{ route('root.home-index') }}" class="btn btn-primary">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="lightbox">
        <div class="lightbox-content">
            <span class="lightbox-close">&times;</span>
            <img id="lightbox-img" src="" alt="">
            <div class="lightbox-info">
                <h4 id="lightbox-title"></h4>
                <p id="lightbox-description"></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary" id="lightbox-category"></span>
                    <small class="text-muted" id="lightbox-date"></small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple animation for cards
            const animateOnScroll = (elements, className) => {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting) {
                            setTimeout(() => {
                                entry.target.classList.add(className);
                            }, index * 100);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                elements.forEach(el => observer.observe(el));
            };

            const galleryCards = document.querySelectorAll('.gallery-card');
            animateOnScroll(galleryCards, 'visible');

            // Add click event listeners to gallery cards
            galleryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const imageSrc = this.dataset.image;
                    const title = this.dataset.title;
                    const description = this.dataset.description;
                    const category = this.dataset.category;
                    const date = this.dataset.date;
                    
                    openLightbox(imageSrc, title, description, category, date);
                });
            });

            // Lightbox functionality
            const lightbox = document.getElementById('lightbox');
            const lightboxClose = document.querySelector('.lightbox-close');

            lightboxClose.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });

            // Close lightbox with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLightbox();
                }
            });
        });

        function openLightbox(imageSrc, title, description, category, date) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            const lightboxTitle = document.getElementById('lightbox-title');
            const lightboxDescription = document.getElementById('lightbox-description');
            const lightboxCategory = document.getElementById('lightbox-category');
            const lightboxDate = document.getElementById('lightbox-date');

            lightboxImg.src = imageSrc;
            lightboxTitle.textContent = title;
            lightboxDescription.textContent = description;
            lightboxCategory.textContent = category;
            lightboxDate.textContent = date;

            lightbox.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
