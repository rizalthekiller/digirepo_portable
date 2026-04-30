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
@if(Auth::user()->isGuest())
<!-- GUEST DASHBOARD -->
<div class="mb-4">
    <h4 class="fw-bold text-dark mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
    <p class="text-muted small">Temukan dan jelajahi berbagai karya ilmiah di repositori kami.</p>
</div>

<!-- Stats Overview for Guest -->
<div class="row g-4 mb-5">
    @foreach([
        ['label' => 'Koleksi Tersimpan', 'val' => '0', 'desc' => 'Dokumen yang Anda bookmark', 'icon' => 'fas fa-star', 'color' => 'primary'],
        ['label' => 'Total Unduhan', 'val' => '0', 'desc' => 'Dokumen yang pernah Anda unduh', 'icon' => 'fas fa-download', 'color' => 'success'],
        ['label' => 'Pencarian Terakhir', 'val' => '-', 'desc' => 'Riwayat pencarian Anda', 'icon' => 'fas fa-search', 'color' => 'info']
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

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 border-0">
                <h5 class="fw-bold mb-1 text-dark">Eksplorasi Cepat</h5>
                <p class="text-muted small mb-0">Akses menu pencarian dan penjelajahan dokumen</p>
            </div>
            <div class="card-body pt-0">
                <div class="d-grid gap-3">
                    <a href="{{ route('browse') }}" class="btn btn-light d-flex justify-content-between align-items-center p-3 rounded-3 text-start border shadow-sm text-decoration-none" style="transition: all 0.3s;" onmouseover="this.classList.add('bg-white')" onmouseout="this.classList.remove('bg-white')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Pencarian Lanjut (Advanced Search)</div>
                                <div class="small text-muted">Cari dokumen berdasarkan judul, penulis, pembimbing, dan tahun</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('browse') }}" class="btn btn-light d-flex justify-content-between align-items-center p-3 rounded-3 text-start border shadow-sm text-decoration-none" style="transition: all 0.3s;" onmouseover="this.classList.add('bg-white')" onmouseout="this.classList.remove('bg-white')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Jelajah Berdasarkan Fakultas</div>
                                <div class="small text-muted">Lihat koleksi karya ilmiah sesuai bidang studi dan fakultas</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('profile') }}" class="btn btn-light d-flex justify-content-between align-items-center p-3 rounded-3 text-start border shadow-sm text-decoration-none" style="transition: all 0.3s;" onmouseover="this.classList.add('bg-white')" onmouseout="this.classList.remove('bg-white')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-user-gear"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Perbarui Profil</div>
                                <div class="small text-muted">Kelola data pribadi dan pengaturan keamanan akun Anda</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100 position-relative overflow-hidden">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-headset fa-lg"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Butuh Bantuan?</h5>
                </div>
                <p class="small opacity-75 mb-4">Pelajari cara mencari, membaca, dan mengunduh dokumen secara efektif di sistem repositori ini.</p>
                <a href="{{ route('faq') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm w-100">
                    <i class="fas fa-question-circle me-1"></i> Baca FAQ
                </a>
            </div>
            <i class="fas fa-book-reader position-absolute text-white opacity-10" style="bottom: -20px; right: -20px; font-size: 10rem; transform: rotate(-15deg);"></i>
        </div>
    </div>
</div>
@else
<!-- EXISTING CONTENT FOR MAHASISWA/DOSEN -->
@php
    $latestThesis = Auth::user()->theses()->orderBy('created_at', 'desc')->first();
@endphp

<div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
        <p class="text-muted small mb-0">Lengkapi kewajiban akademik Anda dengan mengunggah karya ilmiah ke repositori.</p>
    </div>
    @if(!$latestThesis || $latestThesis->status == 'rejected')
        <a href="{{ route('theses.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas {{ $latestThesis ? 'fa-edit' : 'fa-plus' }} me-2"></i> {{ $latestThesis ? 'Revisi Dokumen' : 'Unggah Karya Baru' }}
        </a>
    @endif
</div>

