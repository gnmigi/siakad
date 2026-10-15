@extends('central.backpage.core-backpage-dosen')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $menus }}</div>
                <h2 class="page-title"><i class="fas fa-clipboard-list me-2"></i>{{ $pages }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('dosen.absensi-kelas.rekap') }}" class="btn btn-info shadow-sm w-100 w-sm-auto">
                    <i class="fas fa-chart-line me-2"></i> Lihat Semua Rekap
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            @forelse($mataKuliah as $mk)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card mata-kuliah-card h-100">
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-auto">
                                <div class="avatar avatar-lg bg-primary-lt text-primary">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-bold text-truncate" title="{{ $mk->name }}">{{ $mk->name }}</div>
                                <div class="text-muted small">
                                    <span class="badge bg-blue-lt me-1">Semester {{ $mk->semester ?? '-' }}</span> 
                                    {{ $mk->bsks ?? $mk->sks }} SKS • {{ $mk->programStudi->name ?? 'Program Studi' }}
                                </div>
                            </div>  
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('dosen.absensi-kelas.show', $mk->id) }}" class="btn btn-primary w-100">
                                <i class="fas fa-edit me-2"></i> Isi Absensi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="empty-title">Belum ada mata kuliah</p>
                        <p class="empty-subtitle text-secondary">
                            Anda belum memiliki mata kuliah yang diampu.
                        </p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection