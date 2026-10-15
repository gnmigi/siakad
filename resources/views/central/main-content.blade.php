@extends('core-themes.core-mainpage')

@section('structured-data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "{{ $webs->school_name ?? 'Ma\'had Aly Ma\'al Amin' }}",
    "alternateName": "{{ $webs->school_apps ?? 'SIAKAD Ma\'had Aly' }}",
    "description": "{{ $webs->school_desc ?? 'Ma\'had Aly adalah lembaga pendidikan tinggi Islam yang berfokus pada pendalaman ilmu-ilmu keislaman secara takhassus (spesialisasi), terutama dalam bidang kitab kuning turats (warisan keilmuan klasik Islam)' }}",
    "url": "{{ url('/') }}",
    "logo": "{{ $webs->school_logo ?? asset('logo.png') }}",
    "image": "{{ $webs->school_logo_hori ?? asset('logo.png') }}",
    "telephone": "{{ $webs->school_phone ?? '+62 822 2529 66763' }}",
    "email": "{{ $webs->school_email ?? 'gusti.nsfians@gmail.com' }}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $webs->school_address ?? 'Yayasan Al Baladul Amin' }}",
        "addressLocality": "Indonesia",
        "addressCountry": "ID"
    },
    "sameAs": [
        "https://www.facebook.com/mahadaly",
        "https://www.instagram.com/mahadaly",
        "https://www.youtube.com/mahadaly"
    ],
    "foundingDate": "2020",
    "numberOfStudents": "56+",
    "faculty": [
        {
            "@type": "EducationalOrganization",
            "name": "Fakultas Bahasa dan Sastra Arab",
            "description": "Program Studi Bahasa dan Sastra Arab bertujuan mencetak lulusan yang mahir berbahasa Arab, memahami sastra Arab klasik dan modern"
        }
    ],
    "offers": [
        {
            "@type": "Offer",
            "name": "Program Sarjana",
            "description": "Program pendidikan sarjana dengan fokus pada ilmu keislaman"
        },
        {
            "@type": "Offer", 
            "name": "Beasiswa",
            "description": "Program beasiswa untuk mahasiswa berprestasi"
        }
    ],
    "hasCredential": {
        "@type": "EducationalOccupationalCredential",
        "name": "Akreditasi Institusi",
        "description": "Terakreditasi sebagai lembaga pendidikan tinggi Islam"
    }
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "{{ $webs->school_apps ?? 'SIAKAD Ma\'had Aly' }}",
    "url": "{{ url('/') }}",
    "description": "{{ $webs->school_desc ?? 'Sistem Informasi Akademik Ma\'had Aly Ma\'al Amin' }}",
    "publisher": {
        "@type": "EducationalOrganization",
        "name": "{{ $webs->school_name ?? 'Ma\'had Aly Ma\'al Amin' }}",
        "logo": "{{ $webs->school_logo ?? asset('logo.png') }}"
    },
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/') }}?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ url('/') }}"
        }
    ]
}
</script>
@endsection

