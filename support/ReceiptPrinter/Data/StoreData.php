<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter\Data;

readonly class StoreData
{
    public function __construct(
        public string $mid,
        public string $name,
        public string $address,
        public string $phone,
        public string $email,
        public string $website
    ) {
    }
}
