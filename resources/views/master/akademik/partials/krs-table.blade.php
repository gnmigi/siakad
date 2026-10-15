<table class="table table-vcenter card-table">
    <thead>
        <tr>
            @if($showBulkActions)
            <th class="w-1" data-orderable="false">
                <input class="form-check-input m-0 align-middle select-all-krs" type="checkbox">
            </th>
            @endif
            <th>Mahasantri</th>
            <th>Program Studi</th>
            <th>Semester</th>
            <th>Tahun Akademik</th>
            <th>Total SKS</th>
            <th>Status</th>
            <th class="sort-tanggal">Tanggal Submit</th>
            <th class="w-1">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($krsData as $krs)
        <tr>
            @if($showBulkActions)
            <td>
                @if($krs->status === 'submitted')
                <input class="form-check-input m-0 align-middle bulk-approval-checkbox" type="checkbox" value="{{ $krs->id }}">
                @endif
            </td>
            @endif
            <td>
                <div class="d-flex py-1 align-items-center">
                    <span class="avatar me-2" style="background-image: url({{ $krs->mahasiswa->photo ?? asset('storage/images/profile/default.jpg') }})"></span>
                    <div class="flex-fill">
                        <div class="font-weight-medium">{{ $krs->mahasiswa->name }}</div>
                        <div class="text-secondary"><small>{{ $krs->mahasiswa->nim }}</small></div>
                    </div>
                </div>
            </td>
            <td>
                <div>{{ $krs->mahasiswa->prodi->name ?? '-' }}</div>
                <div class="text-secondary"><small>{{ $krs->mahasiswa->prodi->code ?? '-' }}</small></div>
            </td>
            <td>
                <span class="badge bg-blue-lt">Semester {{ $krs->semester }}</span>
            </td>
            <td>{{ $krs->tahunAkademik->name ?? '-' }}</td>
            <td>
                <strong>{{ $krs->details->sum('sks') }} SKS</strong>
                <div class="text-secondary"><small>{{ $krs->details->count() }} Mata Kuliah</small></div>
            </td>
            <td>
                @if($krs->status === 'draft')
                    <span class="badge bg-secondary text-white">Draft</span>
                @elseif($krs->status === 'submitted')
                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                @elseif($krs->status === 'approved')
                    <span class="badge bg-success text-white">Disetujui</span>
                @elseif($krs->status === 'rejected')
                    <span class="badge bg-danger text-white">Ditolak</span>
                @endif
            </td>
            <td data-order="{{ $krs->submitted_at ? $krs->submitted_at->format('Y-m-d H:i:s') : '1970-01-01 00:00:00' }}">
                @if($krs->submitted_at)
                    {{ $krs->submitted_at->format('d/m/Y H:i') }}
                @else
                    <span class="text-secondary">-</span>
                @endif
            </td>
            <td>
                <div class="btn-list flex-nowrap">
                    <a href="{{ route('akademik.krs-show', $krs->id) }}" class="btn btn-sm btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                        </svg>
                        Detail
                    </a>
                    @if($krs->status === 'submitted')
                    <form method="POST" action="{{ route('akademik.krs-approve', $krs->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin menyetujui KRS ini?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Setujui
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-danger reject-krs-btn" data-bs-toggle="modal" data-bs-target="#rejectionModal" data-krs-id="{{ $krs->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        Tolak
                    </button>
                    @elseif($krs->status === 'rejected')
                    <form method="POST" action="{{ route('akademik.krs-reopen', $krs->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Yakin ingin membuka kembali KRS ini untuk diajukan ulang?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 12v-3a3 3 0 0 1 3 -3h13m-3 -3l3 3l-3 3" />
                                <path d="M20 12v3a3 3 0 0 1 -3 3h-13m3 3l-3 -3l3 -3" />
                            </svg>
                            Ajukan Ulang
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>