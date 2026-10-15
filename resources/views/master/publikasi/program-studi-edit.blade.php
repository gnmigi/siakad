@extends('core-themes.core-backpage')

@section('custom-css')
<style>
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
    }
    
    .form-label {
        font-weight: 600;
        color: #374151;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
    }
    
    .image-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .image-upload-area:hover {
        border-color: #667eea;
        background-color: #f8faff;
    }
    
    .image-upload-area.dragover {
        border-color: #667eea;
        background-color: #f0f4ff;
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Publikasi</div>
                <h2 class="page-title">Edit Program Studi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('master.publikasi.program-studi.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 6"/>
                            <path d="M5 12l6 -6"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <form action="{{ route('master.publikasi.program-studi.update', $programStudi) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Program Studi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama Program Studi</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name', $programStudi->name) }}" 
                                               placeholder="Masukkan nama program studi" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Kategori</label>
                                        <select class="form-select @error('kategori_id') is-invalid @enderror" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategoris as $kategori)
                                                <option value="{{ $kategori->id }}" 
                                                    {{ old('kategori_id', $programStudi->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kategori_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Deskripsi Singkat</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  name="description" rows="3" 
                                                  placeholder="Masukkan deskripsi singkat program studi" required>{{ old('description', $programStudi->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konten Detail</label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  name="content" rows="8" 
                                                  placeholder="Masukkan konten detail program studi (opsional)">{{ old('content', $programStudi->content) }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Konten detail akan ditampilkan pada halaman detail program studi.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="active" {{ old('status', $programStudi->status) == 'active' ? 'selected' : '' }}>
                                                Aktif
                                            </option>
                                            <option value="inactive" {{ old('status', $programStudi->status) == 'inactive' ? 'selected' : '' }}>
                                                Tidak Aktif
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Gambar Program Studi</label>
                                        
                                        @if($programStudi->photo)
                                            <div class="mb-3">
                                                <label class="form-label">Gambar Saat Ini:</label>
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/images/program-studi/' . $programStudi->photo) }}" 
                                                         alt="{{ $programStudi->name }}" 
                                                         class="preview-image">
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="image-upload-area">
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                                   name="photo" accept="image/*" id="photo-input">
                                            <div class="mt-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M15 8h.01"/>
                                                    <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/>
                                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/>
                                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/>
                                                </svg>
                                                <div class="text-muted">
                                                    <strong>Klik untuk upload</strong> atau drag & drop
                                                </div>
                                                <small class="text-muted">
                                                    Format: JPG, PNG, GIF (Max: 2MB)
                                                </small>
                                            </div>
                                        </div>
                                        
                                        @error('photo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <label class="form-label">Preview Gambar Baru:</label>
                                            <div class="text-center">
                                                <img id="preview-img" src="" alt="Preview" class="preview-image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="{{ route('master.publikasi.program-studi.index') }}" class="btn btn-outline-secondary me-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M5 12l6 6"/>
                                        <path d="M5 12l6 -6"/>
                                    </svg>
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M12 5l7 7l-7 7"/>
                                    </svg>
                                    Update Program Studi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo-input');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const uploadArea = document.querySelector('.image-upload-area');

    // Handle file input change
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                photoInput.files = files;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Handle click on upload area
    uploadArea.addEventListener('click', function() {
        photoInput.click();
    });
});
</script>
@endsection
