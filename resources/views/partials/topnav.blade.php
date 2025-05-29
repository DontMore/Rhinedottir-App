<!-- Mobile Top Navigation -->
@if(request()->is('*'))
<nav class="top-nav d-lg-none">
    <div class="navbar navbar-dark bg-dark py-2 px-3 shadow-sm">
        <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
            <a href="{{ route('dashboard.index') }}" class="navbar-brand d-flex align-items-center gap-2 mb-0 p-0">
                <i class="bi bi-box-seam fs-4"></i>
                <span class="fw-bold text-white" style="font-size:1.1rem;">Rhinedottir Lab</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-flex align-items-center justify-content-center rounded-2"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#userMenu"
                        aria-label="Menu"
                        style="width:38px;height:38px;">
                    <i class="bi bi-person"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- User Menu Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="userMenu">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-4"></i>
                <div>
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-secondary">{{ auth()->user()->role }}</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        
        <div class="offcanvas-body">
            <div class="menu-group">
                <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}" class="menu-item">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
.top-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1080;
    background: var(--sidebar-bg);
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.menu-group {
    margin-bottom: 1rem;
    padding: 0 0.5rem;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #212529;
    text-decoration: none;
    border-radius: 0.5rem;
    gap: 0.75rem;
    margin-bottom: 0.25rem;
}

.menu-item:hover {
    background: #f8f9fa;
    color: var(--bs-primary);
}

.menu-item i {
    font-size: 1.1rem;
    opacity: 0.8;
}
</style>
@endif
