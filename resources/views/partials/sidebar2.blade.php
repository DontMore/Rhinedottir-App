<!-- Main Navigation -->
<div x-data="{ sidebarOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex flex-col fixed inset-y-0 left-0 w-64 bg-slate-800 text-white shadow-lg transform transition-transform duration-300">
        <!-- Logo Area -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700">
            <span class="text-xl font-bold">Rhinedottir</span>
            <button class="p-2 rounded-lg hover:bg-slate-700">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-b border-slate-700">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                    <i class="bi bi-person text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-medium">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-slate-400">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @can('admin')
            <div class="space-y-2">
                <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Management
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('dashboard.index') }}" 
                        class="group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                        {{ request()->is('dashboard*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                        <i class="bi bi-house mr-3 text-lg"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('management-stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('management-stock*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-box"></i> <span>Stock Management</span>
                    </a>
                    <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('stock-opname*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                </div>
            </div>
            @endcan

            <div class="space-y-2">
                <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Operations
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('logbook.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('logbook*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-journal-text"></i> <span>Logbook</span>
                    </a>
                    @can('admin')
                    <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('order*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-cart"></i> <span>Orders</span>
                    </a>
                    <a href="{{ route('report.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('report*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-bar-chart"></i> <span>Reports</span>
                    </a>
                    <a href="user-list" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('user-list*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-people"></i> <span>Users</span>
                    </a>
                    @endcan
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="border-t border-slate-700 p-4 space-y-2">
            <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition mb-2 {{ Route::currentRouteName() === 'user.edit' ? 'bg-blue-600 text-white' : '' }}">
                <i class="bi bi-gear"></i> <span>Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white transition">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Menu -->
    <div x-show="sidebarOpen" 
         class="fixed inset-0 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50" 
             @click="sidebarOpen = false"
             x-transition></div>

        <!-- Slide-over menu -->
        <div class="fixed inset-y-0 left-0 w-screen max-w-xs bg-slate-800 transform transition-transform duration-300"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Mobile menu content -->
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700">
                    <span class="text-xl font-bold text-white">Menu</span>
                    <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-slate-700 text-slate-400">
                        <i class="bi bi-x text-2xl"></i>
                    </button>
                </div>
                
                <!-- Mobile Navigation Items -->
                <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                    @can('admin')
                    <div class="space-y-2">
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Management
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard.index') }}" 
                                class="group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                                {{ request()->is('dashboard*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                                <i class="bi bi-house mr-3 text-lg"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('management-stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('management-stock*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-box"></i> <span>Stock Management</span>
                            </a>
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('stock-opname*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-clipboard-check"></i> <span>Stock Opname</span>
                            </a>
                        </div>
                    </div>
                    @endcan

                    <div class="space-y-2">
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Operations
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('logbook.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('logbook*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-journal-text"></i> <span>Logbook</span>
                            </a>
                            @can('admin')
                            <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('order*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-cart"></i> <span>Orders</span>
                            </a>
                            <a href="{{ route('report.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('report*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-bar-chart"></i> <span>Reports</span>
                            </a>
                            <a href="user-list" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('user-list*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-people"></i> <span>Users</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed inset-x-0 bottom-0 z-30 bg-white border-t border-gray-200 lg:hidden">
        <nav class="flex items-center justify-around h-16">
            <a href="{{ route('dashboard.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('dashboard*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-house-fill text-lg mb-1"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('logbook.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('logbook*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-journal-text text-lg mb-1"></i>
                <span>Logbook</span>
            </a>
            @can('admin')
            <a href="{{ route('management-stock.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('management-stock*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-box-seam-fill text-lg mb-1"></i>
                <span>Stock</span>
            </a>
            <a href="{{ route('order.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('order*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-cart-fill text-lg mb-1"></i>
                <span>Orders</span>
            </a>
            @endcan
        </nav>
    </div>

    <!-- Main Content Area -->
    <main class="lg:ml-64 min-h-screen bg-gray-100">
        <!-- Your page content here -->
        {{ $slot ?? '' }}
    </main>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
<!-- Main Navigation -->
<div x-data="{ sidebarOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex flex-col fixed inset-y-0 left-0 w-64 bg-slate-800 text-white shadow-lg transform transition-transform duration-300">
        <!-- Logo Area -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700">
            <span class="text-xl font-bold">Rhinedottir</span>
            <button class="p-2 rounded-lg hover:bg-slate-700">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-b border-slate-700">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                    <i class="bi bi-person text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-medium">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-slate-400">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @can('admin')
            <div class="space-y-2">
                <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Management
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('dashboard.index') }}" 
                        class="group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                        {{ request()->is('dashboard*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                        <i class="bi bi-house mr-3 text-lg"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('management-stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('management-stock*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-box"></i> <span>Stock Management</span>
                    </a>
                    <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('stock-opname*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                </div>
            </div>
            @endcan

            <div class="space-y-2">
                <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Operations
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('logbook.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('logbook*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-journal-text"></i> <span>Logbook</span>
                    </a>
                    @can('admin')
                    <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('order*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-cart"></i> <span>Orders</span>
                    </a>
                    <a href="{{ route('report.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('report*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-bar-chart"></i> <span>Reports</span>
                    </a>
                    <a href="user-list" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('user-list*') ? 'bg-blue-600 text-white' : '' }}">
                        <i class="bi bi-people"></i> <span>Users</span>
                    </a>
                    @endcan
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="border-t border-slate-700 p-4 space-y-2">
            <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition mb-2 {{ Route::currentRouteName() === 'user.edit' ? 'bg-blue-600 text-white' : '' }}">
                <i class="bi bi-gear"></i> <span>Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white transition">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Menu -->
    <div x-show="sidebarOpen" 
         class="fixed inset-0 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50" 
             @click="sidebarOpen = false"
             x-transition></div>

        <!-- Slide-over menu -->
        <div class="fixed inset-y-0 left-0 w-screen max-w-xs bg-slate-800 transform transition-transform duration-300"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Mobile menu content -->
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700">
                    <span class="text-xl font-bold text-white">Menu</span>
                    <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-slate-700 text-slate-400">
                        <i class="bi bi-x text-2xl"></i>
                    </button>
                </div>
                
                <!-- Mobile Navigation Items -->
                <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                    @can('admin')
                    <div class="space-y-2">
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Management
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard.index') }}" 
                                class="group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                                {{ request()->is('dashboard*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                                <i class="bi bi-house mr-3 text-lg"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('management-stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('management-stock*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-box"></i> <span>Stock Management</span>
                            </a>
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('stock-opname*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-clipboard-check"></i> <span>Stock Opname</span>
                            </a>
                        </div>
                    </div>
                    @endcan

                    <div class="space-y-2">
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Operations
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('logbook.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('logbook*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-journal-text"></i> <span>Logbook</span>
                            </a>
                            @can('admin')
                            <a href="{{ route('order.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('order*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-cart"></i> <span>Orders</span>
                            </a>
                            <a href="{{ route('report.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('report*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-bar-chart"></i> <span>Reports</span>
                            </a>
                            <a href="user-list" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition {{ request()->is('user-list*') ? 'bg-blue-600 text-white' : '' }}">
                                <i class="bi bi-people"></i> <span>Users</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed inset-x-0 bottom-0 z-30 bg-white border-t border-gray-200 lg:hidden">
        <nav class="flex items-center justify-around h-16">
            <a href="{{ route('dashboard.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('dashboard*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-house-fill text-lg mb-1"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('logbook.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('logbook*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-journal-text text-lg mb-1"></i>
                <span>Logbook</span>
            </a>
            @can('admin')
            <a href="{{ route('management-stock.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('management-stock*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-box-seam-fill text-lg mb-1"></i>
                <span>Stock</span>
            </a>
            <a href="{{ route('order.index') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->is('order*') ? 'text-blue-600' : 'text-gray-500' }}">
                <i class="bi bi-cart-fill text-lg mb-1"></i>
                <span>Orders</span>
            </a>
            @endcan
        </nav>
    </div>

    <!-- Main Content Area -->
    <main class="lg:ml-64 min-h-screen bg-gray-100">
        <!-- Your page content here -->
        {{ $slot ?? '' }}
    </main>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
