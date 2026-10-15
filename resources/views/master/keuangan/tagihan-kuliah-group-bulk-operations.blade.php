@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .info-box {
            display: block;
            min-height: 90px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 90px;
            width: 90px;
            text-align: center;
            font-size: 45px;
            line-height: 90px;
            background: rgba(0,0,0,0.2);
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 90px;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .info-box-text {
            display: block;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bg-info .info-box-icon {
            color: #fff;
        }

        .bg-warning .info-box-icon {
            color: #fff;
        }

        .bg-success .info-box-icon {
            color: #fff;
        }

        .bg-primary .info-box-icon {
            color: #fff;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        /* Mobile responsive button improvements */
        @media (max-width: 768px) {
            .flex-fill {
                min-width: 0;
                flex: 1 1 auto;
            }
            
            .gap-2 {
                gap: 0.5rem !important;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .btn i {
                margin-right: 0.25rem;
            }
            
            /* Stack buttons vertically on very small screens */
            @media (max-width: 576px) {
                .d-flex.flex-wrap {
                    flex-direction: column;
                }
                
                .flex-fill {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .btn {
                    justify-content: center;
                    text-align: center;
                }
            }
        }

        /* Ensure buttons don't break on medium screens */
        @media (min-width: 769px) and (max-width: 991px) {
            .btn {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
            }
        }

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

        /* Header button improvements */
        .card-header .d-flex {
            gap: 1rem;
        }
        
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .card-header .ms-auto {
                margin-left: 0 !important;
                margin-top: 0.5rem;
                align-self: flex-end;
            }
            
            .card-header h4 {
                font-size: 1.1rem;
            }
            
            .card-header .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .card-header .ms-auto {
                align-self: stretch;
            }
            
            .card-header .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Alert Container -->
    <div id="alert-container"></div>
    
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="flex-grow-1 mb-2 mb-md-0">
                            <h4 class="mb-0">Bulk Operations - {{ $group->name }}</h4>
                            <small class="text-muted">Kelola tagihan secara massal</small>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('keuangan.tagihan-kuliah-group-detail', $group->code) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> 
                                <span class="d-none d-sm-inline">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Tagihan</span>
                                    <span class="info-box-number" id="total-tagihan-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending</span>
                                    <span class="info-box-number" id="pending-tagihan-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Lunas</span>
                                    <span class="info-box-number" id="paid-tagihan-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Pending</span>
                                    <span class="info-box-number" id="pending-amount">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi Massal</h5>
                </div>
                <div class="card-body">
                    <!-- Mobile-friendly button layout -->
                    <div class="row">
                        <div class="col-12">
                            <!-- First row of buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-danger flex-fill" id="btn-bulk-delete" disabled>
                                    <i class="fas fa-trash"></i> 
                                    <span class="d-none d-sm-inline">Hapus Terpilih</span>
                                    <span class="d-inline d-sm-none">Hapus</span>
                                    (<span id="selected-count">0</span>)
                                </button>
                                <button type="button" class="btn btn-warning flex-fill" id="btn-bulk-update-amount" disabled>
                                    <i class="fas fa-edit"></i> 
                                    <span class="d-none d-sm-inline">Ubah Nominal</span>
                                    <span class="d-inline d-sm-none">Nominal</span>
                                </button>
                            </div>
                            
                            <!-- Second row of buttons -->
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-info flex-fill" id="btn-bulk-update-due-date" disabled>
                                    <i class="fas fa-calendar"></i> 
                                    <span class="d-none d-sm-inline">Ubah Jatuh Tempo</span>
                                    <span class="d-inline d-sm-none">Tempo</span>
                                </button>
                                @if($group->status === 'Published')
                                <button type="button" class="btn btn-outline-danger flex-fill" id="btn-recall-group">
                                    <i class="fas fa-undo"></i> 
                                    <span class="d-none d-sm-inline">Recall Group</span>
                                    <span class="d-inline d-sm-none">Recall</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Tagihan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select-all-tagihan">
                                    </th>
                                    <th>Kode</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th width="150">Nominal</th>
                                    <th width="120">Jatuh Tempo</th>
                                    <th width="100">Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tagihan-table-body">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input for group code -->
<input type="hidden" id="tagihan-group-code" value="{{ $group->code }}">

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                <h5 class="modal-title mb-0" style="flex: 1; padding-right: 3rem;">Konfirmasi Hapus Massal</h5>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="padding: 0.5rem; margin: 0; font-size: 1.5rem; background: none; border: none; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Peringatan!</strong> Anda akan menghapus <strong><span id="selected-delete-count">0</span></strong> tagihan yang belum dibayar.
                </div>
                <p>Tagihan yang sudah dibayar tidak akan dihapus. Aksi ini tidak dapat dibatalkan.</p>
                <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Amount Modal -->
<div class="modal fade" id="bulkUpdateAmountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                <h5 class="modal-title mb-0" style="flex: 1; padding-right: 3rem;">Ubah Nominal Massal</h5>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="padding: 0.5rem; margin: 0; font-size: 1.5rem; background: none; border: none; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Anda akan mengubah nominal <strong><span id="selected-update-count">0</span></strong> tagihan yang belum dibayar.
                </div>
                
                <form>
                    <div class="form-group">
                        <label for="update-type">Jenis Perubahan</label>
                        <select class="form-control" id="update-type" required>
                            <option value="fixed_amount">Set Nominal Tetap</option>
                            <option value="percentage_increase">Naikkan Persentase</option>
                            <option value="percentage_decrease">Turunkan Persentase</option>
                            <option value="add_amount">Tambah Nominal</option>
                            <option value="subtract_amount">Kurangi Nominal</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount-value" id="amount-label">Nominal Baru</label>
                        <input type="number" class="form-control" id="amount-value" placeholder="Contoh: 100000" required min="0" step="1000">
                        <small class="form-text text-muted">
                            <strong>Contoh:</strong>
                            <ul class="mb-0 mt-1">
                                <li><strong>Set Nominal Tetap:</strong> 500000 (semua tagihan jadi Rp 500.000)</li>
                                <li><strong>Naikkan Persentase:</strong> 10 (naik 10% dari nominal saat ini)</li>
                                <li><strong>Tambah Nominal:</strong> 50000 (tambah Rp 50.000 ke nominal saat ini)</li>
                            </ul>
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirm-bulk-update-amount">Ya, Ubah</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Due Date Modal -->
<div class="modal fade" id="bulkUpdateDueDateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                <h5 class="modal-title mb-0" style="flex: 1; padding-right: 3rem;">Ubah Jatuh Tempo Massal</h5>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="padding: 0.5rem; margin: 0; font-size: 1.5rem; background: none; border: none; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Anda akan mengubah jatuh tempo <strong><span id="selected-due-date-count">0</span></strong> tagihan yang belum dibayar.
                </div>
                
                <form>
                    <div class="form-group">
                        <label for="due-date-type">Jenis Perubahan</label>
                        <select class="form-control" id="due-date-type" required>
                            <option value="set_date">Set Tanggal Tertentu</option>
                            <option value="add_days">Tambah Hari dari Jatuh Tempo Saat Ini</option>
                            <option value="subtract_days">Kurangi Hari dari Jatuh Tempo Saat Ini</option>
                            <option value="remove_due_date">Hapus Jatuh Tempo (Tidak Ada Tempo)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="due-date-value-group">
                        <label for="due-date-value" id="due-date-label">Tanggal Jatuh Tempo</label>
                        <input type="date" class="form-control" id="due-date-value" required>
                    </div>
                    
                    <div class="form-group" id="days-value-group" style="display: none;">
                        <label for="days-value" id="days-label">Jumlah Hari</label>
                        <input type="number" class="form-control" id="days-value" placeholder="Contoh: 30" min="1" max="365" step="1">
                        <small class="form-text text-muted">
                            <strong>Contoh:</strong>
                            <ul class="mb-0 mt-1">
                                <li><strong>Tambah 30 hari:</strong> Jatuh tempo diperpanjang 30 hari dari tanggal saat ini</li>
                                <li><strong>Kurangi 7 hari:</strong> Jatuh tempo dimajukan 7 hari dari tanggal saat ini</li>
                            </ul>
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="confirm-bulk-update-due-date">Ya, Ubah</button>
            </div>
        </div>
    </div>
</div>

<!-- Recall Group Modal -->
<div class="modal fade" id="recallGroupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                <h5 class="modal-title mb-0" style="flex: 1; padding-right: 3rem;">Konfirmasi Recall Group</h5>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="padding: 0.5rem; margin: 0; font-size: 1.5rem; background: none; border: none; opacity: 0.7;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Peringatan!</strong> Anda akan menarik kembali group "{{ $group->name }}".
                </div>
                <p><strong>Aksi ini akan:</strong></p>
                <ul>
                    <li>Menghapus semua tagihan yang belum dibayar</li>
                    <li>Mengubah status group kembali ke Draft</li>
                    <li>Tagihan yang sudah dibayar tidak akan terpengaruh</li>
                </ul>
                <p class="text-danger"><strong>Aksi ini tidak dapat dibatalkan. Apakah Anda yakin?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-recall-group">Ya, Tarik Group</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.bg-info .info-box-icon {
    color: #fff;
}

.bg-warning .info-box-icon {
    color: #fff;
}

.bg-success .info-box-icon {
    color: #fff;
}

.bg-primary .info-box-icon {
    color: #fff;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Modal close button spacing improvements - Force override Bootstrap */
.modal .modal-header {
    padding: 1.25rem 1.5rem !important;
    border-bottom: 1px solid #dee2e6 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.modal .modal-header .close,
.modal .modal-header button.close {
    padding: 0.75rem !important;
    margin: 0 !important;
    margin-left: auto !important;
    background-color: transparent !important;
    border: 0 !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    color: #000 !important;
    text-shadow: 0 1px 0 #fff !important;
    opacity: 0.5 !important;
    cursor: pointer !important;
    min-width: 2.5rem !important;
    min-height: 2.5rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
}

.modal .modal-header .close:hover,
.modal .modal-header button.close:hover {
    color: #000 !important;
    text-decoration: none !important;
    opacity: 0.75 !important;
    background-color: rgba(0,0,0,0.1) !important;
    border-radius: 0.25rem !important;
}

.modal .modal-header .close:focus,
.modal .modal-header button.close:focus {
    outline: 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.modal .modal-header .modal-title,
.modal .modal-header h5.modal-title {
    margin-bottom: 0 !important;
    line-height: 1.5 !important;
    flex: 1 !important;
    padding-right: 3rem !important;
    font-size: 1.25rem !important;
    font-weight: 500 !important;
    max-width: calc(100% - 4rem) !important;
}

/* Additional modal improvements */
.modal .modal-content {
    border-radius: 0.5rem !important;
    border: none !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.modal .modal-body {
    padding: 1.5rem !important;
}

.modal .modal-footer {
    padding: 1rem 1.5rem !important;
    border-top: 1px solid #dee2e6 !important;
}

/* Specific targeting for this page's modals */
#bulkDeleteModal .modal-header,
#bulkUpdateAmountModal .modal-header,
#bulkUpdateDueDateModal .modal-header,
#recallGroupModal .modal-header {
    padding: 1.5rem !important;
}

#bulkDeleteModal .modal-title,
#bulkUpdateAmountModal .modal-title,
#bulkUpdateDueDateModal .modal-title,
#recallGroupModal .modal-title {
    padding-right: 4rem !important;
}

#bulkDeleteModal .close,
#bulkUpdateAmountModal .close,
#bulkUpdateDueDateModal .close,
#recallGroupModal .close {
    position: absolute !important;
    right: 1.5rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    padding: 0.5rem !important;
    width: 2rem !important;
    height: 2rem !important;
}
</style>
@endpush

@section('custom-js')
    <!-- Ensure jQuery is loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Initialize Bootstrap modals manually if needed -->
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap modals
            $('.modal').modal({
                backdrop: 'static',
                keyboard: true
            });
            
            // Manual modal close handlers
            $('[data-dismiss="modal"], .close').on('click', function() {
                var modalId = $(this).closest('.modal').attr('id');
                if (modalId) {
                    $('#' + modalId).modal('hide');
                }
            });
            
            // ESC key handler
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.modal.show').modal('hide');
                }
            });
        });
    </script>
    
    <script src="{{ asset('js/bulk-tagihan-operations.js') }}"></script>
@endsection
