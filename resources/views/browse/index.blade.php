@extends('layouts.app')

@section('title', 'Jelajahi Repositori')

@section('styles')
<style>
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
    
    .custom-radio .form-check-label { color: #64748b; border-color: #e2e8f0 !important; }
    .custom-radio .form-check-input:checked + .form-check-label { background: var(--primary-gradient); color: white; border-color: transparent !important; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15); }
    .form-control-premium:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.05); }
</style>
@endsection

@section('content')
<div class="container py-5 mt-4">
    <div class="row g-5">
        <!-- Sidebar Filter -->
        <div class="col-lg-3">
            <div class="glass-card filter-card animate-fade-in">
                <h5 class="fw-800 mb-4 text-dark">Filter Koleksi</h5>
                <form action="{{ url('/browse') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Pencarian</label>
                        <input type="text" name="q" class="form-control-premium w-100" placeholder="Judul / Abstrak..." value="{{ request('q') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Fakultas</label>
                        <select name="faculty" class="form-control-premium w-100">
                            <option value="">Semua Fakultas</option>
                            @foreach($faculties as $f)
                                <option value="{{ $f->id }}" {{ request('faculty') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Program Studi</label>
                        <select name="department" class="form-control-premium w-100">
                            <option value="">Semua Program Studi</option>
                            @foreach($faculties as $f)
                                <optgroup label="{{ $f->name }}">
                                    @foreach($departments->where('faculty_id', $f->id) as $d)
                                        <option value="{{ $d->id }}" {{ request('department') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Tahun</label>
                        <select name="year" class="form-control-premium w-100">
                            <option value="">Semua Tahun</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Urutkan Berdasarkan</label>
                        <select name="sort" class="form-control-premium w-100">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul (A-Z)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Penulis / NIM</label>
                        <input type="text" name="author" class="form-control-premium w-100" placeholder="Nama atau NIM..." value="{{ request('author') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-800 text-uppercase opacity-50" style="letter-spacing: 0.1em;">Pembimbing</label>
                        <input type="text" name="supervisor" class="form-control-premium w-100" placeholder="Nama Pembimbing..." value="{{ request('supervisor') }}">
                    </div>

                    <div class="mb-5">
                        <label class="form-label small fw-800 text-uppercase opacity-50 mb-3" style="letter-spacing: 0.1em;">Tipe Dokumen</label>
                        <div class="d-grid gap-2">
                            @foreach($types as $t)
                            <div class="form-check custom-radio">
                                <input class="form-check-input d-none" type="radio" name="type" value="{{ $t->name }}" id="type{{ $t->id }}" {{ request('type') == $t->name ? 'checked' : '' }}>
                                <label class="form-check-label py-2 px-3 border rounded-3 w-100 text-center cursor-pointer transition-all small fw-bold" for="type{{ $t->id }}">
                                    {{ $t->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3 py-3 shadow-lg">
                        <i class="fas fa-search me-2"></i> CARI KOLEKSI
                    </button>
                    @if(request()->hasAny(['q', 'faculty', 'department', 'year', 'type', 'author', 'supervisor', 'sort']))
                        <a href="{{ url('/browse') }}" class="btn btn-link w-100 text-muted small text-decoration-none fw-bold">
                            <i class="fas fa-sync-alt me-1"></i> Reset Semua Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
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
                            <span><i class="fas fa-user-circle me-2 text-primary-light"></i> {{ $thesis->user->name }}</span>
                            <span><i class="fas fa-university me-2 text-primary-light"></i> {{ $thesis->user->department->name }}</span>
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
