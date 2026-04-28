@extends('layouts.admin')

@section('page_title', 'Faculty Management')

@section('styles')
<style>
    .count-badge { background: #f1f5f9; color: var(--primary-color); padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Fakultas</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master.faculties.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Fakultas</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Teknik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Singkatan / Level</label>
                        <input type="text" name="level" class="form-control rounded-3" placeholder="Contoh: FT" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Fakultas (Opsional)</label>
                        <input type="text" name="code" class="form-control rounded-3" placeholder="Contoh: 01">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">SIMPAN FAKULTAS</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-university me-2 text-primary"></i>Daftar Fakultas</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 small fw-bold text-muted">NO.</th>
                                <th class="py-3 border-0 small fw-bold text-muted">FAKULTAS</th>
                                <th class="py-3 border-0 small fw-bold text-muted">SINGKATAN</th>
                                <th class="py-3 border-0 small fw-bold text-muted text-center">PRODI</th>
                                <th class="py-3 border-0 small fw-bold text-muted text-end pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $faculty)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $faculties->firstItem() + $loop->index }}.</td>
                                <td class="fw-bold text-dark">{{ $faculty->name }}</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border-primary border-opacity-25 px-2">{{ $faculty->level ?: '-' }}</span></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill px-3 small">{{ $faculty->departments_count }} Prodi</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editFaculty{{ $faculty->id }}"><i class="fas fa-edit me-2 text-warning"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.master.faculties.destroy', $faculty->id) }}" method="POST" onsubmit="return confirm('Hapus fakultas ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data fakultas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($faculties->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $faculties->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@foreach($faculties as $faculty)
<div class="modal fade" id="editFaculty{{ $faculty->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h6 class="modal-title fw-bold">Update Data Fakultas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.faculties.update', $faculty->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Fakultas</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ $faculty->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Singkatan / Level</label>
                        <input type="text" name="level" class="form-control rounded-3" value="{{ $faculty->level }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Fakultas</label>
                        <input type="text" name="code" class="form-control rounded-3" value="{{ $faculty->code ?? '' }}">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
