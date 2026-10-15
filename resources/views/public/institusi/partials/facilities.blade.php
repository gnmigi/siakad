<!-- Facilities Section Content -->
<div class="py-6">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">{{ $data['title'] }}</h1>
            <p class="lead text-muted">Mengenal berbagai fasilitas dan sarana prasarana institusi kami</p>
            <hr class="w-25 mx-auto">
        </div>
        @if($data['page'] && $data['page']->is_active)
        <div class="row g-4">
            <div class="col-12">
                    <!-- Main Content -->
                    <div class="card content-card">
                        <div class="card-body p-4">
                            <h2 class="section-title">{{ $data['page']->title ?? 'Fasilitas' }}</h2>
                            
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
                                <!-- Facilities Grid -->
                                @if(isset($data['page']->additional_data['facilities']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Fasilitas Tersedia</h3>
                                        <div class="row g-4">
                                            @foreach($data['page']->additional_data['facilities'] as $fasilitas)
                                                <div class="col-md-6">
                                                    <div class="card facility-card">
                                                        @if(isset($fasilitas['image']) && $fasilitas['image'])
                                                            <img src="{{ asset('storage/images/institusi/facilities/' . $fasilitas['image']) }}"
                                                                 class="card-img-top facility-image"
                                                                 alt="{{ $fasilitas['name'] ?? '' }}">
                                                        @endif
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                    <path d="M3 21h18" />
                                                                    <path d="M5 21v-14l8 -4v18" />
                                                                    <path d="M19 21v-10l-6 -4" />
                                                                    <path d="M9 9v.01" />
                                                                    <path d="M9 12v.01" />
                                                                    <path d="M9 15v.01" />
                                                                    <path d="M9 18v.01" />
                                                                </svg>
                                                                {{ $fasilitas['name'] ?? '' }}
                                                            </h5>
                                                            <p class="card-text">{{ $fasilitas['description'] ?? '' }}</p>
                                                            @if(isset($fasilitas['kapasitas']) || isset($fasilitas['lokasi']))
                                                                <div class="d-flex flex-column gap-1">
                                                                    @if(isset($fasilitas['kapasitas']))
                                                                        <small class="text-muted d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                                            </svg>
                                                                            Kapasitas: {{ $fasilitas['kapasitas'] }}
                                                                        </small>
                                                                    @endif
                                                                    @if(isset($fasilitas['lokasi']))
                                                                        <small class="text-muted d-flex align-items-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                                                            </svg>
                                                                            {{ $fasilitas['lokasi'] }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Facility Categories -->
                                @if(isset($data['page']->additional_data['kategori_fasilitas']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Kategori Fasilitas</h3>
                                        <div class="row g-4">
                                            @foreach($data['page']->additional_data['kategori_fasilitas'] as $kategori)
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="card text-center">
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-category text-primary" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                    <path d="M4 4h6v6h-6z" />
                                                                    <path d="M14 4h6v6h-6z" />
                                                                    <path d="M4 14h6v6h-6z" />
                                                                    <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                                </svg>
                                                            </div>
                                                            <h5 class="card-title">{{ $kategori['nama'] ?? '' }}</h5>
                                                            <p class="card-text">{{ $kategori['deskripsi'] ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card content-card">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M3 21h18" />
                                <path d="M5 21v-14l8 -4v18" />
                                <path d="M19 21v-10l-6 -4" />
                                <path d="M9 9v.01" />
                                <path d="M9 12v.01" />
                                <path d="M9 15v.01" />
                                <path d="M9 18v.01" />
                            </svg>
                            <h3>Fasilitas</h3>
                            <p class="text-muted">Informasi fasilitas belum tersedia atau sedang dalam proses pembaruan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Virtual Tour Section -->
@if($data['page'] && $data['page']->is_active && $data['page']->additional_data && isset($data['page']->additional_data['virtual_tour']))
<div class="py-6 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Virtual Tour</h2>
            <p class="lead">Jelajahi fasilitas kami secara virtual</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-360 text-primary mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path d="M17 15.328c2.414 -.718 4 -1.94 4 -3.328c0 -2.21 -4.03 -4 -9 -4s-9 1.79 -9 4s4.03 4 9 4" />
                            <path d="M9 13l3 3l3 -3" />
                        </svg>
                        <h4>Virtual Tour 360°</h4>
                        <p class="text-muted mb-4">Nikmati pengalaman virtual tour fasilitas institusi kami</p>
                        <a href="{{ $data['page']->additional_data['virtual_tour'] }}" class="btn btn-primary" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                <path d="M11 13l9 -9" />
                                <path d="M15 4h5v5" />
                            </svg>
                            Mulai Virtual Tour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
