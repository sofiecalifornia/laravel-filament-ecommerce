<?php

declare(strict_types=1);

namespace App\Filament\Branch\Resources\Shop\OrderResource\Pages;

use App\Filament\Branch\Resources\Shop\OrderResource;
use App\Filament\Resources\Shop\OrderResource\Schema\OrderSchema;
use App\Filament\Support\TenantHelper;
use Filament\Forms\Form;

class CreateOrder extends \App\Filament\Resources\Shop\OrderResource\Pages\CreateOrder
{
    protected static string $resource = OrderResource::class;

    public function form(Form $form): Form
    {
        return OrderSchema::createForm(
            parent::form($form),
            submitAction: $this->getSubmitFormAction(),
            cancelAction: $this->getCancelFormAction(),
            tenantBranch: TenantHelper::getBranch(),
        );
    }
}
