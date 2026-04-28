@extends('layouts.admin')

@section('page_title', 'Laporan & Statistik')

@section('styles')
<style>
    .val-text { font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-label { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 1rem; }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-label">Total Kunjungan</div>
            <div class="val-text">{{ number_format(\App\Models\Visit::count()) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-label">Total Disetujui</div>
            <div class="val-text">{{ number_format($statusStats['approved']) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Menunggu Review</div>
            <div class="val-text">{{ number_format($statusStats['pending']) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-label">Ditolak/Revisi</div>
            <div class="val-text">{{ number_format($statusStats['rejected']) }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Unggahan (6 Bulan)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">BULAN</th>
                            <th class="text-end pe-4">JUMLAH UNGGAHAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrends as $trend)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $trend->month }}</td>
                            <td class="text-end pe-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold">
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
    
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-pie-chart me-2 text-primary"></i>Distribusi Tipe</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">TIPE DOKUMEN</th>
                            <th class="text-end pe-4">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($typeStats as $type)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $type->type }}</td>
                            <td class="text-end pe-4 fw-bold text-primary">{{ number_format($type->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Laporan Kunjungan Bulanan</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">PERIODE BULAN</th>
                            <th class="text-center">TRAFIK KUNJUNGAN</th>
                            <th class="text-end pe-4">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitTrends as $visit)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $visit->month }}</td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 text-dark">{{ number_format($visit->total) }}</span>
                                <span class="text-muted extra-small ms-1">Views</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold">
                                    <i class="fas fa-history me-1 text-muted"></i> LOGGED
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

<div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold mb-2 text-white">Ekspor Data Koleksi</h4>
            <p class="opacity-75 mb-0 small">Dapatkan laporan lengkap seluruh koleksi skripsi dalam format Excel (.xlsx).</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.theses.export') }}" class="btn btn-white bg-white text-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-download me-2"></i> DOWNLOAD EXCEL
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Charts removed for table implementation
</script>
@endsection
