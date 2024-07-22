<?php

declare(strict_types=1);

use App\Settings\OrderSettings;
use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\Database\Factories\OrderFactory;

use function Pest\Laravel\travelTo;

beforeEach(function () {
    $this->branch = BranchFactory::new()->createOne(['code' => 'BRANCH_']);

    $orderSettingPrefix = 'SETTING_';
    OrderSettings::fake([
        'prefix' => $orderSettingPrefix,
    ]);

    $this->prefix = $orderSettingPrefix.$this->branch->code;
    $this->prefixWithDate = $this->prefix.'210131';

    $this->generator = app(GenerateReceiptNumberAction::class);

    travelTo(now()->parse('2021-01-31'));
});

it('generate first time', function () {

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0001');
});

it('generate first time in the next day', function () {

    travelTo(now()->subDay());

    OrderFactory::new()
        ->for(BranchFactory::new()->enabled()->createOne())
        ->sequence(
            ['receipt_number' => $this->prefix.'210130'.'0001', 'created_at' => now()->subDays(4)]
        )
        ->createOne();

    travelTo(now()->addDay());

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0001');
});

it('generate 3rd time on same day', function () {

    OrderFactory::new()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefixWithDate.'0001', 'created_at' => now()->subSeconds(2)],
            ['receipt_number' => $this->prefixWithDate.'0002', 'created_at' => now()->subSecond()]
        )
        ->count(2)
        ->create();

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0003');
});

it('generate 3rd time in the next day', function () {

    travelTo(now()->subDay());

    OrderFactory::new()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefix.'210130'.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $this->prefix.'210130'.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    travelTo(now()->addDay());

    OrderFactory::new()
        ->for($this->branch)
        ->sequence(
            ['receipt_number' => $this->prefixWithDate.'0001', 'created_at' => now()->subDays(4)],
            ['receipt_number' => $this->prefixWithDate.'0002', 'created_at' => now()->subDays(2)]
        )
        ->count(2)
        ->create();

    expect($this->generator->execute($this->branch))
        ->toBe($this->prefixWithDate.'0003');
});
