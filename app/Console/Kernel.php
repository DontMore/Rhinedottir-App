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
        // $schedule->command('inspire')->hourly();

        // GAS Auto-Push Dynamic Schedule
        // We run it every minute and check inside the command if it should really run,
        // OR we use the dynamic approach below which is cleaner if the scheduler is not cached.
        $settings = \App\Models\ApiSetting::first();
        if ($settings && $settings->push_is_active) {
            $task = $schedule->command('gas:push');
            
            switch ($settings->push_interval) {
                case 'everyMinute': $task->everyMinute(); break;
                case 'everyFiveMinutes': $task->everyFiveMinutes(); break;
                case 'everyTenMinutes': $task->everyTenMinutes(); break;
                case 'everyThirtyMinutes': $task->everyThirtyMinutes(); break;
                case 'hourly': $task->hourly(); break;
                case 'daily': $task->daily(); break;
                default: $task->daily(); break;
            }
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
