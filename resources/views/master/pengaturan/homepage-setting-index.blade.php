@extends('core-themes.core-backpage')
@section('custom-css')
<style>
    .nav-pills .nav-link.active {
        background-color: var(--tblr-primary);
    }
    .card-tabs .nav-tabs .nav-link.active {
        background-color: var(--tblr-primary);
        color: white;
    }
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $menus }}</div>
                <h2 class="page-title">{{ $pages }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#hero-section" class="nav-link active" data-bs-toggle="tab">Hero Section</a>
                            </li>
                            <li class="nav-item">
                                <a href="#statistics" class="nav-link" data-bs-toggle="tab">Statistik</a>
                            </li>
                            <li class="nav-item">
                                <a href="#calendar" class="nav-link" data-bs-toggle="tab">Kalender Akademik</a>
                            </li>
                            <li class="nav-item">
                                <a href="#features" class="nav-link" data-bs-toggle="tab">Keunggulan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#programs" class="nav-link" data-bs-toggle="tab">Program Unggulan</a>
                            </li>
                            <li class="nav-item">
                                <a href="#news" class="nav-link" data-bs-toggle="tab">Berita Utama</a>
                            </li>
                            <li class="nav-item">
                                <a href="#gallery" class="nav-link" data-bs-toggle="tab">Galeri</a>
                            </li>
                            <li class="nav-item">
                                <a href="#faq" class="nav-link" data-bs-toggle="tab">FAQ</a>
                            </li>
                            <li class="nav-item">
                                <a href="#cta" class="nav-link" data-bs-toggle="tab">Call to Action</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Hero Section Tab -->
                            <div class="tab-pane active" id="hero-section">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="section" value="hero">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Judul Hero</label>
                                                <input type="text" class="form-control" name="hero_title" value="{{ $webs->hero_title ?? 'Universitas Masa Depan' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Subtitle Hero</label>
                                                <textarea class="form-control" name="hero_subtitle" rows="3" required>{{ $webs->hero_subtitle ?? 'Membentuk Pemimpin Digital untuk Era Transformasi Global' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Background Image</label>
                                                <input type="file" class="form-control" name="hero_background_image" accept="image/*" onchange="previewImage(this, 'hero_preview')">
                                                @if($webs->hero_background_image)
                                                    <img src="{{ asset('storage/images/homepage/' . $webs->hero_background_image) }}" id="hero_preview" class="image-preview">
                                                @else
                                                    <img id="hero_preview" class="image-preview" style="display: none;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Hero Section</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Statistics Tab -->
                            <div class="tab-pane" id="statistics">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section" value="statistics">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Program Studi</label>
                                                <input type="text" class="form-control" name="stat_programs_count" value="{{ $webs->stat_programs_count ?? '50+' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Label Program Studi</label>
                                                <input type="text" class="form-control" name="stat_programs_label" value="{{ $webs->stat_programs_label ?? 'Program Studi' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Mahasantri</label>
                                                <input type="text" class="form-control" name="stat_students_count" value="{{ $webs->stat_students_count ?? '15K+' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Label Mahasantri</label>
                                                <input type="text" class="form-control" name="stat_students_label" value="{{ $webs->stat_students_label ?? 'Mahasantri Aktif' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Dosen</label>
                                                <input type="text" class="form-control" name="stat_lecturers_count" value="{{ $webs->stat_lecturers_count ?? '500+' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Label Dosen</label>
                                                <input type="text" class="form-control" name="stat_lecturers_label" value="{{ $webs->stat_lecturers_label ?? 'Dosen Berkualitas' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Mitra</label>
                                                <input type="text" class="form-control" name="stat_partners_count" value="{{ $webs->stat_partners_count ?? '100+' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Label Mitra</label>
                                                <input type="text" class="form-control" name="stat_partners_label" value="{{ $webs->stat_partners_label ?? 'Mitra Industri' }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Statistik</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Academic Calendar Tab -->
                            <div class="tab-pane" id="calendar">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section" value="calendar">
                                    
                                    @php
                                        $calendar = $webs->academic_calendar ? json_decode($webs->academic_calendar, true) : [
                                            ['title' => 'Awal Perkuliahan', 'subtitle' => 'Semester Ganjil 2024/2025', 'date' => '4 Sep', 'month' => 'SEP', 'color' => 'primary'],
                                            ['title' => 'Ujian Tengah Semester', 'subtitle' => '15-26 Oktober 2024', 'date' => '15 Okt', 'month' => 'OKT', 'color' => 'yellow'],
                                            ['title' => 'Ujian Akhir Semester', 'subtitle' => '11-22 Desember 2024', 'date' => '11 Des', 'month' => 'DES', 'color' => 'red']
                                        ];
                                    @endphp
                                    
                                    <div id="calendar-items">
                                        @foreach($calendar as $index => $item)
                                            <div class="calendar-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label">Judul</label>
                                                            <input type="text" class="form-control" name="calendar[{{ $index }}][title]" value="{{ $item['title'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label">Subtitle</label>
                                                            <input type="text" class="form-control" name="calendar[{{ $index }}][subtitle]" value="{{ $item['subtitle'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal</label>
                                                            <input type="text" class="form-control" name="calendar[{{ $index }}][date]" value="{{ $item['date'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label class="form-label">Bulan</label>
                                                            <input type="text" class="form-control" name="calendar[{{ $index }}][month]" value="{{ $item['month'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label class="form-label">Warna</label>
                                                            <select class="form-control" name="calendar[{{ $index }}][color]">
                                                                <option value="primary" {{ $item['color'] == 'primary' ? 'selected' : '' }}>Biru</option>
                                                                <option value="yellow" {{ $item['color'] == 'yellow' ? 'selected' : '' }}>Kuning</option>
                                                                <option value="red" {{ $item['color'] == 'red' ? 'selected' : '' }}>Merah</option>
                                                                <option value="green" {{ $item['color'] == 'green' ? 'selected' : '' }}>Hijau</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Kalender Akademik</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Features Tab -->
                            <div class="tab-pane" id="features">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section" value="features">
                                    
                                    @php
                                        $features = $webs->features_section ? json_decode($webs->features_section, true) : [
                                            ['title' => 'Pembelajaran Digital', 'description' => 'Platform pembelajaran digital terintegrasi dengan akses 24/7 ke materi perkuliahan', 'icon' => 'device-laptop'],
                                            ['title' => 'Sertifikasi Industri', 'description' => 'Program sertifikasi profesional dari mitra industri terkemuka', 'icon' => 'certificate'],
                                            ['title' => 'Jaringan Global', 'description' => 'Kesempatan pertukaran pelajar dan kolaborasi internasional', 'icon' => 'world']
                                        ];
                                    @endphp
                                    
                                    <div id="features-items">
                                        @foreach($features as $index => $feature)
                                            <div class="feature-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Judul Keunggulan</label>
                                                            <input type="text" class="form-control" name="features[{{ $index }}][title]" value="{{ $feature['title'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" name="features[{{ $index }}][description]" rows="2">{{ $feature['description'] }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label class="form-label">Icon (Tabler)</label>
                                                            <input type="text" class="form-control" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] }}" placeholder="device-laptop">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Keunggulan</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Programs Tab -->
                            <div class="tab-pane" id="programs">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="section" value="programs">
                                    
                                    @php
                                        $programs = $webs->programs_section ? json_decode($webs->programs_section, true) : [
                                            ['title' => 'Fakultas Teknologi', 'description' => 'Teknik Informatika, Sistem Informasi, Data Science', 'image' => ''],
                                            ['title' => 'Fakultas Bisnis', 'description' => 'Manajemen, Akuntansi, Digital Business', 'image' => ''],
                                            ['title' => 'Fakultas Industri Kreatif', 'description' => 'Desain Komunikasi Visual, Animasi, Game Development', 'image' => '']
                                        ];
                                    @endphp
                                    
                                    <div id="programs-items">
                                        @foreach($programs as $index => $program)
                                            <div class="program-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Program</label>
                                                            <input type="text" class="form-control" name="programs[{{ $index }}][title]" value="{{ $program['title'] }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi</label>
                                                            <textarea class="form-control" name="programs[{{ $index }}][description]" rows="2">{{ $program['description'] }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Gambar Program</label>
                                                            <input type="file" class="form-control" name="programs[{{ $index }}][image]" accept="image/*" onchange="previewImage(this, 'program_preview_{{ $index }}')">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if(!empty($program['image']))
                                                            <img src="{{ asset('storage/images/homepage/' . $program['image']) }}" id="program_preview_{{ $index }}" class="image-preview">
                                                        @else
                                                            <img id="program_preview_{{ $index }}" class="image-preview" style="display: none;">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Program Unggulan</button>
                                    </div>
                                </form>
                            </div>

                            <!-- News Tab -->
                            <div class="tab-pane" id="news">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="section" value="news">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Judul Berita Utama</label>
                                                <input type="text" class="form-control" name="news_main_title" value="{{ $webs->news_main_title ?? 'International Technology Conference 2024' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Konten Berita</label>
                                                <textarea class="form-control" name="news_main_content" rows="4">{{ $webs->news_main_content ?? 'Konferensi teknologi tahunan dengan pembicara dari perusahaan teknologi global' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kategori</label>
                                                <input type="text" class="form-control" name="news_main_category" value="{{ $webs->news_main_category ?? 'Event' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Berita</label>
                                                <input type="date" class="form-control" name="news_main_date" value="{{ $webs->news_main_date ?? date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Gambar Berita Utama</label>
                                                <input type="file" class="form-control" name="news_main_image" accept="image/*" onchange="previewImage(this, 'news_preview')">
                                                @if($webs->news_main_image)
                                                    <img src="{{ asset('storage/images/homepage/' . $webs->news_main_image) }}" id="news_preview" class="image-preview">
                                                @else
                                                    <img id="news_preview" class="image-preview" style="display: none;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Berita Utama</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Gallery Tab -->
                            <div class="tab-pane" id="gallery">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="section" value="gallery">
                                    
                                    @php
                                        $gallery = $webs->gallery_section ? json_decode($webs->gallery_section, true) : [
                                            ['title' => 'Kehidupan Kampus', 'description' => 'Suasana akademik yang mendukung', 'image' => ''],
                                            ['title' => 'Perpustakaan Modern', 'description' => 'Pusat sumber daya pembelajaran', 'image' => ''],
                                            ['title' => 'Laboratorium Canggih', 'description' => 'Fasilitas penelitian terkini', 'image' => '']
                                        ];
                                    @endphp
                                    
                                    <div id="gallery-items">
                                        @foreach($gallery as $index => $item)
                                            <div class="gallery-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Judul Galeri</label>
                                                            <input type="text" class="form-control" name="gallery[{{ $index }}][title]" value="{{ $item['title'] }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi</label>
                                                            <input type="text" class="form-control" name="gallery[{{ $index }}][description]" value="{{ $item['description'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Gambar Galeri</label>
                                                            <input type="file" class="form-control" name="gallery[{{ $index }}][image]" accept="image/*" onchange="previewImage(this, 'gallery_preview_{{ $index }}')">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if(!empty($item['image']))
                                                            <img src="{{ asset('storage/images/homepage/' . $item['image']) }}" id="gallery_preview_{{ $index }}" class="image-preview">
                                                        @else
                                                            <img id="gallery_preview_{{ $index }}" class="image-preview" style="display: none;">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Galeri</button>
                                    </div>
                                </form>
                            </div>

                            <!-- FAQ Tab -->
                            <div class="tab-pane" id="faq">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section" value="faq">
                                    
                                    @php
                                        $faq = $webs->faq_section ? json_decode($webs->faq_section, true) : [
                                            ['question' => 'Bagaimana cara mendaftar kuliah?', 'answer' => 'Pendaftaran dapat dilakukan secara online melalui website PMB kami. Ikuti langkah-langkah yang tersedia dan siapkan dokumen yang diperlukan.'],
                                            ['question' => 'Apa saja program beasiswa yang tersedia?', 'answer' => 'Kami menyediakan berbagai program beasiswa, termasuk beasiswa akademik, beasiswa prestasi non-akademik, dan beasiswa kemitraan dengan industri.'],
                                            ['question' => 'Bagaimana sistem perkuliahan hybrid?', 'answer' => 'Sistem perkuliahan hybrid menggabungkan pembelajaran tatap muka dan online, didukung platform digital yang memudahkan akses materi dan interaksi.']
                                        ];
                                    @endphp
                                    
                                    <div id="faq-items">
                                        @foreach($faq as $index => $item)
                                            <div class="faq-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Pertanyaan</label>
                                                            <input type="text" class="form-control" name="faq[{{ $index }}][question]" value="{{ $item['question'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Jawaban</label>
                                                            <textarea class="form-control" name="faq[{{ $index }}][answer]" rows="3">{{ $item['answer'] }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan FAQ</button>
                                    </div>
                                </form>
                            </div>

                            <!-- CTA Tab -->
                            <div class="tab-pane" id="cta">
                                <form action="{{ route($spref . 'pengaturan.homepage-update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section" value="cta">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Judul CTA</label>
                                                <input type="text" class="form-control" name="cta_title" value="{{ $webs->cta_title ?? 'Siap Bergabung dengan Kami?' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Subtitle CTA</label>
                                                <textarea class="form-control" name="cta_subtitle" rows="2">{{ $webs->cta_subtitle ?? 'Daftar sekarang dan mulai perjalanan akademik Anda bersama kami.' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Teks Tombol</label>
                                                <input type="text" class="form-control" name="cta_button_text" value="{{ $webs->cta_button_text ?? 'Daftar Sekarang' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Link Tombol</label>
                                                <input type="url" class="form-control" name="cta_button_link" value="{{ $webs->cta_button_link ?? '#' }}" placeholder="https://example.com">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan Call to Action</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
