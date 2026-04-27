@extends('layouts.admin')

@section('page_title', 'Pengaturan Surat')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.certificates.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Kop Surat Upload --}}
            <div class="zenith-card mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box-rounded bg-primary bg-opacity-10 text-primary mb-0">
                        <i class="fas fa-image"></i>
                    </div>
                    <h5 class="fw-zenith mb-0">Visual Kop Surat</h5>
                </div>
                
                <p class="text-secondary small mb-4">Unggah gambar Kop Surat yang akan tampil di bagian paling atas sertifikat.</p>
                
                <div class="mb-4">
                    <label class="info-label-premium">File Gambar Kop Surat</label>
                    <div class="d-flex align-items-center gap-4 p-4 rounded-4 border-2 border-dashed bg-light bg-opacity-50">
                        <div class="flex-grow-1">
                            <input type="file" name="cert_logo" class="form-control border-0 bg-white p-2 rounded-3 shadow-sm" accept="image/*">
                            <div class="form-text mt-2 small">Format yang disarankan: <b>PNG</b> atau <b>JPG</b> dengan lebar minimal 800px.</div>
                        </div>
                    </div>
                </div>

                @if($settings['cert_logo_path'] && !is_dir(public_path($settings['cert_logo_path'])) && file_exists(public_path($settings['cert_logo_path'])))
                <div class="p-4 rounded-4 bg-white border shadow-sm">
                    <div class="text-muted small fw-bold text-uppercase mb-3">Preview Kop Saat Ini:</div>
                    <img src="{{ asset($settings['cert_logo_path']) }}?v={{ time() }}" class="img-fluid rounded-3 border" style="max-height: 120px;">
                    <div class="mt-2 text-success small">
                        <i class="fas fa-check-circle me-1"></i> File aktif: <code>{{ $settings['cert_logo_path'] }}</code>
                    </div>
                </div>
                @else
                <div class="p-5 text-center rounded-4 border-2 border-dashed bg-light text-muted">
                    <i class="fas fa-file-image fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0 small fw-bold">Belum ada Kop Surat yang diunggah.</p>
                </div>
                @endif
            </div>

            {{-- Format Nomor & Konten --}}
            <div class="zenith-card mb-4">
                <h5 class="fw-zenith mb-1">Format Nomor & Konten</h5>
                <p class="text-secondary small mb-4">Teks yang tampil di badan surat keterangan.</p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="info-label-premium">Format Nomor Surat</label>
                        <input type="text" name="cert_number_format" class="form-control border-0 bg-light p-3 rounded-3 font-monospace"
                               value="{{ $settings['cert_number_format'] }}">
                        <div class="form-text ms-1">Gunakan: <code>{ID}</code> nomor urut, <code>{YEAR}</code> tahun, <code>{ROMAN}</code> bulan romawi, <code>{MONTH}</code> angka bulan.</div>
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Paragraf Pembuka</label>
                        <textarea name="cert_opening_text" class="form-control border-0 bg-light p-3 rounded-3" rows="3">{{ $settings['cert_opening_text'] }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Paragraf Isi Utama</label>
                        <textarea name="cert_main_content" class="form-control border-0 bg-light p-3 rounded-3" rows="3">{{ $settings['cert_main_content'] }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Paragraf Penutup</label>
                        <textarea name="cert_closing_content" class="form-control border-0 bg-light p-3 rounded-3" rows="3">{{ $settings['cert_closing_content'] }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Penandatangan --}}
            <div class="zenith-card mb-4">
                <h5 class="fw-zenith mb-1">Penandatangan</h5>
                <p class="text-secondary small mb-4">Data yang tampil di bagian tanda tangan surat.</p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="info-label-premium">Prefix <span class="text-muted fw-normal">(Plt./Dr./dll)</span></label>
                        <input type="text" name="cert_signatory_prefix" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['cert_signatory_prefix'] }}" placeholder="Plt.">
                    </div>
                    <div class="col-md-9">
                        <label class="info-label-premium">Jabatan Penandatangan</label>
                        <input type="text" name="cert_signatory_title" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['cert_signatory_title'] }}" placeholder="Kepala UPT. Perpustakaan">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label-premium">Nama Penandatangan</label>
                        <input type="text" name="cert_signatory_name" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['cert_signatory_name'] }}" placeholder="Nama lengkap + gelar">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label-premium">NIP</label>
                        <input type="text" name="cert_signatory_nip" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['cert_signatory_nip'] }}" placeholder="19800101 200501 1 001">
                    </div>
                    <div class="col-md-12">
                        <label class="info-label-premium">Kota Penerbitan</label>
                        <input type="text" name="cert_issued_city" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['cert_issued_city'] }}"
                               placeholder="Contoh: Samarinda">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg"
                    style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">
                <i class="fas fa-save me-2"></i> SIMPAN PENGATURAN SURAT
            </button>
        </form>
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4">
        <div class="zenith-card mb-4">
            <h6 class="fw-zenith mb-3">Informasi Penting</h6>
            <p class="small text-muted mb-3">Kop surat yang diunggah harus mencakup Logo, Nama Instansi, dan Alamat lengkap karena sistem tidak lagi merender teks manual untuk bagian header.</p>
            <div class="alert alert-info border-0 rounded-4 p-3 small mb-0">
                <i class="fas fa-info-circle me-2"></i> Gunakan gambar dengan latar belakang transparan (PNG) untuk hasil terbaik.
            </div>
        </div>

        <div class="zenith-card mb-4">
            <h6 class="fw-zenith mb-3">Variabel Nomor</h6>
            <div class="d-flex flex-column gap-2">
                @foreach([
                    '{ID}' => 'Nomor urut',
                    '{YEAR}' => 'Tahun aktif',
                    '{MONTH}' => 'Bulan angka',
                    '{ROMAN}' => 'Bulan romawi',
                ] as $var => $desc)
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light">
                    <code class="text-primary fw-bold" style="font-size: 0.78rem; white-space: nowrap;">{{ $var }}</code>
                    <span class="text-secondary" style="font-size: 0.78rem;">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="zenith-card">
            <h6 class="fw-zenith mb-3">Aksi Cepat</h6>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-light rounded-3 text-start fw-bold small d-flex align-items-center gap-2">
                    <i class="fas fa-file-signature text-primary"></i> Data Surat
                </a>
                <a href="{{ route('admin.settings') }}" class="btn btn-light rounded-3 text-start fw-bold small d-flex align-items-center gap-2">
                    <i class="fas fa-cog text-primary"></i> Pengaturan Situs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
