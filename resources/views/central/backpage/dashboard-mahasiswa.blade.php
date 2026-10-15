 @extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <style>
        .welcome-card {
            background: #667eea;
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
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-terminal {
            background: #fff3cd;
            color: #856404;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 500;
            display: inline-block;
            margin-left: 0.5rem;
        }

        /* Mobile responsive badge styles */
        @media (max-width: 767.98px) {
            .badge-status {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
                display: block;
                text-align: center;
                margin-bottom: 0.5rem;
                max-width: 100%;
            }
            
            .badge-terminal {
                display: block;
                margin-left: 0;
                margin-top: 0.25rem;
                text-align: center;
                font-size: 0.65rem;
                padding: 0.2rem 0.6rem;
            }
            
            .status-container {
                text-align: center;
            }
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
                <h1 class="mb-2">Selamat Datang, {{ $mahasiswa->name }}!</h1>
                <p class="mb-0 opacity-75">
                    Semangat belajar hari ini! Jangan lupa untuk selalu mengecek jadwal kuliah dan tugas-tugas yang harus dikerjakan.
                </p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ $mahasiswa->photo }}" alt="Profile" class="profile-avatar">
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stats-number text-primary">{{ $mahasiswa->semester ?? 0 }}</div>
                <div class="stats-label">Semester Aktif</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stats-number text-success">{{ $stats['mata_kuliah'] ?? 0 }}</div>
                <div class="stats-label">Mata Kuliah</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stats-number text-warning">{{ $stats['tugas_pending'] ?? 0 }}</div>
                <div class="stats-label">Tugas Pending</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-number text-danger">{{ $stats['kehadiran'] ?? 0 }}%</div>
                <div class="stats-label">Kehadiran</div>
            </div>
        </div>
    </div>

    <!-- Payment Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 col-12 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(168, 85, 247, 0.1); color: #a855f7;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stats-number text-purple">{{ $stats['total_tagihan'] ?? 0 }}</div>
                <div class="stats-label">Total Tagihan</div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number text-danger">Rp {{ number_format($stats['tagihan_pending'] ?? 0, 0, ',', '.') }}</div>
                <div class="stats-label">Tagihan Pending</div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number text-success">Rp {{ number_format($stats['total_paid'] ?? 0, 0, ',', '.') }}</div>
                <div class="stats-label">Total Terbayar</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Mobile First (akan muncul di atas pada mobile) -->
    <div class="row mb-4 d-md-none">
        <div class="col-12">
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Aksi Cepat
                </h3>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.profile-render') }}" class="quick-action">
                            <i class="fas fa-user-edit quick-action-icon"></i>
                            <div class="quick-action-text">Edit Profile</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.krs.index') }}" class="quick-action">
                            <i class="fas fa-book-open quick-action-icon"></i>
                            <div class="quick-action-text">KRS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.khs.index') }}" class="quick-action">
                            <i class="fas fa-chart-line quick-action-icon"></i>
                            <div class="quick-action-text">KHS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.transkrip.index') }}" class="quick-action">
                            <i class="fas fa-file-alt quick-action-icon"></i>
                            <div class="quick-action-text">Transkrip</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action">
                            <i class="fas fa-calendar quick-action-icon"></i>
                            <div class="quick-action-text">Jadwal</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.kelulusan.index') }}" class="quick-action">
                            <i class="fas fa-graduation-cap quick-action-icon"></i>
                            <div class="quick-action-text">Kelulusan</div>
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
                    Informasi Akademik
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
                                <td class="text-muted">Kelas</td>
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
                                <td class="text-muted">Semester</td>
                                <td><strong>{{ $mahasiswa->semester ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td class="status-container">
                                    <span class="badge-status badge-active">
                                        {{ $mahasiswa->type }}
                                    </span>
                                    @if($mahasiswa->isTerminal())
                                        <span class="badge-terminal">
                                            Terminal/Dibekukan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dosen Penasehat Information -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-chalkboard-teacher me-2 text-success"></i>
                    Dosen Penasehat
                </h3>
                @if($mahasiswa->dosenPenasehat)
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="{{ $mahasiswa->dosenPenasehat->photo }}" alt="Dosen Penasehat" 
                                 class="rounded-circle" width="80" height="80" style="object-fit: cover; border: 3px solid #22c55e;">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" width="30%">Nama</td>
                                    <td><strong>{{ $mahasiswa->dosenPenasehat->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIDN/NIP</td>
                                    <td><strong>{{ $mahasiswa->dosenPenasehat->nidn ?? $mahasiswa->dosenPenasehat->nip ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td><strong>{{ $mahasiswa->dosenPenasehat->email ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Telepon</td>
                                    <td><strong>{{ $mahasiswa->dosenPenasehat->phone ?? '-' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Informasi:</strong> Anda dapat menghubungi dosen penasehat untuk konsultasi akademik, 
                                bimbingan studi, atau diskusi terkait perkuliahan dan pengembangan karir.
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Dosen Penasehat</h5>
                        <p class="text-muted mb-0">
                            Anda belum memiliki dosen penasehat yang ditugaskan. 
                            Silakan hubungi bagian akademik untuk informasi lebih lanjut.
                        </p>
                    </div>
                @endif
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
                            <span class="avatar avatar-sm bg-primary text-white">
                                <i class="fas fa-user-edit"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-medium">Profile diperbarui</div>
                            <div class="text-muted small">Hari ini</div>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-sm bg-success text-white">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-medium">Login ke sistem</div>
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
                        <a href="{{ route('mahasiswa.profile-render') }}" class="quick-action">
                            <i class="fas fa-user-edit quick-action-icon"></i>
                            <div class="quick-action-text">Edit Profile</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.krs.index') }}" class="quick-action">
                            <i class="fas fa-book-open quick-action-icon"></i>
                            <div class="quick-action-text">KRS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.khs.index') }}" class="quick-action">
                            <i class="fas fa-chart-line quick-action-icon"></i>
                            <div class="quick-action-text">KHS</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.transkrip.index') }}" class="quick-action">
                            <i class="fas fa-file-alt quick-action-icon"></i>
                            <div class="quick-action-text">Transkrip</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-action">
                            <i class="fas fa-calendar quick-action-icon"></i>
                            <div class="quick-action-text">Jadwal</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.kelulusan.index') }}" class="quick-action">
                            <i class="fas fa-graduation-cap quick-action-icon"></i>
                            <div class="quick-action-text">Kelulusan</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Tagihan -->
            <div class="info-card">
                <h3 class="card-title mb-3">
                    <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>
                    Tagihan Terbaru
                    <a href="{{ route('mahasiswa.payment.index') }}" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </h3>
                <div class="list-group list-group-flush">
                    @if(isset($recentTagihan) && count($recentTagihan) > 0)
                        @foreach($recentTagihan as $tagihan)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-medium">{{ $tagihan->desc ?? 'Tagihan Kuliah' }}</div>
                                    <div class="text-muted small">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        Rp {{ number_format($tagihan->amount ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-hashtag me-1"></i>
                                        {{ $tagihan->code }}
                                    </div>
                                    @if($tagihan->due_date)
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            Jatuh tempo: {{ \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('mahasiswa.payment.create', $tagihan->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-credit-card me-1"></i>Bayar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="list-group-item">
                            <div class="text-muted text-center">
                                <i class="fas fa-check-circle me-1"></i>
                                Tidak ada tagihan pending
                            </div>
                        </div>
                    @endif
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

            // Auto refresh every 5 minutes
            setTimeout(() => {
                location.reload();
            }, 300000);
        });
    </script>
@endsection
