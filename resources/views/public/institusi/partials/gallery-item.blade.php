<div class="col-12 col-md-6 col-lg-4">
    <div class="gallery-item">
        <div class="card">
            @if($item->photo)
                <img src="{{ asset('storage/images/galeri/' . $item->photo) }}" 
                     alt="{{ $item->name }}" 
                     class="card-img-top"
                     style="height: 250px; object-fit: cover;">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $item->name }}</h5>
                <p class="card-text">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                        {{ $item->created_at->format('d M Y') }}
                    </small>
                    @if($item->kategori)
                        <span class="badge bg-primary">{{ $item->kategori->name }}</span>
                    @endif
                </div>
                        <div class="mt-3">
                            <a href="{{ route('root.gallery.detail', $item->code) }}" class="btn btn-sm btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                                Lihat Detail
                            </a>
                        </div>
            </div>
        </div>
    </div>
</div>
