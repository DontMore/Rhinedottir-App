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
        // Mengambil setting pertama atau buat baru jika kosong
        $settings = ApiSetting::first() ?? new ApiSetting();
        return view('settings.api', compact('settings'));
    }

    /**
     * Menyimpan pengaturan API Settings (GAS Integration & Push Scheduler)
     */
    public function updateApiSettings(Request $request)
    {
        // 1. Validasi Data Input
        $validated = $request->validate([
            'gas_web_app_url'   => 'nullable|url',
            // ✅ Validasi sebagai string dari daftar yang diizinkan (bukan integer)
            'push_interval'     => 'required|in:everyMinute,everyFiveMinutes,everyTenMinutes,everyThirtyMinutes,hourly,daily',
            'selected_tables'   => 'nullable|array',
            'selected_tables.*' => 'in:reagens,logbook_reagens,stock_reagens,reagens_in,orders,stock_opnames,order_recommendations', // ✅ Tambahkan ini
        ]);

        // 2. Siapkan Data untuk Disimpan
        // Checkbox HTML tidak dikirim jika tidak dicentang. 
        // Kita gunakan $request->has() untuk mengubahnya menjadi boolean yang konsisten.
        $data = [
            'gas_web_app_url'   => $validated['gas_web_app_url'] ?? null,
            'is_active'         => $request->has('is_active'),
            'push_is_active'    => $request->has('push_is_active'),
            'push_interval'     => $validated['push_interval'],
            'selected_tables'   => $validated['selected_tables'] ?? [],
        ];

        // 3. Update Record yang Ada atau Buat Baru
        $settings = ApiSetting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            ApiSetting::create($data);
        }

        // 4. Notifikasi & Redirect Kembali
        Alert::success('Success', 'API settings updated successfully');
        return back();
    }

    public function regenerateToken()
    {
        $settings = ApiSetting::first();
        if ($settings) {
            $settings->api_token = \Illuminate\Support\Str::random(60);
            $settings->save();
            Alert::success('Berhasil', 'API Token baru telah dibuat.');
        }
        return back();
    }

    public function schedulerIndex()
    {
        return view('settings.scheduler');
    }

    /**
     * Start Laravel Scheduler via UI (Non-blocking & PID-Aware)
     */
    public function startScheduler()
    {
        $exePath = storage_path('tools/LaravelScheduler.exe');
        $lockFile = storage_path('scheduler.lock');

        if (!file_exists($exePath)) {
            Alert::error('Error', 'File LaravelScheduler.exe tidak ditemukan di storage/tools/');
            return back();
        }

        // ✅ CEK PROSES HIDUP vs STALE LOCK
        if (file_exists($lockFile)) {
            $pid = trim(file_get_contents($lockFile));
            // Cek apakah PID tersebut masih terdaftar di Task Manager Windows
            $checkProcess = @exec("tasklist /FI \"PID eq $pid\" /NH");

            if (strpos($checkProcess, $pid) !== false) {
                Alert::info('Info', 'Scheduler benar-benar sedang berjalan aktif di background.');
                return back();
            }

            // Jika PID tidak ditemukan, ini adalah STALE LOCK (sisa crash/kill paksa)
            @unlink($lockFile);
            @unlink(storage_path('scheduler_heartbeat.json'));
        }

        //  DETACH PROCESS: Jalankan di background tanpa blocking PHP
        $cmd = 'start /B "" "' . $exePath . '"';
        pclose(popen($cmd, 'r'));

        Alert::success('Berhasil', 'Perintah start dikirim. Scheduler akan aktif dalam 3-5 detik.');
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
