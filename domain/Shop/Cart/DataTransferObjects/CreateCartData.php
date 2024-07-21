<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\DataTransferObjects;

use Domain\Shop\Branch\Models\Branch;
use Domain\Shop\Customer\Models\Customer;

final readonly class CreateCartData
{
    public function __construct(
        public Branch $branch,
        public Customer $customer,
        public string $sku_id,
        public float $quantity,
    ) {
    }
}
