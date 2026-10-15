@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fasilitas Institusi</h3>
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
                <form action="{{ route($spref . 'institusi.facilities-handle') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $page->title ?? 'Fasilitas Institusi' }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Umum Fasilitas <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" rows="6" required>{{ $page->content ?? '' }}</textarea>
                                @error('content')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Tuliskan deskripsi umum tentang fasilitas yang tersedia di institusi.</small>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Daftar Fasilitas</h4>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addFacility()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <line x1="12" y1="5" x2="12" y2="19"/>
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                            Tambah Fasilitas
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="facilities-container">
                                        @if($page && $page->additional_data && isset($page->additional_data['facilities']))
                                            @foreach($page->additional_data['facilities'] as $index => $facility)
                                                <div class="facility-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Fasilitas</label>
                                                                <input type="text" class="form-control" name="facilities[{{ $index }}][name]" value="{{ $facility['name'] }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Gambar Fasilitas</label>
                                                                <input type="file" class="form-control" name="facilities[{{ $index }}][image]" accept="image/*">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Deskripsi</label>
                                                                <textarea class="form-control" name="facilities[{{ $index }}][description]" rows="3">{{ $facility['description'] }}</textarea>
                                                            </div>
                                                        </div>
                                                        @if(isset($facility['image']))
                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Gambar Saat Ini</label>
                                                                    <img src="{{ asset('storage/images/institusi/facilities/' . $facility['image']) }}" class="img-fluid rounded" style="max-height: 150px;">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeFacility(this)">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <div class="text-center" id="no-facilities" style="{{ ($page && $page->additional_data && isset($page->additional_data['facilities']) && count($page->additional_data['facilities']) > 0) ? 'display: none;' : '' }}">
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 21l18 0"/>
                                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/>
                                                <path d="M9 9l1 0"/>
                                                <path d="M9 12l1 0"/>
                                                <path d="M9 15l1 0"/>
                                                <path d="M14 8l1 0"/>
                                                <path d="M14 12l1 0"/>
                                            </svg>
                                            <p>Belum ada fasilitas yang ditambahkan</p>
                                            <button type="button" class="btn btn-primary" onclick="addFacility()">Tambah Fasilitas Pertama</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gambar Header</label>
                                <input type="file" class="form-control" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Gambar untuk header halaman. Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                            </div>

                            @if($page && $page->image)
                                <div class="mb-3">
                                    <label class="form-label">Gambar Header Saat Ini</label>
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
                                    <h4 class="card-title">Tips Fasilitas</h4>
                                </div>
                                <div class="card-body">
                                    <ul class="small">
                                        <li>Sertakan foto untuk setiap fasilitas</li>
                                        <li>Jelaskan kapasitas dan spesifikasi</li>
                                        <li>Cantumkan lokasi fasilitas</li>
                                        <li>Update informasi secara berkala</li>
                                        <li>Gunakan deskripsi yang menarik</li>
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
                                Simpan Fasilitas
                            </button>
                            <a href="{{ route((isset($spref) ? $spref : 'master.') . 'dashboard-render') }}" class="btn btn-link ms-auto">Batal</a>
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
let facilityIndex = {{ $page && $page->additional_data && isset($page->additional_data['facilities']) ? count($page->additional_data['facilities']) : 0 }};

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

function addFacility() {
    const container = document.getElementById('facilities-container');
    const noFacilities = document.getElementById('no-facilities');
    
    const facilityHtml = `
        <div class="facility-item border rounded p-3 mb-3" data-index="${facilityIndex}">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" name="facilities[${facilityIndex}][name]" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Gambar Fasilitas</label>
                        <input type="file" class="form-control" name="facilities[${facilityIndex}][image]" accept="image/*">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="facilities[${facilityIndex}][description]" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFacility(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="4" y1="7" x2="20" y2="7"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', facilityHtml);
    noFacilities.style.display = 'none';
    facilityIndex++;
}

function removeFacility(button) {
    const facilityItem = button.closest('.facility-item');
    facilityItem.remove();
    
    // Show no facilities message if no items left
    const container = document.getElementById('facilities-container');
    const noFacilities = document.getElementById('no-facilities');
    
    if (container.children.length === 0) {
        noFacilities.style.display = 'block';
    }
}
</script>
@endsection
