@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="container auth-container animate-fade-in">
    <div class="glass-card" style="max-width: 500px; width: 100%;">
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                <i class="fas fa-key fa-2x"></i>
            </div>
            <h2 class="fw-800 mb-2">Lupa Kata Sandi?</h2>
            <p class="text-secondary">Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang kata sandi.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 mb-4 small py-3 px-4 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4 small py-3 px-4 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="text-center">
            @csrf
            <div class="mb-5">
                <label class="form-label fw-600 small d-block">Alamat Email Terdaftar</label>
                <input type="email" name="email" class="form-control-premium w-100 text-center" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-3 mb-4">
                Kirim Link Reset
            </button>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-secondary small fw-bold text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
