@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Visi & Misi Institusi</h3>
                <div class="card-actions">
                    <a href="{{ route('root.home-index') }}" class="btn btn-outline-primary" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5"/>
                            <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5"/>
                        </svg>
                        Lihat Halaman Publik
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route($spref . 'institusi.vision-mission-handle') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $page->title ?? 'Visi & Misi' }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Visi <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="vision" rows="6" required>{{ $page && $page->additional_data ? $page->additional_data['vision'] ?? '' : '' }}</textarea>
                                @error('vision')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Tuliskan visi institusi dengan jelas dan inspiratif.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Misi <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="mission" rows="10" required>{{ $page && $page->additional_data ? $page->additional_data['mission'] ?? '' : '' }}</textarea>
                                @error('mission')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Tuliskan misi institusi dalam bentuk poin-poin yang terstruktur.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gambar Visi & Misi</label>
                                <input type="file" class="form-control" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                            </div>

                            @if($page && $page->image)
                                <div class="mb-3">
                                    <label class="form-label">Gambar Saat Ini</label>
                                    <img src="{{ asset('storage/images/institusi/' . $page->image) }}" class="img-fluid rounded" id="imagePreview" style="max-height: 200px;">
                                </div>
                            @else
                                <img id="imagePreview" class="img-fluid rounded" style="display: none; max-height: 200px;">
                            @endif

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" {{ ($page && $page->is_active) ? 'checked' : '' }}>
                                    <span class="form-check-label">Aktifkan Halaman</span>
                                </label>
                                <small class="form-hint">Centang untuk menampilkan halaman ini di website publik.</small>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Tips Penulisan</h4>
                                </div>
                                <div class="card-body">
                                    <h5>Visi</h5>
                                    <ul class="small">
                                        <li>Singkat dan mudah diingat</li>
                                        <li>Menggambarkan masa depan yang diinginkan</li>
                                        <li>Inspiratif dan memotivasi</li>
                                    </ul>
                                    
                                    <h5>Misi</h5>
                                    <ul class="small">
                                        <li>Gunakan format poin-poin</li>
                                        <li>Jelaskan cara mencapai visi</li>
                                        <li>Spesifik dan dapat diukur</li>
                                        <li>Mencakup semua aspek institusi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                                    <circle cx="12" cy="14" r="2"/>
                                    <polyline points="14,4 14,8 8,8 8,4"/>
                                </svg>
                                Simpan Visi & Misi
                            </button>
                            <a href="{{ route((isset($spref) ? $spref : 'master.') . 'dashboard-render') }}" class="btn btn-link ms-auto">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contoh Format</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Contoh Visi</h4>
                        <div class="card card-sm">
                            <div class="card-body">
                                <p class="text-muted mb-0">"Menjadi institusi pendidikan tinggi Islam terdepan yang menghasilkan lulusan berkualitas, berakhlak mulia, dan berdaya saing global pada tahun 2030."</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Contoh Misi</h4>
                        <div class="card card-sm">
                            <div class="card-body">
                                <ol class="text-muted mb-0">
                                    <li>Menyelenggarakan pendidikan tinggi yang berkualitas</li>
                                    <li>Mengembangkan penelitian dan pengabdian masyarakat</li>
                                    <li>Membentuk karakter islami pada setiap lulusan</li>
                                    <li>Menjalin kerjasama dengan berbagai pihak</li>
                                </ol>
                            </div>
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
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
