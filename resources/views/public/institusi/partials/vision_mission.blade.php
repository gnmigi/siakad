<!-- Vision Mission Section Content -->
<style>
    .vision-mission-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .vision-mission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .vision-icon, .mission-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-info));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
    }

    .vision-content {
        font-size: 1.1rem;
        line-height: 1.6;
        text-align: center;
        font-style: italic;
        color: var(--tblr-muted);
    }

    .mission-content {
        text-align: left;
    }

    .mission-list {
        text-align: left;
    }

    .mission-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        text-align: left;
    }

    .mission-number {
        font-weight: bold;
        color: var(--tblr-primary);
        margin-right: 0.5rem;
        flex-shrink: 0;
        min-width: 1.5rem;
    }

    .mission-text {
        flex: 1;
        line-height: 1.5;
        text-align: left;
    }

    /* Arabic text detection and right alignment */
    .mission-text:lang(ar),
    .mission-text[dir="rtl"] {
        text-align: right;
        direction: rtl;
    }

    /* Auto-detect Arabic characters and apply RTL */
    .mission-text.arabic-text {
        text-align: right;
        direction: rtl;
    }

    .values-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .values-card:hover {
        transform: translateY(-5px);
    }

    .values-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
    }

    .content-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .content-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="py-6">
    <div class="container">
        <!-- Section Title -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">{{ $data['title'] }}</h1>
            <p class="lead text-muted">Mengenal visi, misi, dan tujuan institusi kami</p>
            <hr class="w-25 mx-auto">
        </div>
        @if($data['page'] && $data['page']->is_active)
            <div class="row g-4">
                <div class="col-12">
                    <!-- Vision and Mission Cards -->
                    @if($data['page']->additional_data && is_array($data['page']->additional_data))
                        <div class="row g-4 mb-4">
                            <!-- Vision Card -->
                            @if(isset($data['page']->additional_data['vision']) && !empty(trim($data['page']->additional_data['vision'])))
                                <div class="col-md-6">
                                    <div class="card vision-mission-card h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="vision-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </div>
                                            <h4 class="mb-3">VISI</h4>
                                            <div class="vision-content">
                                                {{ trim($data['page']->additional_data['vision']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Mission Card -->
                            @if(isset($data['page']->additional_data['mission']) && !empty(trim($data['page']->additional_data['mission'])))
                                <div class="col-md-6">
                                    <div class="card vision-mission-card h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="mission-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-target" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M12 12m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0" />
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                </svg>
                                            </div>
                                            <h4 class="mb-3">MISI</h4>
                                            <div class="mission-content">
                                                @php
                                                    // Clean mission text by removing existing numbers
                                                    $missionText = preg_replace('/^\d+\.\s*/m', '', $data['page']->additional_data['mission']);
                                                    // Split by newlines and clean each item
                                                    $missions = array_filter(array_map('trim', explode("\n", $missionText)));
                                                @endphp
                                                
                                                <div class="mission-list">
                                                    @foreach($missions as $index => $mission)
                                                        @if(!empty(trim($mission)))
                                                            <div class="mission-item">
                                                                <span class="mission-number">{{ $loop->iteration }}.</span>
                                                                <span class="mission-text" data-text="{{ trim($mission) }}">{{ trim($mission) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Other content sections (tujuan, sasaran) -->
                    @if(isset($data['page']->additional_data['tujuan']))
                        <div class="card content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-flag me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M5 5a5 5 0 0 1 7 0a5 5 0 0 0 7 0v9a5 5 0 0 1 -7 0a5 5 0 0 0 -7 0v-9z" />
                                        <path d="M5 21v-7" />
                                    </svg>
                                    TUJUAN
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-content">
                                    {!! $data['page']->additional_data['tujuan'] !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($data['page']->additional_data['sasaran']))
                        <div class="card content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-crosshair me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path d="M4 8v-2a2 2 0 0 1 2 -2h2" />
                                        <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                                        <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                                        <path d="M16 20h2a2 2 0 0 0 2 -2v-2" />
                                        <path d="M9 12l6 0" />
                                        <path d="M12 9l0 6" />
                                    </svg>
                                    SASARAN
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-content">
                                    {!! $data['page']->additional_data['sasaran'] !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card content-card">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            <h3>Visi & Misi</h3>
                            <p class="text-muted">Informasi visi dan misi institusi belum tersedia atau sedang dalam proses pembaruan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Values Section -->
@if($data['page'] && $data['page']->is_active && $data['page']->additional_data && isset($data['page']->additional_data['nilai']))
<div class="py-6 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Nilai-Nilai Institusi</h2>
            <p class="lead">Nilai-nilai yang menjadi landasan dalam setiap aktivitas institusi</p>
        </div>
        
        <div class="row g-4">
            @foreach($data['page']->additional_data['nilai'] as $index => $nilai)
                <div class="col-lg-3 col-md-6">
                    <div class="card values-card text-center">
                        <div class="card-body p-4">
                            <div class="values-icon" style="background: {{ ['var(--tblr-primary)', 'var(--tblr-success)', 'var(--tblr-warning)', 'var(--tblr-danger)', 'var(--tblr-purple)', 'var(--tblr-orange)'][$index % 6] }};">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-{{ ['heart', 'handshake', 'star', 'shield', 'bulb', 'users'][$index % 6] }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    @if($index % 6 == 0)
                                        <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.566" />
                                    @elseif($index % 6 == 1)
                                        <path d="M11 17a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0a2.5 2.5 0 0 0 2 0" />
                                        <path d="M5 7a4 4 0 0 1 4 -4h6a4 4 0 0 1 4 4v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10z" />
                                    @elseif($index % 6 == 2)
                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                    @elseif($index % 6 == 3)
                                        <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                                    @elseif($index % 6 == 4)
                                        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                                        <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" />
                                    @else
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    @endif
                                </svg>
                            </div>
                            <h5 class="card-title">{{ $nilai['nama'] ?? '' }}</h5>
                            <p class="card-text">{{ $nilai['deskripsi'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to detect Arabic characters
    function hasArabicCharacters(text) {
        const arabicRegex = /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/;
        return arabicRegex.test(text);
    }

    // Apply proper alignment to mission text
    const missionTexts = document.querySelectorAll('.mission-text');
    missionTexts.forEach(function(element) {
        const text = element.textContent || element.innerText;
        
        if (hasArabicCharacters(text)) {
            // Arabic text - apply RTL
            element.style.textAlign = 'right';
            element.style.direction = 'rtl';
            element.classList.add('text-end', 'arabic-text');
            
            // Also adjust the parent mission item
            const missionItem = element.closest('.mission-item');
            if (missionItem) {
                missionItem.style.flexDirection = 'row-reverse';
                const missionNumber = missionItem.querySelector('.mission-number');
                if (missionNumber) {
                    missionNumber.style.marginRight = '0';
                    missionNumber.style.marginLeft = '0.5rem';
                }
            }
        } else {
            // Non-Arabic text - apply LTR (left alignment)
            element.style.textAlign = 'left';
            element.style.direction = 'ltr';
            element.classList.add('text-start');
        }
    });
});
</script>
