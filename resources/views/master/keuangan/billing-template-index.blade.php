@extends('core-themes.core-backpage')

@section('title')
    Billing Templates - {{ $academy ?? 'SIAKAD' }}
@endsection

@section('menu')
    Master Keuangan
@endsection

@section('submenu')
    Billing Templates
@endsection

@section('subdesc')
    Kelola template untuk auto-generation tagihan kuliah
@endsection

@section('content')
<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Add SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="ti ti-file-invoice me-2"></i>
                                Billing Templates Management
                            </h4>
                            <p class="mb-0 opacity-75">Kelola template untuk auto-generation tagihan</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-white" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                                <i class="ti ti-plus me-1"></i> Tambah Template
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="ti ti-file-text fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['total_templates'] }}</h3>
                    <p class="text-muted mb-0">Total Templates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="ti ti-check fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['active_templates'] }}</h3>
                    <p class="text-muted mb-0">Active Templates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="ti ti-wand fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['auto_generate_templates'] }}</h3>
                    <p class="text-muted mb-0">Auto-Generate</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="ti ti-edit fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['draft_templates'] }}</h3>
                    <p class="text-muted mb-0">Draft Templates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-bar me-2"></i>
                        Billing Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $billing_stats['total_groups'] }}</h4>
                                <p class="text-muted mb-0">Total Groups</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ $billing_stats['published_groups'] }}</h4>
                                <p class="text-muted mb-0">Published Groups</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ number_format($billing_stats['total_tagihan']) }}</h4>
                                <p class="text-muted mb-0">Total Tagihan</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">Rp {{ number_format($billing_stats['pending_amount'], 0, ',', '.') }}</h4>
                                <p class="text-muted mb-0">Pending Amount</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-list me-2"></i>
                        Billing Templates
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkAction('release_billing')">
                            <i class="ti ti-rocket me-1"></i> Release Billing Multi
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="bulkAction('activate')">
                            <i class="ti ti-check me-1"></i> Activate Selected
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="bulkAction('deactivate')">
                            <i class="ti ti-player-pause me-1"></i> Deactivate Selected
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
                            <i class="ti ti-trash me-1"></i> Delete Selected
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Template</th>
                                    <th>Type</th>
                                    <th>Base Amount</th>
                                    <th>Status</th>
                                    <th>Auto Generate</th>
                                    <th>Usage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="template_ids[]" value="{{ $template->id }}" class="form-check-input template-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $template->name }}</h6>
                                            <small class="text-muted">{{ $template->code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline text-info">{{ $template->type_display }}</span>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($template->amount_formula['base_amount'] ?? 0, 0, ',', '.') }}</strong>
                                        @if(count($template->amount_formula['modifiers'] ?? []) > 0)
                                            <br><small class="text-muted">+ {{ count($template->amount_formula['modifiers']) }} modifiers</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-outline {{ $template->status_css_class }}">{{ $template->status_display }}</span>
                                    </td>
                                    <td>
                                        @if($template->auto_generate)
                                            <span class="badge badge-outline text-success">
                                                <i class="ti ti-check me-1"></i>Yes
                                            </span>
                                        @else
                                            <span class="badge badge-outline text-secondary">
                                                <i class="ti ti-x me-1"></i>No
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-outline text-primary">{{ $template->tagihan_kuliah_groups_count }} groups</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info" onclick="viewTemplate({{ $template->id }})" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary" onclick="previewGeneration({{ $template->id }})" title="Preview Generation">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <path d="m21 21-4.35-4.35"></path>
                                                </svg>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="generateBilling({{ $template->id }})" title="Generate Billing">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <polygon points="5,3 19,12 5,21 5,3"></polygon>
                                                </svg>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editTemplate({{ $template->id }})" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            @if($template->tagihan_kuliah_groups_count == 0)
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTemplate({{ $template->id }})" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <polyline points="3,6 5,6 21,6"></polyline>
                                                    <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-inbox fa-3x mb-3"></i>
                                            <p>Belum ada template billing</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                                                <i class="ti ti-plus me-1"></i> Tambah Template Pertama
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                        <form action="{{ route('keuangan.billing-templates.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Template Billing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Template *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe *</label>
                                <select name="type" class="form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="semester">Per Semester</option>
                                    <option value="annual">Tahunan</option>
                                    <option value="one_time">Sekali Bayar</option>
                                    <option value="monthly">Bulanan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Base Amount *</label>
                                <input type="number" name="base_amount" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Grace Period (hari) *</label>
                                <input type="number" name="grace_period_days" class="form-control" value="7" min="0" max="365" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date Formula *</label>
                        <select name="due_date_formula" class="form-select" required>
                            <option value="semester_start + 30 days">30 hari setelah awal semester</option>
                            <option value="semester_start + 45 days">45 hari setelah awal semester</option>
                            <option value="now + 30 days">30 hari dari sekarang</option>
                            <option value="now + 60 days">60 hari dari sekarang</option>
                            <option value="no_due_date">Tidak ada tempo</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semester yang Berlaku</label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAllSemesters">
                                        <label class="form-check-label fw-bold" for="selectAllSemesters">
                                            Pilih Semua Semester
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @for($i = 1; $i <= $maxSemesterLimit; $i++)
                                    <div class="form-check">
                                        <input type="checkbox" name="applicable_semesters[]" value="{{ $i }}" class="form-check-input semester-checkbox" id="semester{{ $i }}">
                                        <label class="form-check-label" for="semester{{ $i }}">
                                            Semester {{ $i }}
                                        </label>
                                    </div>
                                    @endfor
                                </div>
                                <small class="text-muted">Kosongkan untuk semua semester (1-{{ $maxSemesterLimit }})</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Program Studi yang Berlaku</label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAllPrograms">
                                        <label class="form-check-label fw-bold" for="selectAllPrograms">
                                            Pilih Semua Program
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach($programs as $program)
                                    <div class="form-check">
                                        <input type="checkbox" name="applicable_programs[]" value="{{ $program->id }}" class="form-check-input program-checkbox" id="program{{ $program->id }}">
                                        <label class="form-check-label" for="program{{ $program->id }}">
                                            {{ $program->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Kosongkan untuk semua program</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kelas yang Berlaku</label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAllClasses">
                                        <label class="form-check-label fw-bold" for="selectAllClasses">
                                            Pilih Semua Kelas
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach($classes as $class)
                                    <div class="form-check">
                                        <input type="checkbox" name="applicable_classes[]" value="{{ $class->id }}" class="form-check-input class-checkbox" id="class{{ $class->id }}">
                                        <label class="form-check-label" for="class{{ $class->id }}">
                                            {{ $class->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Kosongkan untuk semua kelas (Reguler A, B, dll)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Generation Trigger *</label>
                                <select name="generation_trigger" class="form-select" required>
                                    <option value="manual">Manual</option>
                                    <option value="semester_start">Awal Semester</option>
                                    <option value="scheduled">Terjadwal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="auto_generate" value="1" class="form-check-input" id="autoGenerate">
                                    <label class="form-check-label" for="autoGenerate">
                                        Enable Auto Generation
                                    </label>
                                </div>

                                <div class="form-check mt-3">
                                    <input type="checkbox" name="use_dynamic_naming" value="1" class="form-check-input" id="useDynamicNaming">
                                    <label class="form-check-label" for="useDynamicNaming">
                                        <i class="ti ti-wand me-1"></i> Use Dynamic Naming
                                    </label>
                                    <small class="form-text text-muted">Otomatis gunakan nama dengan tahun akademik dan semester</small>
                                </div>

                                <div class="mt-3" id="dynamicNamingOptions" style="display: none;">
                                    <label class="form-label">Dynamic Naming Pattern</label>
                                    <select name="dynamic_naming_pattern" class="form-select" id="dynamicNamingPattern">
                                        <option value="{template_name} - {academic_year} {semester_type}">SPP Semester - 2024/2025 Ganjil</option>
                                        <option value="{template_name} {academic_year} ({semester_type})">SPP Semester 2024/2025 (Ganjil)</option>
                                        <option value="{template_name} - {semester_type} {academic_year}">SPP Semester - Ganjil 2024/2025</option>
                                        <option value="{academic_year} {semester_type} - {template_name}">2024/2025 Ganjil - SPP Semester</option>
                                        <option value="{template_name} - Semester {semester_type} {year}">SPP Semester - Semester Ganjil 2024/2025</option>
                                        <option value="{template_name} {student_semester} - {academic_year} {semester_type}">SPP Semester 6 - 2024/2025 Ganjil</option>
                                        <option value="{template_name} {student_semester}">SPP Semester 5</option>
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="ti ti-info-circle me-1"></i>
                                            Preview: <span id="namingPreview" class="fw-bold text-primary">SPP Semester - 2024/2025 Ganjil</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modifiers Section -->
                    <div class="mb-3">
                        <label class="form-label">Amount Modifiers</label>
                        <div id="modifiersContainer">
                            <div class="modifier-item border rounded p-3 mb-2" style="display: none;" id="modifierTemplate">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Kondisi</label>
                                        <select class="form-select modifier-condition">
                                            <option value="">Pilih Kondisi</option>
                                            <option value="semester >= 3">Semester >= 3</option>
                                            <option value="semester >= 5">Semester >= 5</option>
                                            <option value="semester >= 7">Semester >= 7</option>
                                            <option value="program_id == 1">Program Studi 1</option>
                                            <option value="program_id == 2">Program Studi 2</option>
                                            <option value="program_id == 3">Program Studi 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Penyesuaian</label>
                                        <input type="text" class="form-control modifier-adjustment" placeholder="+50000 atau *1.2">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm d-block remove-modifier">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addModifier">
                            <i class="ti ti-plus me-1"></i> Tambah Modifier
                        </button>
                        <small class="text-muted d-block mt-1">
                            Modifier memungkinkan penyesuaian amount berdasarkan kondisi tertentu.<br>
                            Contoh: +50000 (tambah), -25000 (kurang), *1.2 (kali), /2 (bagi)
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>
                    Preview Billing Generation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="proceedGeneration">
                    <i class="fas fa-play me-1"></i> Proceed with Generation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Template Details Modal -->
<div class="modal fade" id="viewTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Template Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewTemplateContent">
                <!-- Template details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editFromView">
                    <i class="fas fa-edit me-1"></i> Edit Template
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editTemplateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Edit Template Billing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editTemplateContent">
                    <!-- Edit form will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
// Preload form data for JavaScript access
window.formData = {
    programs: @json($programs),
    classes: @json($classes),
    maxSemesterLimit: {{ $maxSemesterLimit }}
};
// Get CSRF token
function getCSRFToken() {
    var metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

// Function to close modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.querySelector('.modal-backdrop')?.remove();
    document.body.classList.remove('modal-open');
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    var selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.template-checkbox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
    }
    
    // Select All Semesters functionality
    var selectAllSemesters = document.getElementById('selectAllSemesters');
    if (selectAllSemesters) {
        selectAllSemesters.addEventListener('change', function() {
            var semesterCheckboxes = document.querySelectorAll('.semester-checkbox');
            for (var i = 0; i < semesterCheckboxes.length; i++) {
                semesterCheckboxes[i].checked = this.checked;
            }
        });
    }
    
    // Select All Programs functionality
    var selectAllPrograms = document.getElementById('selectAllPrograms');
    if (selectAllPrograms) {
        selectAllPrograms.addEventListener('change', function() {
            var programCheckboxes = document.querySelectorAll('.program-checkbox');
            for (var i = 0; i < programCheckboxes.length; i++) {
                programCheckboxes[i].checked = this.checked;
            }
        });
    }
    
    // Select All Classes functionality
    var selectAllClasses = document.getElementById('selectAllClasses');
    if (selectAllClasses) {
        selectAllClasses.addEventListener('change', function() {
            var classCheckboxes = document.querySelectorAll('.class-checkbox');
            for (var i = 0; i < classCheckboxes.length; i++) {
                classCheckboxes[i].checked = this.checked;
            }
        });
    }
    
    // Modifier management
    var addModifierBtn = document.getElementById('addModifier');
    if (addModifierBtn) {
        addModifierBtn.addEventListener('click', function() {
            addModifier();
        });
    }
    
    // Add event listeners for all close buttons
    document.addEventListener('click', function(e) {
        // Close button with data-bs-dismiss="modal"
        if (e.target.getAttribute('data-bs-dismiss') === 'modal') {
            var modal = e.target.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        }
        
        // Close button with btn-close class
        if (e.target.classList.contains('btn-close')) {
            var modal = e.target.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        }
        
        // Close modal when clicking backdrop
        if (e.target.classList.contains('modal-backdrop')) {
            closeModal(e.target.previousElementSibling?.id);
        }
    });
});

// Utility function to show SweetAlert2 notification
function showToast(message, type) {
    type = type || 'info';
    
    var iconType = 'info';
    var titleText = 'Informasi';
    
    switch(type) {
        case 'success':
            iconType = 'success';
            titleText = 'Berhasil!';
            break;
        case 'danger':
        case 'error':
            iconType = 'error';
            titleText = 'Error!';
            break;
        case 'warning':
            iconType = 'warning';
            titleText = 'Peringatan!';
            break;
        default:
            iconType = 'info';
            titleText = 'Informasi';
    }
    
    Swal.fire({
        icon: iconType,
        title: titleText,
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
}

// View Template Details
function viewTemplate(templateId) {
    fetch('/keuangan/billing-templates/' + templateId, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            var template = data.data;
            var content = '<div class="row">' +
                '<div class="col-md-6">' +
                    '<h6>Informasi Dasar</h6>' +
                    '<table class="table table-sm">' +
                        '<tr><td><strong>Nama:</strong></td><td>' + template.name + '</td></tr>' +
                        '<tr><td><strong>Kode:</strong></td><td>' + template.code + '</td></tr>' +
                        '<tr><td><strong>Tipe:</strong></td><td><span class="badge bg-info">' + (template.type_display || template.type) + '</span></td></tr>' +
                        '<tr><td><strong>Status:</strong></td><td><span class="badge bg-' + (template.status === 'active' ? 'success' : 'secondary') + '">' + (template.status_display || template.status) + '</span></td></tr>' +
                    '</table>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<h6>Detail Keuangan</h6>' +
                    '<table class="table table-sm">' +
                        '<tr><td><strong>Jumlah Dasar:</strong></td><td>Rp ' + (template.amount_formula && template.amount_formula.base_amount ? template.amount_formula.base_amount.toLocaleString('id-ID') : '0') + '</td></tr>' +
                        '<tr><td><strong>Masa Tenggang:</strong></td><td>' + template.grace_period_days + ' hari</td></tr>' +
                        '<tr><td><strong>Auto Generate:</strong></td><td>' + (template.auto_generate ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>') + '</td></tr>' +
                        '<tr><td><strong>Jumlah Penggunaan:</strong></td><td>' + (template.tagihan_kuliah_groups_count || 0) + ' grup</td></tr>' +
                    '</table>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-12">' +
                    '<h6>Deskripsi</h6>' +
                    '<p>' + (template.description || 'Tidak ada deskripsi') + '</p>' +
                '</div>' +
            '</div>';
            
            document.getElementById('viewTemplateContent').innerHTML = content;
            
            // Set up edit button
            document.getElementById('editFromView').onclick = function() {
                document.getElementById('viewTemplateModal').style.display = 'none';
                document.querySelector('.modal-backdrop')?.remove();
                document.body.classList.remove('modal-open');
                editTemplate(templateId);
            };
            
            // Show modal using data attributes
            var modal = document.getElementById('viewTemplateModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        } else {
            showToast('Failed to load template details: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(function(error) {
        showToast('Error loading template details: ' + error.message, 'danger');
    });
}

// Edit Template
function editTemplate(templateId) {
    fetch('/keuangan/billing-templates/' + templateId, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            var template = data.data;
            var content = '<div class="row">' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Nama Template *</label>' +
                        '<input type="text" name="name" class="form-control" value="' + template.name + '" required>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Tipe *</label>' +
                        '<select name="type" class="form-select" required>' +
                            '<option value="semester" ' + (template.type === 'semester' ? 'selected' : '') + '>Per Semester</option>' +
                            '<option value="annual" ' + (template.type === 'annual' ? 'selected' : '') + '>Tahunan</option>' +
                            '<option value="one_time" ' + (template.type === 'one_time' ? 'selected' : '') + '>Sekali Bayar</option>' +
                            '<option value="monthly" ' + (template.type === 'monthly' ? 'selected' : '') + '>Bulanan</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Base Amount *</label>' +
                        '<input type="number" name="base_amount" class="form-control" value="' + (template.amount_formula && template.amount_formula.base_amount ? template.amount_formula.base_amount : 0) + '" min="0" required>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Grace Period (hari) *</label>' +
                        '<input type="number" name="grace_period_days" class="form-control" value="' + template.grace_period_days + '" min="0" max="365" required>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            '<div class="mb-3">' +
                '<label class="form-label">Due Date Formula *</label>' +
                '<select name="due_date_formula" class="form-select" required>' +
                    '<option value="semester_start + 30 days" ' + (template.due_date_formula === 'semester_start + 30 days' ? 'selected' : '') + '>30 hari setelah awal semester</option>' +
                    '<option value="semester_start + 45 days" ' + (template.due_date_formula === 'semester_start + 45 days' ? 'selected' : '') + '>45 hari setelah awal semester</option>' +
                    '<option value="now + 30 days" ' + (template.due_date_formula === 'now + 30 days' ? 'selected' : '') + '>30 hari dari sekarang</option>' +
                    '<option value="now + 60 days" ' + (template.due_date_formula === 'now + 60 days' ? 'selected' : '') + '>60 hari dari sekarang</option>' +
                    '<option value="no_due_date" ' + (template.due_date_formula === 'no_due_date' ? 'selected' : '') + '>Tidak ada tempo</option>' +
                '</select>' +
            '</div>' +
            
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Semester yang Berlaku</label>' +
                        '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">' +
                            '<div class="form-check">' +
                                '<input type="checkbox" class="form-check-input" id="editSelectAllSemesters">' +
                                '<label class="form-check-label fw-bold" for="editSelectAllSemesters">Pilih Semua Semester</label>' +
                            '</div>' +
                            '<hr class="my-2">' +
                            generateEditSemesterCheckboxes(template.applicable_semesters) +
                        '</div>' +
                        '<small class="text-muted">Kosongkan untuk semua semester (1-' + window.formData.maxSemesterLimit + ')</small>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Program Studi yang Berlaku</label>' +
                        '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;" id="editProgramsContainer">' +
                            '<div class="form-check">' +
                                '<input type="checkbox" class="form-check-input" id="editSelectAllPrograms">' +
                                '<label class="form-check-label fw-bold" for="editSelectAllPrograms">Pilih Semua Program</label>' +
                            '</div>' +
                            '<hr class="my-2">' +
                            '<!-- Program checkboxes will be loaded dynamically -->' +
                        '</div>' +
                        '<small class="text-muted">Kosongkan untuk semua program</small>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Kelas yang Berlaku</label>' +
                        '<div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;" id="editClassesContainer">' +
                            '<div class="form-check">' +
                                '<input type="checkbox" class="form-check-input" id="editSelectAllClasses">' +
                                '<label class="form-check-label fw-bold" for="editSelectAllClasses">Pilih Semua Kelas</label>' +
                            '</div>' +
                            '<hr class="my-2">' +
                            '<!-- Class checkboxes will be loaded dynamically -->' +
                        '</div>' +
                        '<small class="text-muted">Kosongkan untuk semua kelas (Reguler A, B, dll)</small>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Generation Trigger *</label>' +
                        '<select name="generation_trigger" class="form-select" required>' +
                            '<option value="manual" ' + (template.generation_trigger === 'manual' ? 'selected' : '') + '>Manual</option>' +
                            '<option value="semester_start" ' + (template.generation_trigger === 'semester_start' ? 'selected' : '') + '>Awal Semester</option>' +
                            '<option value="scheduled" ' + (template.generation_trigger === 'scheduled' ? 'selected' : '') + '>Terjadwal</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<label class="form-label">Status</label>' +
                        '<select name="status" class="form-select">' +
                            '<option value="draft" ' + (template.status === 'draft' ? 'selected' : '') + '>Draft</option>' +
                            '<option value="active" ' + (template.status === 'active' ? 'selected' : '') + '>Active</option>' +
                            '<option value="inactive" ' + (template.status === 'inactive' ? 'selected' : '') + '>Inactive</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<div class="mb-3">' +
                        '<div class="form-check mt-4">' +
                            '<input type="checkbox" name="auto_generate" value="1" class="form-check-input" id="editAutoGenerate" ' + (template.auto_generate ? 'checked' : '') + '>' +
                            '<label class="form-check-label" for="editAutoGenerate">Enable Auto Generation</label>' +
                        '</div>' +
                        '<div class="form-check mt-3">' +
                            '<input type="checkbox" name="use_dynamic_naming" value="1" class="form-check-input" id="editUseDynamicNaming" ' + (template.use_dynamic_naming ? 'checked' : '') + '>' +
                            '<label class="form-check-label" for="editUseDynamicNaming">' +
                                '<i class="ti ti-wand me-1"></i> Use Dynamic Naming' +
                            '</label>' +
                            '<small class="form-text text-muted">Otomatis gunakan nama dengan tahun akademik dan semester</small>' +
                        '</div>' +
                        '<div class="mt-3" id="editDynamicNamingOptions" style="display: ' + (template.use_dynamic_naming ? 'block' : 'none') + ';">' +
                            '<label class="form-label">Dynamic Naming Pattern</label>' +
                            '<select name="dynamic_naming_pattern" class="form-select" id="editDynamicNamingPattern">' +
                                '<option value="{template_name} - {academic_year} {semester_type}" ' + (template.dynamic_naming_pattern === '{template_name} - {academic_year} {semester_type}' ? 'selected' : '') + '>SPP Semester - 2024/2025 Ganjil</option>' +
                                '<option value="{template_name} {academic_year} ({semester_type})" ' + (template.dynamic_naming_pattern === '{template_name} {academic_year} ({semester_type})' ? 'selected' : '') + '>SPP Semester 2024/2025 (Ganjil)</option>' +
                                '<option value="{template_name} - {semester_type} {academic_year}" ' + (template.dynamic_naming_pattern === '{template_name} - {semester_type} {academic_year}' ? 'selected' : '') + '>SPP Semester - Ganjil 2024/2025</option>' +
                                '<option value="{academic_year} {semester_type} - {template_name}" ' + (template.dynamic_naming_pattern === '{academic_year} {semester_type} - {template_name}' ? 'selected' : '') + '>2024/2025 Ganjil - SPP Semester</option>' +
                                '<option value="{template_name} - Semester {semester_type} {year}" ' + (template.dynamic_naming_pattern === '{template_name} - Semester {semester_type} {year}' ? 'selected' : '') + '>SPP Semester - Semester Ganjil 2024/2025</option>' +
                                '<option value="{template_name} {student_semester} - {academic_year} {semester_type}" ' + (template.dynamic_naming_pattern === '{template_name} {student_semester} - {academic_year} {semester_type}' ? 'selected' : '') + '>SPP Semester 6 - 2024/2025 Ganjil</option>' +
                                '<option value="{template_name} {student_semester}" ' + (template.dynamic_naming_pattern === '{template_name} {student_semester}' ? 'selected' : '') + '>SPP Semester 5</option>' +
                            '</select>' +
                            '<div class="mt-2">' +
                                '<small class="text-info">' +
                                    '<i class="ti ti-info-circle me-1"></i>' +
                                    'Preview: <span id="editNamingPreview" class="fw-bold text-primary"></span>' +
                                '</small>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            // Add Modifiers Section for Edit
            '<div class="mb-3">' +
                '<label class="form-label">Amount Modifiers</label>' +
                '<div id="editModifiersContainer">' +
                    '<div class="modifier-item border rounded p-3 mb-2" style="display: none;" id="editModifierTemplate">' +
                        '<div class="row">' +
                            '<div class="col-md-6">' +
                                '<label class="form-label">Kondisi</label>' +
                                '<select class="form-select modifier-condition">' +
                                    '<option value="">Pilih Kondisi</option>' +
                                    '<option value="semester >= 3">Semester >= 3</option>' +
                                    '<option value="semester >= 5">Semester >= 5</option>' +
                                    '<option value="semester >= 7">Semester >= 7</option>' +
                                    '<option value="program_id == 1">Program Studi 1</option>' +
                                    '<option value="program_id == 2">Program Studi 2</option>' +
                                    '<option value="program_id == 3">Program Studi 3</option>' +
                                '</select>' +
                            '</div>' +
                            '<div class="col-md-4">' +
                                '<label class="form-label">Penyesuaian</label>' +
                                '<input type="text" class="form-control modifier-adjustment" placeholder="+50000 atau *1.2">' +
                            '</div>' +
                            '<div class="col-md-2">' +
                                '<label class="form-label">&nbsp;</label>' +
                                '<button type="button" class="btn btn-danger btn-sm d-block remove-modifier">' +
                                    '<i class="ti ti-trash"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<button type="button" class="btn btn-sm btn-outline-primary" id="editAddModifier">' +
                    '<i class="ti ti-plus me-1"></i> Tambah Modifier' +
                '</button>' +
                '<small class="text-muted d-block mt-1">' +
                    'Modifier memungkinkan penyesuaian amount berdasarkan kondisi tertentu.<br>' +
                    'Contoh: +50000 (tambah), -25000 (kurang), *1.2 (kali), /2 (bagi)' +
                '</small>' +
            '</div>' +
            
            '<div class="mb-3">' +
                '<label class="form-label">Description</label>' +
                '<textarea name="description" class="form-control" rows="3">' + (template.description || '') + '</textarea>' +
            '</div>';
            
            document.getElementById('editTemplateContent').innerHTML = content;
            document.getElementById('editTemplateForm').action = '/keuangan/billing-templates/' + templateId;
            
            // Load programs and classes options using checkbox interface
            loadEditFormCheckboxOptions(template);
            
            // Setup select all functionality for semesters after content is loaded
            setTimeout(function() {
                var editSelectAllSemesters = document.getElementById('editSelectAllSemesters');
                if (editSelectAllSemesters) {
                    editSelectAllSemesters.addEventListener('change', function() {
                        var semesterCheckboxes = document.querySelectorAll('.edit-semester-checkbox');
                        for (var i = 0; i < semesterCheckboxes.length; i++) {
                            semesterCheckboxes[i].checked = this.checked;
                        }
                    });
                }
            }, 100);
            
            // Load existing modifiers
            if (template.amount_formula && template.amount_formula.modifiers) {
                for (var i = 0; i < template.amount_formula.modifiers.length; i++) {
                    addEditModifier(template.amount_formula.modifiers[i]);
                }
            }
            
            // Setup add modifier button for edit modal
            document.getElementById('editAddModifier').addEventListener('click', function() {
                addEditModifier();
            });
            
            // Setup dynamic naming functionality for edit modal
            setTimeout(function() {
                var editUseDynamicNaming = document.getElementById('editUseDynamicNaming');
                if (editUseDynamicNaming) {
                    editUseDynamicNaming.addEventListener('change', function() {
                        var options = document.getElementById('editDynamicNamingOptions');
                        if (options) {
                            options.style.display = this.checked ? 'block' : 'none';
                            updateEditNamingPreview();
                        }
                    });
                }

                var editDynamicNamingPattern = document.getElementById('editDynamicNamingPattern');
                if (editDynamicNamingPattern) {
                    editDynamicNamingPattern.addEventListener('change', updateEditNamingPreview);
                }
                
                var editTemplateNameInput = document.querySelector('#editTemplateModal input[name="name"]');
                if (editTemplateNameInput) {
                    editTemplateNameInput.addEventListener('input', updateEditNamingPreview);
                }
                
                // Initial preview update
                updateEditNamingPreview();
            }, 200);
            
            // Show modal using vanilla JavaScript
            var modal = document.getElementById('editTemplateModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        } else {
            showToast('Failed to load template for editing: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(function(error) {
        showToast('Error loading template: ' + error.message, 'danger');
    });
}


// Preview Generation
function previewGeneration(templateId) {
    fetch('/keuangan/billing-templates/' + templateId + '/preview', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            var content = '<div class="row">' +
                '<div class="col-md-6">' +
                    '<h6>Informasi Template</h6>' +
                    '<p><strong>ID Template:</strong> ' + templateId + '</p>' +
                    '<p><strong>Tipe:</strong> ' + (data.data.template && data.data.template.type_display ? data.data.template.type_display : 'Per Semester') + '</p>' +
                    '<p><strong>Status:</strong> ' + (data.data.template && data.data.template.status_display ? data.data.template.status_display : 'Aktif') + '</p>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<h6>Preview Generasi</h6>' +
                    '<p><strong>Mahasiswa Memenuhi Syarat:</strong> ' + (data.data.eligible_students_count || 0) + '</p>' +
                    '<p><strong>Tanggal Jatuh Tempo:</strong> ' + (data.data.due_date || 'Belum ditentukan') + '</p>' +
                    '<p><strong>Masa Tenggang:</strong> ' + (data.data.grace_period_days || 7) + ' hari</p>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-12">' +
                    '<h6>Ringkasan Keuangan</h6>' +
                    '<p><strong>Jumlah Dasar:</strong> Rp ' + (data.data.base_amount || 0).toLocaleString('id-ID') + '</p>' +
                    '<p><strong>Total Estimasi Jumlah:</strong> Rp ' + (data.data.total_estimated_amount || 0).toLocaleString('id-ID') + '</p>' +
                '</div>' +
            '</div>' +
            '<div class="alert alert-info">' +
                '<strong>Catatan:</strong> Ini adalah preview. Generasi sebenarnya akan membuat record tagihan untuk mahasiswa yang memenuhi syarat.' +
            '</div>';
            
            document.getElementById('previewContent').innerHTML = content;
            
            // Set up proceed button
            document.getElementById('proceedGeneration').onclick = function() {
                document.getElementById('previewModal').style.display = 'none';
                document.querySelector('.modal-backdrop')?.remove();
                document.body.classList.remove('modal-open');
                generateBilling(templateId);
            };
            
            // Show modal using vanilla JavaScript
            var modal = document.getElementById('previewModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        } else {
            showToast('Failed to load preview: ' + (data.message || 'Unknown error'), 'danger');
        }
    })
    .catch(function(error) {
        showToast('Error loading preview: ' + error.message, 'danger');
    });
}

// Handle edit form submission
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit form submission
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'editTemplateForm') {
            e.preventDefault();
            
            var formData = new FormData(e.target);
            var url = e.target.action;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // Hide modal using vanilla JavaScript
                document.getElementById('editTemplateModal').style.display = 'none';
                document.querySelector('.modal-backdrop')?.remove();
                document.body.classList.remove('modal-open');
                
                if (data.success) {
                    showToast('Template berhasil diperbarui!', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('Error: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(function(error) {
                showToast('Error updating template: ' + error.message, 'danger');
            });
        }
    });
});

// Generate Billing
function generateBilling(templateId) {
    Swal.fire({
        title: 'Konfirmasi Generate Billing',
        text: 'Apakah Anda yakin ingin generate billing dari template ini? Proses ini akan membuat tagihan untuk semua mahasiswa yang memenuhi kriteria.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Generate!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Sedang memproses...',
                text: 'Mohon tunggu, billing sedang di-generate.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            fetch('/keuangan/billing-templates/' + templateId + '/generate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Billing berhasil di-generate! ' + (data.message || 'Generation completed successfully.'),
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Failed to generate billing: ' + (data.message || 'Unknown error')
                    });
                }
            })
            .catch(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error generating billing: ' + error.message
                });
            });
        }
    });
}

