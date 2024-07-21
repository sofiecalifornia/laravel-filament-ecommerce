<?php

declare(strict_types=1);

namespace App\Console;

use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\SnapshotCommand as HorizonSnapshotCommand;
use Laravel\Telescope\Console\PruneCommand as TelescopePruneCommand;
use Spatie\Backup\Commands\MonitorCommand as SpatieBackUpMonitor;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand as SpatieHealthDispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand as SpatieHealthScheduleCheckHeartbeatCommand;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(HorizonSnapshotCommand::class)->everyMinute();

        $schedule->command(SpatieHealthDispatchQueueCheckJobsCommand::class)->everyMinute();

        $schedule->command(SpatieBackUpMonitor::class)->daily()->at('03:00');

        if (class_exists('\Laravel\Telescope\TelescopeServiceProvider')) {
            $schedule->command(TelescopePruneCommand::class, ['--hours' => 48])
                ->daily();
        }

        // We recommend to put this command as the very last command in your schedule.
        // https://spatie.be/docs/laravel-health/available-checks/schedule
        $schedule->command(SpatieHealthScheduleCheckHeartbeatCommand::class)->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        // TODO: change this in actual production site
        return 'Asia/Manila';
    }
}
