/**
 * Graduation Management JavaScript
 * Handles bulk graduation functionality for student management
 */

$(document).ready(function () {
    // Initialize graduation management
    initializeGraduationManagement();
});

function initializeGraduationManagement() {
    // Update bulk action buttons when checkboxes change
    $(document).on('change', '.student-checkbox', function () {
        updateBulkActionButtons();
    });

    // Bulk Graduate Button Click Handler
    $('#bulkGraduateBtn').on('click', function () {
        const selectedStudents = getSelectedGraduationCandidates();
        if (selectedStudents.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Kandidat Kelulusan Terpilih',
                text: 'Silakan pilih minimal satu mahasiswa yang memenuhi syarat kelulusan (semester ≥ 7 dan status aktif).',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Populate modal with selected students data
        populateBulkGraduationModal(selectedStudents);
        $('#bulkGraduationModal').modal('show');
    });

    // Bulk graduation form submission
    $('#bulkGraduationForm').on('submit', function (e) {
        e.preventDefault();

        const selectedStudents = getSelectedGraduationCandidates();
        if (selectedStudents.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak ada mahasiswa yang memenuhi syarat kelulusan.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Show confirmation
        Swal.fire({
            title: 'Konfirmasi Kelulusan Massal',
            html: `Anda akan meluluskan <strong>${selectedStudents.length} mahasiswa</strong>.<br><br>Tindakan ini tidak dapat dibatalkan. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-graduation-cap me-1"></i> Ya, Luluskan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses Kelulusan...',
                    html: 'Mohon tunggu, sistem sedang memproses kelulusan mahasiswa.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                this.submit();
            }
        });
    });
}

// Update bulk action buttons to show both terminal and graduation counts
function updateBulkActionButtons() {
    const selectedCheckboxes = $('.student-checkbox:checked');
    const totalSelected = selectedCheckboxes.length;

    // Count graduation candidates (active students with semester >= 7)
    let graduationCandidates = 0;
    selectedCheckboxes.each(function () {
        const row = $(this).closest('tr');
        const statusCell = row.find('td').eq(8); // Status column (adjusted index)
        const semesterCell = row.find('td').eq(6); // Semester column (adjusted index)

        const statusText = statusCell.text().trim();
        const semesterText = semesterCell.text().trim();
        const semester = parseInt(semesterText);

        console.log('Checking student:', {
            statusText: statusText,
            semesterText: semesterText,
            semester: semester,
            isActive: statusText.includes('Aktif') && !statusText.includes('Non-Aktif'),
            semesterEligible: semester >= 7
        });

        // Check if student is active and semester >= 7
        if (statusText.includes('Aktif') && !statusText.includes('Non-Aktif') && semester >= 7) {
            graduationCandidates++;
        }
    });

    console.log('Total selected:', totalSelected, 'Graduation candidates:', graduationCandidates);

    // Update counters
    $('#selectedCount').text(totalSelected);
    $('#selectedGraduateCount').text(graduationCandidates);

    // Show/hide bulk action buttons
    if (totalSelected > 0) {
        $('#bulkActionsGroup').show();

        // Enable/disable graduation button based on candidates
        if (graduationCandidates > 0) {
            $('#bulkGraduateBtn').prop('disabled', false);
        } else {
            $('#bulkGraduateBtn').prop('disabled', true);
        }
    } else {
        $('#bulkActionsGroup').hide();
    }
}

// Get selected graduation candidates
function getSelectedGraduationCandidates() {
    const candidates = [];
    $('.student-checkbox:checked').each(function () {
        const row = $(this).closest('tr');
        const code = $(this).val();
        const name = row.find('td').eq(2).text().trim(); // Name column
        const nim = row.find('td').eq(3).text().trim(); // NIM column
        const prodi = row.find('td').eq(4).text().trim(); // Prodi column (adjusted)
        const kelas = row.find('td').eq(5).text().trim(); // Kelas column
        const semesterCell = row.find('td').eq(6); // Semester column (adjusted)
        const statusCell = row.find('td').eq(8); // Status column (adjusted)

        const statusText = statusCell.text().trim();
        const semesterText = semesterCell.text().trim();
        const semester = parseInt(semesterText);

        console.log('Getting graduation candidate:', {
            code: code,
            name: name,
            nim: nim,
            prodi: prodi,
            statusText: statusText,
            semesterText: semesterText,
            semester: semester,
            isActive: statusText.includes('Aktif') && !statusText.includes('Non-Aktif'),
            semesterEligible: semester >= 7
        });

        // Only include active students with semester >= 7
        if (statusText.includes('Aktif') && !statusText.includes('Non-Aktif') && semester >= 7) {
            candidates.push({
                code: code,
                name: name,
                nim: nim,
                prodi: prodi,
                semester: semester
            });
        }
    });

    console.log('Final graduation candidates:', candidates);
    return candidates;
}

// Populate bulk graduation modal
function populateBulkGraduationModal(selectedStudents) {
    $('#bulkGraduateSelectedCount').text(selectedStudents.length);

    // Populate students list
    let studentsHtml = '';
    selectedStudents.forEach(function (student) {
        studentsHtml += `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <strong>${student.name}</strong><br>
                    <small class="text-muted">NIM: ${student.nim} | ${student.prodi} | Semester: ${student.semester}</small>
                </div>
                <span class="badge bg-success">Memenuhi Syarat</span>
            </div>
        `;
    });
    $('#selectedGraduateStudentsList').html(studentsHtml);

    // Set hidden input with selected student codes
    const studentCodes = selectedStudents.map(s => s.code);
    $('#selectedGraduateStudentsInput').val(JSON.stringify(studentCodes));
}

// Individual graduation form handlers
$(document).on('submit', 'form[action*="graduate"]', function (e) {
    e.preventDefault();

    const studentName = $(this).find('input[name="graduation_date"]').closest('.modal').find('.modal-title').text().replace('Luluskan Mahasiswa - ', '');

    Swal.fire({
        title: 'Konfirmasi Kelulusan',
        html: `Anda akan meluluskan mahasiswa <strong>${studentName}</strong>.<br><br>Tindakan ini tidak dapat dibatalkan. Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-graduation-cap me-1"></i> Ya, Luluskan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses Kelulusan...',
                html: 'Mohon tunggu, sistem sedang memproses kelulusan mahasiswa.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            this.submit();
        }
    });
});
