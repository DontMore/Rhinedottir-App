<!-- Navbar Mobile -->
<div class="navbar-mobile d-lg-none">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ URL::previous() }}">
                <i class="bi bi-arrow-left"></i>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('logbook*') ? 'active' : '' }}" aria-current="page" href="{{ route('logbook.index') }}">Logbook</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.edit' ? 'active' : '' }}" href="{{ route('user.edit', ['id' => auth()->user()->id]) }}">Setting</a>
                    </li>
                    <li class="nav-item dropdown">
                        <div class="">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<!-- Sidebar Desktop -->
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

@media (max-width: 992px) {
    .sidebar {
        display: none;
    }
}
</style>

<script>
// Add mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    
    if (navbarToggler && sidebar) {
        navbarToggler.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>
