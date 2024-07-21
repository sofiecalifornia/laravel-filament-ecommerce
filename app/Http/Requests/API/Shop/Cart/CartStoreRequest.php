<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Cart;

use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartStoreRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = $this->route('enabledBranch');

        return [
            'sku_id' => [
                'required',
                Rule::exists(Sku::class, (new Sku())->getRouteKeyName()),
                'required_with:quantity',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:1',
                new CheckQuantitySkuStockRule(
                    branch: $branch,
                    sku: (string) $this->string('sku_id'),
                    skuColumn: (new Sku())->getRouteKeyName(),
                ),
            ],
        ];
    }
}
