@extends('layouts.admin')

@section('page_title', 'Manajemen Tipe Skripsi')

@section('styles')
<style>
    .type-badge { background: #f1f5f9; color: var(--zenith-primary); padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Kategori Tipe Skripsi</h6>
            <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                <i class="fas fa-plus me-1"></i> Tambah Tipe
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">NO.</th>
                        <th>NAMA KATEGORI</th>
                        <th>SLUG</th>
                        <th>DESKRIPSI</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                    <tr>
                        <td class="ps-4 text-muted">{{ $types->firstItem() + $loop->index }}.</td>
                        <td><span class="badge bg-light text-primary border">{{ $type->name }}</span></td>
                        <td><code class="small">{{ $type->slug }}</code></td>
                        <td><small class="text-muted">{{ $type->description ?: '-' }}</small></td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-link text-primary" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('admin.master.types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus tipe?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">Belum ada data tipe skripsi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($types->hasPages())
    <div class="card-footer bg-white">
        {{ $types->links() }}
    </div>
    @endif
</div>

<!-- Edit Modals -->
@foreach($types as $type)
<div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Tipe Skripsi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.types.update', $type->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Tipe</label>
                        <input type="text" name="name" class="form-control" value="{{ $type->name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3">{{ $type->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Tipe Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.types.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Tipe</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Skripsi, Thesis, Disertasi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
