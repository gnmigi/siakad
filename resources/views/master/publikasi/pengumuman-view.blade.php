@extends('core-themes.core-backpage')

@section('custom-css')
<style>
    .kategori-badge {
        background-color: #007bff;
        color: white;
        padding: 0.25rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    
    [data-bs-theme="dark"] .article-content {
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>{{ $pengumuman->name }}</h3>
                <span class="kategori-badge">{{ $pengumuman->kategori->name }}</span>
            </div>
            <div class="card-body">
                @if($pengumuman->photo)
                    <img src="{{ asset('storage/images/pengumuman/' . $pengumuman->photo) }}" alt="{{ $pengumuman->name }}" class="img-fluid mb-3" />
                @endif
                <div class="article-content">{!! nl2br(e($pengumuman->content)) !!}</div>
            </div>
            <div class="card-footer">
                <a href="{{ route($spref . 'publikasi.pengumuman-render') }}" class="btn btn-secondary">Kembali ke Daftar Pengumuman</a>
            </div>
        </div>
    </div>
</div>
@endsection
