@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-card h3 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #000 !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .form-control:focus, .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        /* Pagination styling */
        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            color: #435ebe;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }

        .pagination .page-link:hover {
            color: #435ebe;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background-color: #435ebe;
            border-color: #435ebe;
            color: #fff;
        }

        /* Perfect circular images for profile photos */
        .profile-image {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 2px solid #e9ecef;
        }

        .profile-image-small {
            width: 30px !important;
            height: 30px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 2px solid #e9ecef;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Statistics -->
        <div class="col-12 mb-4">
            <div class="stats-card">
                <h3><i class="fas fa-chalkboard-teacher me-2"></i>Statistik Dosen Penasehat</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['total_mahasiswa'] }}</span>
                        <span class="stat-label">Total Mahasiswa</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['mahasiswa_dengan_penasehat'] }}</span>
                        <span class="stat-label">Dengan Penasehat</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['mahasiswa_tanpa_penasehat'] }}</span>
                        <span class="stat-label">Tanpa Penasehat</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['total_dosen_penasehat'] }}</span>
                        <span class="stat-label">Dosen Penasehat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $pages }}</h5>
                        <p class="text-muted mb-0">Kelola penugasan dosen penasehat untuk mahasiswa</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                            <i class="fas fa-users me-1"></i>Penugasan Massal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="filterProdi">
                                <option value="">Semua Program Studi</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="dengan_penasehat">Dengan Penasehat</option>
                                <option value="tanpa_penasehat">Tanpa Penasehat</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchMahasiswa" placeholder="Cari nama mahasiswa...">
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Mencari data...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Mahasiswa</th>
                                    <th>Program Studi</th>
                                    <th>Kelas</th>
                                    <th>Dosen Penasehat</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaTableBody">
                                @include('master.akademik.partials.dosen-penasehat-table', ['mahasiswa' => $mahasiswa, 'spref' => $spref])
                            </tbody>
                        </table>
                    </div>

                    <!-- Results info -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="resultsInfo">
                            Showing {{ $mahasiswa->firstItem() }} to {{ $mahasiswa->lastItem() }} of {{ $mahasiswa->total() }} results
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {{ $mahasiswa->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tugaskan Dosen Penasehat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route($spref . 'akademik.dosen-penasehat-assign') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="mahasiswa_id" id="assignMahasiswaId">
                        <div class="mb-3">
                            <label class="form-label">Mahasiswa</label>
                            <input type="text" class="form-control" id="assignMahasiswaName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Dosen Penasehat <span class="text-danger">*</span></label>
                            <select class="form-select" name="dosen_id" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">
                                        {{ $dosen->name }} ({{ $dosen->nidn ?? $dosen->nip }})
                                        - {{ $dosen->total_mahasiswa_bimbingan }} mahasiswa
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Dosen Penasehat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route($spref . 'akademik.dosen-penasehat-remove') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="mahasiswa_id" id="removeMahasiswaId">
                        <p>Apakah Anda yakin ingin menghapus dosen penasehat untuk mahasiswa <strong id="removeMahasiswaName"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Modal -->
    <div class="modal fade" id="bulkAssignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penugasan Massal Dosen Penasehat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route($spref . 'akademik.dosen-penasehat-bulk-assign') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Dosen Penasehat <span class="text-danger">*</span></label>
                            <select class="form-select" name="dosen_id" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">
                                        {{ $dosen->name }} ({{ $dosen->nidn ?? $dosen->nip }})
                                        - {{ $dosen->total_mahasiswa_bimbingan }} mahasiswa
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Pilih mahasiswa di tabel dengan mencentang checkbox, kemudian pilih dosen penasehat untuk ditugaskan secara massal.
                        </div>
                        <div id="selectedStudents"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="bulkAssignBtn" disabled>Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let searchTimeout;
        const searchUrl = "{{ route($spref . 'akademik.dosen-penasehat-search') }}";

        $(document).ready(function() {
            // Select All functionality - using event delegation for dynamic content
            $(document).on('change', '#selectAll', function() {
                $('.mahasiswa-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkAssignButton();
            });

            $(document).on('change', '.mahasiswa-checkbox', function() {
                updateBulkAssignButton();
                
                // Update select all checkbox
                const totalCheckboxes = $('.mahasiswa-checkbox').length;
                const checkedCheckboxes = $('.mahasiswa-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            // Real-time AJAX search functionality
            $('#filterProdi, #filterStatus').change(function() {
                performAjaxSearch();
            });

            $('#searchMahasiswa').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    performAjaxSearch();
                }, 300); // 300ms debounce
            });

            // Handle pagination clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    performAjaxSearch(url);
                }
            });
        });

        function performAjaxSearch(url = null) {
            const searchUrl = url || "{{ route($spref . 'akademik.dosen-penasehat-search') }}";
            const prodiFilter = $('#filterProdi').val();
            const statusFilter = $('#filterStatus').val();
            const searchText = $('#searchMahasiswa').val();

            // Show loading indicator
            $('#loadingIndicator').show();
            $('#mahasiswaTableBody').hide();

            $.ajax({
                url: searchUrl,
                type: 'GET',
                data: {
                    prodi_id: prodiFilter,
                    status: statusFilter,
                    search: searchText
                },
                success: function(response) {
                    if (response.success) {
                        // Update table content
                        $('#mahasiswaTableBody').html(response.html).show();
                        
                        // Update pagination
                        $('#paginationContainer').html(response.pagination);
                        
                        // Update results info
                        $('#resultsInfo').text(response.showing);
                        
                        // Reset checkboxes
                        $('#selectAll').prop('checked', false);
                        updateBulkAssignButton();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Terjadi kesalahan saat mencari data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mencari data. Silakan coba lagi.'
                    });
                },
                complete: function() {
                    // Hide loading indicator
                    $('#loadingIndicator').hide();
                }
            });
        }

        function assignAdvisor(mahasiswaId, mahasiswaName) {
            $('#assignMahasiswaId').val(mahasiswaId);
            $('#assignMahasiswaName').val(mahasiswaName);
            $('#assignModal').modal('show');
        }

        function changeAdvisor(mahasiswaId, mahasiswaName, currentDosenId) {
            $('#assignMahasiswaId').val(mahasiswaId);
            $('#assignMahasiswaName').val(mahasiswaName);
            $('select[name="dosen_id"]').val(currentDosenId);
            $('#assignModal').modal('show');
        }

        function removeAdvisor(mahasiswaId, mahasiswaName) {
            $('#removeMahasiswaId').val(mahasiswaId);
            $('#removeMahasiswaName').text(mahasiswaName);
            $('#removeModal').modal('show');
        }

        function updateBulkAssignButton() {
            const checkedBoxes = $('.mahasiswa-checkbox:checked');
            const bulkAssignBtn = $('#bulkAssignBtn');
            const selectedStudents = $('#selectedStudents');
            
            if (checkedBoxes.length > 0) {
                bulkAssignBtn.prop('disabled', false);
                
                // Update hidden inputs for selected students
                selectedStudents.empty();
                checkedBoxes.each(function() {
                    selectedStudents.append(`<input type="hidden" name="mahasiswa_ids[]" value="${$(this).val()}">`);
                });
                
                selectedStudents.append(`<p><strong>${checkedBoxes.length}</strong> mahasiswa dipilih untuk penugasan massal.</p>`);
            } else {
                bulkAssignBtn.prop('disabled', true);
                selectedStudents.empty();
            }
        }
    </script>
@endsection
