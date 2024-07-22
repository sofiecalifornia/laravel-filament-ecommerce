<?php

declare(strict_types=1);

namespace App\Settings;

class SkuStockSettings extends BaseSettings
{
    public string $yellow_warning_count;

    public string $red_warning_count;

    #[\Override]
    public static function group(): string
    {
        return 'sku_stock';
    }

    public function getColor(int $stock): string
    {
        if ($stock <= $this->red_warning_count) {
            return 'danger';
        }

        if ($stock <= $this->yellow_warning_count) {
            return 'warning';
        }

        return 'success';
    }
}
