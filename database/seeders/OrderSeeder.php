<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Database\Factories\AddressFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\Order\Actions\OrderCreatedPipelineAction;
use Domain\Shop\Order\Database\Factories\OrderFactory;
use Domain\Shop\Product\Enums\Status;
use Domain\Shop\Product\Models\Sku;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

use function Spatie\PestPluginTestTime\testTime;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orderPipeline = app(OrderCreatedPipelineAction::class);

        $this->command
            ->withProgressBar(
                range(1, 10),
                function () use ($orderPipeline) {

                    testTime()->subDay();

                    CustomerFactory::new(['password' => 'secret'])
                        ->count(Arr::random(range(2, 15)))
                        ->has(AddressFactory::new()->count(Arr::random(range(1, 3))))
                        ->active()
                        ->create();

                    self::order($orderPipeline);
                }
            );

        $this->command->newLine();
    }

    private static function order(OrderCreatedPipelineAction $orderPipeline): void
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Customer::where('created_at', '<=', now())
            ->inRandomOrder()
            ->first();

        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = Branch::where(
            'status',
            \Domain\Shop\Branch\Enums\Status::enabled
        )
            ->inRandomOrder()->first();

        $order = OrderFactory::new()
            ->for($branch)
            ->for($customer)
            ->hasOrderItems(
                Sku::whereRelation('product', 'status', Status::in_stock)
                    ->whereRelation('skuStocks', function (Builder $query) use ($branch) {
                        $query->whereBelongsTo($branch);
                    })
                    ->inRandomOrder()
                    ->take(4)
                    ->get()
            )
            ->createOne();

        $orderPipeline->execute($order);
    }
}
