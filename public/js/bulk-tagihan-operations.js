/**
 * Bulk Tagihan Operations JavaScript
 * Handles bulk delete, bulk update amount, and group recall operations
 */

class BulkTagihanOperations {
    constructor() {
        this.selectedTagihan = new Set();
        this.groupCode = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTagihanData();
    }

    bindEvents() {
        // Select all checkbox
        $(document).on('change', '#select-all-tagihan', (e) => {
            this.toggleSelectAll(e.target.checked);
        });

        // Individual checkbox
        $(document).on('change', '.tagihan-checkbox', (e) => {
            this.toggleTagihan(e.target.value, e.target.checked);
        });

        // Bulk action buttons
        $(document).on('click', '#btn-bulk-delete', () => {
            this.showBulkDeleteModal();
        });

        $(document).on('click', '#btn-bulk-update-amount', () => {
            this.showBulkUpdateAmountModal();
        });

        $(document).on('click', '#btn-bulk-update-due-date', () => {
            this.showBulkUpdateDueDateModal();
        });

        $(document).on('click', '#btn-recall-group', () => {
            this.showRecallGroupModal();
        });

        // Modal confirmations
        $(document).on('click', '#confirm-bulk-delete', () => {
            this.executeBulkDelete();
        });

        $(document).on('click', '#confirm-bulk-update-amount', () => {
            this.executeBulkUpdateAmount();
        });

        $(document).on('click', '#confirm-bulk-update-due-date', () => {
            this.executeBulkUpdateDueDate();
        });

        $(document).on('click', '#confirm-recall-group', () => {
            this.executeRecallGroup();
        });

        // Update type change handler
        $(document).on('change', '#update-type', (e) => {
            this.handleUpdateTypeChange(e.target.value);
        });

        // Due date type change handler
        $(document).on('change', '#due-date-type', (e) => {
            this.handleDueDateTypeChange(e.target.value);
        });

        // Modal close handlers
        $(document).on('click', '[data-dismiss="modal"]', (e) => {
            const modalId = $(e.target).closest('.modal').attr('id');
            if (modalId) {
                $(`#${modalId}`).modal('hide');
            }
        });

        // Close button handlers
        $(document).on('click', '.close', (e) => {
            const modalId = $(e.target).closest('.modal').attr('id');
            if (modalId) {
                $(`#${modalId}`).modal('hide');
            }
        });

        // ESC key handler for modals
        $(document).on('keydown', (e) => {
            if (e.key === 'Escape') {
                $('.modal.show').modal('hide');
            }
        });
    }

    loadTagihanData() {
        // Extract group code from URL or data attribute
        this.groupCode = $('#tagihan-group-code').val() || window.location.pathname.split('/').pop();

        if (!this.groupCode) {
            console.error('Group code not found');
            return;
        }

        // Load tagihan list via AJAX
        this.fetchTagihanList();
    }

    fetchTagihanList() {
        $.ajax({
            url: `/keuangan/tagihan-kuliah-group/${this.groupCode}/tagihan`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            success: (response) => {
                if (response.success) {
                    this.renderTagihanTable(response.data);
                    this.updateStatistics(response.data.statistics);
                }
            },
            error: (xhr) => {
                console.error('Failed to load tagihan data:', xhr.responseJSON);
                this.showAlert('error', 'Gagal memuat data tagihan');
            }
        });
    }

    renderTagihanTable(data) {
        const tbody = $('#tagihan-table-body');
        tbody.empty();

        if (data.tagihans.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data tagihan</td>
                </tr>
            `);
            return;
        }

        data.tagihans.forEach(tagihan => {
            const canEdit = tagihan.can_edit;
            const statusBadge = this.getStatusBadge(tagihan.status);

            tbody.append(`
                <tr>
                    <td>
                        ${canEdit ? `<input type="checkbox" class="tagihan-checkbox" value="${tagihan.id}">` : ''}
                    </td>
                    <td>${tagihan.code}</td>
                    <td>${tagihan.mahasiswa_name}</td>
                    <td>${tagihan.mahasiswa_nim}</td>
                    <td class="text-right">${tagihan.formatted_amount}</td>
                    <td>${tagihan.due_date}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${canEdit ? `
                            <button class="btn btn-sm btn-warning edit-tagihan" data-id="${tagihan.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                        ` : ''}
                        ${tagihan.status === 'Paid' || tagihan.status === 'Sukses' ? `
                            <button class="btn btn-sm btn-info view-receipt" data-id="${tagihan.id}">
                                <i class="fas fa-receipt"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `);
        });

