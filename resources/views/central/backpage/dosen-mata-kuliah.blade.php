@extends('central.backpage.core-backpage-dosen')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ $menus }}
                </div>
                <h2 class="page-title">
                    {{ $pages }}
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Mata Kuliah yang Diampu</h3>
                        <div class="card-actions">
                            <span class="badge bg-blue">{{ count($mataKuliah) }} Mata Kuliah</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($mataKuliah) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode MK</th>
                                            <th>Nama Mata Kuliah</th>
                                            <th>SKS</th>
                                            <th>Semester</th>
                                            <th>Program Studi</th>
                                            <th>Status Dosen</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mataKuliah as $key => $mk)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-azure text-white fw-bold">{{ (!empty(trim($mk->code))) ? $mk->code : 'MK-' . str_pad($mk->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex py-1 align-items-center">
                                                        <div class="flex-fill">
                                                            <div class="font-weight-medium">{{ $mk->name }}</div>
                                                            @if($mk->desc && trim($mk->desc) !== '.' && trim($mk->desc) !== '' && trim($mk->desc) !== $mk->name)
                                                                <div class="text-secondary small text-muted">{{ Str::limit($mk->desc, 100) }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $mk->bsks ?? 0 }} SKS</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-purple-lt">Semester {{ $mk->semester ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-secondary">
                                                        {{ $mk->programStudi->name ?? 'Program Studi tidak ditemukan' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusDosen = '';
                                                        if($mk->dosen1_id == $user->id) $statusDosen = 'Dosen Utama';
                                                        elseif($mk->dosen2_id == $user->id) $statusDosen = 'Dosen Pendamping 1';
                                                        elseif($mk->dosen3_id == $user->id) $statusDosen = 'Dosen Pendamping 2';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusDosen == 'Dosen Utama' ? 'success' : 'warning' }}">
                                                        {{ $statusDosen }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($mk->status == 'Aktif')
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ route($spref.'nilai.input-render') }}?mata_kuliah_id={{ $mk->id }}" class="btn btn-sm btn-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M12 5l0 14"/>
                                                                <path d="M5 12l14 0"/>
                                                            </svg>
                                                            Input Nilai
                                                        </a>
                                                        <a href="{{ route($spref.'nilai.rekap-render') }}?mata_kuliah_id={{ $mk->id }}" class="btn btn-sm btn-info">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/>
                                                                <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/>
                                                                <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/>
                                                                <path d="M4 20l14 0"/>
                                                            </svg>
                                                            Rekap
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-img">
                                    <img src="{{ asset('dashboard/static/illustrations/undraw_printing_invoices_5r4r.svg') }}" height="128" alt="">
                                </div>
                                <p class="empty-title">Tidak ada mata kuliah</p>
                                <p class="empty-subtitle text-secondary">
                                    Anda belum ditugaskan untuk mengampu mata kuliah apapun.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(count($mataKuliah) > 0)
            <div class="row row-cards mt-3">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            <span class="avatar avatar-xl mb-3 bg-primary-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                                    <path d="M10 14l2 2l4 -4"/>
                                    <path d="M14 4v4a2 2 0 0 0 2 2h4"/>
                                </svg>
                            </span>
                            <h3 class="m-0 mb-1">{{ count($mataKuliah) }}</h3>
                            <div class="text-secondary">Total Mata Kuliah</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            <span class="avatar avatar-xl mb-3 bg-success-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                            </span>
                            <h3 class="m-0 mb-1">{{ $mataKuliah->where('status', 'Aktif')->count() }}</h3>
                            <div class="text-secondary">Mata Kuliah Aktif</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            <span class="avatar avatar-xl mb-3 bg-warning-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 9v4"/>
                                    <path d="M12 17h.01"/>
                                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                                </svg>
                            </span>
                            <h3 class="m-0 mb-1">{{ $mataKuliah->where('dosen1_id', $user->id)->count() }}</h3>
                            <div class="text-secondary">Sebagai Dosen Utama</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            <span class="avatar avatar-xl mb-3 bg-info-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5"/>
                                    <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42-3.39z"/>
                                </svg>
                            </span>
                            <h3 class="m-0 mb-1">{{ $mataKuliah->sum('bsks') }}</h3>
                            <div class="text-secondary">Total SKS</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('custom-css')
<style>
.btn-list .btn {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}
</style>
@endsection
