<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Database\Factories;

use Domain\Access\Admin\Database\Factories\AdminFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Order\Actions\CalculateOrderTotalPriceAction;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\DataTransferObjects\ItemWithMinMaxData;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Domain\Shop\Order\Models\Order;
use Domain\Shop\Order\Models\OrderItem;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Shop\Order\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    private static int $receiptCount = 1;

    public function definition(): array
    {
        return [
            'customer_id' => CustomerFactory::new(),
            'admin_id' => AdminFactory::new(),
            'receipt_number' => app(GenerateReceiptNumberAction::class)->execute(self::$receiptCount++),
            'notes' => $this->faker->sentence(10),
            'payment_status' => Arr::random(PaymentStatus::cases()),
            'status' => fn (array $attributes) => match ($attributes['payment_status']) {
                PaymentStatus::PAID => Status::COMPLETED,
                PaymentStatus::PENDING, PaymentStatus::UNPAID => Arr::random(Arr::except(Status::cases(), [Status::COMPLETED->value])),
                default => Status::FAILED,
            },
        ];
    }

    /** @param  \Illuminate\Database\Eloquent\Collection<int, \Domain\Shop\Product\Models\Sku>  $SKUs */
    public function hasOrderItems(Collection $SKUs): self
    {
        $SKUs->ensure(Sku::class);

        $self = $this;

        $SKUs->each(
            function (Sku $sku) use (&$self): self {
                return $self = $self
                    ->has(
                        OrderItemFactory::new()
                            ->forSku($sku)
                    );
            }
        );

        return $self;
    }

    public function configure(): self
    {
        return parent::configure()
            ->afterCreating(function (Order $order) {

                $order->refresh();

                $order->update([
                    'total_price' => app(CalculateOrderTotalPriceAction::class)
                        ->execute(
                            $order->orderItems
                                ->map(fn (OrderItem $orderItem): ItemWithMinMaxData => ItemWithMinMaxData::fromOrderItem($orderItem))
                                ->toArray()
                        )
                        ->getAmount(),
                ]);
            });
    }
}
