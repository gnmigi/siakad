@extends('central.backpage.core-backpage-dosen')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ $menus }}
                </div>
                <h2 class="page-title">
                    {{ $pages }}
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload Nilai Excel</h3>
                        <div class="card-actions">
                            <a href="{{ route($spref.'mata-kuliah-render') }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l14 0"/>
                                    <path d="M5 12l6 6"/>
                                    <path d="M5 12l6 -6"/>
                                </svg>
                                Kembali ke Mata Kuliah
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route($spref.'nilai.upload-handle') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Mata Kuliah</label>
                                    <select class="form-select" name="mata_kuliah_id" id="mata_kuliah_id" required>
                                        <option value="">Pilih Mata Kuliah</option>
                                        @foreach($mataKuliah as $mk)
                                            <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                                {{ $mk->name }} ({{ $mk->sks }} SKS) - {{ $mk->programStudi->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mata_kuliah_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">File Excel</label>
                                    <input type="file" class="form-control" name="excel_file" accept=".xlsx" required>
                                    @error('excel_file')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="text-muted">Format file: XLSX, maksimal 5MB</small>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        <path d="M12 11v6"/>
                                        <path d="M9.5 13.5l2.5 -2.5l2.5 2.5"/>
                                    </svg>
                                    Upload Excel
                                </button>
                                <a id="download-template-btn" href="#" class="btn btn-success ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                        <path d="M7 11l5 5l5 -5"/>
                                        <path d="M12 4l0 12"/>
                                    </svg>
                                    Download Template Excel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Panduan Upload Excel</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Format File Excel:</h4>
                                <p>Gunakan template Excel dari tombol <strong>Download Template Excel</strong>. Kolom wajib:</p>
                                <ol>
                                    <li><strong>nama_mahasiswa</strong> - otomatis terisi dari template</li>
                                    <li><strong>nim</strong> - identitas utama mahasiswa</li>
                                    <li><strong>kelas</strong> - informasi kelas reguler</li>
                                    <li><strong>semester</strong> - semester aktif</li>
                                    <li><strong>komponen nilai dinamis</strong> - mengikuti pengaturan admin (contoh: tugas, quiz/keaktifan, uts, uas)</li>
                                </ol>

                                <div class="alert alert-info">
                                    <h4 class="alert-title">Catatan Penting:</h4>
                                    <p class="mb-0">Nama kolom komponen nilai harus sama dengan template (mengikuti setting bobot nilai dari admin).</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Ketentuan Upload:</h4>
                                <ul>
                                    <li>File harus berformat Excel (.xlsx)</li>
                                    <li>Ukuran file maksimal 5MB</li>
                                    <li>Baris pertama wajib berisi header template</li>
                                    <li>NIM mahasiswa harus sudah terdaftar di sistem</li>
                                    <li>Nilai harus berupa angka antara 0-100</li>
                                    <li>Jika ada error, sistem akan melaporkan baris yang bermasalah</li>
                                </ul>

                                <div class="alert alert-warning">
                                    <h4 class="alert-title">Perhatian!</h4>
                                    <p class="mb-0">
                                        Upload Excel akan menimpa nilai yang sudah ada sebelumnya untuk mahasiswa yang sama pada mata kuliah yang dipilih.
                                    </p>
                                </div>

                                <div class="alert alert-success">
                                    <h4 class="alert-title">Komponen Bobot Aktif (dari Admin):</h4>
                                    <ul class="mb-0">
                                        @forelse($bobotNilaiSettings as $component)
                                            <li>{{ $component->component_name }} ({{ $component->component_key }}): {{ number_format((float)$component->weight, 0) }}%</li>
                                        @empty
                                            <li>Belum ada komponen aktif.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Data Preview -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview Kolom Template Excel</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>nama_mahasiswa</th>
                                        <th>nim</th>
                                        <th>kelas</th>
                                        <th>semester</th>
                                        @foreach($bobotNilaiSettings as $component)
                                            <th>{{ $component->component_key }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-secondary">
                                        <td colspan="{{ 4 + count($bobotNilaiSettings) }}" class="text-center">
                                            <em>Data mahasiswa akan otomatis disiapkan saat download template berdasarkan mata kuliah terpilih.</em>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
function updateTemplateDownloadLink() {
    const mataKuliahSelect = document.getElementById('mata_kuliah_id');
    const downloadBtn = document.getElementById('download-template-btn');
    const mataKuliahId = mataKuliahSelect ? mataKuliahSelect.value : '';

    if (!downloadBtn) return;

    if (!mataKuliahId) {
        downloadBtn.setAttribute('href', '#');
        downloadBtn.classList.add('disabled');
        return;
    }

    const baseUrl = "{{ route($spref.'nilai.upload-template') }}";
    downloadBtn.setAttribute('href', `${baseUrl}?mata_kuliah_id=${encodeURIComponent(mataKuliahId)}`);
    downloadBtn.classList.remove('disabled');
}

document.addEventListener('DOMContentLoaded', function() {
    const mataKuliahSelect = document.getElementById('mata_kuliah_id');
    if (mataKuliahSelect) {
        mataKuliahSelect.addEventListener('change', updateTemplateDownloadLink);
    }
    updateTemplateDownloadLink();

    // File input validation
    const fileInput = document.querySelector('input[name="excel_file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = '';
                return;
            }

            const allowedExtensions = ['xlsx'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                alert('Format file tidak didukung. Gunakan file XLSX.');
                this.value = '';
                return;
            }
        });
    }

    // Form submission with loading state
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-spin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 3a9 9 0 1 0 9 9"/>
            </svg>
            Uploading Excel...
        `;
        
        // Re-enable button after 30 seconds (timeout)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 30000);
    });
});
</script>

<style>
.icon-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

pre {
    background-color: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endsection
