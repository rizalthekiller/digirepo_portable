@extends('layouts.app')

@section('title', 'Verifikasi Dokumen')

@section('styles')
<style>
    .verify-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        padding: 40px 20px;
    }
    .verify-card {
        background: white;
        border-radius: 40px;
        padding: 60px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 40px 100px rgba(0,0,0,0.05);
        text-align: center;
        border: 1px solid rgba(0,0,0,0.01);
    }
    .status-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin: 0 auto 30px;
    }
    .status-success {
        background: #ecfdf5;
        color: #10b981;
    }
    .status-error {
        background: #fef2f2;
        color: #ef4444;
    }
    .detail-item {
        text-align: left;
        background: #f8fafc;
        padding: 20px;
        border-radius: 20px;
        margin-bottom: 15px;
    }
    .detail-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 5px;
    }
    .detail-value {
        font-weight: 700;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="verify-page">
    <div class="verify-card animate-fade-in">
        @if($success)
            <div class="status-icon status-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="fw-800 mb-2">Dokumen Valid</h2>
            <p class="text-muted mb-5">Sertifikat ini terverifikasi secara resmi oleh Sistem Repositori Perpustakaan.</p>

            <div class="detail-item">
                <div class="detail-label">Nomor Sertifikat</div>
                <div class="detail-value text-primary">{{ $thesis->certificate_number }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Nama Mahasiswa</div>
                <div class="detail-value">{{ $thesis->user->name }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Judul Skripsi</div>
                <div class="detail-value text-dark" style="line-height: 1.4;">{{ $thesis->title }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tanggal Verifikasi</div>
                <div class="detail-value">{{ $thesis->updated_at->format('d F Y') }}</div>
            </div>

            <div class="mt-5">
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Kembali ke Beranda</a>
            </div>
        @else
            <div class="status-icon status-error">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2 class="fw-800 mb-2">Dokumen Tidak Valid</h2>
            <p class="text-muted mb-5">Mohon maaf, kode verifikasi tidak ditemukan atau dokumen tidak terdaftar dalam sistem kami.</p>
            
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-5 py-3 fw-bold">Kembali ke Beranda</a>
        @endif
    </div>
</div>
@endsection
