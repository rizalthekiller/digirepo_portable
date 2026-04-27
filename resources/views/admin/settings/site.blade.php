@extends('layouts.admin')

@section('page_title', 'Pengaturan Situs')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- Kolom Kiri: Form Identitas & Visual --}}
        <div class="col-lg-8">
            {{-- Visual Assets --}}
            <div class="zenith-card mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box-rounded bg-primary bg-opacity-10 text-primary mb-0">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h5 class="fw-zenith mb-0">Visual & Branding</h5>
                </div>
                
                <div class="row g-4">
                    {{-- Logo Website --}}
                    <div class="col-md-6">
                        <label class="info-label-premium">Logo Website</label>
                        <div class="p-3 border rounded-4 bg-light bg-opacity-50 mb-3">
                            <input type="file" name="site_logo" class="form-control form-control-sm border-0 bg-white" accept="image/*">
                        </div>
                        @if($settings['site_logo_path'] && !is_dir(public_path($settings['site_logo_path'])) && file_exists(public_path($settings['site_logo_path'])))
                            <div class="p-2 border rounded-3 bg-white text-center">
                                <img src="{{ asset($settings['site_logo_path']) }}" style="max-height: 40px;" alt="Logo">
                            </div>
                        @endif
                    </div>

                    {{-- Favicon --}}
                    <div class="col-md-6">
                        <label class="info-label-premium">Favicon (Ikon Browser)</label>
                        <div class="p-3 border rounded-4 bg-light bg-opacity-50 mb-3">
                            <input type="file" name="site_favicon" class="form-control form-control-sm border-0 bg-white" accept="image/x-icon,image/png">
                        </div>
                        @if($settings['site_favicon_path'] && !is_dir(public_path($settings['site_favicon_path'])) && file_exists(public_path($settings['site_favicon_path'])))
                            <div class="p-2 border rounded-3 bg-white text-center">
                                <img src="{{ asset($settings['site_favicon_path']) }}" style="max-height: 24px;" alt="Favicon">
                            </div>
                        @endif
                    </div>

                    {{-- Watermark Logo --}}
                    <div class="col-12">
                        <label class="info-label-premium">Logo Watermark (Untuk PDF)</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="flex-grow-1 p-3 border rounded-4 bg-light bg-opacity-50">
                                <input type="file" name="site_watermark" class="form-control form-control-sm border-0 bg-white" accept="image/png">
                                <div class="form-text mt-2 small">Sangat disarankan menggunakan <b>PNG Transparan</b>.</div>
                            </div>
                            @if($settings['site_watermark_path'] && !is_dir(public_path($settings['site_watermark_path'])) && file_exists(public_path($settings['site_watermark_path'])))
                                <div class="p-3 border rounded-3 bg-white text-center" style="background-image: radial-gradient(#ccc 1px, transparent 1px); background-size: 10px 10px;">
                                    <img src="{{ asset($settings['site_watermark_path']) }}" style="max-height: 60px;" alt="Watermark">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="zenith-card mb-4">
                <h5 class="fw-zenith mb-1">Identitas Repositori</h5>
                <p class="text-secondary small mb-4">Informasi umum yang ditampilkan di seluruh halaman situs.</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="info-label-premium">Nama Situs</label>
                        <input type="text" name="site_name" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_name'] }}" placeholder="Contoh: DigiRepo">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label-premium">Nama Institusi</label>
                        <input type="text" name="site_institution" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_institution'] }}" placeholder="Contoh: Universitas">
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Tagline / Slogan</label>
                        <input type="text" name="site_tagline" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_tagline'] }}" placeholder="Contoh: Sistem Repositori Digital Perpustakaan">
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Alamat Institusi</label>
                        <input type="text" name="site_address" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_address'] }}" placeholder="Contoh: Jl. Kampus Terpadu No. 1">
                    </div>
                    <div class="col-md-4">
                        <label class="info-label-premium">Kota</label>
                        <input type="text" name="site_city" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_city'] }}" placeholder="Contoh: Padang">
                    </div>
                    <div class="col-md-4">
                        <label class="info-label-premium">Email Kontak</label>
                        <input type="email" name="site_email" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_email'] }}" placeholder="repo@universitas.ac.id">
                    </div>
                    <div class="col-md-4">
                        <label class="info-label-premium">Website / Domain</label>
                        <input type="text" name="site_website" class="form-control border-0 bg-light p-3 rounded-3"
                               value="{{ $settings['site_website'] }}" placeholder="digirepo.universitas.ac.id">
                    </div>
                    <div class="col-12">
                        <label class="info-label-premium">Teks Footer (opsional)</label>
                        <textarea name="site_footer_text" class="form-control border-0 bg-light p-3 rounded-3" rows="3"
                                  placeholder="© 2026 UPT Perpustakaan.">{{ $settings['site_footer_text'] }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg"
                        style="background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary)); border: none;">
                    <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN SITUS
                </button>
            </div>
        </div>

        {{-- Kolom Kanan: Status Sistem --}}
        <div class="col-lg-4">
            <div class="zenith-card mb-4">
                <h6 class="fw-zenith mb-4">Status Sistem</h6>

                @php
                    $system = [
                        ['icon' => 'fab fa-laravel text-danger', 'label' => 'Versi Laravel', 'value' => app()->version(), 'bg' => '#fef2f2', 'color' => '#dc2626'],
                        ['icon' => 'fas fa-code text-primary', 'label' => 'Versi PHP', 'value' => PHP_VERSION, 'bg' => '#eff6ff', 'color' => '#2563eb'],
                        ['icon' => 'fas fa-server text-warning', 'label' => 'Environment', 'value' => app()->environment(), 'bg' => '#fffbeb', 'color' => '#d97706'],
                    ];
                @endphp

                @foreach($system as $item)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="{{ $item['icon'] }}"></i>
                        <span class="small fw-bold text-secondary">{{ $item['label'] }}</span>
                    </div>
                    <span class="badge rounded-pill" style="background: {{ $item['bg'] }}; color: {{ $item['color'] }}; font-size: 0.75rem;">
                        {{ $item['value'] }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="zenith-card mb-4">
                <h6 class="fw-zenith mb-3">Informasi Visual</h6>
                <div class="alert alert-info border-0 rounded-4 p-3 small mb-0">
                    <p class="mb-2"><strong>Logo Website:</strong> Muncul di sidebar & login.</p>
                    <p class="mb-2"><strong>Favicon:</strong> Muncul di tab browser (rekomendasi .ico atau .png kecil).</p>
                    <p class="mb-0"><strong>Watermark:</strong> Muncul di tengah halaman PDF skripsi yang disetujui.</p>
                </div>
            </div>

            <div class="zenith-card mb-4">
                <h6 class="fw-zenith mb-2">Manajemen Antrean</h6>
                <p class="text-secondary small mb-3">Gunakan tombol ini jika Anda baru saja mengubah kode aplikasi agar pekerja (worker) memuat ulang perubahannya.</p>
                <form action="{{ route('admin.settings.queue_restart') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin merestart antrean? Proses yang sedang berjalan tidak akan terhenti, namun pekerja akan memuat ulang kode setelah selesai.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning w-100 rounded-pill fw-bold">
                        <i class="fas fa-sync-alt me-2"></i> RESTART QUEUE
                    </button>
                </form>
            </div>

            <div class="zenith-card">
                <h6 class="fw-zenith mb-2">Pencadangan Data</h6>
                <p class="text-secondary small mb-3">Ekspor seluruh database sistem ke dalam file .sql untuk cadangan keamanan.</p>
                <a href="{{ route('admin.settings.backup') }}" class="btn btn-outline-success w-100 rounded-pill fw-bold">
                    <i class="fas fa-database me-2"></i> DOWNLOAD DATABASE (.SQL)
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
