@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
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
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-submitted {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .course-table {
            background: var(--tblr-card-bg, white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .course-table th {
            background: var(--tblr-primary);
            color: white;
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        
        .course-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
            vertical-align: middle;
        }
        
        .course-table tr:last-child td {
            border-bottom: none;
        }
        
        .summary-card {
            background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-primary-dark, #0056b3) 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .summary-item {
            text-align: center;
            padding: 1rem;
        }
        
        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Dark mode specific styles */
        [data-bs-theme="dark"] .krs-card {
            background: var(--tblr-dark);
            border-color: var(--tblr-border-color-dark);
            color: var(--tblr-body-color);
        }

        [data-bs-theme="dark"] .course-table {
            background: var(--tblr-dark);
        }

        [data-bs-theme="dark"] .course-table td {
            border-color: var(--tblr-border-color-dark);
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Akademik
                    </div>
                    <h2 class="page-title">
                        Detail KRS
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @if($krs->status === 'draft')
                            <form action="{{ route('mahasiswa.krs.submit', $krs->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin submit KRS ini?')">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Submit KRS
                                </button>
                            </form>
                        @endif
                        
                        @if(in_array($krs->status, ['submitted', 'approved']))
                            <a href="{{ route('mahasiswa.krs.print', $krs->id) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-print me-2"></i>
                                Cetak KRS
                            </a>
                            <a href="{{ route('mahasiswa.krs.export-pdf', $krs->id) }}" class="btn btn-secondary ms-2" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>
                                Export PDF
                            </a>
                        @endif
                        
                        <a href="{{ route('mahasiswa.krs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KRS Summary -->
    <div class="summary-card">
        <div class="row">
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number">{{ $krs->details->count() }}</div>
                    <div class="summary-label">Mata Kuliah</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number">{{ $krs->total_sks ?? 0 }}</div>
                    <div class="summary-label">Total SKS</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number">{{ $krs->semester ?? '-' }}</div>
                    <div class="summary-label">Semester</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number">{{ $krs->tahunAkademik->name ?? '-' }}</div>
                    <div class="summary-label">Tahun Akademik</div>
                </div>
            </div>
        </div>
    </div>

    <!-- KRS Information -->
    <div class="krs-card">
        <div class="row mb-4">
            <div class="col-md-6">
                <h4 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informasi KRS
                </h4>
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Nama Mahasantri</strong></td>
                        <td>: {{ $mahasiswa->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>NIM</strong></td>
                        <td>: {{ $mahasiswa->numb_nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $mahasiswa->prodi->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Semester</strong></td>
                        <td>: {{ $krs->semester ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4 class="mb-3">
                    <i class="fas fa-calendar me-2 text-success"></i>
                    Status & Tanggal
                </h4>
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Status</strong></td>
                        <td>: 
                            <span class="status-badge status-{{ $krs->status }}">
                                {{ ucfirst($krs->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Pengisian</strong></td>
                        <td>: {{ $krs->tanggal_pengisian ? \Carbon\Carbon::parse($krs->tanggal_pengisian)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tahun Akademik</strong></td>
                        <td>: {{ $krs->tahunAkademik->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Course List -->
    <div class="krs-card">
        <h4 class="mb-3">
            <i class="fas fa-book me-2 text-warning"></i>
            Daftar Mata Kuliah
        </h4>
        
        @if($krs->details->count() > 0)
            <div class="table-responsive">
                <table class="table course-table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Dosen</th>
                            <th width="80">SKS</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($krs->details as $index => $detail)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $detail->mataKuliah->code ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $detail->mataKuliah->name ?? '-' }}</div>
                                    <div class="text-muted small">Semester {{ $detail->mataKuliah->semester ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>
                                        @if($detail->mataKuliah->dosen1)
                                            {{ $detail->mataKuliah->dosen1->title_front ? $detail->mataKuliah->dosen1->title_front . ' ' : '' }}{{ $detail->mataKuliah->dosen1->name }}{{ $detail->mataKuliah->dosen1->title_behind ? ', ' . $detail->mataKuliah->dosen1->title_behind : '' }}
                                        @else
                                            Belum ditentukan
                                        @endif
                                    </div>
                                    @if($detail->mataKuliah->dosen2)
                                        <div class="text-muted small">
                                            {{ $detail->mataKuliah->dosen2->title_front ? $detail->mataKuliah->dosen2->title_front . ' ' : '' }}{{ $detail->mataKuliah->dosen2->name }}{{ $detail->mataKuliah->dosen2->title_behind ? ', ' . $detail->mataKuliah->dosen2->title_behind : '' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $detail->sks ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ ucfirst($detail->status ?? 'aktif') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="4" class="text-end">Total SKS:</th>
                            <th class="text-center">
                                <span class="badge bg-primary fs-6">{{ $krs->total_sks ?? 0 }}</span>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-book-open fa-3x text-muted"></i>
                </div>
                <h5>Tidak Ada Mata Kuliah</h5>
                <p class="text-muted">Belum ada mata kuliah yang dipilih untuk KRS ini.</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    @if($krs->status === 'draft')
        <div class="krs-card">
            <div class="alert alert-info">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">KRS Masih Draft</h4>
                        <div class="text-muted">
                            KRS Anda masih dalam status draft. Silakan submit KRS untuk melanjutkan proses persetujuan.
                            Setelah disubmit, Anda tidak dapat mengubah mata kuliah yang dipilih.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($krs->status === 'submitted')
        <div class="krs-card">
            <div class="alert alert-warning">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-clock me-2"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Menunggu Persetujuan</h4>
                        <div class="text-muted">
                            KRS Anda telah disubmit dan sedang menunggu persetujuan dari dosen pembimbing akademik.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($krs->status === 'approved')
        <div class="krs-card">
            <div class="alert alert-success">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-check-circle me-2"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">KRS Disetujui</h4>
                        <div class="text-muted">
                            KRS Anda telah disetujui. Anda dapat mencetak KRS sebagai bukti registrasi mata kuliah.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('custom-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any custom JavaScript here if needed
            console.log('KRS Detail page loaded');
        });
    </script>
@endsection
