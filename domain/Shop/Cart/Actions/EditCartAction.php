<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Actions;

use Domain\Shop\Cart\DataTransferObjects\EditCartData;
use Domain\Shop\Cart\Models\Cart;

final readonly class EditCartAction
{
    public function execute(Cart $cart, EditCartData $editCartData): cart
    {
        $cart->update([
            'quantity' => $editCartData->quantity,
        ]);

        return $cart;
    }
}
