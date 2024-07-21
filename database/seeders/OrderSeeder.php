<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Access\Admin\Models\Admin;
use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Database\Factories\AddressFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Models\Customer;
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
        $days = 30;
        $hours = 20;
        testTime()
            ->subDays(DatabaseSeeder::MONTHS * $days)
            ->subHours(DatabaseSeeder::MONTHS * $days * $hours)
            ->subDay();

        $this->command
            ->withProgressBar(
                DatabaseSeeder::MONTHS * $days * $hours,
                function ($bar) use ($hours, $days) {
                    foreach (range(1, DatabaseSeeder::MONTHS) as $month) {
                        testTime()->addMonth();

                        foreach (range(1, $days) as $day) {
                            testTime()->addDay();

                            /** @var \Domain\Access\Admin\Models\Admin $admin */
                            $admin = Admin::inRandomOrder()->first();

                            CustomerFactory::new(['password' => 'secret'])
                                ->count(Arr::random(range(2, 15)))
                                ->has(AddressFactory::new()->count(Arr::random(range(1, 3))))
                                ->for($admin)
                                ->active()
                                ->create();

                            foreach (range(1, $hours) as $hour) {
                                testTime()->addHour();

                                self::order();

                                $bar->advance();
                            }
                        }
                    }
                }
            );

        $this->command->newLine();
    }

    private static function order(): void
    {
        /** @var \Domain\Access\Admin\Models\Admin $admin */
        $admin = Admin::role(config('domain.access.role.admin'))
            ->inRandomOrder()
            ->first();

        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Customer::where('created_at', '<=', now())
            ->inRandomOrder()
            ->first();

        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = Branch::where(
            'status',
            \Domain\Shop\Branch\Enums\Status::ENABLED
        )
            ->inRandomOrder()->first();

        OrderFactory::new()
            ->for($branch)
            ->for($admin)
            ->for($customer)
            ->hasOrderItems(
                Sku::whereRelation('product', 'status', Status::IN_STOCK)
                    ->whereRelation('skuStocks', function (Builder $query) use ($branch) {
                        $query->whereBelongsTo($branch);
                    })
                    ->inRandomOrder()
                    ->take(4)
                    ->get()
            )
            ->createOne();

    }
}
