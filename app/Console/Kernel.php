<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->call(function () {
        //     // Log when the cron starts
        //     Log::info("Cron job started: Running queue:work");

        //     // Run the queue worker
        //     Artisan::call('queue:work', [
        //         '--stop-when-empty' => true, // Stops after processing all jobs
        //     ]);

        //     // Log when the cron finishes
        //     Log::info("Cron job finished: queue:work executed successfully");
        // })->name('queue-work-schedule') // Add a unique name here
        //     ->everyMinute()->withoutOverlapping();
        // $schedule->command('sos:send-alerts')->everyMinute()->withoutOverlapping();
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
