@extends('core-themes.core-mainpage')

@section('custom-css')
<style>
    /* Main Layout */
    .blog-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .main-content {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    [data-bs-theme="dark"] .main-content {
        background: #1a1a1a;
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.05);
    }
    
    /* Breadcrumb */
    .breadcrumb-nav {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1rem 0;
        margin-bottom: 2rem;
    }
    
    .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
    }
    
    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .breadcrumb-item a:hover {
        color: white;
    }
    
    .breadcrumb-item.active {
        color: white;
        font-weight: 500;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(255, 255, 255, 0.6);
    }
    
    /* Article Header */
    .article-header {
        padding: 2rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    [data-bs-theme="dark"] .article-header {
        border-bottom-color: #333;
    }
    
    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    [data-bs-theme="dark"] .article-title {
        color: #fff;
    }
    
    .article-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    
    /* Fixed Category Badge - Main Fix */
    .article-category {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 10px rgba(79, 70, 229, 0.3);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    /* Dark mode category styling */
    [data-bs-theme="dark"] .article-category {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.4);
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Light mode fallback - ensures visibility */
    .article-category {
        background: #4f46e5 !important; /* Fallback solid color */
        color: white !important;
    }
    
    [data-bs-theme="dark"] .article-category {
        background: #6366f1 !important; /* Dark mode fallback */
        color: white !important;
    }
    
    .article-date {
        color: #6b7280;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    [data-bs-theme="dark"] .article-date {
        color: #9ca3af;
    }
    
    .article-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-top: 1rem;
    }
    
    /* Article Content */
    .article-body {
        padding: 2rem;
    }
    
    .article-content {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #374151;
    }
    
    [data-bs-theme="dark"] .article-content {
        color: #d1d5db;
    }
    
    .article-content p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }
    
    .article-content h2, 
    .article-content h3, 
    .article-content h4 {
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: #1f2937;
        font-weight: 600;
    }
    
    [data-bs-theme="dark"] .article-content h2,
    [data-bs-theme="dark"] .article-content h3,
    [data-bs-theme="dark"] .article-content h4 {
        color: #f9fafb;
    }
    
    .article-content h2 {
        font-size: 1.875rem;
        border-bottom: 3px solid #4f46e5;
        padding-bottom: 0.5rem;
    }
    
    .article-content h3 {
        font-size: 1.5rem;
    }
    
    .article-content h4 {
        font-size: 1.25rem;
    }
    
    /* Sidebar */
    .sidebar {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }
    
    [data-bs-theme="dark"] .sidebar {
        background: #1f2937;
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.02);
    }
    
    .sidebar-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    [data-bs-theme="dark"] .sidebar-title {
        color: #f9fafb;
        border-bottom-color: #374151;
    }
    
    .sidebar-item {
        display: block;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: white;
        border-radius: 0.75rem;
        text-decoration: none;
        color: #374151;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    [data-bs-theme="dark"] .sidebar-item {
        background: #111827;
        color: #d1d5db;
        border-color: #374151;
    }
    
    .sidebar-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #4f46e5;
        border-color: #4f46e5;
    }
    
    [data-bs-theme="dark"] .sidebar-item:hover {
        color: #818cf8;
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    
    .sidebar-item-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .sidebar-item-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    [data-bs-theme="dark"] .sidebar-item-meta {
        color: #9ca3af;
    }
    
    .sidebar-item-category {
        background: #e0e7ff;
        color: #4338ca;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    [data-bs-theme="dark"] .sidebar-item-category {
        background: #312e81;
        color: #c7d2fe;
    }
    
    /* Action Buttons */
    .action-buttons {
        padding: 1.5rem 2rem;
        border-top: 1px solid #e9ecef;
        background: #f8fafc;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    [data-bs-theme="dark"] .action-buttons {
        background: #111827;
        border-top-color: #374151;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        color: white;
    }
    
    .btn-secondary-custom {
        background: #6b7280;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-secondary-custom:hover {
        background: #4b5563;
        transform: translateY(-2px);
        color: white;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .article-title {
            font-size: 1.875rem;
        }
        
        .article-header,
        .article-body {
            padding: 1.5rem;
        }
        
        .article-content {
            font-size: 1rem;
        }
        
        .sidebar {
            margin-top: 2rem;
            position: static;
        }
        
        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .action-buttons {
            padding: 1rem;
            flex-direction: column;
        }
    }
    
    @media (max-width: 576px) {
        .article-title {
            font-size: 1.5rem;
        }
        
        .article-header,
        .article-body {
            padding: 1rem;
        }
        
        .breadcrumb-nav {
            padding: 0.75rem 0;
        }
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-nav">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('root.home-index') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $programStudi->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="blog-container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="main-content">
                    <!-- Article Header -->
                    <header class="article-header">
                        <h1 class="article-title">{{ $programStudi->name }}</h1>
                        <div class="article-meta">
                            <span class="article-category">{{ $programStudi->kategori->name }}</span>
                            <span class="article-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                {{ $programStudi->created_at->format('d F Y') }}
                            </span>
                        </div>
                        @if($programStudi->photo)
                            <img src="{{ asset('storage/images/program-studi/' . $programStudi->photo) }}" alt="{{ $programStudi->name }}" class="article-image" />
                        @endif
                    </header>
                    
                    <!-- Article Body -->
                    <div class="article-body">
                        <div class="article-content">
                            <h2>Deskripsi Program Studi</h2>
                            <p>{{ $programStudi->description }}</p>
                            
                            @if($programStudi->content)
                                <h2>Informasi Detail</h2>
                                {!! nl2br(e($programStudi->content)) !!}
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('root.home-index') }}" class="btn-secondary-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                        <button onclick="window.print()" class="btn-primary-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                            </svg>
                            Print
                        </button>
                    </div>
                </article>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="sidebar">
                    <!-- Related Program Studi -->
                    @if($otherProgramStudis->count() > 0)
                        <div class="mb-4">
                            <h3 class="sidebar-title">Program Studi Lainnya</h3>
                            @foreach($otherProgramStudis as $item)
                                <a href="{{ route('root.viewProgramStudi', $item->slug) }}" class="sidebar-item">
                                    <div class="sidebar-item-title">{{ $item->name }}</div>
                                    <div class="sidebar-item-meta">
                                        <span class="sidebar-item-category">{{ $item->kategori->name }}</span>
                                        <span>{{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Related Berita -->
                    @if($otherBerita->count() > 0)
                        <div class="mb-4">
                            <h3 class="sidebar-title">Berita Terbaru</h3>
                            @foreach($otherBerita as $item)
                                <a href="{{ route('root.viewBerita', $item->slug) }}" class="sidebar-item">
                                    <div class="sidebar-item-title">{{ $item->name }}</div>
                                    <div class="sidebar-item-meta">
                                        <span class="sidebar-item-category">{{ $item->kategori->name }}</span>
                                        <span>{{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Related Pengumuman -->
                    @if($otherPengumuman->count() > 0)
                        <div>
                            <h3 class="sidebar-title">Pengumuman Terbaru</h3>
                            @foreach($otherPengumuman as $item)
                                <a href="{{ route('root.viewPengumuman', $item->slug) }}" class="sidebar-item">
                                    <div class="sidebar-item-title">{{ $item->name }}</div>
                                    <div class="sidebar-item-meta">
                                        <span class="sidebar-item-category">{{ $item->kategori->name }}</span>
                                        <span>{{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages', 'Program Studi')
