@extends('central.backpage.core-backpage-dosen')

@section('custom-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Styling to make Select2 look native with Tabler Bootstrap 5 Theme */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid var(--tblr-border-color) !important;
        font-size: 0.875rem !important;
        min-height: 40px !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 4px !important;
        background-color: var(--tblr-bg-surface) !important;
        color: var(--tblr-body-color) !important;
        box-shadow: none !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: var(--tblr-body-color) !important;
        padding-left: 12px !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: var(--tblr-border-color) !important;
        background-color: var(--tblr-bg-surface) !important;
        z-index: 1060 !important;
        box-shadow: 0 6px 12px rgba(0,0,0,.05) !important;
    }
    .select2-container--bootstrap-5 .select2-search__field {
        background-color: var(--tblr-bg-surface) !important;
        border: 1px solid var(--tblr-border-color) !important;
        color: var(--tblr-body-color) !important;
        border-radius: 4px !important;
    }
    .select2-container--bootstrap-5 .select2-results__option {
        color: var(--tblr-body-color) !important;
        font-size: 0.875rem !important;
        padding: 8px 12px !important;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: var(--tblr-primary) !important;
        color: #fff !important;
    }
    .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
        background-color: var(--tblr-primary-lt) !important;
        color: var(--tblr-primary) !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear {
        margin-right: 10px !important;
        color: var(--tblr-muted) !important;
    }
    .select2-container--bootstrap-5 .select2-selection__arrow {
        border-color: var(--tblr-muted) transparent transparent !important;
    }
    /* Dark Mode specific overrides */
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
        background-color: #1b2434 !important;
        border-color: #2b3c58 !important;
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown {
        background-color: #1b2434 !important;
        border-color: #2b3c58 !important;
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-search__field {
        background-color: #151d2a !important;
        border-color: #2b3c58 !important;
        color: #fff !important;
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #206bc4 !important;
        color: #fff !important;
    }
    [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
        background-color: rgba(32, 107, 196, 0.15) !important;
        color: #58a6ff !important;
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Breadcrumb Navigation -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb breadcrumb-arrows">
                        <li class="breadcrumb-item"><a href="{{ route($spref.'mata-kuliah-render') }}">Mata Kuliah</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Input Nilai</li>
                    </ol>
                </nav>
                <div class="page-pretitle">
                    {{ $menus }}
                </div>
                <h2 class="page-title">
                    <i class="fas fa-edit me-2"></i>{{ $pages }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route($spref.'mata-kuliah-render') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Kembali</span>
                    </a>
                </div>
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
                        <i class="fas fa-check-circle alert-icon"></i>
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
                        <i class="fas fa-exclamation-triangle alert-icon"></i>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <form method="POST" action="{{ route($spref.'nilai.input-handle') }}" id="nilaiForm">
            @csrf
            
            <!-- Course and Student Selection -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-book me-2"></i>Mata Kuliah
                            </h3>
                        </div>
                        <div class="card-body">
                            <label class="form-label required">Pilih Mata Kuliah</label>
                            <select class="form-select form-select-lg" name="mata_kuliah_id" id="mata_kuliah_id" required {{ $existingNilai ? 'disabled' : '' }}>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($mataKuliah as $mk)
                                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                        {{ $mk->name }} ({{ $mk->bsks }} SKS) - Sem. {{ $mk->semester ?? '-' }} - {{ $mk->programStudi->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if($existingNilai)
                                <input type="hidden" name="mata_kuliah_id" value="{{ request('mata_kuliah_id') }}">
                            @endif
                            @error('mata_kuliah_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-graduate me-2"></i>Mahasiswa
                            </h3>
                        </div>
                        <div class="card-body">
                            <label class="form-label required">Cari Mahasiswa</label>
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-lg" id="mahasiswa_search" 
                                       placeholder="Pilih mata kuliah terlebih dahulu..." autocomplete="off" 
                                       {{ $existingNilai ? 'disabled' : '' }} disabled>
                                <select class="form-select d-none" name="mahasiswa_id" id="mahasiswa_id" required {{ $existingNilai ? 'disabled' : '' }}>
                                    <option value="">Pilih Mahasiswa</option>
                                    @if($selectedMahasiswa)
                                        <option value="{{ $selectedMahasiswa->id }}" selected>
                                            {{ $selectedMahasiswa->name }} ({{ $selectedMahasiswa->numb_nim }})
                                        </option>
                                    @endif
                                </select>
                                <div id="mahasiswa_dropdown" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto; display: none;">
                                    <!-- Search results will be populated here -->
                                </div>
                            </div>
                            @if($existingNilai)
                                <input type="hidden" name="mahasiswa_id" value="{{ request('mahasiswa_id') }}">
                            @endif
                            @error('mahasiswa_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Details Card -->
            <div class="row mb-4" id="student_details_row" style="display: none;">
                <div class="col-12">
                    <div class="card bg-azure-lt border-azure">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg bg-azure text-white">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="text-muted small">Nama Mahasiswa</div>
                                            <div class="fw-bold" id="student_name">-</div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="text-muted small">NIM</div>
                                            <div class="fw-bold" id="student_nim">-</div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="text-muted small">Program Studi</div>
                                            <div class="fw-bold" id="student_prodi">-</div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="text-muted small">Kelas</div>
                                            <div class="fw-bold" id="student_kelas">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Input -->
            <div class="row mb-4" id="grade-input" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calculator me-2"></i>Input Nilai
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Grade Input Cards -->
                            <div class="row g-3">
                                @foreach($bobotNilaiSettings as $setting)
                                <div class="col-6 col-md-3">
                                    <div class="card grade-input-card">
                                        <div class="card-body text-center p-3">
                                            <div class="avatar avatar-md mb-2" style="background-color: {{ $setting->color }}20; color: {{ $setting->color }};">
                                                <i class="fas fa-{{ $setting->component_key == 'tugas' ? 'tasks' : ($setting->component_key == 'quiz' ? 'question-circle' : ($setting->component_key == 'uts' ? 'file-alt' : 'graduation-cap')) }}"></i>
                                            </div>
                                            <div class="h6 mb-1">{{ $setting->component_name }}</div>
                                            <div class="text-muted small mb-2">Bobot: {{ $setting->weight }}%</div>
                                            <input type="number" class="form-control form-control-lg text-center fw-bold" 
                                                   name="{{ $setting->component_key }}" 
                                                   min="0" max="100" step="0.01"
                                                   value="{{ old($setting->component_key, $existingNilai->{$setting->component_key} ?? '') }}" 
                                                   placeholder="0-100" 
                                                   oninput="if(this.value > 100) this.value = 100; calculateFinalGrade();">
                                            @error($setting->component_key)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Final Grade Preview -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-success-lt border-success" id="final-grade-preview" style="display: none;">
                                        <div class="card-body text-center">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <div class="h3 mb-0" id="final-grade-number">0</div>
                                                    <div class="text-muted">Nilai Akhir</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="h3 mb-0" id="final-grade-letter">-</div>
                                                    <div class="text-muted">Huruf</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="h3 mb-0" id="final-grade-status">-</div>
                                                    <div class="text-muted">Status</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex flex-column flex-md-row gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                            <i class="fas fa-save me-2"></i>
                                            Simpan Nilai
                                        </button>
                                        <button type="reset" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-undo me-2"></i>
                                            Reset
                                        </button>
                                        @if($selectedMahasiswa)
                                        <a href="{{ route('mahasiswa.khs.index') }}" target="_blank" class="btn btn-info btn-lg">
                                            <i class="fas fa-eye me-2"></i>
                                            <span class="d-none d-md-inline">Cek Nilai</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Help Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Panduan Input Nilai
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <h5>Komponen Penilaian:</h5>
                                <ul class="list-unstyled">
                                    @foreach($bobotNilaiSettings as $setting)
                                        <li class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2" style="background-color: {{ $setting->color }}20; color: {{ $setting->color }};">
                                                    <i class="fas fa-{{ $setting->component_key == 'tugas' ? 'tasks' : ($setting->component_key == 'quiz' ? 'question-circle' : ($setting->component_key == 'uts' ? 'file-alt' : 'graduation-cap')) }}"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $setting->component_name }} ({{ $setting->weight }}%)</strong><br>
                                                    <small class="text-muted">Nilai {{ strtolower($setting->component_name) }} yang diberikan selama semester</small>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <h5>Konversi Nilai:</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Rentang</th>
                                                <th>Huruf</th>
                                                <th>GPA</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $gradeScales = \App\Models\Akademik\GradeScaleSetting::where('is_active', true)
                                                    ->orderBy('order')
                                                    ->get();
                                                
                                                $gradeDescriptions = [
                                                    'A' => 'Sangat Baik',
                                                    'A-' => 'Baik Sekali', 
                                                    'B+' => 'Baik',
                                                    'B' => 'Cukup Baik',
                                                    'B-' => 'Cukup',
                                                    'C+' => 'Kurang Baik',
                                                    'C' => 'Kurang',
                                                    'C-' => 'Kurang Sekali',
                                                    'D+' => 'Buruk',
                                                    'D' => 'Buruk Sekali',
                                                    'E' => 'Gagal'
                                                ];
                                                
                                                $badgeColors = [
                                                    'A' => 'success', 'A-' => 'success',
                                                    'B+' => 'info', 'B' => 'info', 'B-' => 'primary',
                                                    'C+' => 'warning', 'C' => 'warning', 'C-' => 'orange',
                                                    'D+' => 'danger', 'D' => 'danger',
                                                    'E' => 'dark'
                                                ];
                                            @endphp
                                            
                                            @foreach($gradeScales as $scale)
                                                <tr>
                                                    <td>{{ number_format($scale->min_score, 0) }}-{{ number_format($scale->max_score, 0) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $badgeColors[$scale->grade_letter] ?? 'secondary' }}">
                                                            {{ $scale->grade_letter }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($scale->grade_point, 2) }}</td>
                                                    <td>{{ $gradeDescriptions[$scale->grade_letter] ?? 'Unknown' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Skala nilai dapat dikonfigurasi oleh admin melalui menu Bobot Nilai
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-css')
<style>
/* Mobile-First Responsive Design */
.grade-input-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.grade-input-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.grade-input-card.has-value {
    border-color: var(--tblr-success);
    background-color: var(--tblr-success-lt);
}

/* Step Progress Styling */
.steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 60%;
    right: -40%;
    height: 2px;
    background-color: var(--tblr-border-color);
    z-index: -1;
}

.step-item.active::after {
    background-color: var(--tblr-lime);
}

.step-item.completed::after {
    background-color: var(--tblr-lime);
}

.step-item .h4 {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--tblr-border-color);
    color: var(--tblr-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-item.active .h4 {
    background-color: var(--tblr-lime);
    color: white;
}

.step-item.completed .h4 {
    background-color: var(--tblr-lime);
    color: white;
}

/* Enhanced Form Controls */
.form-control-lg, .form-select-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

/* Searchable Dropdown */
#mahasiswa_dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    border: 1px solid var(--tblr-border-color);
    border-radius: 0.375rem;
    background-color: var(--tblr-bg-surface);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.dropdown-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    color: var(--tblr-body-color);
    display: block;
    line-height: 1.4;
    transition: background-color 0.15s ease;
}

.dropdown-item:hover {
    background-color: var(--tblr-hover-bg);
    color: var(--tblr-body-color);
}

.dropdown-item.no-results {
    color: var(--tblr-muted);
    cursor: default;
    font-style: italic;
}

.dropdown-item.no-results:hover {
    background-color: transparent;
}

.mahasiswa-info {
    display: block;
}

.mahasiswa-name {
    font-weight: 600;
    color: inherit;
}

.mahasiswa-details {
    font-size: 0.875rem;
    color: var(--tblr-muted);
    margin-top: 0.25rem;
}

/* Breadcrumb Styling */
.breadcrumb-arrows .breadcrumb-item + .breadcrumb-item::before {
    content: "→";
    color: var(--tblr-muted);
}

/* Alert Icons */
.alert-icon {
    width: 24px;
    height: 24px;
    margin-right: 0.5rem;
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .steps .step-item .h4 {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
    
    .grade-input-card .card-body {
        padding: 1rem;
    }
    
    .avatar-md {
        width: 2rem;
        height: 2rem;
    }
}

@media (max-width: 576px) {
    .container-xl {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .btn-list .btn {
        padding: 0.5rem 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Dark Mode Support - Enhanced */
[data-bs-theme="dark"] .grade-input-card {
    background-color: var(--tblr-dark-bg-surface);
    border-color: var(--tblr-dark-border-color);
}

[data-bs-theme="dark"] .grade-input-card:hover {
    box-shadow: 0 4px 12px rgba(255,255,255,0.1);
}

/* Fix untuk dropdown mahasiswa di dark mode - SOLUSI KUAT */
[data-bs-theme="dark"] #mahasiswa_dropdown,
html[data-bs-theme="dark"] #mahasiswa_dropdown,
body[data-bs-theme="dark"] #mahasiswa_dropdown,
.dark #mahasiswa_dropdown {
    background-color: #1e293b !important;
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    border-color: #334155 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.8), 0 2px 4px -1px rgba(0, 0, 0, 0.6) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

[data-bs-theme="dark"] .dropdown-item,
html[data-bs-theme="dark"] .dropdown-item,
body[data-bs-theme="dark"] .dropdown-item,
.dark .dropdown-item {
    color: #f1f5f9 !important;
    background-color: transparent !important;
    background: transparent !important;
}

[data-bs-theme="dark"] .dropdown-item:hover,
html[data-bs-theme="dark"] .dropdown-item:hover,
body[data-bs-theme="dark"] .dropdown-item:hover,
.dark .dropdown-item:hover {
    background-color: #334155 !important;
    background: #334155 !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .dropdown-item.no-results,
html[data-bs-theme="dark"] .dropdown-item.no-results,
body[data-bs-theme="dark"] .dropdown-item.no-results,
.dark .dropdown-item.no-results {
    color: #94a3b8 !important;
    background-color: transparent !important;
    background: transparent !important;
}

[data-bs-theme="dark"] .dropdown-item.no-results:hover,
html[data-bs-theme="dark"] .dropdown-item.no-results:hover,
body[data-bs-theme="dark"] .dropdown-item.no-results:hover,
.dark .dropdown-item.no-results:hover {
    background-color: transparent !important;
    background: transparent !important;
}

/* Fix untuk mahasiswa details di dropdown - LEBIH SPESIFIK */
[data-bs-theme="dark"] .mahasiswa-name,
html[data-bs-theme="dark"] .mahasiswa-name,
body[data-bs-theme="dark"] .mahasiswa-name,
.dark .mahasiswa-name {
    color: #f1f5f9 !important;
}

[data-bs-theme="dark"] .mahasiswa-details,
html[data-bs-theme="dark"] .mahasiswa-details,  
body[data-bs-theme="dark"] .mahasiswa-details,
.dark .mahasiswa-details {
    color: #94a3b8 !important;
}

/* Fix untuk input search mahasiswa di dark mode */
[data-bs-theme="dark"] #mahasiswa_search {
    background-color: var(--tblr-dark-bg-surface) !important;
    border-color: var(--tblr-dark-border-color) !important;
    color: var(--tblr-dark-body-color) !important;
}

[data-bs-theme="dark"] #mahasiswa_search:focus {
    background-color: var(--tblr-dark-bg-surface) !important;
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25) !important;
}

[data-bs-theme="dark"] #mahasiswa_search::placeholder {
    color: var(--tblr-muted) !important;
    opacity: 0.7;
}

/* Fix untuk card background di dark mode */
[data-bs-theme="dark"] .card {
    background-color: var(--tblr-dark-bg-surface) !important;
    border-color: var(--tblr-dark-border-color) !important;
}

[data-bs-theme="dark"] .card-body {
    background-color: transparent !important;
}

/* Fix untuk form controls di dark mode */
[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}

[data-bs-theme="dark"] .form-control:focus,
[data-bs-theme="dark"] .form-select:focus {
    background-color: #1e293b !important;
    border-color: #3b82f6 !important;
    color: #f1f5f9 !important;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
}

/* Fix untuk select dropdown options di dark mode */
[data-bs-theme="dark"] .form-select option,
html[data-bs-theme="dark"] .form-select option,
body[data-bs-theme="dark"] .form-select option {
    background-color: #1e293b !important;
    color: #f1f5f9 !important;
}

/* Fix untuk select dropdown di dark mode */
[data-bs-theme="dark"] select,
html[data-bs-theme="dark"] select {
    background-color: #1e293b !important;
    color: #f1f5f9 !important;
}

/* Fix khusus untuk mata kuliah select */
[data-bs-theme="dark"] #mata_kuliah_id,
html[data-bs-theme="dark"] #mata_kuliah_id {
    background-color: #1e293b !important;
    color: #f1f5f9 !important;
    border-color: #334155 !important;
}

[data-bs-theme="dark"] #mata_kuliah_id option,
html[data-bs-theme="dark"] #mata_kuliah_id option {
    background-color: #1e293b !important;
    color: #f1f5f9 !important;
}

/* Enhanced badge colors with better contrast */
.badge {
    font-weight: 600 !important;
    color: white !important;
}

.badge.bg-success {
    background-color: #10b981 !important;
    color: white !important;
}

.badge.bg-info {
    background-color: #0ea5e9 !important;
    color: white !important;
}

.badge.bg-warning {
    background-color: #f59e0b !important;
    color: white !important;
}

.badge.bg-danger {
    background-color: #ef4444 !important;
    color: white !important;
}

.badge.bg-secondary {
    background-color: #6b7280 !important;
    color: white !important;
}

.badge.bg-orange {
    background-color: #f97316 !important;
    color: white !important;
}

.badge.bg-primary {
    background-color: #3b82f6 !important;
    color: white !important;
}

.badge.bg-dark {
    background-color: #1f2937 !important;
    color: white !important;
}

/* TAMBAHAN CSS UNTUK MEMASTIKAN DROPDOWN SOLID DI DARK MODE */

/* Force solid background untuk dropdown container */
[data-bs-theme="dark"] .dropdown-menu,
html[data-bs-theme="dark"] .dropdown-menu,
body[data-bs-theme="dark"] .dropdown-menu {
    background-color: #1e293b !important;
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    opacity: 1 !important;
}

/* Override untuk mahasiswa dropdown spesifik */
#mahasiswa_dropdown {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
}

[data-bs-theme="dark"] #mahasiswa_dropdown,
html[data-bs-theme="dark"] #mahasiswa_dropdown,
body[data-bs-theme="dark"] #mahasiswa_dropdown {
    background-color: #1e293b !important;
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    opacity: 1 !important;
    z-index: 9999 !important;
}

/* Pastikan tidak ada transparansi dari parent elements */
[data-bs-theme="dark"] .position-relative {
    background: transparent !important;
}

[data-bs-theme="dark"] .card-body {
    background: transparent !important;
}
</style>
@endsection

@section('custom-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mataKuliahSelect = document.getElementById('mata_kuliah_id');
    const mahasiswaSelect = document.getElementById('mahasiswa_id');
    const mahasiswaSearch = document.getElementById('mahasiswa_search');
    const mahasiswaDropdown = document.getElementById('mahasiswa_dropdown');
    
    let mahasiswaData = [];
    let selectedMahasiswa = null;
    let isInitialLoad = true;

    // Initialize Select2 for course dropdown
    $(mataKuliahSelect).select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Mata Kuliah --',
        allowClear: true
    }).on('change', function() {
        this.dispatchEvent(new Event('change', { bubbles: true }));
    });

    // Initialize with existing selected student if any
    @if($selectedMahasiswa && request('mata_kuliah_id'))
        console.log('Auto-selecting mahasiswa:', {!! json_encode($selectedMahasiswa->name) !!});
        
        // Set selected mahasiswa data
        selectedMahasiswa = {
            id: {{ $selectedMahasiswa->id }},
            name: {!! json_encode($selectedMahasiswa->name) !!},
            nim: {!! json_encode($selectedMahasiswa->numb_nim) !!},
            program_studi_name: {!! json_encode($selectedMahasiswa->programStudi->name ?? 'N/A') !!},
            kelas_name: {!! json_encode($selectedMahasiswa->kelas->name ?? 'N/A') !!}
        };
        
        // Auto-select mata kuliah with proper event triggering
        console.log('Setting mata kuliah value to:', {{ request('mata_kuliah_id') }});
        mataKuliahSelect.value = {{ request('mata_kuliah_id') }};
        $(mataKuliahSelect).trigger('change.select2');
        
        // Show student details and grade input immediately
        showStudentDetails(selectedMahasiswa);
        showGradeInput();
        
        // Auto-fill mahasiswa search input immediately - FORCE UPDATE
        mahasiswaSearch.disabled = false;
        mahasiswaSearch.value = selectedMahasiswa.name + ' (' + selectedMahasiswa.nim + ')';
        mahasiswaSearch.placeholder = 'Mahasiswa terpilih: ' + selectedMahasiswa.name;
        
        // Set hidden select value
        mahasiswaSelect.value = selectedMahasiswa.id;
        
        console.log('Mahasiswa auto-selected:', selectedMahasiswa.name + ' (' + selectedMahasiswa.nim + ')');
        
        // Force update the input field display and trigger selection - CRITICAL FIX
        setTimeout(function() {
            if (selectedMahasiswa && selectedMahasiswa.name && selectedMahasiswa.nim) {
                // Call selectMahasiswa to properly initialize everything
                selectMahasiswa(selectedMahasiswa);
                console.log('Force selected mahasiswa:', selectedMahasiswa.name + ' (' + selectedMahasiswa.nim + ')');
            } else {
                console.warn('selectedMahasiswa is null or missing properties in setTimeout');
            }
        }, 200);
        
        // Load mahasiswa data for this mata kuliah
        const initFetchUrl = {!! json_encode(route($spref.'nilai.mahasiswa-by-matkul')) !!};
        fetch(initFetchUrl + '?mata_kuliah_id=' + {{ request('mata_kuliah_id') }})
            .then(response => response.json())
            .then(data => {
                console.log('Mahasiswa data loaded:', data);
                if (data.success && data.mahasiswa && Array.isArray(data.mahasiswa)) {
                    // Filter out null/invalid data first with comprehensive validation
                    mahasiswaData = data.mahasiswa.filter(mahasiswa => {
                        if (!mahasiswa || typeof mahasiswa !== 'object') {
                            console.warn('Skipping null/invalid mahasiswa:', mahasiswa);
                            return false;
                        }
                        if (!mahasiswa.id || !mahasiswa.name) {
                            console.warn('Skipping mahasiswa with missing id/name:', mahasiswa);
                            return false;
                        }
                        // Additional validation for required properties
                        if (typeof mahasiswa.id !== 'number' && typeof mahasiswa.id !== 'string') {
                            console.warn('Skipping mahasiswa with invalid id type:', mahasiswa);
                            return false;
                        }
                        if (typeof mahasiswa.name !== 'string' || mahasiswa.name.trim() === '') {
                            console.warn('Skipping mahasiswa with invalid name:', mahasiswa);
                            return false;
                        }
                        return true;
                    });
                    
                    console.log('Filtered mahasiswa data:', mahasiswaData.length, 'valid entries');
                    
                    // Populate hidden select with valid mahasiswa options only
                    mahasiswaSelect.innerHTML = '<option value="">Pilih Mahasiswa</option>';
                    mahasiswaData.forEach(mahasiswa => {
                        try {
                            const option = document.createElement('option');
                            option.value = mahasiswa.id;
                            option.textContent = mahasiswa.name + ' (' + (mahasiswa.nim || 'N/A') + ')';
                            if (selectedMahasiswa && mahasiswa.id == selectedMahasiswa.id) {
                                option.selected = true;
                            }
                            mahasiswaSelect.appendChild(option);
                        } catch (error) {
                            console.error('Error creating option for mahasiswa:', mahasiswa, error);
                        }
                    });
                    
                    console.log('Loading existing grades...');
                    
                    // Update the search input with the selected student after data is loaded
                    if (selectedMahasiswa && selectedMahasiswa.id) {
                        mahasiswaSearch.value = selectedMahasiswa.name + ' (' + selectedMahasiswa.nim + ')';
                        mahasiswaSearch.placeholder = 'Mahasiswa terpilih: ' + selectedMahasiswa.name;
                        
                        // Load existing grades
                        loadExistingGrades({{ request('mata_kuliah_id') }}, selectedMahasiswa.id);
                    }
                } else {
                    console.error('Failed to load mahasiswa data:', data);
                    // Show error message to user
                    mahasiswaSearch.placeholder = 'Error loading mahasiswa data';
                    mahasiswaSearch.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error loading mahasiswa data:', error);
            });
    @endif

    // Course selection handler
    mataKuliahSelect.addEventListener('change', function() {
        const mataKuliahId = this.value;
        
        if (mataKuliahId) {
            // Enable student search
            mahasiswaSearch.disabled = false;
            
            // Only clear/reset search elements if NOT initial auto-select load
            const isAutoSelect = isInitialLoad && selectedMahasiswa && selectedMahasiswa.id;
            
            if (!isAutoSelect) {
                mahasiswaSearch.value = '';
                mahasiswaSearch.placeholder = 'Ketik nama atau NIM mahasiswa...';
                mahasiswaDropdown.style.display = 'none';
                mahasiswaSelect.value = '';
                mahasiswaData = [];
                selectedMahasiswa = null;
                hideStudentDetails();
                hideGradeInput();
            } else {
                mahasiswaSearch.placeholder = 'Ketik nama atau NIM mahasiswa...';
            }
            
            // Load students for selected course
            const fetchUrl = {!! json_encode(route($spref.'nilai.mahasiswa-by-matkul')) !!};
            fetch(fetchUrl + '?mata_kuliah_id=' + mataKuliahId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.mahasiswa && Array.isArray(data.mahasiswa)) {
                        // Filter out null/invalid data with comprehensive validation
                        mahasiswaData = data.mahasiswa.filter(mahasiswa => {
                            if (!mahasiswa || typeof mahasiswa !== 'object') {
                                console.warn('Skipping null/invalid mahasiswa:', mahasiswa);
                                return false;
                            }
                            if (!mahasiswa.id || !mahasiswa.name) {
                                console.warn('Skipping mahasiswa with missing id/name:', mahasiswa);
                                return false;
                            }
                            // Additional validation for required properties
                            if (typeof mahasiswa.id !== 'number' && typeof mahasiswa.id !== 'string') {
                                console.warn('Skipping mahasiswa with invalid id type:', mahasiswa);
                                return false;
                            }
                            if (typeof mahasiswa.name !== 'string' || mahasiswa.name.trim() === '') {
                                console.warn('Skipping mahasiswa with invalid name:', mahasiswa);
                                return false;
                            }
                            return true;
                        });
                        
                        console.log("Filtered mahasiswa data for course selection:", mahasiswaData.length, "valid entries");
                        
                        // Update hidden select for form validation with valid data only
                        mahasiswaSelect.innerHTML = "<option value=\"\">Pilih Mahasiswa</option>";
                        mahasiswaData.forEach(mahasiswa => {
                            try {
                                const option = document.createElement("option");
                                option.value = mahasiswa.id;
                                option.textContent = mahasiswa.name + " (" + (mahasiswa.nim || "N/A") + ")";
                                if (selectedMahasiswa && mahasiswa.id == selectedMahasiswa.id) {
                                    option.selected = true;
                                }
                                mahasiswaSelect.appendChild(option);
                            } catch (error) {
                                console.error("Error creating option for mahasiswa:", mahasiswa, error);
                            }
                        });
                    } else {
                        console.error("Invalid mahasiswa data received:", data);
                        mahasiswaData = [];
                    }
                    isInitialLoad = false; // Mark initial load complete after fetching student list
                })
                .catch(error => {
                    console.error("Error:", error);
                    mahasiswaSearch.placeholder = "Error loading mahasiswa";
                    isInitialLoad = false; // Mark initial load complete on error too
                });
        } else {
            // Disable student search when no course selected
            mahasiswaSearch.disabled = true;
            mahasiswaSearch.value = "";
            mahasiswaSearch.placeholder = "Pilih mata kuliah terlebih dahulu...";
            hideStudentDetails();
            hideGradeInput();
            isInitialLoad = false; // Mark initial load complete
        }
    });

    // Student search functionality with improved null handling
    mahasiswaSearch.addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        
        if (!mahasiswaData || mahasiswaData.length === 0) {
            return;
        }

        if (searchTerm.length === 0) {
            displaySearchResults(mahasiswaData);
            return;
        }

        try {
            const filteredData = mahasiswaData.filter(mahasiswa => {
                // Additional null checks in filter
                if (!mahasiswa || !mahasiswa.name) {
                    return false;
                }
                
                const name = mahasiswa.name.toLowerCase();
                const nim = (mahasiswa.nim || "").toLowerCase();
                
                return name.includes(searchTerm) || nim.includes(searchTerm);
            });

            displaySearchResults(filteredData);
        } catch (error) {
            console.error("Error filtering mahasiswa data:", error);
            displaySearchResults([]);
        }
    });

    mahasiswaSearch.addEventListener("focus", function() {
        if (mahasiswaData.length > 0) {
            this.select();
            displaySearchResults(mahasiswaData);
        }
    });

    mahasiswaSearch.addEventListener("click", function() {
        if (mahasiswaData.length > 0) {
            this.select();
            displaySearchResults(mahasiswaData);
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function(event) {
        if (!mahasiswaSearch.contains(event.target) && !mahasiswaDropdown.contains(event.target)) {
            mahasiswaDropdown.style.display = "none";
        }
    });

    function displaySearchResults(results) {
        mahasiswaDropdown.innerHTML = "";
        
        if (!results || results.length === 0) {
            const noResultItem = document.createElement("div");
            noResultItem.className = "dropdown-item no-results";
            noResultItem.textContent = "Tidak ada mahasiswa ditemukan";
            mahasiswaDropdown.appendChild(noResultItem);
        } else {
            results.forEach(mahasiswa => {
                // Additional null check for each mahasiswa in results
                if (!mahasiswa || !mahasiswa.id || !mahasiswa.name) {
                    console.warn("Skipping invalid mahasiswa in search results:", mahasiswa);
                    return;
                }
                
                try {
                    const item = document.createElement("div");
                    item.className = "dropdown-item d-flex justify-content-between align-items-center";
                    
                    const mahasiswaInfo = document.createElement("div");
                    mahasiswaInfo.className = "mahasiswa-info";
                    
                    const mahasiswaName = document.createElement("div");
                    mahasiswaName.className = "mahasiswa-name";
                    mahasiswaName.textContent = mahasiswa.name + " (" + (mahasiswa.nim || "N/A") + ")";
                    
                    const mahasiswaDetails = document.createElement("div");
                    mahasiswaDetails.className = "mahasiswa-details";
                    let detailsText = "";
                    if (mahasiswa.kelas_name) {
                        detailsText += "Kelas: " + mahasiswa.kelas_name;
                    }
                    if (mahasiswa.program_studi_name) {
                        if (detailsText) detailsText += " • ";
                        detailsText += "Prodi: " + mahasiswa.program_studi_name;
                    }
                    mahasiswaDetails.textContent = detailsText;
                    
                    mahasiswaInfo.appendChild(mahasiswaName);
                    if (detailsText) {
                        mahasiswaInfo.appendChild(mahasiswaDetails);
                    }
                    
                    item.appendChild(mahasiswaInfo);

                    // Status badge
                    const badge = document.createElement("span");
                    if (mahasiswa.has_nilai) {
                        badge.className = "badge bg-success text-white ms-2";
                        badge.textContent = "Sudah Dinilai";
                    } else {
                        badge.className = "badge bg-warning text-white ms-2";
                        badge.textContent = "Belum Dinilai";
                    }
                    item.appendChild(badge);

                    item.addEventListener("click", function() {
                        selectMahasiswa(mahasiswa);
                    });
                    mahasiswaDropdown.appendChild(item);
                } catch (error) {
                    console.error("Error creating dropdown item for mahasiswa:", mahasiswa, error);
                }
            });
        }
        
        mahasiswaDropdown.style.display = "block";
    }

    function selectMahasiswa(mahasiswa) {
        selectedMahasiswa = mahasiswa;
        mahasiswaSearch.value = mahasiswa.name + " (" + mahasiswa.nim + ")";
        mahasiswaSelect.value = mahasiswa.id;
        mahasiswaDropdown.style.display = "none";
        
        showStudentDetails(mahasiswa);
        showGradeInput();
        
        // Load existing grades if any
        loadExistingGrades(mataKuliahSelect.value, mahasiswa.id);
    }
    
    function showStudentDetails(mahasiswa) {
        const studentDetailsRow = document.getElementById("student_details_row");
        const studentName = document.getElementById("student_name");
        const studentNim = document.getElementById("student_nim");
        const studentProdi = document.getElementById("student_prodi");
        const studentKelas = document.getElementById("student_kelas");
        
        studentName.textContent = mahasiswa.name || "-";
        studentNim.textContent = mahasiswa.nim || "-";
        studentProdi.textContent = mahasiswa.program_studi_name || "-";
        studentKelas.textContent = mahasiswa.kelas_name || "-";
        
        studentDetailsRow.style.display = "block";
    }
    
    function hideStudentDetails() {
        const studentDetailsRow = document.getElementById("student_details_row");
        studentDetailsRow.style.display = "none";
    }

    function showGradeInput() {
        const gradeInputSection = document.getElementById("grade-input");
        gradeInputSection.style.display = "block";
    }

    function hideGradeInput() {
        const gradeInputSection = document.getElementById("grade-input");
        gradeInputSection.style.display = "none";
    }

    // Grade calculation
    window.calculateFinalGrade = function() {
        const gradeInputs = document.querySelectorAll("input[type=\"number\"]");
        let totalWeightedScore = 0;
        let hasValues = false;

        gradeInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            if (input.value !== "") {
                hasValues = true;
                const weight = getWeightForComponent(input.name);
                totalWeightedScore += value * (weight / 100);
                
                const card = input.closest(".grade-input-card");
                if (value > 0) {
                    card.classList.add("has-value");
                } else {
                    card.classList.remove("has-value");
                }
            }
        });

        if (hasValues) {
            const finalGrade = totalWeightedScore;
            const letterGrade = getLetterGrade(finalGrade);
            const status = finalGrade >= 60 ? "Lulus" : "Tidak Lulus";
            
            document.getElementById("final-grade-number").textContent = finalGrade.toFixed(1);
            document.getElementById("final-grade-letter").textContent = letterGrade;
            document.getElementById("final-grade-status").textContent = status;
            document.getElementById("final-grade-preview").style.display = "block";
            
            const previewCard = document.getElementById("final-grade-preview");
            previewCard.className = finalGrade >= 60 ? "card bg-success-lt border-success" : "card bg-danger-lt border-danger";
        } else {
            document.getElementById("final-grade-preview").style.display = "none";
        }
    }

    function getWeightForComponent(componentKey) {
        const weights = {
            @foreach($bobotNilaiSettings as $setting)
            "{{ $setting->component_key }}": {{ $setting->weight }},
            @endforeach
        };
        return weights[componentKey] || 0;
    }

    function getLetterGrade(score) {
        // Use configurable grade scales from server
        const gradeScales = [
            @php
                $gradeScales = \App\Models\Akademik\GradeScaleSetting::where('is_active', true)
                    ->orderBy('min_score', 'desc')
                    ->get();
            @endphp
            @foreach($gradeScales as $scale)
                {
                    min_score: {{ $scale->min_score }},
                    max_score: {{ $scale->max_score }},
                    grade_letter: "{{ $scale->grade_letter }}"
                },
            @endforeach
        ];
        
        // Find the appropriate grade based on active scales
        for (let i = 0; i < gradeScales.length; i++) {
            const scale = gradeScales[i];
            if (score >= scale.min_score && score <= scale.max_score) {
                return scale.grade_letter;
            }
        }
        
        // If no exact match, find the highest grade that the score qualifies for
        for (let i = 0; i < gradeScales.length; i++) {
            const scale = gradeScales[i];
            if (score >= scale.min_score) {
                return scale.grade_letter;
            }
        }
        
        // Fallback to lowest grade
        return gradeScales.length > 0 ? gradeScales[gradeScales.length - 1].grade_letter : "E";
    }

    // Function to load existing grades
    function loadExistingGrades(mataKuliahId, mahasiswaId) {
        if (!mataKuliahId || !mahasiswaId) {
            return;
        }

        const fetchUrl = {!! json_encode(route($spref."nilai.get-existing")) !!};
        fetch(fetchUrl + "?mata_kuliah_id=" + mataKuliahId + "&mahasiswa_id=" + mahasiswaId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.nilai) {
                    // Fill the form with existing grades
                    const grades = data.nilai;
                    
                    // Fill each grade input
                    Object.keys(grades).forEach(key => {
                        const input = document.querySelector("input[name=\"" + key + "\"]");
                        if (input && key !== "nilai_akhir" && key !== "huruf_nilai") {
                            input.value = grades[key] || "";
                        }
                    });
                    
                    // Trigger calculation to show final grade
                    calculateFinalGrade();
                    
                    console.log("Existing grades loaded:", grades);
                } else {
                    // No existing grades, clear all inputs
                    document.querySelectorAll("input[type=\"number\"]").forEach(input => {
                        input.value = "";
                    });
                    calculateFinalGrade();
                }
            })
            .catch(error => {
                console.error("Error loading existing grades:", error);
                // Clear inputs on error
                document.querySelectorAll("input[type=\"number\"]").forEach(input => {
                    input.value = "";
                });
            });
    }

    // Initialize on page load
    if (mataKuliahSelect.value) {
        mataKuliahSelect.dispatchEvent(new Event("change"));
    } else {
        isInitialLoad = false;
    }
});
</script>
@endsection
