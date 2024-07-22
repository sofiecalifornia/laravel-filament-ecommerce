<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\OrderResource\Pages;

use App\Filament\Branch\Resources\Shop\OrderResource;

class CreateOrder extends \App\Filament\Admin\Resources\Shop\OrderResource\Pages\CreateOrder
{
    protected static string $resource = OrderResource::class;
}
