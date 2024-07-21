<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Actions;

use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\DataTransferObjects\OrderData;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class CreateOrderAction
{
    public function __construct(
        private GenerateReceiptNumberAction $generateReceiptNumberAction,
        private CalculateOrderTotalPriceAction $calculateOrderTotalPriceAction,
        private SaveOrderItemAction $saveOrderItemAction,
        private OrderCreatedPipelineAction $orderCreatedPipelineAction,
    ) {
    }

    public function execute(OrderData $data): Order
    {
        $skus = [];

        $data->customer->load([
            'carts' => fn (HasMany $query) => $query
                ->whereBelongsTo($data->branch),
        ]);

        if ($data->customer->carts->isEmpty()) {
            abort(400, trans('Cart is empty.'));
        }

        $total = $this->calculateOrderTotalPriceAction
            ->execute(
                $data->customer->carts
                    ->map(
                        function (Cart $cart) use (&$skus): ItemWithMinMaxData {
                            $sku = $cart->sku;

                            $skus[] = [
                                'sku' => $sku,
                                'quantity' => $cart->quantity,
                            ];

                            return new ItemWithMinMaxData(
                                price: $sku->price,
                                quantity: $cart->quantity,
                                minimum: $sku->minimum,
                                maximum: $sku->maximum,
                            );
                        }
                    )
                    ->toArray()
            )->getAmount();

        $order = Order::create([
            'branch_id' => $data->branch->getKey(),
            'customer_id' => $data->customer->getKey(),
            'receipt_number' => $this->generateReceiptNumberAction->execute(),
            'notes' => $data->notes,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => $data->payment_method,
            'status' => Status::PENDING,
            'total_price' => $total,
        ]);

        foreach ($skus as $sku) {
            $this->saveOrderItemAction->execute($order, $sku['sku'], $sku['quantity']);
        }

        $this->orderCreatedPipelineAction->execute($order);

        return $order;
    }
}
