@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('styles')
<style>
    .stat-card { border: none; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    @foreach([
        ['label' => 'TOTAL SKRIPSI', 'val' => $totalTheses ?? 0, 'icon' => 'fas fa-book-open', 'color' => 'primary'],
        ['label' => 'MENUNGGU VERIFIKASI', 'val' => $pendingTheses ?? 0, 'icon' => 'fas fa-clock', 'color' => 'warning'],
        ['label' => 'MAHASISWA', 'val' => $totalUsers ?? 0, 'icon' => 'fas fa-users', 'color' => 'success'],
        ['label' => 'FAKULTAS', 'val' => $totalFaculties ?? 0, 'icon' => 'fas fa-university', 'color' => 'info']
    ] as $stat)
    <div class="col-md-3">
        <div class="card stat-card shadow-sm rounded-4 h-100 border-0">
            <div class="card-body p-4">
                <div class="icon-box bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} mb-3">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
                <h6 class="text-muted mb-1 extra-small fw-bold">{{ $stat['label'] }}</h6>
                <h3 class="fw-bold mb-0">{{ number_format($stat['val']) }}</h3>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 px-4">
                <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktifitas Terakhir</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 50%;">JUDUL</th>
                            <th>PENULIS</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-end pe-4">WAKTU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTheses as $thesis)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-1 small" style="line-height: 1.4;">{{ $thesis->title }}</div>
                                <div class="text-muted extra-small">{{ $thesis->type }} - {{ $thesis->year }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-muted extra-small">{{ $thesis->user->nim ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = $thesis->status === 'approved' ? 'success' : ($thesis->status === 'rejected' ? 'danger' : 'warning');
                                    $statusText = $thesis->status === 'approved' ? 'Disetujui' : ($thesis->status === 'rejected' ? 'Ditolak' : 'Pending');
                                @endphp
                                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} rounded-pill px-3 py-1 fw-bold" style="font-size: 0.65rem;">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <span class="extra-small text-muted me-2">{{ $thesis->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('theses.show', $thesis->id) }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-none" style="font-size: 0.7rem;">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center text-muted small">Belum ada aktifitas terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0 px-4">
                <h6 class="fw-bold mb-0">Quick Access</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.queue') }}" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm btn-sm">
                        <i class="fas fa-check-circle me-2"></i> Verifikasi Antrean
                    </a>
                    <a href="{{ route('admin.theses.index') }}" class="btn btn-light border rounded-pill py-2 fw-bold btn-sm">
                        <i class="fas fa-book me-2"></i> Kelola Repositori
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border rounded-pill py-2 fw-bold btn-sm">
                        <i class="fas fa-users me-2"></i> Kelola Users
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-lg rounded-4 p-4 text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="fas fa-microchip fa-4x"></i>
            </div>
            <h6 class="extra-small fw-bold mb-4 text-primary text-uppercase tracking-wider" style="color: #818cf8 !important;">System Health</h6>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fab fa-laravel text-danger"></i>
                        <span class="extra-small opacity-75">Laravel</span>
                    </div>
                    <span class="fw-bold extra-small">{{ app()->version() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fab fa-php text-info"></i>
                        <span class="extra-small opacity-75">PHP</span>
                    </div>
                    <span class="fw-bold extra-small">{{ PHP_VERSION }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-server text-success"></i>
                        <span class="extra-small opacity-75">Env</span>
                    </div>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1" style="font-size: 0.6rem; color: #818cf8 !important; border-color: #818cf8 !important;">{{ strtoupper(config('app.env')) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
