<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\AuthSeeder;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

use function Spatie\PestPluginTestTime\testTime;

class DatabaseSeeder extends Seeder
{
    public const MONTHS = 1;

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Storage::disk(config('media-library.disk_name'))->deleteDirectory('/');

        //        if ( ! app()->isProduction()) {
        testTime()->freeze();
        testTime()->subMonth();
        //        }

        $this->call([
            AuthSeeder::class,
            BranchSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        //        if ( ! app()->isProduction()) {
        $this->call([
            OrderSeeder::class,
            CustomerSeeder::class,
        ]);
        //        }

        // reset product to base on stock
        SkuStock::query()->update([
            'type' => StockType::BASE_ON_STOCK,
            'count' => 10,
            'warning' => 7,
        ]);

        Artisan::call(OptimizeClearCommand::class);
    }
}
