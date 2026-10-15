@extends('base.base-dash-index')

@section('title')
    Kartu Rencana Studi - {{ $user->name }}
@endsection

@section('menu')
    Akademik
@endsection

@section('submenu')
    Kartu Rencana Studi (KRS)
@endsection

@section('urlmenu')
    #
@endsection

@section('subdesc')
    Halaman untuk melihat dan mencetak KRS semester aktif
@endsection

@section('custom-css')
<style>
    /* Remove old info-table styles */
    /* .info-table td {
        border: none;
        padding: 4px 8px;
    } */
    table.data-table {
        border: 1px solid #ccc;
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    table.data-table th,
    table.data-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        /* Adjust student info grid */
        .student-info-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
        .student-info-label,
        .student-info-value {
            flex: 1 1 50%;
            padding: 2px 8px;
            box-sizing: border-box;
        }
        .student-info-label {
            font-weight: 600;
            text-align: right;
            padding-right: 12px;
        }
        .student-info-value {
            text-align: left;
        }

        /* Reduce padding in table cells */
        table.data-table th,
        table.data-table td {
            padding: 6px 4px;
            font-size: 14px;
            white-space: nowrap; /* prevent wrapping */
        }

        /* Force table elements to display as table to prevent stacking */
        table.data-table {
            display: table !important;
        }
        table.data-table thead {
            display: table-header-group !important;
        }
        table.data-table tbody {
            display: table-row-group !important;
        }
        table.data-table tr {
            display: table-row !important;
        }
        table.data-table th,
        table.data-table td {
            display: table-cell !important;
            white-space: nowrap !important;
        }

        /* Buttons full width on mobile */
        .btn {
            width: 100% !important;
            margin-bottom: 8px;
        }

        /* Form select full width */
        select.form-select {
            width: 100% !important;
        }

        /* Force scrollbars visible on table-responsive */
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            border: 1px solid #ccc;
            max-height: none; /* remove height limit */
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        table.data-table {
            min-width: 700px !important; /* ensure table is wider than container to enable scroll */
            width: auto !important; /* allow natural width */
            table-layout: auto !important; /* allow natural column widths */
        }
    }

    /* New styles for student info list */
    .student-info-list {
        display: grid;
        grid-template-columns: max-content auto;
        gap: 4px 12px;
        max-width: 400px;
    }
    .student-info-list dt {
        font-weight: 600;
        text-align: right;
        padding-right: 4px;
        white-space: nowrap;
    }
    .student-info-list dd {
        margin: 0;
        text-align: left;
    }
    .student-info-list div {
        display: contents;
    }

    @media (max-width: 767px) {
        .student-info-list {
            display: block;
            max-width: 100%;
        }
        .student-info-list div {
            margin-bottom: 8px;
            display: block;
        }
        .student-info-list dt {
            display: inline-block;
            padding-right: 0;
        }
        .student-info-list dd {
            display: inline-block;
            margin-left: 4px;
        }
    }
</style>
@endsection

@section('content')
<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">@yield('submenu')</h5>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-warning">
                    <i class="fa-solid fa-backward"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Form Pilih Semester --}}
            <form method="GET" action="{{ route('mahasiswa.akademik.krs-index') }}" class="mb-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="semester_id" class="col-form-label"><strong>Pilih Semester</strong></label>
                    </div>
                    <div class="col-auto">
                        <select name="semester_id" id="semester_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ (isset($taka) && $semester->id == $taka->id) ? 'selected' : '' }}>
                                    {{ $semester->name }} - {{ $semester->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if(isset($taka))
                <div class="mb-3">
                    <dl class="student-info-list">
                        <div>
                            <dt>Nama</dt>
                            <dd>{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt>Program Studi</dt>
                            <dd>{{ $pstudi->name }}</dd>
                        </div>
                        <div>
                            <dt>NIM</dt>
                            <dd>{{ $user->nim }}</dd>
                        </div>
                        <div>
                            <dt>Semester</dt>
                            <dd>{{ $taka->name }} - {{ $taka->semester }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Daftar Mata Kuliah Semester Aktif --}}  
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Dosen Pengampu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matkul as $index => $mk)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $mk->name }}</td>
                                    <td>{{ $mk->bsks }}</td>
                                    <td>{{ optional($mk->dosen1)->dsn_name ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Tidak ada mata kuliah pada semester ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <form action="{{ url('mahasiswa/akademik/krs/cetak') }}" method="post" class="d-inline-block mt-3">
                    @csrf
                    <input type="hidden" name="semester_id" value="{{ $taka->id }}">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                    </button>
                </form>

                @php
                    $approvalStatus = $approval ? $approval->status : null;
                @endphp

                @if(isset($remainingBill) && $remainingBill > 0)
                    <div class="alert alert-danger">
                        <strong>Perhatian:</strong> Anda memiliki sisa tagihan sebesar <strong>Rp {{ number_format($remainingBill, 0, ',', '.') }}</strong> untuk semester ini. Silakan lunasi tagihan terlebih dahulu sebelum mengajukan KRS.
                    </div>
                    <button class="btn btn-outline-primary" disabled>
                        <i class="fa-solid fa-paper-plane"></i> Ajukan KRS
                    </button>
                @else
                    @if($approvalStatus === 'approved')
                        <span class="badge bg-success ms-2">KRS Disetujui</span>
                    @elseif($approvalStatus === 'pending')
                        <span class="badge bg-warning text-dark ms-2">Menunggu Persetujuan</span>
                    @elseif($approvalStatus === 'rejected')
                        <span class="badge bg-danger ms-2" title="Alasan: {{ $approval->rejection_reason }}">KRS Ditolak</span>
                        @php
                            $paymentConfirmed = \App\Models\HistoryTagihan::where('users_id', $user->id)
                                ->where('stat', 1)
                                ->exists();
                        @endphp
                        @if($paymentConfirmed)
                            <form action="{{ route('mahasiswa.akademik.krs-ajukan-ulang') }}" method="post" class="d-inline-block ms-2">
                                @csrf
                                <input type="hidden" name="semester_id" value="{{ $taka->id }}">
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="fa-solid fa-rotate"></i> Ajukan Ulang KRS
                                </button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('mahasiswa.akademik.krs-ajukan') }}" method="post" class="d-inline-block ms-2">
                            @csrf
                            <input type="hidden" name="semester_id" value="{{ $taka->id }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa-solid fa-paper-plane"></i> Ajukan KRS
                            </button>
                        </form>
                    @endif
                @endif
            @else
                <p>Silakan pilih semester terlebih dahulu untuk melihat KRS.</p>
            @endif
        </div>
    </div>
</section>
@endsection

@section('custom-js')
<script>
    // Tambahan JS jika perlu
</script>
@endsection
