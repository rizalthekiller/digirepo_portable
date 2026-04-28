<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName }} | @yield('page_title', 'Dashboard')</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset($siteFavicon ?: 'assets/logo.png') }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --sidebar-width: 260px; --primary-color: #4f46e5; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: var(--sidebar-width); background: #1e293b; color: #fff; z-index: 1000; transition: all 0.3s; }
        .sidebar-brand { padding: 1.5rem; font-size: 1.1rem; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 12px; color: #fff; text-decoration: none; letter-spacing: -0.5px; }
        .nav-link { padding: 0.85rem 1.5rem; color: #94a3b8; display: flex; align-items: center; gap: 12px; font-size: 0.8rem; font-weight: 500; transition: all 0.2s; text-decoration: none; border-left: 3px solid transparent; }
        .nav-link i { width: 20px; text-align: center; font-size: 1rem; opacity: 0.7; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.03); }
        .nav-link.active { color: #fff; background: rgba(79, 70, 229, 0.1); border-left-color: var(--primary-color); font-weight: 600; }
        .nav-link.active i { color: var(--primary-color); opacity: 1; }
        .nav-label { padding: 1.8rem 1.5rem 0.6rem; font-size: 0.65rem; text-transform: uppercase; color: #475569; font-weight: 700; letter-spacing: 0.1em; }
        .main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; }
        .navbar-top { background: #fff; padding: 0.75rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 999; }
        .content-body { padding: 2rem; }

        /* Modern Table Styles */
        .table-modern thead th { background: #f8fafc; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; padding: 15px 20px; font-weight: 700; }
        .table-modern tbody td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .table-modern tr:hover { background-color: #f8fafc; }
        
        .extra-small { font-size: 0.7rem; }
        .letter-spacing-1 { letter-spacing: 0.05em; }

        /* Dropdown custom */
        .dropdown-menu { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; padding: 8px; }
        .dropdown-item { border-radius: 8px; padding: 8px 12px; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .dropdown-item i { width: 16px; text-align: center; }

        @media (max-width: 991px) {
            .sidebar { left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { left: 0; box-shadow: 20px 0 50px rgba(0,0,0,0.2); }
            .main-wrapper { margin-left: 0 !important; }
            .navbar-top { padding: 0.75rem 1rem; }
            .content-body { padding: 1.25rem; }
        }

        @media (max-width: 576px) {
            .sidebar-brand span { font-size: 0.9rem; }
            .navbar-top h5 { font-size: 1rem; }
            .stat-card .card-body { padding: 1.5rem !important; }
            .table-modern thead th, .table-modern tbody td { padding: 12px 15px; }
            .btn-sm { padding: 5px 12px; font-size: 0.75rem; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <a href="{{ url('/') }}" class="sidebar-brand">
            <img src="{{ asset($siteLogo ?: 'assets/logo.png') }}" alt="Logo" style="height: 30px; width: auto;">
            <span>{{ $siteName }}</span>
        </a>
        <div class="overflow-auto" style="height: calc(100vh - 70px);">
            <div class="nav-label">Main Menu</div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard Admin</a>
                <a href="{{ route('admin.queue') }}" class="nav-link {{ Request::is('admin/queue') ? 'active' : '' }}"><i class="fas fa-clipboard-check"></i> Antrean Verifikasi</a>
                <a href="{{ route('admin.theses.index') }}" class="nav-link {{ Request::is('admin/theses*') ? 'active' : '' }}"><i class="fas fa-database"></i> Database Repositori</a>
                <a href="{{ route('admin.certificates.index') }}" class="nav-link {{ Request::is('admin/certificates*') ? 'active' : '' }}"><i class="fas fa-award"></i> Arsip Sertifikat</a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Statistik & Laporan</a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}"><i class="fas fa-house"></i> Dashboard</a>
                <a href="{{ route('theses.create') }}" class="nav-link {{ Request::is('theses/upload') ? 'active' : '' }}"><i class="fas fa-cloud-upload-alt"></i> Unggah Karya Baru</a>
                <a href="{{ route('profile') }}" class="nav-link {{ Request::is('profile*') ? 'active' : '' }}"><i class="fas fa-user-gear"></i> Profil Saya</a>
            @endif
            
            @if(Auth::user()->isSuperAdmin())
            <div class="nav-label">Data Master</div>
            <a href="{{ route('admin.master.faculties') }}" class="nav-link {{ Request::is('admin/faculties*') ? 'active' : '' }}"><i class="fas fa-building-columns"></i> Fakultas</a>
            <a href="{{ route('admin.master.departments') }}" class="nav-link {{ Request::is('admin/departments*') ? 'active' : '' }}"><i class="fas fa-tags"></i> Program Studi</a>
            
            <div class="nav-label">System Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}"><i class="fas fa-user-shield"></i> User Management</a>
            <a href="{{ route('admin.settings') }}" class="nav-link {{ Request::is('admin/settings*') ? 'active' : '' }}"><i class="fas fa-sliders-h"></i> Site Settings</a>
            <a href="{{ route('admin.certificates.settings') }}" class="nav-link {{ Request::is('admin/certificates/settings*') ? 'active' : '' }}"><i class="fas fa-stamp"></i> Certificate Settings</a>
            <a href="{{ route('admin.system.control') }}" class="nav-link {{ Request::is('admin/system/control*') ? 'active' : '' }}"><i class="fas fa-microchip"></i> System Control</a>
            @endif
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="navbar-top">
            <button class="btn d-lg-none" id="sidebar-toggle"><i class="fas fa-bars"></i></button>
            <h5 class="mb-0 fw-bold">@yield('page_title', 'Admin Dashboard')</h5>
            <div class="dropdown">
                <a href="#" class="text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>
        <div class="content-body">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#sidebar-toggle').on('click', function() {
            $('#sidebar').toggleClass('show');
        });
    </script>
    @yield('scripts')
</body>
</html>
