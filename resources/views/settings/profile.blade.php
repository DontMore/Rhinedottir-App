@extends('layout.main')
@section('title', 'Profile Settings')

@section('container')
<div class="flex flex-col md:flex-row gap-8">
    {{-- Navigation Sidebar --}}
    @include('settings.partials.navigation')

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Profile Settings</h2>
            <p class="text-sm text-gray-500 mt-1">Update your account information and password.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="lg:col-span-2">
                <form action="{{ route('settings.profile.update') }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    @csrf
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Username (Read-only for safety usually) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                                <input type="text" value="{{ $user->username }}" disabled
                                    class="block w-full rounded-lg border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                                <p class="mt-1 text-[10px] text-gray-400">Username cannot be changed for security.</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <hr class="border-gray-100">

                        <!-- Password Change Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800">Change Password</h3>
                            <p class="text-xs text-gray-500">Leave these fields blank if you don't want to change your password.</p>
                            
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                                    <input type="password" id="new_password" name="new_password"
                                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    @error('new_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="space-y-6">
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Account Security</h4>
                    <ul class="text-xs text-blue-700 space-y-2 list-disc list-inside">
                        <li>Use a strong password with at least 8 characters.</li>
                        <li>Change your password regularly for better security.</li>
                        <li>Ensure your email is active for system notifications.</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Current Role</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
