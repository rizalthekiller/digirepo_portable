@extends('layouts.admin')

@section('page_title', 'System Control')

@section('styles')
<style>
    .command-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 1.5rem; }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="command-icon {{ $maintenanceMode ? 'bg-warning text-warning' : 'bg-success text-success' }} bg-opacity-10 mb-0">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Maintenance Mode</h5>
                            <p class="text-muted mb-0 small">Batasi akses pengunjung umum saat perbaikan.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4 bg-light p-3 rounded-4 border">
                        <span class="fw-bold small {{ $maintenanceMode ? 'text-warning' : 'text-success' }}">
                            {{ $maintenanceMode ? 'AKTIF (DOWN)' : 'NON-AKTIF (LIVE)' }}
                        </span>
                        <form action="{{ route('admin.system.maintenance') }}" method="POST" id="maintenanceForm">
                            @csrf
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0 shadow-none" type="checkbox" role="switch" 
                                       id="maintenanceSwitch" onchange="document.getElementById('maintenanceForm').submit()"
                                       {{ $maintenanceMode ? 'checked' : '' }} style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </form>
                    </div>
                </div>
                @if($maintenanceMode)
                    <div class="mt-4 alert alert-warning border-0 rounded-4 small d-flex align-items-center gap-3">
                        <i class="fas fa-key fs-4"></i>
                        <div>
                            <strong>Bypass URL:</strong> Gunakan tautan ini untuk mengakses saat maintenance: 
                            <code class="bg-white px-2 py-1 rounded ms-2">{{ url('/zenith_access') }}</code>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="command-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-broom"></i>
            </div>
            <h6 class="fw-bold mb-2">System Clear</h6>
            <p class="text-muted extra-small mb-4">Membersihkan cache config, route, view, dan cache aplikasi secara menyeluruh.</p>
            <form action="{{ route('admin.system.run_command') }}" method="POST">
                @csrf
                <input type="hidden" name="command" value="optimize:clear">
                <button type="submit" class="btn btn-outline-primary w-100 rounded-pill fw-bold py-2 btn-sm">
                    <i class="fas fa-sync-alt me-2"></i> Run Clear
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="command-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-link"></i>
            </div>
            <h6 class="fw-bold mb-2">Storage Link</h6>
            <p class="text-muted extra-small mb-4">Memperbaiki tautan simbolik antara storage dan public (Solusi gambar tidak muncul).</p>
            <form action="{{ route('admin.system.run_command') }}" method="POST">
                @csrf
                <input type="hidden" name="command" value="storage:link">
                <button type="submit" class="btn btn-outline-info w-100 rounded-pill fw-bold py-2 btn-sm">
                    <i class="fas fa-external-link-alt me-2"></i> Fix Link
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
    <h6 class="fw-bold mb-4 text-white"><i class="fas fa-server me-2 text-primary"></i>Server Info</h6>
    <div class="row g-4">
        @foreach([
            'PHP' => PHP_VERSION,
            'Laravel' => app()->version(),
            'Env' => config('app.env'),
            'DB' => config('database.default')
        ] as $label => $val)
        <div class="col-6 col-md-3">
            <div class="text-white-50 extra-small text-uppercase fw-bold mb-1">{{ $label }}</div>
            <div class="fw-bold small">{{ $val }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
