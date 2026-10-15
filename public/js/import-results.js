// Import Results Modal JavaScript
// Handles the display and interaction of import results in a table-based modal

// Global variable to store import results data
window.importResultsData = null;

// Initialize import results functionality
$(document).ready(function () {
    // Check for import results on page load
    if (typeof checkImportResults !== 'undefined' && checkImportResults) {
        setTimeout(function () {
            fetchAndShowImportResults();
        }, 500);
    }

    // Import form submission with AJAX
    $('#importForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Hide import modal
        $('#importModal').modal('hide');

        // Show loading state
        Swal.fire({
            title: 'Mengimport Data...',
            html: 'Mohon tunggu, sedang memproses file Excel Anda.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit form via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.close();

                // Always try to fetch import results after form submission
                setTimeout(function () {
                    fetchAndShowImportResults();
                }, 500);
            },
            error: function (xhr, status, error) {
                Swal.close();

                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Terjadi kesalahan validasi:\n';
                    for (const field in errors) {
                        errorMessage += '• ' + errors[field].join('\n• ') + '\n';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Try to fetch import results even on error
                    setTimeout(function () {
                        fetchAndShowImportResults();
                    }, 500);
                }
            }
        });
    });

    // Pagination click handlers
    $(document).on('click', '.pagination .page-link', function (e) {
        e.preventDefault();

        const page = parseInt($(this).data('page'));
        const type = $(this).data('type');

        if (isNaN(page) || $(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) {
            return;
        }

        // Update current page
        window.importResultsData.pagination[type].current = page;

        // Repopulate table based on type
        if (type === 'success') {
            const search = $('#successSearch').val();
            const filter = $('#successFilter').val();
            populateSuccessTable(page, search, filter);
        } else if (type === 'errors') {
            const search = $('#errorsSearch').val();
            const filter = $('#errorsFilter').val();
            populateErrorsTable(page, search, filter);
        } else if (type === 'warnings') {
            const search = $('#warningsSearch').val();
            populateWarningsTable(page, search);
        }
    });

    // Search and filter handlers
    $('#successSearch, #successFilter').on('input change', function () {
        const search = $('#successSearch').val();
        const filter = $('#successFilter').val();
        window.importResultsData.pagination.success.current = 1;
        populateSuccessTable(1, search, filter);
    });

    $('#errorsSearch, #errorsFilter').on('input change', function () {
        const search = $('#errorsSearch').val();
        const filter = $('#errorsFilter').val();
        window.importResultsData.pagination.errors.current = 1;
        populateErrorsTable(1, search, filter);
    });

    $('#warningsSearch').on('input', function () {
        const search = $(this).val();
        window.importResultsData.pagination.warnings.current = 1;
        populateWarningsTable(1, search);
    });

    // Export results button
    $('#exportResultsBtn').on('click', function () {
        exportImportResults();
    });

    // Refresh page button
    $('#refreshPageBtn').on('click', function () {
        location.reload();
    });
});

// Function to fetch and show import results
function fetchAndShowImportResults() {
    // Get the route URL from global variable or construct it
    const resultsUrl = window.importResultsRoute || '/akademik/data-akademik-mahasiswa-import-results';

    $.ajax({
        url: resultsUrl,
        type: 'GET',
        success: function (results) {
            if (results && (results.success !== undefined || results.error_type)) {
                showImportResultsModal(results);
            } else {
                // No results found, just reload
                location.reload();
            }
        },
        error: function () {
            // No results found, just reload
            location.reload();
        }
    });
}

// Function to show import results modal
function showImportResultsModal(results) {
    // Update summary statistics
    const stats = results.statistics || {};
    $('#summaryImported').text(stats.imported || 0);
    $('#summaryUpdated').text(stats.updated || 0);
    $('#summarySkipped').text(stats.skipped || 0);
    $('#summaryFailed').text(stats.failed || 0);

    // Update import info
    $('#importFilename').text(results.filename || '-');
    $('#importTimestamp').text(results.timestamp || '-');
    $('#importMode').text(results.replace_mode ? 'Replace Mode (Ganti data yang ada)' : 'Normal Mode (Lewati duplikat)');
    $('#importTotalProcessed').text(stats.total_processed || 0);

    // Update tab counts
    const details = results.details || {};
    const successItems = details.success_items || [];
    const errors = details.errors || [];
    const warnings = details.warnings || [];

    $('#successTabCount').text(successItems.length);
    $('#errorsTabCount').text(errors.length);
    $('#warningsTabCount').text(warnings.length);

    // Initialize pagination data
    window.importResultsData = {
        success: successItems,
        errors: errors,
        warnings: warnings,
        pagination: {
            success: { current: 1, perPage: 10 },
            errors: { current: 1, perPage: 10 },
            warnings: { current: 1, perPage: 10 }
        }
    };

    // Populate tables
    populateSuccessTable();
    populateErrorsTable();
    populateWarningsTable();

    // Show the modal
    $('#importResultsModal').modal('show');

    // Show appropriate notification
    if (results.success) {
        Swal.fire({
            icon: 'success',
            title: 'Import Berhasil!',
            text: `${stats.imported || 0} data berhasil diimport, ${stats.updated || 0} data diperbarui.`,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: results.error_title || 'Import Gagal!',
            text: results.error_message || 'Terjadi kesalahan saat mengimport data.',
            confirmButtonText: 'OK'
        });
    }
}

