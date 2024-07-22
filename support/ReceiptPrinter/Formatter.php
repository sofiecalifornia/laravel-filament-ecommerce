<?php

declare(strict_types=1);

namespace Support\ReceiptPrinter;

use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Support\ReceiptPrinter\Data\ItemData;
use Support\ReceiptPrinter\Enums\PaperSizeForItem;

final class Formatter
{
    private function __construct()
    {
    }

    public static function header(string $left_text, string $right_text, bool $doubleWidth = false): string
    {
        $width = $doubleWidth ? 8 : 15;

        return Str::padRight($left_text, $width).Str::padLeft($right_text, $width);
    }

    public static function item(ItemData $item, PaperSizeForItem $paperSize = PaperSizeForItem::_57mm): string
    {
        $size = $paperSize->size();

        $name = Str::padRight($item->name, 16);
        $price = Str::padRight(Number::format($item->price->getValue()).' x '.$item->quantity, $size->padRight);
        /** @var string $subtotal */
        $subtotal = Number::format($item->subTotal->getValue());
        $subtotal = Str::padLeft($subtotal, $size->padLeft);

        return $name.PHP_EOL.$price.$subtotal.PHP_EOL;
    }

    public static function summary(string $label, float|int $value, bool $doubleWWidth = false): string
    {
        $padRight = $doubleWWidth ? 5 : 11;
        $padLeft = $doubleWWidth ? 9 : 19;

        /** @var string $valueFormatted */
        $valueFormatted = Number::format($value);

        return Str::padRight($label, $padRight).
            Str::padLeft($valueFormatted, $padLeft);
    }
}
