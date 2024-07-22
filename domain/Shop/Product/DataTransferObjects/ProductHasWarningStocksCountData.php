<?php

declare(strict_types=1);

namespace Domain\Shop\Product\DataTransferObjects;

final readonly class ProductHasWarningStocksCountData
{
    public function __construct(
        public int $count,
        public string $color,
    ) {
    }
}
