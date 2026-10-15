@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: var(--tblr-card-bg, white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: var(--tblr-body-color);
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--tblr-body-color);
        }
        
        .stats-label {
            color: var(--tblr-muted, #718096);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .info-card {
            background: var(--tblr-card-bg, white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            margin-bottom: 1.5rem;
            color: var(--tblr-body-color);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.25rem;
        }
        
        .badge-alumni {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .quick-action {
            background: var(--tblr-card-bg, white);
            border: 2px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 12px;
            padding: 1rem;
            text-decoration: none;
            color: var(--tblr-body-color, #2d3748);
            transition: all 0.3s ease;
            display: block;
            text-align: center;
        }
        
        .quick-action:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .quick-action-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .quick-action-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .graduation-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Mobile responsive styles */
        @media (max-width: 767.98px) {
            .welcome-card {
                padding: 1.5rem;
                text-align: center;
            }
            
            .welcome-card h1 {
                font-size: 1.5rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
            
            .stats-label {
                font-size: 0.8rem;
            }
            
            .quick-action {
                padding: 0.8rem;
            }
            
            .quick-action-icon {
                font-size: 1.5rem;
                margin-bottom: 0.3rem;
            }
            
            .quick-action-text {
                font-size: 0.8rem;
            }
            
            .info-card {
                padding: 1rem;
            }
            
            .profile-avatar {
                width: 60px;
                height: 60px;
            }

            .badge-status {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
                display: block;
                text-align: center;
                margin-bottom: 0.5rem;
                max-width: 100%;
            }
        }

        /* Dark mode specific styles */
        [data-bs-theme="dark"] .stats-card {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .info-card {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .quick-action {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .stats-number {
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .stats-label {
            color: var(--tblr-muted);
        }

        [data-bs-theme="dark"] .list-group-item {
            background: transparent;
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .table td {
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .text-muted {
            color: var(--tblr-muted) !important;
        }
    </style>
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">🎓 Selamat Datang, {{ $mahasiswa->name }}!</h1>
                <p class="mb-0 opacity-75">
                    Selamat! Anda telah berhasil menyelesaikan studi dan menjadi bagian dari keluarga besar alumni. 
                    Tetap terhubung dengan almamater dan terus berkarya untuk kemajuan bangsa.
                </p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ $mahasiswa->photo }}" alt="Profile" class="profile-avatar">
            </div>
        </div>
    </div>

    <!-- Graduation Info Card -->
    <div class="graduation-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-2">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Informasi Kelulusan
                </h3>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Tanggal Lulus:</strong> {{ $graduation_info['graduated_at'] }}</p>
                        <p class="mb-1"><strong>Semester Terakhir:</strong> {{ $graduation_info['final_semester'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Program Studi:</strong> {{ $stats['program_studi'] }}</p>
                        <p class="mb-0"><strong>Kelas Terakhir:</strong> {{ $stats['kelas_terakhir'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-medal fa-4x opacity-75"></i>
            </div>
        </div>
        @if($graduation_info['graduation_note'])
            <div class="mt-3 pt-3 border-top border-light">
                <p class="mb-0"><strong>Catatan:</strong> {{ $graduation_info['graduation_note'] }}</p>
            </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stats-number text-primary">{{ $stats['total_semester'] }}</div>
                <div class="stats-label">Total Semester</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stats-number text-success" style="font-size: 1.2rem;">{{ Str::limit($stats['program_studi'], 15) }}</div>
                <div class="stats-label">Program Studi</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stats-number text-warning">{{ $stats['total_tagihan'] }}</div>
                <div class="stats-label">Total Tagihan</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number text-success">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
                <div class="stats-label">Total Terbayar</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Mobile First -->
    <div class="row mb-4 d-md-none">
        <div class="col-12">
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Aksi Cepat
                </h3>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('alumni.profile-render') }}" class="quick-action">
                            <i class="fas fa-user-edit quick-action-icon"></i>
                            <div class="quick-action-text">Edit Profile</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.transkrip.index') }}" class="quick-action">
                            <i class="fas fa-file-alt quick-action-icon"></i>
                            <div class="quick-action-text">Transkrip</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.khs.index') }}" class="quick-action">
                            <i class="fas fa-chart-line quick-action-icon"></i>
                            <div class="quick-action-text">KHS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.kelulusan.index') }}" class="quick-action">
                            <i class="fas fa-graduation-cap quick-action-icon"></i>
                            <div class="quick-action-text">Kelulusan</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.payment.history') }}" class="quick-action">
                            <i class="fas fa-history quick-action-icon"></i>
                            <div class="quick-action-text">Riwayat Bayar</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action">
                            <i class="fas fa-certificate quick-action-icon"></i>
                            <div class="quick-action-text">Sertifikat</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                    Informasi Alumni
                </h3>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td><strong>{{ $mahasiswa->numb_nim ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Program Studi</td>
                                <td><strong>{{ $mahasiswa->prodi->name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kelas Terakhir</td>
                                <td><strong>{{ $mahasiswa->kelas->name ?? '-' }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Tahun Akademik</td>
                                <td><strong>{{ $mahasiswa->kelas->taka->name ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Semester Terakhir</td>
                                <td><strong>{{ $mahasiswa->semester ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td class="status-container">
                                    <span class="badge-status badge-alumni">
                                        <i class="fas fa-graduation-cap me-1"></i>
                                        Alumni
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Alumni Achievement -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-trophy me-2 text-warning"></i>
                    Pencapaian Alumni
                </h3>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-medal me-2"></i>
                    <div>
                        <strong>Selamat!</strong> Anda telah berhasil menyelesaikan pendidikan di {{ $mahasiswa->prodi->name ?? 'Program Studi' }} 
                        dengan {{ $stats['total_semester'] }} semester. Terus berkarya dan berkontribusi untuk kemajuan bangsa!
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="avatar avatar-sm bg-success text-white">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-medium">Lulus Tepat Waktu</div>
                                <div class="text-muted small">Menyelesaikan studi sesuai jadwal</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="avatar avatar-sm bg-primary text-white">
                                    <i class="fas fa-book"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-medium">Akademik Selesai</div>
                                <div class="text-muted small">Semua mata kuliah telah diselesaikan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-clock me-2 text-info"></i>
                    Aktivitas Terbaru
                </h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-sm bg-success text-white">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-medium">Status diubah menjadi Alumni</div>
                            <div class="text-muted small">{{ $graduation_info['graduated_at'] }}</div>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-sm bg-primary text-white">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-medium">Login ke sistem alumni</div>
                            <div class="text-muted small">Hari ini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions - Desktop -->
        <div class="col-md-4">
            <div class="info-card d-none d-md-block">
                <h3 class="card-title mb-3">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Aksi Cepat
                </h3>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('alumni.profile-render') }}" class="quick-action">
                            <i class="fas fa-user-edit quick-action-icon"></i>
                            <div class="quick-action-text">Edit Profile</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.transkrip.index') }}" class="quick-action">
                            <i class="fas fa-file-alt quick-action-icon"></i>
                            <div class="quick-action-text">Transkrip</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.khs.index') }}" class="quick-action">
                            <i class="fas fa-chart-line quick-action-icon"></i>
                            <div class="quick-action-text">KHS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.kelulusan.index') }}" class="quick-action">
                            <i class="fas fa-graduation-cap quick-action-icon"></i>
                            <div class="quick-action-text">Kelulusan</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.payment.history') }}" class="quick-action">
                            <i class="fas fa-history quick-action-icon"></i>
                            <div class="quick-action-text">Riwayat Bayar</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action">
                            <i class="fas fa-certificate quick-action-icon"></i>
                            <div class="quick-action-text">Sertifikat</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alumni Network -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-users me-2 text-info"></i>
                    Jaringan Alumni
                </h3>
                <div class="text-center py-3">
                    <i class="fas fa-network-wired fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Bergabung dengan Alumni</h5>
                    <p class="text-muted mb-3">
                        Terhubung dengan sesama alumni untuk berbagi pengalaman dan peluang karir.
                    </p>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-users me-1"></i>Gabung Komunitas
                    </a>
                </div>
            </div>

            <!-- Announcements -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-bullhorn me-2 text-danger"></i>
                    Pengumuman
                </h3>
                <div class="list-group list-group-flush">
                    @if(isset($announcements) && count($announcements) > 0)
                        @foreach($announcements as $announcement)
                            <div class="list-group-item">
                                <div class="font-weight-medium">{{ $announcement->name }}</div>
                                <div class="text-muted small">{{ Str::limit(strip_tags($announcement->content), 100) }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                                    @if($announcement->kategori)
                                        <span class="badge bg-primary ms-2">{{ $announcement->kategori->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="list-group-item">
                            <div class="text-muted text-center">
                                <i class="fas fa-info-circle me-1"></i>
                                Belum ada pengumuman terbaru
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add some interactive effects
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Show welcome message for alumni
            console.log('Welcome to Alumni Dashboard!');
        });
    </script>
@endsection
