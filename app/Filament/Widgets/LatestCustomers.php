<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Shop\CustomerResource;
use Domain\Access\Role\Contracts\HasPermissionWidgets;
use Domain\Access\Role\PermissionWidgets;
use Domain\Shop\Customer\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestCustomers extends TableWidget implements HasPermissionWidgets
{
    use PermissionWidgets;

    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->query(Customer::limit(5)->latest())
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->sortable()
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->translateLabel()
                    ->authorize('update')
                    ->url(fn (Customer $record): string => CustomerResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
