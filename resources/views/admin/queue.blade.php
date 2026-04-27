@extends('layouts.admin')

@section('page_title', 'Verification Queue')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 25px 20px; font-size: 0.85rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; background: #f8fafc; border-radius: 12px; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 25px 20px; font-size: 1rem; color: #334155; }
    .zenith-table tbody tr:hover td { background: rgba(79, 70, 229, 0.02); }
    .text-title-zenith { font-size: 1.05rem; font-weight: 800; color: var(--zenith-sidebar); }
</style>
@endsection

@section('content')

<div class="zenith-card p-0 overflow-hidden animate-fade-in">
    <div class="p-5 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-zenith mb-1">Antrean Validasi Dokumen</h4>
            <p class="text-secondary small mb-0">Terdapat <span class="text-warning fw-bold">{{ count($pendingTheses) }} dokumen</span> yang menunggu tinjauan Anda.</p>
        </div>
        <form action="{{ route('admin.queue') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control border-0 bg-light px-4 rounded-pill shadow-sm" style="width: 300px;" placeholder="Cari nama, NIM, atau judul..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary rounded-circle shadow-sm" style="width: 45px; height: 45px; background: var(--zenith-primary); border: none;">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.queue') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fas fa-times text-danger"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive px-5 pb-5">
        <table class="table zenith-table align-middle">
            <thead>
                <tr>
                    <th style="width: 70px;">NO.</th>
                    <th>DETAIL DOKUMEN</th>
                    <th>PENGIRIM (MAHASISWA)</th>
                    <th>PRODI</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingTheses as $thesis)
                <tr>
                    <td class="text-secondary fw-800">{{ $pendingTheses->firstItem() + $loop->index }}.</td>
                    <td>
                        <div class="text-title-zenith mb-1 line-clamp-1" title="{{ $thesis->title }}">
                            @if(!$thesis->file_path)
                                <span class="badge bg-warning text-dark me-2 animate-pulse" style="font-size: 0.65rem;">
                                    <i class="fas fa-spinner fa-spin me-1"></i> SEDANG DIPROSES
                                </span>
                            @endif
                            {{ $thesis->title }}
                        </div>
                        <div class="text-muted" style="font-size: 0.8rem;">
                            <span class="badge bg-light text-secondary me-2 p-2 px-3 rounded-pill">{{ $thesis->type }}</span>
                            <i class="far fa-clock me-1"></i> {{ $thesis->created_at->diffForHumans() }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle" style="width: 35px; height: 35px; font-size: 0.75rem;">{{ $thesis->user ? substr($thesis->user->name, 0, 1) : '?' }}</div>
                            <div>
                                <div class="fw-bold small">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-secondary" style="font-size: 0.65rem;">NIM: {{ $thesis->user->nim ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-700 text-dark small">{{ $thesis->user?->department?->name ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: var(--zenith-primary); border: none;" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $thesis->id }}">
                            <i class="fas fa-search-plus me-2"></i> Review
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-5">
                        <div class="text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="fas fa-folder-open fa-3x text-muted opacity-25"></i>
                            </div>
                            <h5 class="fw-800 text-secondary">Antrean Kosong</h5>
                            <p class="text-muted small">Belum ada dokumen baru yang perlu diverifikasi saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 bg-light">
        {{ $pendingTheses->links() }}
    </div>
</div>

<!-- Modals -->
@foreach($pendingTheses as $thesis)
<div class="modal fade" id="verifyModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-5 pb-0">
                <h4 class="fw-zenith mb-0">Verifikasi Dokumen</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-4">
                    <div class="col-lg-6 d-flex align-items-stretch">
                        <div class="w-100 p-5 rounded-4 text-center border d-flex flex-column justify-content-center align-items-center" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); min-height: 400px;">
                            @if($thesis->file_path)
                                <div class="mb-4 d-inline-block p-4 rounded-circle bg-white shadow-sm">
                                    <i class="fas fa-file-pdf text-primary" style="font-size: 3rem; opacity: 0.8;"></i>
                                </div>
                                <h5 class="fw-zenith text-dark mb-2">Tinjau Dokumen</h5>
                                <p class="text-muted small px-3 mb-4">
                                    Klik tombol di bawah untuk memeriksa kesesuaian berkas skripsi.
                                </p>
                                <a href="{{ route('theses.stream', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: var(--zenith-primary); border: none;">
                                    <i class="fas fa-search-plus me-2"></i> BUKA PDF
                                </a>
                            @else
                                <div class="mb-4 d-inline-block p-4 rounded-circle bg-white shadow-sm">
                                    <i class="fas fa-hourglass-half text-warning animate-spin" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="fw-zenith text-warning mb-2">Proses Pemindahan File</h5>
                                <p class="text-muted small px-3 mb-4">
                                    Sistem sedang memindahkan file PDF ke folder permanen. Harap tunggu beberapa detik lalu refresh halaman ini.
                                </p>
                                <button disabled class="btn btn-light rounded-pill px-4 fw-bold">
                                    <i class="fas fa-spinner fa-spin me-2"></i> MENUNGGU FILE...
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-stretch">
                        <div class="zenith-card w-100 shadow-none border bg-light bg-opacity-30 p-4 d-flex flex-column">
                            <h5 class="fw-zenith mb-4">Keputusan Verifikasi</h5>
                            <form action="{{ route('admin.theses.approve', $thesis->id) }}" method="POST" class="flex-grow-1 d-flex flex-column">
                                @csrf
                                @if(!$thesis->user->isDosen())
                                <div class="mb-4">
                                    <label class="info-label-premium">Nomor Urut Surat (Sesuai Urutan Antrean)</label>
                                    <input type="text" name="cert_number_seq" class="form-control border-0 p-3 rounded-3 shadow-sm" placeholder="Contoh: 001" required>
                                    <div class="form-text mt-2" style="font-size: 0.7rem;">
                                        Format Aktif: <span class="text-primary fw-bold">{{ \App\Models\Setting::get('cert_number_format', '{ID}/UN.12/PERP/SKP/{ROMAN}/{YEAR}') }}</span>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-info border-0 rounded-4 small mb-4">
                                    <i class="fas fa-info-circle me-2"></i> Pengirim adalah <strong>Dosen</strong>. Tidak memerlukan nomor sertifikat, hanya email konfirmasi.
                                </div>
                                @endif
                                
                                <div class="mt-auto">
                                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg mb-3" {{ !$thesis->file_path ? 'disabled' : '' }}>
                                        <i class="fas fa-check-circle me-2"></i> 
                                        @if($thesis->file_path)
                                            SETUJUI & WATERMARK
                                        @else
                                            MENUNGGU FILE...
                                        @endif
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-bold small" data-bs-dismiss="modal">
                                        BATALKAN
                                    </button>
                                </div>
                            </form>
                            
                            <hr class="my-4 opacity-10">

                            <form action="{{ route('admin.theses.reject', $thesis->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="info-label-premium">Alasan Penolakan</label>
                                    <textarea name="reason" class="form-control border-0 p-3 rounded-3 shadow-sm" placeholder="Tuliskan revisi yang diperlukan..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold">
                                    <i class="fas fa-times-circle me-2"></i> TOLAK PENGAJUAN
                                </button>
                            </form>

                            <form action="{{ route('admin.theses.destroy', $thesis->id) }}" method="POST" class="mt-2" onsubmit="return confirm('HAPUS PERMANEN? Data dan file fisik akan hilang selamanya!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger w-100 small text-decoration-none fw-bold" style="font-size: 0.75rem;">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus Permanen dari Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
