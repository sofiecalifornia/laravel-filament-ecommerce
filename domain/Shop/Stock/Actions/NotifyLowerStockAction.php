<?php

declare(strict_types=1);

namespace Domain\Shop\Stock\Actions;

use App\Filament\Resources\Shop\ProductResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Database\Eloquent\Builder;

final readonly class NotifyLowerStockAction
{
    public function execute(SkuStock $stock, Order $order): void
    {
        $admins = Admin::role(config('domain.access.role.admin'), guard: 'admin')
            ->when($order->admin, function (Builder $query, Admin $admin) {
                $query
                    ->orWhere((new Admin())->getKeyName(), $admin->getKey());
            })
            ->get();

        FilamentNotification::make()
            ->title('Sku ['.$stock->sku->code.'] stock warning')
            ->body(
                sprintf(
                    'Sku [%s] has only [%s] available stock, after order [%s] created.',
                    $stock->sku->code,
                    $stock->count,
                    $order->receipt_number,
                )
            )
            ->icon('heroicon-o-exclamation-circle')
            ->actions([
                Action::make('view')
                    ->label('View Sku Stock')
                    ->button()
                    ->markAsRead()
                    ->url(ProductResource::getUrl('edit', [$stock->sku->product])),
            ])
            ->sendToDatabase($admins);
    }
}
