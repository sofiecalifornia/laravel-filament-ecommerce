<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\BranchResource\Pages;

use App\Filament\Resources\Shop\BranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;
}
