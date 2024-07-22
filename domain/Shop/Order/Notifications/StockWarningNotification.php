<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Notifications;

use App\Filament\Admin\Resources\Shop\ProductResource as AdminProductResource;
use App\Filament\Branch\Resources\Shop\ProductResource as BranchProductResource;
use App\Jobs\QueueJobPriority;
use Domain\Access\Admin\Models\Admin;
use Domain\Access\Role\Support;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Stock\Models\SkuStock;
use Filament\Facades\Filament;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class StockWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly SkuStock $skuStock,
    ) {
        $this->queue = QueueJobPriority::HIGH;
    }

    public function via(Admin $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(Admin $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(trans('Sku [:sku_code] Stock Warning', ['sku_code' => $this->skuStock->sku->code]))
            ->greeting(trans('Hello :admin!', ['admin' => $notifiable->name]))
            ->line(
                trans(
                    'Sku [:sku_code] has only [:stock_count] available stock, after order [:order] created.',
                    [
                        'sku_code' => $this->skuStock->sku->code,
                        'stock_count' => $this->skuStock->count ?? 'n/a',
                        'order' => $this->order->receipt_number,
                    ]
                )
            )
            ->line(trans('Branch: :branch', ['branch' => $this->order->branch->name]))
            ->when(
                $this->orderProductUrl($notifiable),
                function (MailMessage $mailMessage, string $url) {
                    $mailMessage
                        ->action(trans('View Product'), $url);
                }
            );
    }

    public function toDatabase(Admin $notifiable): array
    {
        return FilamentNotification::make()
            ->title(trans('Sku [:sku_code] Stock Warning.', ['sku_code' => $this->skuStock->sku->code]))
            ->body(
                trans(
                    'Sku [:sku_code] has only [:stock_count] available stock, after order [:order] created.',
                    [
                        'sku_code' => $this->skuStock->sku->code,
                        'stock_count' => $this->skuStock->count ?? 'n/a',
                        'order' => $this->order->receipt_number,
                    ]
                )
            )
            ->icon('heroicon-o-exclamation-circle')
            ->when(
                $this->orderProductUrl($notifiable),
                function (FilamentNotification $notification, string $url) {
                    $notification
                        ->actions([
                            Action::make('view_sku_stock')
                                ->translateLabel()
                                ->button()
                                ->markAsRead()
                                ->url($url),
                        ]);
                }
            )
            ->getDatabaseMessage();
    }

    private function orderProductUrl(Admin $admin): ?string
    {
        if ($admin->can(Support::getPanelPermissionName('admin')) && $admin->can('product.update')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            return AdminProductResource::getUrl('edit', [$this->skuStock->sku->product]);
        }

        if (! $admin->can('product.update')) {
            return null;
        }

        Auth::setUser($admin);

        Filament::setTenant($this->order->branch);

        Filament::setCurrentPanel(Filament::getPanel('branch'));

        return BranchProductResource::getUrl('edit', [$this->skuStock->sku->product]);
    }
}
