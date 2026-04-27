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
                        <label class="form-label fw-600">File Dokumen (PDF)</label>
                        <div class="upload-zone p-5 border-2 border-dashed border-light-subtle rounded-4 text-center bg-light transition-all cursor-pointer" id="drop-zone" style="position: relative; z-index: 1;">
                            <input type="file" name="file" id="file-input" class="d-none" accept=".pdf" required>
                            <div id="upload-idle">
                                <i class="fas fa-cloud-upload-alt fa-3x text-secondary opacity-25 mb-3"></i>
                                <p class="mb-0 text-secondary">Klik atau tarik file PDF ke sini</p>
                                <small class="text-muted">Maksimal 20MB</small>
                            </div>
                            <div id="upload-selected" class="d-none animate-fade-in">
                                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <h5 class="text-success fw-bold mb-1">Berhasil Dipilih!</h5>
                                <p class="mb-2 text-secondary small" id="file-name"></p>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" id="btn-cancel-file" style="font-size: 0.7rem; position: relative; z-index: 10;">
                                    <i class="fas fa-trash-alt me-1"></i> GANTI FILE
                                </button>
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
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const uploadIdle = document.getElementById('upload-idle');
    const uploadSelected = document.getElementById('upload-selected');
    const fileName = document.getElementById('file-name');
    const btnCancel = document.getElementById('btn-cancel-file');
    const uploadForm = document.getElementById('upload-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Progress Elements
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');

    // Klik untuk pilih file
    dropZone.onclick = (e) => {
        if (e.target.id !== 'btn-cancel-file' && !e.target.closest('#btn-cancel-file')) {
            fileInput.click();
        }
    };

    fileInput.onchange = (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    };

    function handleFileSelect(file) {
        if (file.type !== 'application/pdf') {
            alert('Mohon unggah file dalam format PDF!');
            fileInput.value = '';
            return;
        }
        
        if (file.size > 20 * 1024 * 1024) {
            alert('Ukuran file maksimal adalah 20MB!');
            fileInput.value = '';
            return;
        }
        
        // Tampilkan indikator sukses
        fileName.innerText = file.name + " (" + (file.size / 1024 / 1024).toFixed(2) + " MB)";
        uploadIdle.classList.add('d-none');
        uploadSelected.classList.remove('d-none');
        
        // Tambahkan efek border hijau pada zone
        dropZone.classList.add('border-success', 'bg-success', 'bg-opacity-10');
        dropZone.classList.remove('bg-light');
    }

    btnCancel.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileInput.value = '';
        uploadIdle.classList.remove('d-none');
        uploadSelected.classList.add('d-none');
        dropZone.classList.remove('border-success', 'bg-success', 'bg-opacity-10');
        dropZone.classList.add('bg-light');
    };

    // AJAX Upload with Progress
    uploadForm.onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        
        // Disable Submit Button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        
        // Show Progress Container
        progressContainer.classList.remove('d-none');
        
        // Track Progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.innerText = percent + '%';
                
                if (percent === 100) {
                    progressPercent.innerText = 'Menyimpan ke server...';
                }
            }
        });
        
        // Handle Response
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Redirect ke dashboard (pesan sukses sudah di-flash di server)
                        window.location.href = response.redirect_url;
                    } else {
                        alert('Gagal: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                        resetUploadState();
                    }
                } catch (e) {
                    // Jika server mengirimkan HTML (misal redirect standar), tetap arahkan ke dashboard
                    window.location.href = "{{ route('dashboard') }}";
                }
            } else if (xhr.status === 413) {
                alert('GAGAL: Ukuran file terlalu besar bagi server (413 Payload Too Large). \n\nSilakan tingkatkan "client_max_body_size" di Nginx aaPanel Anda.');
                resetUploadState();
            } else {
                alert('Terjadi kesalahan server (Error ' + xhr.status + '). \n\nPastikan konfigurasi PHP "upload_max_filesize" dan "post_max_size" di aaPanel sudah 50M.');
                resetUploadState();
            }
        };
        
        xhr.onerror = function() {
            alert('Koneksi terputus atau terjadi kesalahan jaringan. \n\nHal ini biasanya terjadi jika server (Nginx/PHP) menutup paksa koneksi karena file terlalu besar.');
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
