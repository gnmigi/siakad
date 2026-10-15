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
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
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
    
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.1);
    }
    
    .action-buttons .btn {
        margin: 0 2px;
        padding: 0.375rem 0.75rem;
    }
    
    .program-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Publikasi</div>
                <h2 class="page-title">Program Studi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('master.publikasi.program-studi.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Tambah Program Studi
                    </a>
                    <a href="{{ route('master.publikasi.program-studi.create') }}" class="btn btn-primary d-sm-none btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Program Studi</h3>
                    </div>
                    <div class="card-body">
                        @if($programStudis->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Gambar</th>
                                            <th>Nama Program Studi</th>
                                            <th>Kategori</th>
                                            <th>Status</th>
                                            <th>Tanggal Dibuat</th>
                                            <th class="w-1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($programStudis as $index => $programStudi)
                                            <tr>
                                                <td>{{ $programStudis->firstItem() + $index }}</td>
                                                <td>
                                                    @if($programStudi->photo)
                                                        <img src="{{ asset('storage/images/program-studi/' . $programStudi->photo) }}" 
                                                             alt="{{ $programStudi->name }}" 
                                                             class="program-image">
                                                    @else
                                                        <div class="program-image bg-secondary d-flex align-items-center justify-content-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/>
                                                                <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $programStudi->name }}</span>
                                                        <small class="text-muted">{{ Str::limit($programStudi->description, 60) }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-blue-lt">{{ $programStudi->kategori->name }}</span>
                                                </td>
                                                <td>
                                                    <span class="status-badge {{ $programStudi->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                                        {{ $programStudi->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>{{ $programStudi->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('master.publikasi.program-studi.show', $programStudi) }}" 
                                                           class="btn btn-sm btn-outline-info" title="Lihat">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('master.publikasi.program-studi.edit', $programStudi) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                                <path d="M16 5l3 3"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('master.publikasi.program-studi.toggle-status', $programStudi) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-sm {{ $programStudi->status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }}" 
                                                                    title="{{ $programStudi->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                                @if($programStudi->status === 'active')
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                        <path d="M10.09 4.01l.496 -.495a2 2 0 0 1 2.828 0l7.071 7.07a2 2 0 0 1 0 2.83l-7.07 7.07a2 2 0 0 1 -2.83 0l-7.07 -7.07a2 2 0 0 1 0 -2.83l3.535 -3.535h-3.988"/>
                                                                        <path d="M7.05 11.038v-3.988"/>
                                                                    </svg>
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                        <path d="M5 12l14 0"/>
                                                                        <path d="M13 18l6 -6"/>
                                                                        <path d="M13 6l6 6"/>
                                                                    </svg>
                                                                @endif
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('master.publikasi.program-studi.destroy', $programStudi) }}" 
                                                              method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus program studi ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M4 7l16 0"/>
                                                                    <path d="M10 11l0 6"/>
                                                                    <path d="M14 11l0 6"/>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $programStudis->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/>
                                    <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/>
                                </svg>
                                <h3 class="text-muted">Belum ada program studi</h3>
                                <p class="text-muted">Mulai dengan menambahkan program studi pertama Anda.</p>
                                <a href="{{ route('master.publikasi.program-studi.create') }}" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14"/>
                                        <path d="M5 12l14 0"/>
                                    </svg>
                                    Tambah Program Studi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('menus', 'Master')
@section('pages', 'Program Studi')
