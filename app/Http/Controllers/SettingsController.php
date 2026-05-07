<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailSetting;
use App\Models\ApiSetting;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function emailSettings()
    {
        $settings = EmailSetting::first();
        return view('settings.index', compact('settings'));
    }

    public function profileSettings()
    {
        $user = auth()->user();
        return view('settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->guid . ',guid',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Update Name & Email
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update Password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        Alert::success('Success', 'Profile updated successfully');
        return back();
    }

    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|in:25,465,587,2525',
            'mail_encryption' => 'nullable|in:ssl,tls',
            'mail_username' => 'required|email',
            'mail_password' => 'required',
            'mail_from_address' => 'required|email',
        ]);

        $settings = EmailSetting::first();

        if (!$settings) {
            $settings = EmailSetting::create([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_encryption' => $request->mail_encryption,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_from_address' => $request->mail_from_address,
            ]);
        } else {
            $settings->update([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_encryption' => $request->mail_encryption,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_from_address' => $request->mail_from_address,
            ]);
        }

        // Refresh config runtime
        EmailSetting::updateConfig();

        // Clear cache
        Artisan::call('config:clear');

        Alert::success('Success', 'Email settings updated successfully');
        return back();
    }

    public function apiSettings()
    {
        $settings = ApiSetting::first();
        return view('settings.api', compact('settings'));
    }

    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'gas_web_app_url' => 'nullable|url',
            'is_active' => 'boolean',
            'push_is_active' => 'boolean',
            'push_interval' => 'required|in:everyMinute,everyFiveMinutes,everyTenMinutes,everyThirtyMinutes,hourly,daily',
        ]);

        $settings = ApiSetting::first();
        $isActive = $request->has('is_active') ? $request->is_active : false;
        $pushIsActive = $request->has('push_is_active') ? $request->push_is_active : false;

        $data = [
            'gas_web_app_url' => $request->gas_web_app_url,
            'is_active' => $isActive,
            'push_is_active' => $pushIsActive,
            'push_interval' => $request->push_interval,
        ];

        if (!$settings) {
            ApiSetting::create($data);
        } else {
            $settings->update($data);
        }

        Alert::success('Success', 'API settings updated successfully');
        return back();
    }

    public function regenerateApiToken()
    {
        $settings = ApiSetting::first();

        if (!$settings) {
            $settings = ApiSetting::create([
                'is_active' => false,
            ]);
        } else {
            $settings->update([
                'api_token' => \Illuminate\Support\Str::random(60)
            ]);
        }

        Alert::success('Success', 'API Token regenerated successfully');
        return back();
    }

    public function schedulerIndex()
    {
        return view('settings.scheduler');
    }

    /**
     * Start Laravel Scheduler via UI (Non-blocking)
     */
    public function startScheduler()
    {
        $exePath = storage_path('tools/LaravelScheduler.exe');

        if (!file_exists($exePath)) {
            Alert::error('Error', 'File LaravelScheduler.exe tidak ditemukan di storage/tools/');
            return back();
        }

        // Cegah eksekusi ganda
        if (file_exists(storage_path('scheduler.lock'))) {
            Alert::info('Info', 'Scheduler sudah berjalan di background.');
            return back();
        }

        // 🚀 DETACH PROCESS: Jalankan di background tanpa blocking PHP
        // start /B = tanpa jendela baru, pclose(popen()) = langsung release ke OS
        $cmd = 'start /B "" "' . $exePath . '"';
        pclose(popen($cmd, 'r'));

        Alert::success('Berhasil', 'Perintah start dikirim. Scheduler akan aktif dalam beberapa detik.');
        Log::info('Scheduler start command triggered via UI by ' . auth()->user()->email);

        return back();
    }

    /**
     * Stop Scheduler via UI (hapus lock file)
     */
    public function stopScheduler()
    {
        // 1. Paksa hentikan proses di Windows
        // Hapus '2>nul' jika ingin melihat output error di log
        exec('taskkill /F /IM LaravelScheduler.exe /T 2>nul', $out1, $code1);
        exec('taskkill /F /IM pythonw.exe /T 2>nul', $out2, $code2);

        // 2. Bersihkan file status agar tidak bentrok saat restart
        @unlink(storage_path('scheduler.lock'));
        @unlink(storage_path('scheduler_heartbeat.json'));

        Alert::success('Berhasil', 'Scheduler telah dihentikan. Proses & file status dibersihkan.');
        Log::info('Scheduler forcefully stopped via UI by ' . auth()->user()->email);

        return back();
    }

    /**
     * Get real-time scheduler status (AJAX)
     */
    public function getSchedulerStatus()
    {
        $heartbeat = storage_path('scheduler_heartbeat.json');
        $logFile   = storage_path('logs/scheduler.log');
        $status    = ['running' => false, 'last_run' => 'Belum pernah berjalan', 'logs' => []];

        if (file_exists($heartbeat)) {
            $data = json_decode(file_get_contents($heartbeat), true);
            if ($data && isset($data['last_run'])) {
                $lastRun = \Carbon\Carbon::parse($data['last_run']);
                $status['running'] = $lastRun->diffInMinutes(now()) < 3; // <3 menit = masih jalan
                $status['last_run'] = $lastRun->format('Y-m-d H:i:s') . ' (' . $lastRun->diffForHumans() . ')';
            }
        }

        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $status['logs'] = array_slice($lines, -8); // 8 log terakhir
        }

        return response()->json($status);
    }
}
