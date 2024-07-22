<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Notifications;

use App\Jobs\QueueJobPriority;
use App\Settings\SiteSettings;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class CustomerOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly Order $order)
    {
        $this->queue = QueueJobPriority::HIGH;
    }

    public function via(Customer $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @throws \Exception
     */
    public function toMail(Customer $notifiable): MailMessage
    {
        $siteSetting = app(SiteSettings::class);

        /** @var \Domain\Shop\Order\Models\OrderInvoice $invoice */
        $invoice = $this->order->orderInvoices->first();

        return (new MailMessage())
            ->subject(trans(':site Order Confirmation', ['site' => $siteSetting->name]))
            ->greeting(trans('Hello :customer!', ['customer' => $notifiable->full_name]))
            ->line(trans('Your order [:order] has been submitted and is now processing.', ['order' => $this->order->receipt_number]))
            ->line(
                trans(
                    'Order created with price amount :amount.',
                    ['amount' => $this->order->total_price->format()],
                )
            )
            ->line(trans('Branch: :branch', ['branch' => $this->order->branch->name]))
            ->attachData(
                $invoice->readStream(),
                $invoice->file_name
            );
    }
}
