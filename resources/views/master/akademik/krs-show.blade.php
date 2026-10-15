@extends('core-themes.core-backpage')

@section('content')
<div class="row row-deck row-cards">
    <!-- KRS Header Information -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail KRS Mahasantri</h3>
                <div class="card-actions">
                    <a href="{{ route($spref . 'akademik.krs-render') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mahasantri</label>
                            <div class="d-flex align-items-center">
                                <span class="avatar me-2" style="background-image: url({{ $krs->mahasiswa->photo ?? asset('storage/images/profile/default.jpg') }})"></span>
                                <div>
                                    <div class="font-weight-medium">{{ $krs->mahasiswa->name }}</div>
                                    <div class="text-secondary">{{ $krs->mahasiswa->nim }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <div>{{ $krs->mahasiswa->prodi->name ?? '-' }}</div>
                            <div class="text-secondary"><small>{{ $krs->mahasiswa->prodi->code ?? '-' }}</small></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <div><span class="badge bg-blue-lt">Semester {{ $krs->semester }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tahun Akademik</label>
                            <div>{{ $krs->tahunAkademik->name ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total SKS</label>
                            <div><strong>{{ $krs->details->sum('sks') }} SKS</strong></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                @if($krs->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($krs->status === 'submitted')
                                    <span class="badge bg-warning">Menunggu Persetujuan</span>
                                @elseif($krs->status === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($krs->status === 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($krs->status === 'submitted')
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="btn-list">
                            <form method="POST" action="{{ route($spref . 'akademik.krs-approve', $krs->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin menyetujui KRS ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Setujui KRS
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectionModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Tolak KRS
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                @if($krs->status === 'approved')
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">KRS Disetujui</h4>
                                    <div class="text-secondary">
                                        Disetujui pada {{ $krs->approved_at ? $krs->approved_at->format('d/m/Y H:i') : '-' }}
                                        @if($krs->approvedBy)
                                            oleh {{ $krs->approvedBy->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($krs->status === 'rejected')
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">KRS Ditolak</h4>
                                    <div class="text-secondary">
                                        Ditolak pada {{ $krs->rejected_at ? $krs->rejected_at->format('d/m/Y H:i') : '-' }}
                                        @if($krs->rejectedBy)
                                            oleh {{ $krs->rejectedBy->name }}
                                        @endif
                                    </div>
                                    @if($krs->rejection_reason)
                                    <div class="mt-2">
                                        <strong>Alasan:</strong> {{ $krs->rejection_reason }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- KRS Details Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Mata Kuliah</h3>
                <div class="card-actions">
                    <span class="badge bg-primary">{{ $krs->details->count() }} Mata Kuliah</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Dosen</th>
                                <th>Kelas</th>
                                <th>Jadwal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($krs->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ $detail->mataKuliah->code ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="font-weight-medium">{{ $detail->mataKuliah->name ?? '-' }}</div>
                                    <div class="text-secondary"><small>{{ $detail->mataKuliah->type ?? '-' }}</small></div>
                                </td>
                                <td>
                                    <strong>{{ $detail->sks }}</strong>
                                </td>
                                <td>
                                    @if($detail->mataKuliah && $detail->mataKuliah->dosen1)
                                        <div>{{ $detail->mataKuliah->dosen1->name }}</div>
                                        <div class="text-secondary"><small>{{ $detail->mataKuliah->dosen1->nip ?? 'NIP: -' }}</small></div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->kelas)
                                        <span class="badge bg-green-lt">{{ $detail->kelas->name }}</span>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->jadwal)
                                        <div>{{ $detail->jadwal->hari ?? '-' }}</div>
                                        <div class="text-secondary">
                                            <small>{{ $detail->jadwal->jam_mulai ?? '-' }} - {{ $detail->jadwal->jam_selesai ?? '-' }}</small>
                                        </div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-img"><img src="{{ asset('dashboard/static/illustrations/undraw_quitting_time_dm8t.svg') }}" height="128" alt=""></div>
                                        <p class="empty-title">Tidak ada mata kuliah</p>
                                        <p class="empty-subtitle text-secondary">
                                            Belum ada mata kuliah yang dipilih dalam KRS ini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($krs->details->count() > 0)
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total SKS:</th>
                                <th><strong>{{ $krs->details->sum('sks') }}</strong></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal modal-blur fade" id="rejectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route($spref . 'akademik.krs-reject', $krs->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak KRS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="4" placeholder="Masukkan alasan penolakan KRS..." required></textarea>
                        <div class="form-hint">Berikan alasan yang jelas mengapa KRS ini ditolak.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak KRS</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
