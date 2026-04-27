@extends('layouts.admin')

@section('page_title', 'Faculty Management')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 20px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 20px; font-size: 0.9rem; color: #334155; }
    .count-badge { background: rgba(79, 70, 229, 0.05); color: var(--zenith-primary); padding: 5px 12px; border-radius: 10px; font-weight: 800; font-size: 0.75rem; }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="zenith-card">
            <h5 class="fw-zenith mb-4">Tambah Fakultas</h5>
            <form action="{{ route('admin.master.faculties.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="info-label-premium">Nama Fakultas</label>
                    <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Contoh: Teknik" required>
                </div>
                <div class="mb-4">
                    <label class="info-label-premium">Level / Singkatan</label>
                    <input type="text" name="level" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Contoh: FT">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">
                    SIMPAN FAKULTAS
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="zenith-card p-0 overflow-hidden h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-zenith mb-0 px-3">Daftar Fakultas</h5>
            </div>
            <div class="table-responsive p-4">
                <table class="table zenith-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">NO.</th>
                            <th>FAKULTAS</th>
                            <th>LEVEL</th>
                            <th class="text-center">PRODI</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faculties as $faculty)
                        <tr>
                            <td class="text-secondary fw-800">{{ $faculties->firstItem() + $loop->index }}.</td>
                            <td><div class="fw-800 text-dark" style="font-size: 1rem;">{{ $faculty->name }}</div></td>
                            <td><span class="text-muted small fw-bold">{{ $faculty->level ?: '-' }}</span></td>
                            <td class="text-center">
                                <span class="count-badge p-2 px-3">{{ $faculty->departments_count }} Prodi</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-link text-primary p-2" style="font-size: 1.1rem;" data-bs-toggle="modal" data-bs-target="#editFaculty{{ $faculty->id }}"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('admin.master.faculties.destroy', $faculty->id) }}" method="POST" onsubmit="return confirm('Hapus fakultas ini? Seluruh data prodi terkait akan dicek.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-2" style="font-size: 1.1rem;">
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
            <div class="px-4 py-3 bg-light">
                {{ $faculties->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($faculties as $faculty)
<div class="modal fade" id="editFaculty{{ $faculty->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Update Fakultas</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.faculties.update', $faculty->id) }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="info-label-premium">Nama Fakultas</label>
                        <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $faculty->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Level / Singkatan</label>
                        <input type="text" name="level" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $faculty->level }}">
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">
                        UPDATE DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
