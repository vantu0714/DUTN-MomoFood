<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\UpdateProductExpirationStatus;
use App\Console\Commands\ServeFixedCommand; // thêm dòng này

class Kernel extends ConsoleKernel
{
    /**
     * Đăng ký custom commands.
     */
    protected $commands = [
        ServeFixedCommand::class,
        UpdateProductExpirationStatus::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(UpdateProductExpirationStatus::class)->daily();
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
