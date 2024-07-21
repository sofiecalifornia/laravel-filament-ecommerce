<?php

declare(strict_types=1);

use App\Settings\OrderSettings;
use App\Settings\SiteSettings;
use Carbon\Carbon;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\Database\Factories\OrderFactory;

use function Pest\Laravel\travelTo;

beforeEach(function () {
    SiteSettings::fake([
        'timezone' => 'Asia/Manila',
    ]);
    travelTo(now()->parse('2021-01-01'));
});

it('generate purchase number for the fist time', function () {
    $prefix = 'TEST_PREFIX';

    OrderSettings::fake([
        'prefix' => $prefix,
    ]);

    expect(app(GenerateReceiptNumberAction::class)->execute())
        ->toBe(generatePrefix($prefix, now()).'0001');
});

it('generate purchase number  for the fist time in the next day', function () {
    $prefix = 'TEST_PREFIX';

    OrderSettings::fake([
        'prefix' => $prefix,
    ]);

    $format = generatePrefix($prefix, now());

    OrderFactory::new()
        ->for(BranchFactory::new()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $format.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $format.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    travelTo(now()->addDay());

    expect(app(GenerateReceiptNumberAction::class)->execute())
        ->toBe(generatePrefix($prefix, now()).'0001');
});

it('generate purchase 3rd time', function () {
    $prefix = 'TEST_PREFIX';

    OrderSettings::fake([
        'prefix' => $prefix,
    ]);

    $format = generatePrefix($prefix, now());

    OrderFactory::new()
        ->for(BranchFactory::new()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $format.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $format.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    expect(app(GenerateReceiptNumberAction::class)->execute())
        ->toBe($format.'0003');
});

it('generate purchase 3rd time in the next day', function () {
    $prefix = 'TEST_PREFIX';

    OrderSettings::fake([
        'prefix' => $prefix,
    ]);

    $format = generatePrefix($prefix, now());

    OrderFactory::new()
        ->for(BranchFactory::new()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $format.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $format.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    travelTo(now()->addDay());

    $format = generatePrefix($prefix, now());

    OrderFactory::new()
        ->for(BranchFactory::new()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $format.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $format.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    expect(app(GenerateReceiptNumberAction::class)->execute())
        ->toBe($format.'0003');
});

function generatePrefix(string $prefix, Carbon $now): string
{
    $y = $now->format('y');
    $m = $now->format('m');
    $d = $now->format('d');

    return sprintf('%s%s%s%s', $prefix, $y, $m, $d);
}
