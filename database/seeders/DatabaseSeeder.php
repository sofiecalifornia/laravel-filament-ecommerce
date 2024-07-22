<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\AuthSeeder;
use Domain\Shop\Order\Actions\OrderInvoiceAction;
use Domain\Shop\Stock\Enums\StockType;
use Domain\Shop\Stock\Models\SkuStock;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if ('redis' === config('queue.default')) {
            Artisan::call('app:horizon:clear');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Storage::disk(config('media-library.disk_name'))
            ->deleteDirectory(config('media-library.prefix'));
        Storage::disk(config('invoices.disk'))
            ->deleteDirectory(OrderInvoiceAction::FOLDER);

        activity()->disableLogging();

        File::deleteDirectory(storage_path('media-library/temp'));

        Mail::fake();
        Notification::fake();

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
            'type' => StockType::base_on_stock,
            'count' => 10,
            'warning' => 7,
        ]);

        Artisan::call(OptimizeClearCommand::class);
    }
}
