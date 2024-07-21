<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Cart;

use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Illuminate\Foundation\Http\FormRequest;

class CartEditRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = $this->route('enabledBranch');
        /** @var \Domain\Shop\Cart\Models\Cart $cart */
        $cart = $this->route('cart');

        return [
            'quantity' => [
                'required',
                'numeric',
                'min:1',
                new CheckQuantitySkuStockRule(
                    branch: $branch,
                    sku: $cart->sku,
                    skuColumn: (new Sku())->getRouteKeyName(),
                ),
            ],
        ];
    }
}
