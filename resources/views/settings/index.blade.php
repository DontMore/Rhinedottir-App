@extends('layout.main')
@section('title', 'Email Settings')

@section('container')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Email Configuration</h2>
        <p class="text-sm text-gray-500 mt-1">Manage SMTP settings for password reset and system notifications.</p>
    </div>

    <!-- Form Card -->
    <form action="{{ route('settings.email.update') }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <!-- SMTP Host -->
            <div>
                <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Host <span class="text-red-500">*</span></label>
                <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings->mail_host ?? 'mail.onexternal.com') }}" required placeholder="e.g., smtp.office365.com"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="mt-1.5 text-xs text-gray-500">Example: mail.onexternal.com, smtp.zoho.com, smtp.office365.com</p>
                @error('mail_host') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Port & Encryption Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-1.5">Port <span class="text-red-500">*</span></label>
                    <select id="mail_port" name="mail_port" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="465" {{ old('mail_port', $settings->mail_port ?? '465') == '465' ? 'selected' : '' }}>465 (SSL)</option>
                        <option value="587" {{ old('mail_port', $settings->mail_port ?? '') == '587' ? 'selected' : '' }}>587 (TLS)</option>
                        <option value="25" {{ old('mail_port', $settings->mail_port ?? '') == '25' ? 'selected' : '' }}>25 (Unencrypted)</option>
                        <option value="2525" {{ old('mail_port', $settings->mail_port ?? '') == '2525' ? 'selected' : '' }}>2525 (Alternative)</option>
                    </select>
                    @error('mail_port') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-1.5">Encryption</label>
                    <select id="mail_encryption" name="mail_encryption"
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption ?? 'ssl') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="tls" {{ old('mail_encryption', $settings->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="" {{ old('mail_encryption', $settings->mail_encryption ?? '') == '' ? 'selected' : '' }}>None</option>
                    </select>
                    @error('mail_encryption') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Email Username -->
            <div>
                <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-1.5">Email Username <span class="text-red-500">*</span></label>
                <input type="email" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings->mail_username ?? '') }}" required placeholder="admin@onexternal.com"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('mail_username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Email Password -->
            <div>
                <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-1.5">Email Password</label>
                <input type="password" id="mail_password" name="mail_password" placeholder="Leave blank to keep current password"
                    {{ $settings ? '' : 'required' }}
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="mt-1.5 text-xs text-gray-500">Leave empty if you don't want to change the password.</p>
                @error('mail_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- From Address -->
            <div>
                <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-1.5">From Address <span class="text-red-500">*</span></label>
                <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address ?? '') }}" required placeholder="noreply@onexternal.com"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('mail_from_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection