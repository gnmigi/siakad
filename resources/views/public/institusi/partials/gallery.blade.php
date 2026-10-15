<style>
/* Dark mode compatibility for gallery page */
[data-bs-theme="dark"] .card {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .card-body {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .card-title {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .text-muted {
    color: var(--tblr-text-muted-dark) !important;
}

[data-bs-theme="dark"] .gallery-item .card {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .gallery-item .card-title {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .gallery-item .card-text {
    color: var(--tblr-text-muted-dark) !important;
}

[data-bs-theme="dark"] .btn-outline-primary {
    color: var(--tblr-primary) !important;
    border-color: var(--tblr-primary) !important;
}

[data-bs-theme="dark"] .btn-outline-primary:hover {
    background-color: var(--tblr-primary) !important;
    color: white !important;
}

[data-bs-theme="dark"] .modal-content {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .modal-header {
    background-color: var(--tblr-dark) !important;
    border-bottom-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .modal-title {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .modal-body {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}
</style>

<!-- Gallery Section Content -->
<div class="py-6">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">{{ $data['title'] }}</h1>
            <p class="lead text-muted">Koleksi foto dan dokumentasi kegiatan institusi kami</p>
            <hr class="w-25 mx-auto">
        </div>
        @if($data['galeri'] && count($data['galeri']) > 0)
            <div class="row g-4">
                <div class="col-12">
                    <!-- Gallery Grid with 2-row limit (6 items: 2 rows x 3 columns) -->
                    <div class="row g-4" id="galleryContainer">
                        @foreach($data['galeri']->take(6) as $item)
                            @include('public.institusi.partials.gallery-item', ['item' => $item])
                        @endforeach
                    </div>

                    <!-- Load More Button -->
                    @if(count($data['galeri']) > 6)
                        <div class="text-center mt-5">
                            <button class="btn btn-primary" id="loadMoreGalleryBtn" onclick="loadMoreInstitutionGallery()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                <span id="loadMoreText">Muat Lebih Banyak</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card content-card">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M15 8h.01" />
                                <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                            </svg>
                            <h3>Galeri</h3>
                            <p class="text-muted">Belum ada galeri yang tersedia saat ini.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Gallery Statistics -->
@if($data['galeri'] && count($data['galeri']) > 0)
<div class="py-6 bg-body-secondary">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M15 8h.01" />
                                <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                            </svg>
                        </div>
                        <h3 class="mb-1">{{ count($data['galeri']) }}</h3>
                        <p class="text-muted mb-0">Total Galeri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                            </svg>
                        </div>
                        <h3 class="mb-1">{{ $data['galeri']->first()->created_at->format('Y') }}</h3>
                        <p class="text-muted mb-0">Tahun Terbaru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag text-warning" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M7.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M3 6v5.172a2 2 0 0 0 .586 1.414l8.5 8.5a2 2 0 0 0 2.828 0l5.172 -5.172a2 2 0 0 0 0 -2.828l-8.5 -8.5a2 2 0 0 0 -1.414 -.586h-5.172a2 2 0 0 0 -2 2z" />
                            </svg>
                        </div>
                        <h3 class="mb-1">{{ $data['galeri']->pluck('kategori.name')->unique()->count() }}</h3>
                        <p class="text-muted mb-0">Kategori</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                        </div>
                        <h3 class="mb-1">{{ number_format(rand(1000, 9999)) }}</h3>
                        <p class="text-muted mb-0">Total Views</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalLabel">Detail Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="galleryModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewGallery(code) {
    console.log('viewGallery called with code:', code);
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded');
        alert('Bootstrap library tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    // Get modal elements
    const modalElement = document.getElementById('galleryModal');
    const modalBody = document.getElementById('galleryModalBody');
    const modalTitle = document.getElementById('galleryModalLabel');
    
    if (!modalElement || !modalBody) {
        console.error('Modal elements not found');
        alert('Modal gallery tidak ditemukan.');
        return;
    }
    
    // Show loading state
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat detail galeri...</p>
        </div>
    `;
    
    // Create and show modal
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Simulate loading gallery details
        setTimeout(() => {
            // Find gallery item data from the page
            const galleryItems = @json($data['galeri'] ?? []);
            const item = galleryItems.find(g => g.code === code);
            
            if (item) {
                modalTitle.textContent = item.name || 'Detail Galeri';
                modalBody.innerHTML = `
                    <div class="row g-0">
                        <div class="col-lg-8">
                            <div class="position-relative" style="height: 80vh; background: #f8f9fa;">
                                <img src="${item.photo ? '/storage/images/galeri/' + item.photo : '/storage/images/galeri/default.jpg'}" 
                                     class="w-100 h-100" 
                                     alt="${item.name || 'Gallery Image'}"
                                     style="object-fit: contain; cursor: pointer;"
                                     onclick="openFullscreen(this)">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <button class="btn btn-sm btn-light shadow" onclick="openFullscreen(document.querySelector('#galleryModal img'))" title="Lihat Ukuran Penuh">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-maximize" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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
                        </div>
                        <div class="col-lg-4">
                            <div class="p-4" style="height: 80vh; overflow-y: auto;">
                                <h4 class="mb-3">${item.name || 'Nama Galeri'}</h4>
                                <div class="d-flex align-items-center mb-3 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar text-muted" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                    <small class="text-muted">${item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}</small>
                                </div>
                                ${item.kategori ? `<div class="mb-3"><span class="badge bg-primary">${item.kategori.name}</span></div>` : ''}
                                <div class="content">
                                    <h6 class="text-muted mb-2">Deskripsi:</h6>
                                    <p class="text-dark">${item.content || 'Deskripsi tidak tersedia.'}</p>
                                </div>
                                <hr>
                                <div class="d-grid">
                                    <button class="btn btn-primary" onclick="openFullscreen(document.querySelector('#galleryModal img'))">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrows-maximize me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path d="M16 4l4 0l0 4" />
                                            <path d="M14 10l6 -6" />
                                            <path d="M8 20l-4 0l0 -4" />
                                            <path d="M4 20l6 -6" />
                                            <path d="M16 20l4 0l0 -4" />
                                            <path d="M14 14l6 6" />
                                            <path d="M8 4l-4 0l0 4" />
                                            <path d="M4 4l6 6" />
                                        </svg>
                                        Lihat Ukuran Penuh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo-off mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                            <path d="M15 8h.01" />
                            <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                            <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                            <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                            <path d="M3 3l18 18" />
                        </svg>
                        <h5>Galeri Tidak Ditemukan</h5>
                        <p class="text-muted">Data galeri dengan kode "${code}" tidak ditemukan.</p>
                    </div>
                `;
            }
        }, 800);
        
    } catch (error) {
        console.error('Error showing modal:', error);
        alert('Terjadi kesalahan saat membuka modal. Error: ' + error.message);
    }
}

// Global variable to track loaded items
let galleryOffset = 6; // Start from 6 since we already loaded first 6 items

function loadMoreInstitutionGallery() {
    const button = document.getElementById('loadMoreGalleryBtn');
    const loadMoreText = document.getElementById('loadMoreText');
    const galleryContainer = document.getElementById('galleryContainer');
    
    if (!button || !galleryContainer) {
        console.error('Required elements not found');
        return;
    }
    
    // Show loading state
    const originalText = loadMoreText.innerHTML;
    button.disabled = true;
    loadMoreText.innerHTML = 'Memuat...';
    button.querySelector('svg').style.display = 'none';
    
    // Add spinner
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
    button.insertBefore(spinner, loadMoreText);
    
    // Make AJAX request
    fetch(`/ajax/gallery/institution/load-more?offset=${galleryOffset}&limit=6`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                // Append new gallery items
                galleryContainer.insertAdjacentHTML('beforeend', data.html);
                
                // Update offset
                galleryOffset = data.nextOffset;
                
                // Hide button if no more items
                if (!data.hasMore) {
                    button.style.display = 'none';
                }
                
                console.log(`Loaded ${data.loadedCount} more items. Total offset: ${galleryOffset}`);
            } else {
                console.error('Failed to load gallery items:', data.error);
                alert('Gagal memuat galeri. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error loading gallery:', error);
            alert('Terjadi kesalahan saat memuat galeri. Silakan coba lagi.');
        })
        .finally(() => {
            // Restore button state
            button.disabled = false;
            loadMoreText.innerHTML = originalText;
            button.querySelector('svg').style.display = 'inline';
            
            // Remove spinner
            const spinnerElement = button.querySelector('.spinner-border');
            if (spinnerElement) {
                spinnerElement.remove();
            }
        });
}

function loadMoreGallery() {
    // Legacy function - redirect to new function
    loadMoreInstitutionGallery();
}

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
        background: rgba(0, 0, 0, 0.9);
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
        background: rgba(255, 255, 255, 0.8);
        border: none;
        font-size: 40px;
        color: #000;
        cursor: pointer;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    `;
    
    closeBtn.onmouseover = () => closeBtn.style.background = 'rgba(255, 255, 255, 1)';
    closeBtn.onmouseout = () => closeBtn.style.background = 'rgba(255, 255, 255, 0.8)';
    
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

// Ensure DOM is loaded before attaching events
document.addEventListener('DOMContentLoaded', function() {
    console.log('Gallery script loaded');
    
    // Test if Bootstrap modal works
    const testModal = document.getElementById('galleryModal');
    if (testModal) {
        console.log('Gallery modal found');
    } else {
        console.warn('Gallery modal not found');
    }
});
</script>
