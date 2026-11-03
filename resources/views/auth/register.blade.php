@extends('layout.main')
@section('title', 'Register New User')
@section('container')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Register New User</h2>
            <p class="text-sm text-gray-500 mt-1">Create a new account and assign access role.</p>
        </div>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">User Information</h3>
                <p class="text-xs text-gray-500">Fields marked <span class="text-red-500 font-semibold">*</span> are required</p>
            </div>
        </div>

        <form method="POST" action="{{ route('user.store') }}" class="p-6 space-y-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter your full name"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Choose a username"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Minimum 6 characters"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="repassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" id="repassword" name="repassword" required placeholder="Re-enter your password"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('repassword')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- ✅ ROLE dengan opsi PIC -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select id="role" name="role" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a role</option>
                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Analis" {{ old('role') == 'Analis' ? 'selected' : '' }}>Analis</option>
                    <option value="PIC" {{ old('role') == 'PIC' ? 'selected' : '' }}>PIC</option>
                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

                <!-- Role description chips -->
                <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="px-3 py-2 rounded-lg bg-blue-50 border border-blue-100">
                        <p class="text-xs font-semibold text-blue-700">Admin</p>
                        <p class="text-[11px] text-blue-600/80">Full management of stock & users</p>
                    </div>
                    <div class="px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-100">
                        <p class="text-xs font-semibold text-emerald-700">Analis</p>
                        <p class="text-[11px] text-emerald-600/80">Logbook & reagent usage</p>
                    </div>
                    <div class="px-3 py-2 rounded-lg bg-amber-50 border border-amber-100">
                        <p class="text-xs font-semibold text-amber-700">PIC</p>
                        <p class="text-[11px] text-amber-600/80">Person in charge — approval & monitoring</p>
                    </div>
                    <div class="px-3 py-2 rounded-lg bg-violet-50 border border-violet-100">
                        <p class="text-xs font-semibold text-violet-700">Super Admin</p>
                        <p class="text-[11px] text-violet-600/80">Full access across organizations</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring ring-blue-300 disabled:opacity-25 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Register User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection