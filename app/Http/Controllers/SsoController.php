<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoController extends Controller
{
	/**
	 * Tombol "Login dengan SSO" → redirect browser ke SSO Server
	 */
	public function login()
	{
		$params = http_build_query([
			'client_id'     => config('sso.client_id'),
			'redirect_uri'  => config('sso.redirect_uri'),
			'response_type' => 'code',
			'scope'         => 'openid profile',
		]);

		return redirect()->away(config('sso.browser_url') . '/oauth/authorize?' . $params);
	}

	/**
	 * Callback dari SSO membawa ?code=...
	 */
	public function callback(Request $request)
	{
		$code = $request->query('code');
		if (!$code) {
			return redirect('/login')->with('loginError', 'Login SSO gagal: code tidak diterima.');
		}

		// 1) Tukar code → token (server-to-server, dari dalam Podman ke host)
		try {
			$tokenRes = Http::timeout(10)->post(config('sso.server_url') . '/oauth/token', [
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'client_id'     => config('sso.client_id'),
				'client_secret' => config('sso.client_secret'),
			]);
		} catch (\Exception $e) {
			return redirect('/login')->with('loginError', 'Login SSO gagal: ' . $e->getMessage());
		}

		if ($tokenRes->failed() || empty($tokenRes->json('access_token'))) {
			$err = $tokenRes->json('error') ?? 'Response SSO tidak valid.';
			return redirect('/login')->with('loginError', 'Login SSO gagal: ' . $err);
		}

		$accessToken = $tokenRes->json('access_token');

		// 2) Ambil profil user dari SSO
		try {
			$userRes = Http::timeout(10)->withToken($accessToken)
				->get(config('sso.server_url') . '/oauth/userinfo');
		} catch (\Exception $e) {
			return redirect('/login')->with('loginError', 'Login SSO gagal: ' . $e->getMessage());
		}

		if ($userRes->failed() || empty($userRes->json('email'))) {
			return redirect('/login')->with('loginError', 'Login SSO gagal: profil user tidak terbaca.');
		}

		$ssoUser = $userRes->json();

		// 3) Cari user lokal berdasarkan email, atau buat baru (auto-provisioning)
		$user = User::firstWhere('email', $ssoUser['email']);

		if (!$user) {
			$user = User::create([
				'name'     => $ssoUser['name'] ?? Str::before($ssoUser['email'], '@'),
				'username' => $this->uniqueUsername($ssoUser['email']),
				'email'    => $ssoUser['email'],
				'password' => Hash::make(Str::random(40)), // acak, tidak bisa login manual
				'role'     => $this->mapRole($ssoUser['app_role'] ?? null),
			]);
		} elseif (config('sso.sync_role')) {
			// Opsional: jadikan SSO sebagai sumber kebenaran role
			$mapped = $this->mapRole($ssoUser['app_role'] ?? null);
			if ($user->role !== $mapped) {
				$user->role = $mapped;
				$user->save();
			}
		}

		// 4) Cek status aktif — SAMA PERSIS seperti authenticate()
		if (!$user->is_active) {
			return redirect('/login')->with('loginError', 'Akun Anda tidak aktif. Hubungi administrator.');
		}

		// 5) Login & amankan session
		Auth::login($user);
		$request->session()->regenerate();

		// 6) Redirect berdasarkan role — SAMA PERSIS seperti authenticate()
		return $this->redirectByRole($user);
	}

	/**
	 * Meniru logika redirect per role dari AuthenticationController@authenticate
	 */
	private function redirectByRole($user)
	{
		switch (strtolower($user->role)) {
			case 'admin':
				return redirect()->route('dashboard.index');
			case 'analis':
				return redirect()->route('logbook.index');
			case 'superadmin':
				return redirect()->route('superadmin.index');
			default:
				return redirect('/');
		}
	}

	/**
	 * Mapping role SSO → role lokal (Admin/Analis/superadmin)
	 */
	private function mapRole($appRole)
	{
		$map = config('sso.role_map', []);
		return $map[strtolower((string) $appRole)] ?? config('sso.default_role', 'Analis');
	}

	/**
	 * Generate username unik dari email (kolom username wajib unik)
	 */
	private function uniqueUsername($email)
	{
		$base = Str::before($email, '@');
		$username = $base;
		$i = 1;
		while (User::where('username', $username)->exists()) {
			$username = $base . $i;
			$i++;
		}
		return $username;
	}
}