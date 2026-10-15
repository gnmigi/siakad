@extends('core-themes.core-backpage')

@section('custom-css')
    <style>
        .stat-card {
            border-radius: 20px;
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .welcome-card {
            background: linear-gradient(135deg, #2E8B57 0%, #228B22 100%);
            color: white;
            border-radius: 25px;
            border: none;
            box-shadow: 0 15px 35px rgba(46, 139, 87, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .recent-activity {
            max-height: 450px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 1.5rem;
            border-left: 4px solid #2E8B57;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            border-radius: 0 15px 15px 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .activity-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.1);
            border-left-color: #228B22;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: #2E8B57;
            border-radius: 50%;
            border: 3px solid white;
        }

        .chart-container {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            min-height: 0 !important;
        }

        #mahasiswaDistributionChart {
            display: block;
            width: 100% !important;
            height: 400px !important;
        }

        .quick-action-btn {
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            text-decoration: none;
            display: block;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .quick-action-btn:hover::before {
            left: 100%;
        }

        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            color: white;
            text-decoration: none;
        }

        .quick-action-btn.btn-primary-custom:hover {
            background: linear-gradient(135deg, #2E8B57 0%, #228B22 100%);
            border-color: #2E8B57;
        }

        .quick-action-btn.btn-success-custom:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            border-color: #20c997;
        }

        .quick-action-btn.btn-info-custom:hover {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            border-color: #17a2b8;
        }

        .quick-action-btn.btn-warning-custom:hover {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border-color: #ffc107;
            color: #212529;
        }

        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            border-radius: 20px 20px 0 0 !important;
            padding: 1.5rem;
        }

        .badge-islamic {
            background: linear-gradient(135deg, #2E8B57 0%, #20c997 100%);
            color: white;
            border-radius: 25px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
        }

        /* Improve badge contrast in stat cards */
        .stats-grid .stat-card .badge {
            color: #ffffff !important;
            font-weight: 600;
        }

        .stats-grid .stat-card[style*="#ffc107"] .badge,
        .stats-grid .stat-card[style*="#fd7e14"] .badge {
            color: #212529 !important;
            background-color: rgba(255, 255, 255, 0.65) !important;
        }

        .stats-grid .stat-card .card-title {
            color: rgba(255, 255, 255, 0.95) !important;
        }

        .islamic-greeting {
            font-family: 'Amiri', serif;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #2E8B57 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            position: relative;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 30px;
            background: linear-gradient(135deg, #2E8B57 0%, #20c997 100%);
            border-radius: 2px;
        }

        /* Dark Mode Styles */
        [data-bs-theme="dark"] .card {
            background-color: #1f2937 !important;
            border: 1px solid #374151;
        }

        [data-bs-theme="dark"] .card-header {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
            border-bottom: 1px solid #374151;
            color: #f9fafb;
        }

        [data-bs-theme="dark"] .card-body {
            color: #f9fafb;
        }

        [data-bs-theme="dark"] .card-title,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] h6 {
            color: #f9fafb !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #d1d5db !important;
        }

        [data-bs-theme="dark"] .activity-item {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
            border-left: 4px solid #2E8B57;
            color: #f9fafb;
        }

        [data-bs-theme="dark"] .activity-item h6 {
            color: #f9fafb !important;
        }

        [data-bs-theme="dark"] .activity-item p {
            color: #d1d5db !important;
        }

        [data-bs-theme="dark"] .activity-item small {
            color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .quick-action-btn {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
            color: #f9fafb !important;
            border: 2px solid #374151;
        }

        [data-bs-theme="dark"] .quick-action-btn h6 {
            color: #f9fafb !important;
        }

        [data-bs-theme="dark"] .quick-action-btn small {
            color: #d1d5db !important;
        }

        [data-bs-theme="dark"] .quick-action-btn:hover {
            color: white !important;
        }

        [data-bs-theme="dark"] .quick-action-btn:hover h6 {
            color: white !important;
        }

        [data-bs-theme="dark"] .quick-action-btn:hover small {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Chart container dark mode */
        [data-bs-theme="dark"] .chart-container {
            background-color: #1f2937;
        }

        /* Ensure gradient text is visible in dark mode */
        [data-bs-theme="dark"] .gradient-text {
            background: linear-gradient(135deg, #4ade80 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Badge adjustments for dark mode */
        [data-bs-theme="dark"] .badge-islamic {
            background: linear-gradient(135deg, #2E8B57 0%, #20c997 100%);
            color: white;
        }

        /* Activity item icons in dark mode */
        [data-bs-theme="dark"] .activity-item .fas {
            color: #4ade80 !important;
        }

        [data-bs-theme="dark"] .activity-item .text-success {
            color: #4ade80 !important;
        }

        [data-bs-theme="dark"] .activity-item .text-info {
            color: #22d3ee !important;
        }

        [data-bs-theme="dark"] .activity-item .text-warning {
            color: #fbbf24 !important;
        }

        [data-bs-theme="dark"] .activity-item .text-purple {
            color: #a855f7 !important;
        }

        /* Section title adjustments */
        [data-bs-theme="dark"] .section-title {
            color: #f9fafb;
        }

        /* Card hover effects in dark mode */
        [data-bs-theme="dark"] .card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        [data-bs-theme="dark"] .stat-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        [data-bs-theme="dark"] .activity-item:hover {
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.3);
        }

        [data-bs-theme="dark"] .quick-action-btn:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card">
                    <div class="card-body p-4" style="position: relative; z-index: 1;">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ isset($user) && $user && $user->photo ? $user->photo : asset('storage/images/profile/default.jpg') }}" alt="Profile" class="rounded-circle border border-white border-3 pulse-animation" style="width: 90px; height: 90px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <div class="islamic-greeting">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
                                <h3 class="mb-2 fw-bold">Assalamu'alaikum, {{ isset($user) && $user ? $user->name : 'Admin' }}!</h3>
                                <p class="mb-0 opacity-90">Selamat datang di Dashboard Ma'had Aly Maal Amin. Semoga Allah SWT memberikan keberkahan dalam setiap aktivitas hari ini.</p>
                            </div>
                            <div class="flex-shrink-0 text-center">
                                <div class="badge-islamic mb-3">
                                    <i class="fas fa-calendar-islamic me-2"></i>{{ date('l') }}
                                </div>
                                <div class="text-white fw-bold">{{ date('d F Y') }}</div>
                                <div class="text-white-50 small">{{ date('H:i') }} WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="card stat-card text-white" style="background: linear-gradient(135deg, #2E8B57 0%, #228B22 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2 opacity-90">Total Mahasantri</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        $totalMahasantri = \App\Models\Mahasantri::count();
                                    } catch (\Exception $e) {
                                        $totalMahasantri = 0;
                                    }
                                @endphp
                                {{ number_format($totalMahasantri) }}
                            </h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-white bg-opacity-25">
                            <i class="fas fa-mosque me-1"></i> Santri Ma'had
                        </span>
                    </div>
                </div>
            </div>

            <div class="card stat-card text-white" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2 opacity-90">Program Studi</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        $totalProdi = \App\Models\Akademik\ProgramStudi::count();
                                    } catch (\Exception $e) {
                                        $totalProdi = 0;
                                    }
                                @endphp
                                {{ number_format($totalProdi) }}
                            </h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-white bg-opacity-25">
                            <i class="fas fa-university me-1"></i> Prodi Aktif
                        </span>
                    </div>
                </div>
            </div>

            <div class="card stat-card text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2 opacity-90">Dosen & Ustadz</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        $totalDosen = \App\Models\Dosen::count();
                                    } catch (\Exception $e) {
                                        $totalDosen = 0;
                                    }
                                @endphp
                                {{ number_format($totalDosen) }}
                            </h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-white bg-opacity-25">
                            <i class="fas fa-user-tie me-1"></i> Tenaga Pengajar
                        </span>
                    </div>
                </div>
            </div>

            <div class="card stat-card text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-2 opacity-90">Pengumuman</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        $totalPengumuman = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')->count();
                                    } catch (\Exception $e) {
                                        $totalPengumuman = 0;
                                    }
                                @endphp
                                {{ number_format($totalPengumuman) }}
                            </h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-bullhorn fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-white bg-opacity-25">
                            <i class="fas fa-calendar-alt me-1"></i> Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Activities -->
        <div class="row">
            <!-- Mahasantri Distribution Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-bar text-success me-3 fa-lg"></i>
                            <div>
                                <h5 class="mb-0 gradient-text fw-bold">Distribusi Mahasantri per Program Studi</h5>
                                <small class="text-muted">Data terkini sebaran mahasantri di Ma'had Aly</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="mahasiswaDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history text-info me-3 fa-lg"></i>
                            <div>
                                <h5 class="mb-0 gradient-text fw-bold">Aktivitas Terbaru</h5>
                                <small class="text-muted">Kegiatan terkini di Ma'had</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body recent-activity">
                        <!-- @php
                            $debugRecentActivitiesCount = ($recentActivities ?? collect())->count();
                            $debugRecentActivitiesSample = ($recentActivities ?? collect())->take(3);
                        @endphp -->
                        <!-- <div class="alert alert-warning py-2 px-3 mb-3 small">
                            <strong>DEBUG:</strong> recentActivities count = {{ $debugRecentActivitiesCount }} |
                            sample title =
                            @if($debugRecentActivitiesSample->isNotEmpty())
                                {{ $debugRecentActivitiesSample->pluck('title')->implode(', ') }}
                            @else
                                (kosong)
                            @endif
                        </div> -->
                        @forelse(($recentActivities ?? collect()) as $activity)
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold {{ $activity['colorClass'] ?? 'text-success' }}">
                                            {{ $activity['title'] ?? 'Aktivitas Baru' }}
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            {{ $activity['description'] ?? '-' }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ isset($activity['time']) && $activity['time'] ? \Carbon\Carbon::parse($activity['time'])->diffForHumans() : '-' }}
                                        </small>
                                    </div>
                                    <i class="fas {{ $activity['icon'] ?? 'fa-history' }} {{ $activity['colorClass'] ?? 'text-success' }}"></i>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-secondary">Belum ada aktivitas terbaru</h6>
                                        <p class="mb-1 text-muted small">Aktivitas realtime akan tampil di sini.</p>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Baru saja</small>
                                    </div>
                                    <i class="fas fa-history text-secondary"></i>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bolt text-warning me-3 fa-lg"></i>
                            <div>
                                <h5 class="mb-0 gradient-text fw-bold">Aksi Cepat - Ma'had Aly Maal Amin</h5>
                                <small class="text-muted">Shortcut untuk tugas-tugas penting</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route((isset($spref) ? $spref : 'master.') . 'pengguna.mahasiswa-render') }}" class="quick-action-btn btn-primary-custom">
                                    <div class="text-center">
                                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                                        <h6 class="mb-1 fw-bold">Daftar Mahasantri</h6>
                                        <small>Tambah mahasantri baru ke Ma'had</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route((isset($spref) ? $spref : 'master.') . 'akademik.prodi-render') }}" class="quick-action-btn btn-success-custom">
                                    <div class="text-center">
                                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                        <h6 class="mb-1 fw-bold">Program Studi</h6>
                                        <small>Kelola program studi Ma'had</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route((isset($spref) ? $spref : 'master.') . 'publikasi.pengumuman-render') }}" class="quick-action-btn btn-info-custom">
                                    <div class="text-center">
                                        <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                        <h6 class="mb-1 fw-bold">Buat Pengumuman</h6>
                                        <small>Publikasi pengumuman penting</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route((isset($spref) ? $spref : 'master.') . 'akademik.krs-render') }}" class="quick-action-btn btn-warning-custom">
                                    <div class="text-center">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <h6 class="mb-1 fw-bold">Kelola KRS</h6>
                                        <small>Manajemen Kartu Rencana Studi</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mahasantri Distribution Chart
            const ctx = document.getElementById('mahasiswaDistributionChart').getContext('2d');
            
            // Fetch real data from database
            fetch('{{ route((isset($spref) ? $spref : "root.") . "dashboard-chart-data") }}')
                .then(response => response.json())
                .then(data => {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels || ['Syariah', 'Dakwah', 'Tarbiyah', 'Ushuluddin', 'Adab'],
                            datasets: [{
                                label: 'Jumlah Mahasantri',
                                data: data.values || [25, 30, 20, 15, 10],
                                backgroundColor: [
                                    '#2E8B57',
                                    '#20c997',
                                    '#17a2b8',
                                    '#ffc107',
                                    '#6f42c1'
                                ],
                                borderColor: [
                                    '#2E8B57',
                                    '#20c997',
                                    '#17a2b8',
                                    '#ffc107',
                                    '#6f42c1'
                                ],
                                borderWidth: 3,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    borderColor: '#2E8B57',
                                    borderWidth: 1,
                                    cornerRadius: 10,
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed + ' mahasantri';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 2000
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    // Fallback to default data if fetch fails
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Syariah', 'Dakwah', 'Tarbiyah', 'Ushuluddin', 'Adab'],
                            datasets: [{
                                label: 'Jumlah Mahasantri',
                                data: [25, 30, 20, 15, 10],
                                backgroundColor: [
                                    '#2E8B57',
                                    '#20c997',
                                    '#17a2b8',
                                    '#ffc107',
                                    '#6f42c1'
                                ],
                                borderColor: [
                                    '#2E8B57',
                                    '#20c997',
                                    '#17a2b8',
                                    '#ffc107',
                                    '#6f42c1'
                                ],
                                borderWidth: 3,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    borderColor: '#2E8B57',
                                    borderWidth: 1,
                                    cornerRadius: 10,
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed + ' mahasantri';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 2000
                            }
                        }
                    });
                });
        });
    </script>
@endsection
