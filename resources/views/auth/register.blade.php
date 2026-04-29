@extends('layouts.app')

@section('title', 'Daftar Akun DigiRepo')

@section('styles')
<style>
    body { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); min-height: 100vh; }
    .register-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 50px 0; }
    .register-card { max-width: 600px; width: 90%; background: white; border-radius: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.05); padding: 50px; }
    .form-control, .form-select { border-radius: 12px; padding: 12px 20px; border: 1px solid #e2e8f0; background: #f8fafc; }
</style>
@endsection

@section('content')
<div class="container register-container animate-fade-in">
    <div class="register-card">
        <div class="text-center mb-5">
            <h2 class="fw-800 mb-2">Buat Akun Baru</h2>
            <p class="text-secondary">Bergabunglah untuk mulai mengelola karya ilmiah Anda.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4 small">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-600 small">Daftar Sebagai</label>
                <select name="role" id="role-select" class="form-select" required>
                    <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest (Umum)</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-600 small">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama lengkap Anda" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-600 small" id="identity-label">NIM</label>
                    <input type="text" name="nim" id="identity-input" class="form-control" placeholder="Nomor Induk Mahasiswa" value="{{ old('nim') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-600 small">Alamat Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@univ.ac.id" value="{{ old('email') }}" required>
            </div>

            <div id="department-section" class="mb-4 {{ in_array(old('role'), ['guest', 'dosen']) ? 'd-none' : '' }}">
                <label class="form-label fw-600 small">Program Studi</label>
                <select name="department_id" class="form-select" id="department_id">
                    <option value="" selected disabled>Pilih Prodi Anda</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->faculty->name }} - {{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-600 small">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
                </div>
                <div class="col-md-6 mb-5">
                    <label class="form-label fw-600 small">Konfirmasi Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 shadow-lg mb-4">
                Daftar Sekarang
            </button>
            
            <div class="text-center">
                <p class="text-secondary small">Sudah punya akun? <a href="{{ url('/login') }}" class="text-primary fw-bold text-decoration-none">Masuk di sini</a></p>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const roleSelect = document.getElementById('role-select');
    const identityLabel = document.getElementById('identity-label');
    const identityInput = document.getElementById('identity-input');
    const deptSection = document.getElementById('department-section');
    const deptInput = document.getElementById('department_id');

    function updateLabels() {
        const role = roleSelect.value;
        if (role === 'dosen') {
            identityLabel.innerText = 'NIDN';
            identityInput.placeholder = 'Nomor Induk Dosen Nasional';
            deptSection.classList.add('d-none');
            deptInput.required = false;
        } else if (role === 'guest') {
            identityLabel.innerText = 'No. Identitas / KTP';
            identityInput.placeholder = 'Masukkan Nomor KTP Anda';
            deptSection.classList.add('d-none');
            deptInput.required = false;
        } else {
            identityLabel.innerText = 'NIM';
            identityInput.placeholder = 'Nomor Induk Mahasiswa';
            deptSection.classList.remove('d-none');
            deptInput.required = true;
        }
    }

    roleSelect.addEventListener('change', updateLabels);
    window.addEventListener('DOMContentLoaded', updateLabels);
</script>
@endsection
