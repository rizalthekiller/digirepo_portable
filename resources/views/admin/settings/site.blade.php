@extends('layouts.admin')

@section('page_title', 'Pengaturan Situs')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-paint-brush me-2 text-primary"></i>Visual & Branding</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Logo Website</label>
                            <div class="p-3 border rounded-4 bg-light mb-3">
                                <input type="file" name="site_logo" class="form-control form-control-sm border-0 bg-white" accept="image/*">
                            </div>
                            @if($settings['site_logo_path'] && file_exists(public_path($settings['site_logo_path'])))
                                <div class="p-2 border rounded-3 bg-white text-center">
                                    <img src="{{ asset($settings['site_logo_path']) }}" style="max-height: 40px;" alt="Logo">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Favicon (Ikon Browser)</label>
                            <div class="p-3 border rounded-4 bg-light mb-3">
                                <input type="file" name="site_favicon" class="form-control form-control-sm border-0 bg-white" accept="image/x-icon,image/png">
                            </div>
                            @if($settings['site_favicon_path'] && file_exists(public_path($settings['site_favicon_path'])))
                                <div class="p-2 border rounded-3 bg-white text-center">
                                    <img src="{{ asset($settings['site_favicon_path']) }}" style="max-height: 24px;" alt="Favicon">
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Logo Watermark (Untuk PDF)</label>
                            <div class="d-flex align-items-center gap-4">
                                <div class="flex-grow-1 p-3 border rounded-4 bg-light">
                                    <input type="file" name="site_watermark" class="form-control form-control-sm border-0 bg-white" accept="image/png">
                                    <div class="form-text mt-2 extra-small">Disarankan menggunakan <b>PNG Transparan</b>.</div>
                                </div>
                                @if($settings['site_watermark_path'] && file_exists(public_path($settings['site_watermark_path'])))
                                    <div class="p-3 border rounded-3 bg-white text-center" style="background-image: radial-gradient(#ccc 1px, transparent 1px); background-size: 10px 10px;">
                                        <img src="{{ asset($settings['site_watermark_path']) }}" style="max-height: 60px;" alt="Watermark">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-home me-2 text-primary"></i>Halaman Utama (Hero)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Judul Besar (Hero Title)</label>
                            <input type="text" name="site_hero_title" class="form-control rounded-3" value="{{ $settings['site_hero_title'] ?? '' }}" placeholder="Judul yang muncul di halaman depan...">
                            <div class="form-text extra-small">Jika dikosongkan, maka akan menggunakan <b>Nama Situs</b> sebagai default.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-id-card me-2 text-primary"></i>Identitas Repositori</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Situs</label>
                            <input type="text" name="site_name" class="form-control rounded-3" value="{{ $settings['site_name'] }}" placeholder="Contoh: DigiRepo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Institusi</label>
                            <input type="text" name="site_institution" class="form-control rounded-3" value="{{ $settings['site_institution'] }}" placeholder="Contoh: Universitas">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Tagline / Slogan</label>
                            <input type="text" name="site_tagline" class="form-control rounded-3" value="{{ $settings['site_tagline'] }}" placeholder="Contoh: Sistem Repositori Digital">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Alamat Institusi</label>
                            <input type="text" name="site_address" class="form-control rounded-3" value="{{ $settings['site_address'] }}" placeholder="Alamat lengkap...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kota</label>
                            <input type="text" name="site_city" class="form-control rounded-3" value="{{ $settings['site_city'] }}" placeholder="Contoh: Padang">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Email Kontak</label>
                            <input type="email" name="site_email" class="form-control rounded-3" value="{{ $settings['site_email'] }}" placeholder="repo@univ.ac.id">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Website / Domain</label>
                            <input type="text" name="site_website" class="form-control rounded-3" value="{{ $settings['site_website'] }}" placeholder="digirepo.univ.ac.id">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Teks Footer</label>
                            <textarea name="site_footer_text" class="form-control rounded-3" rows="2">{{ $settings['site_footer_text'] }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN SITUS
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                <h6 class="fw-bold mb-4">Status Sistem</h6>
                @foreach([
                    ['icon' => 'fab fa-laravel text-danger', 'label' => 'Versi Laravel', 'value' => app()->version(), 'bg' => 'rgba(220, 38, 38, 0.05)', 'color' => '#dc2626'],
                    ['icon' => 'fas fa-code text-primary', 'label' => 'Versi PHP', 'value' => PHP_VERSION, 'bg' => 'rgba(37, 99, 235, 0.05)', 'color' => '#2563eb'],
                    ['icon' => 'fas fa-server text-warning', 'label' => 'Environment', 'value' => app()->environment(), 'bg' => 'rgba(217, 119, 6, 0.05)', 'color' => '#d97706'],
                ] as $item)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <i class="{{ $item['icon'] }}"></i>
                        <span class="small fw-bold text-muted">{{ $item['label'] }}</span>
                    </div>
                    <span class="badge rounded-pill" style="background: {{ $item['bg'] }}; color: {{ $item['color'] }}; font-size: 0.7rem;">
                        {{ $item['value'] }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-light">
                <h6 class="fw-bold mb-3">Pencadangan Data</h6>
                <p class="text-muted small mb-3">Ekspor database sistem ke file .sql untuk cadangan keamanan.</p>
                <a href="{{ route('admin.settings.backup') }}" class="btn btn-outline-success w-100 rounded-pill fw-bold btn-sm">
                    <i class="fas fa-database me-2"></i> DOWNLOAD DATABASE
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