        this.updateBulkActionButtons();
    }

    getStatusBadge(status) {
        const badges = {
            'Pending': '<span class="badge badge-warning">Pending</span>',
            'Paid': '<span class="badge badge-success">Lunas</span>',
            'Sukses': '<span class="badge badge-success">Sukses</span>',
            'Cancelled': '<span class="badge badge-danger">Dibatalkan</span>'
        };
        return badges[status] || `<span class="badge badge-secondary">${status}</span>`;
    }

    updateStatistics(stats) {
        $('#total-tagihan-count').text(stats.total_count);
        $('#pending-tagihan-count').text(stats.pending_count);
        $('#paid-tagihan-count').text(stats.paid_count);
        $('#total-amount').text(this.formatCurrency(stats.total_amount));
        $('#pending-amount').text(this.formatCurrency(stats.pending_amount));
    }

    toggleSelectAll(checked) {
        $('.tagihan-checkbox').prop('checked', checked);

        if (checked) {
            $('.tagihan-checkbox').each((index, element) => {
                this.selectedTagihan.add(element.value);
            });
        } else {
            this.selectedTagihan.clear();
        }

        this.updateBulkActionButtons();
    }

    toggleTagihan(tagihanId, checked) {
        if (checked) {
            this.selectedTagihan.add(tagihanId);
        } else {
            this.selectedTagihan.delete(tagihanId);
        }

        // Update select all checkbox
        const totalCheckboxes = $('.tagihan-checkbox').length;
        const checkedCheckboxes = $('.tagihan-checkbox:checked').length;

        $('#select-all-tagihan').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#select-all-tagihan').prop('checked', checkedCheckboxes === totalCheckboxes);

        this.updateBulkActionButtons();
    }

    updateBulkActionButtons() {
        const hasSelection = this.selectedTagihan.size > 0;
        $('#btn-bulk-delete').prop('disabled', !hasSelection);
        $('#btn-bulk-update-amount').prop('disabled', !hasSelection);
        $('#btn-bulk-update-due-date').prop('disabled', !hasSelection);

        $('#selected-count').text(this.selectedTagihan.size);
    }

    showBulkDeleteModal() {
        if (this.selectedTagihan.size === 0) return;

        $('#selected-delete-count').text(this.selectedTagihan.size);
        $('#bulkDeleteModal').modal('show');
    }

    showBulkUpdateAmountModal() {
        if (this.selectedTagihan.size === 0) return;

        $('#selected-update-count').text(this.selectedTagihan.size);
        $('#update-type').val('fixed_amount').trigger('change');
        $('#amount-value').val('');
        $('#bulkUpdateAmountModal').modal('show');
    }

    showBulkUpdateDueDateModal() {
        if (this.selectedTagihan.size === 0) return;

        $('#selected-due-date-count').text(this.selectedTagihan.size);
        $('#due-date-type').val('set_date').trigger('change');
        $('#due-date-value').val('');
        $('#days-value').val('');
        $('#bulkUpdateDueDateModal').modal('show');
    }

    showRecallGroupModal() {
        $('#recallGroupModal').modal('show');
    }

    handleUpdateTypeChange(updateType) {
        const amountInput = $('#amount-value');
        const amountLabel = $('#amount-label');

        const labels = {
            'fixed_amount': 'Nominal Baru',
            'percentage_increase': 'Persentase Kenaikan (%)',
            'percentage_decrease': 'Persentase Penurunan (%)',
            'add_amount': 'Jumlah Penambahan',
            'subtract_amount': 'Jumlah Pengurangan'
        };

        amountLabel.text(labels[updateType] || 'Nilai');

        // Set appropriate placeholder
        if (updateType.includes('percentage')) {
            amountInput.attr('placeholder', 'Contoh: 10 (untuk 10%)');
        } else {
            amountInput.attr('placeholder', 'Contoh: 100000');
        }
    }

    executeBulkDelete() {
        if (this.selectedTagihan.size === 0) return;

        const tagihanIds = Array.from(this.selectedTagihan);

        $('#confirm-bulk-delete').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');

        $.ajax({
            url: `/keuangan/tagihan-kuliah-group/${this.groupCode}/bulk-delete`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            data: {
                tagihan_ids: tagihanIds
            },
            success: (response) => {
                if (response.success) {
                    this.showAlert('success', response.message);
                    $('#bulkDeleteModal').modal('hide');
                    this.selectedTagihan.clear();
                    this.fetchTagihanList(); // Reload data
                } else {
                    this.showAlert('error', response.message);
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus tagihan';
                this.showAlert('error', message);
            },
            complete: () => {
                $('#confirm-bulk-delete').prop('disabled', false).html('Ya, Hapus');
            }
        });
    }

    executeBulkUpdateAmount() {
        if (this.selectedTagihan.size === 0) return;

        const tagihanIds = Array.from(this.selectedTagihan);
        const updateType = $('#update-type').val();
        const amountValue = parseFloat($('#amount-value').val());

        if (!amountValue || amountValue <= 0) {
            this.showAlert('error', 'Nilai harus diisi dan lebih dari 0');
            return;
        }

        $('#confirm-bulk-update-amount').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengubah...');

        $.ajax({
            url: `/keuangan/tagihan-kuliah-group/${this.groupCode}/bulk-update-amount`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            data: {
                tagihan_ids: tagihanIds,
                update_type: updateType,
                amount_value: amountValue
            },
            success: (response) => {
                if (response.success) {
                    this.showAlert('success', response.message);
                    $('#bulkUpdateAmountModal').modal('hide');
                    this.selectedTagihan.clear();
                    this.fetchTagihanList(); // Reload data
                } else {
                    this.showAlert('error', response.message);
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengubah nominal tagihan';
                this.showAlert('error', message);
            },
            complete: () => {
                $('#confirm-bulk-update-amount').prop('disabled', false).html('Ya, Ubah');
            }
        });
    }

    handleDueDateTypeChange(dueDateType) {
        const dueDateValueGroup = $('#due-date-value-group');
        const daysValueGroup = $('#days-value-group');
        const dueDateLabel = $('#due-date-label');
        const daysLabel = $('#days-label');

        // Hide all groups first
        dueDateValueGroup.hide();
        daysValueGroup.hide();

        switch (dueDateType) {
            case 'set_date':
                dueDateValueGroup.show();
                dueDateLabel.text('Tanggal Jatuh Tempo');
                $('#due-date-value').prop('required', true);
                $('#days-value').prop('required', false);
                break;
            case 'add_days':
                daysValueGroup.show();
                daysLabel.text('Jumlah Hari yang Ditambahkan');
                $('#due-date-value').prop('required', false);
                $('#days-value').prop('required', true);
                break;
            case 'subtract_days':
                daysValueGroup.show();
                daysLabel.text('Jumlah Hari yang Dikurangi');
                $('#due-date-value').prop('required', false);
                $('#days-value').prop('required', true);
                break;
            case 'remove_due_date':
                // No input needed for removing due date
                $('#due-date-value').prop('required', false);
                $('#days-value').prop('required', false);
                break;
        }
    }

    executeBulkUpdateDueDate() {
        if (this.selectedTagihan.size === 0) return;

        const tagihanIds = Array.from(this.selectedTagihan);
        const dueDateType = $('#due-date-type').val();
        let dueDateValue = null;
        let daysValue = null;

        // Validate based on due date type
        if (dueDateType === 'set_date') {
            dueDateValue = $('#due-date-value').val();
            if (!dueDateValue) {
                this.showAlert('error', 'Tanggal jatuh tempo harus diisi');
                return;
            }
        } else if (dueDateType === 'add_days' || dueDateType === 'subtract_days') {
            daysValue = parseInt($('#days-value').val());
            if (!daysValue || daysValue <= 0) {
                this.showAlert('error', 'Jumlah hari harus diisi dan lebih dari 0');
                return;
            }
        }

        $('#confirm-bulk-update-due-date').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengubah...');

        const requestData = {
            tagihan_ids: tagihanIds,
            due_date_type: dueDateType
        };

        if (dueDateValue) {
            requestData.due_date_value = dueDateValue;
        }
        if (daysValue) {
            requestData.days_value = daysValue;
        }

        $.ajax({
            url: `/keuangan/tagihan-kuliah-group/${this.groupCode}/bulk-update-due-date`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            data: requestData,
            success: (response) => {
                if (response.success) {
                    this.showAlert('success', response.message);
                    $('#bulkUpdateDueDateModal').modal('hide');
                    this.selectedTagihan.clear();
                    this.fetchTagihanList(); // Reload data
                } else {
                    this.showAlert('error', response.message);
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengubah jatuh tempo tagihan';
                this.showAlert('error', message);
            },
            complete: () => {
                $('#confirm-bulk-update-due-date').prop('disabled', false).html('Ya, Ubah');
            }
        });
    }

    executeRecallGroup() {
        $('#confirm-recall-group').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menarik...');

        $.ajax({
            url: `/keuangan/tagihan-kuliah-group/${this.groupCode}/recall`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            success: (response) => {
                if (response.success) {
                    this.showAlert('success', response.message);
                    $('#recallGroupModal').modal('hide');
                    // Redirect back to group list or reload page
                    setTimeout(() => {
                        window.location.href = '/keuangan/tagihan-kuliah-group';
                    }, 2000);
                } else {
                    this.showAlert('error', response.message);
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menarik group';
                this.showAlert('error', message);
            },
            complete: () => {
                $('#confirm-recall-group').prop('disabled', false).html('Ya, Tarik Group');
            }
        });
    }

    showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        $('#alert-container').html(alertHtml);

        // Auto hide after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
}

// Initialize when document is ready
$(document).ready(function () {
    // Only initialize if we're on the tagihan group detail page
    if ($('#tagihan-table-body').length > 0) {
        window.bulkTagihanOps = new BulkTagihanOperations();
    }
});
