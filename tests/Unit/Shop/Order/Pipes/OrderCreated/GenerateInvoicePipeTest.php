<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Order\Actions\OrderCreatedPipelineAction;
use Domain\Shop\Order\Database\Factories\OrderFactory;
use Domain\Shop\Order\Models\OrderInvoice;
use Domain\Shop\Product\Database\Factories\SkuFactory;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Database\Factories\SkuStockFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

it('generate invoice', function () {

    $branch = BranchFactory::new()
        ->createOne();

    SkuFactory::new()
        ->has(SkuStockFactory::new()->for($branch))
        ->createOne();

    $order = OrderFactory::new()
        ->for($branch)
        ->hasOrderItems(Sku::get())
        ->createOne();

    assertDatabaseEmpty(OrderInvoice::class);

    app(OrderCreatedPipelineAction::class)
        ->execute($order);

    assertDatabaseCount(OrderInvoice::class, 1);
});
