<!-- Structure Section Content -->
<div class="py-6">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">{{ $data['title'] }}</h1>
            <p class="lead text-muted">Struktur organisasi dan kepemimpinan institusi kami</p>
            <hr class="w-25 mx-auto">
        </div>
        @if($data['page'] && $data['page']->is_active)
        <div class="row g-4">
            <div class="col-12">
                    <!-- Main Content -->
                    <div class="card content-card">
                        <div class="card-body p-4">
                            <h2 class="section-title">{{ $data['page']->title ?? 'Struktur Organisasi' }}</h2>
                            
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
                                <!-- Organizational Chart -->
                                @if(isset($data['page']->additional_data['bagan_organisasi']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Bagan Organisasi</h3>
                                        <div class="text-center mb-4">
                                            <img src="{{ asset($data['page']->additional_data['bagan_organisasi']) }}" 
                                                 alt="Bagan Organisasi" 
                                                 class="img-fluid rounded-4">
                                        </div>
                                    </div>
                                @endif

                                <!-- Leadership -->
                                @if(isset($data['page']->additional_data['struktur']) && !empty($data['page']->additional_data['struktur']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Struktur Kepemimpinan</h3>
                                        <div class="row g-4">
                                            @foreach($data['page']->additional_data['struktur'] as $pengurus)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card text-center h-100 leadership-card">
                                                        @if(isset($pengurus['foto']) && !empty($pengurus['foto']))
                                                            <div class="card-img-top d-flex justify-content-center pt-4">
                                                                <img src="{{ asset($pengurus['foto']) }}" 
                                                                     alt="{{ $pengurus['nama'] ?? '' }}" 
                                                                     class="leader-avatar">
                                                            </div>
                                                        @else
                                                            <div class="card-img-top d-flex justify-content-center pt-4">
                                                                <div class="leader-placeholder">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="60" height="60" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $pengurus['nama'] ?? '' }}</h5>
                                                            <p class="card-text text-primary fw-bold">{{ $pengurus['jabatan'] ?? '' }}</p>
                                                            @if(isset($pengurus['pendidikan']) && !empty($pengurus['pendidikan']))
                                                                <p class="card-text small text-muted">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                                        <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                                                                        <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                                                                    </svg>
                                                                    {{ $pengurus['pendidikan'] }}
                                                                </p>
                                                            @endif
                                                            @if(isset($pengurus['deskripsi']) && !empty($pengurus['deskripsi']))
                                                                <p class="card-text small">{{ $pengurus['deskripsi'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Departments -->
                                @if(isset($data['page']->additional_data['departemen']))
                                    <div class="mt-5">
                                        <h3 class="section-title">Departemen</h3>
                                        <div class="row g-3">
                                            @foreach($data['page']->additional_data['departemen'] as $departemen)
                                                <div class="col-md-6">
                                                    <div class="card">
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
                                                                {{ $departemen['nama'] ?? '' }}
                                                            </h5>
                                                            @if(isset($departemen['kepala']))
                                                                <p class="card-text">
                                                                    <strong>Kepala:</strong> {{ $departemen['kepala'] }}
                                                                </p>
                                                            @endif
                                                            @if(isset($departemen['deskripsi']))
                                                                <p class="card-text">{{ $departemen['deskripsi'] }}</p>
                                                            @endif
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hierarchy mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M12 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M5 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M19 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M6.5 17.5l5.5 -4.5l5.5 4.5" />
                                <path d="M12 7l0 6" />
                            </svg>
                            <h3>Struktur Organisasi</h3>
                            <p class="text-muted">Informasi struktur organisasi belum tersedia atau sedang dalam proses pembaruan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
