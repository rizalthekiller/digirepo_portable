@extends('layouts.admin')

@section('page_title', 'User Management')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 20px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 20px; font-size: 0.9rem; color: #334155; }
    .zenith-table tbody tr:hover td { background: rgba(79, 70, 229, 0.02); }
    
    .initial-circle { width: 42px; height: 42px; border-radius: 14px; background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; }
    .status-pill { padding: 6px 12px; border-radius: 10px; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div class="zenith-card animate-fade-in">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-zenith mb-1">Database Pengguna</h4>
                <p class="text-secondary small mb-0">Total <span class="text-primary fw-bold">{{ $users->total() }} akun</span> terdaftar.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i> Tambah User
            </button>
        </div>

        <!-- Role Tabs -->
        <ul class="nav nav-pills gap-2 mb-4 p-2 bg-light rounded-4 w-fit">
            <li class="nav-item">
                <a class="nav-link rounded-3 px-4 fw-bold {{ !request('role_group') || request('role_group') == 'users' ? 'active shadow-sm bg-white text-primary' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['role_group' => 'users']) }}">
                   <i class="fas fa-users-viewfinder me-2"></i> Pengguna Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 px-4 fw-bold {{ request('role_group') == 'admins' ? 'active shadow-sm bg-white text-primary' : 'text-secondary' }}" 
                   href="{{ route('admin.users.index', ['role_group' => 'admins']) }}">
                   <i class="fas fa-shield-halved me-2"></i> Tim Admin
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="zenith-card mt-4 animate-fade-in" style="animation-delay: 0.2s;">
    <div class="table-responsive">
        <table class="table zenith-table align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;">NO.</th>
                    <th>IDENTITAS</th>
                    <th>NIM / ID</th>
                    <th>{{ request('role_group') == 'admins' ? 'HAK AKSES' : 'PROGRAM STUDI' }}</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="text-secondary fw-800">{{ $users->firstItem() + $loop->index }}.</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="initial-circle shadow-sm">{{ substr($user->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-800 text-dark small mb-0">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="fw-700 text-dark small">{{ $user->nim ?: 'N/A' }}</span></td>
                    <td>
                        @if(request('role_group') == 'admins')
                            <div class="fw-800 text-primary small text-uppercase" style="letter-spacing: 0.05em;">
                                <i class="fas {{ $user->role == 'superadmin' ? 'fa-crown text-warning' : 'fa-user-shield' }} me-1"></i>
                                {{ str_replace('superadmin', 'Super Admin', $user->role) }}
                            </div>
                        @else
                            <div class="fw-700 text-dark small">{{ $user->department->name ?? '-' }}</div>
                            <div class="text-secondary" style="font-size: 0.65rem;">{{ $user->department->faculty->name ?? '-' }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <span class="status-pill w-fit {{ $user->is_verified ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                                {{ $user->is_verified ? 'Verified' : 'Pending' }}
                            </span>
                            @if(request('role_group') != 'admins')
                                <span class="text-uppercase small fw-800 text-muted" style="font-size: 0.6rem; letter-spacing: 0.1em;">{{ $user->role }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            @if(!$user->is_verified)
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link text-success p-2" title="Verify User"><i class="fas fa-check-circle"></i></button>
                            </form>
                            @endif
                            
                            <button class="btn btn-link text-primary p-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit Data"><i class="fas fa-edit"></i></button>
                            
                            @if(auth()->check() && auth()->user()->role === 'superadmin')
                            <button class="btn btn-link text-warning p-2" data-bs-toggle="modal" data-bs-target="#editPasswordModal{{ $user->id }}" title="Ganti Password"><i class="fas fa-key"></i></button>
                            @endif

                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-2" title="Hapus User"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 bg-light bg-opacity-50">
        {{ $users->links() }}
    </div>
</div>

<!-- Zenith Edit Modals -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Edit Account</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="info-label-premium">Full Name</label>
                            <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">Institutional Email</label>
                            <input type="email" name="email" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">NIM / Identity ID</label>
                            <input type="text" name="nim" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $user->nim }}">
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">Access Role</label>
                            <select name="role" class="form-control border-0 bg-light p-3 rounded-3">
                                <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="dosen" {{ $user->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="guest" {{ $user->role == 'guest' ? 'selected' : '' }}>Guest</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="info-label-premium">Department / Major</label>
                            <select name="department_id" class="form-control border-0 bg-light p-3 rounded-3">
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                        [{{ $dept->level }}] {{ $dept->name }} - {{ $dept->faculty->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Password hanya untuk Superadmin --}}
@if(auth()->check() && auth()->user()->role === 'superadmin')
<div class="modal fade" id="editPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Reset Password</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <p class="text-secondary small mb-4">Ganti password untuk <strong>{{ $user->name }}</strong>. Password baru minimal 8 karakter.</p>
                    <div class="mb-3">
                        <label class="info-label-premium">Password Baru</label>
                        <div class="position-relative">
                            <i class="fas fa-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="password" name="password" class="form-control border-0 bg-light p-3 ps-5 rounded-3" placeholder="Masukkan password baru" required minlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-lg text-white" style="border: none;">Perbarui Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Zenith Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Tambah User Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="info-label-premium">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">Email Institusi</label>
                            <input type="email" name="email" class="form-control border-0 bg-light p-3 rounded-3" placeholder="email@univ.ac.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">NIM / NIDN / ID</label>
                            <input type="text" name="nim" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Masukkan nomor identitas" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label-premium">Role Akses</label>
                            <select name="role" class="form-control border-0 bg-light p-3 rounded-3" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="guest">Guest</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="info-label-premium">Program Studi</label>
                            <select name="department_id" class="form-control border-0 bg-light p-3 rounded-3">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">
                                        [{{ $dept->level }}] {{ $dept->name }} - {{ $dept->faculty->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="info-label-premium">Password Sementara</label>
                            <input type="password" name="password" class="form-control border-0 bg-light p-3 rounded-3" placeholder="Min. 8 karakter" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg" style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
