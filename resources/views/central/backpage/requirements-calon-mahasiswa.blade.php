@extends('central.backpage.core-backpage-calon-mahasiswa')

@section('content')
<div class="container-fluid px-3">
    <!-- Page header -->
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Syarat Pendaftaran</h2>
                <div class="text-muted small">Upload dokumen syarat pendaftaran sesuai ketentuan</div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        @if($requirements->isEmpty())
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="empty">
                        <div class="empty-img mb-3">
                            <img src="{{ asset('dashboard/static/illustrations/undraw_printing_invoices_5r4r.svg') }}" 
                                 style="max-height: 80px; width: auto;" alt="">
                        </div>
                        <p class="empty-title h6 mb-2">Belum ada syarat pendaftaran</p>
                        <p class="empty-subtitle text-muted small mb-0">
                            Syarat pendaftaran untuk periode Anda belum ditetapkan oleh admin.
                        </p>
                    </div>
                </div>
            </div>
        @else
            @foreach($requirements as $requirement)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <div class="d-flex flex-column flex-sm-row align-items-start">
                            <h3 class="card-title mb-1 mb-sm-0 me-auto">
                                {{ $requirement->name }}
                            </h3>
                            @if($requirement->is_required)
                                <span class="badge bg-red">Wajib</span>
                            @else
                                <span class="badge bg-blue">Opsional</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body p-3">
                        @if($requirement->description)
                            <div class="mb-3">
                                <p class="text-muted small mb-0">{{ $requirement->description }}</p>
                            </div>
                        @endif

                        <!-- File Requirements Info -->
                        <div class="row g-2 mb-3">
                            @if($requirement->file_types)
                                <div class="col-12 col-sm-6">
                                    <div class="bg-light rounded p-2">
                                        <small class="text-muted d-block">
                                            <strong>Format:</strong><br>
                                            {{ strtoupper(str_replace(',', ', ', $requirement->file_types)) }}
                                        </small>
                                    </div>
                                </div>
                            @endif

                            @if($requirement->max_file_size)
                                <div class="col-12 col-sm-6">
                                    <div class="bg-light rounded p-2">
                                        <small class="text-muted d-block">
                                            <strong>Ukuran Max:</strong><br>
                                            {{ number_format($requirement->max_file_size / 1024, 0) }} MB
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @php
                            $upload = $uploads->get($requirement->id);
                        @endphp

                        @if($upload)
                            <!-- Uploaded File Info -->
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="avatar avatar-sm me-2 flex-shrink-0">
                                        📄
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-medium small text-break">{{ $upload->file_name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ number_format($upload->file_size / 1024, 1) }} KB •
                                            {{ $upload->uploaded_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <div class="flex-grow-1">
                                        @if($upload->status == 'approved')
                                            <span class="badge bg-green">✓ Disetujui</span>
                                        @elseif($upload->status == 'rejected')
                                            <span class="badge bg-red">✗ Ditolak</span>
                                        @else
                                            <span class="badge bg-yellow">⏳ Menunggu Review</span>
                                        @endif
                                    </div>
                                    <a href="{{ asset('storage/' . $upload->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary btn-sm">
                                        👁️ Lihat File
                                    </a>
                                </div>
                            </div>

                            @if($upload->status == 'rejected' && $upload->rejection_reason)
                                <div class="alert alert-danger py-2 mb-3">
                                    <strong>Alasan penolakan:</strong><br>
                                    <small>{{ $upload->rejection_reason }}</small>
                                </div>
                            @endif
                        @endif

                        <!-- Upload Form -->
                        <form action="{{ route('camaba.requirements-upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="requirement_id" value="{{ $requirement->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label small">Pilih File:</label>
                                <input type="file" 
                                       class="form-control form-control-sm" 
                                       name="file" 
                                       accept="{{ $requirement->file_types ? '.' . str_replace(',', ',.', $requirement->file_types) : '' }}" 
                                       required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                📤 
                                @if($upload)
                                    Ganti File
                                @else
                                    Upload File
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Back Button -->
        <div class="mt-4">
            <a href="{{ route('camaba.dashboard-render') }}" class="btn btn-secondary btn-sm">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Mobile Optimization CSS -->
<style>
/* Mobile First Approach */
.container-fluid {
    max-width: 100vw;
    overflow-x: hidden;
}

.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.text-break {
    word-break: break-word;
    overflow-wrap: break-word;
}

.min-width-0 {
    min-width: 0;
}

.avatar {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 1rem;
}

/* Small screens */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 12px;
        padding-right: 12px;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .form-control-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .alert {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
}

/* Extra small screens */
@media (max-width: 400px) {
    .container-fluid {
        padding-left: 8px;
        padding-right: 8px;
    }
    
    .card {
        margin-bottom: 0.75rem !important;
    }
    
    .page-title {
        font-size: 1.1rem;
    }
}

/* Prevent horizontal scroll */
body {
    overflow-x: hidden;
}

.row {
    margin-left: -0.5rem;
    margin-right: -0.5rem;
}

.row > * {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}
</style>
@endsection