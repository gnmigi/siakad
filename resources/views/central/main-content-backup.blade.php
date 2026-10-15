@extends('core-themes.core-mainpage')
@section('custom-css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-color: #1e7e34;
            --secondary-color: #28a745;
            --accent-color: #ffc107;
            --gold-color: #d4af37;
            --emerald-color: #50c878;
            --dark-emerald: #2d5a3d;
            --light-emerald: #e8f5e8;
            --islamic-blue: #1e3a8a;
            --warm-white: #fefefe;
            --soft-gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.12);
            --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.15);
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--emerald-color));
            --gradient-gold: linear-gradient(135deg, var(--gold-color), #f4d03f);
            --gradient-islamic: linear-gradient(135deg, var(--islamic-blue), var(--primary-color));
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
        }

        .arabic-font {
            font-family: 'Amiri', serif;
        }

        /* Islamic Geometric Patterns */
        .islamic-pattern {
            position: relative;
            overflow: hidden;
        }

        .islamic-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(30, 126, 52, 0.1) 0%, transparent 50%),
                linear-gradient(45deg, transparent 40%, rgba(255, 193, 7, 0.05) 50%, transparent 60%);
            pointer-events: none;
            z-index: 1;
        }

        .geometric-bg {
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(212, 175, 55, 0.03) 10px, rgba(212, 175, 55, 0.03) 20px),
                repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(30, 126, 52, 0.03) 10px, rgba(30, 126, 52, 0.03) 20px);
        }

        /* Enhanced Hero Section */
        .hero-section {
            background: linear-gradient(135deg, 
                rgba(30, 126, 52, 0.95) 0%, 
                rgba(45, 90, 61, 0.9) 50%, 
                rgba(30, 58, 138, 0.85) 100%
            ), url('https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?q=80&w=2070&auto=format&fit=crop') center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(ellipse at top, rgba(212, 175, 55, 0.1) 0%, transparent 70%),
                radial-gradient(ellipse at bottom, rgba(255, 193, 7, 0.1) 0%, transparent 70%);
            z-index: 1;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, var(--warm-white), transparent);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 700;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.4rem);
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            align-items: center;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            min-width: 180px;
            text-align: center;
            box-shadow: var(--shadow-medium);
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-hero:hover::before {
            left: 100%;
        }

        .btn-hero-primary {
            background: var(--gradient-gold);
            color: var(--text-dark);
            border: none;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.4);
            color: var(--text-dark);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            color: white;
        }

        .btn-hero-success {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .btn-hero-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 126, 52, 0.4);
            color: white;
        }

        /* Floating Stats with Islamic Design */
        .floating-stats {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 2rem;
            padding: 3rem 2rem;
            margin-top: -8rem;
            position: relative;
            z-index: 10;
            box-shadow: var(--shadow-heavy);
            transition: transform 0.3s ease;
        }

        .floating-stats:hover {
            transform: translateY(-5px);
        }

        .stat-item {
            text-align: center;
            position: relative;
        }

        .stat-number {
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Quick Access Cards */
        .quick-access-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
            border: 1px solid rgba(30, 126, 52, 0.1);
        }

        .quick-access-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
            border-color: var(--primary-color);
        }

        .quick-access-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .quick-access-card:hover .quick-access-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* Enhanced Feature Cards */
        .feature-card {
            background: white;
            border-radius: 2rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-light);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(30, 126, 52, 0.1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--primary-color);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            background: var(--gradient-gold);
        }

        /* Program Cards */
        .program-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: all 0.4s ease;
            border: 1px solid rgba(30, 126, 52, 0.1);
            height: 100%;
        }

        .program-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .program-card img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .program-card:hover img {
            transform: scale(1.05);
        }

        .program-card .card-body {
            padding: 1.5rem;
        }

        /* News Cards */
        .news-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: all 0.4s ease;
            border: 1px solid rgba(30, 126, 52, 0.1);
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .news-card img {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .news-card:hover img {
            transform: scale(1.05);
        }

        /* Section Styling */
        .section {
            padding: 5rem 0;
            position: relative;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text-dark);
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        /* Islamic Decorative Elements */
        .islamic-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            background: var(--gradient-gold);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .islamic-decoration:nth-child(2) {
            animation-delay: -2s;
            right: 10%;
            top: 20%;
        }

        .islamic-decoration:nth-child(3) {
            animation-delay: -4s;
            left: 10%;
            bottom: 20%;
            width: 60px;
            height: 60px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Enhanced Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .stagger-animation {
            transition-delay: calc(var(--stagger) * 0.1s);
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-islamic);
            border-radius: 2rem;
            padding: 4rem 2rem;
            position: relative;
            overflow: hidden;
            margin: 3rem 0;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center center;
            opacity: 0.3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }
            
            .floating-stats {
                margin-top: -4rem;
                padding: 2rem 1.5rem;
            }
            
            .section {
                padding: 3rem 0;
            }
            
            .feature-card {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 576px) {
            .floating-stats {
                margin-top: -2rem;
                padding: 1.5rem 1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }

        /* Calendar and Announcement Cards */
        .info-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(30, 126, 52, 0.1);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            box-shadow: var(--shadow-medium);
            transform: translateY(-2px);
        }

        .info-card .card-header {
            background: var(--light-emerald);
            border-bottom: 1px solid rgba(30, 126, 52, 0.1);
            border-radius: 1.5rem 1.5rem 0 0;
            padding: 1.5rem;
        }

        /* Gallery Enhancements */
        .gallery-card {
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: all 0.4s ease;
            border: 1px solid rgba(30, 126, 52, 0.1);
        }

        .gallery-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .gallery-card img {
            transition: transform 0.3s ease;
        }

        .gallery-card:hover img {
            transform: scale(1.05);
        }

        /* FAQ Enhancements */
        .accordion-item {
            border: 1px solid rgba(30, 126, 52, 0.1);
            border-radius: 1rem !important;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-light);
        }

        .accordion-button {
            border-radius: 1rem !important;
            font-weight: 600;
            color: var(--text-dark);
        }

        .accordion-button:not(.collapsed) {
            background: var(--light-emerald);
            color: var(--primary-color);
        }
    </style>
