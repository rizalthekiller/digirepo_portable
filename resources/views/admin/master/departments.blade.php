@extends('layouts.admin')

@section('page_title', 'Department Management')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 20px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 20px; font-size: 0.9rem; color: #334155; }
    .level-badge { background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.65rem; }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="zenith-card">
            <h5 class="fw-zenith mb-4">Tambah Program Studi</h5>
            <form action="{{ route('admin.master.departments.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="info-label-premium">Pilih Fakultas</label>
                    <select name="faculty_id" class="form-control border-0 bg-light p-3 rounded-3 shadow-none" required>
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="info-label-premium">Kode Program Studi</label>
                    <input type="text" name="code" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Contoh: IF" required>
                </div>
                <div class="mb-4">
                    <label class="info-label-premium">Nama Program Studi</label>
                    <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Contoh: Teknik Informatika" required>
                </div>
                <div class="mb-4">
                    <label class="info-label-premium">Jenjang (Level)</label>
                    <select name="level" class="form-control border-0 bg-light p-3 rounded-3">
                        <option value="S1">S1 - Sarjana</option>
                        <option value="S2">S2 - Magister</option>
                        <option value="S3">S3 - Doktor</option>
                        <option value="D3">D3 - Diploma</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">
                    SIMPAN PRODI
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="zenith-card p-0 overflow-hidden h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-zenith mb-0 px-3">Daftar Program Studi</h5>
            </div>
            <div class="table-responsive p-4">
                <table class="table zenith-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">NO.</th>
                            <th style="width: 100px;">KODE</th>
                            <th>PROGRAM STUDI</th>
                            <th>FAKULTAS</th>
                            <th class="text-center">JENJANG</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $dept)
                        <tr>
                            <td class="text-secondary fw-800">{{ $departments->firstItem() + $loop->index }}.</td>
                            <td><span class="badge bg-light text-primary fw-bold p-2 px-3">{{ $dept->code ?: '-' }}</span></td>
                            <td><div class="fw-800 text-dark" style="font-size: 1rem;">{{ $dept->name }}</div></td>
                            <td><div class="text-secondary small fw-bold">{{ $dept->faculty->name ?? '-' }}</div></td>
                            <td class="text-center">
                                <span class="level-badge p-2 px-3">{{ $dept->level ?: 'S1' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-link text-primary p-2" style="font-size: 1.1rem;" data-bs-toggle="modal" data-bs-target="#editDept{{ $dept->id }}"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('admin.master.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Hapus program studi ini?')">
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
            <div class="px-4 py-3 bg-light bg-opacity-50 border-top">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($departments as $dept)
<div class="modal fade" id="editDept{{ $dept->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Update Prodi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.departments.update', $dept->id) }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="info-label-premium">Pilih Fakultas</label>
                        <select name="faculty_id" class="form-control border-0 bg-light p-3 rounded-3" required>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ $dept->faculty_id == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Kode Program Studi</label>
                        <input type="text" name="code" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $dept->code }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Nama Program Studi</label>
                        <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $dept->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="info-label-premium">Jenjang (Level)</label>
                        <select name="level" class="form-control border-0 bg-light p-3 rounded-3">
                            <option value="S1" {{ $dept->level == 'S1' ? 'selected' : '' }}>S1 - Sarjana</option>
                            <option value="S2" {{ $dept->level == 'S2' ? 'selected' : '' }}>S2 - Magister</option>
                            <option value="S3" {{ $dept->level == 'S3' ? 'selected' : '' }}>S3 - Doktor</option>
                            <option value="D3" {{ $dept->level == 'D3' ? 'selected' : '' }}>D3 - Diploma</option>
                        </select>
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
