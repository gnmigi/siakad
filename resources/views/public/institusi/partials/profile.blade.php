<!-- Profile Section Content -->
<div class="py-6">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">{{ $data['title'] }}</h1>
            <p class="lead text-muted">Mengenal lebih dekat dengan sejarah, visi, dan misi institusi kami</p>
            <hr class="w-25 mx-auto">
        </div>
        <div class="row g-4">
            <div class="col-12">
                <!-- Main Content -->
                <div class="card content-card">
                    <div class="card-body p-4">
                        @if($data['page'] && $data['page']->is_active)
                            <h2 class="section-title">{{ $data['page']->title ?? 'Profil Institusi' }}</h2>
                            
                            @if($data['page']->image)
                                <div class="text-center mb-4">
                                    <img src="{{ asset($data['page']->image) }}" 
                                         alt="{{ $data['page']->title }}" 
                                         class="img-fluid rounded-4">
                                </div>
                            @endif
                            
                            <div class="text-content">
                                {!! $data['page']->content !!}
                            </div>

                            @if($data['page']->additional_data && is_array($data['page']->additional_data))
                                <!-- YouTube Video Section -->
                                @if(isset($data['page']->additional_data['youtube_url']) && !empty($data['page']->additional_data['youtube_url']))
                                    @php
                                        $youtube_url = $data['page']->additional_data['youtube_url'];
                                        $video_id = '';
                                        
                                        // Extract video ID from different YouTube URL formats
                                        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                                            $video_id = $matches[1];
                                        } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                                            $video_id = $matches[1];
                                        } elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                                            $video_id = $matches[1];
                                        }
                                        
                                        $embed_url = $video_id ? "https://www.youtube.com/embed/{$video_id}" : '';
                                    @endphp
                                    
                                    @if($embed_url)
                                        <div class="mt-5">
                                            <h3 class="section-title">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-video me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                                                    <rect x="3" y="6" width="12" height="12" rx="2" />
                                                </svg>
                                                Video Profil
                                            </h3>
                                            <div class="ratio ratio-16x9 mb-3">
                                                <iframe src="{{ $embed_url }}" 
                                                        title="Video Profil Kampus" 
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                                        allowfullscreen
                                                        class="rounded-4">
                                                </iframe>
                                            </div>
                                            <p class="text-muted small">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                    <path d="M12 9h.01" />
                                                    <path d="M11 12h1v4h1" />
                                                </svg>
                                            </p>
                                        </div>
                                    @endif
                                @endif
                                @if(isset($data['page']->additional_data['sejarah']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Sejarah</h3>
                                        <div class="text-content">
                                            {!! $data['page']->additional_data['sejarah'] !!}
                                        </div>
                                    </div>
                                @endif

                                @if(isset($data['page']->additional_data['prestasi']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Prestasi</h3>
                                        <div class="row g-3">
                                            @foreach($data['page']->additional_data['prestasi'] as $prestasi)
                                                <div class="col-md-6">
                                                    <div class="card prestasi-card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $prestasi['judul'] ?? '' }}</h5>
                                                            <p class="card-text">{{ $prestasi['deskripsi'] ?? '' }}</p>
                                                            @if(isset($prestasi['tahun']))
                                                                <small class="text-muted">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                                        <path d="M16 3v4" />
                                                                        <path d="M8 3v4" />
                                                                        <path d="M4 11h16" />
                                                                    </svg>
                                                                    Tahun: {{ $prestasi['tahun'] }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="text-center py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 9h.01" />
                                    <path d="M11 12h1v4h1" />
                                </svg>
                                <h3>Profil Institusi</h3>
                                <p class="text-muted">Informasi profil institusi belum tersedia atau sedang dalam proses pembaruan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
