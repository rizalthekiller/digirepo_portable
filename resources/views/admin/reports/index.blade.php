@extends('layouts.admin')

@section('page_title', 'Laporan & Statistik')

@section('styles')
<style>
    .stat-card-premium {
        background: white;
        border-radius: 25px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        height: 100%;
    }
    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }
    .icon-box-rounded {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .export-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        border-radius: 25px;
        padding: 40px;
        position: relative;
        overflow: hidden;
    }
    .export-card::after {
        content: '\f1c3';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.05;
        transform: rotate(-15deg);
    }
</style>
@endsection

@section('content')
<!-- Stats Overview -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card-premium animate-fade-in" style="animation-delay: 0.1s;">
            <div class="icon-box-rounded bg-info bg-opacity-10 text-info">
                <i class="fas fa-eye"></i>
            </div>
            <div class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Total Kunjungan</div>
            <h2 class="fw-800 mb-0">{{ number_format(\App\Models\Visit::count()) }}</h2>
            <div class="mt-3 small text-info">
                <i class="fas fa-chart-line me-1"></i> <span class="fw-bold">Seluruh Waktu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-premium animate-fade-in" style="animation-delay: 0.1s;">
            <div class="icon-box-rounded bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Total Disetujui</div>
            <h2 class="fw-800 mb-0">{{ number_format($statusStats['approved']) }}</h2>
            <div class="mt-3 small text-success">
                <i class="fas fa-arrow-up me-1"></i> <span class="fw-bold">Dokumen Valid</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-premium animate-fade-in" style="animation-delay: 0.2s;">
            <div class="icon-box-rounded bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Menunggu Review</div>
            <h2 class="fw-800 mb-0">{{ number_format($statusStats['pending']) }}</h2>
            <div class="mt-3 small text-warning">
                <i class="fas fa-hourglass-half me-1"></i> <span class="fw-bold">Perlu Validasi</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-premium animate-fade-in" style="animation-delay: 0.3s;">
            <div class="icon-box-rounded bg-danger bg-opacity-10 text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Ditolak/Revisi</div>
            <h2 class="fw-800 mb-0">{{ number_format($statusStats['rejected']) }}</h2>
            <div class="mt-3 small text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i> <span class="fw-bold">Data Bermasalah</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Monthly Trends Table -->
    <div class="col-lg-7">
        <div class="zenith-card animate-fade-in" style="animation-delay: 0.4s;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-800 mb-1">Tren Unggahan</h5>
                    <p class="text-muted small mb-0">Statistik pengiriman dokumen 6 bulan terakhir</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small fw-800 text-muted px-4 py-3">BULAN</th>
                            <th class="border-0 small fw-800 text-muted px-4 py-3 text-end">JUMLAH UNGGAHAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrends as $trend)
                        <tr>
                            <td class="px-4 py-3 fw-700 text-dark">{{ $trend->month }}</td>
                            <td class="px-4 py-3 text-end">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-800">
                                    {{ $trend->total }} Dokumen
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Type Distribution Table -->
    <div class="col-lg-5">
        <div class="zenith-card animate-fade-in" style="animation-delay: 0.5s;">
            <h5 class="fw-800 mb-4">Distribusi Tipe</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small fw-800 text-muted px-4 py-3">TIPE DOKUMEN</th>
                            <th class="border-0 small fw-800 text-muted px-4 py-3 text-end">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($typeStats as $type)
                        <tr>
                            <td class="px-4 py-3 fw-700 text-dark">{{ $type->type }}</td>
                            <td class="px-4 py-3 text-end fw-800 text-primary">{{ number_format($type->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Visit Trends Table -->
    <div class="col-lg-12">
        <div class="zenith-card animate-fade-in" style="animation-delay: 0.6s;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-800 mb-1">Laporan Kunjungan Bulanan</h5>
                    <p class="text-muted small mb-0">Tren trafik pengunjung website repositori (Mulai dari 0 setiap bulan)</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small fw-800 text-muted px-4 py-3">PERIODE BULAN</th>
                            <th class="border-0 small fw-800 text-muted px-4 py-3 text-center">TRAFIK KUNJUNGAN</th>
                            <th class="border-0 small fw-800 text-muted px-4 py-3 text-end">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitTrends as $visit)
                        <tr>
                            <td class="px-4 py-3 fw-700 text-dark">{{ $visit->month }}</td>
                            <td class="px-4 py-3 text-center">
                                <h5 class="mb-0 fw-800">{{ number_format($visit->total) }} <small class="text-muted" style="font-size: 0.6rem;">Kunjungan</small></h5>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-800">
                                    <i class="fas fa-chart-bar me-1"></i> Terhitung
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Export Section -->
<div class="export-card animate-fade-in" style="animation-delay: 0.6s;">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h3 class="fw-800 mb-3">Ekspor Data Koleksi</h3>
            <p class="opacity-75 mb-4">Dapatkan laporan lengkap seluruh koleksi skripsi dalam format Excel untuk keperluan arsip dan pelaporan institusi.</p>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.theses.export') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-800 shadow-lg">
                    <i class="fas fa-download me-2"></i> DOWNLOAD EXCEL (.xlsx)
                </a>
            </div>
        </div>
        <div class="col-md-5 d-none d-md-block text-center">
            <div class="p-4 bg-white bg-opacity-10 rounded-circle d-inline-block">
                <i class="fas fa-file-invoice-dollar fa-5x"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Charts removed for table implementation
</script>
@endsection
