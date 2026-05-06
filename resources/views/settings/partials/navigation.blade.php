<div class="w-full md:w-64 flex-shrink-0">
    <nav class="space-y-1">
        <a href="{{ route('settings.profile') }}" 
           class="{{ request()->routeIs('settings.profile') ? 'bg-blue-50 text-blue-700 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center px-4 py-3 text-sm font-medium border-l-4 transition-all duration-200">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('settings.profile') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile Settings
        </a>

        @can('admin')
        <a href="{{ route('settings.index') }}" 
           class="{{ request()->routeIs('settings.index') || request()->routeIs('settings.email') ? 'bg-blue-50 text-blue-700 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center px-4 py-3 text-sm font-medium border-l-4 transition-all duration-200">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('settings.index') || request()->routeIs('settings.email') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Email Configuration
        </a>

        <a href="{{ route('settings.api') }}" 
           class="{{ request()->routeIs('settings.api') ? 'bg-blue-50 text-blue-700 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center px-4 py-3 text-sm font-medium border-l-4 transition-all duration-200">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('settings.api') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            API Configuration
        </a>

        <a href="{{ route('settings.backup') }}" 
           class="{{ request()->routeIs('settings.backup') ? 'bg-blue-50 text-blue-700 border-blue-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center px-4 py-3 text-sm font-medium border-l-4 transition-all duration-200">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('settings.backup') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Backup & Export
        </a>
        @endcan
    </nav>
</div>
