@extends('core-themes.core-mainpage')

@section('custom-css')
<style>
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

    .values-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .values-card:hover {
        transform: translateY(-5px);
    }

    .values-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
    }

    /* New styles for mission list */
    .mission-content {
        text-align: left;
        padding: 0 1.5rem;
    }

    .mission-list {
        margin-top: 1rem;
    }

    .mission-item {
        display: flex;
        margin-bottom: 0.5rem;
        align-items: flex-start;
    }

    .mission-number {
        font-weight: bold;
        color: var(--tblr-success);
        margin-right: 0.5rem;
        flex-shrink: 0;
    }

    .mission-text {
        flex-grow: 1;
    }

    .vision-content {
        text-align: center;
        font-style: italic;
        font-size: 1.1rem;
        padding: 0 1rem;
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 40vh;
            padding: 3rem 0;
        }
        
        .mission-content,
        .vision-content {
            padding: 0 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center text-white">
    <div class="container hero-content">
        <h1 class="display-4 fw-bold mb-4">Visi & Misi</h1>
        <p class="fs-5 mb-4">Mengenal visi, misi, dan tujuan institusi kami</p>
    </div>
</section>

<div class="py-6">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                        </svg>
                        Beranda
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Visi & Misi</li>
            </ol>
        </nav>

        @if($page && $page->is_active)
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Vision and Mission Cards -->
                    @if($page->additional_data && is_array($page->additional_data))
                        <div class="row g-4 mb-4">
                            <!-- Vision Card -->
                            @if(isset($page->additional_data['vision']) && !empty(trim($page->additional_data['vision'])))
                                <div class="col-md-6">
                                    <div class="card vision-mission-card h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="vision-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </div>
                                            <h4 class="mb-3">VISI</h4>
                                            <div class="vision-content">
                                                {{ trim($page->additional_data['vision']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Mission Card -->
                            @if(isset($page->additional_data['mission']) && !empty(trim($page->additional_data['mission'])))
                                <div class="col-md-6">
                                    <div class="card vision-mission-card h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="mission-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-target" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M12 12m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0" />
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                </svg>
                                            </div>
                                            <h4 class="mb-3">MISI</h4>
                                            <div class="mission-content">
                                                @php
                                                    // Clean mission text by removing existing numbers
                                                    $missionText = preg_replace('/^\d+\.\s*/m', '', $page->additional_data['mission']);
                                                    // Split by newlines and clean each item
                                                    $missions = array_filter(array_map('trim', explode("\n", $missionText)));
                                                @endphp
                                                
                                                <div class="mission-list">
                                                    @foreach($missions as $index => $mission)
                                                        @if(!empty(trim($mission)))
                                                            <div class="mission-item">
                                                                <span class="mission-number">{{ $loop->iteration }}.</span>
                                                                <span class="mission-text">{{ trim($mission) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Other content sections (tujuan, sasaran) -->
                    @if(isset($page->additional_data['tujuan']))
                        <div class="card content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-flag me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M5 5a5 5 0 0 1 7 0a5 5 0 0 0 7 0v9a5 5 0 0 1 -7 0a5 5 0 0 0 -7 0v-9z" />
                                        <path d="M5 21v-7" />
                                    </svg>
                                    TUJUAN
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-content">
                                    {!! $page->additional_data['tujuan'] !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($page->additional_data['sasaran']))
                        <div class="card content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-crosshair me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M4 8v-2a2 2 0 0 1 2 -2h2" />
                                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                                        <path d="M16 20h2a2 2 0 0 0 2 -2v-2" />
                                        <path d="M9 12l6 0" />
                                        <path d="M12 9l0 6" />
                                    </svg>
                                    SASARAN
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-content">
                                    {!! $page->additional_data['sasaran'] !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card content-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 9h.01" />
                                    <path d="M11 12h1v4h1" />
                                </svg>
                                Informasi Institusi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M3 21h18" />
                                        <path d="M5 21v-14l8 -4v18" />
                                        <path d="M19 21v-10l-6 -4" />
                                        <path d="M9 9v.01" />
                                        <path d="M9 12v.01" />
                                        <path d="M9 15v.01" />
                                        <path d="M9 18v.01" />
                                    </svg>
                                    Nama Institusi:
                                </strong><br>
                                {{ $webs->school_name ?? 'Nama Institusi' }}
                            </div>
                            @if($webs->school_address)
                                <div class="mb-3">
                                    <strong>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                        </svg>
                                        Alamat:
                                    </strong><br>
                                    {{ $webs->school_address }}
                                </div>
                            @endif
                            @if($webs->school_phone)
                                <div class="mb-3">
                                    <strong>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                        </svg>
                                        Telepon:
                                    </strong><br>
                                    {{ $webs->school_phone }}
                                </div>
                            @endif
                            @if($webs->school_email)
                                <div class="mb-3">
                                    <strong>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                        Email:
                                    </strong><br>
                                    <a href="mailto:{{ $webs->school_email }}">{{ $webs->school_email }}</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="card content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M9 15l6 -6" />
                                    <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                    <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" />
                                </svg>
                                Link Terkait
                            </h3>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('root.institution.profile') }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M3 21h18" />
                                    <path d="M5 21v-14l8 -4v18" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9v.01" />
                                    <path d="M9 12v.01" />
                                    <path d="M9 15v.01" />
                                    <path d="M9 18v.01" />
                                </svg>
                                Profil Institusi
                            </a>
                            <a href="{{ route('root.institution.structure') }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hierarchy me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M12 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M19 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M6.5 17.5l5.5 -4.5l5.5 4.5" />
                                    <path d="M12 7l0 6" />
                                </svg>
                                Struktur Organisasi
                            </a>
                            <a href="{{ route('root.institution.facilities') }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M3 21h18" />
                                    <path d="M5 21v-14l8 -4v18" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9v.01" />
                                    <path d="M9 12v.01" />
                                    <path d="M9 15v.01" />
                                    <path d="M9 18v.01" />
                                </svg>
                                Fasilitas
                            </a>
                            <a href="{{ route('root.institution.gallery') }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M15 8h.01" />
                                    <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                </svg>
                                Galeri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card content-card">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            <h3>Visi & Misi</h3>
                            <p class="text-muted">Informasi visi dan misi institusi belum tersedia atau sedang dalam proses pembaruan.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Values Section -->
@if($page && $page->is_active && $page->additional_data && isset($page->additional_data['nilai']))
<div class="py-6 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Nilai-Nilai Institusi</h2>
            <p class="lead">Nilai-nilai yang menjadi landasan dalam setiap aktivitas institusi</p>
        </div>
        
        <div class="row g-4">
            @foreach($page->additional_data['nilai'] as $index => $nilai)
                <div class="col-lg-3 col-md-6">
                    <div class="card values-card text-center">
                        <div class="card-body p-4">
                            <div class="values-icon" style="background: {{ ['var(--tblr-primary)', 'var(--tblr-success)', 'var(--tblr-warning)', 'var(--tblr-danger)', 'var(--tblr-purple)', 'var(--tblr-orange)'][$index % 6] }};">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-{{ ['heart', 'handshake', 'star', 'shield', 'bulb', 'users'][$index % 6] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    @if($index % 6 == 0)
                                        <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.566" />
                                    @elseif($index % 6 == 1)
                                        <path d="M11 17a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0" />
                                        <path d="M5 7a4 4 0 0 1 4 -4h6a4 4 0 0 1 4 4v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10z" />
                                    @elseif($index % 6 == 2)
                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                    @elseif($index % 6 == 3)
                                        <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                                    @elseif($index % 6 == 4)
                                        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                                        <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" />
                                    @else
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    @endif
                                </svg>
                            </div>
                            <h5 class="card-title">{{ $nilai['nama'] ?? '' }}</h5>
                            <p class="card-text">{{ $nilai['deskripsi'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection