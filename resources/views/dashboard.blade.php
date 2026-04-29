@extends('layouts.admin')

@section('page_title', Auth::user()->role === 'guest' ? 'Guest Access' : 'Dashboard Mahasiswa')

@section('styles')
<style>
    .stat-card { transition: transform 0.3s ease; border: none; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon-box { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .badge-soft { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-success { background: #ecfdf5; color: #065f46; }
    .badge-soft-warning { background: #fffbeb; color: #92400e; }
    .badge-soft-danger { background: #fef2f2; color: #991b1b; }
    .badge-soft-info { background: #f0f9ff; color: #0369a1; }
</style>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="mb-4">
    <h4 class="fw-bold text-dark mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
    <p class="text-muted small">Kelola dan pantau status pengajuan karya ilmiah Anda di sini.</p>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-5">
    @foreach([
        ['label' => 'Total Unggahan', 'val' => Auth::user()->theses()->count(), 'desc' => 'Karya ilmiah yang telah Anda daftarkan', 'icon' => 'fas fa-file-alt', 'color' => 'primary'],
        ['label' => 'Menunggu Verifikasi', 'val' => Auth::user()->theses()->where('status', 'pending')->count(), 'desc' => 'Sedang dalam proses peninjauan admin', 'icon' => 'fas fa-clock', 'color' => 'warning'],
        ['label' => 'Disetujui & Publish', 'val' => Auth::user()->theses()->where('status', 'approved')->count(), 'desc' => 'Karya yang sudah dapat diakses publik', 'icon' => 'fas fa-check-circle', 'color' => 'success']
    ] as $item)
    <div class="col-md-4">
        <div class="card stat-card shadow-sm rounded-4 h-100 border-0 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-box bg-{{ $item['color'] }} bg-opacity-10 text-{{ $item['color'] }}">
                        <i class="{{ $item['icon'] }}"></i>
                    </div>
                    <span class="text-muted small fw-bold">{{ $item['label'] }}</span>
                </div>
                <h2 class="fw-bold mb-1 {{ $item['color'] == 'primary' ? 'text-dark' : 'text-'.$item['color'] }}">{{ $item['val'] }}</h2>
                <p class="text-muted extra-small mb-0 mt-2">{{ $item['desc'] }}</p>
            </div>
            <div class="bg-{{ $item['color'] }} opacity-10 position-absolute" style="bottom: -20px; right: -20px; width: 100px; height: 100px; border-radius: 50%;"></div>
            <div class="bg-{{ $item['color'] }} opacity-5 position-absolute" style="top: -15px; left: -15px; width: 60px; height: 60px; border-radius: 50%;"></div>
        </div>
    </div>
    @endforeach
</div>

<!-- Main Content Area -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Pengajuan Terbaru</h5>
                <p class="text-muted small mb-0">Riwayat unggahan karya ilmiah Anda dalam sistem</p>
            </div>
            @php
                $latestThesis = Auth::user()->theses()->orderBy('created_at', 'desc')->first();
            @endphp

            @if(Auth::user()->role !== 'guest')
                @if(Auth::user()->isMahasiswa() && $latestThesis && ($latestThesis->status == 'approved' || $latestThesis->status == 'pending'))
                    <button class="btn btn-light rounded-pill px-4 fw-bold shadow-none text-muted small" disabled>
                        <i class="fas fa-check-circle me-1"></i> Sudah Diunggah
                    </button>
                @elseif($latestThesis && $latestThesis->status == 'rejected')
                    <a href="{{ route('theses.create') }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-edit me-1"></i> Revisi Dokumen
                    </a>
                @else
                    <a href="{{ route('theses.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-plus me-1"></i> Unggah Karya Baru
                    </a>
                @endif
            @endif
        </div>
    </div>

    @if($latestThesis && $latestThesis->status == 'rejected')
        <div class="px-4 pb-4">
            <div class="alert alert-danger border-0 rounded-4 p-4 m-0 shadow-sm d-flex align-items-center" style="background: #fff1f2;">
                <div class="stat-icon-box bg-white shadow-sm me-4 text-danger flex-shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-danger mb-1">Catatan Revisi dari Admin:</h6>
                    <p class="mb-0 text-dark opacity-75 small">"{{ $latestThesis->rejection_reason ?: 'Harap periksa kembali berkas dan data Anda sesuai petunjuk.' }}"</p>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->isMahasiswa() && $latestThesis && $latestThesis->status == 'approved')
        <div class="px-4 pb-4">
            <div class="alert alert-success border-0 rounded-4 p-4 m-0 shadow-sm d-flex align-items-center" style="background: #ecfdf5;">
                <div class="stat-icon-box bg-white shadow-sm me-4 text-success flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success mb-1">Selamat! Karya Ilmiah Anda Telah Disetujui</h6>
                    <p class="mb-0 text-dark opacity-75 small">Dokumen Anda sudah terverifikasi dan dipublikasikan di repositori. Anda tidak perlu mengunggah dokumen baru lagi.</p>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->isMahasiswa() && $latestThesis && $latestThesis->status == 'pending')
        <div class="px-4 pb-4">
            <div class="alert alert-info border-0 rounded-4 p-4 m-0 shadow-sm d-flex align-items-center" style="background: #f0f9ff;">
                <div class="stat-icon-box bg-white shadow-sm me-4 text-info flex-shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-info mb-1">Pengajuan Sedang Diproses</h6>
                    <p class="mb-0 text-dark opacity-75 small">Karya ilmiah Anda sedang dalam tahap peninjauan oleh Admin. Harap bersabar menunggu hasil verifikasi.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small fw-bold text-muted" style="width: 50%;">JUDUL KARYA</th>
                    <th class="border-0 text-center small fw-bold text-muted">TAHUN</th>
                    <th class="border-0 text-center small fw-bold text-muted">STATUS</th>
                    <th class="border-0 text-end pe-4 small fw-bold text-muted">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse(Auth::user()->theses()->orderBy('created_at', 'desc')->get() as $thesis)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="fw-bold text-dark mb-1 line-clamp-1" style="font-size: 0.9rem;">{{ $thesis->title }}</div>
                        <div class="badge bg-light text-primary border rounded-pill extra-small px-2 py-1">{{ $thesis->type }}</div>
                    </td>
                    <td class="text-center">
                        <span class="fw-semibold text-secondary small">{{ $thesis->year }}</span>
                    </td>
                    <td class="text-center">
                        @if($thesis->status == 'pending')
                            @if(!$thesis->file_path)
                                <span class="badge-soft badge-soft-info">
                                    <i class="fas fa-sync fa-spin me-1"></i> PROCESSING
                                </span>
                            @else
                                <span class="badge-soft badge-soft-warning">
                                    <i class="fas fa-clock me-1"></i> PENDING
                                </span>
                            @endif
                        @elseif($thesis->status == 'approved')
                            <span class="badge-soft badge-soft-success">
                                <i class="fas fa-check-circle me-1"></i> APPROVED
                            </span>
                        @else
                            <span class="badge-soft badge-soft-danger">
                                <i class="fas fa-times-circle me-1"></i> REJECTED
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            @if($thesis->status == 'approved' && !Auth::user()->isDosen())
                                <a href="{{ route('theses.certificate', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" title="Cetak Sertifikat">
                                    <i class="fas fa-certificate"></i>
                                </a>
                            @endif
                            <a href="{{ route('theses.read', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold text-nowrap">
                                <i class="fas fa-eye me-1"></i> Lihat Dokumen
                            </a>
                            <a href="{{ route('theses.show', $thesis->id) }}" data-turbo="false" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">
                                Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-5 text-center">
                        <div class="d-flex flex-column align-items-center py-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <i class="fas fa-folder-open fa-2x text-muted opacity-25"></i>
                            </div>
                            <h6 class="text-muted fw-bold">Belum ada riwayat pengajuan</h6>
                            <p class="small text-muted mb-0">Karya ilmiah yang Anda unggah akan muncul di sini untuk diproses.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
