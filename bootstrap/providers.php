<?php

declare(strict_types=1);

return [
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\BranchPanelProvider::class,
    App\Providers\Filament\CommonPanelProvider::class,

    App\Providers\ActivityLogPipeChangesProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\HealthCheckServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    /*
     * domain
     */
    Domain\Access\Role\RoleServiceProvider::class,
    Domain\Shop\Order\OrderServiceProvider::class,

    /*
     * support
     */
    Support\Google2FA\Google2FAServiceProvider::class,
    Support\ReceiptPrinter\PrinterServiceProvider::class,
];
