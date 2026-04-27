@extends('layouts.admin')

@section('page_title', Auth::user()->role === 'guest' ? 'Guest' : 'Dashboard Mahasiswa')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="zenith-card h-100" style="background: linear-gradient(135deg, #6366f1, #a855f7); color: white; border: none;">
            <span class="info-label-premium text-white-50">Total Karya Ilmiah</span>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="display-5 fw-zenith mb-0">{{ Auth::user()->theses()->count() }}</h2>
                <i class="fas fa-file-alt fa-2x opacity-50"></i>
            </div>
            <p class="small mt-3 mb-0 opacity-75">Karya ilmiah yang telah Anda unggah</p>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="zenith-card h-100">
            <span class="info-label-premium">Menunggu Verifikasi</span>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="display-5 fw-zenith mb-0" style="color: #f59e0b;">{{ Auth::user()->theses()->where('status', 'pending')->count() }}</h2>
                <div class="avatar-circle" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="small mt-3 mb-0 text-muted">Sedang ditinjau oleh admin</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="zenith-card h-100">
            <span class="info-label-premium">Disetujui & Publish</span>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="display-5 fw-zenith mb-0" style="color: #10b981;">{{ Auth::user()->theses()->where('status', 'approved')->count() }}</h2>
                <div class="avatar-circle" style="background: #d1fae5; color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="small mt-3 mb-0 text-muted">Karya yang sudah dapat diakses publik</p>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="zenith-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-zenith">Pengajuan Terbaru</h4>
                    <p class="text-muted small mb-0">Riwayat unggahan karya ilmiah Anda</p>
                </div>
                @php
                    $latestThesis = Auth::user()->theses()->orderBy('created_at', 'desc')->first();
                @endphp

                @if(Auth::user()->role !== 'guest')
                    @if($latestThesis && ($latestThesis->status == 'approved' || $latestThesis->status == 'pending'))
                        <button class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm opacity-50" disabled title="Dokumen sedang diproses atau sudah disetujui">
                            <i class="fas fa-check me-2"></i> Sudah Diunggah
                        </button>
                    @elseif($latestThesis && $latestThesis->status == 'rejected')
                        <a href="{{ route('theses.create') }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-edit me-2"></i> Lakukan Revisi
                        </a>
                    @else
                        <a href="{{ route('theses.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-plus me-2"></i> Unggah Baru
                        </a>
                    @endif
                @endif
            </div>

            @if($latestThesis && $latestThesis->status == 'rejected')
                <div class="alert alert-danger border-0 rounded-4 p-4 mb-4 shadow-sm" style="background: #fef2f2;">
                    <div class="d-flex align-items-center">
                        <div class="p-3 rounded-circle bg-white shadow-sm me-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Catatan Revisi dari Admin:</h6>
                            <p class="mb-0 text-dark opacity-75">"{{ $latestThesis->rejection_reason ?: 'Harap periksa kembali berkas dan data Anda sesuai petunjuk.' }}"</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted small fw-bold">
                        <tr>
                            <th class="border-0 px-0" style="width: 50%;">JUDUL KARYA</th>
                            <th class="border-0 text-center">TAHUN</th>
                            <th class="border-0 text-center">STATUS</th>
                            <th class="border-0 text-center">OPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(Auth::user()->theses()->orderBy('created_at', 'desc')->get() as $thesis)
                        <tr>
                            <td class="px-0 py-3">
                                <div class="fw-bold text-dark line-clamp-1">{{ $thesis->title }}</div>
                                <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ $thesis->type }}</div>
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark rounded-pill px-3">{{ $thesis->year }}</span></td>
                            <td class="text-center">
                                @if($thesis->status == 'pending')
                                    @if(!$thesis->file_path)
                                        <span class="badge rounded-pill px-3 py-2" style="background: #e0f2fe; color: #0369a1;">
                                            <i class="fas fa-cog fa-spin me-1"></i> Memproses...
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2" style="background: #fffbeb; color: #92400e;">
                                            <i class="fas fa-clock me-1"></i> Menunggu
                                        </span>
                                    @endif
                                @elseif($thesis->status == 'approved')
                                    <span class="badge rounded-pill px-3 py-2" style="background: #ecfdf5; color: #065f46;">
                                        <i class="fas fa-check me-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background: #fef2f2; color: #991b1b;">
                                        <i class="fas fa-times me-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="text-center px-0">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($thesis->status == 'approved')
                                        <a href="{{ route('theses.certificate', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                            <i class="fas fa-certificate me-1"></i> Sertifikat
                                        </a>
                                    @endif
                                    <a href="{{ route('theses.show', $thesis->id) }}" data-turbo="false" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                    <i class="fas fa-folder-open fa-3x text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-muted fw-zenith">Belum ada riwayat pengajuan</h5>
                                <p class="small text-muted">Karya ilmiah yang Anda unggah akan muncul di sini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
