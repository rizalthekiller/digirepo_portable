<!-- Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
        <i class="fas fa-search-plus"></i>
    </div>
    <h5 class="fw-bold mb-0 text-dark">Filter Pencarian</h5>
</div>

<form action="{{ url('/browse') }}" method="GET">
    <!-- Section: Kata Kunci -->
    <div class="mb-4">
        <label class="form-label small fw-bold text-secondary mb-2">Kata Kunci</label>
        <input type="text" name="q" class="form-control py-3 px-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe;" placeholder="Cari judul atau abstrak..." value="{{ request('q') }}">
    </div>

    <hr class="my-4 opacity-5">

    <!-- Section: Akademik -->
    <div class="mb-4">
        <label class="form-label small fw-bold text-secondary mb-2">Kategori Akademik</label>
        <div class="d-grid gap-3">
            <select name="faculty" class="form-select py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe; font-size: 0.9rem;">
                <option value="">Semua Fakultas</option>
                @foreach($faculties as $f)
                    <option value="{{ $f->id }}" {{ request('faculty') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>

            <select name="department" class="form-select py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe; font-size: 0.9rem;">
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
    </div>

    <hr class="my-4 opacity-5">

    <!-- Section: Tahun & Urutan -->
    <div class="mb-4">
        <label class="form-label small fw-bold text-secondary mb-2">Tahun & Urutan</label>
        <div class="row g-2">
            <div class="col-6">
                <select name="year" class="form-select py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe; font-size: 0.9rem;">
                    <option value="">Tahun</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <select name="sort" class="form-select py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe; font-size: 0.9rem;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Section: Penulis -->
    <div class="mb-4">
        <label class="form-label small fw-bold text-secondary mb-2">Penulis & Pembimbing</label>
        <div class="d-grid gap-2">
            <input type="text" name="author" class="form-control py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe;" placeholder="Nama Penulis..." value="{{ request('author') }}">
            <input type="text" name="supervisor" class="form-control py-3 border-light-subtle" style="border-radius: 12px; background: #fcfdfe;" placeholder="Nama Pembimbing..." value="{{ request('supervisor') }}">
        </div>
    </div>

    <hr class="my-4 opacity-5">

    <!-- Section: Tipe -->
    <div class="mb-5">
        <label class="form-label small fw-bold text-secondary mb-3">Tipe Dokumen</label>
        <div class="d-flex flex-wrap gap-2">
            @foreach($types as $t)
            <div class="form-check p-0 m-0">
                <input class="btn-check" type="radio" name="type" value="{{ $t->name }}" id="type{{ $t->id }}" {{ request('type') == $t->name ? 'checked' : '' }}>
                <label class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;" for="type{{ $t->id }}">
                    {{ $t->name }}
                </label>
            </div>
            @endforeach
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.8rem; letter-spacing: 0.05em;">
            <i class="fas fa-search me-2"></i> TEMUKAN DOKUMEN
        </button>
        @if(request()->hasAny(['q', 'faculty', 'department', 'year', 'type', 'author', 'supervisor', 'sort']))
            <a href="{{ url('/browse') }}" class="btn btn-link text-muted small text-decoration-none fw-bold mt-2">
                <i class="fas fa-sync me-1"></i> Bersihkan Semua Filter
            </a>
        @endif
    </div>
</form>
