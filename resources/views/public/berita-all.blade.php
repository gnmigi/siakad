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

        .news-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            background: var(--tblr-card-bg);
        }

        .news-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .news-card img {
            transition: transform 0.3s ease;
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .news-card:hover img {
            transform: scale(1.05);
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
            background-color: var(--tblr-success);
            border-color: var(--tblr-success);
            color: white;
        }

        .pagination .page-link:hover {
            background-color: var(--tblr-success);
            border-color: var(--tblr-success);
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

        .badge.bg-success {
            background-color: var(--tblr-success) !important;
        }

        .btn-success {
            background-color: var(--tblr-success);
            border-color: var(--tblr-success);
        }

        .btn-success:hover {
            background-color: var(--tblr-success);
            border-color: var(--tblr-success);
        }

        @media (max-width: 768px) {
            .news-card {
                margin-bottom: 1.5rem;
            }
            
            .news-card img {
                height: 180px;
            }
            
            .page-header {
                padding: 1.5rem 0;
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
                            <li class="breadcrumb-item active" aria-current="page">Semua Berita</li>
                        </ol>
                    </nav>
                    <h1 class="h2 mb-3">Semua Berita</h1>
                    <p class="mb-0">Berita terkini dan kegiatan kampus</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-4">
        <div class="container">
            @if($berita->count() > 0)
                <div class="row g-4">
                    @foreach($berita as $news)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card news-card h-100">
                                @if($news->photo)
                                    <img src="{{ asset('storage/images/berita/' . $news->photo) }}" class="card-img-top" alt="{{ $news->name }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=1200&h=500" class="card-img-top" alt="{{ $news->name }}">
                                @endif
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3 flex-wrap">
                                        <span class="badge bg-success me-2 mb-1">{{ $news->kategori ? $news->kategori->name : 'Berita' }}</span>
                                        <small class="text-muted">{{ $news->created_at->format('d M Y') }}</small>
                                    </div>
                                    <h3 class="card-title h5">{{ $news->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($news->content), 120) }}</p>
                                    <a href="{{ route('root.viewBerita', $news->slug) }}" class="btn btn-success">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $berita->links() }}
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            <h3 class="text-muted">Belum ada berita</h3>
                            <p class="text-muted">Berita akan muncul di sini ketika tersedia</p>
                            <a href="{{ route('root.home-index') }}" class="btn btn-success">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
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

            const newsCards = document.querySelectorAll('.news-card');
            animateOnScroll(newsCards, 'visible');
        });
    </script>
@endsection
