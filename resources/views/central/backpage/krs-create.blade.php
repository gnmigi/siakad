@extends('central.backpage.core-backpage-mahasiswa')

@section('custom-css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Base Card Styling */
        .krs-card {
            background: var(--tblr-card-bg, white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            margin-bottom: 1.5rem;
            color: var(--tblr-body-color);
        }
        
        /* Course Item Styling */
        .course-item {
            background: var(--tblr-card-bg, white);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            height: calc(100% - 1rem);
        }
        
        .course-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: var(--tblr-primary);
        }
        
        .course-item.selected {
            border-color: var(--tblr-primary);
            background: rgba(var(--tblr-primary-rgb), 0.05);
            box-shadow: 0 0 0 1px var(--tblr-primary);
        }
        
        .course-code {
            font-weight: 700;
            color: var(--tblr-primary);
            font-size: 0.9rem;
        }
        
        .course-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--tblr-body-color);
            line-height: 1.3;
        }
        
        .course-details {
            font-size: 0.8rem;
            color: var(--tblr-muted);
            line-height: 1.5;
        }
        
        .sks-badge {
            background: var(--tblr-primary);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Selected Summary & Items */
        .selected-summary {
            position: sticky;
            top: 20px;
            background: var(--tblr-card-bg, white);
            border: 1px solid var(--tblr-border-color, #e2e8f0);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .selected-course-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: background 0.3s;
        }

        .selected-course-code {
            font-weight: 600;
            color: var(--tblr-primary);
            font-size: 0.85rem;
        }

        .selected-course-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--tblr-body-color);
        }

        /* --- CSS SAFETY LOCK --- */
        .course-items-container.is-locked .course-item {
            opacity: 0.65;
            cursor: not-allowed;
            filter: grayscale(10%);
        }
        .course-items-container.is-locked .course-item:hover {
            box-shadow: none;
            border-color: var(--tblr-border-color, #e2e8f0);
            transform: none;
        }
        .course-items-container.is-locked .course-item.selected {
            border-color: var(--tblr-primary);
        }

        /* --- PANEL UNLOCK DI RINGKASAN --- */
        .unlock-panel {
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        /* ==========================================
           DARK MODE OVERRIDES
           ========================================== */
        [data-bs-theme="dark"] .krs-card,
        [data-bs-theme="dark"] .selected-summary {
            background: #182433;
            border-color: #2f3b4c;
        }

        [data-bs-theme="dark"] .course-item {
            background: #1e293b;
            border-color: #334155;
        }
        
        [data-bs-theme="dark"] .course-item.selected {
            background: rgba(32, 107, 196, 0.15);
        }

        [data-bs-theme="dark"] .selected-course-item {
            background: #1e293b; 
            border-color: #334155;
        }
        [data-bs-theme="dark"] .selected-course-name {
            color: #e2e8f0;
        }
        
        [data-bs-theme="dark"] .unlock-panel {
            background-color: #1e293b;
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .unlock-panel .text-body {
            color: #f8fafc !important;
        }

        /* ==========================================
           MOBILE RESPONSIVENESS
           ========================================== */
        @media (max-width: 768px) {
            .krs-card {
                padding: 1rem;
            }
            .course-item {
                padding: 0.75rem;
            }
            .course-name {
                font-size: 0.95rem;
            }
            .selected-summary {
                margin-bottom: 2rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Akademik
                    </div>
                    <h2 class="page-title">
                        Buat KRS Baru
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('mahasiswa.krs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ isset($krs) ? route('mahasiswa.krs.update', $krs->id) : route('mahasiswa.krs.store') }}" method="POST" id="krsForm">
        @csrf
        @if(isset($krs))
            @method('PUT')
        @endif
        <input type="hidden" name="semester" value="{{ $selectedSemester ?? $mahasiswa->semester }}">
        
        <div class="row">
            <div class="col-lg-8">
                <div class="krs-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="m-0 fs-3">
                            <i class="fas fa-book me-2 text-success"></i>
                            Pilih Mata Kuliah
                        </h3>
                        <span class="badge bg-success-lt px-2 py-1 fs-5"><i class="fas fa-lock me-1"></i> Paket Terkunci</span>
                    </div>
                    
                    <p class="text-muted mb-4 fs-5" style="line-height: 1.6;">
                        Mata kuliah di bawah ini sudah dipilih otomatis sesuai paket semester {{ $selectedSemester ?? $mahasiswa->semester ?? '-' }}. 
                        <strong class="text-body">Jika tidak ada perubahan, silakan langsung <i>ke bawah</i> untuk menyimpan KRS.</strong>
                    </p>
                    
                    @if($availableCourses->count() > 0)
                        <div class="row course-items-container is-locked" id="courseContainer">
                            @foreach($availableCourses as $course)
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="course-item" data-course-id="{{ $course->id }}" data-sks="{{ $course->bsks ?? $course->sks ?? 0 }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="course-code">{{ $course->code ?? '-' }}</div>
                                            <span class="sks-badge">{{ $course->bsks ?? $course->sks ?? 0 }} SKS</span>
                                        </div>
                                        <div class="course-name">{{ $course->name ?? '-' }}</div>
                                        <div class="course-details">
                                            <div class="mb-1">
                                                <i class="fas fa-user text-muted w-4"></i> 
                                                @if($course->dosen1)
                                                    {{ $course->dosen1->title_front ? $course->dosen1->title_front . ' ' : '' }}{{ $course->dosen1->name }}{{ $course->dosen1->title_behind ? ', ' . $course->dosen1->title_behind : '' }}
                                                @else
                                                    Belum ditentukan
                                                @endif
                                            </div>
                                            <div><i class="fas fa-calendar text-muted w-4"></i> Semester {{ $course->semester ?? '-' }}</div>
                                            @if($course->prasyarat)
                                                <div class="mt-1"><i class="fas fa-link text-warning w-4"></i> Prasyarat: {{ $course->prasyarat->name }}</div>
                                            @endif
                                        </div>
                                        <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $course->id }}" style="display: none;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4>Tidak Ada Mata Kuliah</h4>
                            <p>Tidak ada mata kuliah yang tersedia untuk semester ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="selected-summary">
                    <h4 class="mb-4 fs-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        Ringkasan KRS
                    </h4>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Semester</div>
                            <div class="h4 mb-0">{{ $selectedSemester ?? $mahasiswa->semester ?? '-' }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-muted small">Program Studi</div>
                            <div class="h5 mb-0 text-truncate" title="{{ $mahasiswa->prodi->name ?? '-' }}">{{ $mahasiswa->prodi->name ?? '-' }}</div>
                        </div>
                    </div>
                    
                    <hr class="my-3">

                    <div class="unlock-panel p-3 mb-4 border rounded d-flex justify-content-between align-items-center shadow-sm">
                        <div>
                            <div class="fw-bold mb-1 text-body fs-4"><i class="fas fa-sliders-h me-2 text-primary"></i>Ubah Matkul</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Buka kunci untuk tambah/hapus SKS</div>
                        </div>
                        <div class="form-check form-switch m-0" style="font-size: 1.5rem;">
                            <input class="form-check-input" type="checkbox" id="unlockEditMode" style="cursor: pointer;">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Mata Kuliah Dipilih:</span>
                        <span id="selectedCount" class="badge bg-blue-lt fs-5">0</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-muted">Total SKS:</span>
                        <span id="totalSks" class="badge bg-primary text-white fs-4">0</span>
                    </div>
                    
                    <div id="selectedCourses" class="mb-4" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                        </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i>
                            Simpan KRS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('custom-js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseItems = document.querySelectorAll('.course-item');
            const selectedCountEl = document.getElementById('selectedCount');
            const totalSksEl = document.getElementById('totalSks');
            const selectedCoursesEl = document.getElementById('selectedCourses');
            const submitBtn = document.getElementById('submitBtn');
            const courseContainer = document.getElementById('courseContainer');
            const unlockCheckbox = document.getElementById('unlockEditMode');
            
            let selectedCourses = [];
            let totalSks = 0;
            let isLocked = true; 

            // Event Listener untuk Checkbox Kunci
            if(unlockCheckbox) {
                unlockCheckbox.addEventListener('change', function() {
                    isLocked = !this.checked;
                    
                    if (isLocked) {
                        courseContainer.classList.add('is-locked');
                    } else {
                        courseContainer.classList.remove('is-locked');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Kunci Dibuka, Mode Edit Aktif',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }

            // Inisialisasi Data
            const isEditMode = {{ isset($krs) ? 'true' : 'false' }};
            const existingCourseIds = @if(isset($krs)) {!! json_encode($krs->details->pluck('mata_kuliah_id')->toArray()) !!} @else [] @endif;

            courseItems.forEach(item => {
                const courseId = item.dataset.courseId;
                const checkbox = item.querySelector('input[type="checkbox"]');
                const courseSks = parseInt(item.dataset.sks) || 0;
                const courseName = item.querySelector('.course-name').textContent;
                const courseCode = item.querySelector('.course-code').textContent;

                let shouldSelect = false;
                if (isEditMode) {
                    shouldSelect = existingCourseIds.includes(parseInt(courseId));
                } else {
                    shouldSelect = !item.classList.contains('selected');
                }

                if (shouldSelect) {
                    item.classList.add('selected');
                    checkbox.checked = true;
                    selectedCourses.push({ id: courseId, name: courseName, code: courseCode, sks: courseSks });
                    totalSks += courseSks;
                }
            });

            updateSummary();

            courseItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (isLocked) {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'warning',
                            title: '"Matkul" dikunci karena SKS sudah disesuaikan, tapi jika ada arahan SKS berbeda, silahkan buka kunci di panel sebelah kanan/bawah terlebih dahulu!',
                            showConfirmButton: false,
                            timer: 2500
                        });
                        this.style.transform = 'translateX(5px)';
                        setTimeout(() => this.style.transform = 'translateX(-5px)', 100);
                        setTimeout(() => this.style.transform = 'none', 200);
                        return; 
                    }

                    const courseId = this.dataset.courseId;
                    const courseSks = parseInt(this.dataset.sks) || 0;
                    const courseName = this.querySelector('.course-name').textContent;
                    const courseCode = this.querySelector('.course-code').textContent;
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        checkbox.checked = false;
                        selectedCourses = selectedCourses.filter(course => course.id !== courseId);
                        totalSks -= courseSks;
                    } else {
                        this.classList.add('selected');
                        checkbox.checked = true;
                        selectedCourses.push({ id: courseId, name: courseName, code: courseCode, sks: courseSks });
                        totalSks += courseSks;
                    }
                    
                    updateSummary();
                });
            });
            
            function updateSummary() {
                selectedCountEl.textContent = selectedCourses.length;
                totalSksEl.textContent = totalSks;
                
                selectedCoursesEl.innerHTML = '';
                selectedCourses.forEach(course => {
                    const courseEl = document.createElement('div');
                    courseEl.className = 'selected-course-item d-flex justify-content-between align-items-center';
                    courseEl.innerHTML = `
                        <div style="width: calc(100% - 60px);">
                            <div class="selected-course-code">${course.code}</div>
                            <div class="selected-course-name text-truncate" title="${course.name}">${course.name}</div>
                        </div>
                        <div class="selected-course-sks fw-bold badge bg-blue-lt">${course.sks} SKS</div>
                    `;
                    selectedCoursesEl.appendChild(courseEl);
                });
                
                submitBtn.disabled = selectedCourses.length === 0;
            }
            
            let isSubmitting = false;
            document.getElementById('krsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                
                if (selectedCourses.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan!', text: 'Pilih minimal satu mata kuliah.' });
                    return false;
                }
                if (totalSks > 24) {
                    Swal.fire({ icon: 'error', title: 'SKS Melebihi Batas!', text: 'Total SKS maksimal 24. Anda memilih ' + totalSks + ' SKS.' });
                    return false;
                }
                
                const actionText = isEditMode ? 'memperbarui' : 'menyimpan';
                
                Swal.fire({
                    title: isEditMode ? 'Perbarui KRS?' : 'Simpan KRS?',
                    text: `Apakah Anda yakin ingin ${actionText} KRS ini dengan ${selectedCourses.length} mata kuliah (${totalSks} SKS)?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Ya, ${actionText}!`,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true;
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                        
                        const form = document.getElementById('krsForm');
                        fetch(form.action, {
                            method: form.method,
                            body: new FormData(form),
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        }).then(async response => {
                            if (!response.ok) throw new Error('Gagal menyimpan KRS draft');
                            
                            let createdKrsId = null;
                            try {
                                const data = await response.clone().json();
                                createdKrsId = data?.krs_id || data?.id || null;
                            } catch (e) {}

                            if (!createdKrsId) {
                                window.location.href = '{{ route("mahasiswa.krs.index") }}';
                                return;
                            }

                            const submitChoice = await Swal.fire({
                                title: 'Berhasil Disimpan',
                                text: 'KRS berhasil disimpan ke draft. Ingin langsung submit (ajukan) ke admin sekarang?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#198754',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Ajukan Sekarang',
                                cancelButtonText: 'Nanti Dulu',
                                reverseButtons: true
                            });

                            if (!submitChoice.isConfirmed) {
                                window.location.href = '{{ route("mahasiswa.krs.index") }}';
                                return;
                            }

                            Swal.fire({
                                title: 'Mengajukan KRS...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => Swal.showLoading()
                            });

                            const submitUrl = '{{ route("mahasiswa.krs.submit", ":id") }}'.replace(':id', createdKrsId);
                            const submitResp = await fetch(submitUrl, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });

                            if (!submitResp.ok) throw new Error('Draft tersimpan, tapi gagal diajukan.');

                            await Swal.fire({ icon: 'success', title: 'Berhasil', text: 'KRS berhasil diajukan!' });
                            window.location.href = '{{ route("mahasiswa.krs.index") }}';
                            
                        }).catch(async error => {
                            isSubmitting = false;
                            await Swal.fire({ icon: 'error', title: 'Error!', text: error?.message || 'Terjadi kesalahan.' });
                            window.location.href = '{{ route("mahasiswa.krs.index") }}';
                        });
                    }
                });
            });
        });
    </script>
@endsection