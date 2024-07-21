<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shop\OrderResource\Pages\Actions;

use Domain\Shop\Order\Actions\PrintOrderAction as DomainPrintOrderAction;
use Filament\Actions\Action;

class PrintOrderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'print';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->translateLabel()
//            ->modalHeading(
//            fn (Order $record) => 'Print '.$record->purchased_number
//        )

//        ->requiresConfirmation()
            ->modalSubmitActionLabel(trans('Print'))
            ->color('success')
            ->icon('heroicon-s-printer')
            ->hidden(function (): bool {
                /** @phpstan-ignore-next-line */
                $record = $this->getLivewire()->record;

                if (method_exists($record, 'trashed')) {
                    /** @phpstan-ignore-next-line  */
                    return $record->trashed();
                }

                return false;
            })
            ->action(
                fn () => app(DomainPrintOrderAction::class)
                    ->execute(
                        /** @phpstan-ignore-next-line */
                        $this->getLivewire()->record
                    )->download()
            )
            ->authorize('print');
    }
}
