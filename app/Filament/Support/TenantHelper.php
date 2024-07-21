<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Domain\Shop\Branch\Models\Branch;
use Filament\Facades\Filament;

final class TenantHelper
{
    private function __construct()
    {
    }

    public static function getBranch(): ?Branch
    {
        $branch = Filament::getTenant();

        if ($branch instanceof Branch) {
            return $branch;
        }

        return null;
    }
}
