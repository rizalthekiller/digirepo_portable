@extends('layouts.admin')

@section('page_title', 'Antrean Verifikasi')

@section('styles')
<style>
    .queue-card { transition: all 0.3s ease; border: 1px solid #e2e8f0; }
    .queue-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: var(--primary-color); }
    .avatar-letter { width: 40px; height: 40px; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 700; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Antrean Verifikasi</h5>
                <p class="text-muted small mb-0">Total {{ $pendingTheses->count() }} dokumen menunggu peninjauan</p>
            </div>
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small">
                <i class="fas fa-info-circle me-2"></i> 
                @php
                    $hasStudent = $pendingTheses->contains(fn($t) => $t->user && !$t->user->isDosen());
                    $hasDosen = $pendingTheses->contains(fn($t) => $t->user && $t->user->isDosen());
                @endphp
                @if($hasStudent && !$hasDosen)
                    Verifikasi untuk menerbitkan sertifikat otomatis.
                @elseif($hasDosen && !$hasStudent)
                    Verifikasi karya ilmiah dosen (Tanpa Sertifikat).
                @else
                    Verifikasi dokumen untuk publikasi & sertifikasi.
                @endif
            </div>
        </div>

        @if($pendingTheses->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                    <i class="fas fa-inbox text-muted opacity-25" style="font-size: 3rem;"></i>
                </div>
            </div>
            <h6 class="fw-bold text-muted">Belum ada antrean baru</h6>
            <p class="text-secondary small">Semua dokumen telah diproses atau belum ada kiriman baru.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">NO.</th>
                        <th style="width: 40%;">DETAIL DOKUMEN</th>
                        <th>PENGIRIM</th>
                        <th class="text-center">FILE</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingTheses as $thesis)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $pendingTheses->firstItem() + $loop->index }}.</td>
                        <td>
                            <div class="fw-bold text-dark mb-1 small" style="line-height: 1.4;">{{ $thesis->title }}</div>
                            <div class="d-flex gap-2">
                                <span class="badge-soft badge-soft-primary">{{ $thesis->type }}</span>
                                <span class="badge-soft badge-soft-secondary text-dark">{{ $thesis->year }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                            @if($thesis->user && $thesis->user->isDosen())
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1 extra-small fw-bold"><i class="fas fa-chalkboard-teacher me-1"></i>Dosen</span>
                            @else
                                <div class="text-muted extra-small">{{ $thesis->user->department?->name ?? 'Umum/Non-Prodi' }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($thesis->file_path)
                                <span class="badge-soft badge-soft-success">READY</span>
                            @else
                                <span class="badge-soft badge-soft-danger">MISSING</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $thesis->id }}">
                                TINJAU
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@foreach($pendingTheses as $thesis)
<!-- Review Modal -->
<div class="modal fade" id="reviewModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Peninjauan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="p-2 bg-light rounded-4 h-100 border" style="min-height: 500px;">
                            @if($thesis->files && $thesis->files->count() > 0)
                                <div class="mb-3 d-flex gap-2 overflow-auto pb-2">
                                    @foreach($thesis->files as $file)
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill text-nowrap" onclick="document.getElementById('iframe-{{ $thesis->id }}').src='{{ route('theses.file.stream', $file->uuid ?? $file->id) }}'">
                                            <i class="fas fa-file-pdf me-1"></i> {{ $file->label }}
                                        </button>
                                    @endforeach
                                </div>
                                <iframe id="iframe-{{ $thesis->id }}" src="{{ route('theses.file.stream', $thesis->files->first()->uuid ?? $thesis->files->first()->id) }}" width="100%" height="550px" style="border: none; border-radius: 12px; background: white;"></iframe>
                            @elseif($thesis->file_path)
                                <iframe src="{{ route('theses.stream', $thesis->id) }}" width="100%" height="600px" style="border: none; border-radius: 12px; background: white;"></iframe>
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-5">
                                    <i class="fas fa-file-excel fa-4x text-muted mb-3 opacity-25"></i>
                                    <h6 class="fw-bold text-muted">File Tidak Ditemukan</h6>
                                    <p class="small text-secondary">Mahasiswa belum mengunggah file PDF.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        @php $isDosen = $thesis->user && $thesis->user->isDosen(); @endphp
                        <form action="{{ route('admin.theses.approve', $thesis->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Judul</label>
                                <textarea name="title" class="form-control rounded-3 small" rows="3" required>{{ $thesis->title ?? '' }}</textarea>
                            </div>
                            @if(!$isDosen)
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Dosen Pembimbing</label>
                                <input type="text" name="supervisor_name" class="form-control rounded-3 small" value="{{ $thesis->supervisor_name ?? '' }}" required>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Abstrak</label>
                                <textarea name="abstract" class="form-control rounded-3 small" rows="5" required>{{ $thesis->abstract ?? '' }}</textarea>
                            </div>
                            @if(!$isDosen)
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Nomor Urut Sertifikat (Opsional)</label>
                                <input type="number" name="cert_number_seq" class="form-control rounded-3 small" placeholder="Contoh: 001">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Masa Embargo (Opsional)</label>
                                <input type="date" name="embargo_until" class="form-control rounded-3 small">
                            </div>
                            @else
                            <div class="alert alert-info border-0 rounded-3 small mb-4">
                                <i class="fas fa-info-circle me-1"></i> Dokumen Dosen disetujui <b>tanpa sertifikat</b>. Hanya dipublikasikan di repositori.
                            </div>
                            @endif
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success py-2 rounded-pill fw-bold" {{ !$thesis->file_path ? 'disabled' : '' }}>
                                    @if($isDosen)
                                        <i class="fas fa-check me-1"></i> SETUJUI & PUBLIKASIKAN
                                    @else
                                        <i class="fas fa-check me-1"></i> SETUJUI & WATERMARK
                                    @endif
                                </button>
                                <button type="button" class="btn btn-outline-danger py-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $thesis->id }}">
                                    TOLAK / REVISI
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-danger">Tolak Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.theses.reject', $thesis->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Berikan alasan mengapa dokumen ini ditolak atau perlu direvisi. Pesan ini akan dikirimkan ke pengirim dokumen.</p>
                    <textarea name="reason" class="form-control rounded-3" rows="4" placeholder="Contoh: File PDF rusak, judul tidak sesuai..." required></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Review logic
</script>
@endsection
