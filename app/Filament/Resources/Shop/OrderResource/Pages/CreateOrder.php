<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\OrderResource\Pages;

use App\Filament\Resources\Shop\OrderResource;
use App\Filament\Resources\Shop\OrderResource\Schema\OrderSchema;
use Domain\Shop\Order\Actions\GenerateReceiptNumberAction;
use Domain\Shop\Order\Actions\OrderCreatedPipelineAction;
use Domain\Shop\Order\Enums\PaymentStatus;
use Domain\Shop\Order\Enums\Status;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property-read \Domain\Shop\Order\Models\Order $record
 */
class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'admin_id' => Filament::auth()->id(),
            'receipt_number' => app(GenerateReceiptNumberAction::class)->execute(),
            'status' => Status::PENDING->value,
            'payment_status' => PaymentStatus::PENDING->value,
        ] + $data;
    }

    public function form(Form $form): Form
    {
        return OrderSchema::createForm(
            parent::form($form),
            submitAction: $this->getSubmitFormAction(),
            cancelAction: $this->getCancelFormAction(),
        );
    }

    /** @throws Throwable */
    protected function afterCreate(): void
    {
        DB::transaction(
            fn () => app(OrderCreatedPipelineAction::class)
                ->execute($this->record)
        );
    }

    public function getFormActions(): array
    {
        return [];
    }
}
