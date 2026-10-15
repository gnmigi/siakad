@extends('core-themes.core-mainpage')

@section('custom-css')
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(var(--tblr-primary-rgb), 0.25), rgba(var(--tblr-info-rgb), 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=2186&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
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

    .facility-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        height: 100%;
        overflow: hidden;
    }

    .facility-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .facility-image {
        height: 200px;
        object-fit: cover;
        border-radius: 1rem 1rem 0 0;
    }

    .category-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
    }

    .category-icon {
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

    .stats-card {
        border: none;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .virtual-tour-container {
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 40vh;
            padding: 3rem 0;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center text-white">
    <div class="container hero-content">
        <h1 class="display-4 fw-bold mb-4">Fasilitas</h1>
        <p class="fs-5 mb-4">Mengenal berbagai fasilitas dan sarana prasarana institusi kami</p>
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
                <li class="breadcrumb-item active" aria-current="page">Fasilitas</li>
            </ol>
        </nav>

        @if($page && $page->is_active)
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Main Content -->
                    <div class="card content-card mb-4">
                        <div class="card-body p-4">
                            <h2 class="mb-4">{{ $page->title ?? 'Fasilitas' }}</h2>
                            
                            @if($page->image)
                                <div class="text-center mb-4">
                                    <img src="{{ asset($page->image) }}" 
                                         alt="{{ $page->title }}" 
                                         class="img-fluid rounded-4">
                                </div>
                            @endif
                            
                            <div class="text-content">
                                {!! $page->content !!}
                            </div>
                        </div>
                    </div>

                    <!-- Facilities Grid -->
                    @if($page->additional_data && is_array($page->additional_data) && isset($page->additional_data['facilities']))
                        <div class="row g-4">
                            @foreach($page->additional_data['facilities'] as $fasilitas)
                                <div class="col-md-6">
                                    <div class="card facility-card">
                        @if(isset($fasilitas['image']) && $fasilitas['image'])
                            <img src="{{ asset('storage/images/institusi/facilities/' . $fasilitas['image']) }}" 
                                 class="card-img-top facility-image" 
                                 alt="{{ $fasilitas['name'] ?? '' }}">
                        @endif
                        <div class="card-body p-4">
                            <h5 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M3 21h18" />
                                    <path d="M5 21v-14l8 -4v18" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9v.01" />
                                    <path d="M9 12v.01" />
                                    <path d="M9 15v.01" />
                                    <path d="M9 18v.01" />
                                </svg>
                                {{ $fasilitas['name'] ?? '' }}
                            </h5>
                            <p class="card-text">{{ $fasilitas['description'] ?? '' }}</p>
                                            <div class="d-flex flex-column gap-1">
                                                @if(isset($fasilitas['kapasitas']))
                                                    <small class="text-muted d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                        </svg>
                                                        Kapasitas: {{ $fasilitas['kapasitas'] }}
                                                    </small>
                                                @endif
                                                @if(isset($fasilitas['lokasi']))
                                                    <small class="text-muted d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                                        </svg>
                                                        {{ $fasilitas['lokasi'] }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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

                    <!-- Facility Statistics -->
                    @if($page && $page->additional_data && isset($page->additional_data['statistik']))
                        <div class="card stats-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M4 20l14 0" />
                                    </svg>
                                    Statistik Fasilitas
                                </h3>
                            </div>
                            <div class="card-body">
                                @foreach($page->additional_data['statistik'] as $stat)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $stat['nama'] ?? '' }}</span>
                                        <span class="badge bg-primary">{{ $stat['jumlah'] ?? 0 }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

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
                            <a href="{{ route('root.institution.vision-mission') }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                                Visi & Misi
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M3 21h18" />
                                <path d="M5 21v-14l8 -4v18" />
                                <path d="M19 21v-10l-6 -4" />
                                <path d="M9 9v.01" />
                                <path d="M9 12v.01" />
                                <path d="M9 15v.01" />
                                <path d="M9 18v.01" />
                            </svg>
                            <h3>Fasilitas</h3>
                            <p class="text-muted">Informasi fasilitas belum tersedia atau sedang dalam proses pembaruan.</p>
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

<!-- Facility Categories -->
@if($page && $page->is_active && $page->additional_data && isset($page->additional_data['kategori_fasilitas']))
<div class="py-6 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Kategori Fasilitas</h2>
            <p class="lead">Berbagai kategori fasilitas yang tersedia untuk mendukung kegiatan akademik dan non-akademik</p>
        </div>
        
        <div class="row g-4">
            @foreach($page->additional_data['kategori_fasilitas'] as $kategori)
                <div class="col-lg-3 col-md-6">
                    <div class="card category-card text-center">
                        <div class="card-body p-4">
                            <div class="category-icon" style="background: {{ $kategori['warna'] ?? 'var(--tblr-primary)' }};">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M3 21h18" />
                                    <path d="M5 21v-14l8 -4v18" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9v.01" />
                                    <path d="M9 12v.01" />
                                    <path d="M9 15v.01" />
                                    <path d="M9 18v.01" />
                                </svg>
                            </div>
                            <h5 class="card-title">{{ $kategori['nama'] ?? '' }}</h5>
                            <p class="card-text">{{ $kategori['deskripsi'] ?? '' }}</p>
                            @if(isset($kategori['jumlah']))
                                <span class="badge bg-primary">{{ $kategori['jumlah'] }} Unit</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Virtual Tour Section -->
@if($page && $page->is_active && $page->additional_data && isset($page->additional_data['virtual_tour']))
<div class="py-6">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Virtual Tour</h2>
            <p class="lead">Jelajahi fasilitas kami secara virtual</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="virtual-tour-container">
                    <div class="ratio ratio-16x9">
                        @if(isset($page->additional_data['virtual_tour']['embed_code']))
                            {!! $page->additional_data['virtual_tour']['embed_code'] !!}
                        @elseif(isset($page->additional_data['virtual_tour']['video_url']))
                            <iframe src="{{ $page->additional_data['virtual_tour']['video_url'] }}" 
                                    allowfullscreen class="rounded-4"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
