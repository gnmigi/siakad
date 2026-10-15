@extends('core-themes.core-backpage')

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
body.colorblind-mode .table-rekap .badge.bg-success { background-color: transparent !important; color: #000 !important; border: 2px solid #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-info { background-color: transparent !important; color: #000 !important; border: 2px dashed #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-warning { background-color: transparent !important; color: #000 !important; border: 2px dotted #000 !important; }
body.colorblind-mode .table-rekap .badge.bg-danger { background-color: #000 !important; color: #fff !important; border: 2px solid #000 !important; border-radius: 50% !important; }
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-success, [data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-info, [data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-warning { color: #fff !important; border-color: #fff !important; }
[data-bs-theme="dark"] body.colorblind-mode .table-rekap .badge.bg-danger { background-color: #fff !important; color: #000 !important; border-color: #fff !important; }
</style>
@endsection

@section('content')
<div id="page-loader">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 0.3em;"></div>
    <h2 class="text-primary mb-1">Memuat Data Absensi...</h2>
    <p class="text-muted">Mohon tunggu sebentar, sistem sedang menyusun rekap kehadiran kelas.</p>
</div>

<div class="row g-3 align-items-center mb-4">
    <div class="col-12 col-md">
        <h2 class="page-title"><i class="fas fa-chart-line me-2"></i>{{ $pages }}</h2>
    </div>
    <div class="col-12 col-md-auto ms-md-auto d-print-none">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="colorblindToggle">
            <label class="form-check-label fw-bold" for="colorblindToggle">Mode Aksesibilitas</label>
        </div>
    </div>
</div>

<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <form action="{{ route('akademik.rekap-absensi-render') }}" method="GET" id="formFilterRekap">
            <div class="row align-items-end">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label required">Pilih Mata Kuliah Seluruh Kampus</label>
                    <select name="mata_kuliah_id" class="form-select" required onchange="gantiMatkul()">
                        <option value="">-- Silakan Pilih Matkul Terlebih Dahulu --</option>
                        @foreach($mataKuliahList->sortByDesc('semester') as $mk)
                            <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->name }} (Semester {{ $mk->semester ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-primary">Filter Berdasarkan Kelas</label>
                    <select name="kelompok" id="dropdownKelompok" class="form-select border-primary" onchange="showLoader(); this.form.submit()" {{ !request('mata_kuliah_id') ? 'disabled' : '' }}>
                        <option value="all" {{ $kelompokPilih == 'all' ? 'selected' : '' }}>Semua Kelas (Keseluruhan)</option>
                        @if(isset($groupedMahasiswa))
                            @foreach(collect($groupedMahasiswa)->sortKeysDesc() as $kel => $mhs)
                                <option value="{{ $kel }}" {{ $kelompokPilih == $kel ? 'selected' : '' }}>
                                    {{ $kel }} ({{ count($mhs) }} Mahasantri)
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="col-12 mt-3 border-top pt-3">
                    <label class="form-check form-switch mb-0">
                        <input type="hidden" name="krs_only" value="0">
                        <input class="form-check-input" type="checkbox" name="krs_only" value="1" onchange="showLoader(); this.form.submit()" {{ !isset($krs_only) || $krs_only ? 'checked' : '' }}>
                        <span class="form-check-label text-primary fw-bold">Hanya Tampilkan Mahasantri KRS Aktif (Exclude Draft)</span>
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedMatkul)
    <div class="card mb-3 bg-primary-lt border-primary">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 text-primary"><i class="fas fa-book-open me-2"></i>{{ $selectedMatkul->name }} <span class="badge bg-primary-lt ms-2">Semester {{ $selectedMatkul->semester ?? '-' }}</span></h3>
                <div class="text-muted small">Keterangan: <strong>H</strong> (Hadir), <strong>S</strong> (Sakit), <strong>I</strong> (Izin), <strong>A</strong> (Alpa)</div>
            </div>
            <div class="mt-3 mt-md-0 d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <a href="{{ route('akademik.rekap-absensi-export', ['mata_kuliah_id' => $selectedMatkul->id, 'kelompok' => $kelompokPilih, 'krs_only' => isset($krs_only) && $krs_only ? 1 : 0]) }}" class="btn btn-success shadow-sm w-100 w-sm-auto" onclick="showExportLoader()">
                    <i class="fas fa-file-excel me-2"></i> Download Excel
                </a>
                <button class="btn btn-info shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#exportSemesterModal">
                    <i class="fas fa-layer-group me-2"></i> Export Semester
                </button>
                <button class="btn btn-warning shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i> Upload Excel
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
                    <a href="#tab-rekap-{{ $tabIndex }}" class="nav-link {{ $tabIndex == 0 ? 'active' : '' }}" data-bs-toggle="tab">
                        <i class="fas fa-users me-2"></i> {{ $kelompok }} <span class="badge bg-white text-primary ms-2">{{ count($rekapData) }}</span>
                    </a>
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
                        <table class="table table-vcenter table-bordered table-striped table-hover table-rekap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th rowspan="2" class="w-1 text-center">No</th>
                                    <th rowspan="2" class="nama-mhs">Mahasantri</th>
                                    <th colspan="14">Pertemuan Ke-</th>
                                    <th rowspan="2">% Hadir</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 14; $i++)
                                        <th style="min-width: 40px;" class="text-center">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($rekapData as $data)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    
                                    <td class="nama-mhs">
                                        <div class="fw-bold" style="white-space: normal; word-break: break-word; min-width: 140px;">{{ $data['mhs']->name }}</div>
                                        <div class="mt-1"><span class="badge bg-secondary text-white">{{ $data['mhs']->nim }}</span></div>
                                    </td>
                                    
                                    @for($i = 1; $i <= 14; $i++)
                                        @php $status = $data['pertemuan'][$i]; @endphp
                                        <td class="text-center">
                                            @if($status == 'H') 
                                                <span class="badge bg-success text-white" style="width: 25px;">H</span>
                                            @elseif($status == 'S') 
                                                <span class="badge bg-info text-white" style="width: 25px;">S</span>
                                            @elseif($status == 'I') 
                                                <span class="badge bg-warning text-white" style="width: 25px;">I</span>
                                            @elseif($status == 'A') 
                                                <span class="badge bg-danger text-white" style="width: 25px;">A</span>
                                            @else 
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endfor

                                    <td>
                                        @php $color = $data['persentase'] >= 85 ? 'success' : ($data['persentase'] >= 60 ? 'warning' : 'danger'); @endphp
                                        <span class="badge bg-{{ $color }} text-white">{{ $data['persentase'] }}%</span>
                                    </td>
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
    @else
    <div class="alert alert-info">Belum ada data kehadiran untuk kriteria tersebut.</div>
    @endif

@endif

<div class="modal modal-blur fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Rekap Excel (Master Admin)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('akademik.rekap-absensi-import') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
          @csrf
          <input type="hidden" name="mata_kuliah_id" value="{{ $selectedMatkul->id ?? '' }}">
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">File Excel Absensi</label>
                <input type="file" class="form-control" name="file_excel" accept=".xlsx, .xls" required>
            </div>
            <div class="mb-3">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="sync_nilai" value="1" checked>
                  <span class="form-check-label fw-bold">Sinkronkan ke Nilai Keaktifan</span>
                </label>
                <small class="text-muted d-block mt-1">Sistem akan otomatis mengkreditkan nilai ini ke akun Dosen Utama dari Matkul tersebut.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary ms-auto"><i class="fas fa-upload me-2"></i> Proses Upload</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="exportSemesterModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i> Export Rekap 1 Kelas (Semesteran)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('akademik.rekap-absensi-export-semester') }}" method="GET" onsubmit="showExportLoader()">
          <div class="modal-body">
            <div class="alert alert-info">
                Fitur ini akan mengekspor <strong>semua Mata Kuliah</strong> dalam satu Semester untuk <strong>1 Kelas</strong> tertentu. (Setiap Matkul akan dibuatkan Sheet tersendiri).
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Pilih Semester</label>
                <select name="semester" id="semesterDropdown" class="form-select" required onchange="filterKelasBySemester()">
                    <option value="">-- Pilih Semester --</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}">Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Pilih Kelas</label>
                <select name="kelas_name" id="semesterKelasDropdown" class="form-select" required disabled>
                    <option value="">-- Pilih Semester Terlebih Dahulu --</option>
                    {{-- Opsi kelas akan di-generate via JavaScript --}}
                </select>
                <script>
                    window.allClasses = [
                        @if(isset($kelasList))
                            @foreach($kelasList as $kls)
                                "{{ $kls->name }}",
                            @endforeach
                        @endif
                    ];
                </script>
            </div>

            <div class="mb-3">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="krs_only" value="1" checked>
                  <span class="form-check-label fw-bold text-primary">Hanya Tampilkan Mahasantri KRS Aktif (Exclude Draft)</span>
                </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-info ms-auto"><i class="fas fa-download me-2"></i> Download File</button>
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
        let dropdownKelompok = document.getElementById('dropdownKelompok');
        if(dropdownKelompok) { dropdownKelompok.removeAttribute('name'); }
        document.getElementById('formFilterRekap').submit();
    }
    function showExportLoader() { showLoader(); setTimeout(() => { document.getElementById('page-loader').style.display = 'none'; }, 2500); }
    
    // FUNGSI SAKTI: Filter Kelas berdasarkan Semester (Untuk Modal Export)
    function filterKelasBySemester() {
        const semester = document.getElementById('semesterDropdown').value;
        const kelasDropdown = document.getElementById('semesterKelasDropdown');
        
        kelasDropdown.innerHTML = '<option value="">-- Pilih Kelas --</option>'; // Reset opsi
        
        if (!semester) {
            kelasDropdown.disabled = true;
            kelasDropdown.innerHTML = '<option value="">-- Pilih Semester Terlebih Dahulu --</option>';
            return;
        }

        kelasDropdown.disabled = false;

        // Populate ulang pilihan dropdown dari daftar kelas yang disuntikkan via PHP
        window.allClasses.forEach(function(kelasName) {
            const option = document.createElement('option');
            option.value = kelasName;
            option.text = kelasName;
            kelasDropdown.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('colorblindToggle');
        if (localStorage.getItem('colorblindMode') === 'true') { document.body.classList.add('colorblind-mode'); toggle.checked = true; }
        toggle.addEventListener('change', function() {
            if (this.checked) { document.body.classList.add('colorblind-mode'); localStorage.setItem('colorblindMode', 'true'); } 
            else { document.body.classList.remove('colorblind-mode'); localStorage.setItem('colorblindMode', 'false'); }
        });
    });
</script>
@endsection