<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use App\Settings\SiteSettings;
use Domain\Shop\Order\Models\Order;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Invoice;

final readonly class OrderInvoiceAction
{
    public const string FOLDER = 'invoices';

    public function __construct(private SiteSettings $siteSettings)
    {
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function execute(Order $order): Invoice
    {
        $invoice = Invoice::make()
            ->currencyCode(config('money.defaults.currency'))
            ->currencySymbol(config('money.currencies.'.config('money.defaults.currency').'.symbol'))
            ->currencyFraction('cents.')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',');

        foreach ($order->orderItems as $orderItem) {
            $invoice->addItem(
                $invoice::makeItem()
                    ->title($orderItem->name)
                    ->description(Str::limit($orderItem->description ?? ''))
                    ->pricePerUnit($orderItem->price->getValue())
                    ->subTotalPrice($orderItem->price->multiply($orderItem->quantity)->getValue())
                    ->quantity($orderItem->quantity)
                //                    ->units('piece')
                    ->discount($orderItem->discount_price->getValue())
                //                ->units()

                //            if ($orderItem->description !== null) {
                //                $item->description((string) Str::of($orderItem->description)->stripTags());
                //            }
            );
        }

        if ($order->delivery_price->greaterThan(money(0))) {
            $invoice->addItem(
                $invoice::makeItem()
                    ->title(trans('Delivery Fee'))
                    ->subTotalPrice($order->delivery_price->getValue())
            );

        }

        $customer = $order->customer;

        return $invoice
            ->seller(
                $invoice::makeParty([
                    'name' => $this->siteSettings->name,
                    'address' => $this->siteSettings->address,
                ])
            )
            ->buyer(
                new Buyer([
                    'name' => $customer->full_name,
                    'address' => 'TODO: address', // TODO: address
                    'custom_fields' => [
                        'email' => $customer->email,
                        'mobile' => $customer->mobile ?? 'n/a',
                        'landline' => $customer->landline ?? 'n/a',
                        'order number' => $order->receipt_number,
                    ],
                ])
            )
            ->totalAmount($order->total_price->getValue())
            ->status($order->payment_status->getLabel() ?? throw new \LogicException('this should not happen'))
            ->filename(sprintf(
                // invoices/ORDER/ORDER_TIMESTAMP_invoice
                '%s/%s/%s_%s_invoice',
                self::FOLDER,
                $order->receipt_number,
                $order->receipt_number,
                $order->updated_at?->timestamp ?? now()->timestamp,
            ))
            ->notes(
                'Test Note'
            );
    }
}
