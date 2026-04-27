@extends('layouts.admin')

@section('page_title', 'Profil Saya')

@section('styles')
<style>
    .avatar-giant {
        width: 120px; height: 120px; border-radius: 40px;
        background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary));
        color: white; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 30px; font-size: 3rem; font-weight: 800;
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.2);
        font-family: 'Outfit', sans-serif;
    }

    .info-label-index {
        font-size: 0.7rem; font-weight: 800; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 8px; display: block;
    }

    .info-value-index {
        font-weight: 700; color: #1e293b;
        font-size: 1.1rem; font-family: 'Outfit', sans-serif;
    }

    .security-badge-index {
        background: #f8fafc; border-radius: 20px; padding: 30px;
        border: 1px solid #e2e8f0; transition: var(--transition);
    }
</style>
@endsection

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-lg-4">
        <div class="zenith-card text-center h-100">
            <div class="avatar-giant">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <h4 class="fw-zenith mb-1">{{ Auth::user()->name }}</h4>
            <p class="text-muted small mb-4">{{ Auth::user()->email }}</p>
            
            <div class="badge rounded-pill px-3 py-2 mb-4" style="background: #f1f5f9; color: #475569;">
                <i class="fas fa-id-card me-2"></i> {{ Auth::user()->nim ?: 'ADMIN' }}
            </div>

            <div class="security-badge-index text-center mt-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-lg" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)) !important;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p class="small text-muted mb-4">Akun Anda terlindungi dengan enkripsi standar industri.</p>
                <button class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold small" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-2"></i> Ganti Kata Sandi
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="zenith-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-5">
                <div>
                    <h4 class="fw-zenith mb-1">Informasi Akademik</h4>
                    <p class="text-muted small">Detail data identitas dan institusi Anda</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-2"></i> Edit Profil
                </button>
            </div>

            <div class="row g-5">
                <div class="col-md-6">
                    <div class="mb-4">
                        <span class="info-label-index">Fakultas / Institusi</span>
                        <div class="info-value-index text-primary">{{ Auth::user()->department->faculty->name ?? 'Repositori Pusat' }}</div>
                    </div>
                    <div class="mb-4">
                        <span class="info-label-index">Program Studi</span>
                        <div class="info-value-index">{{ Auth::user()->department->name ?? 'Manajemen Data Digital' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <span class="info-label-index">Nomor Identitas (NIM)</span>
                        <div class="info-value-index">{{ Auth::user()->nim ?: '-' }}</div>
                    </div>
                    <div class="mb-4">
                        <span class="info-label-index">Status Verifikasi</span>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                <i class="fas fa-check-circle me-1"></i> AKTIF & TERVERIFIKASI
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5 opacity-10">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light">
                        <div class="avatar-circle" style="width: 45px; height: 45px; background: #fff; color: var(--zenith-primary);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted extra-small" style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">Terdaftar Sejak</div>
                            <div class="fw-bold small">{{ Auth::user()->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light">
                        <div class="avatar-circle" style="width: 45px; height: 45px; background: #fff; color: var(--zenith-secondary);">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <div class="text-muted extra-small" style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">Terakhir Diperbarui</div>
                            <div class="fw-bold small">{{ Auth::user()->updated_at->diffForHumans() }}</div>
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
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h4 class="fw-800 mb-1">Edit Profil</h4>
                    <p class="text-muted small">Perbarui informasi identitas Anda</p>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                        <input type="text" name="name" class="form-control rounded-pill px-4 py-2 bg-light border-0" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">NIM / NOMOR IDENTITAS</label>
                        <input type="text" name="nim" class="form-control rounded-pill px-4 py-2 bg-light border-0" value="{{ Auth::user()->nim }}" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold">SIMPAN PERUBAHAN</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Kata Sandi -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h4 class="fw-800 mb-1">Ganti Kata Sandi</h4>
                    <p class="text-muted small">Gunakan kata sandi yang kuat dan unik</p>
                </div>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KATA SANDI SEKARANG</label>
                        <input type="password" name="current_password" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KATA SANDI BARU</label>
                        <input type="password" name="password" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KONFIRMASI KATA SANDI BARU</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill py-3 fw-bold">UPDATE KATA SANDI</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h4 class="fw-800 mb-1">Edit Profil</h4>
                    <p class="text-muted small">Perbarui informasi identitas Anda</p>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                        <input type="text" name="name" class="form-control rounded-pill px-4 py-2 bg-light border-0" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">NIM / NOMOR IDENTITAS</label>
                        <input type="text" name="nim" class="form-control rounded-pill px-4 py-2 bg-light border-0" value="{{ Auth::user()->nim }}" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold">SIMPAN PERUBAHAN</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Kata Sandi -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <h4 class="fw-800 mb-1">Ganti Kata Sandi</h4>
                    <p class="text-muted small">Gunakan kata sandi yang kuat dan unik</p>
                </div>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KATA SANDI SEKARANG</label>
                        <input type="password" name="current_password" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KATA SANDI BARU</label>
                        <input type="password" name="password" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KONFIRMASI KATA SANDI BARU</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-pill px-4 py-2 bg-light border-0" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill py-3 fw-bold">UPDATE KATA SANDI</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
