<?php

declare(strict_types=1);

namespace App\Providers;

use App\Jobs\QueueJobPriority;
use Illuminate\Support\ServiceProvider;
use Lloricode\SpatieImageOptimizerHealthCheck\ImageOptimizerCheck;
use Lloricode\SpatieImageOptimizerHealthCheck\Optimizer;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;
use VictoRD11\SslCertificationHealthCheck\SslCertificationExpiredCheck;

/** @property \Illuminate\Foundation\Application $app */
class HealthCheckServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Health::checks([
            CacheCheck::new(),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
            DatabaseCheck::new(),
            DatabaseConnectionCountCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            ScheduleCheck::new(),
            SslCertificationExpiredCheck::new()
                ->url(config('app.url'))
                ->warnWhenSslCertificationExpiringDay(24)
                ->failWhenSslCertificationExpiringDay(14),
            UsedDiskSpaceCheck::new(),
            QueueCheck::new()
                ->onQueue(QueueJobPriority::PRIORITIES),
            RedisCheck::new(),
            RedisMemoryUsageCheck::new()
                ->failWhenAboveMb(1000),
            HorizonCheck::new(),
            OptimizedAppCheck::new(),
            SecurityAdvisoriesCheck::new(),
            ImageOptimizerCheck::new()
                ->addChecks([
                    Optimizer::JPEGOPTIM,
                    Optimizer::OPTIPNG,
                    Optimizer::PNGQUANT,
                ]),
        ]);
    }
}
