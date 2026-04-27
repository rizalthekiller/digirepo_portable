@extends('layouts.admin')

@section('page_title', 'Unggah Karya Ilmiah')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="zenith-card animate-fade-in">
            <div class="d-flex align-items-center mb-5">
                <div class="avatar-circle" style="width: 60px; height: 60px; background: rgba(79, 70, 229, 0.1); color: var(--zenith-primary); border-radius: 20px;">
                    <i class="fas fa-file-upload fa-lg"></i>
                </div>
                <div class="ms-4">
                    <h4 class="fw-zenith mb-1">{{ isset($existingThesis) ? 'Revisi Karya Ilmiah' : 'Unggah Karya Ilmiah Baru' }}</h4>
                    <p class="text-muted small mb-0">Pastikan dokumen dalam format PDF dan sesuai dengan template institusi.</p>
                </div>
            </div>

                <form action="{{ route('theses.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-600">Judul Lengkap</label>
                        <input type="text" name="title" class="form-control form-control-lg border-0 bg-light rounded-4 p-3" value="{{ old('title', $existingThesis->title ?? '') }}" placeholder="Masukkan judul skripsi/thesis Anda" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Tipe Dokumen</label>
                            <select name="type" class="form-select border-0 bg-light rounded-4 p-3" required>
                                <option value="Skripsi" {{ (old('type', $existingThesis->type ?? '') == 'Skripsi') ? 'selected' : '' }}>Skripsi (S1)</option>
                                <option value="Thesis" {{ (old('type', $existingThesis->type ?? '') == 'Thesis') ? 'selected' : '' }}>Thesis (S2)</option>
                                <option value="Disertasi" {{ (old('type', $existingThesis->type ?? '') == 'Disertasi') ? 'selected' : '' }}>Disertasi (S3)</option>
                                @if(auth()->user()->isDosen())
                                    <option value="Jurnal" {{ (old('type', $existingThesis->type ?? '') == 'Jurnal') ? 'selected' : '' }}>Jurnal Ilmiah</option>
                                    <option value="Buku" {{ (old('type', $existingThesis->type ?? '') == 'Buku') ? 'selected' : '' }}>Buku/E-Book</option>
                                    <option value="Artikel" {{ (old('type', $existingThesis->type ?? '') == 'Artikel') ? 'selected' : '' }}>Artikel Populer</option>
                                    <option value="Lainnya" {{ (old('type', $existingThesis->type ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Tahun Lulus</label>
                            <input type="number" name="year" class="form-control border-0 bg-light rounded-4 p-3" value="{{ old('year', $existingThesis->year ?? date('Y')) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Dosen Pembimbing</label>
                        <input type="text" name="supervisor_name" class="form-control border-0 bg-light rounded-4 p-3" value="{{ old('supervisor_name', $existingThesis->supervisor_name ?? '') }}" placeholder="Nama lengkap tanpa gelar" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Kata Kunci (Keywords)</label>
                        <input type="text" name="keywords" class="form-control border-0 bg-light rounded-4 p-3" value="{{ old('keywords', $existingThesis->keywords ?? '') }}" placeholder="Contoh: Optimasi, PHP, Laravel" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Abstrak</label>
                        <textarea name="abstract" class="form-control border-0 bg-light rounded-4 p-3" rows="6" placeholder="Tuliskan abstrak di sini..." required>{{ old('abstract', $existingThesis->abstract ?? '') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-600 d-flex justify-content-between align-items-center">
                            Berkas Dokumen (PDF)
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" id="add-file-row">
                                <i class="fas fa-plus me-1"></i> TAMBAH BAGIAN BERKAS
                            </button>
                        </label>
                        <div id="files-container">
                            <div class="file-row mb-3 animate-fade-in">
                                <div class="p-4 rounded-4 border bg-light shadow-sm">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-5">
                                            <label class="small text-muted mb-2 fw-bold">Label Berkas</label>
                                            <input type="text" name="file_labels[]" class="form-control border-0 bg-white rounded-3 p-3" value="Full Text" placeholder="Contoh: Bab 1 / Full Text" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small text-muted mb-2 fw-bold">Pilih PDF (Maks 20MB)</label>
                                            <input type="file" name="files[]" class="form-control border-0 bg-white rounded-3 p-3" accept=".pdf" required>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <label class="small text-muted mb-2 d-block">&nbsp;</label>
                                            <button type="button" class="btn btn-link text-danger remove-row d-none" title="Hapus Baris">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary border-0 rounded-4 mt-3 small p-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle fs-5"></i>
                                <span>Tips: Anda dapat mengunggah file utuh (Full Text) saja, atau membaginya per bab.</span>
                            </div>
                        </div>

                        <!-- Progress Bar (Hidden by default) -->
                        <div id="progress-container" class="mt-4 d-none">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold text-primary">Mengunggah Dokumen...</span>
                                <span class="small fw-bold text-primary" id="progress-percent">0%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 12px; background: #e2e8f0;">
                                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated rounded-pill" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #4f46e5, #8b5cf6);"></div>
                            </div>
                            <p class="extra-small text-muted mt-2 text-center">Mohon jangan tutup halaman ini sampai proses selesai.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 shadow-lg" id="submit-btn">
                        <i class="fas fa-paper-plane me-2"></i> Ajukan Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const filesContainer = document.getElementById('files-container');
    const addFileRowBtn = document.getElementById('add-file-row');
    const uploadForm = document.getElementById('upload-form');
    const submitBtn = document.getElementById('submit-btn');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');

    // Tambah Baris Baru
    addFileRowBtn.addEventListener('click', () => {
        const rowCount = filesContainer.querySelectorAll('.file-row').length;
        const newRow = document.createElement('div');
        newRow.className = 'file-row mb-3 animate-fade-in';
        newRow.innerHTML = `
            <div class="p-4 rounded-4 border bg-light shadow-sm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label class="small text-muted mb-2 fw-bold">Label Berkas</label>
                        <input type="text" name="file_labels[]" class="form-control border-0 bg-white rounded-3 p-3" placeholder="Contoh: Bab ${rowCount + 1}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-2 fw-bold">Pilih PDF (Maks 20MB)</label>
                        <input type="file" name="files[]" class="form-control border-0 bg-white rounded-3 p-3" accept=".pdf" required>
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="small text-muted mb-2 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-link text-danger remove-row" title="Hapus Baris">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        filesContainer.appendChild(newRow);
        updateRemoveButtons();
    });

    // Hapus Baris
    filesContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.file-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = filesContainer.querySelectorAll('.file-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-row');
            if (rows.length === 1) {
                removeBtn.classList.add('d-none');
            } else {
                removeBtn.classList.remove('d-none');
            }
        });
    }

    // AJAX Upload with Progress
    uploadForm.onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        
        // Validation: Check file sizes before sending
        const fileInputs = this.querySelectorAll('input[type="file"]');
        for (let input of fileInputs) {
            if (input.files.length > 0) {
                if (input.files[0].size > 20 * 1024 * 1024) {
                    alert('Salah satu file melebihi batas 20MB!');
                    return;
                }
            }
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        progressContainer.classList.remove('d-none');
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.innerText = percent + '%';
                
                if (percent === 100) {
                    progressPercent.innerText = 'Menyimpan berkas di server...';
                }
            }
        });
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                        resetUploadState();
                    }
                } catch (e) {
                    window.location.href = "{{ route('dashboard') }}";
                }
            } else {
                alert('Terjadi kesalahan server (Error ' + xhr.status + ').');
                resetUploadState();
            }
        };
        
        xhr.onerror = function() {
            alert('Koneksi terputus.');
            resetUploadState();
        };

        function resetUploadState() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Ajukan Verifikasi';
            progressContainer.classList.add('d-none');
        }
        
        xhr.open('POST', this.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    };
</script>
@endsection
