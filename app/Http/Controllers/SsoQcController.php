<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoQcController extends Controller
{
    public function callback(Request $request)
    {
        $code = (string) $request->query('code');

        if ($code === '') {
            abort(400, 'Code SSO tidak ditemukan.');
        }

        $qcVerifyUrl = rtrim(config('services.qc.url'), '/') . '/sso/verify.php';
        $secret = config('services.qc.secret');

        // 1. Verifikasi code ke QC
        $response = Http::timeout(10)->post($qcVerifyUrl, [
            'code' => $code,
            'secret' => $secret,
        ]);

        if ($response->failed()) {
            abort(401, 'SSO Gagal: ' . $response->body());
        }

        $data = $response->json();
        $payload = $data['user'] ?? null;

        if (!$payload || (empty($payload['email']) && empty($payload['username']))) {
            abort(401, 'Data user dari QC tidak valid.');
        }

        // 2. Cari user di database Laravel Reagen
        $user = null;

        if (!empty($payload['email'])) {
            $user = User::where('email', $payload['email'])->first();
        }

        if (!$user && !empty($payload['username'])) {
            $user = User::where('username', $payload['username'])->first();
        }

        // 3. Auto-create user jika belum ada di database Reagen
        if (!$user) {
            $user = User::create([
                'name' => $payload['name'] ?? $payload['username'] ?? 'User QC',
                'username' => $payload['username'] ?? null,
                'email' => $payload['email'] ?? ($payload['username'] . '@qc.local'),
                'password' => Hash::make(Str::random(40)), // Password random karena login via SSO
                'is_active' => 1,
                'role' => 'user', // Sesuaikan dengan role default di aplikasi Anda
                'guid' => (string) Str::uuid(),
            ]);
        }

        // 4. Logout user lain jika sedang login di browser yang sama
        if (Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
        }

        // 5. Login otomatis dan arahkan ke Dashboard
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}