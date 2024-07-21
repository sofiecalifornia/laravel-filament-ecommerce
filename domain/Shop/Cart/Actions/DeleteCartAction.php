<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Actions;

use Domain\Shop\Cart\Models\Cart;

final readonly class DeleteCartAction
{
    public function execute(Cart $cart): void
    {
        $cart->delete();
    }
}