// Bulk Actions
function bulkAction(action) {
    var checkboxes = document.querySelectorAll('.template-checkbox:checked');
    var selectedIds = [];
    for (var i = 0; i < checkboxes.length; i++) {
        selectedIds.push(checkboxes[i].value);
    }
    
    if (selectedIds.length === 0) {
        showToast('Pilih minimal satu template untuk melakukan bulk action.', 'warning');
        return;
    }

    var actionText = {
        'activate': 'mengaktifkan',
        'deactivate': 'menonaktifkan', 
        'delete': 'menghapus',
        'release_billing': 'release billing untuk'
    };

    var actionColor = {
        'activate': '#28a745',
        'deactivate': '#6c757d', 
        'delete': '#dc3545',
        'release_billing': '#17a2b8'
    };

    // Special handling for release_billing action
    if (action === 'release_billing') {
        Swal.fire({
            title: 'Konfirmasi Release Billing Multi',
            text: 'Apakah Anda yakin ingin release billing untuk ' + selectedIds.length + ' template yang dipilih? Proses ini akan membuat tagihan untuk semua mahasiswa yang memenuhi kriteria dari template-template tersebut.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: actionColor[action],
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Release Billing!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading with different message for release billing
                Swal.fire({
                    title: 'Sedang memproses release billing...',
                    text: 'Mohon tunggu, billing sedang di-generate dari template yang dipilih.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                var formData = new FormData();
                formData.append('action', action);
                for (var i = 0; i < selectedIds.length; i++) {
                    formData.append('template_ids[]', selectedIds[i]);
                }

                fetch('/keuangan/billing-templates/bulk-action', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Release Billing Berhasil!',
                            text: data.message || 'Release billing berhasil dilakukan.',
                            timer: 4000,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Release Billing Gagal!',
                            text: 'Error: ' + (data.message || 'Unknown error')
                        });
                    }
                })
                .catch(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error performing release billing: ' + error.message
                    });
                });
            }
        });
        return; // Exit early for release_billing action
    }

    // Standard bulk actions (activate, deactivate, delete)
    Swal.fire({
        title: 'Konfirmasi Bulk Action',
        text: 'Apakah Anda yakin ingin ' + actionText[action] + ' ' + selectedIds.length + ' template yang dipilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: actionColor[action],
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, ' + actionText[action] + '!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Sedang memproses...',
                text: 'Mohon tunggu, bulk action sedang diproses.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            var formData = new FormData();
            formData.append('action', action);
            for (var i = 0; i < selectedIds.length; i++) {
                formData.append('template_ids[]', selectedIds[i]);
            }

            fetch('/keuangan/billing-templates/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Bulk action berhasil dilakukan.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Error: ' + (data.message || 'Unknown error')
                    });
                }
            })
            .catch(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error performing bulk action: ' + error.message
                });
            });
        }
    });
}

