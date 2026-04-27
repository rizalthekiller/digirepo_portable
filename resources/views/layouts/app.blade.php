<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('site_name', 'DigiRepo') }} | @yield('title', 'Repositori Digital')</title>
    
    @php
        $favicon = \App\Models\Setting::get('site_favicon_path');
        $siteLogo = \App\Models\Setting::get('site_logo_path');
        $siteName = \App\Models\Setting::get('site_name', 'DigiRepo');
    @endphp

    @if($favicon && file_exists(public_path($favicon)))
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;        /* Indigo - Modern & Professional */
            --primary-light: #8b5cf6;  /* Violet - Friendly Accent */
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            --secondary: #64748b;      /* Slate - For Muted Text */
            --dark: #0f172a;           /* Deep Navy - For Dark Backgrounds */
            --text-main: #1e293b;      /* Slate 800 - High Readability on White */
            --text-muted: #475569;     /* Slate 600 - Secondary Text */
            --bg-body: #e2e8f0;        /* Slate 200 - Strong contrast against white elements */
            --card-shadow: 0 10px 40px rgba(15, 23, 42, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        
        .navbar {
            background-color: #ffffff !important;
            padding: 1rem 0;
            transition: none;
            border: none;
            box-shadow: none;
            z-index: 1050;
        }
        
        /* Navbar brand color */
        .navbar-brand {
            font-size: 1.6rem;
            color: var(--primary) !important;
            letter-spacing: -1px;
            transition: transform 0.3s ease;
        }
        
        .nav-link {
            font-weight: 600;
            color: var(--text-muted) !important;
            padding: 0.5rem 1.2rem !important;
            transition: color 0.3s ease, background-color 0.3s ease;
            border-radius: 12px;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            background-color: rgba(30, 58, 138, 0.05);
        }
        
        .dropdown-menu {
            transform: translateY(15px) scale(0.95);
            opacity: 0;
            display: block;
            visibility: hidden;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, visibility 0.3s ease;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-radius: 20px;
            margin-top: 0; /* Align to trigger */
        }

        /* Invisible bridge to prevent mouse-out flickering */
        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 0;
            right: 0;
            height: 20px;
        }

        .dropdown:hover .dropdown-menu {
            transform: translateY(5px) scale(1);
            opacity: 1;
            visibility: visible;
        }

        .fw-800 { font-weight: 800; }
        .fw-700 { font-weight: 700; }
        .fw-600 { font-weight: 600; }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 700;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.3);
            filter: brightness(1.1);
        }

        .glass-card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.02);
            border-radius: 30px;
            box-shadow: var(--card-shadow);
            padding: 40px;
            transition: var(--transition);
        }

        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        }

        .form-control-premium {
            background: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 14px 20px;
            font-weight: 500;
            transition: var(--transition);
        }

        .form-control-premium:focus {
            background: white;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        /* Animations disabled for performance */
        .animate-fade-in {
            opacity: 1;
        }

        /* Notification Styles */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 12px;
            transition: var(--transition);
        }
        
        .notification-bell:hover {
            background: rgba(79, 70, 229, 0.05);
        }
        
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444; /* Red 500 */
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            padding: 0 4px;
        }

        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                @if($siteLogo && !is_dir(public_path($siteLogo)) && file_exists(public_path($siteLogo)))
                    <img src="{{ asset($siteLogo) }}" alt="Logo" style="max-height: 40px;">
                @endif
                <span class="fw-900">{{ $siteName }}</span><span class="text-primary-light">.</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/browse') }}">Jelajahi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/faq') }}">F.A.Q</a></li>
                    @auth
                        <!-- Notifications -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link notification-bell d-flex align-items-center p-0" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="position-relative p-2">
                                    <i class="far fa-bell fs-5"></i>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="notification-badge">
                                            {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown border-0 shadow-lg p-3 rounded-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                                    <h6 class="fw-800 mb-0">Notifikasi</h6>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <button onclick="markAllAsRead()" class="btn btn-link p-0 text-primary small fw-bold text-decoration-none">Tandai Baca Semua</button>
                                    @endif
                                </div>
                                <div class="notification-list">
                                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                        <div class="p-3 mb-2 rounded-3 bg-light hover-lift cursor-pointer small" onclick="markAsRead('{{ $notification->id }}')">
                                            <div class="fw-bold mb-1 text-dark">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</div>
                                            <div class="text-muted" style="font-size: 0.8rem;">{{ $notification->data['message'] ?? 'Klik untuk melihat detail.' }}</div>
                                            <div class="text-primary-light mt-1" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-bell-slash text-muted opacity-25 mb-3 fa-2x"></i>
                                            <p class="text-muted small mb-0">Tidak ada notifikasi baru</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-4">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item rounded-3 py-2 fw-bold text-primary" href="{{ url('/admin/dashboard') }}"><i class="fas fa-shield-alt me-2 opacity-50"></i> Panel Admin</a></li>
                                @else
                                    <li><a class="dropdown-item rounded-3 py-2" href="{{ url('/dashboard') }}"><i class="fas fa-columns me-2 opacity-50"></i> Dashboard Saya</a></li>
                                @endif
                                <li><a class="dropdown-item rounded-3 py-2" href="{{ url('/profile') }}"><i class="fas fa-user-circle me-2 opacity-50"></i> Profil Saya</a></li>
                                <li><hr class="dropdown-divider opacity-10"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" data-turbo="false">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3"><a class="nav-link" href="{{ url('/login') }}">Masuk</a></li>
                        <li class="nav-item ms-lg-2"><a class="btn btn-primary" href="{{ url('/register') }}">Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @if(session('success') || session('status') || session('error') || $errors->any())
            <div class="container mt-4">
                @if(session('success') || session('status'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-4 animate-fade-in" style="background: #ecfdf5; color: #065f46;">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                            <i class="fas fa-check-circle fs-5 text-success"></i>
                        </div>
                        <div class="fw-bold">{{ session('success') ?: session('status') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-4 animate-fade-in" style="background: #fef2f2; color: #991b1b;">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                            <i class="fas fa-exclamation-circle fs-5 text-danger"></i>
                        </div>
                        <div class="fw-bold">{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4 animate-fade-in" style="background: #fef2f2; color: #991b1b;">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="fas fa-exclamation-triangle fs-5"></i>
                            <div class="fw-bold">Mohon periksa kembali:</div>
                        </div>
                        <ul class="mb-0 small fw-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="background: #0f172a; color: #94a3b8; padding: 100px 0 50px; margin-top: 150px;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <h2 class="text-white fw-800 mb-4" style="letter-spacing: -1px;">DigiRepo<span class="text-primary-light">.</span></h2>
                    <p class="mb-4 pe-lg-5">Sistem Repositori Digital Perpustakaan Modern yang dirancang untuk mendukung ekosistem literasi digital yang terintegrasi, aman, dan mudah diakses.</p>
                    <div class="d-flex gap-3">
                        @foreach(['facebook-f', 'twitter', 'instagram', 'linkedin-in'] as $social)
                            <a href="#" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-none" style="width: 40px; height: 40px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                <i class="fab fa-{{ $social }} fa-sm"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white fw-800 mb-4 text-uppercase small" style="letter-spacing: 0.1em;">Tautan Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-decoration-none text-secondary hover-text-white transition-all">Beranda</a></li>
                        <li class="mb-2"><a href="/browse" class="text-decoration-none text-secondary hover-text-white transition-all">Jelajahi</a></li>
                        <li class="mb-2"><a href="/faq" class="text-decoration-none text-secondary hover-text-white transition-all">Bantuan & FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white fw-800 mb-4 text-uppercase small" style="letter-spacing: 0.1em;">Koleksi</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/browse?type=Skripsi" class="text-decoration-none text-secondary hover-text-white transition-all">Skripsi</a></li>
                        <li class="mb-2"><a href="/browse?type=Thesis" class="text-decoration-none text-secondary hover-text-white transition-all">Thesis</a></li>
                        <li class="mb-2"><a href="/browse?type=Disertasi" class="text-decoration-none text-secondary hover-text-white transition-all">Disertasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-800 mb-4 text-uppercase small" style="letter-spacing: 0.1em;">Kontak Kami</h6>
                    <p class="small mb-2"><i class="fas fa-map-marker-alt me-2 text-primary-light"></i> Gedung Perpustakaan Pusat, Lantai 2</p>
                    <p class="small mb-2"><i class="fas fa-envelope me-2 text-primary-light"></i> library@institution.ac.id</p>
                </div>
            </div>
            <hr class="my-5 opacity-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="small mb-0">&copy; {{ date('Y') }} DigiRepo System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAllAsRead() {
            fetch('{{ route("notifications.read_all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => window.location.reload());
        }

        function markAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => window.location.reload());
        }
    </script>
    @yield('scripts')
</body>
</html>
