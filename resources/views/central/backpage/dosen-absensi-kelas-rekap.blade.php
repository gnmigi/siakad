@extends('central.backpage.core-backpage-dosen')

@section('custom-css')
<style>
.table-rekap-container { overflow-x: auto; white-space: nowrap; }
.table-rekap th, .table-rekap td { vertical-align: middle; text-align: center; }
.table-rekap td.nama-mhs { text-align: left; position: sticky; left: 0; background-color: var(--tblr-bg-surface); z-index: 1; border-right: 2px solid var(--tblr-border-color); }
[data-bs-theme="dark"] .table-rekap td.nama-mhs { background-color: #1e293b; }
.table-rekap .badge { color: #ffffff !important; }
@media (max-width: 767px) {
    .nav-tabs.swipeable-tabs { flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; white-space: nowrap; -webkit-overflow-scrolling: touch; border-bottom: 1px solid var(--tblr-border-color); padding-bottom: 2px; }
    .nav-tabs.swipeable-tabs::-webkit-scrollbar { display: none; }
    .swipeable-tabs .nav-item { display: inline-block; float: none; }
}
#page-loader { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.85); z-index: 99999; display: none; justify-content: center; align-items: center; flex-direction: column; backdrop-filter: blur(5px); }
[data-bs-theme="dark"] #page-loader { background: rgba(30, 41, 59, 0.85); }
/* ==========================================
   CSS UNTUK MODE BUTA WARNA
   ========================================== */
body.colorblind-mode .table-rekap .badge.bg-success { background-color: transparent !important; color: #000 !important; border: 2px solid #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-info { background-color: transparent !important; color: #000 !important; border: 2px dashed #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-warning { background-color: transparent !important; color: #000 !important; border: 2px dotted #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-danger { background-color: #000 !important; color: #fff !important; border: 2px solid #000 !important; border-radius: 50% !important; }

/* Dukungan untuk Dark Mode saat Mode Buta Warna aktif */
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-success, 
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-info, 
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-warning { color: #fff !important; border-color: #fff !important; }
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-danger { background-color: #fff !important; color: #000 !important; border-color: #fff !important; }
</style>
@endsection

@section('content')
<div id="page-loader">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 0.3em;"></div>
    <h2 class="text-primary mb-1 text-center px-3">Memuat Data Absensi...</h2>
</div>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $menus }}</div>
                <h2 class="page-title"><i class="fas fa-chart-line me-2"></i>{{ $pages }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="colorblindToggle">
                    <label class="form-check-label fw-bold d-none d-sm-inline" for="colorblindToggle">Mode Aksesibilitas</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form action="{{ route('dosen.absensi-kelas.rekap') }}" method="GET" id="formFilterRekap">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label required">Pilih Mata Kuliah</label>
                            <select name="mata_kuliah_id" class="form-select" required onchange="gantiMatkul()">
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($mataKuliahList as $mk)
                                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>{{ $mk->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-primary">Filter Kelas</label>
                            <select name="kelompok" id="dropdownKelompok" class="form-select border-primary" onchange="showLoader(); this.form.submit()" {{ !request('mata_kuliah_id') ? 'disabled' : '' }}>
                                <option value="all" {{ $kelompokPilih == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                                @if(isset($groupedMahasiswa))
                                    @foreach(collect($groupedMahasiswa)->sortKeysDesc() as $kel => $mhs)
                                        <option value="{{ $kel }}" {{ $kelompokPilih == $kel ? 'selected' : '' }}>{{ $kel }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 mt-3 border-top pt-3">
                            <label class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="krs_only" value="1" onchange="showLoader(); this.form.submit()" {{ isset($krs_only) && $krs_only ? 'checked' : '' }}>
                                <span class="form-check-label text-primary fw-bold">Hanya Mahasantri KRS Aktif</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedMatkul)
            <div class="card mb-3 bg-primary-lt border-primary">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="w-100">
                        <h3 class="mb-1 text-primary">
                            <i class="fas fa-book-open me-2"></i>{{ $selectedMatkul->name }} 
                            <span class="badge bg-primary-lt ms-2">Sem. {{ $selectedMatkul->semester ?? '-' }}</span>
                        </h3>
                        <div class="text-muted small">Ket: <strong>H = Hadir</strong>, <strong>S = Sakit</strong>, <strong>I = Izin</strong>, <strong>A = Alpa/Absen</strong></div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                        <a href="{{ route('dosen.absensi-kelas.show', $selectedMatkul->id) }}" class="btn btn-primary shadow-sm w-100 w-sm-auto">
                            <i class="fas fa-edit me-2"></i> Isi Absen
                        </a>
                        <a href="{{ route('dosen.absensi-kelas.export', ['mata_kuliah_id' => $selectedMatkul->id, 'kelompok' => $kelompokPilih, 'krs_only' => isset($krs_only) && $krs_only ? 1 : 0]) }}" class="btn btn-success shadow-sm w-100 w-sm-auto" onclick="showExportLoader()">
                            <i class="fas fa-file-excel me-2"></i> Export
                        </a>
                        <button class="btn btn-warning shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-upload me-2"></i> Import
                        </button>
                    </div>
                </div>
            </div>

            @if($rekapDataByKelas->isNotEmpty())
            <div class="card mb-4 shadow-sm">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs swipeable-tabs px-3 pt-3" data-bs-toggle="tabs" role="tablist">
                        @php $tabIndex = 0; @endphp
                        @foreach(collect($rekapDataByKelas)->sortKeysDesc() as $kelompok => $rekapData)
                        <li class="nav-item" role="presentation">
                            <a href="#tab-rekap-{{ $tabIndex }}" class="nav-link {{ $tabIndex == 0 ? 'active' : '' }}" data-bs-toggle="tab">{{ $kelompok }}</a>
                        </li>
                        @php $tabIndex++; @endphp
                        @endforeach
                    </ul>
                </div>
                
                <div class="card-body p-0">
                    <div class="tab-content">
                        @php $contentIndex = 0; @endphp
                        @foreach(collect($rekapDataByKelas)->sortKeysDesc() as $kelompok => $rekapData)
                        <div class="tab-pane {{ $contentIndex == 0 ? 'active show' : '' }}" id="tab-rekap-{{ $contentIndex }}" role="tabpanel">
                            <div class="table-rekap-container">
                                <table class="table table-vcenter table-bordered table-striped table-rekap mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Nama Mahasantri</th>
                                            <th colspan="14">Pertemuan Ke-</th>
                                            <th rowspan="2">%</th>
                                        </tr>
                                        <tr>
                                            @for($i = 1; $i <= 14; $i++) <th style="min-width: 35px;">{{ $i }}</th> @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekapData as $idx => $data)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td class="fw-bold nama-mhs small">{{ $data['mhs']->name }}</td>
                                            @for($i = 1; $i <= 14; $i++)
                                                @php $status = $data['pertemuan'][$i]; @endphp
                                                <td>
                                                    @if($status == 'H') <span class="badge bg-success text-white">H</span>
                                                    @elseif($status == 'S') <span class="badge bg-info text-white">S</span>
                                                    @elseif($status == 'I') <span class="badge bg-warning text-white">I</span>
                                                    @elseif($status == 'A') <span class="badge bg-danger text-white">A</span>
                                                    @else <span class="text-muted">-</span> @endif
                                                </td>
                                            @endfor
                                            <td><span class="badge bg-{{ $data['persentase'] >= 85 ? 'success' : 'danger' }}">{{ $data['persentase'] }}%</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @php $contentIndex++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

<div class="modal modal-blur fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Rekap Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('dosen.absensi-kelas.import') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
          @csrf
          <input type="hidden" name="mata_kuliah_id" value="{{ $selectedMatkul->id ?? '' }}">
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">File Excel (.xlsx, .xls)</label>
                <input type="file" class="form-control" name="file_excel" accept=".xlsx, .xls" required>
            </div>
            <div class="mb-0">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="sync_nilai" value="1" checked>
                  <span class="form-check-label fw-bold">Sinkronkan ke Nilai</span>
                </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary ms-auto">Upload</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('custom-js')
<script>
    window.addEventListener('pageshow', function(event) {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    });
    function showLoader() { document.getElementById('page-loader').style.display = 'flex'; }
    function gantiMatkul() {
        showLoader();
        let drop = document.getElementById('dropdownKelompok');
        if(drop) drop.removeAttribute('name');
        document.getElementById('formFilterRekap').submit();
    }
    function showExportLoader() { showLoader(); setTimeout(() => { document.getElementById('page-loader').style.display = 'none'; }, 3000); }
    document.addEventListener('DOMContentLoaded', function() {
        // Menangani Toggle Mode Buta Warna
        const toggle = document.getElementById('colorblindToggle');
        
        if (toggle) {
            // Cek apakah sebelumnya user sudah mengaktifkan mode ini (simpan di localStorage)
            if (localStorage.getItem('colorblindMode') === 'true') { 
                document.body.classList.add('colorblind-mode'); 
                toggle.checked = true; 
            }
            
            // Event ketika toggle di-klik
            toggle.addEventListener('change', function() {
                if (this.checked) { 
                    document.body.classList.add('colorblind-mode'); 
                    localStorage.setItem('colorblindMode', 'true'); 
                } else { 
                    document.body.classList.remove('colorblind-mode'); 
                    localStorage.setItem('colorblindMode', 'false'); 
                }
            });
        }
    });
</script>
@endsection