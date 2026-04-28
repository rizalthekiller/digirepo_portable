@extends('layouts.admin')

@section('page_title', 'Pengaturan Surat')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.certificates.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-image me-2 text-primary"></i>Visual Kop Surat</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Unggah gambar Kop Surat yang akan tampil di bagian atas sertifikat.</p>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">File Gambar Kop Surat</label>
                        <div class="p-4 rounded-4 border-2 border-dashed bg-light bg-opacity-50 text-center">
                            <input type="file" name="cert_logo" class="form-control border-0 bg-white p-2 rounded-3 shadow-sm mx-auto" style="max-width: 400px;" accept="image/*">
                            <div class="form-text mt-2 extra-small">Format: <b>PNG</b> atau <b>JPG</b> (Lebar min. 800px).</div>
                        </div>
                    </div>

                    @if($settings['cert_logo_path'] && file_exists(public_path($settings['cert_logo_path'])))
                    <div class="p-4 rounded-4 bg-white border">
                        <div class="text-muted extra-small fw-bold text-uppercase mb-2">Preview Kop Saat Ini:</div>
                        <img src="{{ asset($settings['cert_logo_path']) }}?v={{ time() }}" class="img-fluid rounded-3 border" style="max-height: 100px;">
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2 text-primary"></i>Format Nomor & Konten</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Format Nomor Surat</label>
                            <input type="text" name="cert_number_format" class="form-control rounded-3 font-monospace" value="{{ $settings['cert_number_format'] }}">
                            <div class="form-text extra-small">Gunakan: <code>{ID}</code>, <code>{YEAR}</code>, <code>{ROMAN}</code>, <code>{MONTH}</code>.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Paragraf Pembuka</label>
                            <textarea name="cert_opening_text" class="form-control rounded-3" rows="2">{{ $settings['cert_opening_text'] }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Paragraf Isi Utama</label>
                            <textarea name="cert_main_content" class="form-control rounded-3" rows="3">{{ $settings['cert_main_content'] }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Paragraf Penutup</label>
                            <textarea name="cert_closing_content" class="form-control rounded-3" rows="2">{{ $settings['cert_closing_content'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-signature me-2 text-primary"></i>Penandatangan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Prefix</label>
                            <input type="text" name="cert_signatory_prefix" class="form-control rounded-3" value="{{ $settings['cert_signatory_prefix'] }}" placeholder="Plt.">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">Jabatan</label>
                            <input type="text" name="cert_signatory_title" class="form-control rounded-3" value="{{ $settings['cert_signatory_title'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama</label>
                            <input type="text" name="cert_signatory_name" class="form-control rounded-3" value="{{ $settings['cert_signatory_name'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIP</label>
                            <input type="text" name="cert_signatory_nip" class="form-control rounded-3" value="{{ $settings['cert_signatory_nip'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Kota Penerbitan</label>
                            <input type="text" name="cert_issued_city" class="form-control rounded-3" value="{{ $settings['cert_issued_city'] }}">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> SIMPAN PENGATURAN SURAT
            </button>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
            <h6 class="fw-bold mb-3">Informasi Variabel</h6>
            <div class="d-flex flex-column gap-2">
                @foreach(['{ID}' => 'Nomor urut', '{YEAR}' => 'Tahun aktif', '{MONTH}' => 'Bulan angka', '{ROMAN}' => 'Bulan romawi'] as $var => $desc)
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light border">
                    <code class="text-primary fw-bold small">{{ $var }}</code>
                    <span class="text-muted extra-small">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
            <h6 class="fw-bold mb-3">Panduan Kop</h6>
            <p class="text-muted extra-small mb-0">Kop surat harus mencakup Logo, Nama Instansi, dan Alamat lengkap karena sistem tidak merender teks manual di header.</p>
        </div>
    </div>
</div>
@endsection
