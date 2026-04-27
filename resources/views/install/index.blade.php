<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi DigiRepo | Repositori Digital Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            --bg-body: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
        }
        .install-card {
            background: white;
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
            max-width: 1000px;
            width: 100%;
            overflow: hidden;
            display: flex;
        }
        .install-sidebar {
            background: var(--primary-gradient);
            color: white;
            padding: 60px 40px;
            width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .install-content {
            flex-grow: 1;
            padding: 60px;
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            opacity: 0.5;
            transition: all 0.3s ease;
        }
        .step-item.active {
            opacity: 1;
            transform: translateX(10px);
        }
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .form-control-premium {
            background: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 14px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .form-control-premium:focus {
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        .btn-install {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 16px 40px;
            color: white;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }
        .btn-install:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
            color: white;
        }
        .loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.9);
            z-index: 100;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="install-card position-relative">
        <div id="loading" class="loading-overlay">
            <div class="spinner mb-4"></div>
            <h4 class="fw-800">Sedang Menginstall...</h4>
            <p class="text-secondary">Mohon tunggu, sistem sedang menyiapkan database dan aset Anda.</p>
        </div>

        <div class="install-sidebar">
            <div>
                <h2 class="mb-4">DigiRepo<span class="opacity-50">.</span></h2>
                <div class="step-list">
                    <div class="step-item active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="fw-bold">Database</div>
                    </div>
                    <div class="step-item" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="fw-bold">Akun Admin</div>
                    </div>
                    <div class="step-item" id="step3-indicator">
                        <div class="step-number">3</div>
                        <div class="fw-bold">Selesai</div>
                    </div>
                </div>
            </div>
            <div class="small opacity-75">
                © 2026 DigiRepo System <br> Digital Literacy Ecosystem
            </div>
        </div>

        <div class="install-content">
            <form id="installForm">
                @csrf
                <!-- Step 1: Database -->
                <div id="step1">
                    <h3 class="mb-2">Konfigurasi Database</h3>
                    <p class="text-secondary mb-5">Masukkan detail koneksi MySQL Anda. Jika database belum ada, sistem akan mencoba membuatnya.</p>
                    
                    <div class="row g-4">
                        <div class="col-md-9">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Database Host</label>
                            <input type="text" name="db_host" class="form-control form-control-premium" value="127.0.0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Port</label>
                            <input type="text" name="db_port" class="form-control form-control-premium" value="3306">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Nama Database</label>
                            <input type="text" name="db_name" class="form-control form-control-premium" value="digirepo_laravel" placeholder="Contoh: digirepo_db">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Username</label>
                            <input type="text" name="db_user" class="form-control form-control-premium" value="root">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Password</label>
                            <input type="password" name="db_pass" class="form-control form-control-premium" placeholder="Kosongkan jika default XAMPP">
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="button" onclick="nextStep(2)" class="btn btn-install">Lanjut ke Admin <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Admin -->
                <div id="step2" style="display: none;">
                    <h3 class="mb-2">Akun Administrator</h3>
                    <p class="text-secondary mb-5">Buat akun Super Admin pertama Anda untuk mengelola repositori.</p>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Nama Lengkap</label>
                            <input type="text" name="admin_name" class="form-control form-control-premium" placeholder="Contoh: Administrator Utama">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Email Admin</label>
                            <input type="email" name="admin_email" class="form-control form-control-premium" placeholder="email@admin.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase opacity-50">Password</label>
                            <input type="password" name="admin_password" class="form-control form-control-premium" placeholder="Minimal 8 karakter">
                        </div>
                    </div>

                    <div class="mt-5 d-flex justify-content-between">
                        <button type="button" onclick="nextStep(1)" class="btn btn-link text-decoration-none text-secondary fw-bold">Kembali</button>
                        <button type="submit" class="btn btn-install">Install Sekarang <i class="fas fa-magic ms-2"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function nextStep(step) {
            document.getElementById('step1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step2').style.display = step === 2 ? 'block' : 'none';
            
            document.getElementById('step1-indicator').classList.toggle('active', step === 1);
            document.getElementById('step2-indicator').classList.toggle('active', step === 2);
        }

        document.getElementById('installForm').onsubmit = async (e) => {
            e.preventDefault();
            
            document.getElementById('loading').style.display = 'flex';
            
            const formData = new FormData(e.target);
            try {
                const response = await fetch('{{ route("install.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Instalasi Berhasil!',
                        text: result.message,
                        confirmButtonText: 'Ke Halaman Login'
                    }).then(() => {
                        window.location.href = '{{ url("/login") }}';
                    });
                } else {
                    document.getElementById('loading').style.display = 'none';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menginstall',
                        text: result.message
                    });
                }
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kesalahan saat menghubungi server.'
                });
            }
        };
    </script>
</body>
</html>
