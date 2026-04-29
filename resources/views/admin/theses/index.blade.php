@extends('layouts.admin')

@section('page_title', 'Repositori')

@section('styles')
<style>
    .badge-soft { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 6px; }
    .badge-soft-success { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .badge-soft-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
    .badge-soft-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    .badge-soft-primary { background: rgba(79, 70, 229, 0.1); color: #4f46e5; }
    
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    
    /* Fix for dropdown being clipped by table-responsive */
    .table-responsive {
        overflow: visible !important;
        padding-bottom: 80px;
        margin-bottom: -80px;
    }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-4 border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Repositori Karya Ilmiah</h5>
                <p class="text-muted small mb-0">Mengelola total <span class="text-primary fw-bold">{{ $theses->total() }} dokumen</span> dalam sistem.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-1"></i> Import
                </button>
                <a href="{{ route('admin.theses.export') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addManualModal">
                    <i class="fas fa-plus me-1"></i> Tambah Manual
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body bg-light bg-opacity-50 border-top border-bottom py-3">
        <form action="{{ route('admin.theses.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-2">
                <select name="status" class="form-select rounded-pill border-0 shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="file_status" class="form-select rounded-pill border-0 shadow-sm">
                    <option value="">Status Berkas</option>
                    <option value="exists" {{ request('file_status') == 'exists' ? 'selected' : '' }}>Ada File</option>
                    <option value="missing" {{ request('file_status') == 'missing' ? 'selected' : '' }}>Tanpa File</option>
                </select>
            </div>
            <div class="col-md-5">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 ps-2" placeholder="Cari judul, NIM, atau penulis..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 fw-bold shadow-sm">FILTER</button>
                @if(request()->hasAny(['search', 'status', 'file_status']))
                    <a href="{{ route('admin.theses.index') }}" class="btn btn-white bg-white rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-redo-alt text-muted small"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 50px;">NO.</th>
                    <th style="width: 45%;">DETAIL DOKUMEN</th>
                    <th>PENULIS</th>
                    <th class="text-center">HITS</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($theses as $thesis)
                <tr>
                    <td class="ps-4 text-muted small">{{ $theses->firstItem() + $loop->index }}.</td>
                    <td>
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.4;">{{ $thesis->title }}</div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge-soft badge-soft-primary">{{ $thesis->type }}</span>
                            <span class="badge-soft badge-soft-secondary text-dark">{{ $thesis->year }}</span>
                            @if($thesis->file_path)
                                <span class="badge-soft badge-soft-success"><i class="fas fa-file-pdf"></i> FILE OK</span>
                            @else
                                <span class="badge-soft badge-soft-danger"><i class="fas fa-exclamation-triangle"></i> NO FILE</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark small">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-muted extra-small">NIM: {{ $thesis->user->nim ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        <div class="fw-bold text-primary">{{ number_format($thesis->downloads_count) }}</div>
                    </td>
                    <td class="text-center">
                        @php
                            $statusClass = $thesis->status === 'approved' ? 'success' : ($thesis->status === 'rejected' ? 'danger' : 'warning');
                            $statusText = $thesis->status === 'approved' ? 'APPROVED' : ($thesis->status === 'rejected' ? 'REJECTED' : 'PENDING');
                        @endphp
                        <span class="badge-soft badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                <i class="fas fa-ellipsis-v small"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detailModal{{ $thesis->id }}">
                                        <i class="fas fa-eye text-primary"></i> Detail & Edit
                                    </a>
                                </li>
                                @if($thesis->status != 'pending')
                                <li>
                                    <form action="{{ route('admin.theses.reset', $thesis->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item" onclick="return confirm('Kembalikan ke antrean?')">
                                            <i class="fas fa-undo text-warning"></i> Reset Status
                                        </button>
                                    </form>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <form action="{{ route('admin.theses.destroy', $thesis->id) }}" method="POST" onsubmit="return confirm('Hapus permanen dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-folder-open text-muted mb-3 d-block fs-1 opacity-25"></i>
                        <p class="text-muted">Tidak ada data ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($theses->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $theses->links() }}
    </div>
    @endif
</div>

@foreach($theses as $thesis)
<!-- Modal Detail & Edit -->
<div class="modal fade" id="detailModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Informasi & Manajemen Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card bg-light border-0 rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-paperclip me-2 text-primary"></i>Berkas Utama</h6>
                                
                                @if($thesis->files && $thesis->files->count() > 0)
                                    <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach($thesis->files as $file)
                                        <div class="p-2 bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-between border">
                                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                                <i class="fas fa-file-pdf text-danger fs-5"></i>
                                                <div class="text-truncate">
                                                    <div class="fw-bold small text-dark text-truncate">{{ $file->label }}</div>
                                                    <div class="extra-small text-muted">PDF File</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('theses.file.stream', $file->uuid ?? $file->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 text-nowrap">Lihat</a>
                                        </div>
                                    @endforeach
                                    </div>
                                @elseif($thesis->file_path)
                                    <div class="p-3 bg-white rounded-3 shadow-sm mb-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fas fa-file-pdf text-danger fs-4"></i>
                                            <div>
                                                <div class="fw-bold small text-dark">Dokumen Utama</div>
                                                <div class="extra-small text-muted">PDF File</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('theses.read', $thesis->id) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3">Buka</a>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted border border-dashed rounded-3 mb-3">
                                        <i class="fas fa-file-excel fs-2 mb-2 opacity-25"></i>
                                        <div class="small">Belum ada file.</div>
                                    </div>
                                @endif

                                <div class="mt-auto pt-3">
                                    <form action="{{ route('admin.theses.upload_file', $thesis->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn btn-white border bg-white shadow-sm rounded-pill w-100 fw-bold small py-2 cursor-pointer">
                                            <i class="fas fa-cloud-upload-alt me-1 text-primary"></i> GANTI / UNGGAH FILE
                                            <input type="file" name="pdf_file" class="d-none" onchange="this.form.submit()" accept=".pdf">
                                        </label>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <ul class="nav nav-pills nav-pills-custom mb-4" id="pills-tab{{ $thesis->id }}" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-info{{ $thesis->id }}" type="button">Informasi</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-preview{{ $thesis->id }}" type="button">Pratinjau Dokumen</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-edit{{ $thesis->id }}" type="button">Edit Data</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pills-info{{ $thesis->id }}">
                                <h6 class="fw-bold mb-3 text-dark">{{ $thesis->title }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <label class="extra-small fw-bold text-muted text-uppercase d-block mb-1">Penulis</label>
                                            <div class="fw-bold text-dark">{{ $thesis->user->name ?? 'User Terhapus' }} ({{ $thesis->user->nim ?? '-' }})</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <label class="extra-small fw-bold text-muted text-uppercase d-block mb-1">Tahun & Tipe</label>
                                            <div class="fw-bold text-dark">{{ $thesis->year }} — {{ $thesis->type }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="p-3 bg-light rounded-3">
                                            <label class="extra-small fw-bold text-muted text-uppercase d-block mb-1">Dosen Pembimbing</label>
                                            <div class="fw-bold text-dark">{{ $thesis->supervisor_name ?: '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="extra-small fw-bold text-muted text-uppercase d-block mb-1">Abstrak</label>
                                        <div class="p-3 bg-white border rounded-3 small text-secondary" style="max-height: 200px; overflow-y: auto; line-height: 1.6;">
                                            {{ $thesis->abstract ?: 'Tidak ada abstrak.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="pills-preview{{ $thesis->id }}">
                                <div class="p-2 bg-light rounded-4 h-100 border" style="min-height: 500px;">
                                    @if($thesis->files && $thesis->files->count() > 0)
                                        <div class="mb-3 d-flex gap-2 overflow-auto pb-2">
                                            @foreach($thesis->files as $file)
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill text-nowrap" onclick="document.getElementById('iframe-index-{{ $thesis->id }}').src='{{ route('theses.file.stream', $file->uuid ?? $file->id) }}'">
                                                    <i class="fas fa-file-pdf me-1"></i> {{ $file->label }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <iframe id="iframe-index-{{ $thesis->id }}" src="{{ route('theses.file.stream', $thesis->files->first()->uuid ?? $thesis->files->first()->id) }}" width="100%" height="500px" style="border: none; border-radius: 12px; background: white;"></iframe>
                                    @elseif($thesis->file_path)
                                        <iframe src="{{ route('theses.stream', $thesis->id) }}" width="100%" height="550px" style="border: none; border-radius: 12px; background: white;"></iframe>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-5">
                                            <i class="fas fa-file-excel fa-4x text-muted mb-3 opacity-25"></i>
                                            <h6 class="fw-bold text-muted">File Tidak Ditemukan</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="pills-edit{{ $thesis->id }}">
                                <form action="{{ route('admin.theses.update', $thesis->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Judul Lengkap</label>
                                            <textarea name="title" class="form-control rounded-3" rows="2" required>{{ $thesis->title }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Tipe</label>
                                            <select name="type" class="form-select rounded-3">
                                                <option value="Skripsi" {{ $thesis->type == 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                                                <option value="Thesis" {{ $thesis->type == 'Thesis' ? 'selected' : '' }}>Thesis</option>
                                                <option value="Disertasi" {{ $thesis->type == 'Disertasi' ? 'selected' : '' }}>Disertasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Tahun Lulus</label>
                                            <input type="number" name="year" class="form-control rounded-3" value="{{ $thesis->year }}" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">Dosen Pembimbing</label>
                                            <input type="text" name="supervisor_name" class="form-control rounded-3" value="{{ $thesis->supervisor_name }}" required>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-white">Simpan Perubahan</button>
                                        </div>
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Import Data Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.theses.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-file-excel text-success display-4 mb-3"></i>
                    <p class="text-muted small">Pilih file Excel (.xlsx atau .xls) untuk mengunggah data skripsi secara massal.</p>
                    <input type="file" name="file" class="form-control mb-3" accept=".xlsx,.xls" required>
                    <div class="alert alert-info small text-start border-0 rounded-3">
                        <i class="fas fa-info-circle me-1"></i> <b>Urutan Kolom:</b><br>
                        NIM, Nama, Judul, Tipe, Tahun, Prodi, Kode Prodi, Pembimbing, Status, Abstrak, Kata Kunci.
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">Proses Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Manual -->
<div class="modal fade" id="addManualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Tambah Skripsi Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.theses.store_manual') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM Mahasiswa</label>
                            <input type="text" name="nim" class="form-control rounded-3" placeholder="Masukkan NIM" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="student_name" class="form-control rounded-3" placeholder="Masukkan nama mahasiswa" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Judul Lengkap</label>
                            <textarea name="title" class="form-control rounded-3" rows="2" placeholder="Masukkan judul skripsi" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe</label>
                            <select name="type" class="form-select rounded-3">
                                <option value="Skripsi">Skripsi</option>
                                <option value="Thesis">Thesis</option>
                                <option value="Disertasi">Disertasi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tahun</label>
                            <input type="number" name="year" class="form-control rounded-3" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="department_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach(\App\Models\Department::with('faculty')->get() as $dept)
                                    <option value="{{ $dept->id }}">[{{ $dept->level }}] {{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Dosen Pembimbing</label>
                            <input type="text" name="supervisor_name" class="form-control rounded-3" placeholder="Nama pembimbing" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Berkas PDF (Max 20MB)</label>
                            <div class="p-3 border border-dashed rounded-3 text-center bg-light">
                                <input type="file" name="pdf_file" class="form-control border-0 bg-transparent" accept=".pdf" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Repositori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
