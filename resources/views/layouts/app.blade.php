<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Rhinedottir Lab</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body class="{{ auth()->user()->role }}-layout">

    <!-- Mobile Navigation -->
    @if (request()->is('*'))
    <nav class="mobile-nav d-lg-none">
        <div class="navbar navbar-dark bg-dark py-2 px-3 shadow-sm">
            <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
                <a href="{{ route('dashboard.index') }}" class="navbar-brand d-flex align-items-center gap-2 mb-0 p-0">
                    <i class="bi bi-box-seam fs-4"></i>
                    <span class="fw-bold text-white" style="font-size:1.1rem;">Rhinedottir Lab</span>
                </a>
                <button class="btn btn-light d-flex align-items-center justify-content-center rounded-2"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#mobileMenu"
                        aria-label="Menu"
                        style="width:38px;height:38px;">
                    <i class="bi bi-grid-fill fs-5"></i>
                </button>
            </div>
        </div>

        <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="mobileMenu">
            <div class="offcanvas-header border-bottom border-secondary">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div class="avatar mobile-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="text-white flex-grow-1">
                        <h6 class="mb-0" style="font-size:1rem;">{{ auth()->user()->name }}</h6>
                        <small class="text-muted">{{ auth()->user()->role }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column" style="padding-bottom:0;">
                <!-- Copy desktop menu items here but with mobile styling -->
                @can('admin')
                <div class="mobile-section mb-2">
                    <small class="text-muted text-uppercase ps-1">Management</small>
                    
                    <div class="menu-item-mobile {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.index') }}" class="menu-link-mobile">
                            <i class="bi bi-house"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    <div class="menu-item-mobile {{ request()->is('management-stock*') ? 'active' : '' }}">
                        <a href="{{ route('management-stock.index') }}" class="menu-link-mobile">
                            <i class="bi bi-box"></i>
                            <span>Stock Management</span>
                        </a>
                    </div>

                    <div class="menu-item-mobile {{ request()->is('stock-opname*') ? 'active' : '' }}">
                        <a href="{{ route('stock.index') }}" class="menu-link-mobile">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Stock Opname</span>
                        </a>
                    </div>
                </div>
                @endcan
                
                <div class="mobile-section mb-2">
                    <small class="text-muted text-uppercase ps-1">Operations</small>

                    <div class="menu-item-mobile {{ request()->is('logbook*') ? 'active' : '' }}">
                        <a href="{{ route('logbook.index') }}" class="menu-link-mobile">
                            <i class="bi bi-journal-text"></i>
                            <span>Logbook</span>
                        </a>
                    </div>

                    @can('admin')
                    <div class="menu-item-mobile {{ request()->is('order*') ? 'active' : '' }}">
                        <a href="{{ route('order.index') }}" class="menu-link-mobile">
                            <i class="bi bi-cart"></i>
                            <span>Orders</span>
                        </a>
                    </div>

                    <div class="menu-item-mobile {{ request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.index') }}" class="menu-link-mobile">
                            <i class="bi bi-bar-chart"></i>
                            <span>Reports</span>
                        </a>
                    </div>

                    <div class="menu-item-mobile {{ request()->is('user-list*') ? 'active' : '' }}">
                        <a href="user-list" class="menu-link-mobile">
                            <i class="bi bi-people"></i>
                            <span>Users</span>
                        </a>
                    </div>
                    @endcan
                </div>

                <div class="mt-auto pt-2 pb-1">
                    <hr class="border-secondary mb-2 mt-2">
                    <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}" 
                       class="mobile-link {{ Route::currentRouteName() === 'user.edit' ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" style="font-size:1rem;">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <!-- Desktop Sidebar - Keep existing code -->
    <div class="sidebar d-none d-lg-flex flex-column">
        <!-- User Profile -->
        <div class="sidebar-header p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar">
                    <i class="bi bi-person-circle fs-4"></i>
                </div>
                <div class="user-info">
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->role }}</small>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="sidebar-menu flex-grow-1 p-3">
            @can('admin')
            <div class="menu-section">
                <small class="text-muted text-uppercase px-3">Management</small>
                
                <div class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.index') }}" class="menu-link">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="menu-item {{ request()->is('management-stock*') ? 'active' : '' }}">
                    <a href="{{ route('management-stock.index') }}" class="menu-link">
                        <i class="bi bi-box"></i>
                        <span>Stock Management</span>
                    </a>
                </div>

                <div class="menu-item {{ request()->is('stock-opname*') ? 'active' : '' }}">
                    <a href="{{ route('stock.index') }}" class="menu-link">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Stock Opname</span>
                    </a>
                </div>
            </div>
            @endcan

            <div class="menu-section mt-3">
                <small class="text-muted text-uppercase px-3">Operations</small>

                <div class="menu-item {{ request()->is('logbook*') ? 'active' : '' }}">
                    <a href="{{ route('logbook.index') }}" class="menu-link">
                        <i class="bi bi-journal-text"></i>
                        <span>Logbook</span>
                    </a>
                </div>

                @can('admin')
                <div class="menu-item {{ request()->is('order*') ? 'active' : '' }}">
                    <a href="{{ route('order.index') }}" class="menu-link">
                        <i class="bi bi-cart"></i>
                        <span>Orders</span>
                    </a>
                </div>

                <div class="menu-item {{ request()->is('report*') ? 'active' : '' }}">
                    <a href="{{ route('report.index') }}" class="menu-link">
                        <i class="bi bi-bar-chart"></i>
                        <span>Reports</span>
                    </a>
                </div>

                <div class="menu-item {{ request()->is('user-list*') ? 'active' : '' }}">
                    <a href="user-list" class="menu-link">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                        </a>
                </div>
                @endcan
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="sidebar-footer border-top p-3">
            <div class="menu-item {{ Route::currentRouteName() === 'user.edit' ? 'active' : '' }}">
                <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}" class="menu-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content" style="margin-left: var(--sidebar-width); padding: 1rem;">
        @yield('content')
    </div>

    <style>
    :root {
        --sidebar-width: 240px;
        --sidebar-bg: #1a1d20;
        --sidebar-hover: #23272b;
        --sidebar-active: #0d6efd;
        --sidebar-text: #e2e8f0;
        --sidebar-muted: #6c757d;
        --sidebar-border: rgba(255,255,255,0.08);
    }

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1030;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Remove scroll */
        overflow: hidden;
    }

    .sidebar-header {
        padding: 1rem 1rem 0.5rem 1rem;
        border-bottom: 1px solid var(--sidebar-border);
        min-height: 60px;
    }

    .sidebar-menu {
        flex: 1 1 auto;
        padding: 0.5rem 0.5rem 0 0.5rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 0.5rem;
    }

    .menu-section {
        margin-bottom: 0.5rem;
    }

    .menu-section small {
        color: var(--sidebar-muted);
        font-size: 0.72rem;
        font-weight: 600;
        margin: 0.5rem 0 0.25rem 0.5rem;
        letter-spacing: 0.04em;
        display: block;
    }

    .menu-item {
        margin: 0.1rem 0;
    }

    .menu-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 0.4rem;
        gap: 0.7rem;
        font-size: 0.93rem;
        font-weight: 500;
        transition: background 0.18s, color 0.18s;
    }

    .menu-link:hover {
        background: var(--sidebar-hover);
        color: #fff;
    }

    .menu-item.active .menu-link {
        background: var(--sidebar-active);
        color: #fff;
    }

    .menu-link i {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .sidebar-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--sidebar-border);
        background: rgba(0,0,0,0.08);
    }

    .avatar {
        width: 36px;
        height: 36px;
        background: var(--sidebar-hover);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-info {
        color: var(--sidebar-text);
    }

    .user-info small {
        color: var(--sidebar-muted);
    }

    .mobile-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1080;
        background: var(--sidebar-bg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .mobile-nav .navbar {
        min-height: 56px;
        padding-left: 0;
        padding-right: 0;
    }

    .mobile-nav .navbar-brand {
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.02em;
        padding: 0;
    }

    .mobile-nav .btn-light {
        padding: 0.5rem;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #fff;
        border: none;
        color: #212529;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: background 0.15s;
    }

    .mobile-nav .btn-light:hover {
        background: #f1f3f5;
    }

    .mobile-nav .btn-light:active {
        background: #e9ecef;
    }

    .mobile-avatar {
        width: 42px;
        height: 42px;
        background: var(--sidebar-hover);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
    }

    .offcanvas-header {
        padding: 0.8rem 1rem;
        min-height: 60px;
    }

    .offcanvas-body {
        padding: 0.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        height: calc(100% - 60px);
    }

    .mobile-section {
        padding: 0.2rem 0 0.2rem 0;
    }

    .mobile-section small {
        display: block;
        padding: 0.3rem 0 0.3rem 0.2rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: var(--sidebar-muted);
    }

    .menu-item-mobile {
        margin: 0.15rem 0;
    }

    .menu-link-mobile {
        display: flex;
        align-items: center;
        padding: 0.65rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 0.5rem;
        gap: 0.85rem;
        font-size: 1.02rem;
        font-weight: 500;
        transition: background 0.18s, color 0.18s;
    }

    .menu-link-mobile:hover,
    .menu-item-mobile.active .menu-link-mobile {
        background: var(--sidebar-active);
        color: #fff;
    }

    .menu-link-mobile i {
        font-size: 1.18rem;
        opacity: 0.93;
    }

    .mobile-link {
        display: flex;
        align-items: center;
        padding: 0.7rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 0.5rem;
        gap: 0.75rem;
        transition: all 0.2s;
        font-size: 1.02rem;
        font-weight: 500;
    }

    .mobile-link:hover,
    .mobile-link.active {
        background: var(--sidebar-hover);
        color: #fff;
    }

    .offcanvas {
        z-index: 1090 !important;
        background-color: var(--sidebar-bg) !important;
        width: 280px !important;
    }

    .offcanvas-backdrop {
        z-index: 1085;
        background-color: rgba(0,0,0,0.5);
    }

    @media (max-width: 992px) {
        body {
            padding-top: 56px !important; 
        }
        
        .main-content {
            margin-top: 1rem;
        }
    }

    @media (max-width: 576px) {
        .offcanvas {
            width: 85% !important;
        }
    }
    </style>

    <!-- Add this before </body> tag in your layout -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix backdrop
        const offcanvas = document.getElementById('mobileMenu');
        offcanvas?.addEventListener('shown.bs.offcanvas', function () {
            document.body.style.overflow = 'hidden';
        });
        offcanvas?.addEventListener('hidden.bs.offcanvas', function () {
            document.body.style.overflow = '';
        });
    });
    </script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>