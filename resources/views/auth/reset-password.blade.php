@extends('layouts.app')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
<div class="container login-container animate-fade-in">
    <div class="glass-card" style="max-width: 500px; width: 100%;">
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                <i class="fas fa-shield-alt fa-2x"></i>
            </div>
            <h2 class="fw-800 mb-2">Atur Ulang Sandi</h2>
            <p class="text-secondary">Silakan masukkan kata sandi baru Anda untuk mengamankan akun.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4 small py-3 px-4 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="text-center">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mb-4">
                <label class="form-label fw-600 small d-block">Alamat Email</label>
                <input type="email" name="email" class="form-control-premium w-100 text-center" value="{{ $email ?? old('email') }}" required readonly>
            </div>

            <div class="mb-4">
                <label class="form-label fw-600 small d-block">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control-premium w-100 text-center" placeholder="••••••••" required autofocus>
                <div class="form-text small opacity-50">Minimal 8 karakter campuran huruf dan angka.</div>
            </div>

            <div class="mb-5">
                <label class="form-label fw-600 small d-block">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-control-premium w-100 text-center" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                Simpan Kata Sandi Baru
            </button>
        </form>
    </div>
</div>
@endsection
