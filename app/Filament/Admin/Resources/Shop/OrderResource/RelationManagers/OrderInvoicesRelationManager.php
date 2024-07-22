<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Shop\OrderResource\RelationManagers;

use Domain\Shop\Order\Models\OrderInvoice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class OrderInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'orderInvoices';

    #[\Override]
    public function table(Table $table): Table
    {
        return $table
//            ->recordTitleAttribute('file_name')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('download')
                    ->translateLabel()
                    ->icon('heroicon-s-printer')
                    ->url(
                        fn (OrderInvoice $record) => route('admin.order-invoices.download', $record),
                        shouldOpenInNewTab: true
                    )
                    ->authorize('downloadInvoice'),
            ]);
    }
}
