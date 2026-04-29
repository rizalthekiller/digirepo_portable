@extends('layouts.app')

@section('title', $thesis->title)

@section('extra_meta')
    <!-- Dublin Core Metadata Standard -->
    <meta name="DC.title" content="{{ $thesis->title }}">
    <meta name="DC.creator" content="{{ $thesis->user->name ?? 'Anonim' }}">
    <meta name="DC.subject" content="{{ $thesis->keywords }}">
    <meta name="DC.description" content="{{ Str::limit($thesis->abstract, 300) }}">
    <meta name="DC.publisher" content="Universitas Islam Negeri Sultan Aji Muhammad Idris Samarinda">
    <meta name="DC.contributor" content="{{ $thesis->supervisor_name }}">
    <meta name="DC.date" content="{{ $thesis->year }}">
    <meta name="DC.type" content="{{ $thesis->type }}">
    <meta name="DC.format" content="application/pdf">
    <meta name="DC.identifier" content="{{ url()->current() }}">
    <meta name="DC.language" content="id">
    
    <!-- Highwire Press Tags (For Google Scholar) -->
    <meta name="citation_title" content="{{ $thesis->title }}">
    <meta name="citation_author" content="{{ $thesis->user->name ?? 'Anonim' }}">
    <meta name="citation_publication_date" content="{{ $thesis->year }}">
    <meta name="citation_pdf_url" content="{{ route('theses.download', $thesis->id) }}">
    <meta name="citation_abstract_html_url" content="{{ url()->current() }}">
    <meta name="citation_language" content="id">
    <meta name="citation_keywords" content="{{ $thesis->keywords }}">

    <!-- Open Graph (Social Media) -->
    <meta property="og:title" content="{{ $thesis->title }}">
    <meta property="og:description" content="{{ Str::limit($thesis->abstract, 200) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ \App\Models\Setting::get('site_name', 'DigiRepo') }}">
@endsection

