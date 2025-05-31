<x-app-layout>
    <div class="p-6">
        <form method="POST" action="{{ route('email.settings.update') }}">
            @csrf
            <div class="mb-4">
                <label for="mail_username">Email Username</label>
                <input type="email" name="mail_username" value="{{ $settings->mail_username ?? '' }}" required>
            </div>
            <div class="mb-4">
                <label for="mail_password">Email Password</label>
                <input type="password" name="mail_password" required>
            </div>
            <div class="mb-4">
                <label for="mail_from_address">From Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings->mail_from_address ?? '' }}" required>
            </div>
            <button type="submit">Save Settings</button>
        </form>
    </div>
</x-app-layout>
