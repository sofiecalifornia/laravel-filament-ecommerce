<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Pipes\OrderCreated;

use App\Filament\Resources\Shop\OrderResource;
use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Order\DataTransferObjects\OrderPipelineData;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class NotifyAdminPipe
{
    public function handle(OrderPipelineData $orderPipelineData, callable $next): OrderPipelineData
    {
        $admins = Admin::role(config('domain.access.role.admin'), guard: 'admin')
            ->when($orderPipelineData->order->admin, function (Builder $query, Admin $admin) {
                $query
                    ->orWhere((new Admin())->getKeyName(), $admin->getKey());
            })
            ->get();

        Notification::make()
            ->title('Order '.$orderPipelineData->order->receipt_number.' created')
            ->body(
                sprintf(
                    'Order created with price amount %s',
                    money($orderPipelineData->order->total_price * 100)->format(),
                )
            )
            ->icon('heroicon-o-shopping-bag')
            ->actions([
                Action::make('view_order')
                    ->button()
                    ->markAsRead()
                    ->url(OrderResource::getUrl('view', [$orderPipelineData->order])),
            ])
            ->sendToDatabase($admins);

        return $next($orderPipelineData);
    }
}
