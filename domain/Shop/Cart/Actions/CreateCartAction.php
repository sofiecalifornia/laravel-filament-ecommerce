<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Actions;

use Domain\Shop\Cart\DataTransferObjects\CreateCartData;
use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Models\Sku;

final class CreateCartAction
{
    public function execute(CreateCartData $data): Cart
    {
        /** @var \Domain\Shop\Product\Models\Sku $sku */
        $sku = Sku::where((new Sku())->getRouteKeyName(), $data->sku_id)
            ->first();

        return Cart::create([
            'branch_id' => $data->branch->getKey(),
            'customer_id' => $data->customer->getKey(),
            'product_id' => $sku->product->getKey(),
            'sku_id' => $sku->getKey(),
            'sku_code' => $sku->code,
            'product_name' => $sku->product->name,
            'price' => $sku->price,
            'minimum' => $sku->minimum,
            'maximum' => $sku->maximum,
            'quantity' => $data->quantity,
        ]);
    }
}
