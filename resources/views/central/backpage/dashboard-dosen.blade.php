@extends('central.backpage.core-backpage-dosen')

@section('custom-css')
<style>
    /* Tema Ma'had Aly Maal Amin - Warna Hijau Islami Modern dengan Dark/Light Mode Support */
    :root {
        --mahad-primary: #2d7d32;
        --mahad-primary-light: #4caf50;
        --mahad-primary-dark: #1b5e20;
        --mahad-secondary: #388e3c;
        --mahad-accent: #66bb6a;
        --mahad-gold: #ffc107;
        --mahad-cream: #f8f6f0;
        --mahad-text: #2e2e2e;
        --mahad-blue-dark: #1a2a3a; /* New dark blue color */
        --mahad-blue-darker: #121f2b; /* Darker blue */
    }
    
    /* Light Mode */
    [data-bs-theme="light"] {
        --mahad-bg: linear-gradient(135deg, var(--mahad-cream) 0%, #ffffff 100%);
        --mahad-card-bg: #ffffff;
        --mahad-text-color: var(--mahad-text);
        --mahad-border-color: rgba(45, 125, 50, 0.1);
        --mahad-shadow: 0 4px 20px rgba(45, 125, 50, 0.1);
        --mahad-shadow-hover: 0 8px 30px rgba(45, 125, 50, 0.15);
        --mahad-list-bg: var(--mahad-cream);
        --mahad-header-bg: linear-gradient(45deg, var(--mahad-primary), var(--mahad-secondary));
    }
    
    /* Dark Mode - Updated with blueish theme */
    [data-bs-theme="dark"] {
        --mahad-bg: linear-gradient(135deg, var(--mahad-blue-darker) 0%, var(--mahad-blue-dark) 100%);
        --mahad-card-bg: var(--mahad-blue-dark);
        --mahad-text-color: #e0e0e0;
        --mahad-border-color: rgba(76, 175, 80, 0.2);
        --mahad-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        --mahad-shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.5);
        --mahad-list-bg: #243445;
        --mahad-header-bg: linear-gradient(45deg, #2c3e50, #34495e);
    }
    
    /* Dark mode body override */
    [data-bs-theme="dark"] body {
        background: var(--mahad-bg) !important;
        color: var(--mahad-text-color) !important;
    }
    
    body {
        background: var(--mahad-bg);
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--mahad-text-color);
        min-height: 100vh;
    }
    
    .card-mahad {
        border: none;
        border-radius: 12px;
        box-shadow: var(--mahad-shadow);
        transition: all 0.3s ease;
        background: var(--mahad-card-bg);
        color: var(--mahad-text-color);
        overflow: hidden;
        position: relative;
    }
    
    .card-mahad::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--mahad-primary), var(--mahad-accent));
    }
    
    .card-mahad:hover {
        transform: translateY(-5px);
        box-shadow: var(--mahad-shadow-hover);
    }
    
    .card-mahad-primary {
        background: linear-gradient(135deg, var(--mahad-primary) 0%, var(--mahad-secondary) 100%) !important;
        color: white !important;
        border-left: 5px solid var(--mahad-gold);
    }
    
    .progress-mahad .progress-bar {
        background: linear-gradient(45deg, var(--mahad-primary), var(--mahad-primary-light));
        border-radius: 10px;
    }
    
    .badge-mahad {
        background: var(--mahad-primary);
        color: white;
        border-radius: 20px;
        padding: 8px 15px;
        font-weight: 600;
    }
    
    .btn-mahad {
        background: linear-gradient(45deg, var(--mahad-primary), var(--mahad-secondary));
        border: none;
        color: white !important;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-mahad:hover {
        background: linear-gradient(45deg, var(--mahad-primary-dark), var(--mahad-primary));
        transform: translateY(-2px);
        box-shadow: 0 7px 15px rgba(45, 125, 50, 0.3);
        color: white !important;
    }
    
    .text-mahad {
        color: var(--mahad-primary) !important;
        font-weight: 600;
    }
    
    .welcome-card {
        background: linear-gradient(135deg, var(--mahad-primary) 0%, var(--mahad-secondary) 100%) !important;
        color: white !important;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(45, 125, 50, 0.3);
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" font-size="50" fill="rgba(255,255,255,0.1)">☪</text></svg>') no-repeat;
        background-size: 200px;
        transform: rotate(15deg);
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .icon-mata-kuliah { 
        background: linear-gradient(45deg, var(--mahad-primary), var(--mahad-primary-light)); 
    }
    .icon-mahasantri { 
        background: linear-gradient(45deg, var(--mahad-secondary), var(--mahad-accent)); 
    }
    .icon-tugas { 
        background: linear-gradient(45deg, var(--mahad-gold), #ffb300); 
    }
    .icon-jadwal { 
        background: linear-gradient(45deg, #2196f3, #64b5f6); 
    }
    
    .arabic-text {
        font-family: 'Amiri', 'Times New Roman', serif;
        font-size: 1.1em;
        color: var(--mahad-gold) !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    
    .list-group-item-mahad {
        border: none;
        border-radius: 8px;
        margin-bottom: 10px;
        background: var(--mahad-list-bg);
        transition: all 0.3s ease;
        color: var(--mahad-text-color);
        padding: 12px 15px;
    }
    
    .list-group-item-mahad:hover {
        background: var(--mahad-primary-light) !important;
        color: white !important;
        transform: translateX(5px);
    }
    
    .progress-sm {
        height: 8px;
        border-radius: 10px;
        background: var(--mahad-border-color);
    }
    
    .card-header-mahad {
        background: var(--mahad-header-bg) !important;
        color: white !important;
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 15px 20px;
    }
    
    .quick-action-card {
        background: var(--mahad-card-bg);
        border: 2px solid var(--mahad-border-color);
        border-radius: 15px;
        transition: all 0.3s ease;
        color: var(--mahad-text-color);
    }
    
    .quick-action-card:hover {
        border-color: var(--mahad-primary);
        background: var(--mahad-primary) !important;
        color: white !important;
    }
    
    /* Modern UI Enhancements */
    .page-header {
        padding: 20px 0;
    }
    
    .page-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 5px;
    }
    
    .page-pretitle {
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }
    
    .avatar {
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 3px solid rgba(255,255,255,0.2);
    }
    
    /* Dark mode specific adjustments */
    [data-bs-theme="dark"] .page-title {
        color: var(--mahad-primary-light) !important;
    }
    
    [data-bs-theme="dark"] .page-pretitle {
        color: var(--mahad-gold) !important;
    }
    
    [data-bs-theme="dark"] .subheader {
        color: #cccccc !important;
    }
    
    [data-bs-theme="dark"] .text-muted {
        color: #999999 !important;
    }
    
    [data-bs-theme="dark"] .h2 {
        color: var(--mahad-text-color) !important;
    }
    
    /* New modern elements */
    .stats-card {
        position: relative;
        overflow: hidden;
    }
    
    .stats-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        transform: translate(30%, 30%);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-mahad {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }
    }
    
    /* Animation for cards */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-mahad {
        animation: fadeIn 0.5s ease forwards;
    }
    
    /* Delay animations for staggered effect */
    .card-mahad:nth-child(1) { animation-delay: 0.1s; }
    .card-mahad:nth-child(2) { animation-delay: 0.2s; }
    .card-mahad:nth-child(3) { animation-delay: 0.3s; }
    .card-mahad:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle arabic-text">
                        بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم
                    </div>
                    <h2 class="page-title text-mahad">
                        Dashboard Ustadz
                    </h2>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <div class="h5 mb-0 text-mahad">{{ now()->locale('id')->translatedFormat('l') }}</div>
                        <div class="text-muted">{{ now()->locale('id')->translatedFormat('d F Y') }}</div>
                        <div class="small text-muted">{{ now()->locale('id')->translatedFormat('H:i') }} WIB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <!-- Welcome Card -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card welcome-card card-mahad">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-xl" style="background-image: url({{ $user->photo ? asset('storage/images/profile/' . $user->photo) : asset('storage/images/profile/default.jpg') }})"></span>
                                </div>
                                <div class="col">
                                    <h3 class="card-title mb-2 text-white arabic-text">
                                        السَّلاَمُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَاتُهُ
                                    </h3>
                                    <h4 class="mb-2 text-white">
                                        {{ $user->title_front }} {{ $user->name }} {{ $user->title_behind }}
                                    </h4>
                                    <p class="text-white-75 mb-0">
                                        بارك الله فيكم - Semoga Allah memberikan keberkahan dalam setiap langkah pengajaran hari ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-mahad stats-card">
                        <div class="card-body">
                            <div class="stats-icon icon-mata-kuliah mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <div class="subheader mb-2">Mata Kuliah Diampu</div>
                            @if($mata_kuliah_diampu->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($mata_kuliah_diampu as $mk)
                                    <div class="list-group-item list-group-item-mahad px-0 py-2 border-0">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $mk->name }}</span>
                                            @if($mk->dosen1_id == $user->id)
                                                <span class="badge bg-success text-white">Utama</span>
                                            @elseif($mk->dosen2_id == $user->id)
                                                <span class="badge bg-warning">Pendamping 1</span>
                                            @elseif($mk->dosen3_id == $user->id)
                                                <span class="badge bg-info">Pendamping 2</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <div class="text-muted">Tidak ada mata kuliah</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-mahad stats-card text-center">
                        <div class="card-body">
                            <div class="stats-icon icon-mahasantri mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="h2 mb-3 text-mahad">{{ $total_mahasantri }}</div>
                            <div class="subheader mb-2">Total Mahasantri</div>
                            <div class="row text-center mb-2">
                                <div class="col-6">
                                    <div class="h4 text-success mb-0">{{ $total_mahasantri_sudah_krs }}</div>
                                    <small class="text-muted">Sudah KRS</small>
                                </div>
                                <div class="col-6">
                                    <div class="h4 text-warning mb-0">{{ $total_mahasantri_belum_krs }}</div>
                                    <small class="text-muted">Belum KRS</small>
                                </div>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm progress-mahad">
                                        <div class="progress-bar bg-success" style="width: {{ $total_mahasantri > 0 ? ($total_mahasantri_sudah_krs / $total_mahasantri) * 100 : 0 }}%" role="progressbar"></div>
                                        <div class="progress-bar bg-warning" style="width: {{ $total_mahasantri > 0 ? ($total_mahasantri_belum_krs / $total_mahasantri) * 100 : 0 }}%" role="progressbar"></div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <small class="text-muted">{{ $total_mahasantri > 0 ? number_format(($total_mahasantri_sudah_krs / $total_mahasantri) * 100, 0) : 0 }}%</small>
                                </div>
                            </div>
                            <div class="text-muted small">Di Program Studi</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-mahad stats-card text-center">
                        <div class="card-body">
                            <div class="stats-icon icon-tugas mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14,2 14,8 20,8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10,9 9,9 8,9"></polyline>
                                </svg>
                            </div>
                            <div class="h2 mb-3 text-mahad">{{ $tugas_pending }}</div>
                            <div class="subheader mb-2">Nilai Pending</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm progress-mahad">
                                        <div class="progress-bar" style="width: {{ $progress_tugas }}%" role="progressbar"></div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <small class="text-muted">{{ number_format($progress_tugas, 0) }}%</small>
                                </div>
                            </div>
                            <div class="text-muted small">
                                <strong>Mahasiswa yang sudah KRS</strong><br>
                                tapi belum diinput nilainya
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-mahad stats-card text-center">
                        <div class="card-body">
                            <div class="stats-icon icon-jadwal mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="h2 mb-3 text-mahad">{{ $jadwal_hari_ini->count() }}</div>
                            <div class="subheader mb-2">Jadwal Hari Ini</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm progress-mahad">
                                        <div class="progress-bar" style="width: {{ $progress_jadwal }}%" role="progressbar"></div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <small class="text-muted">{{ number_format($progress_jadwal, 0) }}%</small>
                                </div>
                            </div>
                            <div class="text-muted small">Kelas mengajar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card card-mahad">
                        <div class="card-header card-header-mahad">
                            <h3 class="card-title text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                Aksi Cepat
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="{{ route($spref . 'profile-render') }}" class="btn btn-mahad w-100">
                                        <i class="fas fa-user-edit me-2"></i>
                                        Edit Profile
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="{{ route($spref . 'absensi-render') }}" class="btn btn-mahad w-100">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        Absensi
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="{{ route($spref . 'mata-kuliah-render') }}" class="btn btn-mahad w-100">
                                        <i class="fas fa-book me-2"></i>
                                        Mata Kuliah
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="{{ route($spref . 'nilai.input-render') }}" class="btn btn-mahad w-100">
                                        <i class="fas fa-tasks me-2"></i>
                                        Input Nilai
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="{{ route($spref . 'nilai.rekap-render') }}" class="btn btn-mahad w-100">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Rekap Nilai
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mahasiswa Bimbingan (Dosen Penasehat) -->
            @if(isset($user->mahasiswaBimbingan) && $user->mahasiswaBimbingan->count() > 0)
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card card-mahad">
                        <div class="card-header card-header-mahad">
                            <h3 class="card-title text-white">
                                <i class="fas fa-users me-2"></i>
                                Mahasiswa Bimbingan (Dosen Penasehat)
                                <span class="badge bg-light text-dark ms-2">{{ $user->mahasiswaBimbingan->count() }} Mahasiswa</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($user->mahasiswaBimbingan as $mahasiswa)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ $mahasiswa->photo }}" alt="Photo" 
                                                     class="rounded-circle me-3" width="50" height="50" 
                                                     style="object-fit: cover; border: 2px solid var(--mahad-primary);">
                                                <div class="flex-fill">
                                                    <h6 class="mb-1 text-mahad">{{ $mahasiswa->name }}</h6>
                                                    <small class="text-muted">{{ $mahasiswa->nim }}</small>
                                                </div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="text-muted small">Program Studi</div>
                                                    <div class="fw-bold small">{{ $mahasiswa->prodi->name ?? '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">Kelas</div>
                                                    <div class="fw-bold small">{{ $mahasiswa->kelas->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="row text-center mt-2">
                                                <div class="col-6">
                                                    <div class="text-muted small">Semester</div>
                                                    <div class="fw-bold small">{{ $mahasiswa->semester ?? '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">Status</div>
                                                    <span class="badge bg-success small">{{ $mahasiswa->type }}</span>
                                                </div>
                                            </div>
                                            <div class="mt-3 d-flex gap-1">
                                                @if($mahasiswa->phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mahasiswa->phone) }}" 
                                                   class="btn btn-success btn-sm flex-fill" target="_blank">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                                @endif
                                                @if($mahasiswa->email)
                                                <a href="mailto:{{ $mahasiswa->email }}" 
                                                   class="btn btn-primary btn-sm flex-fill">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            <div class="row row-deck row-cards">
                <div class="col-md-6">
                    <div class="card card-mahad">
                        <div class="card-header card-header-mahad">
                            <h3 class="card-title text-white">Jadwal Mengajar Hari Ini</h3>
                        </div>
                        <div class="card-body">
                            @if($jadwal_hari_ini->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($jadwal_hari_ini as $jadwal)
                                    <div class="list-group-item list-group-item-mahad">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="badge badge-mahad">{{ $jadwal->jam_mulai ?? '08:00' }}</span>
                                            </div>
                                            <div class="col text-truncate">
                                                <strong>{{ $jadwal->mataKuliah->name ?? 'Mata Kuliah' }}</strong>
                                                <div class="text-muted small">
                                                    {{ $jadwal->ruang->nama ?? 'Ruang' }} - 
                                                    @if(isset($jadwal->kelas) && $jadwal->kelas->count() > 0)
                                                        {{ $jadwal->kelas->first()->nama }}
                                                    @else
                                                        Kelas
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <div>Tidak ada jadwal mengajar hari ini</div>
                                        <small>Nikmati waktu istirahat Anda</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-mahad">
                        <div class="card-header card-header-mahad">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="card-title text-white mb-2">
                                        Nilai Pending Review
                                        <small class="d-block mt-1" style="font-size: 0.8em; opacity: 0.9;">
                                            Mahasiswa yang sudah mengambil mata kuliah (KRS) tapi belum diinput nilai akhirnya
                                        </small>
                                    </h3>
                                </div>
                                <div class="ms-3">
                                    <select class="form-select form-select-sm" id="filterMataKuliah" style="min-width: 200px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white;">
                                        <option value="">Semua Mata Kuliah</option>
                                        @foreach($mata_kuliah_diampu as $mk)
                                            <option value="{{ $mk->id }}" style="color: #333;">{{ $mk->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($tugas_terbaru->count() > 0)
                                @if(config('app.debug'))
                                <div class="alert alert-info mb-3">
                                    <small>
                                        <strong>Debug Info:</strong><br>
                                        - Tugas Pending: {{ $tugas_pending }}<br>
                                        - Tugas Terbaru Count: {{ $tugas_terbaru->count() }}<br>
                                        - Data Available: Yes
                                    </small>
                                </div>
                                @endif
                                <div class="list-group list-group-flush">
                                    @foreach($tugas_terbaru as $tugas)
                                    <div class="list-group-item list-group-item-mahad">
                                        <div class="row align-items-start">
                                            <div class="col-auto">
                                                <span class="badge bg-warning">Pending</span>
                                            </div>
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong class="text-primary">{{ $tugas->mataKuliah->name ?? 'Mata Kuliah' }}</strong>
                                                        @if(isset($tugas->mataKuliah->code) && $tugas->mataKuliah->code)
                                                        <small class="text-muted d-block">({{ $tugas->mataKuliah->code }})</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-muted small">
                                                            <i class="fas fa-user me-1"></i>
                                                            <strong>{{ $tugas->mahasantri->name ?? 'Nama Mahasiswa' }}</strong>
                                                        </div>
                                                        @if(isset($tugas->mahasantri->nim) && $tugas->mahasantri->nim)
                                                        <div class="text-muted small">
                                                            <i class="fas fa-id-card me-1"></i>
                                                            NIM: {{ $tugas->mahasantri->nim }}
                                                        </div>
                                                        @endif
                                                        @if(isset($tugas->kelas_info) && $tugas->kelas_info)
                                                        <div class="text-muted small">
                                                            <i class="fas fa-graduation-cap me-1"></i>
                                                            {{ $tugas->kelas_info }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $tugas->status ?? 'Belum ada nilai akhir' }}
                                    </span>
                                    <a href="{{ route($spref . 'nilai.input-render') }}?mata_kuliah_id={{ $tugas->mataKuliah->id ?? '' }}&mahasiswa_id={{ $tugas->mahasantri->id ?? '' }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-edit me-1"></i>
                                        Input Nilai
                                    </a>
                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @if($tugas_pending > $tugas_terbaru->count())
                                    <div class="list-group-item list-group-item-mahad">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-ellipsis-h me-2"></i>
                                            <small>Dan {{ $tugas_pending - $tugas_terbaru->count() }} mahasiswa lainnya yang belum diinput nilai</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @elseif($tugas_pending > 0)
                                @if(config('app.debug'))
                                <div class="alert alert-warning mb-3">
                                    <small>
                                        <strong>Debug Info:</strong><br>
                                        - Tugas Pending: {{ $tugas_pending }}<br>
                                        - Tugas Terbaru Count: 0<br>
                                        - Data Available: No (showing fallback)
                                    </small>
                                </div>
                                @endif
                                <div class="text-center py-4">
                                    <div class="text-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14,2 14,8 20,8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10,9 9,9 8,9"></polyline>
                                        </svg>
                                        <div><strong>{{ $tugas_pending }} mahasiswa belum diinput nilai</strong></div>
                                        <small>Mahasiswa sudah mengambil mata kuliah (KRS)<br>tapi belum ada nilai akhir yang diinput</small>
                                        <div class="mt-3">
                                            <a href="{{ route($spref . 'nilai.input-render') }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                Input Nilai Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                            <polyline points="20,6 9,17 4,12"></polyline>
                                        </svg>
                                        <div>Semua nilai KHS sudah diinput</div>
                                        <small class="arabic-text">بارك الله فيكم</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter Mata Kuliah
    const filterSelect = document.getElementById('filterMataKuliah');
    const tugasItems = filterSelect ? filterSelect.closest('.card').querySelectorAll('.list-group-item-mahad') : [];
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedMataKuliahName = this.options[this.selectedIndex].text;
            let visibleCount = 0;
            
            console.log('Filter selected:', selectedMataKuliahName); // Debug
            
            tugasItems.forEach(function(item) {
                const mataKuliahElement = item.querySelector('.text-primary');
                if (mataKuliahElement) {
                    const mataKuliahName = mataKuliahElement.textContent.trim();
                    console.log('Checking:', mataKuliahName, 'against:', selectedMataKuliahName); // Debug
                    
                    // Show all if "Semua Mata Kuliah" selected, or if names match
                    if (selectedMataKuliahName === 'Semua Mata Kuliah' || mataKuliahName === selectedMataKuliahName) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
            
            console.log('Visible count:', visibleCount); // Debug
            
            // Update counter jika ada
            const debugInfo = document.querySelector('.alert-info small');
            if (debugInfo) {
                if (selectedMataKuliahName === 'Semua Mata Kuliah') {
                    debugInfo.innerHTML = debugInfo.innerHTML.replace(
                        /- Tugas Terbaru Count: \d+ \(filtered\)/,
                        `- Tugas Terbaru Count: ${visibleCount}`
                    );
                } else {
                    debugInfo.innerHTML = debugInfo.innerHTML.replace(
                        /- Tugas Terbaru Count: \d+/,
                        `- Tugas Terbaru Count: ${visibleCount} (filtered)`
                    );
                }
            }
        });
    }
    
    
    // Progress Indicator untuk setiap mata kuliah
    function updateProgressIndicators() {
        const mataKuliahCards = document.querySelectorAll('.list-group-item-mahad');
        const mataKuliahProgress = {};
        
        // Hitung progress per mata kuliah
        mataKuliahCards.forEach(function(card) {
            const mataKuliahElement = card.querySelector('.text-primary');
            if (mataKuliahElement) {
                const mataKuliahName = mataKuliahElement.textContent.trim();
                if (!mataKuliahProgress[mataKuliahName]) {
                    mataKuliahProgress[mataKuliahName] = {
                        pending: 0,
                        total: 0
                    };
                }
                mataKuliahProgress[mataKuliahName].pending++;
                mataKuliahProgress[mataKuliahName].total++;
            }
        });
        
        // Update progress di mata kuliah diampu
        const mataKuliahItems = document.querySelector('.icon-mata-kuliah')?.closest('.card')?.querySelectorAll('.list-group-item-mahad') || [];
        mataKuliahItems.forEach(function(item) {
            const mataKuliahName = item.querySelector('span').textContent.trim();
            if (mataKuliahProgress[mataKuliahName]) {
                const progress = mataKuliahProgress[mataKuliahName];
                const percentage = Math.round((progress.pending / progress.total) * 100);
                
                // Tambahkan progress bar mini
                if (!item.querySelector('.progress-mini')) {
                    const progressBar = document.createElement('div');
                    progressBar.className = 'progress progress-mini mt-1';
                    progressBar.style.height = '4px';
                    progressBar.innerHTML = `
                        <div class="progress-bar bg-warning" style="width: ${percentage}%" title="${progress.pending} pending dari ${progress.total} mahasiswa"></div>
                    `;
                    item.appendChild(progressBar);
                }
            }
        });
    }
    
    // Jalankan fungsi-fungsi
    updateProgressIndicators();
});
</script>
@endsection
