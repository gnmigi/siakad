@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --border-radius: 12px;
        }

        /* Light theme variables */
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --avatar-border: #ffffff;
            --scrollbar-track: #f1f5f9;
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-thumb-hover: #94a3b8;
        }

        /* Dark theme variables */
        [data-bs-theme="dark"] {
            --bg-color: #1f2937;
            --card-bg: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #4b5563;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --avatar-border: #374151;
            --scrollbar-track: #374151;
            --scrollbar-thumb: #6b7280;
            --scrollbar-thumb-hover: #9ca3af;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .modern-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            border: none;
            overflow: hidden;
        }

        .modern-card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .modern-card-body {
            padding: 1.5rem;
        }

        .class-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .class-stats {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .student-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--avatar-border);
            box-shadow: 0 2px 8px var(--shadow-color);
        }

        .student-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-color);
        }

        .student-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-details h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .student-details small {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .modern-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modern-btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .modern-btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .modern-btn-success {
            background: var(--success-color);
            color: white;
        }

        .modern-btn-success:hover {
            background: #059669;
        }

        .modern-btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .modern-btn-danger:hover {
            background: #dc2626;
        }

        .modern-btn-outline {
            background: transparent;
            border: 2px solid var(--text-secondary);
            color: var(--text-secondary);
        }

        .modern-btn-outline:hover {
            background: var(--text-secondary);
            color: var(--card-bg);
        }

        .search-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-secondary);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-available {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-enrolled {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }

        .status-other {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .students-grid {
            display: grid;
            gap: 1rem;
        }

        .current-students-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .current-students-list::-webkit-scrollbar {
            width: 6px;
        }

        .current-students-list::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 3px;
        }

        .current-students-list::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 3px;
        }

        .current-students-list::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        .action-buttons {
            position: sticky;
            bottom: 0;
            background: var(--card-bg);
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            margin: -1.5rem -1.5rem 0;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            accent-color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Dark theme specific adjustments */
        [data-bs-theme="dark"] .student-card.selected {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
        }

        [data-bs-theme="dark"] .status-available {
            background: rgba(16, 185, 129, 0.2);
        }

        [data-bs-theme="dark"] .status-enrolled {
            background: rgba(59, 130, 246, 0.2);
        }

        [data-bs-theme="dark"] .status-other {
            background: rgba(245, 158, 11, 0.2);
        }

        /* Form elements dark mode support */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }

        /* Text color adjustments */
        [data-bs-theme="dark"] .text-muted {
            color: var(--text-secondary) !important;
        }

        [data-bs-theme="dark"] .fw-bold {
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .class-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .student-info {
                flex-direction: column;
                text-align: center;
            }

            .modern-card-body {
                padding: 1rem;
            }

            .action-buttons {
                padding: 0.75rem;
                margin: -1rem -1rem 0;
            }

            .search-container {
                margin-bottom: 1rem;
            }

            .modern-btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .modern-btn-danger.btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }

            .checkbox-custom {
                width: 18px;
                height: 18px;
            }

            .student-card {
                padding: 1rem;
                margin-bottom: 0.75rem;
            }

            .student-avatar {
                width: 40px;
                height: 40px;
            }

            .current-students-list {
                max-height: 400px;
            }

            /* Fix for mobile layout stacking */
            .d-flex.flex-column.flex-sm-row {
                gap: 0.75rem;
            }

            .d-flex.flex-column.flex-sm-row .modern-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .class-info-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .modern-card-header {
                padding: 1rem;
            }

            .modern-card-header .d-flex {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch !important;
            }

            .modern-btn-outline {
                text-align: center;
            }

            .search-input {
                padding: 0.75rem 0.75rem 0.75rem 2.5rem;
                font-size: 0.9rem;
            }

            .search-icon {
                left: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Class Information Header -->
    <div class="class-info-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">{{ $kelas->name }}</h2>
                <p class="mb-1 opacity-90">{{ $kelas->programStudi->name ?? 'Program Studi Tidak Ditemukan' }}</p>
                <p class="mb-0 opacity-75">{{ $kelas->tahunAkademik->name ?? 'Tahun Akademik Tidak Ditemukan' }} - {{ $kelas->jenisKelas->name ?? 'Jenis Kelas Tidak Ditemukan' }}</p>
            </div>
            <div class="col-md-4">
                <div class="class-stats justify-content-md-end">
                    <div class="stat-item">
                        <div class="stat-number">{{ $current_students->count() }}</div>
                        <div class="stat-label">Mahasiswa Terdaftar</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $kelas->capacity }}</div>
                        <div class="stat-label">Kapasitas Maksimal</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Available Students Section -->
        <div class="col-lg-8 mb-4">
            <div class="modern-card">
                <div class="modern-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah Mahasiswa ke Kelas
                        </h5>
                        <a href="{{ route($spref . 'akademik.kelas-render') }}" class="modern-btn modern-btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="modern-card-body">
                    <!-- Search Box -->
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="searchStudent" placeholder="Cari mahasiswa berdasarkan nama atau NIM...">
                    </div>

                    <!-- Students List -->
                    <form action="{{ route($spref . 'akademik.kelas-students-add', $kelas->code) }}" method="POST" id="addStudentsForm">
                        @csrf
                        <div class="students-grid" id="availableStudentsList">
                            @forelse ($available_students as $student)
                                <div class="student-card" data-student-name="{{ strtolower($student->name) }}" data-student-nim="{{ strtolower($student->nim ?? '') }}">
                                    <div class="student-info">
                                        <input type="checkbox" class="checkbox-custom student-checkbox" name="student_ids[]" value="{{ $student->id }}">
                                        
                                        <img src="{{ $student->photo }}" 
                                             alt="{{ $student->name }}" 
                                             class="student-avatar"
                                             onerror="this.src='{{ asset('storage/images/profile/default.jpg') }}'">

                                        <div class="student-details flex-grow-1">
                                            <h6>{{ $student->name }}</h6>
                                            <small>NIM: {{ $student->nim ?? 'Tidak tersedia' }}</small>
                                        </div>

                                        <div class="ms-auto">
                                            @if($student->kelas_id == 0)
                                                <span class="status-badge status-available">Tersedia</span>
                                            @elseif($student->kelas_id == $kelas->id)
                                                <span class="status-badge status-enrolled">Sudah Terdaftar</span>
                                            @else
                                                <span class="status-badge status-other">Kelas Lain</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p>Tidak ada mahasiswa yang tersedia</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="action-buttons">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="checkbox-custom" id="selectAll">
                                    <label for="selectAll" class="ms-2">Pilih Semua</label>
                                </div>
                                <button type="button" class="modern-btn modern-btn-success" id="addSelectedStudents" disabled>
                                    <i class="fas fa-plus"></i>
                                    Tambah Mahasiswa Terpilih
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Students Section -->
        <div class="col-lg-4 mb-4">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Mahasiswa di Kelas ({{ $current_students->count() }})
                    </h5>
                </div>
                <div class="modern-card-body p-0">
                    @if($current_students->count() > 0)
                        <div class="current-students-list">
                            @foreach($current_students as $student)
                                <div class="student-card mx-3 my-2" data-current-student-name="{{ strtolower($student->name) }}" data-current-student-nim="{{ strtolower($student->nim ?? '') }}">
                                    <div class="student-info">
                                        <input type="checkbox" class="checkbox-custom current-student-checkbox" name="remove_student_ids[]" value="{{ $student->id }}">
                                        
                                        <img src="{{ $student->photo }}" 
                                             alt="{{ $student->name }}" 
                                             class="student-avatar"
                                             onerror="this.src='{{ asset('storage/images/profile/default.jpg') }}'">

                                        <div class="student-details flex-grow-1">
                                            <h6>{{ $student->name }}</h6>
                                            <small>NIM: {{ $student->nim ?? 'Tidak tersedia' }}</small>
                                        </div>

                                        <form action="{{ route($spref . 'akademik.kelas-student-remove', [$kelas->code, $student->id]) }}" 
                                              method="POST" class="d-inline" id="remove-form-{{ $student->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="modern-btn modern-btn-danger" 
                                                    onclick="confirmRemove('{{ $student->id }}', '{{ $student->name }}')"
                                                    title="Keluarkan dari kelas">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Multi-select Remove Actions -->
                        <div class="action-buttons">
                            <!-- Compact Search and Actions in One Row -->
                            <div class="row g-2 align-items-center">
                                <!-- Search Column -->
                                <div class="col-12 col-md-7">
                                    <div class="search-container mb-0">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="search-input" id="searchCurrentStudent" placeholder="Cari mahasiswa...">
                                    </div>
                                </div>
                                
                                <!-- Actions Column -->
                                <div class="col-12 col-md-5">
                                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" class="checkbox-custom" id="selectAllCurrent">
                                            <label for="selectAllCurrent" class="ms-2 mb-0 text-nowrap small">Pilih Semua</label>
                                        </div>
                                        <button type="button" class="modern-btn modern-btn-danger btn-sm w-100 w-md-auto" id="removeSelectedStudents" disabled>
                                            <i class="fas fa-trash"></i>
                                            <span class="d-none d-md-inline">Keluarkan</span>
                                            <span class="d-inline d-md-none">Keluarkan Terpilih</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p>Belum ada mahasiswa di kelas ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Class Information Card -->
            <div class="modern-card mt-3">
                <div class="modern-card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Kelas
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted">Kode Kelas</small>
                            <div class="fw-bold">{{ $kelas->code }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Kapasitas</small>
                            <div class="fw-bold">{{ $kelas->capacity }} mahasiswa</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Terisi</small>
                            <div class="fw-bold text-primary">{{ $current_students->count() }} mahasiswa</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Sisa Slot</small>
                            <div class="fw-bold text-success">{{ $kelas->capacity - $current_students->count() }} slot</div>
                        </div>
                        @if($kelas->ketua)
                        <div class="col-12">
                            <small class="text-muted">Ketua Kelas</small>
                            <div class="fw-bold">{{ $kelas->ketua->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fix theme toggle to prevent page reload and errors
        document.addEventListener('DOMContentLoaded', function() {
            // Override theme toggle links to prevent page reload
            const themeLinks = document.querySelectorAll('a[href*="theme="]');
            themeLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const theme = this.href.includes('theme=dark') ? 'dark' : 'light';
                    
                    // Use Tabler's theme system
                    if (window.tabler && window.tabler.setTheme) {
                        window.tabler.setTheme(theme);
                    } else {
                        // Fallback method
                        document.documentElement.setAttribute('data-bs-theme', theme);
                        localStorage.setItem('tablerTheme', theme);
                    }
                });
            });
        });
    </script>

    <script>
        // Define variables from PHP
        const kelasCapacity = {{ $kelas->capacity ?? 0 }};
        const currentStudentsCount = {{ $current_students->count() ?? 0 }};
        
        $(document).ready(function() {
            // Search functionality for available students
            $('#searchStudent').on('input keyup', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                console.log('Searching available students for:', searchTerm);
                
                $('#availableStudentsList .student-card').each(function() {
                    const $card = $(this);
                    const studentName = ($card.data('student-name') || '').toString().toLowerCase();
                    const studentNim = ($card.data('student-nim') || '').toString().toLowerCase();
                    
                    // Also get text content from the card as fallback
                    const cardText = $card.text().toLowerCase();
                    
                    console.log('Checking student:', studentName, studentNim, 'Card text:', cardText);
                    
                    // Show all if search is empty, otherwise check multiple ways
                    if (searchTerm === '' || 
                        studentName.indexOf(searchTerm) !== -1 || 
                        studentNim.indexOf(searchTerm) !== -1 ||
                        cardText.indexOf(searchTerm) !== -1) {
                        $card.show();
                    } else {
                        $card.hide();
                    }
                });
                
                // Update select all state after search
                updateSelectAllState();
            });

            // Search functionality for current students
            $('#searchCurrentStudent').on('input keyup', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                console.log('Searching current students for:', searchTerm);
                
                $('.current-students-list .student-card').each(function() {
                    const $card = $(this);
                    const studentName = ($card.data('current-student-name') || '').toString().toLowerCase();
                    const studentNim = ($card.data('current-student-nim') || '').toString().toLowerCase();
                    
                    // Also get text content from the card as fallback
                    const cardText = $card.text().toLowerCase();
                    
                    console.log('Checking current student:', studentName, studentNim, 'Card text:', cardText);
                    
                    // Show all if search is empty, otherwise check multiple ways
                    if (searchTerm === '' || 
                        studentName.indexOf(searchTerm) !== -1 || 
                        studentNim.indexOf(searchTerm) !== -1 ||
                        cardText.indexOf(searchTerm) !== -1) {
                        $card.show();
                    } else {
                        $card.hide();
                    }
                });
                
                // Update select all state after search
                updateSelectAllCurrentState();
            });

            // Checkbox interactions for available students
            $('.student-checkbox').on('change', function() {
                const card = $(this).closest('.student-card');
                if ($(this).is(':checked')) {
                    card.addClass('selected');
                } else {
                    card.removeClass('selected');
                }
                updateSelectAllState();
                updateAddButton();
            });

            // Checkbox interactions for current students
            $('.current-student-checkbox').on('change', function() {
                const card = $(this).closest('.student-card');
                if ($(this).is(':checked')) {
                    card.addClass('selected');
                } else {
                    card.removeClass('selected');
                }
                updateSelectAllCurrentState();
                updateRemoveButton();
            });

            // Select all functionality for available students
            $('#selectAll').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#availableStudentsList .student-checkbox:visible').prop('checked', isChecked);
                $('#availableStudentsList .student-card:visible').toggleClass('selected', isChecked);
                updateAddButton();
            });

            // Select all functionality for current students
            $('#selectAllCurrent').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.current-student-checkbox:visible').prop('checked', isChecked);
                $('.current-students-list .student-card:visible').toggleClass('selected', isChecked);
                updateRemoveButton();
            });

            // Update select all state for available students
            function updateSelectAllState() {
                const totalVisible = $('.student-checkbox:visible').length;
                const checkedVisible = $('.student-checkbox:visible:checked').length;
                
                $('#selectAll').prop('indeterminate', checkedVisible > 0 && checkedVisible < totalVisible);
                $('#selectAll').prop('checked', checkedVisible === totalVisible && totalVisible > 0);
            }

            // Update select all state for current students
            function updateSelectAllCurrentState() {
                const totalVisible = $('.current-student-checkbox:visible').length;
                const checkedVisible = $('.current-student-checkbox:visible:checked').length;
                
                $('#selectAllCurrent').prop('indeterminate', checkedVisible > 0 && checkedVisible < totalVisible);
                $('#selectAllCurrent').prop('checked', checkedVisible === totalVisible && totalVisible > 0);
            }

            // Update add button state
            function updateAddButton() {
                const checkedCount = $('.student-checkbox:checked').length;
                const button = $('#addSelectedStudents');
                
                button.prop('disabled', checkedCount === 0);
                
                if (checkedCount > 0) {
                    button.html('<i class="fas fa-plus"></i> Tambah ' + checkedCount + ' Mahasiswa');
                } else {
                    button.html('<i class="fas fa-plus"></i> Tambah Mahasiswa Terpilih');
                }
            }

            // Update remove button state
            function updateRemoveButton() {
                const checkedCount = $('.current-student-checkbox:checked').length;
                const button = $('#removeSelectedStudents');
                
                button.prop('disabled', checkedCount === 0);
                
                if (checkedCount > 0) {
                    button.html('<i class="fas fa-trash"></i> Keluarkan ' + checkedCount + ' Mahasiswa');
                } else {
                    button.html('<i class="fas fa-trash"></i> Keluarkan Terpilih');
                }
            }

            // Add selected students
            $('#addSelectedStudents').on('click', function() {
                const checkedCount = $('.student-checkbox:checked').length;
                const capacity = kelasCapacity;
                const currentCount = currentStudentsCount;
                const remaining = capacity - currentCount;

                if (checkedCount > remaining) {
                    Swal.fire({
                        title: 'Kapasitas Tidak Mencukupi!',
                        text: 'Anda memilih ' + checkedCount + ' mahasiswa, tetapi kelas hanya tersisa ' + remaining + ' tempat.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penambahan',
                    text: 'Apakah Anda yakin ingin menambahkan ' + checkedCount + ' mahasiswa ke kelas ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Tambahkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#addStudentsForm').submit();
                    }
                });
            });

            // Remove selected students
            $('#removeSelectedStudents').on('click', function() {
                const checkedCount = $('.current-student-checkbox:checked').length;
                const selectedIds = [];
                
                $('.current-student-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (checkedCount === 0) return;

                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    text: 'Apakah Anda yakin ingin mengeluarkan ' + checkedCount + ' mahasiswa dari kelas ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Keluarkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a form to submit the bulk removal
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route($spref . "akademik.kelas-students-remove", $kelas->code) }}'
                        });
                        
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));
                        
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }));
                        
                        // Add selected student IDs
                        selectedIds.forEach(function(id) {
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'student_ids[]',
                                value: id
                            }));
                        });
                        
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });

        // Confirm remove student
        function confirmRemove(studentId, studentName) {
            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                text: 'Apakah Anda yakin ingin mengeluarkan ' + studentName + ' dari kelas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Keluarkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove-form-' + studentId).submit();
                }
            });
        }
    </script>
@endsection
