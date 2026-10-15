@extends('core-themes.core-backpage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Struktur Organisasi</h3>
                <div class="card-actions">
                    <a href="{{ route('root.institution.structure') }}" class="btn btn-outline-primary" target="_blank">
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
                <form action="{{ route($spref . 'institusi.structure-handle') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $page->title ?? 'Struktur Organisasi' }}" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Struktur <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" rows="8" required>{{ $page->content ?? '' }}</textarea>
                                @error('content')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Jelaskan struktur organisasi institusi, tugas dan fungsi masing-masing posisi.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bagan Struktur Organisasi</label>
                                <input type="file" class="form-control" name="structure_image" accept="image/*" onchange="previewImage(this, 'structurePreview')">
                                @error('structure_image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Upload bagan/chart struktur organisasi. Format: JPG, PNG, GIF. Maksimal 5MB.</small>
                            </div>

                            @if($page && $page->additional_data && isset($page->additional_data['bagan_organisasi']))
                                <div class="mb-3">
                                    <label class="form-label">Bagan Struktur Saat Ini</label>
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <img src="{{ asset($page->additional_data['bagan_organisasi']) }}" class="img-fluid" id="structurePreview" style="max-height: 400px;">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-3" id="structurePreviewContainer" style="display: none;">
                                    <label class="form-label">Preview Bagan Struktur</label>
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <img id="structurePreview" class="img-fluid" style="max-height: 400px;">
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                                    <img src="{{ asset($page->image) }}" class="img-fluid rounded" id="imagePreview" style="max-height: 200px;">
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

                    <!-- Struktur Kepemimpinan Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Struktur Kepemimpinan</h4>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addLeader()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <line x1="12" y1="5" x2="12" y2="19"/>
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                            Tambah Pengurus
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="leadersContainer">
                                        @php
                                            $leaders = [];
                                            if($page && $page->additional_data && isset($page->additional_data['struktur'])) {
                                                $leaders = $page->additional_data['struktur'];
                                            }
                                        @endphp
                                        
                                        @if(count($leaders) > 0)
                                            @foreach($leaders as $index => $leader)
                                                <div class="leader-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Foto Pengurus</label>
                                                                <input type="file" class="form-control" name="leaders[{{ $index }}][foto]" accept="image/*" onchange="previewLeaderImage(this, {{ $index }})">
                                                                @if(isset($leader['foto']) && $leader['foto'])
                                                                    <div class="mt-2">
                                                                        <img src="{{ asset($leader['foto']) }}" class="img-fluid rounded" id="leaderPreview{{ $index }}" style="max-height: 150px;">
                                                                    </div>
                                                                @else
                                                                    <img id="leaderPreview{{ $index }}" class="img-fluid rounded mt-2" style="display: none; max-height: 150px;">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Lengkap</label>
                                                                        <input type="text" class="form-control" name="leaders[{{ $index }}][nama]" value="{{ $leader['nama'] ?? '' }}" placeholder="Dr. Ahmad Suryadi, M.Pd">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jabatan</label>
                                                                        <input type="text" class="form-control" name="leaders[{{ $index }}][jabatan]" value="{{ $leader['jabatan'] ?? '' }}" placeholder="Rektor">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Pendidikan</label>
                                                                        <input type="text" class="form-control" name="leaders[{{ $index }}][pendidikan]" value="{{ $leader['pendidikan'] ?? '' }}" placeholder="S3 Pendidikan - Universitas Indonesia">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Deskripsi Singkat</label>
                                                                        <textarea class="form-control" name="leaders[{{ $index }}][deskripsi]" rows="2" placeholder="Memimpin institusi dengan visi modern">{{ $leader['deskripsi'] ?? '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeLeader({{ $index }})">
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
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    @if(count($leaders) == 0)
                                        <div class="empty" id="emptyState">
                                            <div class="empty-img">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                </svg>
                                            </div>
                                            <p class="empty-title">Belum ada pengurus</p>
                                            <p class="empty-subtitle text-muted">Klik tombol "Tambah Pengurus" untuk menambahkan struktur kepemimpinan</p>
                                        </div>
                                    @endif
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
                                Simpan Struktur
                            </button>
                            <a href="{{ route((isset($spref) ? $spref : 'master.') . 'dashboard-render') }}" class="btn btn-link ms-auto">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk pengurus baru -->
<template id="leaderTemplate">
    <div class="leader-item border rounded p-3 mb-3" data-index="INDEX_PLACEHOLDER">
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Foto Pengurus</label>
                    <input type="file" class="form-control" name="leaders[INDEX_PLACEHOLDER][foto]" accept="image/*" onchange="previewLeaderImage(this, INDEX_PLACEHOLDER)">
                    <img id="leaderPreviewINDEX_PLACEHOLDER" class="img-fluid rounded mt-2" style="display: none; max-height: 150px;">
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="leaders[INDEX_PLACEHOLDER][nama]" placeholder="Dr. Ahmad Suryadi, M.Pd">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control" name="leaders[INDEX_PLACEHOLDER][jabatan]" placeholder="Rektor">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pendidikan</label>
                            <input type="text" class="form-control" name="leaders[INDEX_PLACEHOLDER][pendidikan]" placeholder="S3 Pendidikan - Universitas Indonesia">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" name="leaders[INDEX_PLACEHOLDER][deskripsi]" rows="2" placeholder="Memimpin institusi dengan visi modern"></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeLeader(INDEX_PLACEHOLDER)">
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
    </div>
</template>
@endsection

@section('custom-js')
<script>
let leaderIndex = {{ count($leaders ?? []) }};

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
            
            // Show preview container for structure image
            if (previewId === 'structurePreview') {
                const container = document.getElementById('structurePreviewContainer');
                if (container) {
                    container.style.display = 'block';
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewLeaderImage(input, index) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('leaderPreview' + index).src = e.target.result;
            document.getElementById('leaderPreview' + index).style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addLeader() {
    const template = document.getElementById('leaderTemplate');
    const container = document.getElementById('leadersContainer');
    const emptyState = document.getElementById('emptyState');
    
    // Hide empty state if visible
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
    // Clone template and replace placeholders
    let newLeader = template.content.cloneNode(true);
    let html = newLeader.querySelector('.leader-item').outerHTML;
    html = html.replace(/INDEX_PLACEHOLDER/g, leaderIndex);
    
    // Add to container
    container.insertAdjacentHTML('beforeend', html);
    
    leaderIndex++;
}

function removeLeader(index) {
    const leaderItem = document.querySelector('[data-index="' + index + '"]');
    if (leaderItem) {
        leaderItem.remove();
        
        // Show empty state if no leaders left
        const remainingLeaders = document.querySelectorAll('.leader-item');
        if (remainingLeaders.length === 0) {
            const emptyState = document.getElementById('emptyState');
            if (emptyState) {
                emptyState.style.display = 'block';
            }
        }
    }
}
</script>
@endsection
