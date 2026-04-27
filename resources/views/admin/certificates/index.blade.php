@extends('layouts.admin')

@section('page_title', 'Data Surat Keterangan')

@section('styles')
<style>
    .zenith-table thead th { border: none; padding: 20px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 800; background: #f8fafc; }
    .zenith-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 20px; font-size: 0.95rem; color: #334155; }
    .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; }
    .status-sent { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fffbeb; color: #d97706; }
    .status-failed { background: #fef2f2; color: #dc2626; }
    
    .action-btn { 
        width: 40px; 
        height: 40px; 
        border-radius: 10px; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
    }
    .action-btn:hover { background: #f1f5f9; color: var(--zenith-primary); transform: translateY(-2px); }
    .action-btn.btn-view:hover { color: #3b82f6; border-color: #3b82f6; }
    .action-btn.btn-print:hover { color: #10b981; border-color: #10b981; }
    .action-btn.btn-edit:hover { color: #f59e0b; border-color: #f59e0b; }
    .action-btn.btn-resend:hover { color: #8b5cf6; border-color: #8b5cf6; }
    .action-btn.btn-reset:hover { color: #ef4444; border-color: #ef4444; }
</style>
@endsection

@section('content')
<div class="zenith-card p-0 overflow-hidden animate-fade-in">
    <div class="p-5 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-zenith mb-1">Arsip Surat Keterangan</h4>
            <p class="text-secondary small mb-0">Manajemen seluruh <span class="text-primary fw-bold">Surat Keterangan Penyerahan (SKP)</span>.</p>
        </div>
        <form action="{{ route('admin.certificates.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control border-0 bg-light px-4 py-3 rounded-pill shadow-sm" placeholder="Cari nomor atau nama..." style="width: 280px; font-size: 0.85rem;" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary rounded-circle shadow-sm" style="width: 48px; height: 48px; background: var(--zenith-primary); border: none;">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-times text-danger"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive px-5 pb-5">
        <table class="table zenith-table align-middle border-0">
            <thead>
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th style="width: 220px;">NOMOR SURAT</th>
                    <th style="width: 250px;">NAMA MAHASISWA</th>
                    <th>PRODI & TIPE</th>
                    <th style="width: 150px;" class="text-center">STATUS EMAIL</th>
                    <th style="width: 180px;" class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($theses as $thesis)
                <tr>
                    <td class="text-muted fw-bold">{{ $theses->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-800 text-dark mb-1" style="font-size: 0.9rem;">{{ $thesis->certificate_number }}</div>
                        <div class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i> 
                            {{ $thesis->certificate_date ? \Carbon\Carbon::parse($thesis->certificate_date)->format('d M Y') : $thesis->updated_at->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        <div class="fw-800 text-dark mb-1">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-muted small">NIM: {{ $thesis->user->nim ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="fw-700 text-dark mb-1">
                            <span class="text-primary">[{{ $thesis->user->department->level ?? '-' }}]</span> 
                            {{ $thesis->user->department->name ?? '-' }}
                        </div>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.65rem;">{{ strtoupper($thesis->type) }}</span>
                    </td>
                    <td class="text-center">
                        @if($thesis->delivery_status == 'sent')
                            <span class="status-badge status-sent"><i class="fas fa-check-circle me-1"></i> SENT</span>
                        @elseif($thesis->delivery_status == 'failed')
                            <span class="status-badge status-failed"><i class="fas fa-times-circle me-1"></i> FAILED</span>
                        @else
                            <span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> PENDING</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="action-btn btn-view" 
                                    onclick="previewLetter('{{ route('admin.certificates.print', $thesis->id) }}?preview=1', '{{ $thesis->certificate_number }}')" 
                                    title="Pratinjau">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editCertModal{{ $thesis->id }}" title="Edit Surat">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.certificates.resend', $thesis->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="action-btn btn-resend" title="Kirim Ulang Email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.theses.reset', $thesis->id) }}" method="POST" onsubmit="return confirm('Batalkan surat ini?')" class="d-inline">
                                @csrf
                                <button type="submit" class="action-btn btn-reset" title="Reset/Batalkan">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($theses as $thesis)
    <div class="modal fade" id="editCertModal{{ $thesis->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Edit Data Surat: {{ $thesis->user->name ?? '-' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.certificates.update', $thesis->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nomor Surat</label>
                            <input type="text" name="certificate_number" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $thesis->certificate_number }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tanggal Surat</label>
                            <input type="date" name="certificate_date" class="form-control border-0 bg-light p-3 rounded-3" value="{{ $thesis->certificate_date ?: $thesis->updated_at->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Isi Keterangan</label>
                            <textarea name="certificate_content" class="form-control border-0 bg-light p-3 rounded-3" rows="5" required>{{ $thesis->certificate_content }}</textarea>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="resend_email" id="resendCheck{{ $thesis->id }}">
                            <label class="form-check-label small fw-bold text-muted" for="resendCheck{{ $thesis->id }}">
                                Kirim ulang email ke mahasiswa setelah simpan
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm" style="background: var(--zenith-primary); border: none;">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <div class="px-5 py-4 bg-light bg-opacity-50 border-top">
        {{ $theses->links() }}
    </div>
</div>

<!-- Preview Modal (Single Dynamic Modal) -->
<div class="modal fade" id="previewLetterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0" id="modalTitle">Pratinjau Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closePreview()"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat Dokumen...</p>
                </div>
                <iframe id="previewIframe" src="" style="width: 100%; height: 700px; border: 1px solid #eee; border-radius: 12px; display: none;"></iframe>
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
