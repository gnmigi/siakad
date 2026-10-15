@extends('core-themes.core-backpage')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        PMB Management
                    </div>
                    <h2 class="page-title">
                        Detail Calon Mahasiswa
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route((isset($spref) ? $spref : '') . 'pmb.management-index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="row row-deck row-cards">
            <!-- Status Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Status Pendaftaran</h3>
                        <div class="card-actions">
                            @if($pendaftar->status == 'pending')
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                    Setujui
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M18 6l-12 12"></path>
                                        <path d="M6 6l12 12"></path>
                                    </svg>
                                    Tolak
                                </button>
                            @endif
                            
                            @if($pendaftar->status == 'verified' && (!$pendaftar->mahasiswa_id || $pendaftar->mahasiswa_id == 0))
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#convertModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    Convert ke Mahasiswa
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Pendaftaran</label>
                                    <div class="form-control-plaintext"><strong>{{ $pendaftar->code }}</strong></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        @if($pendaftar->status == 'pending')
                                            <span class="badge bg-yellow">Menunggu Verifikasi</span>
                                        @elseif($pendaftar->status == 'verified')
                                            <span class="badge bg-green">Terverifikasi</span>
                                        @elseif($pendaftar->status == 'rejected')
                                            <span class="badge bg-red">Ditolak</span>
                                        @elseif($pendaftar->status == 'diterima')
                                            <span class="badge bg-blue">Diterima</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($pendaftar->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->created_at->format('d F Y H:i') }}</div>
                                </div>
                                @if($pendaftar->mahasiswa_id && $pendaftar->mahasiswa_id > 0)
                                <div class="mb-3">
                                    <label class="form-label">Status Konversi</label>
                                    <div>
                                        <span class="badge bg-blue">Sudah dikonversi ke Mahasiswa</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pribadi</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="form-control-plaintext">{{ $pendaftar->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="form-control-plaintext">{{ $pendaftar->email }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <div class="form-control-plaintext">{{ $pendaftar->phone }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <div class="form-control-plaintext">{{ $pendaftar->nik ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->tempat_lahir ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <div class="form-control-plaintext">
                                        {{ $pendaftar->tanggal_lahir ? \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d F Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div class="form-control-plaintext">{{ $pendaftar->jenis_kelamin ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <div class="form-control-plaintext">{{ $pendaftar->alamat ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pendidikan & Orang Tua -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pendidikan & Keluarga</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">Pendidikan</h4>
                        <div class="mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <div class="form-control-plaintext">{{ $pendaftar->asal_sekolah ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Lulus</label>
                            <div class="form-control-plaintext">{{ $pendaftar->tahun_lulus ?? '-' }}</div>
                        </div>
                        
                        <hr>
                        <h4 class="mb-3">Data Orang Tua</h4>
                        <div class="mb-3">
                            <label class="form-label">Nama Ayah</label>
                            <div class="form-control-plaintext">{{ $pendaftar->nama_ayah ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Ibu</label>
                            <div class="form-control-plaintext">{{ $pendaftar->nama_ibu ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data PMB -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data PMB</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Periode</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->periode->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Jalur</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->jalur->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Daftar</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->register_date ? \Carbon\Carbon::parse($pendaftar->register_date)->format('d F Y') : '-' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan Prodi 1</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->prodi1->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan Prodi 2</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->prodi2->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan Prodi 3</label>
                                    <div class="form-control-plaintext">{{ $pendaftar->prodi3->name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Dokumen Syarat Pendaftaran -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload Dokumen Syarat Pendaftaran</h3>
                        <div class="card-actions">
                            <span class="badge bg-blue">{{ $uploads->count() }} dari {{ $requirements->count() }} dokumen</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($requirements->count() > 0)
                            <!-- Batch Actions untuk Dokumen -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-bolt me-2"></i>Batch Actions untuk Dokumen Terpilih
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-success w-100" onclick="batchApproveDocuments()">
                                                <i class="fas fa-check me-2"></i>Setujui Terpilih
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-danger w-100" onclick="batchRejectDocuments()">
                                                <i class="fas fa-times me-2"></i>Tolak Terpilih
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info w-100" onclick="resetDocumentSelection()">
                                                <i class="fas fa-undo me-2"></i>Reset Pilihan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAllDocuments" class="form-check-input">
                                            </th>
                                            <th>Syarat</th>
                                            <th>Status Upload</th>
                                            <th>File</th>
                                            <th>Status Validasi</th>
                                            <th>Catatan Admin</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requirements as $requirement)
                                            @php
                                                $upload = $uploads->get($requirement->id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if($upload && $upload->status == 'pending')
                                                        <input type="checkbox" name="document_ids[]" value="{{ $upload->id }}" class="form-check-input document-checkbox">
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $requirement->name }}</strong>
                                                        @if($requirement->is_required)
                                                            <span class="badge bg-red ms-1">Wajib</span>
                                                        @else
                                                            <span class="badge bg-secondary ms-1">Opsional</span>
                                                        @endif
                                                    </div>
                                                    @if($requirement->description)
                                                        <small class="text-muted">{{ $requirement->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($upload)
                                                        <span class="badge bg-green">Sudah Upload</span>
                                                    @else
                                                        <span class="badge bg-red">Belum Upload</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($upload)
                                                        <div>
                                                            <strong>{{ $upload->original_name }}</strong><br>
                                                            <small class="text-muted">
                                                                {{ number_format($upload->file_size / 1024, 2) }} KB
                                                                • {{ $upload->uploaded_at->format('d M Y H:i') }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($upload)
                                                        @if($upload->status == 'pending')
                                                            <span class="badge bg-yellow">Menunggu Validasi</span>
                                                        @elseif($upload->status == 'approved')
                                                            <span class="badge bg-green">Disetujui</span>
                                                        @elseif($upload->status == 'rejected')
                                                            <span class="badge bg-red">Ditolak</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($upload && $upload->admin_notes)
                                                        <small>{{ $upload->admin_notes }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($upload)
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat File">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                                </svg>
                                                            </a>
                                                            @if($upload->status == 'pending')
                                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveDocModal{{ $upload->id }}" title="Setujui">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M5 12l5 5l10 -10"></path>
                                                                    </svg>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectDocModal{{ $upload->id }}" title="Tolak">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M18 6l-12 12"></path>
                                                                        <path d="M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                    </svg>
                                </div>
                                <p class="empty-title">Belum ada syarat pendaftaran</p>
                                <p class="empty-subtitle text-muted">Admin belum menambahkan syarat pendaftaran untuk periode ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal modal-blur fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Setujui Calon Mahasiswa?</div>
                <div>Apakah Anda yakin ingin menyetujui calon mahasiswa ini?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.management.approve-pendaftar', $pendaftar->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Ya, Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal modal-blur fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.management.reject-pendaftar', $pendaftar->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Calon Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Convert Modal -->
<div class="modal modal-blur fade" id="convertModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.management.convert-to-mahasiswa') }}" method="POST">
                @csrf
                <input type="hidden" name="pendaftar_id" value="{{ $pendaftar->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Convert ke Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select" name="prodi_id" required>
                            <option value="">Pilih Program Studi</option>
                            @if($pendaftar->prodi1)
                                <option value="{{ $pendaftar->prodi1->id }}">{{ $pendaftar->prodi1->name }} (Pilihan 1)</option>
                            @endif
                            @if($pendaftar->prodi2)
                                <option value="{{ $pendaftar->prodi2->id }}">{{ $pendaftar->prodi2->name }} (Pilihan 2)</option>
                            @endif
                            @if($pendaftar->prodi3)
                                <option value="{{ $pendaftar->prodi3->id }}">{{ $pendaftar->prodi3->name }} (Pilihan 3)</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas (Opsional)</label>
                        <select class="form-select" name="kelas_id">
                            <option value="">Pilih Kelas (Opsional)</option>
                            @foreach(\App\Models\Akademik\Kelas::whereNull('deleted_at')->get() as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <strong>Info:</strong> Sistem akan otomatis generate NIM untuk mahasiswa baru berdasarkan tahun dan program studi yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Convert ke Mahasiswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Validation Modals -->
@foreach($uploads as $upload)
    <!-- Approve Document Modal -->
    <div class="modal modal-blur fade" id="approveDocModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.pendaftar-validasi', $pendaftar->code) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="upload_id" value="{{ $upload->id }}">
                    <input type="hidden" name="status" value="approved">
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Dokumen</label>
                            <div class="form-control-plaintext"><strong>{{ $upload->requirement->name }}</strong></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <div class="form-control-plaintext">{{ $upload->original_name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Admin (Opsional)</label>
                            <textarea class="form-control" name="admin_notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                        <div class="alert alert-success">
                            <strong>Konfirmasi:</strong> Dokumen ini akan disetujui dan status akan berubah menjadi "Disetujui".
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Document Modal -->
    <div class="modal modal-blur fade" id="rejectDocModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route((isset($spref) ? $spref : '') . 'pmb.pendaftar-validasi', $pendaftar->code) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="upload_id" value="{{ $upload->id }}">
                    <input type="hidden" name="status" value="rejected">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Dokumen</label>
                            <div class="form-control-plaintext"><strong>{{ $upload->requirement->name }}</strong></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <div class="form-control-plaintext">{{ $upload->original_name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="admin_notes" rows="3" required placeholder="Masukkan alasan penolakan dokumen..."></textarea>
                        </div>
                        <div class="alert alert-danger">
                            <strong>Peringatan:</strong> Dokumen ini akan ditolak dan calon mahasiswa perlu mengupload ulang dokumen yang sesuai.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Batch Approve Documents Modal -->
<div class="modal modal-blur fade" id="batchApproveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="batchApproveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Dokumen Terpilih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Dokumen Terpilih</label>
                        <div class="form-control-plaintext"><strong id="selectedDocsCount">0</strong> dokumen</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin (Opsional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="Tambahkan catatan untuk semua dokumen yang disetujui..."></textarea>
                    </div>
                    <div class="alert alert-success">
                        <strong>Konfirmasi:</strong> Semua dokumen terpilih akan disetujui dan status akan berubah menjadi "Disetujui".
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui Semua Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch Reject Documents Modal -->
<div class="modal modal-blur fade" id="batchRejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="batchRejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Dokumen Terpilih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Dokumen Terpilih</label>
                        <div class="form-control-plaintext"><strong id="selectedDocsCountReject">0</strong> dokumen</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" required placeholder="Masukkan alasan penolakan untuk semua dokumen..."></textarea>
                    </div>
                    <div class="alert alert-danger">
                        <strong>Peringatan:</strong> Semua dokumen terpilih akan ditolak dan calon mahasiswa perlu mengupload ulang dokumen yang sesuai.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Semua Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Select All Documents Checkbox
    $('#selectAllDocuments').change(function() {
        $('.document-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Update select all checkbox when individual checkboxes change
    $('.document-checkbox').change(function() {
        const totalCheckboxes = $('.document-checkbox').length;
        const checkedCheckboxes = $('.document-checkbox:checked').length;
        $('#selectAllDocuments').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
});

function batchApproveDocuments() {
    const selectedIds = getSelectedDocumentIds();
    if (selectedIds.length === 0) {
        Swal.fire('Peringatan', 'Pilih minimal satu dokumen untuk disetujui', 'warning');
        return;
    }

    $('#selectedDocsCount').text(selectedIds.length);
    $('#batchApproveModal').modal('show');
}

function batchRejectDocuments() {
    const selectedIds = getSelectedDocumentIds();
    if (selectedIds.length === 0) {
        Swal.fire('Peringatan', 'Pilih minimal satu dokumen untuk ditolak', 'warning');
        return;
    }

    $('#selectedDocsCountReject').text(selectedIds.length);
    $('#batchRejectModal').modal('show');
}

function resetDocumentSelection() {
    $('.document-checkbox').prop('checked', false);
    $('#selectAllDocuments').prop('checked', false);
    Swal.fire({
        title: 'Reset Berhasil',
        text: 'Semua pilihan dokumen telah direset',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function getSelectedDocumentIds() {
    const selectedIds = [];
    $('.document-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    return selectedIds;
}

// Handle batch approve form submission
$('#batchApproveForm').on('submit', function(e) {
    e.preventDefault();
    
    const selectedIds = getSelectedDocumentIds();
    const adminNotes = $(this).find('textarea[name="admin_notes"]').val();
    
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyetujui ' + selectedIds.length + ' dokumen',
        allowOutsideClick: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    // Submit batch approval
    submitBatchDocumentAction('approve', selectedIds, adminNotes);
});

// Handle batch reject form submission
$('#batchRejectForm').on('submit', function(e) {
    e.preventDefault();
    
    const selectedIds = getSelectedDocumentIds();
    const adminNotes = $(this).find('textarea[name="admin_notes"]').val();
    
    if (!adminNotes.trim()) {
        Swal.fire('Peringatan', 'Alasan penolakan harus diisi', 'warning');
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menolak ' + selectedIds.length + ' dokumen',
        allowOutsideClick: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    // Submit batch rejection
    submitBatchDocumentAction('reject', selectedIds, adminNotes);
});

function submitBatchDocumentAction(action, selectedIds, adminNotes) {
    const form = $('<form>', {
        method: 'POST',
        action: '{{ route((isset($spref) ? $spref : "") . "pmb.pendaftar-batch-validasi", $pendaftar->code) }}'
    });
    
    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
    form.append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }));
    form.append($('<input>', { type: 'hidden', name: 'action', value: action }));
    form.append($('<input>', { type: 'hidden', name: 'admin_notes', value: adminNotes }));
    
    selectedIds.forEach(function(id) {
        form.append($('<input>', { type: 'hidden', name: 'upload_ids[]', value: id }));
    });
    
    $('body').append(form);
    form.submit();
}
</script>
@endsection
