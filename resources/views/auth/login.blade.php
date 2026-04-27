@extends('layouts.app')

@section('title', 'Masuk ke DigiRepo')


@section('content')
<div class="auth-wrapper d-flex align-items-center justify-content-center animate-fade-in" style="min-height: calc(100vh - 100px); background: #f1f5f9; margin-top: -20px;">
    <div class="glass-card shadow-lg border-0" style="max-width: 480px; width: 90%; padding: 50px; border-radius: 40px; background: #ffffff;">
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                <i class="fas fa-lock fa-2x"></i>
            </div>
            <h2 class="fw-800 mb-2" style="letter-spacing: -1px;">Selamat Datang</h2>
            <p class="text-secondary small">Silakan masuk untuk melanjutkan.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4 small py-3 px-4 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-600 small mb-2">Alamat Email</label>
                <input type="email" name="email" class="form-control-premium w-100" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus style="border: 2px solid #e2e8f0; background: #f8fafc;">
            </div>
            <div class="mb-5">
                <div class="d-flex justify-content-between mb-2">
                    <label class="form-label fw-600 small">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" class="text-primary small fw-bold text-decoration-none">Lupa Sandi?</a>
                </div>
                <input type="password" name="password" class="form-control-premium w-100" placeholder="••••••••" required style="border: 2px solid #e2e8f0; background: #f8fafc;">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 mb-4 rounded-4 shadow-sm fw-800">
                Masuk Sekarang
            </button>
            <div class="text-center">
                <p class="text-secondary small">Belum punya akun? <a href="{{ url('/register') }}" class="text-primary fw-bold text-decoration-none">Daftar Gratis</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
