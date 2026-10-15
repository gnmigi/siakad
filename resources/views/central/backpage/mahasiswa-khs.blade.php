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
          Kartu Hasil Studi (KHS)
        </h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          @if($grades->count() > 0)
            <a href="{{ route('mahasiswa.khs.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/>
              </svg>
              Print KHS
            </a>
            <a href="{{ route('mahasiswa.khs.export-pdf', request()->query()) }}" class="btn btn-primary">
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
                  <strong>IPK Kumulatif:</strong>
                  <span class="badge bg-{{ $cumulativeGPA >= 3.5 ? 'success' : ($cumulativeGPA >= 3.0 ? 'warning' : 'danger') }} fs-3 text-white">
                    {{ number_format($cumulativeGPA, 2) }}
                  </span>
                </div>
                @if($selectedSemester)
                <div class="mb-2">
                  <strong>IPS Semester:</strong>
                  <span class="badge bg-{{ $semesterGPA >= 3.5 ? 'success' : ($semesterGPA >= 3.0 ? 'warning' : 'danger') }} fs-4 text-white">
                    {{ number_format($semesterGPA, 2) }}
                  </span>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form method="GET" action="{{ route('mahasiswa.khs.index') }}">
              <div class="row align-items-end">
                <div class="col-md-4">
                  <label class="form-label">Pilih Semester</label>
                    <select name="semester" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Semester</option>
                        @forelse($availableSemesters ?? [] as $semester)
                            <option value="{{ $semester['semester'] }}" {{ $selectedSemester == $semester['semester'] ? 'selected' : '' }}>
                                {{ $semester['display_text'] }}
                            </option>
                        @empty
                            <option value="">Tidak ada semester tersedia</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-2">
                  <noscript>
                    <button type="submit" class="btn btn-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                        <path d="M21 21l-6 -6"/>
                      </svg>
                      Filter
                    </button>
                  </noscript>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Daftar Nilai {{ $selectedSemester ? 'Semester Terpilih' : 'Semua Semester' }}
            </h3>
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
                          @foreach($bobotNilaiSettings as $setting)
                              <th class="text-center">{{ $setting->component_name }}</th>
                          @endforeach
                          <th class="text-center">Nilai Akhir</th>
                          <th class="text-center">Huruf</th>
                          <th class="text-center">Bobot</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($grades as $index => $grade)
                          <tr>
                              <td>{{ $index + 1 }}</td>
                              <td><strong>{{ $grade->mataKuliah->code ?? '-' }}</strong></td>
                              <td><div>{{ $grade->mataKuliah->name ?? '-' }}</div></td>
                              <td><span class="badge bg-blue text-white">{{ $grade->mataKuliah->bsks ?? 0 }}</span></td>
                              <td>
                                  <div class="text-muted">
                                      @if($grade->dosen)
                                          {{ $grade->dosen->title_front ? $grade->dosen->title_front . ' ' : '' }}{{ $grade->dosen->name }}{{ $grade->dosen->title_behind ? ', ' . $grade->dosen->title_behind : '' }}
                                      @else
                                          -
                                      @endif
                                  </div>
                              </td>
                              @foreach($bobotNilaiSettings as $setting)
                                  <td class="text-center">
                                      {{ number_format($grade->{$setting->component_key}, 1) }}
                                  </td>
                              @endforeach
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
                          </tr>
                      @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="table-active">
                      <td colspan="3"><strong>Total / Rata-rata</strong></td>
                      <td><strong>{{ $grades->sum(function($g) { return $g->mataKuliah->bsks ?? 0; }) }}</strong></td>
                      <td colspan="{{ 1 + $bobotNilaiSettings->where('is_active')->count() }}"></td>
                      <td class="text-center">
                        <strong>{{ $selectedSemester ? number_format($semesterGPA, 2) : number_format($cumulativeGPA, 2) }}</strong>
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
                
                {{-- Logika dinamis berdasarkan status KRS --}}
                @if($selectedSemester && !$isKrsFilled)
                    
                    @if(is_null($krsStatus))
                        {{-- Kasus 1: Belum buat KRS sama sekali --}}
                        <p class="empty-title">KRS Belum Diisi</p>
                        <p class="empty-subtitle text-muted">
                          Anda belum memiliki Kartu Rencana Studi (KRS) untuk semester ini. Silakan buat dan isi KRS terlebih dahulu.
                        </p>
                        <div class="empty-action mt-3">
                          <a href="{{ route('mahasiswa.krs.index', ['semester' => $selectedSemester]) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 4l-8 4l8 4l8 -4l-8 -4" /><path d="M4 12l8 4l8 -4" /><path d="M4 16l8 4l8 -4" /></svg>
                            Isi KRS Semester {{ $selectedSemester }} Sekarang
                          </a>
                        </div>

                    @elseif($krsStatus === 'draft')
                        {{-- Kasus 2: KRS masih draft (belum disubmit) --}}
                        <p class="empty-title">KRS Belum Diajukan</p>
                        <p class="empty-subtitle text-muted">
                          KRS Anda untuk semester ini masih berstatus <strong>Draft</strong>. Silakan periksa kembali dan Ajukan (Submit) KRS Anda agar dapat diproses.
                        </p>
                        <div class="empty-action mt-3">
                          <a href="{{ route('mahasiswa.krs.index', ['semester' => $selectedSemester]) }}" class="btn btn-warning text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                            Lihat & Ajukan KRS Semester {{ $selectedSemester }}
                          </a>
                        </div>

                    @elseif($krsStatus === 'submitted')
                        {{-- Kasus 3: KRS sudah disubmit, nunggu ACC --}}
                        <p class="empty-title">Menunggu Persetujuan Admin</p>
                        <p class="empty-subtitle text-muted">
                          KRS Anda untuk semester ini sedang menunggu persetujuan (ACC) dari Admin/Dosen Pembimbing. KHS akan muncul secara otomatis setelah KRS disetujui.
                        </p>
                        <div class="empty-action mt-3">
                          <a href="{{ route('mahasiswa.krs.index', ['semester' => $selectedSemester]) }}" class="btn btn-outline-secondary">
                            Lihat Status KRS Semester {{ $selectedSemester }}
                          </a>
                        </div>

                    @elseif($krsStatus === 'rejected')
                        {{-- Kasus 4: KRS ditolak --}}
                        <p class="empty-title text-danger">KRS Ditolak</p>
                        <p class="empty-subtitle text-muted">
                          KRS Anda untuk semester ini telah <strong>ditolak</strong> oleh Admin. Silakan periksa catatan penolakan dan perbaiki KRS Anda.
                        </p>
                        <div class="empty-action mt-3">
                          <a href="{{ route('mahasiswa.krs.index', ['semester' => $selectedSemester]) }}" class="btn btn-danger">
                            Perbaiki KRS Semester {{ $selectedSemester }}
                          </a>
                        </div>
                    @endif

                @else
                    {{-- Kasus 5: KRS sudah di-ACC (tapi nilai dari dosen memang belum ada/kosong) --}}
                    <p class="empty-title">Belum ada data nilai</p>
                    <p class="empty-subtitle text-muted">
                      {{ $selectedSemester ? 'Tidak ada nilai untuk semester yang dipilih (Dosen mungkin belum menginput nilai).' : 'Anda belum memiliki nilai yang tersedia.' }}
                    </p>
                @endif
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
</style>
@endsection