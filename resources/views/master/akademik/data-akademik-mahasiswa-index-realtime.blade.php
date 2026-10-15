@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Stats cards */
        .bg-light-primary {
            background-color: rgba(67, 94, 190, 0.1);
        }
        
        .bg-light-success {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .bg-light-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        /* Card styling */
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

        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table th.text-center, .table td.text-center {
            text-align: center !important;
        }

        /* Button styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Form styling */
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }

        /* Badge styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* Search highlight */
        .search-highlight {
            background-color: yellow;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9 col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <div>
                        <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-success me-2" title="Export Excel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route($spref . 'akademik.data-akademik-mahasiswa-export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="btn btn-danger me-2" title="Export PDF">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal" title="Import Excel">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-filter me-2"></i>Filter & Pencarian Data
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Pencarian Real-time</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ $current_filters['search'] ?? '' }}" 
                                           placeholder="Ketik nama, email, atau NIM...">
                                    <small class="text-muted">Hasil akan muncul otomatis saat mengetik</small>
                                </div>
                                <div class="col-md-2">
                                    <label for="prodi_filter" class="form-label">Program Studi</label>
                                    <select class="form-select" id="prodi_filter" name="prodi_filter">
                                        <option value="">Semua Program Studi</option>
                                        @foreach ($program_studi as $prodi)
                                            <option value="{{ $prodi->id }}" 
                                                {{ ($current_filters['prodi_filter'] ?? '') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="kelas_filter" class="form-label">Kelas</label>
                                    <select class="form-select" id="kelas_filter" name="kelas_filter">
                                        <option value="">Semua Kelas</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" 
                                                {{ ($current_filters['kelas_filter'] ?? '') == $k->id ? 'selected' : '' }}>
                                                {{ $k->name }} - {{ $k->jenisKelas->name ?? 'Reguler' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="semester_filter" class="form-label">Semester</label>
                                    <select class="form-select" id="semester_filter" name="semester_filter">
                                        <option value="">Semua Semester</option>
                                        @foreach ($semester_list as $semester)
                                            <option value="{{ $semester }}" 
                                                {{ ($current_filters['semester_filter'] ?? '') == $semester ? 'selected' : '' }}>
                                                Semester {{ $semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="takhasus_filter" class="form-label">Takhasus</label>
                                    <select class="form-select" id="takhasus_filter" name="takhasus_filter">
                                        <option value="">Semua Takhasus</option>
                                        @foreach ($takhasus_list as $takhasus)
                                            <option value="{{ $takhasus }}" 
                                                {{ ($current_filters['takhasus_filter'] ?? '') == $takhasus ? 'selected' : '' }}>
                                                {{ $takhasus }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                                        <i class="fas fa-times me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mb-4" id="statsContainer">
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Mahasantri</h6>
                                <h3 class="mb-0" id="totalMahasiswa">{{ count($mahasiswa) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Sudah Ada NIM</h6>
                                <h3 class="mb-0" id="sudahAdaNim">{{ $mahasiswa->whereNotNull('numb_nim')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Belum Ada NIM</h6>
                                <h3 class="mb-0" id="belumAdaNim">{{ $mahasiswa->whereNull('numb_nim')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-3 bg-light-info rounded">
                                <h6 class="mb-2">Sudah Ada Kelas</h6>
                                <h3 class="mb-0" id="sudahAdaKelas">{{ $mahasiswa->whereNotNull('kelas_id')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="table-responsive" style="position: relative;">
                        <table class="table" id="mahasiswaTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Mahasantri</th>
                                    <th>NIM</th>
                                    <th>Program Studi</th>
                                    <th>Kelas</th>
                                    <th>Semester</th>
                                    <th>Takhasus</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaTableBody">
                                @foreach ($mahasiswa as $key => $item)
                                    <tr>
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Nama Mahasantri">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->photo }}" alt="{{ $item->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e9ecef;">
                                                <div>
                                                    <span class="fw-bold">{{ $item->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $item->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="NIM">
                                            @if($item->numb_nim)
                                                <span class="badge bg-light-success text-success">{{ $item->numb_nim }}</span>
                                            @else
                                                <span class="badge bg-light-warning text-warning">Belum Ada</span>
                                            @endif
                                        </td>
                                        <td data-label="Program Studi">{{ $item->prodi->name ?? '-' }}</td>
                                        <td data-label="Kelas">
                                            @if($item->kelas)
                                                {{ $item->kelas->name }} - {{ $item->kelas->jenisKelas->name ?? 'Reguler' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td data-label="Semester">{{ $item->semester ?? '-' }}</td>
                                        <td data-label="Takhasus">{{ $item->takhasusOption->name ?? $item->takhasus ?? '-' }}</td>
                                        <td data-label="Status" class="text-center">
                                            {!! $item->getTerminalStatusBadge() !!}
                                            @if($item->isTerminal())
                                                <div class="mt-1">
                                                    <small class="text-muted" title="Terminal sejak: {{ $item->terminal_date ? $item->terminal_date->format('d/m/Y H:i') : '-' }}">
                                                        <i class="fas fa-clock me-1"></i>{{ $item->terminal_date ? $item->terminal_date->format('d/m/Y') : '-' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" title="Edit Data Akademik">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editProfileData{{ $item->code }}" class="btn btn-sm btn-success" title="Edit Profile Lengkap">
                                                    <i class="fas fa-user-edit"></i>
                                                </a>
                                                @if($item->isTerminal())
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#terminalModal{{ $item->code }}" title="Nonaktifkan Terminal">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#terminalModal{{ $item->code }}" title="Aktifkan Terminal">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="col-lg-3 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini untuk mengelola data akademik mahasiswa seperti NIM, Program Studi, Kelas, dan Semester.</p>
                    
                    <div class="alert alert-light-info">
                        <h6 class="">Petunjuk:</h6>
                        <ul class="mb-0">
                            <li>Ketik di kolom pencarian untuk filter real-time</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data akademik mahasiswa</li>
                            <li>Data login mahasiswa dikelola di Master Pengguna</li>
                            <li>Data akademik hanya bisa diubah oleh admin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals will be included here - keeping the existing modals from original file -->
    @foreach ($mahasiswa as $item)
        <!-- Edit Academic Modal -->
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'akademik.data-akademik-mahasiswa-update', $item->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Akademik - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_numb_nim{{ $item->code }}" class="form-label">NIM <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="numb_nim" id="edit_numb_nim{{ $item->code }}" value="{{ $item->numb_nim }}" placeholder="Contoh: 2024001001">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_prodi_id{{ $item->code }}" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                    <select class="form-select" name="prodi_id" id="edit_prodi_id{{ $item->code }}">
                                        <option value="">Pilih Program Studi</option>
                                        @foreach ($program_studi as $p)
                                            <option value="{{ $p->id }}" {{ $item->prodi_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_kelas_id{{ $item->code }}" class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select" name="kelas_id" id="edit_kelas_id{{ $item->code }}">
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" {{ $item->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->name }} - {{ $k->jenisKelas->name ?? 'Reguler' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_semester{{ $item->code }}" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="semester" id="edit_semester{{ $item->code }}" min="1" max="14" value="{{ $item->semester }}" placeholder="Contoh: 1">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let searchTimeout;
            let originalData = [];
            
            // Store original data on page load
            storeOriginalData();
            
            // Real-time search functionality
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();
                
                searchTimeout = setTimeout(function() {
                    performRealTimeSearch(searchTerm);
                }, 300); // 300ms debounce
            });

            // Filter change events
            $('#prodi_filter, #kelas_filter, #semester_filter, #takhasus_filter').on('change', function() {
                const searchTerm = $('#search').val().trim();
                performRealTimeSearch(searchTerm);
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#prodi_filter').val('');
                $('#kelas_filter').val('');
                $('#semester_filter').val('');
                $('#takhasus_filter').val('');
                performRealTimeSearch('');
            });

            function storeOriginalData() {
                originalData = [];
                $('#mahasiswaTableBody tr').each(function(index) {
                    const row = $(this);
                    originalData.push({
                        html: row[0].outerHTML,
                        name: row.find('td:eq(1) .fw-bold').text().toLowerCase(),
                        email: row.find('td:eq(1) small').text().toLowerCase(),
                        nim: row.find('td:eq(2)').text().toLowerCase(),
                        prodi: row.find('td:eq(3)').text().toLowerCase(),
                        kelas: row.find('td:eq(4)').text().toLowerCase(),
                        semester: row.find('td:eq(5)').text().toLowerCase(),
                        takhasus: row.find('td:eq(6)').text().toLowerCase()
                    });
                });
            }

            function performRealTimeSearch(searchTerm) {
                console.log('Searching for:', searchTerm);
                
                // Show loading
                showLoading();
                
                // Get filter values
                const prodiFilter = $('#prodi_filter').val();
                const kelasFilter = $('#kelas_filter').val();
                const semesterFilter = $('#semester_filter').val();
                const takhasusFilter = $('#takhasus_filter').val();
                
                // If using AJAX (recommended for large datasets)
                if (originalData.length > 100) {
                    performAjaxSearch(searchTerm, prodiFilter, kelasFilter, semesterFilter, takhasusFilter);
                } else {
                    // Client-side filtering for smaller datasets
                    performClientSideSearch(searchTerm, prodiFilter, kelasFilter, semesterFilter, takhasusFilter);
                }
            }

            function performAjaxSearch(searchTerm, prodiFilter, kelasFilter, semesterFilter, takhasusFilter) {
                $.ajax({
                    url: '{{ route($spref . "akademik.data-akademik-mahasiswa-search") }}',
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        prodi_filter: prodiFilter,
                        kelas_filter: kelasFilter,
                        semester_filter: semesterFilter,
                        takhasus_filter: takhasusFilter
                    },
                    success: function(response) {
                        console.log('Search response:', response);
                        hideLoading();
                        
                        if (response.success && response.data) {
                            updateTableWithAjaxData(response.data);
                            updateStats(response.data);
                        } else {
                            showNoResults();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', error);
                        hideLoading();
                        showError('Terjadi kesalahan saat mencari data');
                    }
                });
            }

            function performClientSideSearch(searchTerm, prodiFilter, kelasFilter, semesterFilter, takhasusFilter) {
                let filteredData = originalData.filter(function(item) {
                    // Search filter
                    const matchesSearch = !searchTerm || 
                        item.name.includes(searchTerm.toLowerCase()) ||
                        item.email.includes(searchTerm.toLowerCase()) ||
                        item.nim.includes(searchTerm.toLowerCase());
                    
                    // Other filters would need to be implemented based on data attributes
                    // For now, just using search
                    return matchesSearch;
                });

                hideLoading();
                updateTableWithClientData(filteredData, searchTerm);
                updateClientStats(filteredData);
            }

            function updateTableWithAjaxData(data) {
                let tableHtml = '';
                
                if (data.length === 0) {
                    tableHtml = '<tr><td colspan="9" class="text-center py-4">Tidak ada data yang ditemukan</td></tr>';
                } else {
                    data.forEach(function(item, index) {
                        const nimBadge = item.numb_nim ? 
                            `<span class="badge bg-light-success text-success">${item.numb_nim}</span>` : 
                            `<span class="badge bg-light-warning text-warning">Belum Ada</span>`;
                        
                        const terminalInfo = item.is_terminal ? 
                            `<div class="mt-1"><small class="text-muted" title="Terminal sejak: ${item.terminal_date}"><i class="fas fa-clock me-1"></i>${item.terminal_date}</small></div>` : '';
                        
                        tableHtml += `
                            <tr>
                                <td data-label="No">${index + 1}</td>
                                <td data-label="Nama Mahasantri">
                                    <div class="d-flex align-items-center">
                                        <img src="${item.photo}" alt="${item.name}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e9ecef;">
                                        <div>
                                            <span class="fw-bold">${item.name}</span>
                                            <br>
                                            <small class="text-muted">${item.email}</small>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="NIM">${nimBadge}</td>
                                <td data-label="Program Studi">${item.prodi_name}</td>
                                <td data-label="Kelas">${item.kelas_name}</td>
                                <td data-label="Semester">${item.semester}</td>
                                <td data-label="Takhasus">${item.takhasus}</td>
                                <td data-label="Status" class="text-center">
                                    ${item.terminal_status}
                                    ${terminalInfo}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editData${item.code}" class="btn btn-sm btn-primary" title="Edit Data Akademik">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editProfileData${item.code}" class="btn btn-sm btn-success" title="Edit Profile Lengkap">
                                            <i class="fas fa-user-edit"></i>
                                        </a>
                                        ${item.is_terminal ? 
                                            `<button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#terminalModal${item.code}" title="Nonaktifkan Terminal">
                                                <i class="fas fa-unlock"></i>
                                            </button>` :
                                            `<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#terminalModal${item.code}" title="Aktifkan Terminal">
                                                <i class="fas fa-ban"></i>
                                            </button>`
                                        }
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#mahasiswaTableBody').html(tableHtml);
            }

            function updateTableWithClientData(filteredData, searchTerm) {
                let tableHtml = '';
                
                if (filteredData.length === 0) {
                    tableHtml = '<tr><td colspan="9" class="text-center py-4">Tidak ada data yang ditemukan</td></tr>';
                } else {
                    filteredData.forEach(function(item, index) {
                        let html = item.html;
                        
                        // Update row number
                        html = html.replace(/data-label="No">\d+/, `data-label="No">${index + 1}`);
                        
                        // Highlight search terms
                        if (searchTerm) {
                            const regex = new RegExp(`(${searchTerm})`, 'gi');
                            html = html.replace(regex, '<span class="search-highlight">$1</span>');
                        }
                        
                        tableHtml += html;
                    });
                }
                
                $('#mahasiswaTableBody').html(tableHtml);
            }

            function updateStats(data) {
                const total = data.length;
                const withNim = data.filter(item => item.numb_nim && item.numb_nim !== 'Belum Ada').length;
                const withoutNim = total - withNim;
                const withKelas = data.filter(item => item.kelas_name && item.kelas_name !== '-').length;
                
                $('#
totalMahasiswa').text(total);
                $('#sudahAdaNim').text(withNim);
                $('#belumAdaNim').text(withoutNim);
                $('#sudahAdaKelas').text(withKelas);
            }

            function updateClientStats(filteredData) {
                $('#totalMahasiswa').text(filteredData.length);
                // For client-side, we'd need to parse the HTML to get accurate counts
                // This is a simplified version
            }

            function showLoading() {
                if ($('.loading-overlay').length === 0) {
                    $('.table-responsive').append('<div class="loading-overlay"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
                }
            }

            function hideLoading() {
                $('.loading-overlay').remove();
            }

            function showNoResults() {
                $('#mahasiswaTableBody').html('<tr><td colspan="9" class="text-center py-4">Tidak ada data yang ditemukan</td></tr>');
            }

            function showError(message) {
                $('#mahasiswaTableBody').html(`<tr><td colspan="9" class="text-center py-4 text-danger">${message}</td></tr>`);
            }
        });
    </script>
@endsection
