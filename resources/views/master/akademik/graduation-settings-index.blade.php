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

        /* Button styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Badge styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Info box styling */
        .info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .info-box h4 {
            color: white;
            margin-bottom: 1rem;
        }

        .info-box .current-settings {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .current-settings .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .current-settings .setting-item:last-child {
            margin-bottom: 0;
        }

        .setting-value {
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <p class="text-muted mb-0">Atur persyaratan minimal untuk kelulusan mahasiswa</p>
                </div>
                <div class="card-body">
                    <!-- Current Settings Info Box -->
                    <div class="info-box">
                        <h4><i class="fas fa-graduation-cap me-2"></i>Pengaturan Kelulusan Saat Ini</h4>
                        <p class="mb-0">Berikut adalah persyaratan kelulusan yang sedang berlaku:</p>
                        
                        <div class="current-settings">
                            <div class="setting-item">
                                <span><i class="fas fa-chart-line me-2"></i>IPK Minimal:</span>
                                <span class="setting-value">{{ $settings->min_gpa_graduation ?? '2.75' }}</span>
                            </div>
                            <div class="setting-item">
                                <span><i class="fas fa-book me-2"></i>SKS Minimal:</span>
                                <span class="setting-value">{{ $settings->min_credits_graduation ?? '144' }} SKS</span>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route($spref . 'akademik.graduation-settings-update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="min_gpa_graduation" class="form-label">
                                    <i class="fas fa-chart-line me-1"></i>IPK Minimal Kelulusan <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('min_gpa_graduation') is-invalid @enderror" 
                                       name="min_gpa_graduation" 
                                       id="min_gpa_graduation" 
                                       value="{{ old('min_gpa_graduation', $settings->min_gpa_graduation ?? '2.75') }}" 
                                       step="0.01" 
                                       min="0" 
                                       max="4" 
                                       placeholder="Contoh: 2.75" 
                                       required>
                                @error('min_gpa_graduation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Rentang: 0.00 - 4.00</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="min_credits_graduation" class="form-label">
                                    <i class="fas fa-book me-1"></i>SKS Minimal Kelulusan <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('min_credits_graduation') is-invalid @enderror" 
                                       name="min_credits_graduation" 
                                       id="min_credits_graduation" 
                                       value="{{ old('min_credits_graduation', $settings->min_credits_graduation ?? '144') }}" 
                                       min="1" 
                                       max="300" 
                                       placeholder="Contoh: 144" 
                                       required>
                                @error('min_credits_graduation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Rentang: 1 - 300 SKS</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pengaturan Kelulusan</h5>
                </div>
                <div class="card-body">
                    <p>Pengaturan kelulusan menentukan persyaratan minimal yang harus dipenuhi mahasiswa untuk dapat lulus.</p>
                    
                    <div class="alert alert-light-info">
                        <h6 class=""><i class="fas fa-info-circle me-1"></i>Penjelasan:</h6>
                        <ul class="mb-0">
                            <li><strong>IPK Minimal:</strong> Indeks Prestasi Kumulatif minimal yang harus dicapai mahasiswa</li>
                            <li><strong>SKS Minimal:</strong> Jumlah Sistem Kredit Semester minimal yang harus diselesaikan</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-light-warning">
                        <h6 class=""><i class="fas fa-exclamation-triangle me-1"></i>Perhatian:</h6>
                        <ul class="mb-0">
                            <li>Perubahan pengaturan akan berlaku untuk semua mahasiswa</li>
                            <li>Pastikan nilai yang dimasukkan sesuai dengan kebijakan institusi</li>
                            <li>Konsultasikan dengan pihak akademik sebelum mengubah pengaturan</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h6><i class="fas fa-history me-1"></i>Standar Umum</h6>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">S1 (Sarjana)</h6>
                                    <small class="text-muted">Umum</small>
                                </div>
                                <p class="mb-1">IPK: 2.00 - 2.75, SKS: 144-160</p>
                                <small>Standar minimal perguruan tinggi</small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">D3 (Diploma)</h6>
                                    <small class="text-muted">Umum</small>
                                </div>
                                <p class="mb-1">IPK: 2.00 - 2.50, SKS: 108-120</p>
                                <small>Standar program diploma</small>
                            </div>
                        </div>
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
        $(document).ready(function() {
            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);

            // Form validation
            $('form').on('submit', function(e) {
                const minGpa = parseFloat($('#min_gpa_graduation').val());
                const minCredits = parseInt($('#min_credits_graduation').val());

                if (minGpa < 0 || minGpa > 4) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Nilai IPK Tidak Valid',
                        text: 'IPK minimal harus berada dalam rentang 0.00 - 4.00',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                if (minCredits < 1 || minCredits > 300) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Nilai SKS Tidak Valid',
                        text: 'SKS minimal harus berada dalam rentang 1 - 300',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Confirmation dialog
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: `Apakah Anda yakin ingin mengubah pengaturan kelulusan menjadi IPK minimal ${minGpa} dan SKS minimal ${minCredits}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        e.target.submit();
                    }
                });
            });

            // Real-time validation feedback
            $('#min_gpa_graduation').on('input', function() {
                const value = parseFloat($(this).val());
                const feedback = $(this).siblings('.invalid-feedback');
                
                if (value < 0 || value > 4) {
                    $(this).addClass('is-invalid');
                    if (feedback.length === 0) {
                        $(this).after('<div class="invalid-feedback">IPK harus berada dalam rentang 0.00 - 4.00</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    feedback.remove();
                }
            });

            $('#min_credits_graduation').on('input', function() {
                const value = parseInt($(this).val());
                const feedback = $(this).siblings('.invalid-feedback');
                
                if (value < 1 || value > 300) {
                    $(this).addClass('is-invalid');
                    if (feedback.length === 0) {
                        $(this).after('<div class="invalid-feedback">SKS harus berada dalam rentang 1 - 300</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    feedback.remove();
                }
            });
        });
    </script>
@endsection
