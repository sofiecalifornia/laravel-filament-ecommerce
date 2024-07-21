<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\Policies;

use Domain\Shop\Cart\Models\Cart;
use Domain\Shop\Customer\Models\Customer;

class CartPolicy
{
    public function update(Customer $customer, Cart $cart): bool
    {
        return $cart->customer->is($customer);
    }

    public function delete(Customer $customer, Cart $cart): bool
    {
        return $cart->customer->is($customer);
    }
}
