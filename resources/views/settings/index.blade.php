@extends('layout.main')
@section('container')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Email Reset Password Settings</h2>
            
            <form method="POST" action="{{ route('settings.email.update') }}" class="space-y-6">
                @csrf
                
                <!-- SMTP Host -->
                <div>
                    <label for="mail_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                    <input id="mail_host" type="text" name="mail_host" 
                        value="{{ old('mail_host', $settings->mail_host ?? 'mail.onexternal.com') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        required />
                    <p class="text-xs text-gray-500 mt-1">Contoh: mail.onexternal.com, smtp.zoho.com, smtp.office365.com</p>
                    @error('mail_host')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Port dan Encryption -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700">Port</label>
                        <select id="mail_port" name="mail_port" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="465" {{ old('mail_port', $settings->mail_port ?? '') == '465' ? 'selected' : '' }}>465 (SSL)</option>
                            <option value="587" {{ old('mail_port', $settings->mail_port ?? '') == '587' ? 'selected' : '' }}>587 (TLS)</option>
                            <option value="25" {{ old('mail_port', $settings->mail_port ?? '') == '25' ? 'selected' : '' }}>25 (Tidak dienkripsi)</option>
                            <option value="2525" {{ old('mail_port', $settings->mail_port ?? '') == '2525' ? 'selected' : '' }}>2525 (Alternatif)</option>
                        </select>
                        @error('mail_port')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                        <select id="mail_encryption" name="mail_encryption" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ old('mail_encryption', $settings->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="" {{ old('mail_encryption', $settings->mail_encryption ?? '') == '' ? 'selected' : '' }}>None</option>
                        </select>
                        @error('mail_encryption')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Username -->
                <div>
                    <label for="mail_username" class="block text-sm font-medium text-gray-700">Email Username</label>
                    <input id="mail_username" type="email" name="mail_username" 
                        value="{{ old('mail_username', $settings->mail_username ?? '') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        required />
                    @error('mail_username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Password -->
                <div>
                    <label for="mail_password" class="block text-sm font-medium text-gray-700">Email Password</label>
                    <input id="mail_password" type="password" name="mail_password" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        {{ $settings ? '' : 'required' }} />
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                    @error('mail_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Address -->
                <div>
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Address</label>
                    <input id="mail_from_address" type="email" name="mail_from_address" 
                        value="{{ old('mail_from_address', $settings->mail_from_address ?? '') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                        required />
                    @error('mail_from_address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection