<?php

declare(strict_types=1);

use App\Exceptions\DeletingResourceException;
use Filament\Facades\Filament;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inspector\Laravel\Middleware\WebRequestMonitoring;
use Laravel\Horizon\Console\SnapshotCommand as HorizonSnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired as SanctumPruneExpired;
use Laravel\Telescope\Console\PruneCommand as TelescopePruneCommand;
use Laravel\Telescope\TelescopeServiceProvider;
use League\Flysystem\UnableToRetrieveMetadata;
use Sentry\Laravel\Integration;
use Spatie\Backup\Commands\MonitorCommand as SpatieBackUpMonitor;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand as SpatieHealthDispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand as SpatieHealthScheduleCheckHeartbeatCommand;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware
            ->append([
                WebRequestMonitoring::class,
            ])
            ->throttleApi('60,1')
            ->redirectGuestsTo(fn () => Filament::getLoginUrl())
            ->validateCsrfTokens(except: [
                'support-bubble',
            ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        Integration::handles($exceptions);

        $exceptions
            ->dontReport([
                DeletingResourceException::class,
            ])
            ->reportable(function (UnableToRetrieveMetadata $e) {
                abort(404, trans('File not found.'));
            });

    })
    ->withSchedule(function (Schedule $schedule) {

        $schedule->command(SanctumPruneExpired::class, ['--hours' => 24])
            ->daily();

        $schedule->command(HorizonSnapshotCommand::class)
            ->everyMinute();

        $schedule->command(SpatieHealthDispatchQueueCheckJobsCommand::class)
            ->everyMinute();

        $schedule->command(SpatieBackUpMonitor::class)
            ->at('03:00');

        if (class_exists(TelescopeServiceProvider::class)) {
            $schedule->command(
                TelescopePruneCommand::class,
                ['--hours' => 48]
            )
                ->daily();
        }

        // We recommend to put this command as the very last command in your schedule.
        // https://spatie.be/docs/laravel-health/available-checks/schedule
        $schedule->command(SpatieHealthScheduleCheckHeartbeatCommand::class)
            ->everyMinute();
    })
    ->create();
