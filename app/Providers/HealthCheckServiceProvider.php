<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Lloricode\SpatieImageOptimizerHealthCheck\ImageOptimizerCheck;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
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
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            ScheduleCheck::new(),
            SslCertificationExpiredCheck::new()
                ->url(config('app.url'))
                ->warnWhenSslCertificationExpiringDay(24)
                ->failWhenSslCertificationExpiringDay(14),
            UsedDiskSpaceCheck::new(),
            OptimizedAppCheck::new(),
            DatabaseConnectionCountCheck::new(),
            QueueCheck::new(),
            // https://github.com/spatie/image-optimizer#optimization-tools
            ImageOptimizerCheck::new()
                ->checkJPEGOPTIM()
                ->checkOPTIPNG()
                ->checkPNGQUANT(),
        ]);
    }
}
