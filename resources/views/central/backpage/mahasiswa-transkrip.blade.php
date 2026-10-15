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
          Transkrip Nilai
        </h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          @if($grades->count() > 0)
            <a href="{{ route('mahasiswa.transkrip.export-pdf') }}" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                <path d="M9 15l2 2l4 -4"/>
              </svg>
              Export PDF
            </a>
          @endif
        </div>
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
                      <strong>NIM:</strong> {{ $mahasiswa->nim }}
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
                  <strong>IPK Kumulatif:</strong>
                  <span class="badge bg-{{ $cumulativeGPA >= 3.5 ? 'success' : ($cumulativeGPA >= 3.0 ? 'warning' : 'danger') }} fs-3 text-white">
                    {{ number_format($cumulativeGPA, 2) }}
                  </span>
                </div>
                <div class="mb-2">
                  <strong>Total SKS:</strong>
                      <span class="badge bg-info fs-4 text-white">{{ $totalCredits }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Academic Summary -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ringkasan Akademik</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0">{{ $grades->count() }}</div>
                  <div class="text-muted">Total Mata Kuliah</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0">{{ $totalCredits }}</div>
                  <div class="text-muted">Total SKS</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  <div class="h1 m-0">{{ number_format($cumulativeGPA, 2) }}</div>
                  <div class="text-muted">IPK Kumulatif</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="text-center">
                  @php
                    $passedCourses = $grades->filter(function($grade) {
                        try {
                            return \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($grade->nilai_akhir) !== 'E';
                        } catch (\Exception $e) {
                            return \App\Models\NilaiMahasiswa::convertToLetterGrade($grade->nilai_akhir) !== 'E';
                        }
                    })->count();
                  @endphp
                  <div class="h1 m-0">{{ $passedCourses }}</div>
                  <div class="text-muted">Mata Kuliah Lulus</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grade Distribution -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Distribusi Nilai</h3>
          </div>
          <div class="card-body">
            <div class="row">
              @php
                // Get active grade scales to build distribution dynamically
                $activeGrades = \App\Models\Akademik\GradeScaleSetting::where('is_active', true)
                    ->orderBy('min_score', 'desc')
                    ->pluck('grade_letter')
                    ->toArray();
                
                $gradeDistribution = [];
                foreach($activeGrades as $gradeKey) {
                    $gradeDistribution[$gradeKey] = $grades->filter(function($g) use ($gradeKey) {
                        try {
                            return \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($g->nilai_akhir) == $gradeKey;
                        } catch (\Exception $e) {
                            return \App\Models\NilaiMahasiswa::convertToLetterGrade($g->nilai_akhir) == $gradeKey;
                        }
                    })->count();
                }
              @endphp
              @foreach($gradeDistribution as $grade => $count)
                @if($count > 0)
                  <div class="col-md-2 col-sm-4 col-6 mb-2">
                    <div class="text-center">
                      @php
                        $badgeColor = in_array($grade, ['A', 'A-']) ? 'success' : 
                                     (in_array($grade, ['B+', 'B', 'B-']) ? 'warning' : 
                                     (in_array($grade, ['C+', 'C', 'C-']) ? 'info' : 'danger'));
                      @endphp
                      <span class="badge bg-{{ $badgeColor }} fs-5 text-white">{{ $grade }}</span>
                      <div class="text-muted">{{ $count }} MK</div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transcript Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Transkrip Nilai Lengkap</h3>
          </div>
          <div class="card-body">
            @if($grades->count() > 0)
              <div class="table-responsive">
                <table class="table table-vcenter">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Kode MK</th>
                      <th>Mata Kuliah</th>
                      <th>SKS</th>
                      <th>Dosen</th>
                      <th class="text-center">Nilai Akhir</th>
                      <th class="text-center">Huruf</th>
                      <th class="text-center">Bobot</th>
                      <th class="text-center">Mutu (SKS x Bobot)</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($grades as $index => $grade)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                          <strong>{{ $grade->mataKuliah->code ?? '-' }}</strong>
                        </td>
                        <td>
                          <div>{{ $grade->mataKuliah->name ?? '-' }}</div>
                        </td>
                        <td>
                          <span class="badge bg-blue text-white">{{ $grade->mataKuliah->bsks ?? 0 }}</span>
                        </td>
                        <td>
                          <div class="text-muted">
                            @if($grade->dosen)
                              {{ $grade->dosen->title_front ? $grade->dosen->title_front . ' ' : '' }}{{ $grade->dosen->name }}{{ $grade->dosen->title_behind ? ', ' . $grade->dosen->title_behind : '' }}
                            @else
                              -
                            @endif
                          </div>
                        </td>
                        <td class="text-center">
                          <strong>{{ number_format($grade->nilai_akhir, 1) }}</strong>
                        </td>
                        <td class="text-center">
                          @php
                            try {
                                $letterGrade = \App\Models\Akademik\GradeScaleSetting::convertToLetterGrade($grade->nilai_akhir);
                            } catch (\Exception $e) {
                                $letterGrade = \App\Models\NilaiMahasiswa::convertToLetterGrade($grade->nilai_akhir);
                            }
                            $badgeColor = $letterGrade == 'A' || $letterGrade == 'A-' ? 'success' : 
                                         ($letterGrade == 'B+' || $letterGrade == 'B' || $letterGrade == 'B-' ? 'warning' : 
                                         ($letterGrade == 'C+' || $letterGrade == 'C' || $letterGrade == 'C-' ? 'info' : 'danger'));
                          @endphp
                          <span class="badge bg-{{ $badgeColor }} text-white">{{ $letterGrade }}</span>
                        </td>
                        <td class="text-center">
                          @php
                            try {
                                $gradePoint = \App\Models\Akademik\GradeScaleSetting::convertToGradePoint($grade->nilai_akhir);
                            } catch (\Exception $e) {
                                $gradePoint = \App\Models\NilaiMahasiswa::convertToGradePoint($grade->nilai_akhir);
                            }
                          @endphp
                          {{ number_format($gradePoint, 2) }}
                        </td>
                        <td class="text-center">
                          @php
                            $bsks = $grade->mataKuliah->bsks ?? 0;
                            $mutu = $bsks * $gradePoint;
                          @endphp
                          {{ number_format($mutu, 2) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="table-active">
                      <td colspan="3"><strong>Total</strong></td>
                      <td><strong>{{ $totalCredits }}</strong></td>
                      <td colspan="4"></td>
                      <td class="text-center">
                        @php
                          $totalMutu = $grades->sum(function($grade) {
                              $bsks = $grade->mataKuliah->bsks ?? 0;
                              try {
                                  $gradePoint = \App\Models\Akademik\GradeScaleSetting::convertToGradePoint($grade->nilai_akhir);
                              } catch (\Exception $e) {
                                  $gradePoint = \App\Models\NilaiMahasiswa::convertToGradePoint($grade->nilai_akhir);
                              }
                              return $bsks * $gradePoint;
                          });
                        @endphp
                        <strong>{{ number_format($totalMutu, 2) }}</strong>
                      </td>
                    </tr>
                    <tr class="table-active">
                      <td colspan="8"><strong>IPK (Indeks Prestasi Kumulatif)</strong></td>
                      <td class="text-center">
                        <strong>{{ number_format($cumulativeGPA, 2) }}</strong>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            @else
              <div class="empty">
                <div class="empty-img">
                  <img src="{{ asset('dashboard/dist/img/undraw_printing_invoices_5r4r.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">Belum ada data transkrip</p>
                <p class="empty-subtitle text-muted">
                  Anda belum memiliki nilai yang tersedia untuk ditampilkan dalam transkrip.
                </p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('custom-css')
<style>
.fs-3 {
  font-size: 1.5rem !important;
}
.fs-4 {
  font-size: 1.25rem !important;
}
.fs-5 {
  font-size: 1.1rem !important;
}
</style>
@endsection
