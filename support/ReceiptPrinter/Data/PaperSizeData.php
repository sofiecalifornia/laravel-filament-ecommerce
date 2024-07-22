<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter\Data;

readonly class PaperSizeData
{
    public function __construct(
        public string $name,
        public int $padLeft,
        public int $padRight
    ) {
    }
}
