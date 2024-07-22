<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Cart;

use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Product\Models\Sku;
use Domain\Shop\Stock\Rules\CheckQuantitySkuStockRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CartStoreRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \Domain\Shop\Branch\Models\Branch $branch */
        $branch = $this->route('enabledBranch');

        return [
            'sku_uuid' => [
                'required',
                Rule::exists(Sku::class, (new Sku())->getRouteKeyName()),
                Rule::unique(Cart::class, 'sku_uuid')
                    ->where('customer_uuid', Auth::id()),
                'required_with:quantity',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:1',
                new CheckQuantitySkuStockRule(
                    branch: $branch,
                    sku: (string) $this->string('sku_uuid'),
                    skuColumn: (new Sku())->getRouteKeyName(),
                ),
            ],
        ];
    }

    #[\Override]
    public function messages()
    {
        return [
            'sku_uuid.unique' => trans('The :attribute has already in you\'re cart.'),
        ];
    }
}
