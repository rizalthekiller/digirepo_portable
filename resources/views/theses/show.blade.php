@extends('layouts.app')

@section('title', $thesis->title)

@section('styles')
<style>
    .detail-hero {
        background: var(--primary-gradient);
        padding: 120px 0 160px;
        color: white;
        margin-bottom: -100px;
        border-radius: 0 0 100px 100px;
    }
    .detail-card {
        background: white;
        border-radius: 40px;
        padding: 60px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.01);
    }
    .meta-item {
        padding: 20px;
        background: #f8fafc;
        border-radius: 20px;
        height: 100%;
        transition: var(--transition);
    }
    .meta-item:hover {
        background: #f1f5f9;
        transform: translateY(-5px);
    }
    .label-meta {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 8px;
        display: block;
    }
    .value-meta {
        font-weight: 700;
        color: #1e293b;
        font-family: 'Outfit', sans-serif;
    }
    .abstract-text {
        line-height: 1.8;
        color: #475569;
        font-size: 1.05rem;
    }
</style>
@endsection

@section('content')
<div class="detail-hero">
    <div class="container text-center">
        <div class="badge bg-white text-primary rounded-pill px-4 py-2 fw-800 text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">
            {{ $thesis->type }} • {{ $thesis->year }}
        </div>
        <h1 class="display-4 fw-800 mx-auto" style="max-width: 900px; line-height: 1.2;">{{ $thesis->title }}</h1>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-card animate-fade-in">
                <div class="row g-5">
                    <div class="col-lg-8">
                        <div class="mb-5">
                            <h4 class="fw-800 mb-4 d-flex align-items-center">
                                <i class="fas fa-quote-left text-primary-light me-3 opacity-25"></i> Abstrak
                            </h4>
                            <div class="abstract-text">
                                {{ $thesis->abstract }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <h5 class="fw-800 mb-3">Kata Kunci</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $thesis->keywords) as $keyword)
                                    <span class="badge bg-light text-secondary rounded-pill px-3 py-2 fw-600 border">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>

                        @php
                            // Cek apakah file benar-benar ada di disk (storage)
                            $fileExists = false;
                            if ($thesis->file_path) {
                                $cleanPath = $thesis->file_path;
                                $prefixes = ['/storage/', 'storage/', '/public/', 'public/'];
                                foreach ($prefixes as $prefix) {
                                    if (str_starts_with($cleanPath, $prefix)) {
                                        $cleanPath = substr($cleanPath, strlen($prefix));
                                    }
                                }
                                $cleanPath = ltrim($cleanPath, '/');
                                $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
                            }
                        @endphp

                        <!-- Desain Kartu Dokumen (Versi Kompak) -->
                        <div class="mt-5 p-4 rounded-4 border bg-white shadow-sm text-center animate-fade-in mx-auto" style="max-width: 500px;">
                            <div class="mb-3">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 60px; height: 60px;">
                                    <i class="fas fa-file-pdf fs-3"></i>
                                </div>
                            </div>
                            
                            <h5 class="fw-800 mb-2 text-dark">File Dokumen</h5>
                            <p class="text-muted mb-4 small">
                                Klik tombol di bawah untuk membuka file asli di tab baru.
                            </p>

                            @if($thesis->file_path && $fileExists)
                                @auth
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        <a href="{{ route('theses.read', $thesis->id) }}" data-turbo="false" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm small">
                                            <i class="fas fa-book-open me-1"></i> BACA
                                        </a>
                                        
                                        <a href="{{ route('theses.download', $thesis->id) }}" data-turbo="false" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold small">
                                            <i class="fas fa-download me-1"></i> UNDUH PDF
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold shadow-sm small">
                                        <i class="fas fa-lock me-1"></i> LOGIN UNTUK MEMBUKA PDF
                                    </a>
                                @endauth
                            @else
                                <div class="d-flex flex-column align-items-center">
                                    <div class="p-2 bg-light rounded-pill d-inline-block px-4 border text-muted fw-bold small" style="cursor: not-allowed; opacity: 0.6;">
                                        <i class="fas fa-file-excel me-1"></i> PDF TIDAK TERSEDIA
                                    </div>
                                    <p class="small text-danger mt-2 mb-0" style="font-size: 0.7rem;">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        @if(!$thesis->file_path)
                                            Belum diunggah.
                                        @else
                                            File fisik tidak ditemukan.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 120px;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Penulis</span>
                                        <div class="value-meta">{{ $thesis->user->name }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">NIM / Identitas</span>
                                        <div class="value-meta">{{ $thesis->user->nim ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Dosen Pembimbing</span>
                                        <div class="value-meta">{{ $thesis->supervisor_name ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Program Studi</span>
                                        <div class="value-meta">{{ $thesis->user->department->name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
