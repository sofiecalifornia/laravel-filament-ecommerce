<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use App\Settings\SiteSettings;
use Domain\Shop\Order\Models\Order;
use Support\ReceiptPrinter\Data\ItemData;
use Support\ReceiptPrinter\Data\ReceiptPrinterData;
use Support\ReceiptPrinter\Data\StoreData;
use Support\ReceiptPrinter\ReceiptPrinter;

readonly class PrintReceiptAction
{
    public function __construct(
        private SiteSettings $siteSettings,
    ) {
    }

    public function execute(Order $order): void
    {
        $items = [];

        foreach ($order->orderItems as $orderItem) {
            $items[] = new ItemData(
                $orderItem->name.(null === $orderItem->minimum ? '' : ' (min: '.$orderItem->minimum.')'),
                $orderItem->quantity,
                $orderItem->price,
                $orderItem->total_price,
            );
        }

        $data = (new ReceiptPrinterData())
            ->store(new StoreData(
                mid: 'TESTMID',
                name: $this->siteSettings->name,
                address: $order->branch->address ?? '',
                phone: $order->branch->phone ?? '',
                email: $order->branch->email ?? '',
                website: $order->branch->website ?? '',
            ))
            ->qrCode([
                'receipt_number' => $order->receipt_number,
            ])
            ->transactionId($order->receipt_number)
            ->items($items);

        (new ReceiptPrinter($data))->send();
    }
}