// Delete Template
function deleteTemplate(templateId) {
    Swal.fire({
        title: 'Konfirmasi Hapus Template',
        text: 'Apakah Anda yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Sedang menghapus...',
                text: 'Mohon tunggu, template sedang dihapus.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            fetch('/keuangan/billing-templates/' + templateId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Template berhasil dihapus!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Error: ' + (data.message || 'Unknown error')
                    });
                }
            })
            .catch(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error deleting template: ' + error.message
                });
            });
        }
    });
}

// Modifier Management Functions
function addModifier() {
    var template = document.getElementById('modifierTemplate');
    var container = document.getElementById('modifiersContainer');
    var clone = template.cloneNode(true);
    
    // Generate unique ID
    var timestamp = Date.now();
    clone.id = 'modifier_' + timestamp;
    clone.style.display = 'block';
    
    // Add name attributes for form submission
    var conditionSelect = clone.querySelector('.modifier-condition');
    var adjustmentInput = clone.querySelector('.modifier-adjustment');
    conditionSelect.name = 'modifiers[' + timestamp + '][condition]';
    adjustmentInput.name = 'modifiers[' + timestamp + '][adjustment]';
    
    // Add remove functionality
    var removeBtn = clone.querySelector('.remove-modifier');
    removeBtn.addEventListener('click', function() {
        clone.remove();
    });
    
    container.appendChild(clone);
}

