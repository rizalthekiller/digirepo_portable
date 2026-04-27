@extends('layouts.admin')

@section('page_title', 'Repository Management')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 25px 20px; font-size: 0.85rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 25px 20px; font-size: 1rem; color: #334155; }
    .zenith-table tbody tr:hover td { background: rgba(79, 70, 229, 0.02); }
    .text-title-zenith { font-size: 1.05rem; font-weight: 800; color: var(--zenith-sidebar); }
</style>
@endsection

@section('content')
<div class="zenith-card p-0 overflow-hidden animate-fade-in">
    <div class="p-5 d-flex justify-content-between align-items-center flex-wrap gap-4">
        <div>
            <h4 class="fw-zenith mb-1">Seluruh Karya Ilmiah</h4>
            <p class="text-secondary small mb-0">Manajemen pusat <span class="text-primary fw-bold">{{ $theses->total() }} dokumen</span> repositori.</p>
        </div>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="btn-group me-2">
                <button class="btn btn-outline-success rounded-start-pill px-3 fw-bold small" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-1"></i> Import
                </button>
                <a href="{{ route('admin.theses.export') }}" class="btn btn-outline-primary px-3 fw-bold small">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
                <button class="btn btn-primary rounded-end-pill px-3 fw-bold" style="background: var(--zenith-primary); border: none;" data-bs-toggle="modal" data-bs-target="#addManualModal">
                    <i class="fas fa-plus me-1"></i> Tambah
                </button>
            </div>

            <form action="{{ route('admin.theses.index') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select border-0 bg-light rounded-pill px-4 shadow-sm" style="width: 130px; font-size: 0.8rem;">
                    <option value="">Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="file_status" class="form-select border-0 bg-light rounded-pill px-4 shadow-sm" style="width: 140px; font-size: 0.8rem;">
                    <option value="">Status File</option>
                    <option value="exists" {{ request('file_status') == 'exists' ? 'selected' : '' }}>Ada File</option>
                    <option value="missing" {{ request('file_status') == 'missing' ? 'selected' : '' }}>Tanpa File</option>
                </select>
                <select name="type" class="form-select border-0 bg-light rounded-pill px-4 shadow-sm" style="width: 130px; font-size: 0.8rem;">
                    <option value="">Tipe</option>
                    @foreach(['Skripsi', 'Thesis', 'Disertasi'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" name="search" class="form-control border-0 bg-light ps-5 pe-4 py-3 rounded-pill shadow-sm" placeholder="Cari..." style="width: 200px; font-size: 0.85rem;" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: var(--zenith-primary); border: none;">
                    FILTER
                </button>
                @if(request('search') || request('status') || request('type') || request('file_status'))
                    <a href="{{ route('admin.theses.index') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-times text-danger"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-responsive px-5 pb-5">
        <table class="table zenith-table align-middle">
            <thead>
                <tr>
                    <th style="width: 70px;">NO.</th>
                    <th>DETAIL DOKUMEN</th>
                    <th>PENULIS</th>
                    <th>PRODI</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($theses as $thesis)
                <tr>
                    <td class="text-secondary fw-800">{{ $theses->firstItem() + $loop->index }}.</td>
                    <td>
                        <div class="text-title-zenith mb-1 line-clamp-1" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                        <div class="text-muted" style="font-size: 0.8rem;">
                            <span class="badge bg-light text-secondary me-2 p-2 px-3 rounded-pill">{{ $thesis->type }}</span>
                            <i class="far fa-calendar-alt me-1"></i> {{ $thesis->year }}
                            @if($thesis->file_path)
                                <span class="ms-2 badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-file-pdf me-1"></i> FILE OK</span>
                            @else
                                <span class="ms-2 badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-file-excel me-1"></i> NO FILE</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">NIM: {{ $thesis->user->nim ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="fw-700 text-dark">{{ $thesis->user?->department?->name ?? '-' }}</div>
                    </td>
                    <td>
                        @if($thesis->status == 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">PENDING</span>
                        @elseif($thesis->status == 'approved')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">APPROVED</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.7rem;">REJECTED</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group justify-content-center">
                            <button class="btn btn-link text-primary p-2" style="font-size: 1.2rem;" title="Pratinjau Cepat" data-bs-toggle="modal" data-bs-target="#detailModal{{ $thesis->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            @if(!$thesis->file_path)
                            <button class="btn btn-link text-success p-2" style="font-size: 1.2rem;" title="Upload File PDF" data-bs-toggle="modal" data-bs-target="#uploadFileModal{{ $thesis->id }}">
                                <i class="fas fa-file-upload"></i>
                            </button>
                            @endif
                            
                            @if($thesis->status != 'pending')
                            <form action="{{ route('admin.theses.reset', $thesis->id) }}" method="POST" onsubmit="return confirm('Kembalikan dokumen ini ke antrean verifikasi?')">
                                @csrf
                                <button type="submit" class="btn btn-link text-warning p-2" style="font-size: 1.2rem;" title="Kembalikan ke Antrean">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                            </form>
                            @endif

                             {{-- Reject Button (Batal Publikasi) --}}
                            <form action="{{ route('admin.theses.reject', $thesis->id) }}" method="POST" onsubmit="return confirm('Tarik dokumen ini dari publikasi?')">
                                @csrf
                                <input type="hidden" name="reason" value="Pencabutan oleh Admin">
                                <button type="submit" class="btn btn-link text-warning p-2" style="font-size: 1.2rem;" title="Tarik dari Publikasi">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>

                            {{-- Real Delete Button --}}
                            <form action="{{ route('admin.theses.destroy', $thesis->id) }}" method="POST" onsubmit="return confirm('HAPUS PERMANEN? Data dan file fisik akan hilang selamanya!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-2" style="font-size: 1.2rem;" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 bg-light bg-opacity-50">
        {{ $theses->links() }}
    </div>
</div>

<!-- Lightweight Detail Modals -->
@foreach($theses as $thesis)
<div class="modal fade" id="detailModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0 d-flex justify-content-between align-items-center">
                <ul class="nav nav-pills zenith-pills" id="pills-tab{{ $thesis->id }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#pills-view{{ $thesis->id }}" type="button">Informasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#pills-edit{{ $thesis->id }}" type="button"><i class="fas fa-edit me-2"></i>Edit Data</button>
                    </li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-5">
                    <div class="col-lg-5">
                        @php
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

                        <div class="bg-light rounded-4 p-5 w-100 h-100 text-center border d-flex flex-column justify-content-center align-items-center">
                            <i class="fas fa-file-pdf text-danger display-2 mb-4"></i>
                            <h5 class="fw-bold mb-2">File Dokumen</h5>
                            
                            @if($thesis->file_path && $fileExists)
                                <p class="text-secondary mb-4 small" style="max-width: 250px;">Dokumen tersedia. Klik untuk membuka atau gunakan tombol ganti di bawah.</p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('theses.stream', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-danger rounded-pill px-5 py-3 fw-bold shadow-sm">
                                        <i class="fas fa-external-link-alt me-2"></i> BUKA PDF
                                    </a>
                                    
                                    <form action="{{ route('admin.theses.upload_file', $thesis->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 mt-2 cursor-pointer border-dashed">
                                            <i class="fas fa-sync-alt me-1"></i> Ganti File
                                            <input type="file" name="pdf_file" class="d-none" onchange="this.form.submit()" accept=".pdf">
                                        </label>
                                    </form>
                                </div>
                            @else
                                <p class="text-danger mb-4 small fw-bold" style="max-width: 250px;">
                                    <i class="fas fa-exclamation-triangle me-1"></i> File fisik tidak ditemukan.
                                </p>
                                <form action="{{ route('admin.theses.upload_file', $thesis->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg cursor-pointer" style="background: var(--zenith-primary); border: none;">
                                        <i class="fas fa-upload me-2"></i> UNGGAH PDF
                                        <input type="file" name="pdf_file" class="d-none" onchange="this.form.submit()" accept=".pdf">
                                    </label>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="tab-content" id="pills-tabContent{{ $thesis->id }}">
                            <!-- Tab 1: Overview -->
                            <div class="tab-pane fade show active" id="pills-view{{ $thesis->id }}">
                                <div class="zenith-card h-100 shadow-none border bg-light bg-opacity-30 p-4">
                                    <h5 class="fw-zenith mb-4 text-primary">Metadata Skripsi</h5>
                                    
                                    <div class="mb-4">
                                        <label class="info-label-premium">Judul Lengkap</label>
                                        <div class="fw-800 text-dark" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem;">{{ $thesis->title }}</div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-6">
                                            <label class="info-label-premium">Penulis</label>
                                            <div class="fw-bold">{{ $thesis->user->name }}</div>
                                            <div class="text-muted small">NIM: {{ $thesis->user->nim ?: '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <label class="info-label-premium">Tahun / Tipe</label>
                                            <div class="fw-bold">{{ $thesis->year }}</div>
                                            <span class="badge bg-white text-primary border rounded-pill">{{ $thesis->type }}</span>
                                        </div>
                                        <div class="col-12">
                                            <label class="info-label-premium">Dosen Pembimbing</label>
                                            <div class="fw-bold small">{{ $thesis->supervisor_name ?: 'Belum diatur' }}</div>
                                        </div>
                                    </div>

                                    <label class="info-label-premium">Abstrak</label>
                                    <div class="text-secondary small mb-4" style="line-height: 1.8; max-height: 250px; overflow-y: auto;">
                                        {{ $thesis->abstract }}
                                    </div>

                                    <div class="p-3 bg-white rounded-3 border border-dashed text-center">
                                        <div class="text-muted small mb-1">Status Verifikasi</div>
                                        <span class="badge {{ $thesis->status == 'approved' ? 'bg-success' : ($thesis->status == 'pending' ? 'bg-warning' : 'bg-danger') }} rounded-pill px-4">
                                            {{ strtoupper($thesis->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Edit Form -->
                            <div class="tab-pane fade" id="pills-edit{{ $thesis->id }}">
                                <form action="{{ route('admin.theses.update', $thesis->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="zenith-card h-100 shadow-none border p-4">
                                        <h5 class="fw-zenith mb-4 text-warning">Edit Informasi</h5>
                                        
                                        <div class="mb-3">
                                            <label class="info-label-premium">Judul Skripsi</label>
                                            <textarea name="title" class="form-control border-0 bg-light p-3 rounded-3 shadow-none" rows="2" required>{{ $thesis->title }}</textarea>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="info-label-premium">Tipe</label>
                                                <select name="type" class="form-control border-0 bg-light p-3 rounded-3 shadow-none">
                                                    <option value="Skripsi" {{ $thesis->type == 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                                                    <option value="Thesis" {{ $thesis->type == 'Thesis' ? 'selected' : '' }}>Thesis</option>
                                                    <option value="Disertasi" {{ $thesis->type == 'Disertasi' ? 'selected' : '' }}>Disertasi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="info-label-premium">Tahun</label>
                                                <input type="number" name="year" class="form-control border-0 bg-light p-3 rounded-3 shadow-none" value="{{ $thesis->year }}" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="info-label-premium">Dosen Pembimbing</label>
                                            <input type="text" name="supervisor_name" class="form-control border-0 bg-light p-3 rounded-3 shadow-none" value="{{ $thesis->supervisor_name }}" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="info-label-premium">Abstrak</label>
                                            <textarea name="abstract" class="form-control border-0 bg-light p-3 rounded-3 shadow-none" rows="6" required>{{ $thesis->abstract }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold text-white shadow-lg">
                                            SIMPAN PERUBAHAN
                                        </button>
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
@endforeach

<!-- Upload File Modals -->
@foreach($theses as $thesis)
<div class="modal fade" id="uploadFileModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Upload File PDF</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form action="{{ route('admin.theses.upload_file', $thesis->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="info-label-premium">Judul Skripsi</label>
                        <div class="fw-bold small text-muted mb-3">{{ $thesis->title }}</div>
                    </div>
                    
                    <div class="p-4 border border-dashed rounded-4 text-center mb-4">
                        <i class="fas fa-file-pdf text-danger display-4 mb-3"></i>
                        <p class="text-secondary small">Pilih file PDF baru untuk diunggah. File lama akan digantikan jika sudah ada.</p>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" style="background: var(--zenith-primary); border: none;">
                        UNGGAH SEKARANG
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Import Data Excel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form action="{{ route('admin.theses.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 border border-dashed rounded-4 text-center mb-4">
                        <i class="fas fa-file-excel text-success display-4 mb-3"></i>
                        <p class="text-secondary small">Pilih file Excel (.xlsx atau .xls) untuk mengunggah data skripsi secara massal.</p>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="alert alert-info small border-0 rounded-3">
                        <i class="fas fa-info-circle me-1"></i> <b>Format Kolom:</b><br>
                        A: NIM, B: Nama, C: Judul, D: Tipe, E: Tahun, F: Prodi, G: Kode Prodi, H: Pembimbing, I: Status, J: Abstrak, K: Kata Kunci.
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg">PROSES IMPORT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Manual Modal -->
<div class="modal fade" id="addManualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Tambah Skripsi Manual</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.theses.store_manual') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="info-label-premium">NIM Mahasiswa</label>
                            <input type="text" name="nim" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Masukkan NIM..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">Nama Lengkap</label>
                            <input type="text" name="student_name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Nama mahasiswa..." required>
                        </div>
                        <div class="col-12">
                            <label class="info-label-premium">Judul Karya Ilmiah</label>
                            <textarea name="title" class="form-control border-0 bg-light p-3 rounded-3" rows="2" placeholder="Judul lengkap..." required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label-premium">Tipe Dokumen</label>
                            <select name="type" class="form-control border-0 bg-light p-3 rounded-3">
                                <option value="Skripsi">Skripsi</option>
                                <option value="Thesis">Thesis</option>
                                <option value="Disertasi">Disertasi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label-premium">Tahun Lulus</label>
                            <input type="number" name="year" class="form-control border-0 bg-light p-3 rounded-3" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label-premium">Program Studi</label>
                            <select name="department_id" class="form-control border-0 bg-light p-3 rounded-3" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach(\App\Models\Department::with('faculty')->get() as $dept)
                                    <option value="{{ $dept->id }}">
                                        [{{ $dept->level }}] {{ $dept->name }} - {{ $dept->faculty->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="info-label-premium">Dosen Pembimbing</label>
                            <input type="text" name="supervisor_name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Nama dosen pembimbing..." required>
                        </div>
                        <div class="col-12">
                            <label class="info-label-premium">Abstrak (Opsional)</label>
                            <textarea name="abstract" class="form-control border-0 bg-light p-3 rounded-3" rows="3" placeholder="Tempel abstrak di sini..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="info-label-premium">Unggah File PDF (Max 20MB)</label>
                            <input type="file" name="pdf_file" class="form-control border-0 bg-light p-3 rounded-3" accept=".pdf" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg">SIMPAN DATA KE REPOSITORI</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