@section('custom-css')
    <!-- Load optimized CSS -->
    <link href="{{ asset('css/homepage-optimized.css') }}" rel="stylesheet">
    <style>
        /* Simplified CSS - Using Tabler's built-in teal theme */
        :root {
            --primary-color: var(--tblr-primary);
            --secondary-color: var(--tblr-secondary);
            --accent-color: var(--tblr-info);
            --primary-color-rgb: var(--tblr-primary-rgb);
            --accent-color-rgb: var(--tblr-info-rgb);
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.25), rgba(var(--accent-color-rgb), 0.9)), url('https://images.unsplash.com/photo-1576495199011-eb94736d05d6?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
            background-size: cover;
            background-attachment: scroll;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 3rem 3rem;
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
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-stats {
            background: var(--tblr-card-bg);
            border: 1px solid var(--tblr-border-color);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-top: -7rem;
            position: relative;
            z-index: 3;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .floating-stats:hover {
            transform: translateY(-5px);
        }

        .feature-card {
            border: none;
            border-radius: 1.5rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow: hidden;
            background: var(--tblr-card-bg);
            position: relative;
            opacity: 0;
            transform: translateY(30px);
        }

        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .news-card {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            opacity: 0;
            transform: translateY(30px);
        }

        .news-card.visible {
            opacity: 1;
            transform: translateY(0);
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

/* Hero Section Button Improvements */
        .hero-section .btn {
            /* Base mobile-first styling */
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            min-width: 140px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Button specific styles */
        .hero-section .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), rgba(var(--primary-color-rgb), 0.8));
            border: none;
            color: white;
        }

        .hero-section .btn-primary:hover {
            background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.9), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--primary-color-rgb), 0.3);
        }

        .hero-section .btn-white {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--primary-color);
            backdrop-filter: blur(10px);
        }

        .hero-section .btn-white:hover {
            background: white;
            border-color: white;
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .hero-section .btn-success {
            background: linear-gradient(135deg, #28a745, rgba(40, 167, 69, 0.8));
            border: none;
            color: white;
        }

        .hero-section .btn-success:hover {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), #28a745);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .news-card {
                margin-bottom: 1.5rem;
            }
            
            .news-card img {
                height: 180px;
            }
            
            .news-card .card-body {
                padding: 1rem;
            }
            
            .news-card .card-title {
                font-size: 1.1rem;
                line-height: 1.3;
                margin-bottom: 0.75rem;
            }
            
            .news-card .card-text {
                font-size: 0.9rem;
                line-height: 1.4;
            }
            
            .floating-stats {
                margin-top: -4rem;
                padding: 1.5rem;
            }
            
            .achievement-counter {
                font-size: 2rem;
            }
            
            .hero-section {
                min-height: 70vh;
                padding: 2rem 0;
            }
            
            .hero-section h1 {
                font-size: 2.5rem !important;
            }
            
            .hero-section p {
                font-size: 1.1rem !important;
            }
            
            .hero-section .d-flex.gap-3 {
                gap: 0.75rem !important;
            }
        }

        /* Small tablets and up */
        @media (min-width: 576px) {
            .hero-section .btn {
                padding: 0.875rem 2rem;
                font-size: 1rem;
                min-width: 160px;
                border-radius: 0.875rem;
            }
        }

        @media (max-width: 576px) {
            .news-card img {
                height: 160px;
            }
            
            .floating-stats {
                margin-top: -2rem;
                padding: 1rem;
            }
            
            .achievement-counter {
                font-size: 1.5rem;
            }
            
            .hero-section {
                min-height: 60vh;
            }
            
            .hero-section h1 {
                font-size: 2rem !important;
            }
            
            .hero-section p {
                font-size: 1rem !important;
            }
            
            .hero-section .btn {
                padding: 0.75rem 1.5rem !important;
                font-size: 0.95rem !important;
            }
        }

        /* Medium tablets and up */
        @media (min-width: 768px) {
            .hero-section .btn {
                padding: 1rem 2.5rem;
                font-size: 1.1rem;
                min-width: 180px;
                border-radius: 1rem;
            }
            
            .hero-section .d-flex {
                max-width: 600px;
                margin: 0 auto;
            }
            
            .hero-section .d-flex.gap-3 {
                gap: 1rem !important;
            }
        }

        /* Desktop and up */
        @media (min-width: 992px) {
            .hero-section .btn {
                padding: 0.875rem 2rem;
                font-size: 1rem;
                min-width: 160px;
                border-radius: 0.875rem;
            }
            
            .hero-section .d-flex {
                max-width: 550px;
            }
            
            .hero-section .d-flex.gap-3 {
                gap: 0.875rem !important;
            }
            
            .hero-section h1 {
                font-size: 3.5rem !important;
            }
            
            .hero-section p {
                font-size: 1.3rem !important;
                margin-bottom: 2.5rem !important;
            }
        }

        /* Large desktop */
        @media (min-width: 1200px) {
            .hero-section .btn {
                padding: 1rem 2.25rem;
                font-size: 1.05rem;
                min-width: 170px;
            }
            
            .hero-section .d-flex {
                max-width: 580px;
            }
        }

        /* Gallery Slider Styles */
        .gallery-slider, .program-slider {
            position: relative;
            overflow: hidden;
        }

        .gallery-track, .program-track {
            display: flex;
            transition: transform 0.3s ease;
        }

        .gallery-item, .program-item {
            flex: 0 0 auto;
            width: 100%;
            padding: 0 0.5rem;
        }

        .gallery-nav, .program-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s ease;
            z-index: 10;
        }

        .gallery-nav:hover, .program-nav:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .gallery-nav.prev, .program-nav.prev {
            left: 10px;
        }

        .gallery-nav.next, .program-nav.next {
            right: 10px;
        }

        .gallery-dots, .program-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .gallery-dot, .program-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ccc;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .gallery-dot.active, .program-dot.active {
            background: var(--tblr-primary);
        }

        @media (min-width: 768px) {
            .gallery-item, .program-item {
                width: 50%;
            }
        }

        @media (min-width: 992px) {
            .gallery-item {
                width: 33.333%;
            }
            .program-item {
                width: 33.333%;
            }
        }

        @media (max-width: 767px) {
            .gallery-nav, .program-nav {
                display: none;
            }
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 2rem;
            padding: 5rem 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(var(--primary-color-rgb), 0.3);
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.1)' fill-rule='evenodd'/%3E%3C/svg%3E") center center;
            opacity: 0.1;
        }

        .achievement-counter {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        /* Improve text contrast for better readability */
        .text-muted {
            color: #6c757d !important;
            font-weight: 500;
        }

        /* Dark mode improvements */
        [data-bs-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        /* Badge improvements for better contrast */
        .badge.bg-primary {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .badge.bg-success {
            background-color: #198754 !important;
            color: white !important;
        }

        /* Card text improvements */
        .card-text {
            color: #495057 !important;
            line-height: 1.6;
        }

        [data-bs-theme="dark"] .card-text {
            color: #dee2e6 !important;
        }

        /* Small text improvements */
        small.text-muted {
            color: #6c757d !important;
            font-weight: 500;
        }

        [data-bs-theme="dark"] small.text-muted {
            color: #adb5bd !important;
        }

        /* List group item text improvements */
        .list-group-item .text-muted {
            color: #6c757d !important;
        }

        [data-bs-theme="dark"] .list-group-item .text-muted {
            color: #adb5bd !important;
        }

        /* Category badge text improvements */
        .d-flex.align-items-center.mb-3 .text-muted {
            color: #6c757d !important;
            font-weight: 500;
        }

        [data-bs-theme="dark"] .d-flex.align-items-center.mb-3 .text-muted {
            color: #adb5bd !important;
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center text-white" style="background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.25), rgba(var(--accent-color-rgb), 0.9)), url('{{ $hero_background }}') no-repeat center center; background-size: cover; background-attachment: scroll;">
        <div class="container hero-content">
            <h1 class="display-1 fw-bold mb-4">{{ $hero_title }}</h1>
            <p class="text-white fs-3 mb-5">{{ $hero_subtitle }}</p>
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center align-items-center">
                <a href="#programs" class="btn btn-lg btn-primary w-100 w-md-auto">Program Studi</a>
                <a href="{{ route('portal') }}" class="btn btn-lg btn-white w-100 w-md-auto">SIAKAD Portal</a>
                <!--<a href="{{ route('pmb.registration.form') }}" class="btn btn-lg btn-success w-100 w-md-auto">Daftar Sekarang</a>-->
            </div>
        </div>
    </section>

    <!-- Quick Access Section -->
    <div class="container position-relative mb-5">
        <div class="floating-stats">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4 text-center">
                        <div class="col-md-3">
                            <div class="achievement-counter">{{ $stat_programs }}</div>
                            <div class="text-muted">{{ $stat_programs_label }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="achievement-counter">{{ $stat_students }}</div>
                            <div class="text-muted">{{ $stat_students_label }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="achievement-counter">{{ $stat_lecturers }}</div>
                            <div class="text-muted">{{ $stat_lecturers_label }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="achievement-counter">{{ $stat_partners }}</div>
                            <div class="text-muted">{{ $stat_partners_label }}</div>
                        </div>
                    </div>
                </div>
                <!--<div class="col-lg-4">-->
                <!--    <div class="d-flex flex-column gap-2">-->
                <!--        <h4 class="mb-3">Akses Cepat</h4>-->
                <!--        <a href="/siakad/jadwal" class="btn btn-primary d-flex align-items-center gap-2">-->
                <!--            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">-->
                <!--                <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />-->
                <!--                <path d="M16 3l0 4" />-->
                <!--                <path d="M8 3l0 4" />-->
                <!--                <path d="M4 11l16 0" />-->
                <!--                <path d="M8 15h2v2h-2z" />-->
                <!--            </svg>-->
                <!--            Jadwal Kuliah-->
                <!--        </a>-->
                <!--        <a href="/siakad/nilai" class="btn btn-primary d-flex align-items-center gap-2">-->
                <!--            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-certificate" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">-->
                <!--                <path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />-->
                <!--                <path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5" />-->
                <!--                <path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73" />-->
                <!--                <path d="M6 9l12 0" />-->
                <!--                <path d="M6 12l3 0" />-->
                <!--                <path d="M6 15l2 0" />-->
                <!--            </svg>-->
                <!--            Nilai & Transkrip-->
                <!--        </a>-->
                <!--        <a href="/siakad/pembayaran" class="btn btn-primary d-flex align-items-center gap-2">-->
                <!--            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-credit-card" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">-->
                <!--                <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />-->
                <!--                <path d="M3 10l18 0" />-->
                <!--                <path d="M7 15l.01 0" />-->
                <!--                <path d="M11 15l2 0" />-->
                <!--            </svg>-->
                <!--            Info Pembayaran-->
                <!--        </a>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
        </div>
    </div>

    <!-- Announcement & Calendar Section -->
    <section class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengumuman Penting</h3>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                            <path d="M12 9h.01"/>
                                            <path d="M11 12h1v4h1"/>
                                        </svg>
                                        <div>Belum ada pengumuman terbaru</div>
                                        <small>Pengumuman akan muncul di sini ketika tersedia</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <!--<div class="card">-->
                    <!--    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">-->
                    <!--        <h3 class="card-title m-0">Kalender Akademik</h3>-->
                    <!--        <a href="#" class="btn btn-sm btn-primary mt-2 mt-sm-0">Lihat Semua</a>-->
                    <!--    </div>-->
                    <!--    <div class="list-group list-group-flush">-->
                    <!--        <div class="list-group-item">-->
                    <!--            <div class="row align-items-center">-->
                    <!--                <div class="col-auto">-->
                    <!--                    <div class="avatar bg-primary-lt">-->
                    <!--                        <span class="avatar-text">SEP</span>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="col">-->
                    <!--                    <div class="text-truncate">Awal Perkuliahan</div>-->
                    <!--                    <div class="text-muted small">Semester Ganjil 2024/2025</div>-->
                    <!--                </div>-->
                    <!--                <div class="col-auto">-->
                    <!--                    <span class="badge bg-primary-lt">4 Sep</span>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--        <div class="list-group-item">-->
                    <!--            <div class="row align-items-center">-->
                    <!--                <div class="col-auto">-->
                    <!--                    <div class="avatar bg-yellow-lt">-->
                    <!--                        <span class="avatar-text">OKT</span>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="col">-->
                    <!--                    <div class="text-truncate">Ujian Tengah Semester</div>-->
                    <!--                    <div class="text-muted small">15-26 Oktober 2024</div>-->
                    <!--                </div>-->
                    <!--                <div class="col-auto">-->
                    <!--                    <span class="badge bg-yellow-lt">15 Okt</span>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--        <div class="list-group-item">-->
                    <!--            <div class="row align-items-center">-->
                    <!--                <div class="col-auto">-->
                    <!--                    <div class="avatar bg-red-lt">-->
                    <!--                        <span class="avatar-text">DES</span>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="col">-->
                    <!--                    <div class="text-truncate">Ujian Akhir Semester</div>-->
                    <!--                    <div class="text-muted small">11-22 Desember 2024</div>-->
                    <!--                </div>-->
                    <!--                <div class="col-auto">-->
                    <!--                    <span class="badge bg-red-lt">11 Des</span>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-6">
        <div class="container">
            <h2 class="text-center mb-5">Keunggulan Kami</h2>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-laptop text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M3 19l18 0" />
                                <path d="M5 6m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Pembelajaran Digital</h3>
                        <p class="text-muted">Platform pembelajaran digital terintegrasi dengan akses 24/7 ke materi perkuliahan</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-certificate text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5" />
                                <path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73" />
                                <path d="M6 9l12 0" />
                                <path d="M6 12l3 0" />
                                <path d="M6 15l2 0" />
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Sertifikasi Industri</h3>
                        <p class="text-muted">Program sertifikasi profesional dari mitra industri terkemuka</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-world text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M3.6 9h16.8" />
                                <path d="M3.6 15h16.8" />
                                <path d="M11.5 3a17 17 0 0 0 0 18" />
                                <path d="M12.5 3a17 17 0 0 1 0 18" />
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Jaringan Global</h3>
                        <p class="text-muted">Kesempatan pertukaran pelajar dan kolaborasi internasional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-6">
        <div class="container">
            <h2 class="text-center mb-5">Program Studi Unggulan</h2>
            @if(isset($programs) && is_array($programs) && count($programs) > 0)
                <div class="program-slider">
                    <div class="program-track" id="programTrack">
                        @foreach($programs as $program)
                            <div class="program-item">
                                <div class="card news-card h-100">
                                    @if(isset($program['image']) && $program['image'])
                                        <img src="{{ asset('storage/images/program-studi/' . $program['image']) }}" class="card-img-top" alt="{{ $program['name'] ?? 'Program' }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="{{ $program['name'] ?? 'Program' }}">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $program['name'] ?? 'Program Studi' }}</h5>
                                        <p class="card-text">{{ $program['description'] ?? 'Deskripsi program studi' }}</p>
                                        <a href="{{ route('root.viewProgramStudi', $program['code']) }}" class="btn btn-primary">Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($programs) > 1)
                        <button class="program-nav prev" id="programPrev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="program-nav next" id="programNext">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    @endif
                </div>
                
                @if(count($programs) > 1)
                    <div class="program-dots" id="programDots">
                        @for($i = 0; $i < count($programs); $i++)
                            <div class="program-dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"></div>
                        @endfor
                    </div>
                @endif
            @else
                <!-- Default programs if no data from database -->
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card news-card h-100">
                            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Teknik Informatika">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Teknologi</h5>
                                <p class="card-text">Teknik Informatika, Sistem Informasi, Data Science</p>
                                <a href="#" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card news-card h-100">
                            <img src="https://images.unsplash.com/photo-1664575602276-acd073f104c1?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Business">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Bisnis</h5>
                                <p class="card-text">Manajemen, Akuntansi, Digital Business</p>
                                <a href="#" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card news-card h-100">
                            <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=400&h=250" class="card-img-top" alt="Creative">
                            <div class="card-body">
                                <h5 class="card-title">Fakultas Industri Kreatif</h5>
                                <p class="card-text">Desain Komunikasi Visual, Animasi, Game Development</p>
                                <a href="#" class="btn btn-primary">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- News Section -->
    <section class="py-6">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap">
                <h2 class="mb-0">Pengumuman</h2>
                <a href="{{ route('root.allPengumuman') }}" class="btn btn-primary mt-2 mt-sm-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                    </svg>
                    <span class="d-none d-sm-inline">Lihat Semua Pengumuman</span>
                    <span class="d-sm-none">Semua</span>
                </a>
            </div>
            <div class="row g-4">
                @if(isset($pengumuman) && count($pengumuman) > 0)
                    @foreach($pengumuman as $announcement)
                        <div class="col-12 col-sm-6 col-lg-6">
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
                                    <h3 class="card-title h4">{{ $announcement->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>
                                    <a href="{{ route('root.viewPengumuman', $announcement->slug) }}" class="btn btn-primary">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            <div>Belum ada pengumuman terbaru</div>
                            <small>Pengumuman akan muncul di sini ketika tersedia</small>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-between mb-5 mt-6 flex-wrap">
                <h2 class="mb-0">Berita & Kegiatan</h2>
                <a href="{{ route('root.allBerita') }}" class="btn btn-success mt-2 mt-sm-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                    </svg>
                    <span class="d-none d-sm-inline">Lihat Semua Berita</span>
                    <span class="d-sm-none">Semua</span>
                </a>
            </div>
            <div class="row g-4">
                @if(isset($berita) && count($berita) > 0)
                    @foreach($berita as $news)
                        <div class="col-12 col-sm-6 col-lg-6">
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
                                    <h3 class="card-title h4">{{ $news->name }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($news->content), 150) }}</p>
                                    <a href="{{ route('root.viewBerita', $news->slug) }}" class="btn btn-success">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            <div>Belum ada berita terbaru</div>
                            <small>Berita akan muncul di sini ketika tersedia</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-6">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap">
                <h2 class="mb-0">Galeri Kampus</h2>
                <a href="/gallery" class="btn btn-primary mt-2 mt-sm-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 8h.01"></path>
                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                    </svg>
                    <span class="d-none d-sm-inline">Lihat Semua Galeri</span>
                    <span class="d-sm-none">Galeri</span>
                </a>
            </div>
            @if(isset($gallery) && is_array($gallery) && count($gallery) > 0)
                <div class="gallery-slider">
                    <div class="gallery-track" id="galleryTrack">
                        @foreach($gallery as $item)
                            <div class="gallery-item">
                                <div class="card rounded-4 overflow-hidden news-card h-100">
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
                    </div>
                    
                    @if(count($gallery) > 3)
                        <button class="gallery-nav prev" id="galleryPrev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="gallery-nav next" id="galleryNext">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    @endif
                </div>
                
                @if(count($gallery) > 1)
                    <div class="gallery-dots" id="galleryDots">
                        @for($i = 0; $i < count($gallery); $i++)
                            <div class="gallery-dot {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}"></div>
                        @endfor
                    </div>
                @endif
            @else
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card rounded-4 overflow-hidden news-card h-100">
                            <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Campus Life" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Kehidupan Kampus</h5>
                                <p class="text-muted">Suasana akademik yang mendukung</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card rounded-4 overflow-hidden news-card h-100">
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Library" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Perpustakaan Modern</h5>
                                <p class="text-muted">Pusat sumber daya pembelajaran</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card rounded-4 overflow-hidden news-card h-100">
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800&h=600" class="card-img-top" alt="Laboratory" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Laboratorium Canggih</h5>
                                <p class="text-muted">Fasilitas penelitian terkini</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-6">
        <div class="container">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                @php
                    $faq = $webs->faq_section ? json_decode($webs->faq_section, true) : [
                        ['question' => 'Bagaimana cara mendaftar kuliah?', 'answer' => 'Pendaftaran dapat dilakukan secara online melalui website PMB kami. Ikuti langkah-langkah yang tersedia dan siapkan dokumen yang diperlukan.'],
                        ['question' => 'Apa saja program beasiswa yang tersedia?', 'answer' => 'Kami menyediakan berbagai program beasiswa, termasuk beasiswa akademik, beasiswa prestasi non-akademik, dan beasiswa kemitraan dengan industri.'],
                        ['question' => 'Bagaimana sistem perkuliahan hybrid?', 'answer' => 'Sistem perkuliahan hybrid menggabungkan pembelajaran tatap muka dan online, didukung platform digital yang memudahkan akses materi dan interaksi.']
                    ];
                @endphp
                
                @foreach($faq as $index => $item)
                    <div class="accordion-item rounded-4 mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
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
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-lg-start text-center">
                    <h2 class="mb-3">{{ $webs->cta_title ?? 'Siap Bergabung dengan Kami?' }}</h2>
                    <p class="text-white mb-0">{{ $webs->cta_subtitle ?? 'Daftar sekarang dan mulai perjalanan akademik Anda bersama kami.' }}</p>
                </div>
                <div class="col-lg-4 text-lg-end text-center mt-4 mt-lg-0">
                    <a href="{{ $webs->cta_button_link ?? route('pmb.registration.form') }}" class="btn btn-primary btn-lg">{{ $webs->cta_button_text ?? 'Daftar Sekarang' }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom-js')
    <!-- Load optimized JavaScript -->
    <script src="{{ asset('js/homepage-optimized.js') }}" defer></script>
@endsection
