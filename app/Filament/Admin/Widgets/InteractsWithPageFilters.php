<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Illuminate\Support\Carbon;

trait InteractsWithPageFilters
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    public function getDateFilter(string $filter): ?Carbon
    {
        if (! isset($this->filters[$filter])) {
            return null;
        }

        return now()->parse($this->filters[$filter]);
    }
}