@section('styles')
<style>
    .detail-hero {
        background: var(--primary-gradient);
        padding: 100px 0 140px;
        color: white;
        margin-bottom: -80px;
        border-radius: 0 0 60px 60px;
    }
    .detail-card {
        background: white;
        border-radius: 35px;
        padding: 40px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.01);
    }
    @media (max-width: 991px) {
        .detail-hero { padding: 80px 0 120px; border-radius: 0 0 40px 40px; margin-bottom: -60px; }
        .detail-card { padding: 25px; border-radius: 25px; }
        .detail-hero h1 { font-size: 1.75rem !important; }
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
                            <div class="abstract-text" style="font-size: 1rem; line-height: 1.7; text-align: justify;">
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

                        <!-- Desain Kartu Dokumen (Versi Multiple Files) -->
                        <div class="mt-5 p-4 p-md-5 rounded-4 border bg-white shadow-sm animate-fade-in mx-auto" style="max-width: 600px;">
                            <h5 class="fw-800 mb-4 text-dark text-center">Berkas Dokumen</h5>
                            
                            <div class="d-grid gap-3">
                                @forelse($thesis->files as $file)
                                    <div class="p-3 rounded-4 bg-light border d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 hover-lift">
                                        <div class="d-flex align-items-center gap-3 text-start w-100">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                                <i class="fas fa-file-pdf fs-4"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark small text-truncate">{{ $file->label }}</div>
                                                <div class="text-muted extra-small" style="font-size: 0.65rem;">Format: PDF • Terverifikasi</div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 w-100 w-sm-auto justify-content-center">
                                            @if(auth()->check() && !auth()->user()->isGuest())
                                                {{-- User Biasa (Dosen/Mahasiswa/Admin) bisa download & baca --}}
                                                @if($thesis->embargo_until && now()->lt($thesis->embargo_until) && auth()->id() !== $thesis->user_id && !auth()->user()->isAdmin())
                                                    <span class="badge bg-white text-warning border rounded-pill px-3 py-2 small shadow-sm w-100">
                                                        <i class="fas fa-lock me-1"></i> EMBARGO
                                                    </span>
                                                @else
                                                    <a href="{{ route('theses.read', $thesis->id) }}" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-bold w-100">
                                                        <i class="fas fa-eye me-1"></i> BACA
                                                    </a>
                                                    <a href="{{ route('theses.download.file', $file->uuid) }}" data-turbo="false" class="btn btn-danger rounded-pill btn-sm px-3 fw-bold shadow-sm w-100">
                                                        <i class="fas fa-download me-1"></i> UNDUH
                                                    </a>
                                                @endif
                                            @elseif(auth()->check() && auth()->user()->isGuest())
                                                {{-- User Tamu hanya bisa baca --}}
                                                <a href="{{ route('theses.read', $thesis->id) }}" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold w-100">
                                                    <i class="fas fa-eye me-1"></i> BACA ONLINE
                                                </a>
                                            @else
                                                {{-- Belum login --}}
                                                <a href="{{ route('login') }}" class="btn btn-warning rounded-pill btn-sm px-3 fw-bold w-100">
                                                    <i class="fas fa-lock me-1"></i> LOGIN
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 opacity-50">
                                        <i class="fas fa-folder-open fs-2 mb-2 d-block"></i>
                                        <div class="small fw-bold">Belum ada file terlampir</div>
                                    </div>
                                @endforelse
                            </div>
                            
                            @if(auth()->check() && auth()->user()->isGuest())
                                <div class="mt-4 alert alert-info border-0 rounded-4 small p-3 text-center mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Akun <b>Tamu</b> memiliki akses <b>Baca Online</b>. Fitur unduh dinonaktifkan untuk role ini.
                                </div>
                            @endif

                            @if($thesis->embargo_until && now()->lt($thesis->embargo_until))
                                <div class="mt-4 alert alert-warning border-0 rounded-4 small p-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Dokumen ini sedang dalam masa embargo hingga <b>{{ \Carbon\Carbon::parse($thesis->embargo_until)->format('d M Y') }}</b>.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 120px;">
                            <div class="row g-3">
                                <div class="col-12">
                                        <div class="value-meta">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">NIM / Identitas</span>
                                        <div class="value-meta">{{ $thesis->user->nim ?? '-' }}</div>
                                    </div>
                                </div>
                                @if($thesis->supervisor_name && in_array($thesis->type, ['Skripsi', 'Thesis', 'Disertasi']))
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Dosen Pembimbing</span>
                                        <div class="value-meta">{{ $thesis->supervisor_name }}</div>
                                    </div>
                                </div>
                                @endif

                                @if($thesis->journal_name)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Nama Jurnal / Prosiding</span>
                                        <div class="value-meta text-primary">{{ $thesis->journal_name }}</div>
                                    </div>
                                </div>
                                @if($thesis->volume || $thesis->issue || $thesis->pages)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Edisi Jurnal (Vol / No / Hal)</span>
                                        <div class="value-meta">
                                            Vol. {{ $thesis->volume ?: '-' }} / No. {{ $thesis->issue ?: '-' }} / Hlm. {{ $thesis->pages ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($thesis->doi)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">DOI</span>
                                        <div class="value-meta">
                                            <a href="https://doi.org/{{ $thesis->doi }}" target="_blank" class="text-decoration-none text-primary">
                                                <i class="fas fa-external-link-alt me-1 small"></i> {{ $thesis->doi }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($thesis->issn)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">ISSN</span>
                                        <div class="value-meta">{{ $thesis->issn }}</div>
                                    </div>
                                </div>
                                @endif
                                @endif

                                @if($thesis->isbn)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">ISBN</span>
                                        <div class="value-meta">{{ $thesis->isbn }}</div>
                                    </div>
                                </div>
                                @endif
                                @if($thesis->publisher)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Penerbit</span>
                                        <div class="value-meta">{{ $thesis->publisher }}</div>
                                    </div>
                                </div>
                                @endif
                                @if($thesis->edition)
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Edisi / Cetakan</span>
                                        <div class="value-meta">{{ $thesis->edition }}</div>
                                    </div>
                                </div>
                                @endif

                                @if($thesis->user && !$thesis->user->isDosen())
                                <div class="col-12">
                                    <div class="meta-item">
                                        <span class="label-meta">Program Studi</span>
                                        <div class="value-meta">{{ $thesis->user->department->name ?? '-' }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
