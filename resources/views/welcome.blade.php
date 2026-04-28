@extends('layouts.app')

@section('title', 'Selamat Datang di DigiRepo - Pusat Referensi Akademik Digital')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        padding: 160px 0 200px;
        color: white;
        position: relative;
    }
    
    .hero-section::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
        pointer-events: none;
    }

    .search-card {
        background: #ffffff;
        border-radius: 40px;
        padding: 12px;
        max-width: 900px;
        margin: 50px auto 0;
        box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        position: relative;
        z-index: 20;
    }

    .search-input-group {
        display: flex; gap: 10px;
    }

    .search-input-group input {
        border: none; background: transparent; color: #1e293b;
        padding: 15px 30px; font-size: 1.15rem; flex-grow: 1;
        font-weight: 500;
    }

    .search-input-group input:focus {
        outline: none;
        box-shadow: none;
    }

    .search-input-group input::placeholder { color: #94a3b8; }

    .search-input-group button:focus {
        outline: none;
        box-shadow: none;
    }

    .stat-overlay {
        margin-top: -100px; position: relative; z-index: 10;
    }

    .badge-soft { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: none; }

    @media (max-width: 991px) {
        .hero-section { padding: 100px 0 140px; }
        .search-card { border-radius: 24px; padding: 10px; margin-top: 30px; }
        .search-input-group { flex-direction: column; }
        .search-input-group input { padding: 12px 20px; font-size: 1rem; text-align: center; }
        .search-input-group button { width: 100%; border-radius: 15px !important; }
        .stat-overlay { margin-top: -60px; }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container animate-fade-in">
        <h1 class="display-3 fw-800 mb-3" style="letter-spacing: -2px;">{{ $siteHeroTitle ?: $siteName }}</h1>
        <p class="lead opacity-75 mb-5 mx-auto" style="max-width: 600px;">{{ $siteTagline }}</p>
        
        <div class="search-card">
            <form action="{{ url('/search') }}" method="GET" class="search-input-group">
                <input type="text" name="q" placeholder="Cari judul, penulis, atau kata kunci..." required>
                <button type="submit" class="btn btn-primary rounded-pill px-5">
                    <i class="fas fa-search me-2"></i> Cari
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="container stat-overlay">
    <div class="row g-4 text-center">
        @foreach([
            ['icon' => 'book-open', 'val' => $stats['total_theses'], 'label' => 'Karya Ilmiah', 'color' => 'primary'],
            ['icon' => 'users', 'val' => $stats['total_users'], 'label' => 'Penulis Aktif', 'color' => 'success'],
            ['icon' => 'eye', 'val' => $stats['total_views'], 'label' => 'Kunjungan Bulan Ini', 'color' => 'warning']
        ] as $item)
        <div class="col-md-4">
            <div class="glass-card py-4 border-0 hover-lift">
                <div class="bg-{{ $item['color'] }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-{{ $item['icon'] }} text-{{ $item['color'] }} fa-lg"></i>
                </div>
                <h2 class="fw-800 mb-0">{{ number_format($item['val']) }}</h2>
                <p class="text-secondary small fw-600 mb-0">{{ $item['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Latest Collection -->
<section class="container py-5 mt-5">
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h2 class="fw-800 mb-1">Koleksi Terbaru</h2>
            <p class="text-secondary">Terbitan karya ilmiah terpopuler bulan ini.</p>
        </div>
        <a href="{{ url('/browse') }}" class="btn btn-link text-primary fw-bold text-decoration-none">Lihat Semua <i class="fas fa-arrow-right ms-2"></i></a>
    </div>

    <div class="row g-4">
        @foreach($latestTheses as $thesis)
        <div class="col-md-4">
            <div class="glass-card hover-lift h-100 p-4 border-0">
                <div class="mb-3">
                    <span class="badge badge-soft px-3 py-2 rounded-pill small fw-bold">{{ $thesis->type }}</span>
                </div>
                <h5 class="fw-700 mb-3" style="line-height: 1.5; height: 3em; overflow: hidden;">
                    <a href="{{ route('theses.show', $thesis->id) }}" class="text-dark text-decoration-none">{{ $thesis->title }}</a>
                </h5>
                <div class="d-flex align-items-center gap-2 mb-4 text-secondary small">
                    <i class="fas fa-user-edit"></i>
                    <span>{{ $thesis->user->name }}</span>
                </div>
                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold"><i class="fas fa-calendar-alt me-1"></i> {{ $thesis->year }}</span>
                    <a href="{{ route('theses.show', $thesis->id) }}" class="text-primary fw-800 small text-decoration-none">Baca Selengkapnya</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Browse By -->
<section class="bg-white py-5 mt-5">
    <div class="container py-5 text-center">
        <h2 class="fw-800 mb-5">Jelajahi Repositori</h2>
        <div class="row g-4">
            @foreach([
                ['icon' => 'university', 'label' => 'Fakultas', 'desc' => 'Berdasarkan Unit Akademik', 'color' => 'primary'],
                ['icon' => 'calendar-check', 'label' => 'Tahun', 'desc' => 'Berdasarkan Waktu Terbit', 'color' => 'success'],
                ['icon' => 'tags', 'label' => 'Tipe', 'desc' => 'Berdasarkan Jenjang Studi', 'color' => 'warning']
            ] as $cat)
            <div class="col-md-4">
                <a href="/browse?by={{ strtolower($cat['label']) }}" class="text-decoration-none">
                    <div class="p-5 rounded-5 bg-light border-0 transition-all hover-shadow">
                        <i class="fas fa-{{ $cat['icon'] }} fa-4x text-{{ $cat['color'] }} mb-4 opacity-75"></i>
                        <h4 class="fw-800 text-dark">{{ $cat['label'] }}</h4>
                        <p class="text-secondary small mb-0">{{ $cat['desc'] }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
