@extends('layouts.admin')

@section('page_title', Auth::user()->isDosen() ? 'Unggah Karya Tulis' : 'Unggah Karya Ilmiah')

@section('styles')
<style>
    .upload-box { border: 2px dashed #e2e8f0; border-radius: 16px; transition: all 0.3s; background: #f8fafc; }
    .upload-box:hover { border-color: var(--primary-color); background: #f0f9ff; }
    .form-label-premium { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .card-stepper { border-left: 4px solid var(--primary-color); }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header Info -->
        <div class="mb-4 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-cloud-arrow-up fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">{{ isset($existingThesis) ? 'Revisi Karya' : 'Unggah Karya Ilmiah Baru' }}</h4>
                <p class="text-muted small mb-0">Lengkapi formulir di bawah untuk mengajukan verifikasi karya tulis Anda.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-stepper">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('theses.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label class="form-label-premium" id="title-label">Judul Lengkap Karya</label>
                            <textarea name="title" id="title-input" class="form-control border bg-light rounded-3 p-3 fw-bold text-dark" rows="2" placeholder="Masukkan judul lengkap sesuai dokumen..." required>{{ old('title', $existingThesis->title ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-premium">Tipe Dokumen</label>
                            <select name="type" id="type-select" class="form-select border bg-light rounded-3 p-3 fw-semibold" required>
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
                            <label class="form-label-premium" id="year-label">Tahun Kelulusan / Publikasi</label>
                            <input type="number" name="year" class="form-control border bg-light rounded-3 p-3 fw-semibold" value="{{ old('year', $existingThesis->year ?? date('Y')) }}" required>
                        </div>
                    </div>

                    <!-- Academic specific fields -->
                    <div id="academic-fields" class="mb-4">
                        <label class="form-label-premium">Dosen Pembimbing Utama</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-user-tie text-muted"></i></span>
                            <input type="text" name="supervisor_name" class="form-control border bg-light border-start-0 rounded-end-3 p-3" value="{{ old('supervisor_name', $existingThesis->supervisor_name ?? '') }}" placeholder="Nama lengkap pembimbing">
                        </div>
                    </div>

                    <!-- Journal fields -->
                    <div id="journal-fields" class="d-none mb-4">
                        <div class="mb-4">
                            <label class="form-label-premium">Nama Jurnal / Prosiding</label>
                            <input type="text" name="journal_name" class="form-control border bg-light rounded-3 p-3" value="{{ old('journal_name', $existingThesis->journal_name ?? '') }}" placeholder="Contoh: Jurnal Nasional Informatika">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-premium">Volume</label>
                                <input type="text" name="volume" class="form-control border bg-light rounded-3 p-3" value="{{ old('volume', $existingThesis->volume ?? '') }}" placeholder="Vol. X">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-premium">Nomor / Issue</label>
                                <input type="text" name="issue" class="form-control border bg-light rounded-3 p-3" value="{{ old('issue', $existingThesis->issue ?? '') }}" placeholder="No. Y">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-premium">Halaman</label>
                                <input type="text" name="pages" class="form-control border bg-light rounded-3 p-3" value="{{ old('pages', $existingThesis->pages ?? '') }}" placeholder="12-34">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">Kata Kunci (Separated by comma)</label>
                        <input type="text" name="keywords" class="form-control border bg-light rounded-3 p-3" value="{{ old('keywords', $existingThesis->keywords ?? '') }}" placeholder="Contoh: AI, Web Development, Laravel" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium" id="abstract-label">Abstrak Dokumen</label>
                        <textarea name="abstract" class="form-control border bg-light rounded-3 p-3" rows="6" placeholder="Masukkan abstrak dalam bahasa Indonesia..." required>{{ old('abstract', $existingThesis->abstract ?? '') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label-premium mb-0">Berkas Pendukung (PDF)</label>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" id="add-file-row">
                                <i class="fas fa-plus me-1"></i> Tambah Bagian
                            </button>
                        </div>
                        
                        <div id="files-container">
                            <div class="file-row mb-3">
                                <div class="p-3 upload-box">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-5">
                                            <label class="extra-small fw-bold text-muted mb-2">Label Bagian</label>
                                            <input type="text" name="file_labels[]" class="form-control border-0 bg-white rounded-3 small py-2" value="Full Text" placeholder="Contoh: Bab 1 / Pendahuluan" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="extra-small fw-bold text-muted mb-2">Pilih File PDF (Max 20MB)</label>
                                            <input type="file" name="files[]" class="form-control border-0 bg-white rounded-3 small py-2" accept=".pdf" required>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-link text-danger remove-row d-none p-0 mb-2">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="progress-container" class="mt-4 d-none">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold text-primary">Mengunggah...</span>
                                <span class="small fw-bold text-primary" id="progress-percent">0%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px; background: #e2e8f0;">
                                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated rounded-pill" role="progressbar" style="width: 0%; background: var(--primary-color);"></div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 rounded-4 p-4 mb-5 shadow-sm d-flex align-items-start gap-3">
                        <i class="fas fa-circle-info fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Ketentuan Verifikasi</h6>
                            <p class="small mb-0 opacity-75">Pastikan seluruh data yang diinput benar. Pengajuan yang sudah dikirim tidak dapat diubah kecuali ditolak oleh admin untuk revisi.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg" id="submit-btn">
                        <i class="fas fa-paper-plane me-2"></i> AJUKAN UNTUK VERIFIKASI
                    </button>
                </form>
            </div>
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

    addFileRowBtn.addEventListener('click', () => {
        const rowCount = filesContainer.querySelectorAll('.file-row').length;
        const newRow = document.createElement('div');
        newRow.className = 'file-row mb-3';
        newRow.innerHTML = `
            <div class="p-3 upload-box">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="extra-small fw-bold text-muted mb-2">Label Bagian</label>
                        <input type="text" name="file_labels[]" class="form-control border-0 bg-white rounded-3 small py-2" placeholder="Contoh: Bab ${rowCount + 1}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="extra-small fw-bold text-muted mb-2">Pilih File PDF (Max 20MB)</label>
                        <input type="file" name="files[]" class="form-control border-0 bg-white rounded-3 small py-2" accept=".pdf" required>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-link text-danger remove-row p-0 mb-2">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        filesContainer.appendChild(newRow);
        updateRemoveButtons();
    });

    filesContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.file-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = filesContainer.querySelectorAll('.file-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-row');
            if (rows.length === 1) {
                removeBtn.classList.add('d-none');
            } else {
                removeBtn.classList.remove('d-none');
            }
        });
    }

    uploadForm.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> MENGUNGGAH...';
        progressContainer.classList.remove('d-none');
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.innerText = percent + '%';
            }
        });
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert('Error: ' + response.message);
                        resetState();
                    }
                } catch (e) {
                    window.location.href = "{{ route('dashboard') }}";
                }
            } else {
                alert('Server error.');
                resetState();
            }
        };
        
        function resetState() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> AJUKAN UNTUK VERIFIKASI';
            progressContainer.classList.add('d-none');
        }
        
        xhr.open('POST', this.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    };

    const typeSelect = document.getElementById('type-select');
    const academicFields = document.getElementById('academic-fields');
    const journalFields = document.getElementById('journal-fields');

    typeSelect.addEventListener('change', function() {
        if (['Skripsi', 'Thesis', 'Disertasi'].includes(this.value)) {
            academicFields.classList.remove('d-none');
            journalFields.classList.add('d-none');
        } else if (['Jurnal', 'Artikel'].includes(this.value)) {
            academicFields.classList.add('d-none');
            journalFields.classList.remove('d-none');
        } else {
            academicFields.classList.add('d-none');
            journalFields.classList.add('d-none');
        }
    });
</script>
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
    const typeSelect = document.getElementById('type-select');
    const academicFields = document.getElementById('academic-fields');
    const journalFields = document.getElementById('journal-fields');
    const bookFields = document.getElementById('book-fields');
    
    // Elements for Dynamic Labels/Placeholders
    const titleLabel = document.getElementById('title-label');
    const titleInput = document.getElementById('title-input');
    const yearLabel = document.getElementById('year-label');
    const abstractLabel = document.getElementById('abstract-label');

    function toggleFields() {
        const type = typeSelect.value;
        
        // Sembunyikan semua dulu
        academicFields.classList.add('d-none');
        journalFields.classList.add('d-none');
        bookFields.classList.add('d-none');
        
        if (['Skripsi', 'Thesis', 'Disertasi'].includes(type)) {
            academicFields.classList.remove('d-none');
            titleLabel.innerText = "Judul Lengkap " + type;
            titleInput.placeholder = "Masukkan judul " + type.toLowerCase() + " Anda...";
            yearLabel.innerText = "Tahun Lulus / Yudisium";
            abstractLabel.innerText = "Abstrak";
        } else if (['Jurnal', 'Artikel'].includes(type)) {
            journalFields.classList.remove('d-none');
            titleLabel.innerText = "Judul " + type;
            titleInput.placeholder = "Masukkan judul " + type.toLowerCase() + " Anda...";
            yearLabel.innerText = "Tahun Terbit / Publikasi";
            abstractLabel.innerText = "Abstrak";
        } else if (['Buku'].includes(type)) {
            bookFields.classList.remove('d-none');
            titleLabel.innerText = "Judul Buku";
            titleInput.placeholder = "Masukkan judul buku lengkap Anda...";
            yearLabel.innerText = "Tahun Terbit";
            abstractLabel.innerText = "Sinopsis Buku";
        } else {
            titleLabel.innerText = "Judul Lengkap";
            titleInput.placeholder = "Masukkan judul dokumen Anda...";
            yearLabel.innerText = "Tahun";
            abstractLabel.innerText = "Abstrak / Deskripsi";
        }
    }

    typeSelect.addEventListener('change', toggleFields);
    
    // Jalankan sekali saat halaman dimuat (untuk mode edit)
    toggleFields();
</script>
@endsection
