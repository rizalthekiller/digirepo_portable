<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('site_name', 'DigiRepo') }} | @yield('page_title', 'Dashboard')</title>
    
    @php
        $favicon = \App\Models\Setting::get('site_favicon_path');
        $siteLogo = \App\Models\Setting::get('site_logo_path');
    @endphp

    @if($favicon && !is_dir(public_path($favicon)) && file_exists(public_path($favicon)))
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @endif

    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Turbo & NProgress -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    
    <style>
        #nprogress .bar { background: #fff !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: #fff !important; border-left-color: #fff !important; }
        :root {
            --zenith-primary: #4f46e5;
            --zenith-secondary: #8b5cf6;
            --zenith-bg: #e2e8f0;
            --zenith-sidebar: #0f172a;
            --zenith-card-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            --zenith-radius: 24px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--zenith-bg);
            color: #1e293b;
            overflow-x: hidden;
        }
        .w-fit { width: fit-content; }

        h1, h2, h3, h4, h5, h6, .fw-zenith { font-family: 'Outfit', sans-serif; font-weight: 800; }

        /* Floating Sidebar */
        .zenith-sidebar {
            position: fixed;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: 280px;
            background: var(--zenith-sidebar);
            border-radius: 30px;
            z-index: 1000;
            padding: 30px 10px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .zenith-nav-container {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0 10px 20px 10px;
        }

        .zenith-nav-container::-webkit-scrollbar {
            width: 5px;
        }
        .zenith-nav-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .zenith-nav-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .zenith-nav-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .zenith-nav-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .zenith-content {
            margin-left: 320px;
            padding: 40px;
            transition: var(--transition);
        }

        .nav-group-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #64748b;
            font-weight: 800;
            margin: 10px 0 15px 15px;
        }

        .zenith-nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 18px;
            margin-bottom: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .zenith-nav-link i { font-size: 1.1rem; width: 24px; text-align: center; }

        .zenith-nav-link:hover, .zenith-nav-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .zenith-nav-link.active {
            background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary));
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .zenith-card {
            background: #ffffff;
            border-radius: var(--zenith-radius);
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: var(--zenith-card-shadow);
            padding: 30px;
        }

        .zenith-card:hover {
            border-color: var(--zenith-primary);
        }

        .zenith-navbar {
            position: sticky;
            top: 0;
            z-index: 1100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 0;
            margin-bottom: 20px;
            background: transparent;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Efek saat discroll - Lebih premium dan menyatu */
        .zenith-navbar.scrolled {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            padding: 15px 30px;
            margin-left: -30px;
            margin-right: -30px;
            border-radius: 0 0 30px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .user-pill {
            background: white;
            padding: 8px 10px 8px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--zenith-card-shadow);
            border: 1px solid rgba(0,0,0,0.01);
            cursor: pointer;
            transition: var(--transition);
        }

        .user-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: var(--zenith-primary);
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .info-label-premium {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: block;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .zenith-sidebar { left: -300px; }
            .zenith-content { margin-left: 0; padding: 20px; }
            .zenith-sidebar.active { left: 20px; }
        }

        /* Zenith Pagination */
        .pagination {
            gap: 4px;
            margin-bottom: 0;
        }
        .pagination .page-item .page-link {
            border: none;
            border-radius: 10px !important;
            color: #64748b;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 8px 14px;
            background: transparent;
            transition: all 0.2s ease;
            box-shadow: none;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--zenith-primary), var(--zenith-secondary));
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }
        .pagination .page-item .page-link:hover {
            background: #f1f5f9;
            color: var(--zenith-primary);
        }
        .pagination .page-item.disabled .page-link {
            background: transparent;
            color: #cbd5e1;
        }

        .btn-logout-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.2) !important;
            filter: brightness(1.1);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Floating Sidebar -->
    <aside class="zenith-sidebar">
        <div class="px-3 mb-3 d-flex align-items-center gap-3">
            @if($siteLogo && !is_dir(public_path($siteLogo)) && file_exists(public_path($siteLogo)))
                <img src="{{ asset($siteLogo) }}" alt="Logo" class="img-fluid" style="max-height: 35px;">
            @endif
            <h4 class="text-white mb-0 text-uppercase" style="letter-spacing: 0.1em; font-size: 1.1rem;">
                {{ str_replace('superadmin', 'SUPER ADMIN', Auth::user()->role) }}<span class="text-primary">.</span>
            </h4>
        </div>

        <div class="zenith-nav-container">
            <div class="nav-group-label">Main Menu</div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="zenith-nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="{{ route('admin.queue') }}" class="zenith-nav-link {{ Request::is('admin/queue') ? 'active' : '' }}">
                    <i class="fas fa-check-double"></i> Verifikasi
                </a>
                <a href="{{ route('admin.theses.index') }}" class="zenith-nav-link {{ Request::is('admin/theses*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Repositori
                </a>
                <a href="{{ route('admin.certificates.index') }}" class="zenith-nav-link {{ Request::is('admin/certificates') ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i> Data Surat
                </a>
                @if(Auth::user()->isSuperAdmin())
                    <div class="nav-group-label">Management</div>
                    <a href="{{ route('admin.certificates.settings') }}" class="zenith-nav-link {{ Request::is('admin/certificates/settings') ? 'active' : '' }}">
                        <i class="fas fa-tools"></i> Pengaturan Surat
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="zenith-nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Pengguna
                    </a>
                    <a href="{{ route('admin.master.faculties') }}" class="zenith-nav-link {{ Request::is('admin/faculties*') ? 'active' : '' }}">
                        <i class="fas fa-university"></i> Data Fakultas
                    </a>
                    <a href="{{ route('admin.master.departments') }}" class="zenith-nav-link {{ Request::is('admin/departments*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i> Program Studi
                    </a>
                    <a href="{{ route('admin.master.types') }}" class="zenith-nav-link {{ Request::is('admin/types*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Tipe Skripsi
                    </a>
                    
                    <div class="nav-group-label">System</div>
                    <a href="{{ route('admin.reports') }}" class="zenith-nav-link {{ Request::is('admin/reports*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Statistik
                    </a>
                    <a href="{{ route('admin.settings') }}" class="zenith-nav-link {{ Request::is('admin/settings*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Pengaturan Situs
                    </a>
                @endif
            @else
                <a href="{{ route('dashboard') }}" class="zenith-nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                @php
                    $userThesis = Auth::user()->theses()->latest()->first();
                    $canUpload = !$userThesis || $userThesis->status === 'rejected' || (Auth::user()->isDosen() && in_array($userThesis->type, ['Jurnal', 'Buku', 'Artikel', 'Lainnya']));
                @endphp
                @if(Auth::user()->role !== 'guest')
                    @if(!$canUpload)
                        <span class="zenith-nav-link opacity-50" style="cursor: not-allowed;" title="Anda sudah mengunggah skripsi">
                            <i class="fas fa-check-circle"></i> Unggah Selesai
                        </span>
                    @else
                        <a href="{{ route('theses.create') }}" class="zenith-nav-link {{ Request::is('theses/upload') ? 'active' : '' }}">
                            <i class="fas fa-cloud-upload-alt"></i> Unggah Skripsi
                        </a>
                    @endif
                @endif
                <a href="{{ route('browse') }}" class="zenith-nav-link">
                    <i class="fas fa-search"></i> Telusuri Repositori
                </a>
                <a href="{{ route('profile') }}" class="zenith-nav-link {{ Request::is('profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
            @endif
        </div>

        <div class="mt-auto pt-5 px-2">
            <form action="{{ route('logout') }}" method="POST" data-turbo="false">
                @csrf
                <button type="submit" class="btn btn-danger w-100 rounded-4 py-3 fw-800 shadow-sm border-0 d-flex align-items-center justify-content-center gap-2 btn-logout-custom" 
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); transition: var(--transition);">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout System</span>
                </button>
            </form>
            <p class="text-center mt-3 mb-0 extra-small opacity-25 text-white" style="font-size: 0.6rem; letter-spacing: 0.05em;">DIGIREPO v2.0</p>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="zenith-content">
        <header class="zenith-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h3 class="fw-zenith mb-0 text-dark">@yield('page_title', 'Dashboard')</h3>
                <div class="d-none d-md-block opacity-25">|</div>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active small fw-bold" aria-current="page" style="color: #94a3b8;">Zenith</li>
                    </ol>
                </nav>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link position-relative p-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.25rem 0.4rem;">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-3 mt-3" style="width: 320px; border-radius: 20px;">
                        <div class="d-flex justify-content-between align-items-center px-2 mb-3">
                            <h6 class="fw-800 mb-0">Notifikasi</h6>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <a href="javascript:void(0)" id="mark-all-read" class="text-primary extra-small fw-bold text-decoration-none" style="font-size: 0.65rem;">Tandai semua dibaca</a>
                            @endif
                        </div>
                        <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                            @forelse(Auth::user()->notifications()->latest()->take(5)->get() as $notification)
                                <div class="dropdown-item p-3 mb-2 rounded-4 mark-as-read {{ $notification->unread() ? 'bg-light fw-600' : 'opacity-75' }}" 
                                     style="white-space: normal; cursor: pointer;" 
                                     data-id="{{ $notification->id }}"
                                     data-url="{{ route('notifications.read', $notification->id) }}">
                                    <div class="d-flex gap-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary h-100">
                                            <i class="fas {{ ($notification->data['type'] ?? '') == 'submitted' ? 'fa-file-upload' : 'fa-check-circle' }}"></i>
                                        </div>
                                        <div>
                                            <div class="small mb-1">{{ $notification->data['message'] }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted small mb-0">Tidak ada notifikasi baru.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <div class="user-pill d-flex align-items-center gap-3" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-800 small mb-0">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->role) }}</div>
                        </div>
                        <div class="avatar-circle shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 mt-3 animate-fade-in" style="border-radius: 20px; min-width: 220px;">
                        <li class="px-3 py-2 mb-2">
                            <div class="fw-800 text-dark small">{{ Auth::user()->name }}</div>
                            <div class="text-muted extra-small" style="font-size: 0.65rem;">{{ Auth::user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 small fw-600" href="{{ route('profile') }}">
                                <i class="fas fa-user-circle text-primary"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 small fw-600" href="{{ route('browse') }}">
                                <i class="fas fa-search text-secondary"></i> Telusuri Repo
                            </a>
                        </li>
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" data-turbo="false">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 small fw-800 text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Keluar Sistem
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-circle fs-4"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // NProgress Integration with Turbo
        document.addEventListener('turbo:visit', function() { NProgress.start(); });
        document.addEventListener('turbo:load', function() { NProgress.done(); });
        document.addEventListener('turbo:before-cache', function() {
            // Tutup semua modal sebelum cache agar tidak nyangkut
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(m => {
                const modalInstance = bootstrap.Modal.getInstance(m);
                if (modalInstance) modalInstance.hide();
            });
        });
        // Notifications Mark as Read
        $(document).on('click', '.mark-as-read', function() {
            const $this = $(this);
            const id = $this.data('id');
            const url = $this.data('url');
            const $badge = $('.badge.rounded-pill.bg-danger');

            if ($this.hasClass('bg-light')) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $this.removeClass('bg-light fw-600').addClass('opacity-75');
                            let count = parseInt($badge.text());
                            if (count > 1) {
                                $badge.text(count - 1);
                            } else {
                                $badge.remove();
                            }
                        }
                    }
                });
            }
        });

        // Mark All as Read
        $('#mark-all-read').on('click', function() {
            const $badge = $('.badge.rounded-pill.bg-danger');
            const $items = $('.mark-as-read');
            const $btn = $(this);

            $.ajax({
                url: '{{ route('notifications.read_all') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $items.removeClass('bg-light fw-600').addClass('opacity-75');
                        $badge.remove();
                        $btn.fadeOut();
                    }
                }
            });
        });
        // Navbar Scroll Effect
        const navbar = document.querySelector('.zenith-navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
