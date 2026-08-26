<?php
return [
	'browser_url'   => env('SSO_BROWSER_URL', 'http://localhost:8083'),
	'server_url'    => env('SSO_SERVER_URL', 'http://127.0.0.1:8083'),
	'client_id'     => env('SSO_CLIENT_ID'),
	'client_secret' => env('SSO_CLIENT_SECRET'),
	'redirect_uri'  => env('SSO_REDIRECT_URI'),

	// Mapping: role di SSO (menu Akses Aplikasi) → role lokal Laravel
	'role_map' => [
		'admin'  => 'Admin',
		'editor' => 'Analis',
		'viewer' => 'Analis',
	],

	// Role default untuk user baru (auto-provisioning)
	'default_role' => 'Analis',

	// true = role user selalu disinkronkan dari SSO tiap login
	// false = role hanya diatur saat user dibuat / dikelola manual di Laravel
	'sync_role' => env('SSO_SYNC_ROLE', false),
];