@endsection

@section('content')
    <!-- Enhanced Hero Section -->
    <section class="hero-section islamic-pattern">
        <div class="islamic-decoration"></div>
        <div class="islamic-decoration"></div>
        <div class="islamic-decoration"></div>
        
        <div class="container hero-content">
            <h1 class="hero-title arabic-font">{{ $hero_title }}</h1>
            <p class="hero-subtitle">{{ $hero_subtitle }}</p>
            <div class="hero-buttons">
                <a href="#programs" class="btn-hero btn-hero-primary">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Program Studi
                </a>
                <a href="{{ route('portal') }}" class="btn-hero btn-hero-secondary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    SIAKAD Portal
                </a>
                <a href="{{ route('pmb.registration.form') }}" class="btn-hero btn-hero-success">
                    <i class="fas fa-user-plus me-2"></i>
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Enhanced Floating Stats -->
    <div class="container position-relative">
        <div class="floating-stats geometric-bg">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-6 col-md-3">
                            <div class="stat-item fade-in-up" style="--stagger: 1">
                                <div class="stat-number">{{ $stat_programs }}</div>
                                <div class="stat-label">{{ $stat_programs_label }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item fade-in-up" style="--stagger: 2">
                                <div class="stat-number">{{ $stat_students }}</div>
                                <div class="stat-label">{{ $stat_students_label }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item fade-in-up" style="--stagger: 3">
                                <div class="stat-number">{{ $stat_lecturers }}</div>
                                <div class="stat-label">{{ $stat_lecturers_label }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item fade-in-up" style="--stagger: 4">
                                <div class="stat-number">{{ $stat_partners }}</div>
                                <div class="stat-label">{{ $stat_partners_label }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h4 class="mb-3 text-center text-lg-start">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Akses Cepat
                    </h4>
                    <div class="row g-2">
                        <div class="col-12 col-sm-6 col-lg-12">
                            <div class="quick-access-card">
                                <div class="d-flex align-items-center">
                                    <div class="quick-access-icon me-3">
                                        <i class="fas fa-calendar-alt text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Jadwal Kuliah</h6>
                                        <small class="text-muted">Lihat jadwal terkini</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-12">
                            <div class="quick-access-card">
                                <div class="d-flex align-items-center">
                                    <div class="quick-access-icon me-3">
                                        <i class="fas fa-certificate text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Nilai & Transkrip</h6>
                                        <small class="text-muted">Cek hasil studi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Section (Moved Up) -->
    <section id="programs" class="section geometric-bg">
        <div class="container">
            <h2 class="section-title">Program Studi Unggulan</h2>
            <div class="row g-4">
                @if(isset($programs) && is_array($programs) && count($programs) > 0)
                    @foreach($programs as $index => $program)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="program-card fade-in-up" style="--stagger: {{ $index + 1 }}">
                                @if(isset($program['image']) && $program['image'])
                                    <img src="{{ asset('storage/images/program-studi/' . $program['image']) }}" class="card-img-top" alt="{{ $program['name'] ?? 'Program' }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="{{ $program['name'] ?? 'Program' }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $program['name'] ?? 'Program Studi' }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($program['description'] ?? 'Deskripsi program studi', 100) }}</p>
                                    <a href="{{ route('root.viewProgramStudi', $program['code']) }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Default programs -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="program-card fade-in-up" style="--stagger: 1">
                            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Teknologi">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Teknologi</h5>
                                <p class="card-text text-muted">Teknik Informatika, Sistem Informasi, Data Science dengan kurikulum terkini</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="program-card fade-in-up" style="--stagger: 2">
                            <img src="https://images.unsplash.com/photo-1664575602276-acd073f104c1?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Bisnis">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Bisnis</h5>
                                <p class="card-text text-muted">Manajemen, Akuntansi, Digital Business dengan pendekatan praktis</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="program-card fade-in-up" style="--stagger: 3">
                            <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Kreatif">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Industri Kreatif</h5>
                                <p class="card-text text-muted">Desain Komunikasi Visual, Animasi, Game Development</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Keunggulan Kami</h2>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="feature-card fade-in-up" style="--stagger: 1">
                        <div class="feature-icon">
                            <i class="fas fa-laptop text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Pembelajaran Digital</h3>
                        <p class="text-muted">Platform pembelajaran digital terintegrasi dengan akses 24/7 ke materi perkuliahan dan sistem evaluasi modern</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="feature-card fade-in-up" style="--stagger: 2">
                        <div class="feature-icon">
                            <i class="fas fa-certificate text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Sertifikasi Industri</h3>
                        <p class="text-muted">Program sertifikasi profesional dari mitra industri terkemuka untuk meningkatkan daya saing lulusan</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="feature-card fade-in-up" style="--stagger: 3">
                        <div class="feature-icon">
                            <i class="fas fa-globe text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Jaringan Global</h3>
                        <p class="text-muted">Kesempatan pertukaran pelajar dan kolaborasi internasional dengan universitas terkemuka dunia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcement & Calendar Section -->
    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="info-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-bullhorn text-primary me-2"></i>
                                Pengumuman Penting
                            </h3>
                        </div>
                        <div class="list-group list-group-flush">
                            @if(isset($pengumuman) && count($pengumuman) > 0)
                                @foreach($pengumuman as $index => $announcement)
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                @php
                                                    $badgeColors = ['bg-red', 'bg-yellow', 'bg-green', 'bg-blue', 'bg-purple'];
                                                    $badgeColor = $badgeColors[$index % count($badgeColors)];
                                                @endphp
                                                <span class="badge {{ $badgeColor }}"></span>
                                            </div>
                                            <div class="col-auto d-none d-sm-block">
                                                <span class="avatar">{{ strtoupper(substr($announcement->name, 0, 3)) }}</span>
                                            </div>
                                            <div class="col text-truncate">
                                                <span class="text-reset d-block">{{ $announcement->name }}</span>
                                                <div class="d-block text-muted text-truncate mt-n1">{{ Str::limit(strip_tags($announcement->content), 80) }}</div>
                                                <div class="d-block text-muted mt-1">
                                                    <small>{{ $announcement->created_at->diffForHumans() }}</small>
                                                    @if($announcement->kategori)
                                                        <span class="badge bg-primary ms-1">{{ $announcement->kategori->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="list-group-item">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <div>Belum ada pengumuman terbaru</div>
                                        <small>Pengumuman akan muncul di sini ketika tersedia</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="info-card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                            <h3 class="card-title m-0">
                                <i class="fas fa-calendar-alt text-success me-2"></i>
                                Kalender Akademik
                            </h3>
                            <a href="#" class="btn btn-sm btn-primary mt-2 mt-sm-0">Lihat Semua</a>
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar bg-primary-lt">
                                            <span class="avatar-text">SEP</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">Awal Perkuliahan</div>
                                        <div class="text-muted small">Semester Ganjil 2024/2025</div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-primary-lt">4 Sep</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar bg-yellow-lt">
                                            <span class="avatar-text">OKT</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">Ujian Tengah Semester</div>
                                        <div class="text-muted small">15-26 Oktober 2024</div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-yellow-lt">15 Okt</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar bg-red-lt">
                                            <span class="avatar-text">DES</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">Ujian Akhir Semester</div>
                                        <div class="text-muted small">11-22 Desember 2024</div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-red-lt">11 Des</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="section geometric-bg">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap">
                <h2 class="section-title mb-0">Berita & Pengumuman</h2>
                <div class="d-flex gap-2 mt-2 mt-sm-0">
                    <a href="{{ route('root.allPengumuman') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>
                        <span class="d-none d-sm-inline">Semua Pengumuman</span>
                        <span class="d-sm-none">Pengumuman</span>
                    </a>
                    <a href="{{ route('root.allBerita') }}" class="btn btn-success">
                        <i class="fas fa-newspaper me-2"></i>
                        <span class="d-none d-sm-inline">Semua Berita</span>
                        <span class="d-sm-none">Berita</span>
                    </a>
                </div>
            </div>
            
            <div class="row g-4">
                @if(isset($pengumuman) && count($pengumuman) > 0)
                    @foreach($pengumuman->take(2) as $index => $announcement)
                        <div class="col-12 col-md-6">
                            <div class="news-card fade-in-up" style="--stagger: {{ $index + 1 }}">
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
                                    <h3 class="card-title h4">{{ $announcement->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>
                                    <a href="{{ route('root.viewPengumuman', $announcement->slug) }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(isset($berita) && count($berita) > 0)
                    @foreach($berita->take(2) as $index => $news)
                        <div class="col-12 col-md-6">
                            <div class="news-card fade-in-up" style="--stagger: {{ $index + 3 }}">
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
                                    <h3 class="card-title h4">{{ $news->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($news->content), 150) }}</p>
                                    <a href="{{ route('root.viewBerita', $news->slug) }}" class="btn btn-success">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if((!isset($pengumuman) || count($pengumuman) == 0) && (!isset($berita) || count($berita) == 0))
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-newspaper mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                            <div class="h4">Belum ada berita atau pengumuman terbaru</div>
                            <p>Konten akan muncul di sini ketika tersedia</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap">
                <h2 class="section-title mb-0">Galeri Kampus</h2>
                <a href="/gallery" class="btn btn-primary mt-2 mt-sm-0">
                    <i class="fas fa-images me-2"></i>
                    <span class="d-none d-sm-inline">Lihat Semua Galeri</span>
                    <span class="d-sm-none">Galeri</span>
                </a>
            </div>
            
            <div class="row g-4">
                @if(isset($gallery) && is_array($gallery) && count($gallery) > 0)
                    @foreach($gallery->take(6) as $index => $item)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="gallery-card fade-in-up" style="--stagger: {{ $index + 1 }}">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ asset('storage/images/galeri/' . $item['image']) }}" class="card-img-top" alt="{{ $item['title'] ?? 'Gallery Image' }}" style="height: 250px; object-fit: cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="{{ $item['title'] ?? 'Gallery Image' }}" style="height: 250px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item['title'] ?? 'Gallery Title' }}</h5>
                                    <p class="text-muted">{{ Str::limit($item['description'] ?? 'Gallery Description', 100) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="gallery-card fade-in-up" style="--stagger: 1">
                            <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Campus Life" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Kehidupan Kampus</h5>
                                <p class="text-muted">Suasana akademik yang mendukung pengembangan diri</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="gallery-card fade-in-up" style="--stagger: 2">
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Library" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Perpustakaan Modern</h5>
                                <p class="text-muted">Pusat sumber daya pembelajaran dengan fasilitas lengkap</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="gallery-card fade-in-up" style="--stagger: 3">
                            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Laboratory" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Laboratorium Canggih</h5>
                                <p class="text-muted">Fasilitas penelitian dengan teknologi terkini</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section geometric-bg">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        @php
                            $faq = $webs->faq_section ? json_decode($webs->faq_section, true) : [
                                ['question' => 'Bagaimana cara mendaftar kuliah?', 'answer' => 'Pendaftaran dapat dilakukan secara online melalui website PMB kami. Ikuti langkah-langkah yang tersedia dan siapkan dokumen yang diperlukan.'],
                                ['question' => 'Apa saja program beasiswa yang tersedia?', 'answer' => 'Kami menyediakan berbagai program beasiswa, termasuk beasiswa akademik, beasiswa prestasi non-akademik, dan beasiswa kemitraan dengan industri.'],
                                ['question' => 'Bagaimana sistem perkuliahan hybrid?', 'answer' => 'Sistem perkuliahan hybrid menggabungkan pembelajaran tatap muka dan online, didukung platform digital yang memudahkan akses materi dan interaksi.'],
                                ['question' => 'Apakah tersedia program magang industri?', 'answer' => 'Ya, kami memiliki program magang yang bekerjasama dengan berbagai perusahaan dan institusi untuk memberikan pengalaman praktis kepada mahasiswa.']
                            ];
                        @endphp
                        
                        @foreach($faq as $index => $item)
                            <div class="accordion-item fade-in-up" style="--stagger: {{ $index + 1 }}">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                        <i class="fas fa-question-circle me-2 text-primary"></i>
                                        {{ $item['question'] }}
                                    </button>
                                </h2>
                                <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $item['answer'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container">
            <div class="cta-section text-white text-center">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-8">
                        <h2 class="mb-3 arabic-font" style="font-size: 2.5rem;">{{ $webs->cta_title ?? 'بِسْمِ اللَّهِ نَبْدَأُ رِحْلَتَنَا' }}</h2>
                        <h3 class="mb-4">{{ $webs->cta_subtitle ?? 'Siap Bergabung dengan Kami?' }}</h3>
                        <p class="mb-4 fs-5">Daftar sekarang dan mulai perjalanan akademik Anda bersama kami menuju masa depan yang gemilang</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <a href="{{ $webs->cta_button_link ?? route('pmb.registration.form') }}" class="btn btn-light btn-lg px-4 py-3">
                                <i class="fas fa-user-plus me-2"></i>
                                {{ $webs->cta_button_text ?? 'Daftar Sekarang' }}
                            </a>
                            <a href="#programs" class="btn btn-outline-light btn-lg px-4 py-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced Animation System
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            // Intersection Observer for animations
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        
                        // Special handling for counters
                        if (entry.target.classList.contains('stat-item')) {
                            animateCounter(entry.target.querySelector('.stat-number'));
                        }
                        
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.fade-in-up').forEach(el => {
                animationObserver.observe(el);
            });

            // Counter Animation
            function animateCounter(element) {
                if (!element) return;
                
                const target = parseInt(element.textContent.replace(/\D/g, ''));
                let current = 0;
                const duration = 2000;
                const increment = target / (duration / 16);
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        element.textContent = Math.ceil(current) + '+';
                        requestAnimationFrame(updateCounter);
                    } else {
                        element.textContent = target + '+';
                    }
                };
                
                updateCounter();
            }

            // Smooth scrolling for anchor links
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

            // Enhanced hover effects for cards
            const cards = document.querySelectorAll('.feature-card, .program-card, .news-card, .gallery-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Parallax effect for decorative elements
            const decorations = document.querySelectorAll('.islamic-decoration');
            let ticking = false;

            function updateParallax() {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;

                decorations.forEach((decoration, index) => {
                    const speed = (index + 1) * 0.3;
                    decoration.style.transform = `translateY(${rate * speed}px)`;
                });

                ticking = false;
            }

            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            }

            window.addEventListener('scroll', requestTick);

            // Enhanced button interactions
            const heroButtons = document.querySelectorAll('.btn-hero');
            heroButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.05)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Loading animation for images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                    this.style.transform = 'scale(1)';
                });
                
                // Set initial state
                img.style.opacity = '0';
                img.style.transform = 'scale(0.95)';
                img.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            });

            // Stagger animation for elements with --stagger variable
            const staggerElements = document.querySelectorAll('[style*="--stagger"]');
            staggerElements.forEach(element => {
                const staggerValue = getComputedStyle(element).getPropertyValue('--stagger');
                element.style.transitionDelay = `${staggerValue * 0.1}s`;
            });

            // Enhanced scroll-triggered animations
            const scrollElements = document.querySelectorAll('.section');
            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            scrollElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                scrollObserver.observe(el);
            });
        });
    </script>
@endsection