function collectModifiers() {
    var modifiers = [];
    var modifierItems = document.querySelectorAll('.modifier-item:not(#modifierTemplate)');
    
    for (var i = 0; i < modifierItems.length; i++) {
        var item = modifierItems[i];
        var condition = item.querySelector('.modifier-condition').value;
        var adjustment = item.querySelector('.modifier-adjustment').value;
        
        if (condition && adjustment) {
            modifiers.push({
                condition: condition,
                adjustment: adjustment
            });
        }
    }
    
    return modifiers;
}

// Generate semester checkboxes for edit form
function generateEditSemesterCheckboxes(selectedSemesters) {
    console.log('Generating semester checkboxes with selected:', selectedSemesters);
    var html = '';
    for (var i = 1; i <= window.formData.maxSemesterLimit; i++) {
        var isChecked = '';
        if (selectedSemesters && Array.isArray(selectedSemesters)) {
            // Check if semester exists in selectedSemesters array
            if (selectedSemesters.includes(i) || selectedSemesters.includes(i.toString())) {
                isChecked = 'checked';
            }
        }
        console.log('Semester', i, 'checked:', isChecked);
        
        html += '<div class="form-check">' +
                    '<input type="checkbox" name="applicable_semesters[]" value="' + i + '" class="form-check-input edit-semester-checkbox" id="editSemester' + i + '" ' + isChecked + '>' +
                    '<label class="form-check-label" for="editSemester' + i + '">Semester ' + i + '</label>' +
                '</div>';
    }
    return html;
}

