@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/dynamic-content-builder.css') }}">
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

        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left;
            color: #374151 !important;
        }

        [data-bs-theme="dark"] .table td {
            color: #f9fafb !important;
        }

        [data-bs-theme="dark"] .table tbody tr td {
            color: #ffffff !important;
        }

        .table th.text-center, .table td.text-center {
            text-align: center !important;
        }

        /* Button styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
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

        /* Badge styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
            color: #ffffff !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
            color: #ffffff !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
            color: #ffffff !important;
        }

        /* Collapsible form */
        .collapse {
            transition: all 0.3s ease;
        }

        .collapse.show {
            margin-top: 1rem;
        }

        /* Image preview */
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }

        /* Responsive styling */
        @media screen and (max-width: 768px) {
            .table td[data-label] .d-flex.align-items-center,
            .table td[data-label] .d-flex.flex-column.align-items-center {
                align-items: flex-end !important;
                text-align: right;
            }
        }

        @media (max-width: 768px) {
            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
                width: 100%;
            }
            .table-responsive thead {
                display: none;
            }
            .table-responsive tr {
                margin-bottom: 1rem;
                border-bottom: 2px solid #eee;
            }
            .table-responsive td {
                position: relative;
                padding-left: 50%;
                text-align: left !important;
                border: none;
                border-bottom: 1px solid #eee;
            }
            .table-responsive td:before {
                position: absolute;
                top: 0;
                left: 0;
                width: 48%;
                padding-left: 1rem;
                white-space: nowrap;
                font-weight: bold;
                color: #888;
                content: attr(data-label);
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Berita
                    </button>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Berita</h6>
                                <h3 class="mb-0">{{ $berita ? count($berita) : 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Berita Publish</h6>
                                <h3 class="mb-0">{{ $berita ? $berita->where('status', 'Publish')->count() : 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Berita Draft</h6>
                                <h3 class="mb-0">{{ $berita ? $berita->where('status', 'Draft')->count() : 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Berita Baru</h5>
                            <form action="{{ route($spref . 'publikasi.berita-handle') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="kategori_id" class="form-label">Kategori</label>
                                        <select class="form-select" name="kategori_id" id="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategori as $kat)
                                                <option value="{{ $kat->id }}">{{ $kat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('kategori_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="name" class="form-label">Judul Berita</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="photo" class="form-label">Foto Berita</label>
                                        <input type="file" class="form-control" name="photo" id="photo" accept="image/*" required onchange="previewImage(this)">
                                        <img id="preview" class="image-preview d-none">
                                        @error('photo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- Content Type Selection -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Tipe Konten</label>
                                        <div class="content-type-toggle">
                                            <input type="radio" class="btn-check" name="content_type" id="content_type_simple" value="simple" checked>
                                            <label class="btn btn-outline-primary" for="content_type_simple">
                                                <i class="fas fa-align-left me-1"></i>Simple Text
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="content_type" id="content_type_dynamic" value="dynamic">
                                            <label class="btn btn-outline-primary" for="content_type_dynamic">
                                                <i class="fas fa-puzzle-piece me-1"></i>Dynamic Builder
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Simple Content -->
                                    <div class="col-md-12 mb-3" id="simple-content-container">
                                        <label for="content" class="form-label">Konten Berita</label>
                                        <textarea class="form-control" name="content" id="content" rows="5"></textarea>
                                        @error('content')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <!-- Dynamic Content Builder -->
                                    <div class="col-md-12 mb-3 d-none" id="dynamic-content-container">
                                        <label class="form-label">Dynamic Content Builder</label>
                                        <div class="dynamic-content-builder" id="dynamic-content-builder"></div>
                                        <input type="hidden" name="dynamic_content" id="dynamic_content_input">
                                        @error('dynamic_content')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option value="Draft">Draft</option>
                                            <option value="Publish">Publish</option>
                                            <option value="Archive">Archive</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <!-- Attachments Section -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Lampiran</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="attachments-container">
                                                    <div class="attachment-item mb-3">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <select class="form-select attachment-type" name="attachment_types[]">
                                                                    <option value="file">Upload File</option>
                                                                    <option value="link">Link External</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" name="attachment_titles[]" placeholder="Judul lampiran (opsional)">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="file" class="form-control attachment-file" name="attachment_files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                                                <input type="url" class="form-control attachment-link d-none" name="attachment_links[]" placeholder="https://...">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <button type="button" class="btn btn-danger btn-sm remove-attachment">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" id="add-attachment">
                                                    <i class="fas fa-plus me-1"></i>Tambah Lampiran
                                                </button>
                                                <small class="text-muted d-block mt-2">
                                                    File yang didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR (Max: 10MB)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($berita as $key => $item)
                                    <tr>
                                        <td data-label="No">{{ ++$key }}</td>
                                        <td data-label="Judul">{{ $item->name }}</td>
                                        <td data-label="Kategori">{{ $item->kategori->name }}</td>
                                        <td data-label="Status">
                                            <span class="badge bg-{{ $item->status == 'Publish' ? 'success' : ($item->status == 'Draft' ? 'warning' : 'secondary') }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route($spref . 'publikasi.berita-view', $item->slug) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Selengkapnya">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Berita">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route($spref . 'publikasi.berita-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="true" data-bs-toggle="tooltip" title="Hapus Berita" onclick="confirmDelete('{{ $item->code }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Content -->
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Berita</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Berita.</p>
                    
                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Berita" untuk menambahkan berita baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit berita</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus berita</li>
                        </ul>
                    </div>

                    @if(count($berita) > 0)
                        <div class="mt-4">
                            <h6>Berita Terbaru</h6>
                            <div class="list-group">
                                @foreach($berita->sortByDesc('created_at')->take(3) as $news)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $news->name }}</h6>
                                            <small class="text-muted">{{ $news->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($news->content), 50) }}</p>
                                        <small class="text-muted">{{ $news->kategori->name }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach ($berita as $item)
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route($spref . 'publikasi.berita-update', $item->code) }}" method="POST" enctype="multipart/form-data">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Berita - {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_kategori_id{{ $item->code }}" class="form-label">Kategori</label>
                                    <select class="form-select" name="kategori_id" id="edit_kategori_id{{ $item->code }}" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategori as $kat)
                                            <option value="{{ $kat->id }}" {{ $item->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="edit_name{{ $item->code }}" class="form-label">Judul Berita</label>
                                    <input type="text" class="form-control" name="name" id="edit_name{{ $item->code }}" value="{{ $item->name }}" required>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="edit_photo{{ $item->code }}" class="form-label">Foto Berita</label>
                                    <input type="file" class="form-control" name="photo" id="edit_photo{{ $item->code }}" accept="image/*" onchange="previewImage(this, 'preview{{ $item->code }}')">
                                    <img src="{{ asset('storage/images/berita/' . $item->photo) }}" id="preview{{ $item->code }}" class="image-preview">
                                    @error('photo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <!-- Content Type Selection -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Tipe Konten</label>
                                    <div class="content-type-toggle">
                                        <input type="radio" class="btn-check edit-content-type" name="content_type" id="edit_content_type_simple_{{ $item->code }}" value="simple" {{ $item->content_type === 'simple' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="edit_content_type_simple_{{ $item->code }}">
                                            <i class="fas fa-align-left me-1"></i>Simple Text
                                        </label>
                                        
                                        <input type="radio" class="btn-check edit-content-type" name="content_type" id="edit_content_type_dynamic_{{ $item->code }}" value="dynamic" {{ $item->content_type === 'dynamic' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="edit_content_type_dynamic_{{ $item->code }}">
                                            <i class="fas fa-puzzle-piece me-1"></i>Dynamic Builder
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Simple Content -->
                                <div class="col-md-12 mb-3 {{ $item->content_type === 'dynamic' ? 'd-none' : '' }}" id="edit-simple-content-container-{{ $item->code }}">
                                    <label for="edit_content{{ $item->code }}" class="form-label">Konten Berita</label>
                                    <textarea class="form-control" name="content" id="edit_content{{ $item->code }}" rows="5">{{ $item->content }}</textarea>
                                    @error('content')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                
                                <!-- Dynamic Content Builder -->
                                <div class="col-md-12 mb-3 {{ $item->content_type === 'simple' ? 'd-none' : '' }}" id="edit-dynamic-content-container-{{ $item->code }}">
                                    <label class="form-label">Dynamic Content Builder</label>
                                    <div class="dynamic-content-builder" id="edit-dynamic-content-builder-{{ $item->code }}"></div>
                                    <input type="hidden" name="dynamic_content" id="edit_dynamic_content_input_{{ $item->code }}" value="{{ $item->dynamic_content ? json_encode($item->dynamic_content) : '' }}">
                                    @error('dynamic_content')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="edit_status{{ $item->code }}" class="form-label">Status</label>
                                    <select class="form-select" name="status" id="edit_status{{ $item->code }}" required>
                                        <option value="Draft" {{ $item->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="Publish" {{ $item->status == 'Publish' ? 'selected' : '' }}>Publish</option>
                                        <option value="Archive" {{ $item->status == 'Archive' ? 'selected' : '' }}>Archive</option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                
                                <!-- Existing Attachments -->
                                @if($item->attachments && count($item->attachments) > 0)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Lampiran Saat Ini</label>
                                        <div class="card">
                                            <div class="card-body">
                                                @foreach($item->attachments as $index => $attachment)
                                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                                        <div>
                                                            <strong>{{ $attachment['title'] }}</strong>
                                                            <small class="text-muted d-block">{{ $attachment['type'] === 'file' ? 'File: ' . $attachment['original_name'] : 'Link: ' . $attachment['url'] }}</small>
                                                        </div>
                                                        <div>
                                                            <input type="checkbox" name="keep_attachments[]" value="{{ $index }}" checked>
                                                            <label class="form-check-label ms-1">Pertahankan</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- New Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Tambah Lampiran Baru</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="edit-attachments-container-{{ $item->code }}">
                                                <div class="attachment-item mb-3">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <select class="form-select attachment-type" name="attachment_types[]">
                                                                <option value="file">Upload File</option>
                                                                <option value="link">Link External</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" name="attachment_titles[]" placeholder="Judul lampiran (opsional)">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="file" class="form-control attachment-file" name="attachment_files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                                            <input type="url" class="form-control attachment-link d-none" name="attachment_links[]" placeholder="https://...">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm remove-attachment">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm add-edit-attachment" data-target="edit-attachments-container-{{ $item->code }}">
                                                <i class="fas fa-plus me-1"></i>Tambah Lampiran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('custom-js')
    <script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/dynamic-content-builder.js') }}"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });
        });

        // Image preview
        function previewImage(input, previewId = 'preview') {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Konfirmasi delete dengan SweetAlert
        function confirmDelete(code) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data berita yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + code).submit();
                }
            });
        }

        // Attachment functionality
        $(document).ready(function() {
            // Add new attachment
            $('#add-attachment').click(function() {
                const attachmentHtml = `
                    <div class="attachment-item mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select attachment-type" name="attachment_types[]">
                                    <option value="file">Upload File</option>
                                    <option value="link">Link External</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="attachment_titles[]" placeholder="Judul lampiran (opsional)">
                            </div>
                            <div class="col-md-4">
                                <input type="file" class="form-control attachment-file" name="attachment_files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                <input type="url" class="form-control attachment-link d-none" name="attachment_links[]" placeholder="https://...">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-attachment">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#attachments-container').append(attachmentHtml);
            });

            // Remove attachment
            $(document).on('click', '.remove-attachment', function() {
                if ($('.attachment-item').length > 1) {
                    $(this).closest('.attachment-item').remove();
                } else {
                    Swal.fire('Info', 'Minimal harus ada satu lampiran', 'info');
                }
            });

            // Toggle between file and link input
            $(document).on('change', '.attachment-type', function() {
                const container = $(this).closest('.attachment-item');
                const fileInput = container.find('.attachment-file');
                const linkInput = container.find('.attachment-link');
                
                if ($(this).val() === 'file') {
                    fileInput.removeClass('d-none');
                    linkInput.addClass('d-none');
                    linkInput.val('');
                } else {
                    fileInput.addClass('d-none');
                    linkInput.removeClass('d-none');
                    fileInput.val('');
                }
            });

            // Add attachment for edit modal
            $(document).on('click', '.add-edit-attachment', function() {
                const targetContainer = $(this).data('target');
                const attachmentHtml = `
                    <div class="attachment-item mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select attachment-type" name="attachment_types[]">
                                    <option value="file">Upload File</option>
                                    <option value="link">Link External</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="attachment_titles[]" placeholder="Judul lampiran (opsional)">
                            </div>
                            <div class="col-md-4">
                                <input type="file" class="form-control attachment-file" name="attachment_files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                <input type="url" class="form-control attachment-link d-none" name="attachment_links[]" placeholder="https://...">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-attachment">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#' + targetContainer).append(attachmentHtml);
            });
        });

        // Dynamic Content Builder functionality
        let contentBuilder = null;
        let editContentBuilders = {};

        // Initialize content builder for add form
        function initializeContentBuilder() {
            if (contentBuilder) {
                contentBuilder.clear();
            }
            contentBuilder = initDynamicContentBuilder('dynamic-content-builder');
        }

        // Initialize content builder for edit forms
        function initializeEditContentBuilder(code, existingContent = null) {
            const builderId = `edit-dynamic-content-builder-${code}`;
            if (editContentBuilders[code]) {
                editContentBuilders[code].clear();
            }
            editContentBuilders[code] = initDynamicContentBuilder(builderId);
            
            if (existingContent) {
                editContentBuilders[code].loadContent(existingContent);
            }
        }

        // Content type toggle for add form
        $(document).on('change', 'input[name="content_type"]', function() {
            const isSimple = $(this).val() === 'simple';
            const simpleContainer = $('#simple-content-container');
            const dynamicContainer = $('#dynamic-content-container');
            const contentField = $('#content');
            
            if (isSimple) {
                simpleContainer.removeClass('d-none');
                dynamicContainer.addClass('d-none');
                contentField.prop('required', true);
                $('#dynamic_content_input').val('');
            } else {
                simpleContainer.addClass('d-none');
                dynamicContainer.removeClass('d-none');
                contentField.prop('required', false);
                contentField.val('');
                
                // Initialize content builder if not already done
                if (!contentBuilder) {
                    initializeContentBuilder();
                }
            }
        });

        // Content type toggle for edit forms
        $(document).on('change', '.edit-content-type', function() {
            const isSimple = $(this).val() === 'simple';
            const code = $(this).attr('id').split('_').pop();
            const simpleContainer = $(`#edit-simple-content-container-${code}`);
            const dynamicContainer = $(`#edit-dynamic-content-container-${code}`);
            const contentField = $(`#edit_content${code}`);
            
            if (isSimple) {
                simpleContainer.removeClass('d-none');
                dynamicContainer.addClass('d-none');
                contentField.prop('required', true);
                $(`#edit_dynamic_content_input_${code}`).val('');
            } else {
                simpleContainer.addClass('d-none');
                dynamicContainer.removeClass('d-none');
                contentField.prop('required', false);
                contentField.val('');
                
                // Initialize content builder for this edit form
                const existingContent = $(`#edit_dynamic_content_input_${code}`).val();
                initializeEditContentBuilder(code, existingContent);
            }
        });

        // Form submission handling
        $('form').on('submit', function(e) {
            const form = $(this);
            const contentType = form.find('input[name="content_type"]:checked').val();
            
            if (contentType === 'dynamic') {
                e.preventDefault(); // Prevent default form submission
                
                // Check if this is add form or edit form
                const isEditForm = form.find('[id^="edit_dynamic_content_input_"]').length > 0;
                
                // Create FormData from the form
                const formData = new FormData(form[0]);
                
                if (isEditForm) {
                    // Handle edit form
                    const code = form.find('[id^="edit_dynamic_content_input_"]').attr('id').split('_').pop();
                    if (editContentBuilders[code]) {
                        // Use prepareFormData method to handle files
                        editContentBuilders[code].prepareFormData(formData);
                    }
                } else {
                    // Handle add form
                    if (contentBuilder) {
                        // Use prepareFormData method to handle files
                        contentBuilder.prepareFormData(formData);
                    }
                }
                
                // Submit form using AJAX with FormData
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method') || 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle success - redirect or show success message
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        // Handle error
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            // Display validation errors
                            Object.keys(errors).forEach(key => {
                                const errorElement = form.find(`[name="${key}"]`).siblings('.text-danger');
                                if (errorElement.length) {
                                    errorElement.text(errors[key][0]);
                                } else {
                                    // Create error element if it doesn't exist
                                    form.find(`[name="${key}"]`).after(`<small class="text-danger">${errors[key][0]}</small>`);
                                }
                            });
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    }
                });
            } else {
                // For simple content, use normal form submission
                if (contentType === 'simple') {
                    // Clear dynamic content input
                    form.find('input[name="dynamic_content"]').val('');
                }
            }
        });

        // Initialize edit content builders when modals are shown
        $('.modal').on('shown.bs.modal', function() {
            const modal = $(this);
            const code = modal.attr('id').replace('editData', '');
            const contentType = modal.find('.edit-content-type:checked').val();
            
            if (contentType === 'dynamic') {
                const existingContent = $(`#edit_dynamic_content_input_${code}`).val();
                initializeEditContentBuilder(code, existingContent);
            }
        });

        // Initialize content builder when dynamic content is first selected
        $(document).ready(function() {
            // Check if dynamic content is selected by default in any edit form
            $('.edit-content-type:checked').each(function() {
                if ($(this).val() === 'dynamic') {
                    const code = $(this).attr('id').split('_').pop();
                    const existingContent = $(`#edit_dynamic_content_input_${code}`).val();
                    if (existingContent) {
                        // Delay initialization to ensure DOM is ready
                        setTimeout(() => {
                            initializeEditContentBuilder(code, existingContent);
                        }, 100);
                    }
                }
            });
        });
    </script>
@endsection
