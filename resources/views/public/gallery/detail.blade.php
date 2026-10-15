@extends('core-themes.core-mainpage')

@section('title', $galeri->name . ' - ' . $academy)

@section('custom-css')
<style>
/* Dark mode compatibility for gallery detail page */
[data-bs-theme="dark"] .card {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .card-body {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .position-relative {
    background: var(--tblr-dark) !important;
}

[data-bs-theme="dark"] .text-muted {
    color: var(--tblr-text-muted-dark) !important;
}

[data-bs-theme="dark"] .breadcrumb {
    background-color: transparent !important;
}

[data-bs-theme="dark"] .breadcrumb-item a {
    color: var(--tblr-primary) !important;
}

[data-bs-theme="dark"] .breadcrumb-item.active {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .btn-light {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .btn-light:hover {
    background-color: var(--tblr-gray-800) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .btn-outline-primary {
    color: var(--tblr-primary) !important;
    border-color: var(--tblr-primary) !important;
}

[data-bs-theme="dark"] .btn-outline-primary:hover {
    background-color: var(--tblr-primary) !important;
    color: white !important;
}

[data-bs-theme="dark"] .btn-outline-secondary {
    color: var(--tblr-secondary) !important;
    border-color: var(--tblr-secondary) !important;
}

[data-bs-theme="dark"] .btn-outline-secondary:hover {
    background-color: var(--tblr-secondary) !important;
    color: white !important;
}

/* Ensure image container has proper dark mode background */
[data-bs-theme="dark"] .gallery-image-container {
    background: var(--tblr-gray-900) !important;
}

/* Fix sidebar cards in dark mode */
[data-bs-theme="dark"] .sidebar-card {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .sidebar-card .card-title {
    color: var(--tblr-body-color-dark) !important;
}

/* Fix gallery grid items in dark mode */
[data-bs-theme="dark"] .gallery-grid-item .card {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .gallery-grid-item .card-title {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .gallery-grid-item .text-muted {
    color: var(--tblr-text-muted-dark) !important;
}
</style>
@endsection

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('root.home-index') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('root.institution.gallery') }}">Galeri</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $galeri->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <!-- Large Image Display -->
                    <div class="position-relative" style="height: 60vh; background: #f8f9fa;">
                        <img src="{{ asset('storage/images/galeri/' . $galeri->photo) }}" 
                             class="w-100 h-100" 
                             alt="{{ $galeri->name }}"
                             style="object-fit: contain; cursor: pointer;"
                             onclick="openFullscreen(this)">
                        
                        <!-- Fullscreen Button -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <button class="btn btn-light shadow-sm" onclick="openFullscreen(document.querySelector('.gallery-main-image'))" title="Lihat Ukuran Penuh">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-maximize" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M16 4l4 0l0 4" />
                                    <path d="M14 10l6 -6" />
                                    <path d="M8 20l-4 0l0 -4" />
                                    <path d="M4 20l6 -6" />
                                    <path d="M16 20l4 0l0 -4" />
                                    <path d="M14 14l6 6" />
                                    <path d="M8 4l-4 0l0 4" />
                                    <path d="M4 4l6 6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Gallery Info -->
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h3 mb-2">{{ $galeri->name }}</h1>
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                            <path d="M16 3v4" />
                                            <path d="M8 3v4" />
                                            <path d="M4 11h16" />
                                        </svg>
                                        {{ $galeri->created_at->format('d M Y') }}
                                    </span>
                                    @if($galeri->kategori)
                                        <span class="badge bg-primary">{{ $galeri->kategori->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="shareGallery()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-share me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M8.7 10.7l6.6 -3.4" />
                                        <path d="M8.7 13.3l6.6 3.4" />
                                    </svg>
                                    Bagikan
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="openFullscreen(document.querySelector('.gallery-main-image'))">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-maximize me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M16 4l4 0l0 4" />
                                        <path d="M14 10l6 -6" />
                                        <path d="M8 20l-4 0l0 -4" />
                                        <path d="M4 20l6 -6" />
                                        <path d="M16 20l4 0l0 -4" />
                                        <path d="M14 14l6 6" />
                                        <path d="M8 4l-4 0l0 4" />
                                        <path d="M4 4l6 6" />
                                    </svg>
                                    Ukuran Penuh
                                </button>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="content">
                            <h5 class="mb-3">Deskripsi</h5>
                            <div class="text-muted">
                                {!! nl2br(e($galeri->content)) !!}
                            </div>
                        </div>
                        
                        <!-- Gallery Photos if available -->
                        @if($galeri->fotos && count($galeri->fotos) > 0)
                            <hr class="my-4">
                            <h5 class="mb-3">Foto Lainnya ({{ count($galeri->fotos) }})</h5>
                            <div class="row g-3">
                                @foreach($galeri->fotos->take(6) as $foto)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="ratio ratio-1x1">
                                            <img src="{{ asset('storage/images/galeri/foto/' . $foto->photo) }}" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 alt="Foto {{ $loop->iteration }}"
                                                 style="object-fit: cover; cursor: pointer;"
                                                 onclick="openFullscreen(this)">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($galeri->fotos) > 6)
                                <div class="text-center mt-3">
                                    <small class="text-muted">Dan {{ count($galeri->fotos) - 6 }} foto lainnya...</small>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Navigation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Navigasi</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('root.institution.gallery') }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M5 12l14 0" />
                                <path d="M5 12l6 6" />
                                <path d="M5 12l6 -6" />
                            </svg>
                            Kembali ke Galeri
                        </a>
                        <a href="{{ route('root.home-index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                            Beranda
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Other Gallery Items -->
            @if($otherGaleri && count($otherGaleri) > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Galeri Lainnya</h6>
                        <div class="row g-3">
                            @foreach($otherGaleri as $item)
                                <div class="col-6">
                                    <a href="{{ route('root.gallery.detail', $item->code) }}" class="text-decoration-none">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="ratio ratio-4x3">
                                                <img src="{{ asset('storage/images/galeri/' . $item->photo) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $item->name }}"
                                                     style="object-fit: cover;">
                                            </div>
                                            <div class="card-body p-2">
                                                <h6 class="card-title small mb-1">{{ Str::limit($item->name, 30) }}</h6>
                                                <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Function to open image in fullscreen
function openFullscreen(img) {
    // Create fullscreen overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    `;
    
    // Create fullscreen image
    const fullImg = document.createElement('img');
    fullImg.src = img.src;
    fullImg.alt = img.alt;
    fullImg.style.cssText = `
        max-width: 95%;
        max-height: 95%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    `;
    
    // Create close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '×';
    closeBtn.style.cssText = `
        position: absolute;
        top: 20px;
        right: 30px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-size: 40px;
        color: #000;
        cursor: pointer;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-weight: 300;
    `;
    
    closeBtn.onmouseover = () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 1)';
        closeBtn.style.transform = 'scale(1.1)';
    };
    closeBtn.onmouseout = () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.9)';
        closeBtn.style.transform = 'scale(1)';
    };
    
    // Add elements to overlay
    overlay.appendChild(fullImg);
    overlay.appendChild(closeBtn);
    
    // Close functions
    const closeFullscreen = () => {
        document.body.removeChild(overlay);
        document.body.style.overflow = 'auto';
    };
    
    overlay.onclick = closeFullscreen;
    closeBtn.onclick = closeFullscreen;
    
    // Prevent image click from closing
    fullImg.onclick = (e) => e.stopPropagation();
    
    // Add keyboard support
    const handleKeydown = (e) => {
        if (e.key === 'Escape') {
            closeFullscreen();
            document.removeEventListener('keydown', handleKeydown);
        }
    };
    document.addEventListener('keydown', handleKeydown);
    
    // Add to body and prevent scrolling
    document.body.style.overflow = 'hidden';
    document.body.appendChild(overlay);
}

// Function to share gallery
function shareGallery() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $galeri->name }}',
            text: 'Lihat galeri: {{ $galeri->name }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}

// Add class to main image for easier targeting
document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.querySelector('.position-relative img');
    if (mainImg) {
        mainImg.classList.add('gallery-main-image');
    }
});
</script>
@endsection