// Load programs and classes checkboxes for edit form
function loadEditFormCheckboxOptions(template) {
    console.log('Loading edit form options for template:', template);
    console.log('Template applicable_programs:', template.applicable_programs);
    console.log('Template applicable_classes:', template.applicable_classes);
    console.log('Template applicable_semesters:', template.applicable_semesters);
    
    // Load programs
    if (window.formData && window.formData.programs) {
        var programsContainer = document.getElementById('editProgramsContainer');
        if (programsContainer) {
            var programsHtml = '<div class="form-check">' +
                                  '<input type="checkbox" class="form-check-input" id="editSelectAllPrograms">' +
                                  '<label class="form-check-label fw-bold" for="editSelectAllPrograms">Pilih Semua Program</label>' +
                               '</div><hr class="my-2">';
            
            window.formData.programs.forEach(function(program) {
                var isChecked = '';
                if (template.applicable_programs && Array.isArray(template.applicable_programs)) {
                    // Check if program ID exists in applicable_programs array
                    if (template.applicable_programs.includes(parseInt(program.id)) || 
                        template.applicable_programs.includes(program.id.toString())) {
                        isChecked = 'checked';
                    }
                }
                console.log('Program', program.name, 'checked:', isChecked);
                
                programsHtml += '<div class="form-check">' +
                                   '<input type="checkbox" name="applicable_programs[]" value="' + program.id + '" class="form-check-input edit-program-checkbox" id="editProgram' + program.id + '" ' + isChecked + '>' +
                                   '<label class="form-check-label" for="editProgram' + program.id + '">' + program.name + '</label>' +
                                '</div>';
            });
            programsContainer.innerHTML = programsHtml;
            
            // Add select all functionality for programs
            setTimeout(function() {
                var editSelectAllPrograms = document.getElementById('editSelectAllPrograms');
                if (editSelectAllPrograms) {
                    editSelectAllPrograms.addEventListener('change', function() {
                        var programCheckboxes = document.querySelectorAll('.edit-program-checkbox');
                        for (var i = 0; i < programCheckboxes.length; i++) {
                            programCheckboxes[i].checked = this.checked;
                        }
                    });
                }
            }, 100);
        }
    }
    
    // Load classes
    if (window.formData && window.formData.classes) {
        var classesContainer = document.getElementById('editClassesContainer');
        if (classesContainer) {
            var classesHtml = '<div class="form-check">' +
                                 '<input type="checkbox" class="form-check-input" id="editSelectAllClasses">' +
                                 '<label class="form-check-label fw-bold" for="editSelectAllClasses">Pilih Semua Kelas</label>' +
                              '</div><hr class="my-2">';
            
            window.formData.classes.forEach(function(kelas) {
                var isChecked = '';
                if (template.applicable_classes && Array.isArray(template.applicable_classes)) {
                    // Check if class ID exists in applicable_classes array
                    if (template.applicable_classes.includes(parseInt(kelas.id)) || 
                        template.applicable_classes.includes(kelas.id.toString())) {
                        isChecked = 'checked';
                    }
                }
                console.log('Class', kelas.name, 'checked:', isChecked);
                
                classesHtml += '<div class="form-check">' +
                                  '<input type="checkbox" name="applicable_classes[]" value="' + kelas.id + '" class="form-check-input edit-class-checkbox" id="editClass' + kelas.id + '" ' + isChecked + '>' +
                                  '<label class="form-check-label" for="editClass' + kelas.id + '">' + kelas.name + '</label>' +
                               '</div>';
            });
            classesContainer.innerHTML = classesHtml;
            
            // Add select all functionality for classes
            setTimeout(function() {
                var editSelectAllClasses = document.getElementById('editSelectAllClasses');
                if (editSelectAllClasses) {
                    editSelectAllClasses.addEventListener('change', function() {
                        var classCheckboxes = document.querySelectorAll('.edit-class-checkbox');
                        for (var i = 0; i < classCheckboxes.length; i++) {
                            classCheckboxes[i].checked = this.checked;
                        }
                    });
                }
            }, 100);
        }
    }
    
    // Add select all functionality for semesters (already loaded in HTML)
    setTimeout(function() {
        var editSelectAllSemesters = document.getElementById('editSelectAllSemesters');
        if (editSelectAllSemesters) {
            editSelectAllSemesters.addEventListener('change', function() {
                var semesterCheckboxes = document.querySelectorAll('.edit-semester-checkbox');
                for (var i = 0; i < semesterCheckboxes.length; i++) {
                    semesterCheckboxes[i].checked = this.checked;
                }
            });
        }
    }, 100);
}

