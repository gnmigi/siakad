@extends('central.backpage.core-backpage-mahasiswa')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">
          Akademik
        </div>
        <h2 class="page-title">
          Status Kelulusan
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    
    <!-- Student Info Card -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <h3 class="card-title mb-3">Informasi Mahasantri</h3>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-2">
                      <strong>NIM:</strong> {{ $mahasiswa->numb_nim }}
                    </div>
                    <div class="mb-2">
                      <strong>Nama:</strong> {{ $mahasiswa->name }}
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-2">
                      <strong>Program Studi:</strong> {{ $mahasiswa->prodi->name ?? '-' }}
                    </div>
                    <div class="mb-2">
                      <strong>Kelas:</strong> {{ $mahasiswa->kelas->name ?? '-' }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-end">
                <div class="mb-2">
                  <strong>Status Kelulusan:</strong>
                  <span class="badge bg-{{ $graduationStatus ? 'success' : 'danger' }} fs-3">
                    {{ $graduationStatus ? 'LULUS' : 'BELUM LULUS' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graduation Requirements Status -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Persyaratan Kelulusan</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- IPK Requirement -->
              <div class="col-md-6 mb-3">
                <div class="card {{ $gpaStatus ? 'border-success' : 'border-danger' }}">
                  <div class="card-body text-center">
                    <div class="mb-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-{{ $gpaStatus ? 'success' : 'danger' }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 12l2 2l4 -4"/>
                        <path d="M21 12c0 4.97 -4.03 9 -9 9s-9 -4.03 -9 -9s4.03 -9 9 -9s9 4.03 9 9z"/>
                      </svg>
                    </div>
                    <h4 class="card-title">IPK Minimum</h4>
                    <div class="text-muted mb-2">Persyaratan: ≥ {{ number_format($minGPA, 2) }}</div>
                    <div class="h2 mb-0 text-{{ $gpaStatus ? 'success' : 'danger' }}">
                      {{ number_format($cumulativeGPA, 2) }}
                    </div>
                    <div class="badge bg-{{ $gpaStatus ? 'success' : 'danger' }} mt-2">
                      {{ $gpaStatus ? 'MEMENUHI' : 'BELUM MEMENUHI' }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Credits Requirement -->
              <div class="col-md-6 mb-3">
                <div class="card {{ $creditsStatus ? 'border-success' : 'border-danger' }}">
                  <div class="card-body text-center">
                    <div class="mb-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-{{ $creditsStatus ? 'success' : 'danger' }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                        <path d="M9 12l2 2l4 -4"/>
                      </svg>
                    </div>
                    <h4 class="card-title">SKS Minimum</h4>
                    <div class="text-muted mb-2">Persyaratan: ≥ {{ $minCredits }} SKS</div>
                    <div class="h2 mb-0 text-{{ $creditsStatus ? 'success' : 'danger' }}">
                      {{ $totalCredits }} SKS
                    </div>
                    <div class="badge bg-{{ $creditsStatus ? 'success' : 'danger' }} mt-2">
                      {{ $creditsStatus ? 'MEMENUHI' : 'BELUM MEMENUHI' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Academic Performance Summary -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ringkasan Prestasi Akademik</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0 text-primary">{{ $totalCredits }}</div>
                  <div class="text-muted">Total SKS Lulus</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0 text-{{ $cumulativeGPA >= 3.5 ? 'success' : ($cumulativeGPA >= 3.0 ? 'warning' : 'danger') }}">
                    {{ number_format($cumulativeGPA, 2) }}
                  </div>
                  <div class="text-muted">IPK Kumulatif</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0 text-{{ $failedCourses == 0 ? 'success' : 'danger' }}">{{ $failedCourses }}</div>
                  <div class="text-muted">Mata Kuliah Gagal</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  @php
                    $completionPercentage = $minCredits > 0 ? round(($totalCredits / $minCredits) * 100, 1) : 0;
                  @endphp
                  <div class="h1 m-0 text-info">{{ $completionPercentage }}%</div>
                  <div class="text-muted">Persentase Kelulusan</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graduation Status Details -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Detail Status Kelulusan</h3>
          </div>
          <div class="card-body">
            @if($graduationStatus)
              <div class="alert alert-success" role="alert">
                <div class="d-flex">
                  <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                      <path d="M5 12l5 5l10 -10"/>
                    </svg>
                  </div>
                  <div>
                    <h4 class="alert-title">Selamat! Anda Memenuhi Syarat Kelulusan</h4>
                    <div class="text-muted">
                      Anda telah memenuhi semua persyaratan kelulusan yang ditetapkan oleh program studi. 
                      Silakan hubungi bagian akademik untuk proses selanjutnya.
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="alert alert-warning" role="alert">
                <div class="d-flex">
                  <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                      <path d="M12 9v2m0 4v.01"/>
                      <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                    </svg>
                  </div>
                  <div>
                    <h4 class="alert-title">Belum Memenuhi Syarat Kelulusan</h4>
                    <div class="text-muted">
                      Anda belum memenuhi semua persyaratan kelulusan. Silakan periksa detail di bawah ini:
                    </div>
                    <ul class="mt-2">
                      @if(!$gpaStatus)
                        <li>IPK Anda ({{ number_format($cumulativeGPA, 2) }}) masih di bawah minimum yang dipersyaratkan ({{ number_format($minGPA, 2) }})</li>
                      @endif
                      @if(!$creditsStatus)
                        <li>Total SKS Anda ({{ $totalCredits }}) masih kurang dari minimum yang dipersyaratkan ({{ $minCredits }} SKS)</li>
                      @endif
                      @if($failedCourses > 0)
                        <li>Anda memiliki {{ $failedCourses }} mata kuliah dengan nilai E yang perlu diperbaiki</li>
                      @endif
                    </ul>
                  </div>
                </div>
              </div>
            @endif

            <!-- Progress Bar -->
            <div class="mb-3">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Progress IPK</label>
                  @php
                    $gpaProgress = ($cumulativeGPA / 4.0) * 100;
                  @endphp
                  <div class="progress">
                    <div class="progress-bar bg-{{ $gpaStatus ? 'success' : 'warning' }}" role="progressbar" style="width: {{ $gpaProgress }}%" aria-valuenow="{{ $gpaProgress }}" aria-valuemin="0" aria-valuemax="100">
                      {{ number_format($cumulativeGPA, 2) }} / 4.00
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Progress SKS</label>
                  <div class="progress">
                    <div class="progress-bar bg-{{ $creditsStatus ? 'success' : 'info' }}" role="progressbar" style="width: {{ min($completionPercentage, 100) }}%" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                      {{ $totalCredits }} / {{ $minCredits }} SKS
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    @if(!$graduationStatus)
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Rekomendasi</h3>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              @if(!$gpaStatus)
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="status-dot status-dot-animated bg-warning d-block"></span>
                    </div>
                    <div class="col text-truncate">
                      <strong>Tingkatkan IPK</strong>
                      <div class="d-block text-muted text-truncate mt-n1">
                        Fokus pada mata kuliah dengan nilai rendah dan pertimbangkan untuk mengulang mata kuliah dengan nilai E.
                      </div>
                    </div>
                  </div>
                </div>
              @endif
              @if(!$creditsStatus)
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="status-dot status-dot-animated bg-info d-block"></span>
                    </div>
                    <div class="col text-truncate">
                      <strong>Lengkapi SKS</strong>
                      <div class="d-block text-muted text-truncate mt-n1">
                        Anda masih membutuhkan {{ $minCredits - $totalCredits }} SKS lagi untuk memenuhi persyaratan kelulusan.
                      </div>
                    </div>
                  </div>
                </div>
              @endif
              @if($failedCourses > 0)
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="status-dot status-dot-animated bg-danger d-block"></span>
                    </div>
                    <div class="col text-truncate">
                      <strong>Perbaiki Nilai E</strong>
                      <div class="d-block text-muted text-truncate mt-n1">
                        Segera daftarkan diri untuk mengulang {{ $failedCourses }} mata kuliah yang memiliki nilai E.
                      </div>
                    </div>
                  </div>
                </div>
              @endif
              <div class="list-group-item">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="status-dot status-dot-animated bg-blue d-block"></span>
                  </div>
                  <div class="col text-truncate">
                    <strong>Konsultasi dengan Dosen Pembimbing</strong>
                    <div class="d-block text-muted text-truncate mt-n1">
                      Diskusikan strategi untuk mencapai persyaratan kelulusan dengan dosen pembimbing akademik Anda.
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection

@section('custom-css')
<style>
.fs-3 {
  font-size: 1.5rem !important;
}
.icon-lg {
  width: 3rem;
  height: 3rem;
}

/* Improve text contrast for better readability */
.text-muted {
  color: #6c757d !important;
  font-weight: 500;
}

/* Dark mode improvements */
[data-bs-theme="dark"] .text-muted {
  color: #adb5bd !important;
}

/* Badge improvements */
.badge.bg-success {
  background-color: #198754 !important;
  color: white !important;
}

.badge.bg-danger {
  background-color: #dc3545 !important;
  color: white !important;
}

/* Progress bar text improvements */
.progress-bar {
  color: white !important;
  font-weight: 600;
}
</style>
@endsection
