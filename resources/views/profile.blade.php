@extends('layouts.admin')

@section('page_title', 'Profil Pengguna')

@section('styles')
<style>
    .profile-avatar-box {
        width: 120px; height: 120px; border-radius: 35px;
        background: linear-gradient(135deg, var(--primary-color), #818cf8);
        color: white; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 25px; font-size: 3rem; font-weight: 800;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.2);
    }
    .info-group { margin-bottom: 2rem; }
    .info-label { font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; display: block; }
    .info-value { font-weight: 700; color: #1e293b; font-size: 1.05rem; }
    .card-accent { border-left: 4px solid var(--primary-color); }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
            <div class="card-body">
                <div class="profile-avatar-box">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h4>
                <p class="text-muted small mb-2">{{ Auth::user()->email }}</p>
                
                <div class="mb-3">
                    <span class="badge {{ Auth::user()->isGuest() ? 'bg-info' : 'bg-primary' }} text-white rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                        {{ Auth::user()->role }}
                    </span>
                </div>
                
                <div class="badge bg-light text-primary rounded-pill px-4 py-2 mb-4 border fw-bold">
                    <i class="fas fa-id-card me-2"></i> {{ Auth::user()->nim ?: (Auth::user()->isGuest() ? 'BELUM DIATUR' : 'ADMINISTRATOR') }}
                </div>

                <div class="p-4 bg-light rounded-4 text-center mt-2 border border-dashed">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 45px; height: 45px;">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h6 class="fw-bold small mb-2">Keamanan Akun</h6>
                    <p class="extra-small text-muted mb-4 opacity-75">Kelola kata sandi Anda secara berkala untuk menjaga keamanan data.</p>
                    <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold small shadow-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-2"></i> Ganti Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 card-accent">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-5">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Informasi Profil</h5>
                        <p class="text-muted small mb-0">Detail identitas dan data akademik Anda</p>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill px-4 fw-bold small" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-user-edit me-2"></i> Edit Profil
                    </button>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <span class="info-label">Nama Lengkap</span>
                            <div class="info-value">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Alamat Email</span>
                            <div class="info-value text-primary">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <span class="info-label">{{ Auth::user()->getIdentityLabel() }}</span>
                            <div class="info-value">{{ Auth::user()->nim ?: '-' }}</div>
                        </div>
                        @if(!Auth::user()->isGuest())
                        <div class="info-group">
                            <span class="info-label">Program Studi</span>
                            <div class="info-value">{{ Auth::user()->department->name ?? 'Administrator System' }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-group">
                            <span class="info-label">Fakultas / Unit Kerja</span>
                            <div class="info-value">{{ Auth::user()->department->faculty->name ?? 'Pusat Data DigiRepo' }}</div>
                        </div>
                    </div>
                        @else
                        <div class="info-group">
                            <span class="info-label">Asal Instansi / Afiliasi</span>
                            <div class="info-value {{ !Auth::user()->affiliation ? 'text-muted fst-italic' : '' }}">{{ Auth::user()->affiliation ?: 'Masyarakat Umum / Peneliti Luar' }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-group">
                            <span class="info-label">Hak Akses</span>
                            <div class="info-value text-success"><i class="fas fa-check-circle me-1"></i> Guest Repository Aktif</div>
                        </div>
                    </div>
                        @endif
                </div>

                <div class="mt-4 pt-4 border-top border-dashed">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light border">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <span class="info-label mb-0">Bergabung</span>
                                    <div class="fw-bold small text-dark">{{ Auth::user()->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light border">
                                <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-clock-rotate-left"></i>
                                </div>
                                <div>
                                    <span class="info-label mb-0">Update Terakhir</span>
                                    <div class="fw-bold small text-dark">{{ Auth::user()->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ Auth::user()->getIdentityLabel() }}</label>
                        <input type="text" name="nim" class="form-control rounded-3" value="{{ Auth::user()->nim }}" required>
                        @if(Auth::user()->isGuest())
                            <div class="form-text extra-small mt-1 text-muted">Gunakan Nomor Induk Kependudukan (NIK) KTP atau nomor Paspor.</div>
                        @endif
                    </div>
                    @if(Auth::user()->isGuest())
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Asal Instansi / Afiliasi (Opsional)</label>
                        <input type="text" name="affiliation" class="form-control rounded-3" value="{{ Auth::user()->affiliation }}" placeholder="Contoh: Universitas Brawijaya / Peneliti Mandiri">
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4 text-danger">
                <h5 class="fw-bold mb-0">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Sekarang</label>
                        <input type="password" name="current_password" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
