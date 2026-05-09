<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cek apakah fitur Push aktif di database
        $settings = \App\Models\ApiSetting::first();

        if ($settings && $settings->push_is_active) {
            // ✅ PERBAIKAN: Gunakan everyMinute()
            // Biarkan Python yang mengatur jeda waktu (delay).
            // Kernel cukup memastikan tugas "selalu siap" (ready) saat dipanggil.
            $schedule->command('gas:push')->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
