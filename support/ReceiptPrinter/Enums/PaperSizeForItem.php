<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter\Enums;

use Support\ReceiptPrinter\Data\PaperSizeData;

enum PaperSizeForItem: string
{
    case _57mm = '57mm';
    case _80mm = '80mm';

    public function size(): PaperSizeData
    {
        return match ($this) {
            self::_57mm => new PaperSizeData('57mm', padLeft: 9, padRight: 21),
            self::_80mm => new PaperSizeData('80mm', padLeft: 15, padRight: 32),
        };
    }
}
