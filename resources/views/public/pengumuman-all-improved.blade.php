@extends('core-themes.core-mainpage')

@section('custom-css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
            --card-shadow-hover: 0 30px 60px rgba(0,0,0,0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .hero-section {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
            padding: 6rem 0 4rem;
            margin-bottom: 0;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            background: linear-gradient(45deg, #fff, #f8f9fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .breadcrumb {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 600;
        }

        .content-section {
            background: #f8f9fa;
            margin-top: -3rem;
            border-radius: 3rem 3rem 0 0;
            padding: 4rem 0;
            position: relative;
            z-index: 3;
            min-height: 70vh;
        }

        .news-card {
            border: none;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            opacity: 0;
            transform: translateY(30px);
            background: white;
            position: relative;
        }

        .news-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .news-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            z-index: 1;
        }

        .news-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--card-shadow-hover);
        }

        .news-card img {
            transition: transform 0.4s ease;
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .news-card:hover img {
            transform: scale(1.08);
        }

        .card-body {
            padding: 2rem;
            position: relative;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.bg-primary {
            background: var(--primary-gradient) !important;
            border: none;
        }

        .card-title {
            font-weight: 700;
            color: #2d3748;
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .card-text {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .pagination {
            justify-content: center;
            margin-top: 4rem;
        }

        .pagination .page-link {
            border-radius: 50px;
            margin: 0 0.25rem;
            border: none;
            color: #667eea;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-link:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 2rem;
            box-shadow: var(--card-shadow);
            margin: 2rem 0;
        }

        .empty-state svg {
            opacity: 0.3;
            margin-bottom: 2rem;
        }

        .empty-state h3 {
            color: #4a5568;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #718096;
            margin-bottom: 2rem;
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            animation: float 15s ease-in-out infinite;
        }

        .floating-elements::before {
            top: 10%;
            left: 10%;
            animation-delay: -5s;
        }

        .floating-elements::after {
            bottom: 10%;
            right: 10%;
            animation-delay: -10s;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-section {
                padding: 4rem 0 3rem;
            }
            
            .content-section {
                margin-top: -2rem;
                padding: 3rem 0;
            }
            
            .news-card {
                margin-bottom: 2rem;
            }
            
            .news-card img {
                height: 200px;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .news-card img {
                height: 180px;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="floating-elements"></div>
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('root.home-index') }}">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Semua Pengumuman</li>
                        </ol>
                    </nav>
                    <h1 class="hero-title">Semua Pengumuman</h1>
                    <p class="hero-subtitle">Informasi terbaru dan pengumuman penting dari kampus</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            @if($pengumuman->count() > 0)
                <div class="row g-4">
                    @foreach($pengumuman as $announcement)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card news-card h-100">
                                @if($announcement->photo)
                                    <img src="{{ asset('storage/images/pengumuman/' . $announcement->photo) }}" class="card-img-top" alt="{{ $announcement->name }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=1200&h=500" class="card-img-top" alt="{{ $announcement->name }}">
                                @endif
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3 flex-wrap">
                                        <span class="badge bg-primary me-2 mb-1">{{ $announcement->kategori ? $announcement->kategori->name : 'Pengumuman' }}</span>
                                        <small class="text-muted">{{ $announcement->created_at->format('d M Y') }}</small>
                                    </div>
                                    <h3 class="card-title h5">{{ $announcement->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
                                    <a href="{{ route('root.viewPengumuman', $announcement->code) }}" class="btn btn-primary">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $pengumuman->links() }}
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-3" width="80" height="80" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            <h3>Belum ada pengumuman</h3>
                            <p>Pengumuman akan muncul di sini ketika tersedia</p>
                            <a href="{{ route('root.home-index') }}" class="btn btn-primary">Kembali ke Beranda</a>
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
            // Intersection Observer untuk animasi scroll
            const animateOnScroll = (elements, className) => {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting) {
                            setTimeout(() => {
                                entry.target.classList.add(className);
                            }, index * 100); // Stagger animation
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                elements.forEach(el => observer.observe(el));
            };

            // Inisialisasi animasi untuk news cards
            const newsCards = document.querySelectorAll('.news-card');
            animateOnScroll(newsCards, 'visible');

            // Smooth scroll untuk pagination
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Let the default behavior happen, but add smooth scroll to top
                    setTimeout(() => {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }, 100);
                });
            });
        });
    </script>
@endsection
