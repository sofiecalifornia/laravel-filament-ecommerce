<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\ExportFinishedListener;
use App\Listeners\SettingsActivityLogListener;
use App\Listeners\SupportBubbleSubmittedToSentryFeedbackListener;
use Domain\Access\Admin\Models\Admin;
use Domain\Access\Admin\Observers\AdminObserver;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Branch\Observers\BranchObserver;
use Domain\Shop\Brand\Models\Brand;
use Domain\Shop\Brand\Observers\BrandObserver;
use Domain\Shop\Category\Models\Category;
use Domain\Shop\Category\Observers\CategoryObserver;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Customer\Observers\CustomerObserver;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Order\Observers\OrderItemObserver;
use Domain\Shop\Product\Models\Attribute;
use Domain\Shop\Product\Models\Product;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Product\Observers\AttributeObserver;
use Domain\Shop\Product\Observers\ProductObserver;
use Domain\Shop\Product\Observers\SkuObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use pxlrbt\FilamentExcel\Events\ExportFinishedEvent;
use Spatie\LaravelSettings\Events\SavingSettings;
use Spatie\SupportBubble\Events\SupportBubbleSubmittedEvent;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string<\Illuminate\Database\Eloquent\Model>, array<int, class-string>> */
    protected $observers = [
        Admin::class => [AdminObserver::class],
        Attribute::class => [AttributeObserver::class],
        Branch::class => [BranchObserver::class],
        Brand::class => [BrandObserver::class],
        Category::class => [CategoryObserver::class],
        Customer::class => [CustomerObserver::class],
        OrderItem::class => [OrderItemObserver::class],
        Sku::class => [SkuObserver::class],
        Product::class => [ProductObserver::class],
    ];

    /** @var array<class-string, array<int, class-string>> */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SavingSettings::class => [
            SettingsActivityLogListener::class,
        ],
        ExportFinishedEvent::class => [
            ExportFinishedListener::class,
        ],
        SupportBubbleSubmittedEvent::class => [
            SupportBubbleSubmittedToSentryFeedbackListener::class,
        ],
    ];

    public function boot(): void
    {

    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
