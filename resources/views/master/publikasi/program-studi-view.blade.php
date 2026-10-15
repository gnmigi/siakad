@extends('core-themes.core-backpage')

@section('custom-css')
<style>
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .program-image {
        max-width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .info-card {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        color: #6b7280;
        font-size: 0.95rem;
    }
    
    .content-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        color: #1f2937;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .meta-info {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .meta-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .meta-item:last-child {
        border-bottom: none;
    }
    
    .meta-label {
        font-weight: 600;
        color: #374151;
    }
    
    .meta-value {
        color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Publikasi</div>
                <h2 class="page-title">Detail Program Studi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('master.publikasi.program-studi.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 6"/>
                            <path d="M5 12l6 -6"/>
                        </svg>
                        Kembali
                    </a>
                    <a href="{{ route('master.publikasi.program-studi.edit', $programStudi) }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Edit Program Studi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <!-- Program Studi Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $programStudi->name }}</h3>
                        <div class="card-actions">
                            <span class="status-badge {{ $programStudi->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ $programStudi->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                @if($programStudi->photo)
                                    <img src="{{ asset('storage/images/program-studi/' . $programStudi->photo) }}" 
                                         alt="{{ $programStudi->name }}" 
                                         class="program-image">
                                @else
                                    <div class="program-image bg-secondary d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/>
                                            <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-8">
                                <div class="info-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="info-label">Nama Program Studi</div>
                                                <div class="info-value">{{ $programStudi->name }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="info-label">Kategori</div>
                                                <div class="info-value">
                                                    <span class="badge bg-blue-lt">{{ $programStudi->kategori->name }}</span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="info-label">Status</div>
                                                <div class="info-value">
                                                    <span class="status-badge {{ $programStudi->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                                        {{ $programStudi->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="info-label">Kode Program Studi</div>
                                                <div class="info-value">{{ $programStudi->code }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="info-label">Tanggal Dibuat</div>
                                                <div class="info-value">{{ $programStudi->created_at->format('d F Y, H:i') }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="info-label">Terakhir Diupdate</div>
                                                <div class="info-value">{{ $programStudi->updated_at->format('d F Y, H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="col-12">
                <div class="content-section">
                    <h3 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                            <path d="M9 9l1 0"/>
                            <path d="M9 13l6 0"/>
                            <path d="M9 17l6 0"/>
                        </svg>
                        Deskripsi Program Studi
                    </h3>
                    <div class="text-muted">
                        {{ $programStudi->description }}
                    </div>
                </div>
            </div>

            <!-- Konten Detail -->
            @if($programStudi->content)
            <div class="col-12">
                <div class="content-section">
                    <h3 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"/>
                            <path d="M18 14v4h4"/>
                            <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"/>
                            <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                            <path d="M8 11h4"/>
                            <path d="M8 15h3"/>
                        </svg>
                        Konten Detail
                    </h3>
                    <div class="text-muted" style="white-space: pre-line;">{{ $programStudi->content }}</div>
                </div>
            </div>
            @endif

            <!-- Informasi Tambahan -->
            <div class="col-12">
                <div class="content-section">
                    <h3 class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9h.01"/>
                            <path d="M11 12h1v4h1"/>
                            <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/>
                        </svg>
                        Informasi Tambahan
                    </h3>
                    <div class="meta-info">
                        <div class="meta-item">
                            <span class="meta-label">ID Program Studi</span>
                            <span class="meta-value">{{ $programStudi->id }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Kode Program Studi</span>
                            <span class="meta-value">{{ $programStudi->code }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Kategori</span>
                            <span class="meta-value">{{ $programStudi->kategori->name }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Status</span>
                            <span class="meta-value">
                                <span class="status-badge {{ $programStudi->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $programStudi->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Dibuat pada</span>
                            <span class="meta-value">{{ $programStudi->created_at->format('d F Y, H:i:s') }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Terakhir diupdate</span>
                            <span class="meta-value">{{ $programStudi->updated_at->format('d F Y, H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="btn-list">
                            <a href="{{ route('master.publikasi.program-studi.index') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l14 0"/>
                                    <path d="M5 12l6 6"/>
                                    <path d="M5 12l6 -6"/>
                                </svg>
                                Kembali ke Daftar
                            </a>
                            <a href="{{ route('master.publikasi.program-studi.edit', $programStudi) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                    <path d="M16 5l3 3"/>
                                </svg>
                                Edit Program Studi
                            </a>
                            <form action="{{ route('master.publikasi.program-studi.toggle-status', $programStudi) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $programStudi->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    @if($programStudi->status === 'active')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10.09 4.01l.496 -.495a2 2 0 0 1 2.828 0l7.071 7.07a2 2 0 0 1 0 2.83l-7.07 7.07a2 2 0 0 1 -2.83 0l-7.07 -7.07a2 2 0 0 1 0 -2.83l3.535 -3.535h-3.988"/>
                                            <path d="M7.05 11.038v-3.988"/>
                                        </svg>
                                        Nonaktifkan
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l14 0"/>
                                            <path d="M13 18l6 -6"/>
                                            <path d="M13 6l6 6"/>
                                        </svg>
                                        Aktifkan
                                    @endif
                                </button>
                            </form>
                            <form action="{{ route('master.publikasi.program-studi.destroy', $programStudi) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program studi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0"/>
                                        <path d="M10 11l0 6"/>
                                        <path d="M14 11l0 6"/>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