// Edit Modifier Management Functions
function addEditModifier(existingModifier) {
    var template = document.getElementById('editModifierTemplate');
    var container = document.getElementById('editModifiersContainer');
    var clone = template.cloneNode(true);
    
    // Generate unique ID
    var timestamp = Date.now();
    clone.id = 'edit_modifier_' + timestamp;
    clone.style.display = 'block';
    
    // Add name attributes for form submission
    var conditionSelect = clone.querySelector('.modifier-condition');
    var adjustmentInput = clone.querySelector('.modifier-adjustment');
    conditionSelect.name = 'modifiers[' + timestamp + '][condition]';
    adjustmentInput.name = 'modifiers[' + timestamp + '][adjustment]';
    
    // Set existing values if provided
    if (existingModifier) {
        conditionSelect.value = existingModifier.condition;
        adjustmentInput.value = existingModifier.adjustment;
    }
    
    // Add remove functionality
    var removeBtn = clone.querySelector('.remove-modifier');
    removeBtn.addEventListener('click', function() {
        clone.remove();
    });
    
    container.appendChild(clone);
}

// Dynamic Naming functionality
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dynamic naming options
    var useDynamicNaming = document.getElementById('useDynamicNaming');
    if (useDynamicNaming) {
        useDynamicNaming.addEventListener('change', function() {
            var options = document.getElementById('dynamicNamingOptions');
            if (options) {
                options.style.display = this.checked ? 'block' : 'none';
                updateNamingPreview();
            }
        });
    }

    // Update naming preview
    function updateNamingPreview() {
        var templateNameInput = document.querySelector('input[name="name"]');
        var patternSelect = document.getElementById('dynamicNamingPattern');
        var previewSpan = document.getElementById('namingPreview');
        
        if (templateNameInput && patternSelect && previewSpan) {
            var templateName = templateNameInput.value || 'Template Name';
            var pattern = patternSelect.value;
            var preview = pattern
                .replace('{template_name}', templateName)
                .replace('{academic_year}', '2024/2025')
                .replace('{semester_type}', 'Ganjil')
                .replace('{year}', '2024/2025')
                .replace('{student_semester}', '5');
            previewSpan.textContent = preview;
        }
    }

    // Update preview when pattern changes
    var dynamicNamingPattern = document.getElementById('dynamicNamingPattern');
    if (dynamicNamingPattern) {
        dynamicNamingPattern.addEventListener('change', updateNamingPreview);
    }
    
    // Update preview when template name changes
    var templateNameInput = document.querySelector('input[name="name"]');
    if (templateNameInput) {
        templateNameInput.addEventListener('input', updateNamingPreview);
    }
});

// Update edit naming preview function
function updateEditNamingPreview() {
    var templateNameInput = document.querySelector('#editTemplateModal input[name="name"]');
    var patternSelect = document.getElementById('editDynamicNamingPattern');
    var previewSpan = document.getElementById('editNamingPreview');
    
    if (templateNameInput && patternSelect && previewSpan) {
        var templateName = templateNameInput.value || 'Template Name';
        var pattern = patternSelect.value;
        var preview = pattern
            .replace('{template_name}', templateName)
            .replace('{academic_year}', '2024/2025')
            .replace('{semester_type}', 'Ganjil')
            .replace('{year}', '2024/2025')
            .replace('{student_semester}', '5');
        previewSpan.textContent = preview;
    }
}
</script>
