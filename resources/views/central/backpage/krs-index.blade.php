@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .krs-card {
            background: var(--tblr-card-bg, white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            margin-bottom: 1.5rem;
            color: var(--tblr-body-color);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-submitted {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .course-item {
            background: var(--tblr-card-bg, white);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .course-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .course-code {
            font-weight: 700;
            color: var(--tblr-primary);
            font-size: 0.9rem;
        }
        
        .course-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--tblr-body-color);
        }
        
        .course-details {
            font-size: 0.85rem;
            color: var(--tblr-muted);
        }
        
        .sks-badge {
            background: var(--tblr-primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--tblr-muted);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Dark mode specific styles */
        [data-bs-theme="dark"] .krs-card {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .course-item {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
        }

        [data-bs-theme="dark"] .course-name {
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .course-details {
            color: var(--tblr-muted);
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Akademik
                    </div>
                    <h2 class="page-title">
                        Kartu Rencana Studi (KRS)
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    @if(!$currentKrs)
                        <a href="{{ route('mahasiswa.krs.create', ['semester' => $selectedSemester]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Buat KRS Semester {{ $selectedSemester }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($semestersWithStatus->count() > 0)
    <div class="container-xl mb-3">
        <div class="card">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Pilih Semester
                        </h4>
                        <p class="text-muted mb-0 small">Pilih semester untuk melihat KRS yang berbeda</p>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('mahasiswa.krs.index') }}" class="d-flex align-items-center justify-content-end">
                            <label for="semester" class="form-label me-3 mb-0 fw-medium">Semester:</label>
                            <select name="semester" id="semester" class="form-select" style="width: auto; min-width: 250px;" onchange="this.form.submit()">
                                <option value="{{ $mahasiswa->semester }}" {{ $selectedSemester == $mahasiswa->semester ? 'selected' : '' }}>
                                    Semester {{ $mahasiswa->semester }} (Aktif)
                                </option>
                                @foreach($semestersWithStatus as $sem => $krsData)
                                    @if($sem != $mahasiswa->semester)
                                        @php
                                            $statusText = '';
                                            switch($krsData->status) {
                                                case 'draft':
                                                    $statusText = 'KRS Draft';
                                                    break;
                                                case 'submitted':
                                                    $statusText = 'KRS Disubmit';
                                                    break;
                                                case 'approved':
                                                    $statusText = 'KRS Disetujui';
                                                    break;
                                                case 'rejected':
                                                    $statusText = 'KRS Ditolak';
                                                    break;
                                                default:
                                                    $statusText = 'KRS Belum Diajukan';
                                            }
                                        @endphp
                                        <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>
                                            Semester {{ $sem }} ({{ $statusText }})
                                        </option>
                                    @endif
                                @endforeach
                                @for($i = 1; $i <= $mahasiswa->semester; $i++)
                                    @if(!$semestersWithStatus->has($i) && $i != $mahasiswa->semester)
                                        <option value="{{ $i }}" {{ $selectedSemester == $i ? 'selected' : '' }}>
                                            Semester {{ $i }} (KRS Belum Diajukan)
                                        </option>
                                    @endif
                                @endfor
                            </select>
                            <noscript>
                                <button type="submit" class="btn btn-primary ms-2">Pilih</button>
                            </noscript>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($currentKrs)
        <div class="krs-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        KRS Semester {{ $currentKrs->semester }}
                    </h3>
                    <p class="text-muted mb-2">
                        Tahun Akademik: {{ $currentKrs->tahunAkademik->name ?? '-' }}
                    </p>
                    <p class="text-muted mb-0">
                        Tanggal Pengisian: {{ $currentKrs->tanggal_pengisian->format('d F Y') }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        @if($currentKrs->status == 'draft')
                            <span class="status-badge status-draft">Draft</span>
                        @elseif($currentKrs->status == 'submitted')
                            <span class="status-badge status-submitted">Disubmit</span>
                        @elseif($currentKrs->status == 'disetujui')
                            <span class="status-badge status-approved">Disetujui</span>
                        @elseif($currentKrs->status == 'rejected')
                            <span class="status-badge bg-danger text-white border-0 shadow-sm">Ditolak</span>
                        @endif
                    </div>
                    <div class="text-muted">
                        Total SKS: <strong>{{ $currentKrs->total_sks ?? 0 }}</strong>
                    </div>
                </div>
            </div> @if($currentKrs->status == 'rejected' && $currentKrs->rejection_reason)
                <div class="alert mt-4 mb-2 border-0 shadow-sm d-flex align-items-start" 
                     style="background-color: rgba(220, 53, 69, 0.1); border-radius: 12px; border-left: 4px solid #dc3545 !important;" 
                     role="alert">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1 text-danger"></i>
                    <div class="text-start">
                        <h4 class="fw-bold mb-1 text-danger">KRS Anda Ditolak</h4>
                        <p class="mb-1" style="color: var(--tblr-body-color);">
                            <strong>Alasan Penolakan:</strong> {{ $currentKrs->rejection_reason }}
                        </p>
                        <hr class="my-2" style="border-color: rgba(220, 53, 69, 0.2);">
                        <p class="mb-0 small" style="color: var(--tblr-body-color); opacity: 0.8;">
                            <i class="fas fa-info-circle me-1"></i> Hubungi admin terkait masalah penolakan ini agar Anda dapat mengajukan ulang KRS.
                        </p>
                    </div>
                </div>
            @endif
            
            @if($currentKrs->status == 'draft')
                <div class="mt-4 pt-3 border-top">
                    <div class="btn-group">
                        <a href="{{ route('mahasiswa.krs.edit', $currentKrs->id) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-1"></i> Edit KRS
                        </a>
                        <a href="{{ route('mahasiswa.krs.show', $currentKrs->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </a>
                        <button type="button" class="btn btn-success" onclick="submitKrs({{ $currentKrs->id }})">
                            <i class="fas fa-paper-plane me-1"></i> Submit KRS
                        </button>
                    </div>
                </div>
            @else
                <div class="mt-4 pt-3 border-top">
                    <div class="btn-group">
                        <a href="{{ route('mahasiswa.krs.show', $currentKrs->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </a>
                        @if($currentKrs->canBePrinted())
                            <a href="{{ route('mahasiswa.krs.print', $currentKrs->id) }}" class="btn btn-outline-success" target="_blank">
                                <i class="fas fa-print me-1"></i> Cetak KRS
                            </a>
                            <a href="{{ route('mahasiswa.krs.export-pdf', $currentKrs->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-secondary" disabled onclick="showNotApprovedAlert()">
                                <i class="fas fa-print me-1"></i> Cetak KRS
                            </button>
                            <button type="button" class="btn btn-outline-secondary" disabled onclick="showNotApprovedAlert()">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="krs-card">
        <h3 class="mb-3">
            <i class="fas fa-book me-2 text-success"></i>
            Daftar Mata Kuliah
        </h3>
        
        @if($krsDetails->count() > 0)
            <div class="row">
                @foreach($krsDetails as $detail)
                    <div class="col-md-6 col-lg-4">
                        <div class="course-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="course-code">{{ $detail->mataKuliah->code ?? '-' }}</div>
                                <span class="sks-badge">{{ $detail->sks }} SKS</span>
                            </div>
                            <div class="course-name">{{ $detail->mataKuliah->name ?? '-' }}</div>
                            <div class="course-details">
                                <div><i class="fas fa-user me-1"></i> 
                                    @if($detail->mataKuliah->dosen1)
                                        {{ $detail->mataKuliah->dosen1->title_front ? $detail->mataKuliah->dosen1->title_front . ' ' : '' }}{{ $detail->mataKuliah->dosen1->name }}{{ $detail->mataKuliah->dosen1->title_behind ? ', ' . $detail->mataKuliah->dosen1->title_behind : '' }}
                                    @else
                                        Belum ditentukan
                                    @endif
                                </div>
                                <div><i class="fas fa-calendar me-1"></i> Semester {{ $detail->mataKuliah->semester ?? '-' }}</div>
                                @if($detail->nilai_huruf)
                                    <div class="mt-2">
                                        <span class="badge bg-success">Nilai: {{ $detail->nilai_huruf }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 pt-3 border-top">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="text-muted">Total Mata Kuliah</div>
                        <div class="h4 mb-0">{{ $krsDetails->count() }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Total SKS</div>
                        <div class="h4 mb-0">{{ $krsDetails->sum('sks') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Selesai</div>
                        <div class="h4 mb-0">{{ $krsDetails->where('nilai_huruf', '!=', null)->count() }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Berlangsung</div>
                        <div class="h4 mb-0">{{ $krsDetails->where('nilai_huruf', null)->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h4>Belum Ada Mata Kuliah</h4>
                <p>Anda belum memiliki KRS untuk semester ini. Silakan buat KRS baru untuk memilih mata kuliah.</p>
                @if(!$currentKrs)
                    <a href="{{ route('mahasiswa.krs.create', ['semester' => $selectedSemester]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Buat KRS Semester {{ $selectedSemester }}
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        function submitKrs(krsId) {
            Swal.fire({
                title: 'Submit KRS?',
                text: 'Apakah Anda yakin ingin submit KRS ini? Setelah disubmit, KRS tidak dapat diubah lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Mengirim KRS...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/mahasiswa/krs/${krsId}/submit`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function showNotApprovedAlert() {
            Swal.fire({
                icon: 'info',
                title: 'KRS Belum Disetujui',
                text: 'KRS harus disetujui terlebih dahulu sebelum dapat dicetak atau diexport.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Show flash messages as SweetAlert
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: {!! json_encode(session('warning')) !!},
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: {!! json_encode(session('info')) !!},
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#17a2b8'
                });
            @endif

            // Add some interactive effects
            const courseItems = document.querySelectorAll('.course-item');
            courseItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection