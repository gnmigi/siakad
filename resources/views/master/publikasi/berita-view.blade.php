@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('css/dynamic-content-builder.css') }}">
    <style>
        .berita-content {
            line-height: 1.8;
            font-size: 1rem;
        }
        
        .berita-meta {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .berita-meta .badge {
            font-size: 0.875rem;
        }
        
        .content-block {
            margin-bottom: 1.5rem;
        }
        
        .content-block.text-block {
            font-size: 1rem;
            line-height: 1.8;
        }
        
        .content-block.image-block {
            text-align: center;
        }
        
        .content-block.image-block.text-left {
            text-align: left;
        }
        
        .content-block.image-block.text-right {
            text-align: right;
        }
        
        .content-block.image-block img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .content-block.image-block figcaption {
            margin-top: 0.5rem;
            font-style: italic;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .content-block.link-block .card {
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }
        
        .content-block.link-block .card:hover {
            border-color: #4e73df;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .content-block.link-block .card-title a {
            color: #4e73df;
            text-decoration: none;
        }
        
        .content-block.link-block .card-title a:hover {
            text-decoration: underline;
        }
        
        .content-block.divider-block hr {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, #e3e6f0, transparent);
            margin: 2rem 0;
        }
        
        .berita-attachments {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 2rem;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.3s ease;
        }
        
        .attachment-item:last-child {
            border-bottom: none;
        }
        
        .attachment-item:hover {
            background-color: #ffffff;
        }
        
        .attachment-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #4e73df;
            color: white;
            border-radius: 6px;
            margin-right: 1rem;
        }
        
        .attachment-link {
            color: #4e73df;
            text-decoration: none;
            font-weight: 500;
        }
        
        .attachment-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-2">{{ $berita->name }}</h3>
                        <div class="berita-meta">
                            <span class="badge bg-primary me-2">{{ $berita->kategori->name }}</span>
                            <span class="badge bg-{{ $berita->status == 'Publish' ? 'success' : ($berita->status == 'Draft' ? 'warning' : 'secondary') }}">
                                {{ $berita->status }}
                            </span>
                            <small class="text-muted ms-3">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $berita->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($berita->photo)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/images/berita/' . $berita->photo) }}" 
                             alt="{{ $berita->name }}" 
                             class="img-fluid rounded shadow" 
                             style="max-height: 400px; object-fit: cover;" />
                    </div>
                @endif
                
                <div class="berita-content">
                    @if($berita->isDynamicContent())
                        {{-- Render Dynamic Content --}}
                        @if($berita->dynamic_content && count($berita->dynamic_content) > 0)
                            @foreach($berita->formatted_dynamic_content as $block)
                                @if($block['type'] === 'text')
                                    <div class="content-block text-block">
                                        {!! nl2br($block['data']['content']) !!}
                                    </div>
                                @elseif($block['type'] === 'image')
                                    <figure class="content-block image-block text-{{ $block['data']['alignment'] ?? 'center' }}">
                                        <img src="{{ $block['data']['image_url'] }}" 
                                             class="img-fluid" 
                                             alt="{{ $block['data']['alt_text'] ?? '' }}">
                                        @if(!empty($block['data']['caption']))
                                            <figcaption class="figure-caption">{{ $block['data']['caption'] }}</figcaption>
                                        @endif
                                    </figure>
                                @elseif($block['type'] === 'link')
                                    <div class="content-block link-block">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ $block['data']['url'] }}" target="_blank" rel="noopener">
                                                        {{ $block['data']['title'] ?: $block['data']['url'] }}
                                                    </a>
                                                </h6>
                                                @if(!empty($block['data']['description']))
                                                    <p class="card-text">{{ $block['data']['description'] }}</p>
                                                @endif
                                                <small class="text-muted">{{ $block['data']['url'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($block['type'] === 'divider')
                                    <div class="content-block divider-block">
                                        <hr>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada konten dinamis yang tersedia.</p>
                        @endif
                    @else
                        {{-- Render Simple Content --}}
                        <div class="simple-content">
                            {!! nl2br($berita->content) !!}
                        </div>
                    @endif
                </div>
                
                {{-- Attachments Section --}}
                @if($berita->attachments && count($berita->attachments) > 0)
                    <div class="berita-attachments">
                        <h6 class="mb-3">
                            <i class="fas fa-paperclip me-2"></i>Lampiran
                        </h6>
                        @foreach($berita->formatted_attachments as $attachment)
                            <div class="attachment-item">
                                <div class="attachment-icon">
                                    @if($attachment['type'] === 'file')
                                        <i class="fas fa-file"></i>
                                    @else
                                        <i class="fas fa-external-link-alt"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    @if($attachment['type'] === 'file')
                                        <a href="{{ $attachment['url'] }}" 
                                           class="attachment-link" 
                                           target="_blank" 
                                           download>
                                            {{ $attachment['title'] }}
                                        </a>
                                        <small class="text-muted d-block">
                                            {{ $attachment['original_name'] ?? 'File' }} 
                                            @if(isset($attachment['size']))
                                                ({{ number_format($attachment['size'] / 1024, 1) }} KB)
                                            @endif
                                        </small>
                                    @else
                                        <a href="{{ $attachment['url'] }}" 
                                           class="attachment-link" 
                                           target="_blank" 
                                           rel="noopener">
                                            {{ $attachment['title'] }}
                                        </a>
                                        <small class="text-muted d-block">{{ $attachment['url'] }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route($spref . 'publikasi.berita-render') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Berita
                    </a>
                    <small class="text-muted">
                        @if($berita->updated_at != $berita->created_at)
                            Terakhir diperbarui: {{ $berita->updated_at->format('d M Y, H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
