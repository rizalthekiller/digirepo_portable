@extends('layouts.admin')

@section('page_title', 'Overview Statistics')

@section('styles')
<style>
    .zenith-stat-icon {
        width: 50px; height: 50px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; margin-bottom: 20px;
    }
    .val-text { font-size: 2rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2; }
    .label-text { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; }
    
    .timeline-zenith { position: relative; padding-left: 20px; }
    .timeline-zenith::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 2px; background: #f1f5f9; }
    .t-item { position: relative; padding-bottom: 25px; padding-left: 20px; }
    .t-item::before { content: ''; position: absolute; left: -25px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: var(--zenith-primary); border: 3px solid white; }
</style>
@endsection

@section('content')
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="zenith-card">
            <div class="zenith-stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-clock"></i>
            </div>
            <div class="label-text">Pending Approval</div>
            <div class="val-text mt-2">{{ $stats['pending_theses'] }}</div>
            <div class="text-warning small fw-bold mt-2"><i class="fas fa-arrow-up me-1"></i> 5 new documents</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="zenith-card">
            <div class="zenith-stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-file-circle-check"></i>
            </div>
            <div class="label-text">Verified Theses</div>
            <div class="val-text mt-2">{{ number_format($stats['approved_theses']) }}</div>
            <div class="text-success small fw-bold mt-2"><i class="fas fa-check-double me-1"></i> Data Realtime</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="zenith-card">
            <div class="zenith-stat-icon bg-indigo bg-opacity-10 text-indigo" style="color: var(--zenith-primary) !important;">
                <i class="fas fa-users"></i>
            </div>
            <div class="label-text">Active Students</div>
            <div class="val-text mt-2">{{ $stats['total_users'] }}</div>
            <div class="text-primary small fw-bold mt-2"><i class="fas fa-user-plus me-1"></i> 3 joined today</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="zenith-card">
            <div class="zenith-stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="fas fa-award"></i>
            </div>
            <div class="label-text">Certificates Issued</div>
            <div class="val-text mt-2">{{ number_format($stats['approved_theses']) }}</div>
            <div class="text-danger small fw-bold mt-2"><i class="fas fa-shield-check me-1"></i> 100% Verified</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="zenith-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h5 class="fw-zenith mb-0">Recent Submissions</h5>
                <a href="{{ route('admin.theses.index') }}" class="btn btn-light rounded-pill px-4 fw-bold small">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-secondary small fw-bold">
                            <th class="border-0" style="width: 50px;">NO</th>
                            <th class="border-0">STUDENT NAME</th>
                            <th class="border-0">TITLE</th>
                            <th class="border-0">FILE</th>
                            <th class="border-0 text-end">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_submissions'] as $thesis)
                        @php
                            $fileExists = false;
                            if ($thesis->file_path) {
                                $cleanPath = $thesis->file_path;
                                $prefixes = ['/storage/', 'storage/', '/public/', 'public/'];
                                foreach ($prefixes as $prefix) {
                                    if (str_starts_with($cleanPath, $prefix)) {
                                        $cleanPath = substr($cleanPath, strlen($prefix));
                                    }
                                }
                                $cleanPath = ltrim($cleanPath, '/');
                                $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
                            }
                        @endphp
                        <tr>
                            <td class="py-3 border-0 text-secondary fw-bold">{{ $loop->iteration }}</td>
                            <td class="py-3 border-0">
                                <div class="fw-bold">{{ $thesis->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-muted small">{{ $thesis->user->nim ?? '-' }}</div>
                            </td>
                            <td class="py-3 border-0">
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $thesis->title }}">{{ $thesis->title }}</div>
                            </td>
                            <td class="py-3 border-0">
                                @if($thesis->file_path && $fileExists)
                                    <a href="{{ route('theses.stream', $thesis->id) }}" target="_blank" data-turbo="false" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" style="font-size: 0.65rem;">
                                        <i class="fas fa-file-pdf me-1"></i> BUKA PDF
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.6rem; cursor: not-allowed; opacity: 0.7;">
                                        <i class="fas fa-times-circle me-1"></i> MISSING
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 border-0 text-end">
                                <span class="badge bg-{{ $thesis->status === 'approved' ? 'success' : ($thesis->status === 'rejected' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $thesis->status === 'approved' ? 'success' : ($thesis->status === 'rejected' ? 'danger' : 'warning') }} rounded-pill px-3 py-2" style="font-size: 0.65rem;">
                                    {{ strtoupper($thesis->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="zenith-card h-100">
            <h5 class="fw-zenith mb-5">System Activity</h5>
            <div class="timeline-zenith">
                <div class="t-item">
                    <div class="fw-800 small">Certificate Generated</div>
                    <div class="text-muted small">Student #202401 successfully verified.</div>
                    <div class="text-secondary" style="font-size: 0.65rem; margin-top: 5px;">2 minutes ago</div>
                </div>
                <div class="t-item">
                    <div class="fw-800 small">New Thesis Uploaded</div>
                    <div class="text-muted small">Manual upload by Administrator.</div>
                    <div class="text-secondary" style="font-size: 0.65rem; margin-top: 5px;">45 minutes ago</div>
                </div>
                <div class="t-item" style="padding-bottom: 0;">
                    <div class="fw-800 small">System Update</div>
                    <div class="text-muted small">Theme changed to Zenith 2.0.</div>
                    <div class="text-secondary" style="font-size: 0.65rem; margin-top: 5px;">1 hour ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