// Function to populate success table
function populateSuccessTable(page = 1, search = '', filter = '') {
    const data = window.importResultsData.success;
    const perPage = window.importResultsData.pagination.success.perPage;

    // Filter data
    let filteredData = data.filter(item => {
        const searchText = (search || '').toLowerCase();

        const message = (item.message || '').toLowerCase();
        const action = (item.action || '').toLowerCase();
        const name = (item.name || '').toLowerCase();
        const nim = (item.nim || '').toLowerCase();
        const rowText = String(item.row || '').toLowerCase();

        const matchesSearch = searchText === '' ||
            message.includes(searchText) ||
            action.includes(searchText) ||
            name.includes(searchText) ||
            nim.includes(searchText) ||
            rowText.includes(searchText);

        const matchesFilter = filter === '' || item.type === filter;
        return matchesSearch && matchesFilter;
    });

    // Paginate
    const totalItems = filteredData.length;
    const totalPages = Math.ceil(totalItems / perPage);
    const startIndex = (page - 1) * perPage;
    const endIndex = startIndex + perPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    // Update pagination info
    $('#successShowingStart').text(totalItems > 0 ? startIndex + 1 : 0);
    $('#successShowingEnd').text(Math.min(endIndex, totalItems));
    $('#successTotal').text(totalItems);

    // Populate table
    let tableHtml = '';
    pageData.forEach(item => {
        const statusBadge = getStatusBadge(item.type, item.action);
        tableHtml += `
            <tr>
                <td>${item.row}</td>
                <td><strong>${item.name || 'Nama tidak tersedia'}</strong></td>
                <td>${item.nim || '-'}</td>
                <td>${statusBadge}</td>
                <td>${item.action}</td>
                <td>${item.message}</td>
            </tr>
        `;
    });

    if (tableHtml === '') {
        tableHtml = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data yang ditemukan</td></tr>';
    }

    $('#successTableBody').html(tableHtml);

    // Update pagination
    updatePagination('success', page, totalPages);
}

// Function to populate errors table
function populateErrorsTable(page = 1, search = '', filter = '') {
    const data = window.importResultsData.errors;
    const perPage = window.importResultsData.pagination.errors.perPage;

    // Filter data
    let filteredData = data.filter(item => {
        const errors = item.errors || 'Error tidak diketahui';
        const columns = item.columns || 'Kolom tidak diketahui';
        const matchesSearch = search === '' ||
            errors.toLowerCase().includes(search.toLowerCase()) ||
            columns.toLowerCase().includes(search.toLowerCase());
        const matchesFilter = filter === '' || item.type === filter;
        return matchesSearch && matchesFilter;
    });

    // Paginate
    const totalItems = filteredData.length;
    const totalPages = Math.ceil(totalItems / perPage);
    const startIndex = (page - 1) * perPage;
    const endIndex = startIndex + perPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    // Update pagination info
    $('#errorsShowingStart').text(totalItems > 0 ? startIndex + 1 : 0);
    $('#errorsShowingEnd').text(Math.min(endIndex, totalItems));
    $('#errorsTotal').text(totalItems);

    // Populate table
    let tableHtml = '';
    pageData.forEach(item => {
        const typeBadge = getErrorTypeBadge(item.type || 'unknown');
        tableHtml += `
            <tr>
                <td>${item.row || '-'}</td>
                <td>${item.columns || 'Kolom tidak diketahui'}</td>
                <td>${item.errors || 'Error tidak diketahui'}</td>
                <td>${typeBadge}</td>
            </tr>
        `;
    });

    if (tableHtml === '') {
        tableHtml = '<tr><td colspan="4" class="text-center text-muted">Tidak ada error yang ditemukan</td></tr>';
    }

    $('#errorsTableBody').html(tableHtml);

    // Update pagination
    updatePagination('errors', page, totalPages);
}

