@extends('core-themes.core-backpage')

@section('title', 'Billing Templates Management')

@section('content')
<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Applicable Semesters</label>
                                <select name="applicable_semesters[]" class="form-select" multiple>
                                    @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Kosongkan untuk semua semester</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Applicable Programs</label>
                                <select name="applicable_programs[]" class="form-select" multiple>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kosongkan untuk semua program</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="auto_generate" value="1" class="form-check-input" id="autoGenerate">
                            <label class="form-check-label" for="autoGenerate">
                                Enable Auto Generation
                            </label>
                        </div>
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

@endsection

@push('scripts')
<script>
// Get CSRF token
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.template-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});

// Utility function to show message
function showMessage(message, type = 'info') {
    alert(message); // Simple alert for now - can be enhanced later
}

// View Template Details
function viewTemplate(templateId) {
    showMessage('Loading template details for ID: ' + templateId + '\n\nThis will show template information in a modal.');
}

// Edit Template
function editTemplate(templateId) {
    showMessage('Edit template functionality for ID: ' + templateId + '\n\nThis will open an edit form.');
}

// Preview Generation
function previewGeneration(templateId) {
    showMessage('Preview billing generation for template ID: ' + templateId + '\n\nThis will show a preview of what will be generated.');
}

// Generate Billing
function generateBilling(templateId) {
    if (confirm('Apakah Anda yakin ingin generate billing dari template ini?\n\nProses ini akan membuat tagihan untuk semua mahasiswa yang memenuhi kriteria.')) {
        showMessage('Generating billing for template ID: ' + templateId + '\n\nBilling generation started!');
    }
}

// Bulk Actions
function bulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.template-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        showMessage('Pilih minimal satu template untuk melakukan bulk action.');
        return;
    }

    const actionText = {
        'activate': 'mengaktifkan',
        'deactivate': 'menonaktifkan', 
        'delete': 'menghapus'
    };

    if (confirm(`Apakah Anda yakin ingin ${actionText[action]} ${selectedIds.length} template yang dipilih?`)) {
        showMessage(`Bulk action "${action}" akan dilakukan untuk ${selectedIds.length} template.\nIDs: ${selectedIds.join(', ')}`);
    }
}

// Delete Template
function deleteTemplate(templateId) {
    if (confirm('Apakah Anda yakin ingin menghapus template ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        showMessage('Template dengan ID ' + templateId + ' akan dihapus.');
    }
}
</script>
@endpush