<div class="row g-4 mb-4">
    <!-- Main Progress Tracker -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 border-0">
                <h5 class="fw-bold mb-0 text-dark">Status Pengajuan Terakhir</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if(!$latestThesis)
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-file-upload fa-2x text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold">Belum Ada Pengajuan</h6>
                        <p class="text-muted small mb-4">Anda belum mengunggah karya ilmiah apa pun ke sistem.</p>
                        <a href="{{ route('theses.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Mulai Unggah Sekarang</a>
                    </div>
                @else
                    <!-- Timeline Progress -->
                    <div class="position-relative mb-4 mt-3 px-3">
                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: #f1f5f9;">
                            @php
                                $progress = 33;
                                $color = 'primary';
                                if($latestThesis->status == 'pending') { $progress = 50; $color = 'warning'; }
                                elseif($latestThesis->status == 'approved') { $progress = 100; $color = 'success'; }
                                elseif($latestThesis->status == 'rejected') { $progress = 50; $color = 'danger'; }
                            @endphp
                            <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between position-absolute w-100" style="top: -12px; left: 0;">
                            <div class="text-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 30px; height: 30px; border: 3px solid #fff;"><i class="fas fa-check small"></i></div>
                                <div class="small fw-bold mt-2 text-dark">Upload</div>
                            </div>
                            <div class="text-center">
                                <div class="bg-{{ $latestThesis->status != 'pending' ? ($latestThesis->status == 'rejected' ? 'danger' : 'success') : 'white' }} {{ in_array($latestThesis->status, ['approved', 'rejected']) ? 'text-white' : 'text-muted' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 30px; height: 30px; border: 3px solid #fff;">
                                    @if($latestThesis->status == 'rejected') <i class="fas fa-times small"></i> @else <i class="fas fa-search small"></i> @endif
                                </div>
                                <div class="small fw-bold mt-2 {{ in_array($latestThesis->status, ['approved', 'rejected']) ? 'text-dark' : 'text-muted' }}">Verifikasi</div>
                            </div>
                            <div class="text-center">
                                <div class="bg-{{ $latestThesis->status == 'approved' ? 'success' : 'white' }} {{ $latestThesis->status == 'approved' ? 'text-white' : 'text-muted' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 30px; height: 30px; border: 3px solid #fff;"><i class="fas fa-award small"></i></div>
                                <div class="small fw-bold mt-2 {{ $latestThesis->status == 'approved' ? 'text-dark' : 'text-muted' }}">Selesai</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Alerts -->
                    @if($latestThesis->status == 'rejected')
                        <div class="alert alert-danger border-0 rounded-4 p-4 mt-5 mb-0 shadow-sm d-flex align-items-center" style="background: #fff1f2;">
                            <div class="stat-icon-box bg-white shadow-sm me-4 text-danger flex-shrink-0"><i class="fas fa-exclamation-triangle"></i></div>
                            <div>
                                <h6 class="fw-bold text-danger mb-1">Pengajuan Ditolak / Butuh Revisi</h6>
                                <p class="mb-0 text-dark opacity-75 small">Catatan Admin: "{{ $latestThesis->rejection_reason ?: 'Harap perbaiki dokumen Anda.' }}"</p>
                            </div>
                        </div>
                    @elseif($latestThesis->status == 'approved')
                        <div class="alert alert-success border-0 rounded-4 p-4 mt-5 mb-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #ecfdf5;">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-box bg-white shadow-sm me-3 text-success flex-shrink-0"><i class="fas fa-check-circle"></i></div>
                                <div>
                                    <h6 class="fw-bold text-success mb-1">Selamat! Karya Ilmiah Disetujui</h6>
                                    <p class="mb-0 text-dark opacity-75 small">Dokumen Anda telah divalidasi. Anda kini dapat mencetak Sertifikat Bebas Pustaka.</p>
                                </div>
                            </div>
                            <a href="{{ route('theses.certificate', $latestThesis->id) }}" target="_blank" data-turbo="false" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                                <i class="fas fa-print"></i> Cetak Sertifikat
                            </a>
                        </div>
                    @elseif($latestThesis->status == 'pending')
                        <div class="alert alert-warning border-0 rounded-4 p-4 mt-5 mb-0 shadow-sm d-flex align-items-center" style="background: #fffbeb;">
                            <div class="stat-icon-box bg-white shadow-sm me-4 text-warning flex-shrink-0"><i class="fas fa-clock"></i></div>
                            <div>
                                <h6 class="fw-bold text-warning mb-1">Sedang Dalam Antrean Verifikasi</h6>
                                <p class="mb-0 text-dark opacity-75 small">Mohon bersabar, admin sedang meninjau kelengkapan dokumen dan meta-data yang Anda unggah.</p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Help & Resources -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white position-relative overflow-hidden">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <h5 class="fw-bold mb-3"><i class="fas fa-book-open me-2"></i> Panduan Unggah</h5>
                <p class="small opacity-75 mb-4">Pastikan format file dan meta-data yang Anda masukkan sesuai dengan pedoman perpustakaan.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('faq') }}" class="btn btn-light btn-sm rounded-pill py-2 fw-bold text-primary text-start px-3 shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i> Syarat Unggah Mandiri
                    </a>
                    <a href="{{ route('faq') }}" class="btn btn-light btn-sm rounded-pill py-2 fw-bold text-primary text-start px-3 shadow-sm">
                        <i class="fas fa-question-circle me-2"></i> FAQ & Bantuan
                    </a>
                </div>
            </div>
            <i class="fas fa-layer-group position-absolute text-white opacity-10" style="bottom: -20px; right: -20px; font-size: 8rem; transform: rotate(-15deg);"></i>
        </div>
    </div>
</div>

<!-- Main Content Area (Table) -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 border-0">
        <h5 class="fw-bold mb-1 text-dark">Riwayat Pengajuan Anda</h5>
        <p class="text-muted small mb-0">Daftar semua karya ilmiah yang pernah Anda ajukan ke repositori</p>
    </div>
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
@endif
@endsection
