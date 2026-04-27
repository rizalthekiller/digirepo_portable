@extends('layouts.admin')

@section('page_title', 'Manajemen Tipe Skripsi')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 25px 20px; font-size: 0.85rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 25px 20px; font-size: 1rem; color: #334155; }
    .type-badge { background: rgba(79, 70, 229, 0.05); color: var(--zenith-primary); padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 0.85rem; border: 1px solid rgba(79, 70, 229, 0.1); }
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 mb-5 animate-fade-in d-flex align-items-center gap-3">
        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
            <i class="fas fa-check small"></i>
        </div>
        <div class="fw-bold">{{ session('success') }}</div>
    </div>
@endif

<div class="zenith-card p-0 overflow-hidden animate-fade-in">
    <div class="p-5 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-zenith mb-1">Kategori Tipe Skripsi</h4>
            <p class="text-secondary small mb-0">Kelola daftar tipe karya ilmiah yang tersedia dalam sistem.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg" style="background: var(--zenith-primary); border: none;" data-bs-toggle="modal" data-bs-target="#addTypeModal">
            <i class="fas fa-plus me-2"></i> Tambah Tipe
        </button>
    </div>

    <div class="table-responsive px-5 pb-5">
        <table class="table zenith-table align-middle">
            <thead>
                <tr>
                    <th style="width: 70px;">NO.</th>
                    <th>NAMA KATEGORI</th>
                    <th>SLUG</th>
                    <th>DESKRIPSI</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                <tr>
                    <td class="text-secondary fw-800">{{ $types->firstItem() + $loop->index }}.</td>
                    <td>
                        <span class="type-badge">{{ $type->name }}</span>
                    </td>
                    <td class="text-secondary small fw-bold">{{ $type->slug }}</td>
                    <td class="text-muted small">{{ $type->description ?: '-' }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-link text-primary p-2" style="font-size: 1.1rem;" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.master.types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus tipe ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-2" style="font-size: 1.1rem;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
    <div class="px-5 py-3 bg-light">
        {{ $types->links() }}
    </div>
</div>

<!-- Edit Modals -->
@foreach($types as $type)
<div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Edit Tipe Skripsi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form action="{{ route('admin.master.types.update', $type->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="info-label-premium">Nama Tipe</label>
                        <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $type->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control border-0 bg-light p-3 rounded-3" rows="3">{{ $type->description }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Tambah Tipe Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form action="{{ route('admin.master.types.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="info-label-premium">Nama Tipe</label>
                        <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Contoh: Skripsi, Thesis, Disertasi" required>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control border-0 bg-light p-3 rounded-3" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg">TAMBAH KATEGORI</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
