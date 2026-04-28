@extends('layouts.admin')

@section('page_title', 'Department Management')

@section('styles')
<style>
    .level-badge { background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.65rem; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Program Studi</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master.departments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fakultas</label>
                        <select name="faculty_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Prodi</label>
                        <input type="text" name="code" class="form-control rounded-3" placeholder="Contoh: IF" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Program Studi</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Teknik Informatika" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jenjang (Level)</label>
                        <select name="level" class="form-select rounded-3">
                            <option value="S1">S1 - Sarjana</option>
                            <option value="S2">S2 - Magister</option>
                            <option value="S3">S3 - Doktor</option>
                            <option value="D3">D3 - Diploma</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">SIMPAN PRODI</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Daftar Program Studi</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 small fw-bold text-muted">NO.</th>
                                <th class="py-3 border-0 small fw-bold text-muted">KODE</th>
                                <th class="py-3 border-0 small fw-bold text-muted">NAMA PRODI</th>
                                <th class="py-3 border-0 small fw-bold text-muted">FAKULTAS</th>
                                <th class="py-3 border-0 small fw-bold text-muted text-center">JENJANG</th>
                                <th class="py-3 border-0 small fw-bold text-muted text-end pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $departments->firstItem() + $loop->index }}.</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border-primary border-opacity-25 px-2">{{ $dept->code ?: '-' }}</span></td>
                                <td class="fw-bold text-dark small">{{ $dept->name }}</td>
                                <td class="text-muted extra-small">{{ $dept->faculty->name ?? '-' }}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-3 small">{{ $dept->level ?: 'S1' }}</span></td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editDept{{ $dept->id }}"><i class="fas fa-edit me-2 text-warning"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.master.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Hapus prodi ini?')">
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
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data program studi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($departments->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $departments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@foreach($departments as $dept)
<div class="modal fade" id="editDept{{ $dept->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h6 class="modal-title fw-bold">Update Data Prodi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.departments.update', $dept->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fakultas</label>
                        <select name="faculty_id" class="form-select rounded-3" required>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ $dept->faculty_id == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Prodi</label>
                        <input type="text" name="code" class="form-control rounded-3" value="{{ $dept->code ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Program Studi</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ $dept->name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jenjang (Level)</label>
                        <select name="level" class="form-select rounded-3">
                            <option value="S1" {{ $dept->level == 'S1' ? 'selected' : '' }}>S1 - Sarjana</option>
                            <option value="S2" {{ $dept->level == 'S2' ? 'selected' : '' }}>S2 - Magister</option>
                            <option value="S3" {{ $dept->level == 'S3' ? 'selected' : '' }}>S3 - Doktor</option>
                            <option value="D3" {{ $dept->level == 'D3' ? 'selected' : '' }}>D3 - Diploma</option>
                        </select>
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
