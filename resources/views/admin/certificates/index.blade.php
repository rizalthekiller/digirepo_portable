@extends('layouts.admin')

@section('page_title', 'Arsip Sertifikat')

@section('styles')
<style>
    .badge-soft { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 6px; }
    .badge-soft-success { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .badge-soft-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
    .badge-soft-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    
    /* Fix for dropdown being clipped by table-responsive */
    .table-responsive {
        overflow: visible !important;
        padding-bottom: 80px;
        margin-bottom: -80px;
    }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-4 border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Arsip Surat Keterangan</h5>
                <p class="text-muted small mb-0">Manajemen seluruh <span class="text-primary fw-bold">Surat Keterangan Penyerahan (SKP)</span>.</p>
            </div>
            <form action="{{ route('admin.certificates.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-light border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-light ps-2" placeholder="Cari nomor atau nama..." style="width: 250px;" value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-light border-0 bg-light px-3"><i class="fas fa-times text-danger"></i></a>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">CARI</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">NO.</th>
                    <th>NOMOR SURAT</th>
                    <th>MAHASISWA</th>
                    <th>PRODI & TIPE</th>
                    <th class="text-center">STATUS EMAIL</th>
                    <th class="text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($theses as $thesis)
                <tr>
                    <td class="ps-4 text-muted small">{{ $theses->firstItem() + $loop->index }}.</td>
                    <td>
                        <div class="fw-bold text-dark mb-1">{{ $thesis->certificate_number }}</div>
                        <div class="text-muted extra-small">
                            <i class="far fa-calendar-alt me-1"></i> 
                            {{ $thesis->certificate_date ? \Carbon\Carbon::parse($thesis->certificate_date)->format('d M Y') : $thesis->updated_at->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-muted extra-small">NIM: {{ $thesis->user->nim ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="small fw-bold text-dark">
                            <span class="text-primary">[{{ $thesis->user->department->level ?? '-' }}]</span> 
                            {{ $thesis->user->department->name ?? '-' }}
                        </div>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 mt-1" style="font-size: 0.6rem;">{{ strtoupper($thesis->type) }}</span>
                    </td>
                    <td class="text-center">
                        @if($thesis->delivery_status == 'sent')
                            <span class="badge-soft badge-soft-success"><i class="fas fa-check-circle"></i> SENT</span>
                        @elseif($thesis->delivery_status == 'failed')
                            <span class="badge-soft badge-soft-danger"><i class="fas fa-times-circle"></i> FAILED</span>
                        @else
                            <span class="badge-soft badge-soft-warning"><i class="fas fa-clock"></i> PENDING</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="previewLetter('{{ route('admin.certificates.print', $thesis->id) }}?preview=1', '{{ $thesis->certificate_number }}')"><i class="fas fa-eye me-2 text-primary"></i> Pratinjau</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editCertModal{{ $thesis->id }}"><i class="fas fa-edit me-2 text-warning"></i> Edit Data</a></li>
                                <li>
                                    <form action="{{ route('admin.certificates.resend', $thesis->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-envelope me-2 text-info"></i> Kirim Ulang Email</button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.theses.reset', $thesis->id) }}" method="POST" onsubmit="return confirm('Batalkan surat ini?')">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-undo me-2"></i> Batalkan / Reset</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-file-alt text-muted mb-3 d-block fs-1 opacity-25"></i>
                        <p class="text-muted">Tidak ada data ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($theses->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $theses->links() }}
    </div>
    @endif
</div>

@foreach($theses as $thesis)
<!-- Modal Edit Cert -->
<div class="modal fade" id="editCertModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Edit Data Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.certificates.update', $thesis->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Surat</label>
                        <input type="text" name="certificate_number" class="form-control rounded-3" value="{{ $thesis->certificate_number }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Surat</label>
                        <input type="date" name="certificate_date" class="form-control rounded-3" value="{{ $thesis->certificate_date ?: $thesis->updated_at->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Isi Keterangan</label>
                        <textarea name="certificate_content" class="form-control rounded-3" rows="5" required>{{ $thesis->certificate_content }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="resend_email" id="resend{{ $thesis->id }}">
                        <label class="form-check-label small fw-bold" for="resend{{ $thesis->id }}">Kirim ulang email ke mahasiswa</label>
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
@endforeach

<!-- Preview Modal -->
<div class="modal fade" id="previewLetterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0" id="modalTitle">Pratinjau Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closePreview()"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Memuat Dokumen...</p>
                </div>
                <iframe id="previewIframe" src="" style="width: 100%; height: 700px; border: 0; border-radius: 12px; display: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const previewModal = new bootstrap.Modal(document.getElementById('previewLetterModal'));
    const previewIframe = document.getElementById('previewIframe');
    const modalTitle = document.getElementById('modalTitle');
    const modalLoader = document.getElementById('modalLoader');

    function previewLetter(url, number) {
        modalTitle.innerText = 'Pratinjau Surat: ' + number;
        previewIframe.src = url;
        previewIframe.style.display = 'none';
        modalLoader.style.display = 'block';
        previewModal.show();

        previewIframe.onload = function() {
            modalLoader.style.display = 'none';
            previewIframe.style.display = 'block';
        };
    }

    function closePreview() {
        previewIframe.src = '';
    }
</script>
@endsection
