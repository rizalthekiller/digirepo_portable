@extends('layouts.admin')

@section('page_title', 'User Management')

@section('styles')
<style>
    .initial-circle { width: 35px; height: 35px; border-radius: 50%; background: #f1f5f9; color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; border: 1px solid #e2e8f0; }
    .nav-pills-custom { background: #f1f5f9; padding: 4px; border-radius: 12px; display: inline-flex; }
    .nav-pills-custom .nav-link { border-radius: 8px; padding: 8px 20px; font-weight: 600; color: #64748b; border: none; font-size: 0.85rem; }
    .nav-pills-custom .nav-link.active { background: white; color: var(--primary-color); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .extra-small { font-size: 0.7rem; }
    
    /* Fix for dropdown being clipped by table-responsive */
    .table-responsive {
        overflow: visible !important;
        padding-bottom: 80px;
        margin-bottom: -80px;
    }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">Database Pengguna</h6>
            <div class="d-flex gap-2">
                <div class="nav-pills-custom me-2">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ !request('role_group') ? 'active' : '' }}">Mahasiswa</a>
                    <a href="{{ route('admin.users.index', ['role_group' => 'admins']) }}" class="nav-link {{ request('role_group') == 'admins' ? 'active' : '' }}">Administrator</a>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-1"></i> Tambah User
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small fw-bold text-muted">NO.</th>
                        <th class="border-0 small fw-bold text-muted">IDENTITAS</th>
                        <th class="border-0 small fw-bold text-muted">NIM / ID</th>
                        <th class="border-0 small fw-bold text-muted">{{ request('role_group') == 'admins' ? 'HAK AKSES' : 'PROGRAM STUDI' }}</th>
                        <th class="border-0 small fw-bold text-muted">STATUS</th>
                        <th class="text-center pe-4 border-0 small fw-bold text-muted">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $users->firstItem() + $loop->index }}.</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="initial-circle">{{ substr($user->name, 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="text-muted extra-small">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="small fw-semibold text-secondary">{{ $user->nim ?: 'N/A' }}</span></td>
                        <td>
                            @if(request('role_group') == 'admins')
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 small px-3 rounded-pill">{{ strtoupper($user->role) }}</span>
                            @else
                                <div class="small text-dark">{{ $user->department->name ?? '-' }}</div>
                                <div class="extra-small text-muted">{{ $user->department->faculty->name ?? '' }}</div>
                            @endif
                        </td>
                        <td>
                            @if($user->is_verified)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Verified</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="fas fa-edit me-2 text-primary"></i> Edit Profil</a></li>
                                    @if(auth()->user()->role === 'superadmin')
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editPasswordModal{{ $user->id }}"><i class="fas fa-key me-2 text-warning"></i> Reset Password</a></li>
                                    @endif
                                    @if(!$user->is_verified)
                                    <li>
                                        <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item"><i class="fas fa-check-circle me-2 text-success"></i> Verifikasi</button>
                                        </form>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini selamanya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

@foreach($users as $user)
<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Edit Profil Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Institusi</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM / NIDN / ID</label>
                            <input type="text" name="nim" class="form-control rounded-3" value="{{ $user->nim }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hak Akses (Role)</label>
                            <select name="role" class="form-select rounded-3">
                                <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="dosen" {{ $user->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="guest" {{ $user->role == 'guest' ? 'selected' : '' }}>Guest</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="department_id" class="form-select rounded-3">
                                <option value="">-- Tanpa Program Studi --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                        [{{ $dept->level }}] {{ $dept->name }} - {{ $dept->faculty->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
@if(auth()->user()->role === 'superadmin')
<div class="modal fade" id="editPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0 text-warning">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-4">Ganti password untuk <strong>{{ $user->name }}</strong>. Password baru minimal 8 karakter.</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Masukkan password baru" required minlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm text-white">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Institusi</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="email@univ.ac.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM / NIDN / ID</label>
                            <input type="text" name="nim" class="form-control rounded-3" placeholder="Masukkan nomor identitas" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Role Akses</label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="guest">Guest</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="department_id" class="form-select rounded-3">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">
                                        [{{ $dept->level }}] {{ $dept->name }} - {{ $dept->faculty->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Password Sementara</label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Min. 8 karakter" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
