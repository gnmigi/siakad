@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profil Institusi</h3>
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
                <form action="{{ route($spref . 'institusi.profile-handle') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $page->title ?? 'Profil Institusi' }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konten Profil <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" rows="15" required>{{ $page->content ?? '' }}</textarea>
                                @error('content')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Tuliskan profil lengkap institusi, sejarah, visi misi singkat, dan informasi penting lainnya.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Video Profil YouTube</label>
                                <input type="url" class="form-control" name="youtube_url" 
                                       value="{{ $page->additional_data['youtube_url'] ?? '' }}" 
                                       placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
                                @error('youtube_url')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Masukkan link YouTube untuk video profil kampus. Video akan ditampilkan di halaman profil institusi.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gambar Profil</label>
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
                                Simpan Profil
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
                <h3 class="card-title">Panduan Penggunaan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Tips Menulis Profil</h4>
                        <ul>
                            <li>Mulai dengan sejarah singkat institusi</li>
                            <li>Jelaskan visi dan misi utama</li>
                            <li>Sertakan prestasi dan pencapaian penting</li>
                            <li>Tambahkan informasi kontak dan lokasi</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Gambar Profil</h4>
                        <ul>
                            <li>Gunakan gambar gedung utama atau logo institusi</li>
                            <li>Pastikan gambar berkualitas tinggi</li>
                            <li>Ukuran optimal: 800x600 piksel</li>
                            <li>Format yang disarankan: JPG atau PNG</li>
                        </ul>
                        
                        <h4 class="mt-4">Video YouTube</h4>
                        <ul>
                            <li>Masukkan link YouTube lengkap (contoh: https://www.youtube.com/watch?v=VIDEO_ID)</li>
                            <li>Atau gunakan link pendek (contoh: https://youtu.be/VIDEO_ID)</li>
                            <li>Video akan otomatis ditampilkan dalam format responsive</li>
                            <li>Pastikan video bersifat publik dan dapat diakses</li>
                        </ul>
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
