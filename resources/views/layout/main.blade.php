@include('partials.header')
@include('sweetalert::alert')

<div class="app-wrapper flex min-h-screen bg-gray-50">
    {{-- Sidebar: Lebar tetap di desktop, tersembunyi/overlay di mobile (jika sidebar sudah handle toggle) --}}
    <aside class="w-64 flex-shrink-0 bg-gray-800">
        @include('partials.sidebar')
    </aside>

    {{-- Main Wrapper --}}
    <div class="main-wrapper flex-1 flex flex-col min-w-0 transition-all duration-300">
        
        {{-- Header (Sticky) --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                {{-- Anda bisa tambahkan toggle sidebar mobile, notifikasi, atau profil di sini --}}
            </div>
        </header>

        {{-- Content Area (Padding Responsif) --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
            @yield('container')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 px-6 py-4 text-center text-sm text-gray-500">
            @include('partials.footer')
            <span class="block mt-1">&copy; {{ date('Y') }} Rhinedottir App. All rights reserved.</span>
        </footer>
    </div>
</div>

@stack('scripts')