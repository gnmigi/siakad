@extends('central.backpage.core-backpage-dosen')

@section('custom-css')
<style>
[data-bs-theme="dark"] .form-label { color: #f8fafc !important; }
[data-bs-theme="dark"] .badge-nim { background-color: #334155 !important; color: #f8fafc !important; }
/* Agar teks di toggle tidak terpotong di HP */
.form-check-label { white-space: normal; line-height: 1.4; }

/* TAMBAHAN UNTUK MOBILE RESPONSIVE TAMPILAN ABSEN */
.nama-mhs-wrap {
    white-space: normal !important;
    word-break: break-word; /* Paksa teks turun kalau mentok */
    min-width: 120px;
}
.select-absen-mobile {
    min-width: 110px; /* Kunci lebar dropdown agar tidak kegencet */
}

/* ==========================================================
   WARNA-WARNI DROPDOWN ABSENSI (SEPERTI BADGE)
   ========================================================== */
select.absen-hadir { background-color: #2fb344 !important; color: white !important; border-color: #2fb344 !important; }
select.absen-sakit { background-color: #206bc4 !important; color: white !important; border-color: #206bc4 !important; }
select.absen-izin { background-color: #f59f00 !important; color: white !important; border-color: #f59f00 !important; }
select.absen-alpa { background-color: #d63939 !important; color: white !important; border-color: #d63939 !important; }
select.absen-kosong { background-color: #ffffff; color: #1e293b; } /* Default kalau belum milih */

/* Biar pas dropdown-nya diklik (list optionnya), warnanya tetap putih bersih dan tulisannya jelas */
select.select-absen-mobile option { background-color: #ffffff; color: #1e293b; font-weight: bold; }
[data-bs-theme="dark"] select.select-absen-mobile option { background-color: #1e293b; color: #f8fafc; }

/* ==========================================
   CSS UNTUK MODE BUTA WARNA (AKSESIBILITAS)
   ========================================== */
body.colorblind-mode select.absen-hadir { background-color: transparent !important; color: #000 !important; border: 2px solid #000 !important; }
body.colorblind-mode select.absen-sakit { background-color: transparent !important; color: #000 !important; border: 2px dashed #000 !important; }
body.colorblind-mode select.absen-izin { background-color: transparent !important; color: #000 !important; border: 2px dotted #000 !important; }
body.colorblind-mode select.absen-alpa { background-color: #000 !important; color: #fff !important; border: 2px solid #000 !important; }

/* Dukungan untuk Dark Mode saat Mode Buta Warna aktif */
[data-bs-theme="dark"] body.colorblind-mode select.absen-hadir, 
[data-bs-theme="dark"] body.colorblind-mode select.absen-sakit, 
[data-bs-theme="dark"] body.colorblind-mode select.absen-izin { color: #fff !important; border-color: #fff !important; }
[data-bs-theme="dark"] body.colorblind-mode select.absen-alpa { background-color: #fff !important; color: #000 !important; border-color: #fff !important; }


/* ==========================================================
   TOMBOL SIMPAN MELAYANG UNIVERSAL (HP, TABLET, & PC/WINDOWS)
   ========================================================== */
.action-bar-sticky {
    position: sticky;
    bottom: 0;
    z-index: 1020;
    background-color: #ffffff !important; 
    border-top: 2px solid #e2e8f0 !important; 
    box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.08); 
    margin-left: -1.25rem; /* Menyesuaikan padding bawaan card Tabler */
    margin-right: -1.25rem;
    margin-bottom: -1.25rem; /* Menutup ruang kosong di bawah card */
    padding: 1rem 1.25rem !important;
}

/* Penyesuaian Warna Teks Toggle */
.action-bar-sticky .text-azure-dynamic {
    color: #1e293b !important;
}

/* ==========================================================
   PENYESUAIAN UNTUK MODE GELAP (DARK MODE)
   ========================================================== */
[data-bs-theme="dark"] .action-bar-sticky {
    background-color: #1e293b !important; 
    border-top-color: #334155 !important;
    box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.6);
}

[data-bs-theme="dark"] .action-bar-sticky .text-azure-dynamic {
    color: #f8fafc !important;
}
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-start align-items-md-center">
            <div class="col-12 col-md">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dosen.absensi-kelas.index') }}">Daftar Mata Kuliah</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Absensi Kelas</li>
                    </ol>
                </nav>
                <div class="page-pretitle">{{ $menus }}</div>
                <h2 class="page-title"><i class="fas fa-user-check me-2"></i>{{ $pages }}</h2>
            </div>
            
            <div class="col-12 col-md-auto ms-md-auto d-print-none">
                <div class="d-flex flex-column align-items-start align-items-md-end gap-3">
                    <div class="form-check form-switch mb-0 mt-2 mt-md-0">
                        <input class="form-check-input" type="checkbox" id="colorblindToggle">
                        <label class="form-check-label fw-bold" for="colorblindToggle">Mode Aksesibilitas</label>
                    </div>
                    
                    <div class="btn-list flex-column flex-sm-row w-100">
                        <a href="{{ route('dosen.absensi-kelas.rekap', ['mata_kuliah_id' => $mataKuliah->id]) }}" class="btn btn-info shadow-sm w-100 w-sm-auto">
                            <i class="fas fa-chart-line me-1"></i> Lihat Rekap
                        </a>
                        <a href="{{ route('dosen.absensi-kelas.index') }}" class="btn btn-outline-secondary shadow-sm w-100 w-sm-auto">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible"><div class="d-flex"><div><i class="fas fa-check-circle me-2"></i></div><div>{{ session('success') }}</div></div><a class="btn-close" data-bs-dismiss="alert"></a></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible"><div class="d-flex"><div><i class="fas fa-exclamation-triangle me-2"></i></div><div>{{ session('error') }}</div></div><a class="btn-close" data-bs-dismiss="alert"></a></div>
        @endif

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary-lt d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <h3 class="card-title mb-0"><strong>Mata Kuliah:</strong> {{ $mataKuliah->name }}</h3>
                <span class="badge bg-primary text-white fs-5">Semester {{ $mataKuliah->semester ?? '-' }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Pilih Pertemuan</label>
                        <select class="form-select" onchange="window.location.href='?pertemuan_ke='+this.value+'&krs_only={{ isset($krs_only) && $krs_only ? 1 : 0 }}'">
                             @for($i = 1; $i <= 14; $i++)
                                @php
                                    $savedGroups = $meetingGroups[$i] ?? [];
                                    $savedCount = count($savedGroups);
                                    $statusText = '';
                                    if ($savedCount > 0) {
                                        if (isset($totalGroups) && $totalGroups > 1) {
                                            if ($savedCount >= $totalGroups) {
                                                $statusText = ' (Sudah Semua)';
                                            } else {
                                                // Clean up kelompok names (remove parenthesized semester info)
                                                $cleanSaved = array_map(function($g) {
                                                    return explode(' (', $g)[0];
                                                }, $savedGroups);
                                                $statusText = ' (Sudah: ' . implode(', ', $cleanSaved) . ')';
                                            }
                                        } else {
                                            $statusText = ' (Sudah Diabsen)';
                                        }
                                    }
                                @endphp
                                <option value="{{ $i }}" {{ $pertemuan_ke == $i ? 'selected' : '' }}>
                                    Pertemuan ke - {{ $i }}{{ $statusText }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    @if($groupedMahasiswa->isNotEmpty())
                    <div class="col-md-6">
                        <label class="form-label required text-primary">Pilih Kelas</label>
                        <select class="form-select border-primary text-primary fw-bold" id="kelasSelector">
                            @php $dropdownIndex = 0; @endphp
                            @foreach(collect($groupedMahasiswa)->sortKeysDesc() as $kelompok => $mahasiswas)
                                <option value="kelas-{{ $dropdownIndex }}">
                                    {{ $kelompok }} ({{ count($mahasiswas) }} Mahasantri)
                                </option>
                                @php $dropdownIndex++; @endphp
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-12 mt-3 pt-3 border-top">
                        <label class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" onchange="window.location.href='?pertemuan_ke={{ $pertemuan_ke }}&krs_only=' + (this.checked ? '1' : '0')" {{ isset($krs_only) && $krs_only ? 'checked' : '' }}>
                            <span class="form-check-label text-primary fw-bold">Hanya Tampilkan Mahasantri KRS Aktif</span>
                        </label>
                        <small class="text-muted d-block mt-1">Jika dinyalakan, mahasantri draft KRS tidak muncul.</small>
                    </div>
                </div>
            </div>
        </div>

        @if($groupedMahasiswa->isNotEmpty())
        <div class="card mb-4 shadow-sm" id="kelasContainers">
            @php $contentIndex = 0; @endphp
            @foreach(collect($groupedMahasiswa)->sortKeysDesc() as $kelompok => $mahasiswas)
            
            <div class="kelas-form-container" id="kelas-{{ $contentIndex }}" style="{{ $contentIndex == 0 ? '' : 'display: none;' }}">
                <div class="card-header border-bottom">
                    <h3 class="card-title text-primary"><i class="fas fa-users me-2"></i> Absensi: <strong>{{ $kelompok }}</strong></h3>
                </div>

                <form action="{{ route('dosen.absensi-kelas.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mata_kuliah_id" value="{{ $mataKuliah->id }}">
                    <input type="hidden" name="pertemuan_ke" value="{{ $pertemuan_ke }}">
                    <input type="hidden" name="kelompok" value="{{ $kelompok }}">

                    @php $pertemuanTab = $pertemuans[$kelompok] ?? null; @endphp

                    <div class="p-3 border-bottom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Tanggal Pertemuan Kelas Ini</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $pertemuanTab->tanggal ?? date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Topik / Materi</label>
                                <input type="text" name="materi" class="form-control" placeholder="Materi khusus kelas ini" value="{{ old('materi', $pertemuanTab->materi ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="w-1 text-center">No</th>
                                    <th>Mahasantri</th>
                                    <th class="text-center" style="width: 130px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswas as $index => $mhs)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold nama-mhs-wrap">{{ $mhs->name }}</div>
                                        <div class="mt-1"><span class="badge bg-secondary text-white badge-nim">{{ $mhs->nim }}</span></div>
                                    </td>
                                    <td>
                                        <select name="absensi[{{ $mhs->id }}]" class="form-select form-select-sm select-absen-mobile fw-bold status-select" onchange="updateSelectColor(this)">
                                            <option value="" disabled {{ !$mhs->status_absen ? 'selected' : '' }}>Pilih...</option>
                                            <option value="Hadir" {{ $mhs->status_absen == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="Sakit" {{ $mhs->status_absen == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="Izin" {{ $mhs->status_absen == 'Izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="Alpa" {{ $mhs->status_absen == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 action-bar-sticky">
                        <label class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="sync_nilai" value="1" checked>
                            <span class="form-check-label fw-bold text-azure-dynamic">Sinkronkan Kehadiran ke Nilai Keaktifan</span>
                        </label>
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">
                            <i class="fas fa-save me-2"></i> Simpan Absen Kelas Ini
                        </button>
                    </div>
                </form>
            </div>
            
            @php $contentIndex++; @endphp
            @endforeach
        </div>
        @else
        <div class="card mb-3">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-user-slash fa-3x mb-3"></i>
                <h4>Tidak ada data mahasantri.</h4>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('custom-js')
<script>
    // 1. Fungsi Ganti Kelas Form Container dengan Presistensi LocalStorage
    document.addEventListener('DOMContentLoaded', function() {
        const kelasSelector = document.getElementById('kelasSelector');
        const mataKuliahId = "{{ $mataKuliah->id }}";
        const storageKey = 'selected_kelas_' + mataKuliahId;

        if(kelasSelector) {
            // Restore from localStorage if exists
            const savedKelas = localStorage.getItem(storageKey);
            if (savedKelas) {
                const targetOption = kelasSelector.querySelector(`option[value="${savedKelas}"]`);
                if (targetOption) {
                    kelasSelector.value = savedKelas;
                    document.querySelectorAll('.kelas-form-container').forEach(el => el.style.display = 'none');
                    const target = document.getElementById(savedKelas);
                    if(target) target.style.display = 'block';
                }
            }

            kelasSelector.addEventListener('change', function() {
                document.querySelectorAll('.kelas-form-container').forEach(el => el.style.display = 'none');
                const target = document.getElementById(this.value);
                if(target) target.style.display = 'block';
                
                // Save to localStorage
                localStorage.setItem(storageKey, this.value);
            });
        }
    });

    // 2. Fungsi Ubah Warna Dropdown Select (Dipanggil tiap ada perubahan)
    function updateSelectColor(selectElement) {
        // Hapus semua class warna sebelumnya
        selectElement.classList.remove('absen-hadir', 'absen-sakit', 'absen-izin', 'absen-alpa', 'absen-kosong');
        
        const value = selectElement.value;
        if (value === 'Hadir') {
            selectElement.classList.add('absen-hadir');
        } else if (value === 'Sakit') {
            selectElement.classList.add('absen-sakit');
        } else if (value === 'Izin') {
            selectElement.classList.add('absen-izin');
        } else if (value === 'Alpa') {
            selectElement.classList.add('absen-alpa');
        } else {
            selectElement.classList.add('absen-kosong');
        }
    }

    // 3. Setel warna awal saat halaman baru di-load (biar history warna gak hilang)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-select').forEach(function(selectElement) {
            updateSelectColor(selectElement);
        });
    });

    // 4. Mode Aksesibilitas (Buta Warna)
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('colorblindToggle');
        if (toggle) {
            // Cek status dari LocalStorage
            if (localStorage.getItem('colorblindMode') === 'true') { 
                document.body.classList.add('colorblind-mode'); 
                toggle.checked = true; 
            }
            
            // Simpan pas diklik
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