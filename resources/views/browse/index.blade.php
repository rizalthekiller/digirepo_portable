@extends('layouts.app')

@section('title', 'Jelajahi Repositori')

@section('styles')
<style>
    :root {
        --primary: #1e3a8a;        /* Navy Blue */
        --primary-light: #3b82f6;  /* Blue Accent */
        --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .btn-primary { background: var(--primary) !important; border-color: var(--primary) !important; }
    .btn-primary:hover { background: #172554 !important; border-color: #172554 !important; }
    .btn-outline-primary { border-color: var(--primary); color: var(--primary); }
    .btn-outline-primary:hover, .btn-check:checked + .btn-outline-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; color: white !important; }

    .filter-card { 
        position: sticky; 
        top: 120px; 
        max-height: calc(100vh - 160px); 
        overflow-y: auto; 
        padding-right: 10px;
    }
    .filter-card::-webkit-scrollbar { width: 5px; }
    .filter-card::-webkit-scrollbar-track { background: transparent; }
    .filter-card::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .filter-card::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    
    .search-result-card { border: none; border-radius: 30px; background: white; box-shadow: var(--card-shadow); transition: var(--transition); padding: 35px; margin-bottom: 25px; }
    .pagination .page-link { border: none; border-radius: 12px; margin: 0 4px; font-weight: 700; color: var(--secondary); padding: 12px 18px; }
    .pagination .page-item.active .page-link { background: var(--primary-gradient); color: white; box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2); }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.6; }
    
    .custom-filter-radio .form-check-label { color: #64748b; border: 1px solid #e2e8f0 !important; background: #fff; }
    .custom-filter-radio .form-check-input:checked + .form-check-label { 
        background: rgba(79, 70, 229, 0.05); 
        color: var(--primary); 
        border-color: var(--primary) !important; 
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08); 
    }
    .custom-filter-radio .form-check-input:checked + .form-check-label .check-icon { opacity: 1 !important; }
    .custom-filter-radio .form-check-input:checked + .form-check-label i:first-child { opacity: 1 !important; color: var(--primary); }
    
    .form-control-premium:focus, .form-select-premium:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.05); }
    
    /* Accordion Styling */
    .custom-accordion .accordion-item { background: transparent; border-color: #f1f5f9 !important; }
    .custom-accordion .accordion-button { 
        background: transparent; 
        box-shadow: none !important; 
        font-size: 0.75rem; 
        letter-spacing: 0.08em;
        color: #1e293b !important;
        transition: all 0.2s ease;
    }
    .custom-accordion .accordion-button:hover { background: rgba(30, 58, 138, 0.03); color: var(--primary) !important; }
    .custom-accordion .accordion-button:not(.collapsed) { 
        color: var(--primary) !important; 
        background: rgba(30, 58, 138, 0.04); 
    }
    .custom-accordion .accordion-button::after { 
        background-size: 10px; 
        transition: transform 0.3s ease; 
    }
    
    .form-select-premium {
        display: block;
        width: 100%;
        padding: 0.75rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.5;
        color: #334155;
        background-color: #f8fafc;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 12px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        transition: all 0.2s ease-in-out;
        appearance: none;
    }

    @media (max-width: 991px) {
        .search-result-card { padding: 25px; border-radius: 20px; }
        .search-result-card h4 { font-size: 1.15rem; }
        .filter-sidebar-container { position: static; }
    }
</style>
@endsection

@section('content')
<div class="container py-5 mt-4">
    <div class="row g-5">
        <!-- Sidebar Filter (Desktop) -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="filter-sidebar-container sticky-top" style="top: 100px;">
                <div class="bg-white shadow-sm border-0 p-4" style="border-radius: 24px;">
                    @include('browse.partials.filter_form')
                </div>
            </div>
        </div>

        <!-- Sidebar Filter (Mobile Offcanvas) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" style="border-radius: 0 30px 30px 0; width: 320px;">
            <div class="offcanvas-header border-bottom p-4">
                <h5 class="offcanvas-title fw-800" id="filterOffcanvasLabel"><i class="fas fa-filter me-2 text-primary"></i> Filter</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                @include('browse.partials.filter_form')
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Mobile Filter Toggle -->
            <div class="d-lg-none mb-4 animate-fade-in">
                <button class="btn btn-white w-100 rounded-4 py-3 fw-bold shadow-sm border-0 d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" style="background: white;">
                    <i class="fas fa-filter text-primary"></i> 
                    <span>Filter & Pencarian Lanjut</span>
                </button>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 animate-fade-in gap-3">
                <div>
                    <h2 class="fw-800 mb-1">Hasil Penelusuran</h2>
                    <p class="text-secondary small mb-0">Ditemukan <span class="text-primary fw-bold">{{ $theses->total() }} dokumen</span> yang sesuai kriteria.</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted extra-small fw-800 text-uppercase opacity-50">Tampilan Terpilih</span>
                    <div class="bg-white p-1 rounded-pill shadow-sm border d-flex">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">List View</button>
                    </div>
                </div>
            </div>

            @forelse($theses as $thesis)
            <div class="search-result-card hover-lift animate-fade-in">
                <div class="row g-4">
                    <div class="col-md-9">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-800 text-uppercase" style="font-size: 0.65rem;">{{ $thesis->type }}</span>
                            <span class="text-muted small fw-600"><i class="fas fa-calendar-alt me-1"></i> {{ $thesis->year }}</span>
                        </div>
                        <h4 class="fw-800 mb-2">
                            <a href="{{ url('/theses/' . $thesis->id) }}" class="text-dark text-decoration-none hover-text-primary transition-all">{{ $thesis->title }}</a>
                        </h4>
                        <div class="d-flex flex-wrap gap-4 text-secondary small mb-3 fw-600">
                            <span><i class="fas fa-user-circle me-2 text-primary-light"></i> {{ $thesis->user->name ?? 'User Terhapus' }}</span>
                            @if($thesis->user && !$thesis->user->isDosen())
                                <span><i class="fas fa-university me-2 text-primary-light"></i> {{ $thesis->user->department->name ?? '-' }}</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0 text-truncate-2 opacity-75">
                            {{ Str::limit($thesis->abstract, 200) }}
                        </p>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex flex-column h-100 justify-content-center gap-2">
                            <a href="{{ route('theses.show', $thesis->id) }}" class="btn btn-primary rounded-pill fw-bold py-2 w-100">
                                <i class="fas fa-info-circle me-2"></i> Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-card text-center py-5 animate-fade-in">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-search fa-3x text-muted opacity-25"></i>
                </div>
                <h4 class="fw-800 text-dark">Tidak ada hasil ditemukan.</h4>
                <p class="text-secondary mx-auto" style="max-width: 400px;">Kami tidak dapat menemukan dokumen yang sesuai dengan kriteria filter Anda. Coba ubah kata kunci atau reset filter.</p>
                <a href="{{ url('/browse') }}" class="btn btn-primary mt-3">Reset Pencarian</a>
            </div>
            @endforelse

            <div class="mt-5 d-flex justify-content-center animate-fade-in">
                {{ $theses->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