// Function to populate warnings table
function populateWarningsTable(page = 1, search = '') {
    const data = window.importResultsData.warnings;
    const perPage = window.importResultsData.pagination.warnings.perPage;

    // Filter data
    let filteredData = data.filter(item => {
        return search === '' || item.toLowerCase().includes(search.toLowerCase());
    });

    // Paginate
    const totalItems = filteredData.length;
    const totalPages = Math.ceil(totalItems / perPage);
    const startIndex = (page - 1) * perPage;
    const endIndex = startIndex + perPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    // Update pagination info
    $('#warningsShowingStart').text(totalItems > 0 ? startIndex + 1 : 0);
    $('#warningsShowingEnd').text(Math.min(endIndex, totalItems));
    $('#warningsTotal').text(totalItems);

    // Populate table
    let tableHtml = '';
    pageData.forEach((item, index) => {
        // Parse warning message to extract row number
        const rowMatch = item.match(/Baris (\d+):/);
        const rowNumber = rowMatch ? rowMatch[1] : startIndex + index + 1;
        const message = item.replace(/Baris \d+:\s*/, '');

        tableHtml += `
            <tr>
                <td>${rowNumber}</td>
                <td>${message}</td>
                <td><span class="badge bg-warning">Periksa</span></td>
            </tr>
        `;
    });

    if (tableHtml === '') {
        tableHtml = '<tr><td colspan="3" class="text-center text-muted">Tidak ada peringatan yang ditemukan</td></tr>';
    }

    $('#warningsTableBody').html(tableHtml);

    // Update pagination
    updatePagination('warnings', page, totalPages);
}

// Helper function to get status badge
function getStatusBadge(type, action) {
    switch (type) {
        case 'success':
            return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Diimport</span>';
        case 'info':
            return '<span class="badge bg-info"><i class="fas fa-sync me-1"></i>Diperbarui</span>';
        case 'warning':
            return '<span class="badge bg-warning"><i class="fas fa-skip-forward me-1"></i>Dilewati</span>';
        default:
            return '<span class="badge bg-secondary">' + action + '</span>';
    }
}

// Helper function to get error type badge
function getErrorTypeBadge(type) {
    switch (type) {
        case 'validation':
            return '<span class="badge bg-danger">Validasi</span>';
        case 'duplicate_email':
            return '<span class="badge bg-warning">Duplikat Email</span>';
        case 'duplicate_phone':
            return '<span class="badge bg-warning">Duplikat Telepon</span>';
        case 'duplicate_nim':
            return '<span class="badge bg-warning">Duplikat NIM</span>';
        case 'system_error':
            return '<span class="badge bg-danger">Error Sistem</span>';
        default:
            return '<span class="badge bg-secondary">' + type + '</span>';
    }
}

// Function to update pagination
function updatePagination(type, currentPage, totalPages) {
    const paginationContainer = $(`#${type}Pagination`);
    let paginationHtml = '';

    if (totalPages <= 1) {
        paginationContainer.html('');
        return;
    }

    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" data-type="${type}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1" data-type="${type}">1</a></li>`;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}" data-type="${type}">${i}</a>
            </li>
        `;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}" data-type="${type}">${totalPages}</a></li>`;
    }

    // Next button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" data-type="${type}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;

    paginationContainer.html(paginationHtml);
}

// Function to export import results
function exportImportResults() {
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";

    // Add headers
    csvContent += "Type,Row,Status,Action,Message,Details\n";

    // Add success items
    window.importResultsData.success.forEach(item => {
        csvContent += `Success,${item.row},${item.type},${item.action},"${item.message}",\n`;
    });

    // Add errors
    window.importResultsData.errors.forEach(item => {
        csvContent += `Error,${item.row},${item.type},${item.columns},"${item.errors}",\n`;
    });

    // Add warnings
    window.importResultsData.warnings.forEach(item => {
        const rowMatch = item.match(/Baris (\d+):/);
        const rowNumber = rowMatch ? rowMatch[1] : '-';
        const message = item.replace(/Baris \d+:\s*/, '');
        csvContent += `Warning,${rowNumber},warning,,"${message}",\n`;
    });

    // Download file
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `import_results_${new Date().getTime()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
