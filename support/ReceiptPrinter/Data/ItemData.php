<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter\Data;

use Akaunting\Money\Money;
use Spatie\Cloneable\Cloneable;

readonly class ItemData
{
    use Cloneable;

    public function __construct(
        public string $name,
        public float $quantity,
        public Money $price,
        public Money $subTotal,
    ) {
    }
}
