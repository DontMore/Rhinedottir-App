@include('partials.header')
@include('sweetalert::alert')

{{-- Tambahkan padding top jika topnav aktif --}}
<style>
    @media (max-width: 991px) {
        body {
            padding-top: 60px !important;
        }
    }
    /* Collapsible Sidebar Styles */
    .sidebar-collapsed aside {
        width: 80px !important;
    }
    .sidebar-collapsed .main-wrapper {
        margin-left: 0 !important;
    }
    .sidebar-collapsed #sidebar-content {
        width: 80px !important;
    }
    .sidebar-collapsed .sidebar-text {
        display: none;
    }
    .sidebar-collapsed .sidebar-dropdown-icon {
        display: none;
    }
    .sidebar-collapsed .sidebar-item-content {
        justify-content: center;
    }
</style>

<div id="app-container" class="app-wrapper flex min-h-screen bg-gray-50 transition-all duration-300">
    
    {{-- Sidebar: Lebar tetap di desktop, tersembunyi/overlay di mobile (jika sidebar sudah handle toggle) --}}
    <aside class="w-64 flex-shrink-0 transition-all duration-300 bg-gray-900 hidden lg:block">
        @include('partials.sidebar')
    </aside>
    {{-- Main Wrapper --}}
     <div class= "main-wrapper flex-1 flex flex-col min-w-0 transition-all duration-300 " >
        
        {{-- Header (Sticky) --}}
         <header class="bg-white border-b border-gray-200 sticky top-0 z-30 px-4 py-2 sm:px-6 shadow-sm">
             <div class="flex items-center justify-between">
                 <div class="flex items-center gap-4">
                     {{-- Sidebar Toggle Desktop --}}
                     <button id="toggleSidebar" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 hidden lg:flex items-center justify-center">
                         <i class="fa-solid fa-bars-staggered text-xl"></i>
                     </button>
                     <h1 class="text-lg font-semibold text-gray-800 hidden sm:block">@yield('title', 'Dashboard')</h1>
                 </div>
                 
                 {{-- Topbar Right Actions --}}
                 <div class="flex items-center gap-3">
                     {{-- Notification or other icons can go here --}}
                     
                     {{-- User Profile Dropdown --}}
                     <div class="relative">
                         <button type="button" class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 transition-all duration-200" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown">
                             <span class="sr-only">Open user menu</span>
                             @php
                                 $userName = auth()->check() ? auth()->user()->name : 'Guest';
                                 $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=random&color=fff";
                             @endphp
                             <img class="w-9 h-9 rounded-full border-2 border-white shadow-sm" src="{{ $avatarUrl }}" alt="user photo">
                         </button>
                         
                         <!-- Dropdown menu -->
                         <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-100 min-w-[200px]" id="user-dropdown">
                             <div class="px-4 py-3">
                                 <span class="block text-sm text-gray-900 font-bold">@auth {{ auth()->user()->name }} @else Guest @endauth</span>
                                 <span class="block text-xs text-gray-500 truncate mt-0.5">@auth {{ auth()->user()->email }} @else guest@example.com @endauth</span>
                             </div>
                             <ul class="py-2" aria-labelledby="user-menu-button">
                                 <li>
                                     <a href="{{ route('settings.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                         <i class="fa-solid fa-user-gear mr-3 opacity-70"></i>
                                         Profile Settings
                                     </a>
                                 </li>
                                 @can('admin')
                                 <li>
                                     <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                         <i class="fa-solid fa-sliders mr-3 opacity-70"></i>
                                         System Settings
                                     </a>
                                 </li>
                                 @endcan
                             </ul>
                             <div class="py-2">
                                 <form action="{{ route('logout') }}" method="POST">
                                     @csrf
                                     <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                         <i class="fa-solid fa-right-from-bracket mr-3 opacity-70"></i>
                                         Sign out
                                     </button>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </header>

        {{-- Content Area (Padding Responsif) --}}
         <main class= "flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto " >
            @yield('container')
         </main >

        {{-- Footer --}}
         <footer class= "bg-white border-t border-gray-200 px-6 py-4 text-center text-sm text-gray-500 " >
            @include('partials.footer')
             <span class= "block mt-1 " > &copy; {{ date('Y') }} Rhinedottir App. All rights reserved. </span >
         </footer >
     </div >
</div>
@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleSidebar');
        const appContainer = document.getElementById('app-container');
        
        // Cek localStorage untuk state sebelumnya
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            appContainer.classList.add('sidebar-collapsed');
        }
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                appContainer.classList.toggle('sidebar-collapsed');
                // Simpan state
                localStorage.setItem('sidebar-collapsed', appContainer.classList.contains('sidebar-collapsed'));
            });
        }
    });
</script>