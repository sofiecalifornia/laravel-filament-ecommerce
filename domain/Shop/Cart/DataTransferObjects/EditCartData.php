<?php

declare(strict_types=1);

namespace Domain\Shop\Cart\DataTransferObjects;

final readonly class EditCartData
{
    public function __construct(
        public float $quantity,
    ) {
    }
}
