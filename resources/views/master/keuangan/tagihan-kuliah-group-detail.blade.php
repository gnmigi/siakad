@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            padding-bottom: 0.75rem;
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Tagihan Kuliah Group - {{ $group->code }}</h5>
                    <div class="btn-group">
                        @if($group->status === 'Published')
                        <a href="{{ route('keuangan.tagihan-kuliah-group-bulk-operations', $group->code) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Bulk Operations
                        </a>
                        @endif
                        <a href="{{ route('keuangan.tagihan-kuliah-group-render') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Group Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Group</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Nama Group</td>
                                    <td>: {{ $group->name }}</td>
                                </tr>
                                <tr>
                                    <td>Tahun Akademik</td>
                                    <td>: {{ $group->tahunAkademik->name }}</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Tagihan</td>
                                    <td>: Rp {{ number_format($group->amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Tenggat Waktu</td>
                                    <td>: 
                                        @php
                                            // Get unique due dates from tagihan
                                            $dueDates = $tagihans->pluck('due_date')->filter()->unique();
                                            $noDueDateCount = $tagihans->where('due_date', null)->count();
                                            $totalTagihan = $tagihans->count();
                                        @endphp
                                        @if($noDueDateCount == $totalTagihan)
                                            <span class="text-info"><i class="fas fa-infinity me-1"></i>Semua tanpa tempo</span>
                                        @elseif($noDueDateCount > 0 && $dueDates->count() > 0)
                                            <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Campuran: {{ $noDueDateCount }} tanpa tempo, {{ $totalTagihan - $noDueDateCount }} dengan tempo</span>
                                        @elseif($dueDates->count() == 1)
                                            <span class="text-success"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($dueDates->first())->format('d M Y') }}</span>
                                        @elseif($dueDates->count() > 1)
                                            <span class="text-info"><i class="fas fa-calendar-alt me-1"></i>Beragam ({{ $dueDates->count() }} tanggal berbeda)</span>
                                        @else
                                            @if($group->due_date)
                                                <span class="text-success"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($group->due_date)->format('d M Y') }}</span>
                                            @else
                                                <span class="text-muted"><i class="fas fa-infinity me-1"></i>Tidak ada tempo</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Kriteria Mahasantri</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Program Studi</td>
                                    <td>: 
                                        @if($group->prodi_id && $group->prodi)
                                            <span class="badge bg-primary">{{ $group->prodi->name }}</span>
                                        @else
                                            <span class="text-muted">Semua Program Studi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>: 
                                        @if($group->kelas_id && $group->kelas)
                                            <span class="badge bg-info">{{ $group->kelas->name }}</span>
                                        @else
                                            <span class="text-muted">Semua Kelas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gelombang</td>
                                    <td>: 
                                        @if($group->gelombang_id && $group->gelombang)
                                            <span class="badge bg-success">{{ $group->gelombang->name }}</span>
                                        @else
                                            <span class="text-muted">Semua Gelombang</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jalur</td>
                                    <td>: 
                                        @if($group->jalur_id && $group->jalur)
                                            <span class="badge bg-warning">{{ $group->jalur->name }}</span>
                                        @else
                                            <span class="text-muted">Semua Jalur</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Semester</td>
                                    <td>: 
                                        @if($group->semester)
                                            <span class="badge bg-secondary">Semester {{ $group->semester }}</span>
                                        @else
                                            <span class="text-muted">Semua Semester</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Tagihan List -->
                    <div class="table-responsive">
                        <table class="table" id="tagihanTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasantri</th>
                                    <th>Program Studi</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihans as $key => $tagihan)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $tagihan->mahasiswa->name }}</td>
                                        <td>{{ $tagihan->mahasiswa->numb_nim }}</td>
                                        <td>{{ $tagihan->mahasiswa->prodi->name }}</td>
                                        <td>{{ $tagihan->mahasiswa->kelas->name }}</td>
                                        <td>
                                            <span class="badge {{ $tagihan->status == 'Paid' ? 'bg-success' : ($tagihan->status == 'Pending' ? 'bg-warning text-white' : 'bg-danger') }}">
                                                {{ $tagihan->status }}
                                            </span>
                                        </td>
                                        <td>{{ $tagihan->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tagihanTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[0, 'asc']]
            });
        });
    </script>
@endsection 